@extends('layouts.admin')

@section('title', 'Detail Transaksi - Admin JasaKampus')

@section('content')
<div class="max-w-6xl mx-auto space-y-6">

    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <a href="{{ route('admin.transactions.index') }}"
                class="inline-flex items-center gap-2 text-sm font-bold text-blue-600 hover:underline mb-3">
                ← Kembali ke Pembayaran
            </a>

            <h1 class="text-3xl font-bold text-slate-900">
                Detail Transaksi #{{ $pesanan->id }}
            </h1>

            <p class="text-sm text-slate-500 mt-1">
                Informasi pesanan, pembayaran, customer, dan freelancer.
            </p>
        </div>

        <span class="inline-flex px-4 py-2 rounded-full text-xs font-bold bg-blue-100 text-blue-700">
            {{ strtoupper(str_replace('_', ' ', $pesanan->status_pesanan)) }}
        </span>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <div class="lg:col-span-2 space-y-6">

            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
                <h2 class="text-lg font-bold text-slate-900 mb-5">
                    Informasi Pesanan
                </h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5 text-sm">
                    <div>
                        <p class="text-slate-500">Nama Jasa</p>
                        <p class="font-bold text-slate-900 mt-1">
                            {{ $pesanan->jasa->nama_jasa ?? '-' }}
                        </p>
                    </div>

                    <div>
                        <p class="text-slate-500">Total Harga</p>
                        <p class="font-bold text-blue-600 text-lg mt-1">
                            Rp {{ number_format($pesanan->total_harga, 0, ',', '.') }}
                        </p>
                    </div>

                    <div>
                        <p class="text-slate-500">Tanggal Pesan</p>
                        <p class="font-semibold text-slate-900 mt-1">
                            {{ optional($pesanan->tanggal_pesan)->format('d M Y H:i') ?? '-' }}
                        </p>
                    </div>

                    <div>
                        <p class="text-slate-500">Deadline</p>
                        <p class="font-semibold text-slate-900 mt-1">
                            {{ optional($pesanan->deadline)->format('d M Y H:i') ?? '-' }}
                        </p>
                    </div>
                </div>

                <div class="mt-6">
                    <p class="text-slate-500 text-sm mb-2">Deskripsi Kebutuhan</p>
                    <div class="rounded-xl bg-slate-50 border border-slate-200 p-4 text-sm text-slate-700 leading-relaxed">
                        {{ $pesanan->deskripsi_kebutuhan ?? '-' }}
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
                <h2 class="text-lg font-bold text-slate-900 mb-5">
                    Informasi Pembayaran
                </h2>

                @if ($pesanan->pembayaran)
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5 text-sm">
                    <div>
                        <p class="text-slate-500">Status Transaksi</p>
                        <p class="font-bold text-slate-900 mt-1">
                            {{ strtoupper($pesanan->pembayaran->transaction_status ?? '-') }}
                        </p>
                    </div>

                    <div>
                        <p class="text-slate-500">Status Escrow</p>
                        <p class="font-bold text-slate-900 mt-1">
                            {{ strtoupper(str_replace('_', ' ', $pesanan->pembayaran->status_escrow ?? '-')) }}
                        </p>
                    </div>

                    <div>
                        <p class="text-slate-500">Gross Amount</p>
                        <p class="font-bold text-blue-600 mt-1">
                            Rp {{ number_format($pesanan->pembayaran->gross_amount ?? 0, 0, ',', '.') }}
                        </p>
                    </div>

                    <div>
                        <p class="text-slate-500">Order ID</p>
                        <p class="font-semibold text-slate-900 mt-1">
                            {{ $pesanan->pembayaran->order_id ?? '-' }}
                        </p>
                    </div>
                </div>
                @else
                <div class="rounded-xl bg-slate-50 border border-slate-200 p-5 text-sm text-slate-500">
                    Belum ada data pembayaran untuk pesanan ini.
                </div>
                @endif
            </div>
        </div>

        <div class="space-y-6">

            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
                <h2 class="text-lg font-bold text-slate-900 mb-5">
                    Customer
                </h2>

                <p class="font-bold text-slate-900">
                    {{ $pesanan->customer->nama ?? '-' }}
                </p>
                <p class="text-sm text-slate-500 mt-1">
                    {{ $pesanan->customer->email ?? '-' }}
                </p>
            </div>

            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
                <h2 class="text-lg font-bold text-slate-900 mb-5">
                    Freelancer
                </h2>

                <p class="font-bold text-slate-900">
                    {{ $pesanan->freelancer->nama ?? '-' }}
                </p>
                <p class="text-sm text-slate-500 mt-1">
                    {{ $pesanan->freelancer->email ?? '-' }}
                </p>
            </div>

            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
                <h2 class="text-lg font-bold text-slate-900 mb-5">
                    Ringkasan Aktivitas
                </h2>

                <div class="space-y-3 text-sm">
                    <div class="flex items-center justify-between">
                        <span class="text-slate-500">Progress</span>
                        <span class="font-bold text-slate-900">
                            {{ $pesanan->progressPekerjaans->count() }}
                        </span>
                    </div>

                    <div class="flex items-center justify-between">
                        <span class="text-slate-500">Revisi</span>
                        <span class="font-bold text-slate-900">
                            {{ $pesanan->revisis->count() }}
                        </span>
                    </div>

                    <div class="flex items-center justify-between">
                        <span class="text-slate-500">Review</span>
                        <span class="font-bold text-slate-900">
                            {{ $pesanan->review ? 'Ada' : 'Belum ada' }}
                        </span>
                    </div>

                    <div class="flex items-center justify-between">
                        <span class="text-slate-500">Dispute</span>
                        <span class="font-bold text-slate-900">
                            {{ $pesanan->dispute ? 'Ada' : 'Tidak ada' }}
                        </span>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection