<?php

namespace App\Services;

use App\Models\Pembayaran;
use App\Models\Pesanan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Midtrans\Config;
use Midtrans\Snap;
use Midtrans\Transaction;
use RuntimeException;
use Throwable;

class MidtransPaymentService
{
    public function configure(): void
    {
        $serverKey = trim(
            (string) config('services.midtrans.server_key')
        );

        $clientKey = trim(
            (string) config('services.midtrans.client_key')
        );

        if ($serverKey === '' || $clientKey === '') {
            throw new RuntimeException(
                'Konfigurasi Midtrans belum lengkap.'
            );
        }

        Config::$serverKey = $serverKey;

        Config::$isProduction = (bool) config(
            'services.midtrans.is_production',
            false
        );

        Config::$isSanitized = (bool) config(
            'services.midtrans.is_sanitized',
            true
        );

        Config::$is3ds = (bool) config(
            'services.midtrans.is_3ds',
            true
        );
    }

    public function generateOrderId(
        Pesanan $pesanan
    ): string {
        return sprintf(
            'JSK-%d-%s-%s',
            $pesanan->id,
            now()->format('YmdHis'),
            Str::upper(Str::random(6))
        );
    }

    public function createSnapToken(
        Pesanan $pesanan,
        Pembayaran $pembayaran
    ): string {
        $this->configure();

        $pesanan->loadMissing([
            'customer',
            'jasa',
        ]);

        $grossAmount = (int) round(
            (float) $pembayaran->gross_amount
        );

        $itemName = Str::limit(
            $pesanan->jasa->nama_jasa
                ?? 'Pembayaran Jasa',
            50,
            ''
        );

        $params = [
            'transaction_details' => [
                'order_id' =>
                $pembayaran->order_id,

                'gross_amount' =>
                $grossAmount,
            ],

            'item_details' => [
                [
                    'id' =>
                    'JASA-' . $pesanan->id_jasa,

                    'price' =>
                    $grossAmount,

                    'quantity' =>
                    1,

                    'name' =>
                    $itemName,
                ],
            ],

            'customer_details' => [
                'first_name' =>
                $pesanan->customer->nama
                    ?? 'Customer',

                'email' =>
                $pesanan->customer->email
                    ?? null,

                'phone' =>
                $pesanan->customer->no_hp
                    ?? null,
            ],

            /*
             * URL ini hanya untuk mengarahkan
             * pengguna kembali ke aplikasi.
             *
             * URL ini bukan sumber status pembayaran.
             */
            'callbacks' => [
                'finish' => route(
                    'customer.payment.show',
                    $pesanan->id
                ),
            ],

            'expiry' => [
                'start_time' => now(
                    'Asia/Jakarta'
                )->format('Y-m-d H:i:s O'),

                'unit' => 'hours',
                'duration' => 24,
            ],
        ];

        return Snap::getSnapToken($params);
    }

    public function verifyWebhookSignature(
        array $payload
    ): bool {
        $orderId = (string) (
            $payload['order_id'] ?? ''
        );

        $statusCode = (string) (
            $payload['status_code'] ?? ''
        );

        /*
         * Jangan ubah format gross_amount.
         * Gunakan string asli dari webhook.
         */
        $grossAmount = (string) (
            $payload['gross_amount'] ?? ''
        );

        $signatureKey = (string) (
            $payload['signature_key'] ?? ''
        );

        if (
            $orderId === '' ||
            $statusCode === '' ||
            $grossAmount === '' ||
            $signatureKey === ''
        ) {
            return false;
        }

        $serverKey = (string) config(
            'services.midtrans.server_key'
        );

        $expectedSignature = hash(
            'sha512',
            $orderId .
                $statusCode .
                $grossAmount .
                $serverKey
        );

        return hash_equals(
            $expectedSignature,
            $signatureKey
        );
    }

    public function getStatus(
        string $orderId
    ): object {
        $this->configure();

        return Transaction::status($orderId);
    }

    /**
     * Menyinkronkan status yang sudah diverifikasi
     * langsung dari server Midtrans.
     */
    public function syncVerifiedStatus(
        Pembayaran $pembayaran,
        object $gatewayStatus
    ): array {
        $receivedOrderId = (string) (
            $gatewayStatus->order_id ?? ''
        );

        $receivedAmount = (int) round(
            (float) (
                $gatewayStatus->gross_amount ?? 0
            )
        );

        $expectedAmount = (int) round(
            (float) $pembayaran->gross_amount
        );

        if (
            $receivedOrderId !==
            (string) $pembayaran->order_id ||
            $receivedAmount !== $expectedAmount
        ) {
            throw new RuntimeException(
                'Order ID atau nominal Midtrans tidak cocok.'
            );
        }

        $statusCode = (string) (
            $gatewayStatus->status_code ?? ''
        );

        $transactionStatus = strtolower(
            (string) (
                $gatewayStatus->transaction_status
                ?? 'pending'
            )
        );

        $fraudStatus = isset(
            $gatewayStatus->fraud_status
        )
            ? strtolower(
                (string) $gatewayStatus->fraud_status
            )
            : null;

        $paymentSuccessful =
            $statusCode === '200' &&
            (
                $transactionStatus ===
                'settlement' ||
                (
                    $transactionStatus ===
                    'capture' &&
                    $fraudStatus === 'accept'
                )
            );

        $paymentFailed = in_array(
            $transactionStatus,
            [
                'deny',
                'cancel',
                'expire',
                'failure',
            ],
            true
        );

        return DB::transaction(
            function () use (
                $pembayaran,
                $gatewayStatus,
                $transactionStatus,
                $fraudStatus,
                $paymentSuccessful,
                $paymentFailed
            ): array {
                $lockedPayment = Pembayaran::query()
                    ->whereKey($pembayaran->id)
                    ->lockForUpdate()
                    ->firstOrFail();

                $lockedOrder = Pesanan::query()
                    ->whereKey(
                        $lockedPayment->id_pesanan
                    )
                    ->lockForUpdate()
                    ->firstOrFail();

                $firstSuccessfulPayment =
                    $paymentSuccessful &&
                    $lockedPayment->tanggal_bayar
                    === null;

                $lockedPayment->fill([
                    'transaction_id' =>
                    $gatewayStatus->transaction_id
                        ?? $lockedPayment->transaction_id,

                    'payment_type' =>
                    $gatewayStatus->payment_type
                        ?? $lockedPayment->payment_type,

                    'transaction_status' =>
                    $transactionStatus,

                    'fraud_status' =>
                    $fraudStatus,

                    'status_message' =>
                    $gatewayStatus->status_message
                        ?? null,

                    'gateway_updated_at' =>
                    now(),
                ]);

                if ($paymentSuccessful) {
                    /*
                     * Jangan menimpa escrow yang sudah
                     * dicairkan atau dikembalikan.
                     */
                    if (
                        $lockedPayment->status_escrow
                        === 'belum_ditahan'
                    ) {
                        $lockedPayment->status_escrow =
                            'ditahan';
                    }

                    $lockedPayment->tanggal_bayar =
                        $lockedPayment->tanggal_bayar
                        ?? now();

                    /*
                     * Jangan mengembalikan pesanan yang
                     * sudah diproses menjadi "dibayar".
                     */
                    if (
                        $lockedOrder->status_pesanan ===
                        'menunggu_pembayaran'
                    ) {
                        $lockedOrder->status_pesanan =
                            'dibayar';

                        $lockedOrder->save();
                    }
                }

                /*
                 * Dengan model satu pembayaran per order,
                 * pembayaran terminal yang gagal membatalkan
                 * pesanan. Customer membuat order baru jika
                 * ingin mencoba lagi.
                 */
                if (
                    $paymentFailed &&
                    $lockedOrder->status_pesanan ===
                    'menunggu_pembayaran'
                ) {
                    $lockedOrder->status_pesanan =
                        'dibatalkan';

                    $lockedOrder->save();
                }

                /*
                 * Refund penuh berasal dari Midtrans.
                 * Partial refund perlu penanganan nominal
                 * tersendiri dan tidak diproses otomatis.
                 */
                if (
                    $transactionStatus === 'refund' &&
                    $lockedPayment->status_escrow !==
                    'dicairkan'
                ) {
                    $lockedPayment->status_escrow =
                        'dikembalikan';
                }

                if (
                    $transactionStatus ===
                    'partial_refund'
                ) {
                    Log::warning(
                        'Partial refund perlu rekonsiliasi admin.',
                        [
                            'pembayaran_id' =>
                            $lockedPayment->id,

                            'order_id' =>
                            $lockedPayment->order_id,
                        ]
                    );
                }

                $lockedPayment->save();

                /*
                 * Notifikasi hanya dibuat sekali,
                 * setelah transaksi database berhasil.
                 */
                if ($firstSuccessfulPayment) {
                    $orderId = $lockedOrder->id;
                    $freelancerId =
                        $lockedOrder->id_freelancer;
                    $customerId =
                        $lockedOrder->id_customer;

                    DB::afterCommit(
                        function () use (
                            $orderId,
                            $freelancerId,
                            $customerId
                        ): void {
                            try {
                                NotifikasiService::kirim(
                                    $freelancerId,
                                    'Pesanan Baru Sudah Dibayar',
                                    'Customer telah melakukan pembayaran untuk pesanan #' .
                                        $orderId .
                                        '. Silakan mulai proses pekerjaan.',
                                    'order',
                                    route(
                                        'freelancer.pesanan.show',
                                        $orderId,
                                        false
                                    )
                                );
                            } catch (Throwable $exception) {
                                Log::error(
                                    'Gagal mengirim notifikasi pembayaran ke freelancer.',
                                    [
                                        'pesanan_id' => $orderId,
                                        'freelancer_id' => $freelancerId,
                                        'message' => $exception->getMessage(),
                                    ]
                                );
                            }

                            try {
                                NotifikasiService::kirim(
                                    $customerId,
                                    'Pembayaran Berhasil',
                                    'Pembayaran untuk pesanan #' .
                                        $orderId .
                                        ' berhasil. Dana ditahan sementara oleh sistem escrow.',
                                    'pembayaran',
                                    route(
                                        'customer.order.show',
                                        $orderId,
                                        false
                                    )
                                );
                            } catch (Throwable $exception) {
                                Log::error(
                                    'Gagal mengirim notifikasi pembayaran ke customer.',
                                    [
                                        'pesanan_id' => $orderId,
                                        'customer_id' => $customerId,
                                        'message' => $exception->getMessage(),
                                    ]
                                );
                            }
                        }
                    );
                }

                return [
                    'successful' =>
                    $paymentSuccessful,

                    'transaction_status' =>
                    $transactionStatus,

                    'fraud_status' =>
                    $fraudStatus,
                ];
            },
            3
        );
    }
}
