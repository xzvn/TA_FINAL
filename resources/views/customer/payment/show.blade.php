@extends('layouts.customer')

@section('title', 'Payment - JasaKampus')

@php
    $payment = $pesanan->pembayaran;

    $midtransIsProduction = filter_var(
        config('services.midtrans.is_production', false),
        FILTER_VALIDATE_BOOLEAN
    );

    $paymentExpiresAt = optional($payment)->expires_at
        ? \Illuminate\Support\Carbon::parse($payment->expires_at)
        : $pesanan->created_at->copy()->addHours(24);

    $isWaitingPayment =
        $pesanan->status_pesanan === 'menunggu_pembayaran';

    $isPaid = in_array(
        $pesanan->status_pesanan,
        [
            'dibayar',
            'diproses',
            'menunggu_approve',
            'revisi',
            'selesai',
        ],
        true
    );
@endphp

@section('content')
<section class="px-6 py-6">

    {{-- HEADER STATUS --}}
    <div class="mb-6 rounded-2xl border border-slate-200 bg-white p-6 text-center shadow-sm">
        @if ($isWaitingPayment)
            <p class="text-xs font-bold uppercase text-slate-500">
                Selesaikan pembayaran sebelum waktu habis
            </p>

            <h1
                id="payment-countdown"
                data-expires-at="{{ $paymentExpiresAt->toIso8601String() }}"
                class="mt-2 text-4xl font-bold text-blue-600">
                --:--:--
            </h1>

            <span class="mt-3 inline-block rounded-full bg-red-100 px-4 py-1 text-xs font-bold text-red-600">
                Batas waktu pembayaran 24 jam
            </span>
        @elseif ($isPaid)
            <h1 class="mt-2 text-4xl font-bold text-green-600">
                Payment Successful
            </h1>

            <span class="mt-3 inline-block rounded-full bg-green-100 px-4 py-1 text-xs font-bold text-green-600">
                Dana berhasil ditahan oleh escrow
            </span>
        @else
            <h1 class="mt-2 text-3xl font-bold text-slate-700">
                Pembayaran Tidak Aktif
            </h1>

            <span class="mt-3 inline-block rounded-full bg-slate-100 px-4 py-1 text-xs font-bold text-slate-600">
                Status pesanan: {{ str_replace('_', ' ', $pesanan->status_pesanan) }}
            </span>
        @endif
    </div>

    {{-- FLASH MESSAGE --}}
    @if (session('success'))
        <div class="mb-6 rounded-xl border border-green-200 bg-green-100 px-5 py-4 text-green-700">
            {{ session('success') }}
        </div>
    @endif

    @if (session('info'))
        <div class="mb-6 rounded-xl border border-blue-200 bg-blue-50 px-5 py-4 text-blue-700">
            {{ session('info') }}
        </div>
    @endif

    @if ($errors->has('payment'))
        <div class="mb-6 rounded-xl border border-red-200 bg-red-50 px-5 py-4 text-red-700">
            {{ $errors->first('payment') }}
        </div>
    @endif

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-12">

        {{-- RINGKASAN PESANAN --}}
        <aside class="space-y-6 lg:col-span-4">
            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                <h2 class="font-bold text-slate-900">
                    Ringkasan Pesanan
                </h2>

                <div class="mt-5 space-y-4 text-sm">
                    <div class="flex items-start justify-between gap-4">
                        <span class="text-slate-500">
                            Nomor Pesanan
                        </span>

                        <span class="text-right font-bold text-slate-900">
                            #{{ $pesanan->id }}
                        </span>
                    </div>

                    <div class="flex items-start justify-between gap-4">
                        <span class="text-slate-500">
                            Freelancer
                        </span>

                        <span class="text-right font-semibold text-slate-900">
                            {{ $pesanan->jasa->freelancer->nama ?? '-' }}
                        </span>
                    </div>

                    <div class="flex items-start justify-between gap-4">
                        <span class="text-slate-500">
                            Jasa
                        </span>

                        <span class="max-w-[190px] text-right font-semibold text-slate-900">
                            {{ $pesanan->jasa->nama_jasa }}
                        </span>
                    </div>

                    <div class="flex items-center justify-between border-t border-slate-100 pt-4">
                        <span class="text-slate-500">
                            Harga Jasa
                        </span>

                        <span class="font-semibold text-slate-900">
                            Rp {{ number_format($pesanan->total_harga, 0, ',', '.') }}
                        </span>
                    </div>

                    <div class="flex items-center justify-between">
                        <span class="text-slate-500">
                            Biaya Layanan
                        </span>

                        <span class="font-semibold text-slate-900">
                            Rp 0
                        </span>
                    </div>

                    <div class="flex items-center justify-between border-t border-slate-100 pt-4">
                        <span class="font-bold text-blue-700">
                            Total Tagihan
                        </span>

                        <span class="font-bold text-blue-700">
                            Rp {{ number_format($pesanan->total_harga, 0, ',', '.') }}
                        </span>
                    </div>
                </div>
            </div>

            {{-- ACTION PAYMENT --}}
            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">

                @if ($isWaitingPayment && ! optional($payment)->snap_token)
                    <form
                        method="POST"
                        action="{{ route('customer.payment.pay', $pesanan->id) }}">
                        @csrf

                        <button
                            type="submit"
                            class="w-full rounded-xl bg-blue-600 px-5 py-3 font-bold text-white hover:bg-blue-700">
                            Buat Pembayaran Midtrans
                        </button>
                    </form>

                    <a
                        href="{{ url('/dashboard') }}"
                        class="mt-3 block w-full rounded-xl bg-slate-100 px-5 py-3 text-center font-bold text-slate-700 hover:bg-slate-200">
                        Kembali ke Dashboard
                    </a>

                @elseif ($isWaitingPayment && optional($payment)->snap_token)
                    <button
                        type="button"
                        id="pay-button"
                        class="w-full rounded-xl bg-green-600 px-5 py-3 font-bold text-white hover:bg-green-700 disabled:cursor-not-allowed disabled:opacity-60">
                        Bayar Sekarang dengan Midtrans
                    </button>

                    @if (app()->environment(['local', 'testing']))
                        <form
                            action="{{ route('customer.payment.simulate-success', $pesanan->id) }}"
                            method="POST"
                            class="mt-4">
                            @csrf

                            <button
                                type="submit"
                                onclick="return confirm('Simulasikan pembayaran berhasil? Fitur ini hanya untuk testing lokal.')"
                                class="w-full rounded-xl bg-amber-500 px-5 py-3 font-bold text-white hover:bg-amber-600">
                                Simulasi Pembayaran Berhasil
                            </button>
                        </form>
                    @endif

                    <a
                        href="{{ url('/dashboard') }}"
                        class="mt-3 block w-full rounded-xl bg-slate-100 px-5 py-3 text-center font-bold text-slate-700 hover:bg-slate-200">
                        Kembali ke Dashboard
                    </a>

                @elseif ($isPaid)
                    <div class="rounded-xl border border-green-200 bg-green-50 p-4">
                        <p class="text-sm font-bold text-green-700">
                            Pembayaran berhasil
                        </p>

                        <p class="mt-1 text-xs text-green-600">
                            Pesanan sudah masuk ke freelancer.
                        </p>
                    </div>

                    <a
                        href="{{ url('/dashboard') }}"
                        class="mt-3 block w-full rounded-xl bg-slate-100 px-5 py-3 text-center font-bold text-slate-700 hover:bg-slate-200">
                        Kembali ke Dashboard
                    </a>

                @else
                    <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                        <p class="text-sm font-bold text-slate-700">
                            Pembayaran tidak dapat dilanjutkan
                        </p>

                        <p class="mt-1 text-xs text-slate-500">
                            Status pesanan saat ini:
                            {{ str_replace('_', ' ', $pesanan->status_pesanan) }}.
                        </p>
                    </div>

                    <a
                        href="{{ url('/dashboard') }}"
                        class="mt-3 block w-full rounded-xl bg-slate-100 px-5 py-3 text-center font-bold text-slate-700 hover:bg-slate-200">
                        Kembali ke Dashboard
                    </a>
                @endif
            </div>
        </aside>

        {{-- INFORMASI PEMBAYARAN --}}
        <div class="space-y-6 lg:col-span-8">
            <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                <div class="flex items-center justify-between border-b border-slate-200 px-6 py-4">
                    <h2 class="font-bold text-slate-900">
                        Pembayaran Midtrans
                    </h2>

                    <span class="rounded bg-slate-900 px-3 py-1 text-xs font-bold text-white">
                        {{ $midtransIsProduction ? 'Production' : 'Sandbox' }}
                    </span>
                </div>

                <div class="p-8 text-center">
                    {{-- ILUSTRASI PAYMENT --}}
                    <div class="mx-auto flex h-64 w-64 items-center justify-center rounded-2xl border border-blue-100 bg-blue-50">
                        <div class="grid h-44 w-44 grid-cols-5 gap-1 rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
                            @for ($i = 1; $i <= 25; $i++)
                                <div
                                    class="rounded-sm {{ in_array(
                                        $i,
                                        [1, 2, 3, 6, 8, 11, 13, 15, 18, 20, 21, 22, 24, 25],
                                        true
                                    )
                                        ? 'bg-slate-900'
                                        : 'bg-slate-100' }}">
                                </div>
                            @endfor
                        </div>
                    </div>

                    <h3 class="mt-6 text-xl font-bold text-slate-900">
                        Bayar melalui Midtrans
                    </h3>

                    <p class="mx-auto mt-2 max-w-xl text-sm text-slate-500">
                        Klik tombol Bayar Sekarang dengan Midtrans, kemudian pilih
                        QRIS, e-wallet, Virtual Account, atau metode pembayaran lain
                        yang tersedia pada popup Midtrans
                        {{ $midtransIsProduction ? 'Production' : 'Sandbox' }}.
                    </p>

                    <div class="mx-auto mt-8 max-w-xl rounded-2xl border border-slate-200 bg-slate-50 p-5 text-left">
                        <h4 class="mb-3 font-bold text-slate-900">
                            Cara Pembayaran
                        </h4>

                        <ol class="list-inside list-decimal space-y-2 text-sm text-slate-600">
                            <li>
                                Klik tombol
                                <strong>Buat Pembayaran Midtrans</strong>.
                            </li>

                            <li>
                                Setelah token dibuat, klik
                                <strong>Bayar Sekarang dengan Midtrans</strong>.
                            </li>

                            <li>
                                Pilih QRIS, e-wallet, Virtual Account, atau metode
                                pembayaran lain.
                            </li>

                            <li>
                                Selesaikan pembayaran mengikuti instruksi Midtrans.
                            </li>

                            <li>
                                Status pembayaran akan diverifikasi langsung oleh
                                server JasaKampus.
                            </li>
                        </ol>
                    </div>
                </div>
            </div>

            {{-- STATUS TRANSAKSI --}}
            @php
                $currentStep = 1;

                if ($isWaitingPayment) {
                    $currentStep = 2;
                }

                if (in_array(
                    $pesanan->status_pesanan,
                    [
                        'dibayar',
                        'diproses',
                        'menunggu_approve',
                        'revisi',
                    ],
                    true
                )) {
                    $currentStep = 3;
                }

                if ($pesanan->status_pesanan === 'selesai') {
                    $currentStep = 4;
                }
            @endphp

            <div class="rounded-2xl border border-slate-200 bg-white p-8 shadow-sm">
                <h2 class="mb-12 text-center text-2xl font-bold text-slate-900">
                    Status Transaksi
                </h2>

                <div class="relative px-8">
                    <div class="absolute left-16 right-16 top-7 h-1 rounded-full bg-slate-200">
                    </div>

                    <div
                        class="absolute left-16 top-7 h-1 rounded-full bg-blue-600
                        @if ($currentStep === 1)
                            w-0
                        @elseif ($currentStep === 2)
                            w-[33%]
                        @elseif ($currentStep === 3)
                            w-[66%]
                        @else
                            right-16
                        @endif">
                    </div>

                    <div class="relative grid grid-cols-4 gap-4">

                        {{-- STEP 1 --}}
                        <div class="text-center">
                            <div
                                class="mx-auto flex items-center justify-center rounded-full bg-blue-600 font-bold text-white shadow ring-8 ring-blue-100"
                                style="width: 56px; height: 56px;">
                                ✓
                            </div>

                            <p class="mt-5 text-sm font-bold text-blue-700 md:text-base">
                                Pesanan Dibuat
                            </p>

                            <p class="mt-1 text-xs text-slate-500">
                                {{ $pesanan->created_at->format('d M Y, H:i') }}
                            </p>
                        </div>

                        {{-- STEP 2 --}}
                        <div class="text-center">
                            <div
                                class="mx-auto flex items-center justify-center rounded-full font-bold shadow ring-8
                                {{ $currentStep >= 2
                                    ? 'bg-blue-600 text-white ring-blue-100'
                                    : 'bg-slate-100 text-slate-400 ring-slate-100' }}"
                                style="width: 56px; height: 56px;">
                                💳
                            </div>

                            <p
                                class="mt-5 text-sm font-bold md:text-base
                                {{ $currentStep >= 2
                                    ? 'text-blue-700'
                                    : 'text-slate-400' }}">
                                Menunggu Pembayaran
                            </p>

                            <p class="mt-1 text-xs text-slate-500">
                                Menunggu konfirmasi Midtrans
                            </p>
                        </div>

                        {{-- STEP 3 --}}
                        <div class="text-center">
                            <div
                                class="mx-auto flex items-center justify-center rounded-full font-bold shadow ring-8
                                {{ $currentStep >= 3
                                    ? 'bg-blue-600 text-white ring-blue-100'
                                    : 'bg-slate-100 text-slate-400 ring-slate-100' }}"
                                style="width: 56px; height: 56px;">
                                ⚙
                            </div>

                            <p
                                class="mt-5 text-sm font-bold md:text-base
                                {{ $currentStep >= 3
                                    ? 'text-blue-700'
                                    : 'text-slate-400' }}">
                                Diproses
                            </p>

                            <p class="mt-1 text-xs text-slate-400">
                                Pengerjaan oleh freelancer
                            </p>
                        </div>

                        {{-- STEP 4 --}}
                        <div class="text-center">
                            <div
                                class="mx-auto flex items-center justify-center rounded-full font-bold shadow ring-8
                                {{ $currentStep >= 4
                                    ? 'bg-blue-600 text-white ring-blue-100'
                                    : 'bg-slate-100 text-slate-400 ring-slate-100' }}"
                                style="width: 56px; height: 56px;">
                                ✓
                            </div>

                            <p
                                class="mt-5 text-sm font-bold md:text-base
                                {{ $currentStep >= 4
                                    ? 'text-blue-700'
                                    : 'text-slate-400' }}">
                                Selesai
                            </p>

                            <p class="mt-1 text-xs text-slate-400">
                                Pesanan diselesaikan
                            </p>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@push('scripts')
@if ($isWaitingPayment && optional($payment)->snap_token)
    <script
        src="{{ $midtransIsProduction
            ? 'https://app.midtrans.com/snap/snap.js'
            : 'https://app.sandbox.midtrans.com/snap/snap.js' }}"
        data-client-key="{{ config('services.midtrans.client_key') }}">
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const payButton =
                document.getElementById('pay-button');

            if (!payButton) {
                return;
            }

            const snapToken =
                @json($payment->snap_token);

            const finishUrl =
                @json(route(
                    'customer.payment.finish',
                    $pesanan
                ));

            const showUrl =
                @json(route(
                    'customer.payment.show',
                    $pesanan
                ));

            const csrfToken =
                @json(csrf_token());

            async function refreshPaymentStatus() {
                try {
                    const response = await fetch(
                        finishUrl,
                        {
                            method: 'POST',
                            credentials: 'same-origin',

                            headers: {
                                'Accept': 'application/json',
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': csrfToken
                            },

                            /*
                             * Browser tidak mengirim
                             * status pembayaran.
                             *
                             * Backend memeriksa status
                             * langsung ke Midtrans.
                             */
                            body: JSON.stringify({})
                        }
                    );

                    const contentType =
                        response.headers.get(
                            'content-type'
                        ) || '';

                    const data =
                        contentType.includes(
                            'application/json'
                        )
                            ? await response.json()
                            : {};

                    if (!response.ok) {
                        throw new Error(
                            data.message ||
                            'Status pembayaran belum dapat diverifikasi.'
                        );
                    }

                    return data;
                } catch (error) {
                    console.error(
                        'Gagal memperbarui status pembayaran:',
                        error
                    );

                    return null;
                }
            }

            async function refreshAndRedirect() {
                await refreshPaymentStatus();

                window.location.href =
                    showUrl;
            }

            payButton.addEventListener(
                'click',
                function () {
                    if (
                        typeof window.snap === 'undefined' ||
                        typeof window.snap.pay !== 'function'
                    ) {
                        alert(
                            'Midtrans belum berhasil dimuat. Muat ulang halaman dan coba kembali.'
                        );

                        return;
                    }

                    payButton.disabled = true;
                    payButton.textContent =
                        'Membuka Midtrans...';

                    window.snap.pay(
                        snapToken,
                        {
                            onSuccess:
                                async function () {
                                    await refreshAndRedirect();
                                },

                            onPending:
                                async function () {
                                    await refreshAndRedirect();
                                },

                            onError:
                                async function () {
                                    await refreshAndRedirect();
                                },

                            onClose:
                                function () {
                                    payButton.disabled =
                                        false;

                                    payButton.textContent =
                                        'Bayar Sekarang dengan Midtrans';
                                }
                        }
                    );
                }
            );
        });
    </script>
@endif

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const countdown =
            document.getElementById(
                'payment-countdown'
            );

        if (!countdown) {
            return;
        }

        const expiresAtValue =
            countdown.dataset.expiresAt;

        if (!expiresAtValue) {
            countdown.textContent =
                '--:--:--';

            return;
        }

        const expiresAt =
            new Date(expiresAtValue).getTime();

        if (Number.isNaN(expiresAt)) {
            countdown.textContent =
                '--:--:--';

            return;
        }

        function updateCountdown() {
            const remaining =
                expiresAt - Date.now();

            if (remaining <= 0) {
                countdown.textContent =
                    '00:00:00';

                const payButton =
                    document.getElementById(
                        'pay-button'
                    );

                if (payButton) {
                    payButton.disabled = true;
                    payButton.textContent =
                        'Waktu Pembayaran Habis';
                }

                return false;
            }

            const totalSeconds =
                Math.floor(remaining / 1000);

            const hours = String(
                Math.floor(totalSeconds / 3600)
            ).padStart(2, '0');

            const minutes = String(
                Math.floor(
                    (totalSeconds % 3600) / 60
                )
            ).padStart(2, '0');

            const seconds = String(
                totalSeconds % 60
            ).padStart(2, '0');

            countdown.textContent =
                `${hours}:${minutes}:${seconds}`;

            return true;
        }

        updateCountdown();

        const timer = setInterval(
            function () {
                if (!updateCountdown()) {
                    clearInterval(timer);
                }
            },
            1000
        );
    });
</script>
@endpush