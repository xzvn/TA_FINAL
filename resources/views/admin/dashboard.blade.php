@extends('layouts.admin')

@section('title', 'Dashboard Admin - JasaKampus')

@section('content')

@php
$aktivitasTerbaru = $aktivitasTerbaru ?? collect();
$adaLogLainnya = $adaLogLainnya ?? false;
$nextLogLimit = $nextLogLimit ?? 15;
$tahunDashboard = $tahunDashboard ?? now()->year;
$trenPendapatanChart = $trenPendapatanChart ?? collect();
$pertumbuhanPenggunaChart = $pertumbuhanPenggunaChart ?? collect();

$tanggalMulaiValue = $tanggalMulaiInput ?? request('tanggal_mulai');
$tanggalSelesaiValue = $tanggalSelesaiInput ?? request('tanggal_selesai');

$formatAngka = fn ($value) => number_format($value ?? 0, 0, ',', '.');
@endphp

@if (session('success'))
<div class="mb-6 px-5 py-4 rounded-2xl bg-green-50 text-green-700 border border-green-200 font-semibold">
    {{ session('success') }}
</div>
@endif

<div class="space-y-6">

    {{-- HEADER --}}
    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6 md:p-8">
        <div class="flex flex-col xl:flex-row xl:items-center xl:justify-between gap-6">
            <div>
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-blue-50 text-blue-700 text-xs font-bold mb-4">
                    <span class="w-2 h-2 rounded-full bg-blue-600"></span>
                    Admin Control Center
                </div>

                <h1 class="text-3xl md:text-4xl font-extrabold text-slate-900 leading-tight">
                    Ringkasan Dashboard
                </h1>

                <p class="text-sm md:text-base text-slate-500 mt-3 leading-relaxed max-w-3xl">
                    Selamat datang kembali, {{ auth()->user()->nama }}. Pantau pengguna, jasa, transaksi,
                    verifikasi freelancer, dispute, dan laporan aktivitas platform JasaKampus.
                </p>
            </div>

            <div class="flex flex-col sm:flex-row gap-3">
                <a href="{{ route('admin.reports.download', [
                    'tanggal_mulai' => $tanggalMulaiValue,
                    'tanggal_selesai' => $tanggalSelesaiValue,
                ]) }}"
                    class="inline-flex items-center justify-center gap-2 px-5 py-3 rounded-xl bg-white border border-slate-200 text-slate-800 text-sm font-bold hover:bg-slate-50 transition">
                    <svg xmlns="http://www.w3.org/2000/svg"
                        class="w-5 h-5"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                        stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M12 3v12m0 0l-4-4m4 4l4-4M4 19h16" />
                    </svg>
                    Unduh Laporan
                </a>

                <button type="button"
                    id="openRequestModal"
                    class="inline-flex items-center justify-center gap-2 px-5 py-3 rounded-xl bg-blue-600 text-white text-sm font-bold hover:bg-blue-700 transition shadow-sm">
                    <svg xmlns="http://www.w3.org/2000/svg"
                        class="w-5 h-5"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                        stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M12 6v12m6-6H6" />
                    </svg>
                    Kirim Pengumuman
                </button>
            </div>
        </div>
    </div>

    {{-- FILTER PERIODE --}}
    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6">
        <div class="flex flex-col xl:flex-row xl:items-end xl:justify-between gap-6">
            <div>
                <h2 class="text-xl font-extrabold text-slate-900">
                    Filter Periode Laporan
                </h2>
                <p class="text-sm text-slate-500 mt-1">
                    Data pesanan, transaksi, pendapatan, dan escrow ditampilkan berdasarkan rentang tanggal.
                </p>
            </div>

            <form method="GET" action="{{ route('dashboard') }}"
                class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-3 w-full xl:w-auto">
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-2">
                        Tanggal Mulai
                    </label>
                    <input type="date"
                        name="tanggal_mulai"
                        value="{{ $tanggalMulaiValue }}"
                        class="w-full rounded-xl border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-2">
                        Tanggal Selesai
                    </label>
                    <input type="date"
                        name="tanggal_selesai"
                        value="{{ $tanggalSelesaiValue }}"
                        class="w-full rounded-xl border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                </div>

                <button type="submit"
                    class="self-end inline-flex items-center justify-center px-5 py-3 rounded-xl bg-blue-600 text-white text-sm font-bold hover:bg-blue-700 transition">
                    Terapkan
                </button>

                <a href="{{ route('dashboard') }}"
                    class="self-end inline-flex items-center justify-center px-5 py-3 rounded-xl border border-slate-300 text-slate-700 text-sm font-bold hover:bg-slate-50 transition">
                    Reset
                </a>
            </form>
        </div>

        <div class="mt-5 flex flex-col sm:flex-row sm:items-center gap-3 px-4 py-3 rounded-2xl bg-blue-50 border border-blue-100 text-blue-700 text-sm font-semibold">
            <div class="inline-flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg"
                    class="w-5 h-5"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                    stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M8 7V3m8 4V3M4 11h16M5 5h14a1 1 0 011 1v14a1 1 0 01-1 1H5a1 1 0 01-1-1V6a1 1 0 011-1z" />
                </svg>
                <span>Periode aktif:</span>
            </div>

            <span>
                {{ isset($tanggalMulai) ? $tanggalMulai->format('d M Y') : '-' }}
                sampai
                {{ isset($tanggalSelesai) ? $tanggalSelesai->format('d M Y') : '-' }}
            </span>
        </div>
    </div>

    {{-- STAT CARDS --}}
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-5">

        {{-- Total Pengguna --}}
        <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6 hover:shadow-md transition">
            <div class="flex items-start justify-between gap-4">
                <div class="w-12 h-12 rounded-2xl bg-blue-100 text-blue-600 flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg"
                        class="w-6 h-6"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                        stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M17 20a4 4 0 00-8 0M12 12a4 4 0 100-8 4 4 0 000 8zm7 8a4 4 0 00-3-3.87M20 8a3 3 0 11-6 0M4 20a4 4 0 013-3.87M4 8a3 3 0 106 0" />
                    </svg>
                </div>

                <span class="px-3 py-1 rounded-full bg-blue-50 text-blue-700 text-xs font-bold">
                    Database
                </span>
            </div>

            <p class="text-xs text-slate-400 mt-5 uppercase font-bold tracking-wide">
                Total Pengguna
            </p>
            <h3 class="text-3xl font-extrabold text-slate-900 mt-1">
                {{ $formatAngka($totalPengguna ?? 0) }}
            </h3>
        </div>

        {{-- Freelancer Aktif --}}
        <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6 hover:shadow-md transition">
            <div class="flex items-start justify-between gap-4">
                <div class="w-12 h-12 rounded-2xl bg-purple-100 text-purple-600 flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg"
                        class="w-6 h-6"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                        stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M9 12l2 2 4-4M12 3l7 4v5c0 5-3.5 8-7 9-3.5-1-7-4-7-9V7l7-4z" />
                    </svg>
                </div>

                <span class="px-3 py-1 rounded-full bg-purple-50 text-purple-700 text-xs font-bold">
                    Verified
                </span>
            </div>

            <p class="text-xs text-slate-400 mt-5 uppercase font-bold tracking-wide">
                Freelancer Aktif
            </p>
            <h3 class="text-3xl font-extrabold text-slate-900 mt-1">
                {{ $formatAngka($freelancerAktif ?? 0) }}
            </h3>
        </div>

        {{-- Layanan Terdaftar --}}
        <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6 hover:shadow-md transition">
            <div class="flex items-start justify-between gap-4">
                <div class="w-12 h-12 rounded-2xl bg-orange-100 text-orange-600 flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg"
                        class="w-6 h-6"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                        stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M4 7h16M4 12h16M4 17h10" />
                    </svg>
                </div>

                <span class="px-3 py-1 rounded-full bg-orange-50 text-orange-700 text-xs font-bold">
                    Jasa
                </span>
            </div>

            <p class="text-xs text-slate-400 mt-5 uppercase font-bold tracking-wide">
                Layanan Terdaftar
            </p>
            <h3 class="text-3xl font-extrabold text-slate-900 mt-1">
                {{ $formatAngka($layananTerdaftar ?? 0) }}
            </h3>
        </div>

        {{-- Proyek Berjalan --}}
        <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6 hover:shadow-md transition">
            <div class="flex items-start justify-between gap-4">
                <div class="w-12 h-12 rounded-2xl bg-green-100 text-green-600 flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg"
                        class="w-6 h-6"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                        stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M3 7a2 2 0 012-2h5l2 2h7a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V7z" />
                    </svg>
                </div>

                <span class="px-3 py-1 rounded-full bg-green-50 text-green-700 text-xs font-bold">
                    Aktif
                </span>
            </div>

            <p class="text-xs text-slate-400 mt-5 uppercase font-bold tracking-wide">
                Proyek Berjalan
            </p>
            <h3 class="text-3xl font-extrabold text-slate-900 mt-1">
                {{ $formatAngka($proyekBerjalan ?? 0) }}
            </h3>
        </div>

        {{-- Total Transaksi --}}
        <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6 hover:shadow-md transition">
            <div class="flex items-start justify-between gap-4">
                <div class="w-12 h-12 rounded-2xl bg-slate-100 text-slate-600 flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg"
                        class="w-6 h-6"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                        stroke-width="1.8">
                        <rect x="3" y="6" width="18" height="12" rx="2" ry="2"></rect>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18" />
                    </svg>
                </div>

                <span class="px-3 py-1 rounded-full bg-slate-100 text-slate-600 text-xs font-bold">
                    Total
                </span>
            </div>

            <p class="text-xs text-slate-400 mt-5 uppercase font-bold tracking-wide">
                Transaksi
            </p>
            <h3 class="text-3xl font-extrabold text-slate-900 mt-1">
                {{ $formatAngka($totalTransaksi ?? 0) }}
            </h3>
        </div>

        {{-- Total Pendapatan --}}
        <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6 hover:shadow-md transition">
            <div class="flex items-start justify-between gap-4">
                <div class="w-12 h-12 rounded-2xl bg-emerald-100 text-emerald-600 flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg"
                        class="w-6 h-6"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                        stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M4 7h16v10H4V7zm3 3h.01M17 14h.01M12 12a2 2 0 100-4 2 2 0 000 4z" />
                    </svg>
                </div>

                <span class="px-3 py-1 rounded-full bg-emerald-50 text-emerald-700 text-xs font-bold">
                    Revenue
                </span>
            </div>

            <p class="text-xs text-slate-400 mt-5 uppercase font-bold tracking-wide">
                Total Pendapatan
            </p>
            <h3 class="text-2xl font-extrabold text-slate-900 mt-1">
                Rp {{ $formatAngka($totalPendapatan ?? 0) }}
            </h3>
        </div>

        {{-- Verifikasi Pending --}}
        <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6 hover:shadow-md transition">
            <div class="flex items-start justify-between gap-4">
                <div class="w-12 h-12 rounded-2xl bg-yellow-100 text-yellow-600 flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg"
                        class="w-6 h-6"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                        stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M12 8v4l3 3M12 3a9 9 0 100 18 9 9 0 000-18z" />
                    </svg>
                </div>

                <span class="px-3 py-1 rounded-full bg-yellow-50 text-yellow-700 text-xs font-bold">
                    Pending
                </span>
            </div>

            <p class="text-xs text-slate-400 mt-5 uppercase font-bold tracking-wide">
                Verifikasi Pending
            </p>
            <h3 class="text-3xl font-extrabold text-slate-900 mt-1">
                {{ $formatAngka($verifikasiPending ?? 0) }}
            </h3>
        </div>

        {{-- Dispute Aktif --}}
        <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6 hover:shadow-md transition">
            <div class="flex items-start justify-between gap-4">
                <div class="w-12 h-12 rounded-2xl bg-red-100 text-red-600 flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg"
                        class="w-6 h-6"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                        stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M12 9v4m0 4h.01M10.29 3.86l-7 12.14A2 2 0 005 19h14a2 2 0 001.71-3l-7-12.14a2 2 0 00-3.42 0z" />
                    </svg>
                </div>

                <span class="px-3 py-1 rounded-full bg-red-50 text-red-700 text-xs font-bold">
                    Aktif
                </span>
            </div>

            <p class="text-xs text-slate-400 mt-5 uppercase font-bold tracking-wide">
                Dispute Aktif
            </p>
            <h3 class="text-3xl font-extrabold text-slate-900 mt-1">
                {{ $formatAngka($disputeAktif ?? 0) }}
            </h3>
        </div>
    </div>

    {{-- CHARTS --}}
    <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">

        {{-- TREN PENDAPATAN --}}
        <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6">
            <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4 mb-8">
                <div>
                    <h2 class="text-xl font-extrabold text-slate-900">
                        Tren Pendapatan Bulanan
                    </h2>
                    <p class="text-sm text-slate-500 mt-1">
                        Pendapatan dari escrow yang sudah dicairkan.
                    </p>
                </div>

                <span class="inline-flex items-center px-4 py-2 rounded-full bg-slate-100 text-slate-600 text-sm font-bold">
                    Tahun {{ $tahunDashboard }}
                </span>
            </div>

            @if ($trenPendapatanChart->count() > 0)
            <div class="h-72 flex items-end gap-3 sm:gap-4">
                @foreach ($trenPendapatanChart as $item)
                @php
                $total = $item['total'] ?? 0;
                $persen = $item['persen'] ?? 0;
                $height = max($persen, $total > 0 ? 8 : 2);
                @endphp

                <div class="flex-1 flex flex-col items-center justify-end h-full">
                    <div class="w-full flex items-end justify-center h-52">
                        <div class="w-9 sm:w-12 rounded-t-2xl bg-blue-300 hover:bg-blue-600 transition"
                            style="height: {{ $height }}%;"
                            title="Rp {{ number_format($total, 0, ',', '.') }}">
                        </div>
                    </div>

                    <p class="mt-3 text-xs sm:text-sm font-semibold text-slate-500">
                        {{ $item['bulan'] ?? '-' }}
                    </p>

                    <p class="text-[11px] text-slate-400 text-center">
                        Rp {{ number_format($total, 0, ',', '.') }}
                    </p>
                </div>
                @endforeach
            </div>
            @else
            <div class="h-72 flex items-center justify-center rounded-2xl border border-dashed border-slate-300 text-slate-500">
                Belum ada data pendapatan untuk ditampilkan.
            </div>
            @endif
        </div>

        {{-- PERTUMBUHAN PENGGUNA --}}
        <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6">
            <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4 mb-8">
                <div>
                    <h2 class="text-xl font-extrabold text-slate-900">
                        Pertumbuhan Pengguna
                    </h2>
                    <p class="text-sm text-slate-500 mt-1">
                        Jumlah customer dan freelancer baru berdasarkan bulan.
                    </p>
                </div>

                <div class="flex items-center gap-4 text-sm">
                    <div class="flex items-center gap-2">
                        <span class="w-3 h-3 rounded-full bg-blue-500"></span>
                        <span class="text-slate-600 font-semibold">Customer</span>
                    </div>

                    <div class="flex items-center gap-2">
                        <span class="w-3 h-3 rounded-full bg-purple-500"></span>
                        <span class="text-slate-600 font-semibold">Freelancer</span>
                    </div>
                </div>
            </div>

            @if ($pertumbuhanPenggunaChart->count() > 0)
            <div class="h-72 flex items-end gap-3 sm:gap-4">
                @foreach ($pertumbuhanPenggunaChart as $item)
                @php
                $customer = $item['customer'] ?? 0;
                $freelancer = $item['freelancer'] ?? 0;
                $customerPersen = $item['customer_persen'] ?? 0;
                $freelancerPersen = $item['freelancer_persen'] ?? 0;
                $customerHeight = max($customerPersen, $customer > 0 ? 8 : 2);
                $freelancerHeight = max($freelancerPersen, $freelancer > 0 ? 8 : 2);
                @endphp

                <div class="flex-1 flex flex-col items-center justify-end h-full">
                    <div class="w-full h-52 flex items-end justify-center gap-1.5">
                        <div class="w-4 sm:w-5 rounded-t-xl bg-blue-400 hover:bg-blue-600 transition"
                            style="height: {{ $customerHeight }}%;"
                            title="Customer: {{ $customer }}">
                        </div>

                        <div class="w-4 sm:w-5 rounded-t-xl bg-purple-400 hover:bg-purple-600 transition"
                            style="height: {{ $freelancerHeight }}%;"
                            title="Freelancer: {{ $freelancer }}">
                        </div>
                    </div>

                    <p class="mt-3 text-xs sm:text-sm font-semibold text-slate-500">
                        {{ $item['bulan'] ?? '-' }}
                    </p>
                </div>
                @endforeach
            </div>
            @else
            <div class="h-72 flex items-center justify-center rounded-2xl border border-dashed border-slate-300 text-slate-500">
                Belum ada data pertumbuhan pengguna untuk ditampilkan.
            </div>
            @endif
        </div>
    </div>

    {{-- LOG AKTIVITAS --}}
    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-6 py-5 flex flex-col md:flex-row md:items-center md:justify-between gap-4 border-b border-slate-200">
            <div>
                <h2 class="text-xl font-extrabold text-slate-900">
                    Log Aktivitas Terbaru
                </h2>
                <p class="text-sm text-slate-500 mt-1">
                    Data aktivitas terbaru dari seluruh interaksi platform.
                </p>
            </div>

            @if ($adaLogLainnya)
            <a href="{{ route('dashboard', ['log_limit' => $nextLogLimit]) }}"
                class="inline-flex items-center justify-center px-4 py-2 rounded-xl bg-slate-100 text-slate-700 text-sm font-bold hover:bg-slate-200 transition">
                Tampilkan Lagi
            </a>
            @endif
        </div>

        <div class="divide-y divide-slate-100">
            @forelse ($aktivitasTerbaru as $log)
            @php
            $warna = $log['warna'] ?? 'blue';

            $dotClass = match ($warna) {
            'green' => 'bg-green-500',
            'yellow' => 'bg-yellow-500',
            'red' => 'bg-red-500',
            default => 'bg-blue-500',
            };

            $badgeClass = match ($warna) {
            'green' => 'bg-green-100 text-green-700',
            'yellow' => 'bg-yellow-100 text-yellow-700',
            'red' => 'bg-red-100 text-red-700',
            default => 'bg-blue-100 text-blue-700',
            };
            @endphp

            <div class="p-5 md:p-6 hover:bg-slate-50 transition">
                <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                    <div class="flex items-start gap-4">
                        <div class="mt-1 w-10 h-10 rounded-2xl bg-slate-100 flex items-center justify-center shrink-0">
                            <span class="w-3 h-3 rounded-full {{ $dotClass }}"></span>
                        </div>

                        <div>
                            <h3 class="font-extrabold text-slate-900">
                                {{ $log['event'] ?? 'Aktivitas Sistem' }}
                            </h3>

                            <p class="text-sm text-slate-500 mt-1">
                                {{ $log['aktor'] ?? 'Sistem' }}
                            </p>

                            <div class="flex flex-wrap items-center gap-3 mt-3">
                                <span class="px-3 py-1 rounded-full text-xs font-bold {{ $badgeClass }}">
                                    {{ $log['status'] ?? 'Info' }}
                                </span>

                                <span class="text-xs text-slate-400">
                                    {{ isset($log['waktu']) ? $log['waktu']->diffForHumans() : '-' }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="lg:text-right">
                        @if (!empty($log['url']))
                        <a href="{{ $log['url'] }}"
                            class="inline-flex items-center justify-center px-4 py-2 rounded-xl bg-blue-50 text-blue-700 text-sm font-bold hover:bg-blue-100 transition">
                            Detail
                        </a>
                        @else
                        <span class="inline-flex items-center justify-center px-4 py-2 rounded-xl bg-slate-100 text-slate-400 text-sm font-bold">
                            Tidak ada aksi
                        </span>
                        @endif
                    </div>
                </div>
            </div>
            @empty
            <div class="p-10 text-center">
                <div class="mx-auto w-16 h-16 rounded-3xl bg-slate-100 flex items-center justify-center text-slate-400">
                    <svg xmlns="http://www.w3.org/2000/svg"
                        class="w-8 h-8"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                        stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M9 12h6m-6 4h6M7 4h10a2 2 0 012 2v14l-4-2-3 2-3-2-4 2V6a2 2 0 012-2z" />
                    </svg>
                </div>

                <h3 class="text-lg font-extrabold text-slate-900 mt-4">
                    Belum ada aktivitas terbaru
                </h3>

                <p class="text-sm text-slate-500 mt-1">
                    Aktivitas platform akan tampil di sini setelah ada interaksi pengguna.
                </p>
            </div>
            @endforelse
        </div>

        <div class="px-6 py-5 border-t border-slate-100 text-center">
            @if ($adaLogLainnya)
            <a href="{{ route('dashboard', ['log_limit' => $nextLogLimit]) }}"
                class="text-blue-600 font-bold hover:underline">
                Tampilkan 10 Log Lainnya
            </a>
            @else
            <span class="text-sm text-slate-400">
                Semua log sudah ditampilkan
            </span>
            @endif
        </div>
    </div>
</div>

{{-- MODAL REQUEST --}}
<div id="requestModal"
    class="hidden fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-sm items-center justify-center px-4">
    <div class="bg-white w-full max-w-lg rounded-3xl shadow-2xl border border-slate-200 overflow-hidden">
        <div class="p-6 border-b border-slate-200 flex items-start justify-between gap-4">
            <div>
                <h3 class="text-xl font-extrabold text-slate-900">
                    Kirim Pengumuman
                </h3>
                <p class="text-sm text-slate-500 mt-1">
                    Kirim informasi penting ke customer, freelancer, atau semua pengguna.
                </p>
            </div>

            <button type="button"
                id="closeRequestModal"
                class="inline-flex items-center justify-center w-10 h-10 rounded-xl bg-slate-100 text-slate-500 hover:bg-slate-200 hover:text-slate-800 transition">
                <svg xmlns="http://www.w3.org/2000/svg"
                    class="w-5 h-5"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                    stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <form method="POST" action="{{ route('admin.request.store') }}" class="p-6 space-y-5">
            @csrf

            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">
                    Target User
                </label>

                <select name="target"
                    class="w-full rounded-xl border-slate-300 focus:border-blue-500 focus:ring-blue-500">
                    <option value="all">Semua User</option>
                    <option value="customer">Customer</option>
                    <option value="freelancer">Freelancer</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">
                    Judul
                </label>

                <input type="text"
                    name="judul"
                    required
                    class="w-full rounded-xl border-slate-300 focus:border-blue-500 focus:ring-blue-500"
                    placeholder="Contoh: Informasi Maintenance Sistem">
            </div>

            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">
                    Pesan
                </label>

                <textarea name="pesan"
                    rows="4"
                    required
                    class="w-full rounded-xl border-slate-300 focus:border-blue-500 focus:ring-blue-500"
                    placeholder="Tulis pesan pengumuman..."></textarea>
            </div>

            <label class="flex items-center gap-3 rounded-2xl bg-slate-50 border border-slate-200 p-4">
                <input type="checkbox"
                    name="kirim_email"
                    value="1"
                    class="rounded border-slate-300 text-blue-600 focus:ring-blue-500">

                <span class="text-sm text-slate-600">
                    Kirim juga ke email user
                </span>
            </label>

            <div class="flex justify-end gap-3 pt-4">
                <button type="button"
                    id="cancelRequestModal"
                    class="px-5 py-3 rounded-xl bg-slate-100 text-slate-700 font-bold hover:bg-slate-200 transition">
                    Batal
                </button>

                <button type="submit"
                    class="px-5 py-3 rounded-xl bg-blue-600 text-white font-bold hover:bg-blue-700 transition">
                    Kirim Pengumuman
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const modal = document.getElementById('requestModal');
        const openBtn = document.getElementById('openRequestModal');
        const closeBtn = document.getElementById('closeRequestModal');
        const cancelBtn = document.getElementById('cancelRequestModal');

        if (!modal || !openBtn) return;

        function openModal() {
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            document.documentElement.classList.add('overflow-hidden');
            document.body.classList.add('overflow-hidden');
        }

        function closeModal() {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            document.documentElement.classList.remove('overflow-hidden');
            document.body.classList.remove('overflow-hidden');
        }

        openBtn.addEventListener('click', openModal);

        if (closeBtn) {
            closeBtn.addEventListener('click', closeModal);
        }

        if (cancelBtn) {
            cancelBtn.addEventListener('click', closeModal);
        }

        modal.addEventListener('click', function(event) {
            if (event.target === modal) {
                closeModal();
            }
        });

        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                closeModal();
            }
        });
    });
</script>
@endsection