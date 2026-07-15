<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Pembayaran;
use App\Models\Pesanan;
use App\Services\MidtransPaymentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Throwable;

class PaymentController extends Controller
{
    public function __construct(
        private readonly MidtransPaymentService $midtrans
    ) {}

    private function authorizeCustomer(
        Request $request,
        Pesanan $pesanan
    ): void {
        $user = $request->user();

        abort_if(
            ! $user ||
                $user->role !== 'customer',
            403
        );

        abort_if(
            $pesanan->id_customer !== $user->id,
            403
        );
    }

    public function index(
        Request $request
    ): View {
        $user = $request->user();

        abort_if(
            ! $user ||
                $user->role !== 'customer',
            403
        );

        $pembayarans = Pembayaran::with([
            'pesanan.jasa.freelancer',
        ])
            ->whereHas(
                'pesanan',
                function ($query) use ($user) {
                    $query->where(
                        'id_customer',
                        $user->id
                    );
                }
            )
            ->latest()
            ->get();

        return view(
            'customer.payment.index',
            compact('pembayarans')
        );
    }

    public function show(
        Request $request,
        Pesanan $pesanan
    ): View {
        $this->authorizeCustomer(
            $request,
            $pesanan
        );

        $pesanan->load([
            'jasa.freelancer',
            'pembayaran',
        ]);

        return view(
            'customer.payment.show',
            compact('pesanan')
        );
    }

    public function pay(
        Request $request,
        Pesanan $pesanan
    ): RedirectResponse {
        $this->authorizeCustomer(
            $request,
            $pesanan
        );

        abort_if(
            $pesanan->status_pesanan !==
                'menunggu_pembayaran',
            403,
            'Pesanan ini tidak dapat dibayar.'
        );

        $pesanan->load([
            'customer',
            'jasa',
            'pembayaran',
        ]);

        $pembayaran = $pesanan->pembayaran;

        /*
         * Satu pesanan hanya memiliki satu
         * transaksi Midtrans.
         */
        if (
            $pembayaran &&
            $pembayaran->snap_token
        ) {
            return redirect()
                ->route(
                    'customer.payment.show',
                    $pesanan->id
                )
                ->with(
                    'info',
                    'Token pembayaran sudah tersedia.'
                );
        }

        $grossAmount = (int) round(
            (float) $pesanan->total_harga
        );

        if (! $pembayaran) {
            $pembayaran = Pembayaran::create([
                'id_pesanan' =>
                $pesanan->id,

                'order_id' =>
                $this->midtrans
                    ->generateOrderId($pesanan),

                'transaction_id' =>
                null,

                'payment_type' =>
                'midtrans',

                'gross_amount' =>
                $grossAmount,

                'transaction_status' =>
                'pending',

                'fraud_status' =>
                null,

                'status_escrow' =>
                'belum_ditahan',

                'snap_token' =>
                null,

                'tanggal_bayar' =>
                null,

                'expires_at' =>
                now()->addHours(24),
            ]);
        }

        try {
            $snapToken = $this->midtrans
                ->createSnapToken(
                    $pesanan,
                    $pembayaran
                );

            $pembayaran->update([
                'snap_token' => $snapToken,
            ]);
        } catch (Throwable $exception) {
            Log::error(
                'Gagal membuat Snap Token.',
                [
                    'pesanan_id' =>
                    $pesanan->id,

                    'order_id' =>
                    $pembayaran->order_id,

                    'message' =>
                    $exception->getMessage(),
                ]
            );

            return back()->withErrors([
                'payment' =>
                'Pembayaran belum dapat dibuat. Silakan coba kembali.',
            ]);
        }

        return redirect()
            ->route(
                'customer.payment.show',
                $pesanan->id
            )
            ->with(
                'success',
                'Pembayaran Midtrans berhasil dibuat.'
            );
    }

    /**
     * Dipanggil browser setelah Snap selesai.
     *
     * Data status dari browser tidak dipercaya.
     * Backend mengambil status langsung ke Midtrans.
     */
    public function finish(
        Request $request,
        Pesanan $pesanan
    ): JsonResponse {
        $this->authorizeCustomer(
            $request,
            $pesanan
        );

        $pesanan->load('pembayaran');

        $pembayaran = $pesanan->pembayaran;

        abort_if(
            ! $pembayaran,
            404,
            'Data pembayaran tidak ditemukan.'
        );

        try {
            $gatewayStatus =
                $this->midtrans->getStatus(
                    $pembayaran->order_id
                );

            $result =
                $this->midtrans
                ->syncVerifiedStatus(
                    $pembayaran,
                    $gatewayStatus
                );

            return response()->json([
                'success' => true,

                'payment_confirmed' =>
                $result['successful'],

                'transaction_status' =>
                $result['transaction_status'],

                'message' =>
                $result['successful']
                    ? 'Pembayaran berhasil diverifikasi.'
                    : 'Pembayaran belum berhasil atau masih diproses.',
            ]);
        } catch (Throwable $exception) {
            Log::error(
                'Gagal menyegarkan status pembayaran.',
                [
                    'pesanan_id' =>
                    $pesanan->id,

                    'order_id' =>
                    $pembayaran->order_id,

                    'message' =>
                    $exception->getMessage(),
                ]
            );

            return response()->json(
                [
                    'success' => false,

                    'message' =>
                    'Status pembayaran belum dapat diverifikasi. Status akan diperbarui otomatis melalui Midtrans.',
                ],
                502
            );
        }
    }

    /**
     * Endpoint publik untuk server Midtrans.
     */
    public function notification(
        Request $request
    ): JsonResponse {
        $payload = $request->json()->all();

        if (
            ! $this->midtrans
                ->verifyWebhookSignature($payload)
        ) {
            Log::warning(
                'Webhook Midtrans dengan signature tidak valid.',
                [
                    'order_id' =>
                    $payload['order_id']
                        ?? null,
                ]
            );

            return response()->json(
                [
                    'success' => false,
                    'message' =>
                    'Signature tidak valid.',
                ],
                403
            );
        }

        $orderId = (string) (
            $payload['order_id'] ?? ''
        );

        $pembayaran = Pembayaran::where(
            'order_id',
            $orderId
        )->first();

        if (! $pembayaran) {
            Log::warning(
                'Webhook untuk order tidak dikenal.',
                [
                    'order_id' => $orderId,
                ]
            );

            return response()->json(
                [
                    'success' => false,
                    'message' =>
                    'Pembayaran tidak ditemukan.',
                ],
                404
            );
        }

        try {
            /*
             * Challenge verification:
             * status diambil langsung dari Midtrans,
             * bukan dipercaya dari payload webhook.
             */
            $gatewayStatus =
                $this->midtrans
                ->getStatus($orderId);

            $result =
                $this->midtrans
                ->syncVerifiedStatus(
                    $pembayaran,
                    $gatewayStatus
                );

            return response()->json([
                'success' => true,

                'transaction_status' =>
                $result['transaction_status'],

                'message' =>
                'Notifikasi berhasil diproses.',
            ]);
        } catch (Throwable $exception) {
            Log::error(
                'Webhook Midtrans gagal diproses.',
                [
                    'order_id' =>
                    $orderId,

                    'message' =>
                    $exception->getMessage(),
                ]
            );

            /*
             * Respons non-2xx memungkinkan
             * Midtrans mengetahui proses gagal.
             */
            return response()->json(
                [
                    'success' => false,
                    'message' =>
                    'Notifikasi gagal diproses.',
                ],
                500
            );
        }
    }

    /**
     * Hanya untuk pengembangan lokal.
     */
    public function simulateSuccess(
        Request $request,
        Pesanan $pesanan
    ): RedirectResponse {
        abort_unless(
            app()->environment([
                'local',
                'testing',
            ]),
            404
        );

        $this->authorizeCustomer(
            $request,
            $pesanan
        );

        $pembayaran = $pesanan->pembayaran;

        if (! $pembayaran) {
            return back()->withErrors([
                'payment' =>
                'Data pembayaran belum dibuat.',
            ]);
        }

        $pembayaran->update([
            'transaction_id' =>
            'SIM-' .
                now()->format('YmdHis'),

            'payment_type' =>
            'simulation',

            'transaction_status' =>
            'settlement',

            'fraud_status' =>
            'accept',

            'status_escrow' =>
            'ditahan',

            'tanggal_bayar' =>
            now(),
        ]);

        if (
            $pesanan->status_pesanan ===
            'menunggu_pembayaran'
        ) {
            $pesanan->update([
                'status_pesanan' =>
                'dibayar',
            ]);
        }

        return redirect()
            ->route(
                'customer.order.show',
                $pesanan->id
            )
            ->with(
                'success',
                'Pembayaran lokal berhasil disimulasikan.'
            );
    }
}
