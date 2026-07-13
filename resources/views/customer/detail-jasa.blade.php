@extends('layouts.customer')

@section('title', 'Detail Jasa - JasaKampus')

@section('content')
@php
$avgRating = round($jasa->reviews->avg('rating') ?? 0, 1);
$totalReview = $jasa->reviews->count();

$ratingText = $totalReview > 0
? number_format($avgRating, 1)
: '0.0';
@endphp
<section class="px-6 py-6">
    <div class="mb-5">
        <p class="text-xs text-slate-500">
            Pasar Jasa / {{ $jasa->kategori }} / Detail Jasa
        </p>

        @php
        $avgRating = round($jasa->reviews->avg('rating') ?? 0, 1);
        $totalReview = $jasa->reviews->count();
        @endphp

        <h1 class="text-2xl md:text-3xl font-bold text-slate-900 mt-2">
            {{ $jasa->nama_jasa }}
        </h1>

        <div class="flex flex-wrap items-center gap-3 mt-3 text-sm">
            <div class="flex items-center gap-2">
                <div class="w-7 h-7 rounded-full bg-slate-900 text-white flex items-center justify-center text-xs font-bold">
                    {{ strtoupper(substr($jasa->freelancer->nama ?? 'F', 0, 1)) }}
                </div>

                <span class="font-semibold text-slate-700">
                    {{ $jasa->freelancer->nama ?? 'Freelancer' }}
                </span>
            </div>

            <span class="text-yellow-500 font-bold">
                ★ {{ $ratingText }}
            </span>

            <span class="text-slate-400">|</span>

            <span class="text-slate-500">
                {{ $totalReview }} review
            </span>

            <span class="text-slate-400">|</span>

            <span class="text-slate-500">
                Freelancer terverifikasi
            </span>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

        <div class="lg:col-span-8 space-y-6">

            <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-5">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div class="md:col-span-3 h-[420px] rounded-xl overflow-hidden bg-slate-100">
                        @if ($jasa->thumbnail)
                        <img src="{{ \App\Services\CloudinaryService::mediaUrl($jasa->thumbnail) }}"
                            alt="{{ $jasa->nama_jasa }}"
                            class="w-full h-full object-cover">
                        @else
                        <div class="w-full h-full bg-gradient-to-br from-blue-100 to-slate-200 flex items-center justify-center">
                            <span class="text-7xl">🖼️</span>
                        </div>
                        @endif
                    </div>

                    <div class="grid grid-cols-2 md:grid-cols-1 gap-4">
                        <div class="h-[200px] rounded-xl overflow-hidden bg-slate-100 flex items-center justify-center">
                            <span class="text-5xl">💻</span>
                        </div>

                        <div class="h-[200px] rounded-xl overflow-hidden bg-slate-100 flex items-center justify-center">
                            <span class="text-5xl">📱</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6">
                <h2 class="text-xl font-bold text-slate-900">
                    Tentang Layanan Ini
                </h2>

                <p class="text-sm text-slate-600 leading-relaxed mt-4 whitespace-pre-line">
                    {{ $jasa->deskripsi }}
                </p>

                <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="p-4 rounded-xl bg-blue-50 border border-blue-100">
                        <p class="text-xs text-slate-500">Kategori</p>
                        <p class="font-bold text-slate-900 mt-1">
                            {{ $jasa->kategori }}
                        </p>
                    </div>

                    <div class="p-4 rounded-xl bg-blue-50 border border-blue-100">
                        <p class="text-xs text-slate-500">Estimasi Pengerjaan</p>
                        <p class="font-bold text-slate-900 mt-1">
                            {{ $jasa->estimasi_pengerjaan }}
                        </p>
                    </div>

                    <div class="p-4 rounded-xl bg-blue-50 border border-blue-100">
                        <p class="text-xs text-slate-500">Sistem Transaksi</p>
                        <p class="font-bold text-slate-900 mt-1">
                            Escrow
                        </p>
                    </div>

                    <div class="p-4 rounded-xl bg-blue-50 border border-blue-100">
                        <p class="text-xs text-slate-500">Status Freelancer</p>
                        <p class="font-bold text-slate-900 mt-1">
                            Terverifikasi
                        </p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-xl font-bold text-slate-900">
                        Portofolio Freelancer
                    </h2>

                    <span class="text-sm text-blue-600 font-semibold">
                        {{ $jasa->freelancer->portofolios->count() }} File
                    </span>
                </div>

                @if ($jasa->freelancer->portofolios->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach ($jasa->freelancer->portofolios as $portofolio)
                    <div class="border border-slate-200 rounded-xl p-4 bg-slate-50">
                        <h3 class="font-bold text-slate-900">
                            {{ $portofolio->judul_portofolio }}
                        </h3>

                        <p class="text-sm text-slate-500 mt-1 line-clamp-2">
                            {{ $portofolio->deskripsi }}
                        </p>

                        <a href="{{ \App\Services\CloudinaryService::mediaUrl($portofolio->file_portofolio) }}"
                            target="_blank"
                            class="inline-block mt-4 px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-semibold hover:bg-blue-700">
                            Lihat Portofolio
                        </a>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="p-5 bg-slate-50 rounded-xl text-sm text-slate-500">
                    Freelancer belum memiliki portofolio tambahan.
                </div>
                @endif
            </div>

            {{-- REVIEW CUSTOMER --}}
            @php
            $avgRating = round($jasa->reviews->avg('rating') ?? 0, 1);
            $totalReview = $jasa->reviews->count();
            @endphp

            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 mt-6">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                    <div>
                        <h2 class="text-xl font-bold text-slate-900">
                            Review Customer
                        </h2>

                        <p class="text-sm text-slate-500 mt-1">
                            Ulasan dari customer yang pernah menggunakan jasa ini.
                        </p>
                    </div>

                    <div class="text-left md:text-right">
                        <div class="flex items-center md:justify-end gap-2">
                            <span class="text-3xl font-bold text-yellow-500">
                                ★ {{ $avgRating }}
                            </span>
                        </div>

                        <p class="text-sm text-slate-500 mt-1">
                            {{ $totalReview }} review
                        </p>
                    </div>
                </div>

                @if ($jasa->reviews->count() > 0)
                <div class="mt-6 space-y-4">
                    @foreach ($jasa->reviews->sortByDesc('created_at') as $review)
                    <div class="p-5 bg-slate-50 border border-slate-200 rounded-2xl">
                        <div class="flex items-start justify-between gap-4">
                            <div class="flex items-center gap-3">
                                <div class="w-12 h-12 rounded-full bg-blue-600 text-white flex items-center justify-center font-bold">
                                    {{ strtoupper(substr($review->customer->nama ?? 'C', 0, 1)) }}
                                </div>

                                <div>
                                    <p class="font-bold text-slate-900">
                                        {{ $review->customer->nama ?? 'Customer' }}
                                    </p>

                                    <p class="text-xs text-slate-500 mt-1">
                                        {{ $review->created_at->format('d M Y') }}
                                    </p>
                                </div>
                            </div>

                            <div class="text-yellow-400 text-lg shrink-0">
                                @for ($i = 1; $i <= 5; $i++)
                                    <span class="{{ $i <= $review->rating ? 'text-yellow-400' : 'text-slate-300' }}">
                                    ★
                                    </span>
                                    @endfor
                            </div>
                        </div>

                        @if ($review->ulasan)
                        <p class="text-sm text-slate-600 mt-4 leading-relaxed">
                            {{ $review->ulasan }}
                        </p>
                        @else
                        <p class="text-sm text-slate-400 mt-4 italic">
                            Customer tidak menulis ulasan.
                        </p>
                        @endif

                        @if ($review->foto_review)
                        <div class="mt-4">
                            <img src="{{ \App\Services\CloudinaryService::mediaUrl($review->foto_review) }}"
                                alt="Foto Review"
                                class="w-full max-w-sm rounded-xl border border-slate-200">
                        </div>
                        @endif

                        @if ($review->rekomendasi)
                        <div class="mt-4 inline-flex items-center gap-2 px-3 py-1 bg-green-100 text-green-700 rounded-full text-xs font-bold">
                            ✓ Direkomendasikan customer
                        </div>
                        @endif
                    </div>
                    @endforeach
                </div>
                @else
                <div class="mt-6 p-8 bg-slate-50 border border-slate-200 rounded-2xl text-center">
                    <div class="text-4xl mb-3">⭐</div>

                    <h3 class="font-bold text-slate-900">
                        Belum ada review
                    </h3>

                    <p class="text-sm text-slate-500 mt-2">
                        Review akan muncul setelah customer menyelesaikan pesanan.
                    </p>
                </div>
                @endif
            </div>
        </div>

        <aside class="lg:col-span-4 space-y-6">
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6 top-20">
                <div class="flex items-center justify-between">
                    <p class="text-sm text-slate-500">
                        Paket Pemula
                    </p>

                    <span class="px-3 py-1 bg-blue-100 text-blue-700 rounded-full text-xs font-bold">
                        Escrow
                    </span>
                </div>

                <p class="text-3xl font-bold text-slate-900 mt-3">
                    Rp {{ number_format($jasa->harga, 0, ',', '.') }}
                </p>

                <p class="text-sm text-slate-500 mt-3">
                    Cocok untuk kebutuhan awal dengan ruang lingkup pekerjaan yang sudah disepakati melalui chat.
                </p>

                <div class="mt-5 space-y-3 text-sm">
                    <div class="flex items-center justify-between">
                        <span class="text-slate-500">Waktu Pengerjaan</span>
                        <span class="font-semibold text-slate-900">
                            {{ $jasa->estimasi_pengerjaan }}
                        </span>
                    </div>

                    <div class="flex items-center justify-between">
                        <span class="text-slate-500">Revisi</span>
                        <span class="font-semibold text-slate-900">
                            Maksimal 3 kali
                        </span>
                    </div>

                    <div class="flex items-center justify-between">
                        <span class="text-slate-500">Pembayaran</span>
                        <span class="font-semibold text-slate-900">
                            Ditahan escrow
                        </span>
                    </div>
                </div>

                <div class="mt-6">
                    <a href="{{ route('customer.chat.show', $jasa->id) }}"
                        class="block text-center w-full px-5 py-3 bg-slate-100 text-slate-700 rounded-lg font-semibold hover:bg-slate-200">
                        Chat Freelancer
                    </a>

                    @if ($sudahChat)
                    <a href="{{ route('customer.order.create', $jasa->id) }}"
                        class="block text-center mt-3 w-full px-5 py-3 bg-blue-600 text-white rounded-lg font-semibold hover:bg-blue-700">
                        Pesan Sekarang
                    </a>

                    <p class="text-xs text-green-600 text-center mt-2">
                        Anda sudah menghubungi freelancer dan dapat melanjutkan order.
                    </p>
                    @else
                    <button disabled
                        class="mt-3 w-full px-5 py-3 bg-slate-200 text-slate-400 rounded-lg font-semibold cursor-not-allowed">
                        Pesan Sekarang
                    </button>

                    <p class="text-xs text-red-500 text-center mt-2">
                        Chat freelancer terlebih dahulu sebelum order.
                    </p>
                    @endif
                </div>
            </div>

            <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-full bg-blue-600 text-white flex items-center justify-center font-bold">
                        {{ strtoupper(substr($jasa->freelancer->nama ?? 'F', 0, 1)) }}
                    </div>

                    <div>
                        <h3 class="font-bold text-slate-900">
                            {{ $jasa->freelancer->nama ?? 'Freelancer' }}
                        </h3>

                        <p class="text-xs text-slate-500">
                            Freelancer Terverifikasi
                        </p>
                    </div>
                </div>

                @php
                $detailJasa = $jasa ?? $jasas;
                $rating = $detailJasa->rating_rata_rata ?? null;
                $totalReview = $detailJasa->reviews_count ?? 0;
                @endphp

                <div class="grid grid-cols-2 gap-4 mt-6">
                    <div class="bg-slate-50 rounded-xl p-5 text-center">
                        <p class="text-3xl font-bold text-slate-900">
                            {{ $totalReviewJasa > 0 ? number_format((float) $ratingJasa, 1) : '0.0' }}
                        </p>

                        <p class="text-sm text-slate-600 mt-1">
                            Rating
                        </p>

                        <p class="text-sm text-slate-400 mt-2">
                            {{ $totalReviewJasa > 0 ? $totalReviewJasa . ' review' : 'Belum ada review' }}
                        </p>
                    </div>

                    <div class="bg-slate-50 rounded-xl p-5 text-center">
                        <p class="text-3xl font-bold text-slate-900">
                            Aktif
                        </p>

                        <p class="text-sm text-slate-600 mt-1">
                            Status
                        </p>
                    </div>
                </div>
            </div>

            <div class="mt-5 border-t border-slate-100 pt-5 text-sm">
                <p class="text-slate-500">
                    Universitas
                </p>

                <p class="font-semibold text-slate-900 mt-1">
                    {{ $jasa->freelancer->verifikasiFreelancer->universitas ?? '-' }}
                </p>

                <p class="text-slate-500 mt-4">
                    Program Studi
                </p>

                <p class="font-semibold text-slate-900 mt-1">
                    {{ $jasa->freelancer->verifikasiFreelancer->program_studi ?? '-' }}
                </p>
            </div>

            <a href="{{ route('customer.freelancer.profile', $jasa->freelancer->id) }}"
                class="block text-center mt-5 w-full px-5 py-3 bg-slate-100 text-slate-700 rounded-lg font-semibold hover:bg-slate-200">
                Lihat Profil Freelancer
            </a>
    </div>
    </aside>
    </div>
</section>
@endsection