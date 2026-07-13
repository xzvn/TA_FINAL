@extends('layouts.customer')

@section('title', 'Pesanan Saya - JasaKampus')

@section('content')
<section class="px-6 py-6">
    <div class="mb-6">
        <p class="text-xs text-slate-500">
            Customer / Pesanan Saya
        </p>

        <h1 class="text-2xl md:text-3xl font-bold text-slate-900 mt-2">
            Pesanan Saya
        </h1>

        <p class="text-sm text-slate-500 mt-1">
            Pantau semua pesanan, pembayaran, dan progress pekerjaan dari freelancer.
        </p>
    </div>

    {{-- FILTER PESANAN --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5 mb-6">
        <div class="flex flex-col lg:flex-row lg:items-end lg:justify-between gap-5">
            <div>
                <h3 class="text-lg font-bold text-slate-900">
                    Filter Pesanan
                </h3>
                <p class="text-sm text-slate-500 mt-1">
                    Filter pesanan berdasarkan status dan tanggal pemesanan.
                </p>
            </div>

            <form method="GET" action="{{ route('customer.order.index') }}"
                class="grid grid-cols-1 md:grid-cols-4 gap-3 w-full lg:w-auto">

                <select name="status_pesanan"
                    class="rounded-xl border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="">Semua Status</option>
                    <option value="menunggu_pembayaran" {{ request('status_pesanan') === 'menunggu_pembayaran' ? 'selected' : '' }}>
                        Menunggu Pembayaran
                    </option>
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
                    <option value="dibatalkan" {{ request('status_pesanan') === 'dibatalkan' ? 'selected' : '' }}>
                        Dibatalkan
                    </option>
                    <option value="dispute" {{ request('status_pesanan') === 'dispute' ? 'selected' : '' }}>
                        Dispute
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

                    <a href="{{ route('customer.order.index') }}"
                        class="px-4 py-2 bg-slate-100 text-slate-700 rounded-xl text-sm font-bold hover:bg-slate-200">
                        Reset
                    </a>
                </div>
            </form>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-6 py-5 border-b border-slate-200 flex items-center justify-between">
            <div>
                <h2 class="font-bold text-slate-900">
                    Daftar Pesanan
                </h2>
                <p class="text-sm text-slate-500 mt-1">
                    Total {{ $pesanans->count() }} pesanan sesuai filter.
                </p>
            </div>

            <a href="{{ route('dashboard') }}"
                class="px-4 py-2 bg-blue-600 text-white rounded-xl text-sm font-bold hover:bg-blue-700">
                Cari Jasa Lagi
            </a>
        </div>

        @if ($pesanans->count() > 0)
        <div class="divide-y divide-slate-100">
            @foreach ($pesanans as $pesanan)
            @php
            $progressTerakhir = $pesanan->progressPekerjaans->sortByDesc('created_at')->first();
            @endphp

            <a href="{{ route('customer.order.show', $pesanan->id) }}"
                class="block px-6 py-5 hover:bg-slate-50 transition">
                <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-5">
                    <div class="flex gap-4">
                        <div class="w-24 h-20 rounded-xl overflow-hidden bg-slate-100 shrink-0">
                            @if ($pesanan->jasa?->thumbnail)
                            <img src="{{ \App\Services\CloudinaryService::mediaUrl($pesanan->jasa->thumbnail) }}"
                                alt="{{ $pesanan->jasa->nama_jasa }}"
                                class="w-full h-full object-cover">
                            @else
                            <div class="w-full h-full bg-gradient-to-br from-blue-100 to-slate-200 flex items-center justify-center">
                                <span class="text-3xl">🖼️</span>
                            </div>
                            @endif
                        </div>

                        <div>
                            <div class="flex flex-wrap items-center gap-2">
                                <span class="px-3 py-1 bg-blue-100 text-blue-700 rounded-full text-xs font-bold">
                                    Order #{{ $pesanan->id }}
                                </span>

                                <span class="px-3 py-1 rounded-full text-xs font-bold
                                            @if ($pesanan->status_pesanan === 'menunggu_pembayaran') bg-yellow-100 text-yellow-700
                                            @elseif ($pesanan->status_pesanan === 'dibayar') bg-blue-100 text-blue-700
                                            @elseif ($pesanan->status_pesanan === 'diproses') bg-orange-100 text-orange-700
                                            @elseif ($pesanan->status_pesanan === 'menunggu_approve') bg-cyan-100 text-cyan-700
                                            @elseif ($pesanan->status_pesanan === 'selesai') bg-green-100 text-green-700
                                            @elseif ($pesanan->status_pesanan === 'revisi') bg-purple-100 text-purple-700
                                            @elseif ($pesanan->status_pesanan === 'dispute') bg-red-100 text-red-700
                                            @elseif ($pesanan->status_pesanan === 'dibatalkan') bg-slate-200 text-slate-700
                                            @else bg-slate-100 text-slate-700
                                            @endif">
                                    {{ str_replace('_', ' ', strtoupper($pesanan->status_pesanan)) }}
                                </span>
                            </div>

                            <h3 class="font-bold text-slate-900 mt-2">
                                {{ $pesanan->jasa->nama_jasa ?? '-' }}
                            </h3>

                            <p class="text-sm text-slate-500 mt-1">
                                Freelancer: {{ $pesanan->jasa->freelancer->nama ?? '-' }}
                            </p>

                            <p class="text-sm text-slate-500">
                                Tanggal:
                                {{ $pesanan->tanggal_pesan ? \Carbon\Carbon::parse($pesanan->tanggal_pesan)->format('d M Y H:i') : $pesanan->created_at->format('d M Y H:i') }}
                            </p>
                        </div>
                    </div>

                    <div class="lg:text-right">
                        <p class="text-xl font-bold text-blue-600">
                            Rp {{ number_format($pesanan->total_harga, 0, ',', '.') }}
                        </p>

                        @if ($progressTerakhir)
                        <p class="text-sm text-slate-500 mt-2">
                            Progress terakhir:
                            <span class="font-bold text-slate-800">
                                {{ $progressTerakhir->persentase_progress }}%
                            </span>
                        </p>

                        <div class="w-48 h-2 bg-slate-100 rounded-full mt-2 lg:ml-auto">
                            <div class="h-2 bg-blue-600 rounded-full"
                                style="width: {{ $progressTerakhir->persentase_progress }}%;">
                            </div>
                        </div>
                        @else
                        <p class="text-sm text-slate-500 mt-2">
                            Belum ada progress.
                        </p>
                        @endif
                    </div>
                </div>
            </a>
            @endforeach
        </div>
        @else
        <div class="p-12 text-center">
            <div class="w-16 h-16 mx-auto rounded-full bg-blue-100 text-blue-600 flex items-center justify-center text-2xl mb-4">
                📦
            </div>

            <h3 class="text-lg font-bold text-slate-900">
                Belum ada pesanan sesuai filter
            </h3>

            <p class="text-sm text-slate-500 mt-2">
                Coba ubah status atau rentang tanggal filter.
            </p>

            <a href="{{ route('dashboard') }}"
                class="inline-block mt-5 px-5 py-3 bg-blue-600 text-white rounded-xl font-bold hover:bg-blue-700">
                Cari Jasa
            </a>
        </div>
        @endif
    </div>
</section>
@endsection