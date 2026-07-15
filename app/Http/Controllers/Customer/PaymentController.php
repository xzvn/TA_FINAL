<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Pembayaran;
use App\Models\Pesanan;
use App\Services\NotifikasiService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Midtrans\Config;
use Midtrans\Notification;
use Midtrans\Snap;
use Illuminate\Support\Facades\Log;
use Midtrans\Transaction;
use Throwable;


class PaymentController extends Controller
{
    private function authorizeCustomer(Request $request, Pesanan $pesanan): void
    {
        $user = $request->user();

        abort_if(! $user || $user->role !== 'customer', 403);
        abort_if($pesanan->id_customer !== $user->id, 403);
    }

    public function index(Request $request): View
    {
        $user = $request->user();

        abort_if(! $user || $user->role !== 'customer', 403);

        $pembayarans = Pembayaran::with(['pesanan.jasa.freelancer'])
            ->whereHas('pesanan', function ($query) use ($user) {
                $query->where('id_customer', $user->id);
            })
            ->latest()
            ->get();

        return view('customer.payment.index', compact('pembayarans'));
    }

    private function setupMidtrans(): void
    {
        Config::$serverKey = config('services.midtrans.server_key');
        Config::$isProduction = filter_var(config('services.midtrans.is_production'), FILTER_VALIDATE_BOOLEAN);
        Config::$isSanitized = true;
        Config::$is3ds = true;
    }

    public function show(Request $request, Pesanan $pesanan): View
    {
        $this->authorizeCustomer($request, $pesanan);

        $pesanan->load('jasa.freelancer', 'pembayaran');

        return view('customer.payment.show', compact('pesanan'));
    }

    public function pay(Request $request, Pesanan $pesanan): RedirectResponse
    {
        $this->authorizeCustomer($request, $pesanan);

        abort_if(
            $pesanan->status_pesanan !== 'menunggu_pembayaran',
            403,
            'Pesanan ini sudah dibayar atau tidak dapat dibayar.'
        );

        $this->setupMidtrans();

        $pesanan->load('jasa.freelancer', 'customer');

        $orderId = 'JSK-' . $pesanan->id . '-' . time();
        $grossAmount = (int) round($pesanan->total_harga);

        $params = [
            'transaction_details' => [
                'order_id' => $orderId,
                'gross_amount' => $grossAmount,
            ],
            'customer_details' => [
                'first_name' => $pesanan->customer->nama ?? 'Customer',
                'email' => $pesanan->customer->email ?? null,
                'phone' => $pesanan->customer->no_hp ?? null,
            ],
            'item_details' => [
                [
                    'id' => 'JASA-' . $pesanan->jasa->id,
                    'price' => $grossAmount,
                    'quantity' => 1,
                    'name' => $pesanan->jasa->nama_jasa ?? 'Pembayaran Jasa',
                ],
            ],
        ];

        $snapToken = Snap::getSnapToken($params);

        Pembayaran::updateOrCreate(
            [
                'id_pesanan' => $pesanan->id,
            ],
            [
                'order_id' => $orderId,
                'transaction_id' => null,
                'payment_type' => 'midtrans',
                'gross_amount' => $grossAmount,
                'transaction_status' => 'pending',
                'fraud_status' => null,
                'status_escrow' => 'belum_ditahan',
                'snap_token' => $snapToken,
                'tanggal_bayar' => null,
            ]
        );

        return redirect()
            ->route('customer.payment.show', $pesanan->id)
            ->with('success', 'Silakan lanjutkan pembayaran melalui Midtrans.');
    }

    public function finish(
        Request $request,
        Pesanan $pesanan
    ): JsonResponse {
        $this->authorizeCustomer($request, $pesanan);

        $pesanan->load('pembayaran');

        $pembayaran = $pesanan->pembayaran;

        abort_if(
            ! $pembayaran,
            404,
            'Data pembayaran tidak ditemukan.'
        );

        $this->setupMidtrans();

        try {
            /*
         * Status tidak diambil dari data JavaScript/browser.
         * Server meminta status langsung kepada Midtrans.
         */
            $status = Transaction::status(
                $pembayaran->order_id
            );

            $receivedOrderId = (string) (
                $status->order_id ?? ''
            );

            $receivedAmount = (int) round(
                (float) ($status->gross_amount ?? 0)
            );

            $expectedAmount = (int) round(
                (float) $pembayaran->gross_amount
            );

            /*
         * Pastikan transaksi Midtrans benar-benar
         * milik pembayaran yang sedang diproses.
         */
            if (
                $receivedOrderId !==
                (string) $pembayaran->order_id ||
                $receivedAmount !== $expectedAmount
            ) {
                throw new \RuntimeException(
                    'Data transaksi Midtrans tidak cocok dengan pembayaran lokal.'
                );
            }

            $transactionStatus = (string) (
                $status->transaction_status ?? 'pending'
            );

            $fraudStatus = isset($status->fraud_status)
                ? (string) $status->fraud_status
                : null;

            $pembayaran->update([
                'transaction_id' =>
                $status->transaction_id ??
                    $pembayaran->transaction_id,

                'payment_type' =>
                $status->payment_type ??
                    $pembayaran->payment_type,

                'transaction_status' =>
                $transactionStatus,

                'fraud_status' =>
                $fraudStatus,
            ]);

            $paymentSuccessful =
                $transactionStatus === 'settlement' ||
                (
                    $transactionStatus === 'capture' &&
                    $fraudStatus === 'accept'
                );

            if ($paymentSuccessful) {
                $this->markAsPaid($pesanan);
            }

            return response()->json([
                'success' => true,
                'payment_confirmed' =>
                $paymentSuccessful,

                'transaction_status' =>
                $transactionStatus,

                'message' => $paymentSuccessful
                    ? 'Pembayaran berhasil diverifikasi.'
                    : 'Pembayaran belum selesai atau masih diproses.',
            ]);
        } catch (Throwable $exception) {
            Log::error(
                'Gagal memverifikasi pembayaran Midtrans.',
                [
                    'pesanan_id' => $pesanan->id,
                    'order_id' => $pembayaran->order_id,
                    'message' => $exception->getMessage(),
                ]
            );

            return response()->json(
                [
                    'success' => false,
                    'message' =>
                    'Status pembayaran belum dapat diverifikasi. Silakan periksa kembali beberapa saat lagi.',
                ],
                502
            );
        }
    }

    public function notification(Request $request): JsonResponse
    {
        $this->setupMidtrans();

        $notification = new Notification();

        $orderId = $notification->order_id;
        $transactionStatus = $notification->transaction_status;
        $fraudStatus = $notification->fraud_status ?? null;

        $pembayaran = Pembayaran::where('order_id', $orderId)->first();

        if (! $pembayaran) {
            return response()->json([
                'success' => false,
                'message' => 'Pembayaran tidak ditemukan.',
            ], 404);
        }

        $pembayaran->update([
            'transaction_id' => $notification->transaction_id ?? $pembayaran->transaction_id,
            'payment_type' => $notification->payment_type ?? $pembayaran->payment_type,
            'transaction_status' => $transactionStatus,
            'fraud_status' => $fraudStatus,
        ]);

        $pesanan = Pesanan::find($pembayaran->id_pesanan);

        if ($pesanan && in_array($transactionStatus, ['settlement', 'capture'])) {
            if ($transactionStatus === 'capture' && $fraudStatus !== 'accept') {
                return response()->json([
                    'success' => true,
                    'message' => 'Pembayaran capture tetapi fraud belum accept.',
                ]);
            }

            $this->markAsPaid($pesanan);
        }

        return response()->json([
            'success' => true,
            'message' => 'Notifikasi Midtrans diterima.',
        ]);
    }

    private function markAsPaid(Pesanan $pesanan): void
    {
        $pesanan->load('pembayaran');

        if (! $pesanan->pembayaran) {
            return;
        }

        if ($pesanan->status_pesanan === 'dibayar') {
            return;
        }

        $pesanan->pembayaran->update([
            'status_escrow' => 'ditahan',

            'tanggal_bayar' =>
            $pesanan->pembayaran->tanggal_bayar
                ?? now(),
        ]);

        $pesanan->update([
            'status_pesanan' => 'dibayar',
        ]);

        NotifikasiService::kirim(
            $pesanan->id_freelancer,
            'Pesanan Baru Sudah Dibayar',
            'Customer telah melakukan pembayaran untuk pesanan #' . $pesanan->id . '. Silakan mulai proses pekerjaan.',
            'order',
            route('freelancer.pesanan.show', $pesanan->id, false)
        );

        NotifikasiService::kirim(
            $pesanan->id_customer,
            'Pembayaran Berhasil',
            'Pembayaran untuk pesanan #' . $pesanan->id . ' berhasil. Dana ditahan sementara oleh sistem escrow.',
            'pembayaran',
            route('customer.order.show', $pesanan->id, false)
        );
    }
    public function simulateSuccess(Request $request, Pesanan $pesanan): RedirectResponse
    {
        $user = $request->user();

        abort_if(! $user || $user->role !== 'customer', 403);
        abort_if($pesanan->id_customer !== $user->id, 403);

        // Pengaman: simulasi hanya boleh di local / sandbox
        abort_if(app()->environment('production'), 403);

        $pembayaran = $pesanan->pembayaran;

        if (! $pembayaran) {
            return back()->withErrors([
                'payment' => 'Data pembayaran belum dibuat. Klik tombol Buat Pembayaran Midtrans dulu.',
            ]);
        }

        $pembayaran->update([
            'transaction_id' => 'SIM-' . now()->format('YmdHis'),
            'payment_type' => 'simulation',
            'transaction_status' => 'settlement',
            'fraud_status' => 'accept',
            'status_escrow' => 'ditahan',
            'tanggal_bayar' => now(),
        ]);

        $pesanan->update([
            'status_pesanan' => 'dibayar',
        ]);

        return redirect()
            ->route('customer.order.show', $pesanan->id)
            ->with('success', 'Pembayaran berhasil disimulasikan. Pesanan sekarang sudah masuk ke freelancer.');
    }
}
