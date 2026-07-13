@extends('layouts.freelancer')

@section('title', 'Pesanan Masuk - JasaKampus')
@section('page-title', 'Pesanan Masuk')

@section('content')
<div class="mb-8">
    <h1 class="text-3xl font-bold text-slate-900">
        Pesanan Masuk
    </h1>
    <p class="text-sm text-slate-500 mt-1">
        Daftar order customer yang sudah melakukan pembayaran.
    </p>
</div>

<div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5 mb-6">
    <div class="flex flex-col lg:flex-row lg:items-end lg:justify-between gap-5">
        <div>
            <h3 class="text-lg font-bold text-slate-900">
                Filter Proyek
            </h3>
            <p class="text-sm text-slate-500 mt-1">
                Filter proyek berdasarkan status pesanan dan tanggal masuk pesanan.
            </p>
        </div>

        <form method="GET" action="{{ route('freelancer.pesanan.index') }}"
            class="grid grid-cols-1 md:grid-cols-4 gap-3 w-full lg:w-auto">

            <select name="status_pesanan"
                class="rounded-xl border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                <option value="">Semua Status</option>

                <option value="dibayar" {{ request('status_pesanan') === 'dibayar' ? 'selected' : '' }}>
                    Dibayar
                </option>

                <option value="diproses" {{ request('status_pesanan') === 'diproses' ? 'selected' : '' }}>
                    Diproses
                </option>

                <option value="menunggu_approve" {{ request('status_pesanan') === 'menunggu_approve' ? 'selected' : '' }}>
                    Menunggu Approve
                </option>

                <option value="revisi" {{ request('status_pesanan') === 'revisi' ? 'selected' : '' }}>
                    Revisi
                </option>

                <option value="selesai" {{ request('status_pesanan') === 'selesai' ? 'selected' : '' }}>
                    Selesai
                </option>

                <option value="dispute" {{ request('status_pesanan') === 'dispute' ? 'selected' : '' }}>
                    Dispute
                </option>

                <option value="dibatalkan" {{ request('status_pesanan') === 'dibatalkan' ? 'selected' : '' }}>
                    Dibatalkan
                </option>
            </select>

            <input type="date"
                name="tanggal_mulai"
                value="{{ request('tanggal_mulai') }}"
                class="rounded-xl border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500">

            <input type="date"
                name="tanggal_selesai"
                value="{{ request('tanggal_selesai') }}"
                class="rounded-xl border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500">

            <div class="flex gap-2">
                <button type="submit"
                    class="px-4 py-2 bg-blue-600 text-white rounded-xl text-sm font-bold hover:bg-blue-700">
                    Filter
                </button>

                <a href="{{ route('freelancer.pesanan.index') }}"
                    class="px-4 py-2 bg-slate-100 text-slate-700 rounded-xl text-sm font-bold hover:bg-slate-200">
                    Reset
                </a>
            </div>
        </form>
    </div>
</div>

<div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
    <div class="px-6 py-5 border-b border-slate-200">
        <h3 class="font-semibold text-slate-800">
            Daftar Pesanan
        </h3>
        <p class="text-sm text-slate-500">
            Total proyek ditemukan sesuai filter: {{ $pesanans->count() }}
        </p>
    </div>

    @if ($pesanans->count() > 0)
    <div class="divide-y divide-slate-100">
        @foreach ($pesanans as $pesanan)
        @php
        $progressTerakhir = $pesanan->progressPekerjaans->sortByDesc('created_at')->first();
        $progressPersen = $pesanan->progressPekerjaans->max('persentase_progress') ?? 0;
        @endphp

        <a href="{{ route('freelancer.progress.create', $pesanan->id) }}"
            class="block px-6 py-5 hover:bg-slate-50 transition">

            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-5">
                <div class="flex-1">
                    <div class="flex flex-wrap items-center gap-3">
                        <h4 class="font-bold text-slate-900">
                            Order #{{ $pesanan->id }}
                        </h4>

                        <span class="px-3 py-1 rounded-full text-xs font-bold
                                    @if ($pesanan->status_pesanan === 'dibayar') bg-blue-100 text-blue-700
                                    @elseif ($pesanan->status_pesanan === 'diproses') bg-yellow-100 text-yellow-700
                                    @elseif ($pesanan->status_pesanan === 'menunggu_approve') bg-cyan-100 text-cyan-700
                                    @elseif ($pesanan->status_pesanan === 'revisi') bg-purple-100 text-purple-700
                                    @elseif ($pesanan->status_pesanan === 'selesai') bg-green-100 text-green-700
                                    @elseif ($pesanan->status_pesanan === 'dispute') bg-red-100 text-red-700
                                    @elseif ($pesanan->status_pesanan === 'dibatalkan') bg-slate-200 text-slate-700
                                    @else bg-slate-100 text-slate-700
                                    @endif">
                            {{ str_replace('_', ' ', strtoupper($pesanan->status_pesanan)) }}
                        </span>
                    </div>

                    <p class="text-sm text-slate-500 mt-2">
                        Customer: {{ $pesanan->customer->nama ?? '-' }}
                    </p>

                    <p class="text-sm text-slate-500">
                        Jasa: {{ $pesanan->jasa->nama_jasa ?? '-' }}
                    </p>

                    <p class="text-sm text-slate-500">
                        Tanggal:
                        {{ $pesanan->tanggal_pesan ? \Carbon\Carbon::parse($pesanan->tanggal_pesan)->format('d M Y H:i') : $pesanan->created_at->format('d M Y H:i') }}
                    </p>

                    <p class="text-sm text-slate-600 mt-2 line-clamp-1">
                        {{ $pesanan->deskripsi_kebutuhan }}
                    </p>

                    @if ($progressTerakhir)
                    <p class="text-xs text-slate-500 mt-3">
                        Progress terakhir:
                        <span class="font-bold text-slate-800">
                            {{ $progressPersen }}%
                        </span>
                    </p>

                    <div class="w-full max-w-sm h-2 bg-slate-100 rounded-full mt-2">
                        <div class="h-2 bg-blue-600 rounded-full"
                            style="width: {{ $progressPersen }}%;">
                        </div>
                    </div>
                    @endif
                </div>

                <div class="lg:text-right shrink-0">
                    <p class="font-bold text-blue-600 text-lg">
                        Rp {{ number_format($pesanan->total_harga, 0, ',', '.') }}
                    </p>

                    <p class="text-xs text-slate-400 mt-1">
                        Klik untuk kelola progress
                    </p>
                </div>
            </div>
        </a>
        @endforeach
    </div>
    @else
    <div class="p-10 text-center">
        <div class="w-16 h-16 mx-auto rounded-full bg-blue-100 text-blue-600 flex items-center justify-center text-2xl mb-4">
            📦
        </div>

        <h3 class="text-lg font-bold text-slate-900">
            Belum ada pesanan sesuai filter
        </h3>

        <p class="text-sm text-slate-500 mt-2">
            Coba ubah status atau rentang tanggal filter.
        </p>
    </div>
    @endif
</div>
@endsection