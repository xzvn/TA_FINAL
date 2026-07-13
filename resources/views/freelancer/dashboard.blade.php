@extends('layouts.freelancer')

@section('title', 'Dashboard Freelancer - JasaKampus')
@section('page-title', 'Dashboard Freelancer')

@section('content')
@php
$user = auth()->user();
$verifikasi = $user->verifikasiFreelancer;
$status = $verifikasi?->status_verifikasi;

$totalJasa = $totalJasa ?? 0;
$jasaAktif = $jasaAktif ?? 0;
$jasaPending = $jasaPending ?? 0;
$pesananBerjalan = $pesananBerjalan ?? 0;
$pesananSelesai = $pesananSelesai ?? 0;
$saldoDitahan = $saldoDitahan ?? 0;
$totalPendapatan = $totalPendapatan ?? 0;
$ratingRataRata = $ratingRataRata ?? 0;
$totalReview = $totalReview ?? 0;
$pesananTerbaru = $pesananTerbaru ?? collect();
$jasaTerbaru = $jasaTerbaru ?? collect();
$totalPortofolio = $user->portofolios()->count();

$statusLabel = match ($status) {
'approved' => 'Terverifikasi',
'pending' => 'Menunggu Verifikasi',
'rejected' => 'Verifikasi Ditolak',
default => 'Belum Mengajukan',
};

$statusBadgeClass = match ($status) {
'approved' => 'bg-green-100 text-green-700 border-green-200',
'pending' => 'bg-yellow-100 text-yellow-700 border-yellow-200',
'rejected' => 'bg-red-100 text-red-700 border-red-200',
default => 'bg-slate-100 text-slate-700 border-slate-200',
};
@endphp

<div class="space-y-6">

    {{-- HEADER --}}
    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6 md:p-8">
        <div class="flex flex-col xl:flex-row xl:items-center xl:justify-between gap-6">
            <div>
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full border text-xs font-bold {{ $statusBadgeClass }} mb-4">
                    <span class="w-2 h-2 rounded-full
                        {{ $status === 'approved' ? 'bg-green-500' : ($status === 'pending' ? 'bg-yellow-500' : ($status === 'rejected' ? 'bg-red-500' : 'bg-slate-500')) }}">
                    </span>
                    {{ $statusLabel }}
                </div>

                <h1 class="text-3xl md:text-4xl font-extrabold text-slate-900 leading-tight">
                    Ringkasan Freelancer
                </h1>

                <p class="text-sm md:text-base text-slate-500 mt-3 leading-relaxed max-w-3xl">
                    Selamat datang kembali, {{ $user->nama }}. Kelola jasa, proyek, portofolio,
                    dan penghasilan Anda dalam satu dashboard.
                </p>
            </div>

            <div class="flex flex-col sm:flex-row gap-3">
                @if ($status === 'approved')
                <a href="{{ route('freelancer.jasa.create') }}"
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
                    Tambah Jasa
                </a>
                @else
                <button disabled
                    class="inline-flex items-center justify-center gap-2 px-5 py-3 rounded-xl bg-slate-200 text-slate-500 text-sm font-bold cursor-not-allowed">
                    <svg xmlns="http://www.w3.org/2000/svg"
                        class="w-5 h-5"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                        stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M12 6v12m6-6H6" />
                    </svg>
                    Tambah Jasa
                </button>
                @endif

                <a href="{{ route('freelancer.profile.index') }}"
                    class="inline-flex items-center justify-center gap-2 px-5 py-3 rounded-xl bg-white border border-slate-200 text-slate-800 text-sm font-bold hover:bg-slate-50 transition">
                    <svg xmlns="http://www.w3.org/2000/svg"
                        class="w-5 h-5"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                        stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M12 12a4 4 0 100-8 4 4 0 000 8zm-7 8a7 7 0 0114 0" />
                    </svg>
                    Kelola Profil
                </a>
            </div>
        </div>
    </div>

    {{-- STATUS VERIFIKASI --}}
    @if ($status === 'pending')
    <div class="rounded-3xl bg-yellow-50 border border-yellow-200 text-yellow-800 p-5 md:p-6">
        <div class="flex items-start gap-4">
            <div class="w-12 h-12 rounded-2xl bg-yellow-100 text-yellow-700 flex items-center justify-center shrink-0">
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

            <div>
                <p class="font-extrabold">
                    Akun sedang menunggu verifikasi admin.
                </p>
                <p class="text-sm mt-1 leading-relaxed">
                    Anda belum bisa membuat jasa sampai admin menyetujui data KTM dan portofolio Anda.
                </p>
            </div>
        </div>
    </div>
    @elseif ($status === 'approved')
    <div class="rounded-3xl bg-green-50 border border-green-200 text-green-800 p-5 md:p-6">
        <div class="flex items-start gap-4">
            <div class="w-12 h-12 rounded-2xl bg-green-100 text-green-700 flex items-center justify-center shrink-0">
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

            <div>
                <p class="font-extrabold">
                    Akun freelancer Anda sudah diverifikasi.
                </p>
                <p class="text-sm mt-1 leading-relaxed">
                    Anda sudah bisa membuat jasa dan menerima pesanan dari customer.
                </p>
            </div>
        </div>
    </div>
    @elseif ($status === 'rejected')
    <div class="rounded-3xl bg-red-50 border border-red-200 text-red-800 p-5 md:p-6">
        <div class="flex items-start gap-4">
            <div class="w-12 h-12 rounded-2xl bg-red-100 text-red-700 flex items-center justify-center shrink-0">
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

            <div>
                <p class="font-extrabold">
                    Verifikasi akun Anda ditolak.
                </p>
                <p class="text-sm mt-1 leading-relaxed">
                    Catatan admin: {{ $verifikasi?->catatan_admin ?? 'Tidak ada catatan.' }}
                </p>
            </div>
        </div>
    </div>
    @endif

    {{-- STATISTIK --}}
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-5">

        {{-- Status --}}
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
                            d="M12 12a4 4 0 100-8 4 4 0 000 8zm-7 8a7 7 0 0114 0" />
                    </svg>
                </div>

                <span class="px-3 py-1 rounded-full text-xs font-bold border {{ $statusBadgeClass }}">
                    Akun
                </span>
            </div>

            <p class="text-xs text-slate-400 mt-5 uppercase font-bold tracking-wide">
                Status Verifikasi
            </p>

            <h3 class="text-2xl font-extrabold text-slate-900 mt-1">
                {{ $statusLabel }}
            </h3>
        </div>

        {{-- Total Jasa --}}
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
                            d="M4 7h16M4 12h16M4 17h10" />
                    </svg>
                </div>

                <span class="px-3 py-1 rounded-full bg-purple-50 text-purple-700 text-xs font-bold">
                    Jasa
                </span>
            </div>

            <p class="text-xs text-slate-400 mt-5 uppercase font-bold tracking-wide">
                Total Jasa
            </p>

            <h3 class="text-3xl font-extrabold text-slate-900 mt-1">
                {{ number_format($totalJasa, 0, ',', '.') }}
            </h3>

            <p class="text-xs text-slate-400 mt-1">
                Aktif: {{ $jasaAktif }} • Pending: {{ $jasaPending }}
            </p>
        </div>

        {{-- Project --}}
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
                Project Berjalan
            </p>

            <h3 class="text-3xl font-extrabold text-slate-900 mt-1">
                {{ number_format($pesananBerjalan, 0, ',', '.') }}
            </h3>

            <p class="text-xs text-slate-400 mt-1">
                Selesai: {{ $pesananSelesai }}
            </p>
        </div>

        {{-- Pendapatan --}}
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
                    Saldo
                </span>
            </div>

            <p class="text-xs text-slate-400 mt-5 uppercase font-bold tracking-wide">
                Pendapatan Cair
            </p>

            <h3 class="text-2xl font-extrabold text-slate-900 mt-1">
                Rp {{ number_format($totalPendapatan, 0, ',', '.') }}
            </h3>

            <p class="text-xs text-slate-400 mt-1">
                Ditahan: Rp {{ number_format($saldoDitahan, 0, ',', '.') }}
            </p>
        </div>

        {{-- Rating --}}
        <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6 hover:shadow-md transition">
            <div class="flex items-start justify-between gap-4">
                <div class="w-12 h-12 rounded-2xl bg-yellow-100 text-yellow-600 flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg"
                        class="w-6 h-6 fill-current"
                        viewBox="0 0 24 24">
                        <path d="M12 3.5l2.65 5.37 5.93.86-4.29 4.18 1.01 5.9L12 17.02 6.7 19.81l1.01-5.9-4.29-4.18 5.93-.86L12 3.5z" />
                    </svg>
                </div>

                <span class="px-3 py-1 rounded-full bg-yellow-50 text-yellow-700 text-xs font-bold">
                    Rating
                </span>
            </div>

            <p class="text-xs text-slate-400 mt-5 uppercase font-bold tracking-wide">
                Rating Freelancer
            </p>

            <h3 class="text-3xl font-extrabold text-slate-900 mt-1">
                {{ $totalReview > 0 ? number_format((float) $ratingRataRata, 1) : '0.0' }}
            </h3>

            <p class="text-xs text-slate-400 mt-1">
                {{ $totalReview }} review
            </p>
        </div>

        {{-- Portofolio --}}
        <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6 hover:shadow-md transition">
            <div class="flex items-start justify-between gap-4">
                <div class="w-12 h-12 rounded-2xl bg-indigo-100 text-indigo-600 flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg"
                        class="w-6 h-6"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                        stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M4 5h16v14H4V5zm3 10l3-3 3 3 2-2 2 2" />
                    </svg>
                </div>

                <span class="px-3 py-1 rounded-full bg-indigo-50 text-indigo-700 text-xs font-bold">
                    Total
                </span>
            </div>

            <p class="text-xs text-slate-400 mt-5 uppercase font-bold tracking-wide">
                Portofolio
            </p>

            <h3 class="text-3xl font-extrabold text-slate-900 mt-1">
                {{ number_format($totalPortofolio, 0, ',', '.') }}
            </h3>
        </div>
    </div>

    {{-- PROJECT + MENU CEPAT --}}
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">

        {{-- PROJECT TERBARU --}}
        <div class="xl:col-span-2 bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="px-6 py-5 border-b border-slate-200 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <h2 class="text-xl font-extrabold text-slate-900">
                        Project Terbaru
                    </h2>
                    <p class="text-sm text-slate-500 mt-1">
                        Daftar proyek yang sedang atau pernah Anda kerjakan.
                    </p>
                </div>

                <a href="{{ route('freelancer.pesanan.index') }}"
                    class="inline-flex items-center justify-center px-4 py-2 rounded-xl bg-slate-100 text-slate-700 text-sm font-bold hover:bg-slate-200 transition">
                    Lihat Semua
                </a>
            </div>

            @if ($pesananTerbaru->count() > 0)
            <div class="divide-y divide-slate-100">
                @foreach ($pesananTerbaru as $pesanan)
                @php
                $warnaStatus = match ($pesanan->status_pesanan) {
                'selesai' => 'bg-green-100 text-green-700',
                'dibayar', 'diproses', 'menunggu_approve' => 'bg-blue-100 text-blue-700',
                'revisi' => 'bg-yellow-100 text-yellow-700',
                'dispute', 'dibatalkan' => 'bg-red-100 text-red-700',
                default => 'bg-slate-100 text-slate-700',
                };
                @endphp

                <div class="p-5 hover:bg-slate-50 transition">
                    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                        <div class="flex items-start gap-4">
                            <div class="w-12 h-12 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
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

                            <div>
                                <h3 class="font-extrabold text-slate-900">
                                    {{ $pesanan->jasa->nama_jasa ?? 'Jasa tidak ditemukan' }}
                                </h3>

                                <p class="text-sm text-slate-500 mt-1">
                                    Customer:
                                    <span class="font-semibold text-slate-700">
                                        {{ $pesanan->customer->nama ?? '-' }}
                                    </span>
                                </p>

                                <p class="text-xs text-slate-400 mt-1">
                                    Order #{{ $pesanan->id }} • {{ $pesanan->created_at->format('d M Y, H:i') }}
                                </p>
                            </div>
                        </div>

                        <div class="flex items-center gap-3 lg:justify-end">
                            <span class="px-3 py-1 rounded-full text-xs font-bold {{ $warnaStatus }}">
                                {{ ucwords(str_replace('_', ' ', $pesanan->status_pesanan)) }}
                            </span>

                            <a href="{{ route('freelancer.pesanan.index') }}"
                                class="inline-flex items-center justify-center px-4 py-2 rounded-xl bg-blue-50 text-blue-700 text-sm font-bold hover:bg-blue-100 transition">
                                Detail
                            </a>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <div class="p-10 text-center">
                <div class="mx-auto w-16 h-16 rounded-3xl bg-slate-100 text-slate-400 flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg"
                        class="w-8 h-8"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                        stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M3 7a2 2 0 012-2h5l2 2h7a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V7z" />
                    </svg>
                </div>

                <h3 class="text-lg font-extrabold text-slate-900 mt-4">
                    Belum ada project aktif
                </h3>

                <p class="text-sm text-slate-500 mt-1">
                    Project akan muncul setelah customer melakukan pemesanan.
                </p>
            </div>
            @endif
        </div>

        {{-- MENU CEPAT --}}
        <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6">
            <h2 class="text-xl font-extrabold text-slate-900">
                Menu Cepat
            </h2>

            <p class="text-sm text-slate-500 mt-1 mb-5">
                Akses cepat untuk mengelola aktivitas freelancer.
            </p>

            <div class="space-y-3">
                @if ($status === 'approved')
                <a href="{{ route('freelancer.jasa.create') }}"
                    class="flex items-center justify-between gap-3 px-5 py-4 bg-blue-600 text-white rounded-2xl font-bold hover:bg-blue-700 transition">
                    <span class="inline-flex items-center gap-3">
                        <svg xmlns="http://www.w3.org/2000/svg"
                            class="w-5 h-5"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                            stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M12 6v12m6-6H6" />
                        </svg>
                        Buat Jasa Baru
                    </span>
                    <span>›</span>
                </a>
                @else
                <div class="flex items-center justify-between gap-3 px-5 py-4 bg-slate-100 text-slate-400 rounded-2xl font-bold">
                    <span class="inline-flex items-center gap-3">
                        <svg xmlns="http://www.w3.org/2000/svg"
                            class="w-5 h-5"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                            stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M12 6v12m6-6H6" />
                        </svg>
                        Buat Jasa Baru
                    </span>
                    <span>Locked</span>
                </div>
                @endif

                <a href="{{ route('freelancer.portfolio.index') }}"
                    class="flex items-center justify-between gap-3 px-5 py-4 bg-slate-50 border border-slate-200 text-slate-700 rounded-2xl font-bold hover:bg-slate-100 transition">
                    <span class="inline-flex items-center gap-3">
                        <svg xmlns="http://www.w3.org/2000/svg"
                            class="w-5 h-5"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                            stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M4 5h16v14H4V5zm3 10l3-3 3 3 2-2 2 2" />
                        </svg>
                        Lihat Portofolio
                    </span>
                    <span>›</span>
                </a>

                <a href="{{ route('freelancer.pesanan.index') }}"
                    class="flex items-center justify-between gap-3 px-5 py-4 bg-slate-50 border border-slate-200 text-slate-700 rounded-2xl font-bold hover:bg-slate-100 transition">
                    <span class="inline-flex items-center gap-3">
                        <svg xmlns="http://www.w3.org/2000/svg"
                            class="w-5 h-5"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                            stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M3 7a2 2 0 012-2h5l2 2h7a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V7z" />
                        </svg>
                        Lihat Project
                    </span>
                    <span>›</span>
                </a>

                <a href="{{ route('freelancer.earnings.index') }}"
                    class="flex items-center justify-between gap-3 px-5 py-4 bg-slate-50 border border-slate-200 text-slate-700 rounded-2xl font-bold hover:bg-slate-100 transition">
                    <span class="inline-flex items-center gap-3">
                        <svg xmlns="http://www.w3.org/2000/svg"
                            class="w-5 h-5"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                            stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M4 7h16v10H4V7zm3 3h.01M17 14h.01M12 12a2 2 0 100-4 2 2 0 000 4z" />
                        </svg>
                        Penghasilan Saya
                    </span>
                    <span>›</span>
                </a>
            </div>
        </div>
    </div>

    {{-- JASA TERBARU --}}
    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-6 py-5 border-b border-slate-200 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h2 class="text-xl font-extrabold text-slate-900">
                    Jasa Terbaru
                </h2>
                <p class="text-sm text-slate-500 mt-1">
                    Daftar jasa terbaru yang Anda buat.
                </p>
            </div>

            <a href="{{ route('freelancer.jasa.index') }}"
                class="inline-flex items-center justify-center px-4 py-2 rounded-xl bg-slate-100 text-slate-700 text-sm font-bold hover:bg-slate-200 transition">
                Kelola Jasa
            </a>
        </div>

        @if ($jasaTerbaru->count() > 0)
        <div class="divide-y divide-slate-100">
            @foreach ($jasaTerbaru as $item)
            @php
            $warnaJasa = match ($item->status_jasa) {
            'active' => 'bg-green-100 text-green-700',
            'pending' => 'bg-yellow-100 text-yellow-700',
            'rejected' => 'bg-red-100 text-red-700',
            default => 'bg-slate-100 text-slate-700',
            };
            @endphp

            <div class="p-5 hover:bg-slate-50 transition">
                <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                    <div class="flex items-start gap-4">
                        <div class="w-12 h-12 rounded-2xl bg-purple-50 text-purple-600 flex items-center justify-center shrink-0">
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

                        <div>
                            <h3 class="font-extrabold text-slate-900">
                                {{ $item->nama_jasa }}
                            </h3>

                            <p class="text-sm text-slate-500 mt-1">
                                {{ $item->kategori }} • Rp {{ number_format($item->harga, 0, ',', '.') }}
                            </p>

                            <p class="text-xs text-slate-400 mt-1">
                                Dibuat {{ $item->created_at->format('d M Y, H:i') }}
                            </p>
                        </div>
                    </div>

                    <div class="flex items-center gap-3 lg:justify-end">
                        <span class="px-3 py-1 rounded-full text-xs font-bold {{ $warnaJasa }}">
                            {{ ucfirst($item->status_jasa) }}
                        </span>

                        <a href="{{ route('freelancer.jasa.index') }}"
                            class="inline-flex items-center justify-center px-4 py-2 rounded-xl bg-blue-50 text-blue-700 text-sm font-bold hover:bg-blue-100 transition">
                            Kelola
                        </a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @else
        <div class="p-10 text-center">
            <div class="mx-auto w-16 h-16 rounded-3xl bg-slate-100 text-slate-400 flex items-center justify-center">
                <svg xmlns="http://www.w3.org/2000/svg"
                    class="w-8 h-8"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                    stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M4 7h16M4 12h16M4 17h10" />
                </svg>
            </div>

            <h3 class="text-lg font-extrabold text-slate-900 mt-4">
                Anda belum membuat jasa
            </h3>

            <p class="text-sm text-slate-500 mt-1">
                Setelah akun disetujui admin, Anda dapat membuat jasa pertama.
            </p>

            @if ($status === 'approved')
            <a href="{{ route('freelancer.jasa.create') }}"
                class="inline-flex items-center justify-center mt-6 px-5 py-3 rounded-xl bg-blue-600 text-white text-sm font-bold hover:bg-blue-700 transition">
                Buat Jasa Sekarang
            </a>
            @endif
        </div>
        @endif
    </div>
</div>
@endsection