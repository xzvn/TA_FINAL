@extends('layouts.customer')

@section('title', 'Payment - JasaKampus')

@section('content')
<section class="px-6 py-6">
    <div class="bg-white border border-slate-200 rounded-2xl shadow-sm p-6 mb-6 text-center">
        <p class="text-xs font-bold text-slate-500 uppercase">
            Selesaikan pembayaran sebelum waktu habis
        </p>

        @if ($pesanan->status_pesanan === 'menunggu_pembayaran')
        <h1 id="payment-countdown" class="text-4xl font-bold text-blue-600 mt-2">
            23:59:54
        </h1>

        <span class="inline-block mt-3 px-4 py-1 bg-red-100 text-red-600 rounded-full text-xs font-bold">
            Batas waktu pembayaran 24 jam
        </span>
        @else
        <h1 class="text-4xl font-bold text-green-600 mt-2">
            Payment Successful
        </h1>

        <span class="inline-block mt-3 px-4 py-1 bg-green-100 text-green-600 rounded-full text-xs font-bold">
            Dana berhasil ditahan oleh escrow
        </span>
        @endif
    </div>

    @if (session('success'))
    <div class="mb-6 px-5 py-4 bg-green-100 text-green-700 rounded-xl border border-green-200">
        {{ session('success') }}
    </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

        {{-- LEFT: ORDER SUMMARY --}}
        <aside class="lg:col-span-4 space-y-6">
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
                <h2 class="font-bold text-slate-900">
                    Ringkasan Pesanan
                </h2>

                <div class="mt-5 space-y-4 text-sm">
                    <div class="flex items-start justify-between gap-4">
                        <span class="text-slate-500">Nomor Pesanan</span>
                        <span class="font-bold text-slate-900 text-right">
                            #{{ $pesanan->id }}
                        </span>
                    </div>

                    <div class="flex items-start justify-between gap-4">
                        <span class="text-slate-500">Freelancer</span>
                        <span class="font-semibold text-slate-900 text-right">
                            {{ $pesanan->jasa->freelancer->nama ?? '-' }}
                        </span>
                    </div>

                    <div class="flex items-start justify-between gap-4">
                        <span class="text-slate-500">Jasa</span>
                        <span class="font-semibold text-slate-900 text-right max-w-[190px]">
                            {{ $pesanan->jasa->nama_jasa }}
                        </span>
                    </div>

                    <div class="border-t border-slate-100 pt-4 flex items-center justify-between">
                        <span class="text-slate-500">Harga Jasa</span>
                        <span class="font-semibold text-slate-900">
                            Rp {{ number_format($pesanan->total_harga, 0, ',', '.') }}
                        </span>
                    </div>

                    <div class="flex items-center justify-between">
                        <span class="text-slate-500">Biaya Layanan</span>
                        <span class="font-semibold text-slate-900">
                            Rp 0
                        </span>
                    </div>

                    <div class="border-t border-slate-100 pt-4 flex items-center justify-between">
                        <span class="font-bold text-blue-700">
                            Total Tagihan
                        </span>

                        <span class="font-bold text-blue-700">
                            Rp {{ number_format($pesanan->total_harga, 0, ',', '.') }}
                        </span>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
                @if ($pesanan->status_pesanan === 'menunggu_pembayaran' && ! optional($pesanan->pembayaran)->snap_token)
                <form method="POST"
                    action="{{ route('customer.payment.pay', $pesanan->id) }}">
                    @csrf

                    <button type="submit"
                        class="w-full px-5 py-3 bg-blue-600 text-white rounded-xl font-bold hover:bg-blue-700">
                        Buat Pembayaran Midtrans
                    </button>
                </form>



                <a href="{{ url('/dashboard') }}"
                    class="block text-center mt-3 w-full px-5 py-3 bg-red-50 text-red-600 rounded-xl font-bold hover:bg-red-100">
                    Batalkan Pesanan
                </a>



                @elseif ($pesanan->status_pesanan === 'menunggu_pembayaran' && optional($pesanan->pembayaran)->snap_token)
                <button type="button"
                    id="pay-button"
                    class="w-full px-5 py-3 bg-green-600 text-white rounded-xl font-bold hover:bg-green-700">
                    Bayar Sekarang dengan Midtrans
                </button>

                @if (app()->environment('local'))
                <form action="{{ route('customer.payment.simulate-success', $pesanan->id) }}"
                    method="POST"
                    class="mt-4">
                    @csrf

                    <button type="submit"
                        onclick="return confirm('Simulasikan pembayaran berhasil? Fitur ini hanya untuk testing lokal.')"
                        class="w-full px-5 py-3 rounded-xl bg-green-600 text-white font-bold hover:bg-green-700">
                        Skip Pembayaran / Simulasi Berhasil
                    </button>
                </form>
                @endif

                <form method="POST"
                    action="{{ route('customer.payment.pay', $pesanan->id) }}"
                    class="mt-3">
                    @csrf

                    <button type="submit"
                        class="w-full px-5 py-3 bg-slate-100 text-slate-700 rounded-xl font-bold hover:bg-slate-200">
                        Buat Ulang Token Pembayaran
                    </button>
                </form>

                <a href="{{ url('/dashboard') }}"
                    class="block text-center mt-3 w-full px-5 py-3 bg-red-50 text-red-600 rounded-xl font-bold hover:bg-red-100">
                    Batalkan Pesanan
                </a>
                @else
                <div class="p-4 bg-green-50 border border-green-200 rounded-xl">
                    <p class="text-sm font-bold text-green-700">
                        Pembayaran berhasil
                    </p>
                    <p class="text-xs text-green-600 mt-1">
                        Pesanan sudah masuk ke freelancer.
                    </p>
                </div>

                <a href="{{ url('/dashboard') }}"
                    class="block text-center mt-3 w-full px-5 py-3 bg-slate-100 text-slate-700 rounded-xl font-bold hover:bg-slate-200">
                    Kembali ke Dashboard
                </a>
                @endif
            </div>
        </aside>

        {{-- CENTER: PAYMENT --}}
        <div class="lg:col-span-8 space-y-6">
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-200 flex items-center justify-between">
                    <h2 class="font-bold text-slate-900">
                        Pembayaran Midtrans
                    </h2>

                    <span class="px-3 py-1 bg-slate-900 text-white rounded text-xs font-bold">
                        Sandbox
                    </span>
                </div>

                <div class="p-8 text-center">
                    <div class="w-64 h-64 mx-auto rounded-2xl bg-blue-50 border border-blue-100 flex items-center justify-center">
                        <div class="w-44 h-44 bg-white rounded-xl shadow-sm border border-slate-200 p-4 grid grid-cols-5 gap-1">
                            @for ($i = 1; $i <= 25; $i++)
                                <div class="rounded-sm {{ in_array($i, [1,2,3,6,8,11,13,15,18,20,21,22,24,25]) ? 'bg-slate-900' : 'bg-slate-100' }}">
                        </div>
                        @endfor
                    </div>
                </div>

                <h3 class="text-xl font-bold text-slate-900 mt-6">
                    Pindai untuk Bayar
                </h3>

                <p class="text-sm text-slate-500 mt-2 max-w-xl mx-auto">
                    Gunakan aplikasi mobile banking, e-wallet, atau aplikasi pembayaran yang mendukung QRIS.
                    Pembayaran akan diproses melalui Midtrans Sandbox. Silakan klik tombol Bayar Sekarang dengan Midtrans setelah token pembayaran dibuat.
                </p>

                <div class="mt-8 text-left max-w-xl mx-auto bg-slate-50 border border-slate-200 rounded-2xl p-5">
                    <h4 class="font-bold text-slate-900 mb-3">
                        Cara Pembayaran:
                    </h4>

                    <ol class="space-y-2 text-sm text-slate-600 list-decimal list-inside">
                        <li>Klik tombol <b>Buat Pembayaran Midtrans</b>.</li>
                        <li>Setelah token dibuat, klik tombol <b>Bayar Sekarang dengan Midtrans</b>.</li>
                        <li>Pilih metode pembayaran Sandbox, misalnya QRIS, GoPay, atau Virtual Account.</li>
                        <li>Selesaikan pembayaran sesuai instruksi Midtrans.</li>
                        <li>Jika pembayaran berhasil, dana akan ditahan sementara oleh sistem escrow.</li>
                    </ol>
                </div>
            </div>
        </div>

        {{-- STATUS TRANSAKSI --}}
        @php
        $currentStep = 2;

        if (in_array($pesanan->status_pesanan, ['dibayar', 'diproses', 'menunggu_approve', 'revisi'])) {
        $currentStep = 3;
        }

        if ($pesanan->status_pesanan === 'selesai') {
        $currentStep = 4;
        }
        @endphp
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-8">
            <h2 class="text-center text-2xl font-bold text-slate-900 mb-12">
                Status Transaksi
            </h2>

            <div class="relative px-8">

                {{-- Garis abu-abu --}}
                <div class="absolute left-16 right-16 top-7 h-1 bg-slate-200 rounded-full"></div>

                {{-- Garis biru aktif --}}
                <div class="absolute left-16 top-7 h-1 bg-blue-600 rounded-full
            @if ($currentStep === 1) w-0
            @elseif ($currentStep === 2) w-[33%]
            @elseif ($currentStep === 3) w-[66%]
            @else right-16
            @endif">
                </div>

                <div class="relative grid grid-cols-4 gap-4">

                    {{-- STEP 1 --}}
                    <div class="text-center">
                        <div class="mx-auto flex items-center justify-center rounded-full bg-blue-600 text-white font-bold shadow ring-8 ring-blue-100"
                            style="width:56px; height:56px;">
                            ✓
                        </div>

                        <p class="text-sm md:text-base font-bold text-blue-700 mt-5">
                            Pesanan Dibuat
                        </p>

                        <p class="text-xs text-slate-500 mt-1">
                            {{ $pesanan->created_at->format('d M Y, H:i') }}
                        </p>
                    </div>

                    {{-- STEP 2 --}}
                    <div class="text-center">
                        <div class="mx-auto flex items-center justify-center rounded-full font-bold shadow ring-8
                    {{ $currentStep >= 2 ? 'bg-blue-600 text-white ring-blue-100' : 'bg-slate-100 text-slate-400 ring-slate-100' }}"
                            style="width:56px; height:56px;">
                            💳
                        </div>

                        <p class="text-sm md:text-base font-bold {{ $currentStep >= 2 ? 'text-blue-700' : 'text-slate-400' }} mt-5">
                            Menunggu Pembayaran
                        </p>

                        <p class="text-xs text-slate-500 mt-1">
                            Menunggu Konfirmasi QRIS
                        </p>
                    </div>

                    {{-- STEP 3 --}}
                    <div class="text-center">
                        <div class="mx-auto flex items-center justify-center rounded-full font-bold shadow ring-8
                    {{ $currentStep >= 3 ? 'bg-blue-600 text-white ring-blue-100' : 'bg-slate-100 text-slate-400 ring-slate-100' }}"
                            style="width:56px; height:56px;">
                            ⚙
                        </div>

                        <p class="text-sm md:text-base font-bold {{ $currentStep >= 3 ? 'text-blue-700' : 'text-slate-400' }} mt-5">
                            Verifikasi
                        </p>

                        <p class="text-xs text-slate-400 mt-1">
                            Otomatis oleh sistem
                        </p>
                    </div>

                    {{-- STEP 4 --}}
                    <div class="text-center">
                        <div class="mx-auto flex items-center justify-center rounded-full font-bold shadow ring-8
                    {{ $currentStep >= 4 ? 'bg-blue-600 text-white ring-blue-100' : 'bg-slate-100 text-slate-400 ring-slate-100' }}"
                            style="width:56px; height:56px;">
                            ✓
                        </div>

                        <p class="text-sm md:text-base font-bold {{ $currentStep >= 4 ? 'text-blue-700' : 'text-slate-400' }} mt-5">
                            Selesai
                        </p>

                        <p class="text-xs text-slate-400 mt-1">
                            Proyek dimulai
                        </p>
                    </div>

                </div>
            </div>
        </div>

</section>
@endsection

@push('scripts')
@if ($pesanan->status_pesanan === 'menunggu_pembayaran' && optional($pesanan->pembayaran)->snap_token)
@php
$midtransIsProduction = filter_var(config('services.midtrans.is_production'), FILTER_VALIDATE_BOOLEAN);
@endphp

<script src="{{ $midtransIsProduction ? 'https://app.midtrans.com/snap/snap.js' : 'https://app.sandbox.midtrans.com/snap/snap.js' }}"
    data-client-key="{{ config('services.midtrans.client_key') }}"></script>
<script>
    const payButton = document.getElementById('pay-button');

    if (payButton) {
        payButton.addEventListener('click', function() {
            window.snap.pay('{{ $pesanan->pembayaran->snap_token }}', {
                onSuccess: function(result) {
                    fetch('{{ url(' / customer / order / ' . $pesanan->id . ' / payment / finish ') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify(result)
                        })
                        .then(response => response.json())
                        .then(data => {
                            window.location.href = '{{ url(' / customer / order / ' . $pesanan->id . ' / payment ') }}';
                        });
                },
                onPending: function(result) {
                    alert('Pembayaran masih pending. Silakan selesaikan pembayaran.');
                    window.location.reload();
                },
                onError: function(result) {
                    alert('Pembayaran gagal. Silakan coba lagi.');
                },
                onClose: function() {
                    alert('Popup pembayaran ditutup sebelum selesai.');
                }
            });
        });
    }
</script>
@endif

<script>
    const countdown = document.getElementById('payment-countdown');

    if (countdown) {
        let totalSeconds = 24 * 60 * 60 - 6;

        setInterval(() => {
            totalSeconds--;

            const hours = String(Math.floor(totalSeconds / 3600)).padStart(2, '0');
            const minutes = String(Math.floor((totalSeconds % 3600) / 60)).padStart(2, '0');
            const seconds = String(totalSeconds % 60).padStart(2, '0');

            countdown.textContent = `${hours}:${minutes}:${seconds}`;
        }, 1000);
    }
</script>
@endpush