@extends('layouts.customer')

@section('title', 'Profil Freelancer - JasaKampus')

@section('content')
<section class="px-6 py-8">
    <div class="max-w-7xl mx-auto space-y-8">

        <div>
            <a href="{{ url()->previous() }}"
                class="inline-flex items-center gap-2 text-sm font-bold text-blue-600 hover:underline">
                ← Kembali
            </a>
        </div>

        {{-- HEADER PROFILE --}}
        <div class="bg-white border border-slate-200 rounded-3xl shadow-sm overflow-hidden">
            <div class="h-40 bg-gradient-to-br from-blue-700 via-blue-800 to-slate-950"></div>

            <div class="px-6 md:px-8 pb-8">
                <div class="-mt-16 flex flex-col md:flex-row md:items-end md:justify-between gap-6">

                    <div class="flex flex-col md:flex-row md:items-end gap-5">
                        <div class="w-32 h-32 rounded-3xl bg-blue-600 border-4 border-white shadow-lg overflow-hidden flex items-center justify-center text-white text-4xl font-extrabold">
                            @if ($freelancer->foto_profil)
                            <img src="{{ \App\Services\CloudinaryService::mediaUrl($freelancer->foto_profil) }}"
                                alt="Foto Freelancer"
                                class="w-full h-full object-cover">
                            @else
                            {{ strtoupper(substr($freelancer->nama ?? $freelancer->email, 0, 1)) }}
                            @endif
                        </div>

                        <div class="pb-1">
                            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-green-100 text-green-700 text-xs font-bold mb-3">
                                <span class="w-2 h-2 rounded-full bg-green-500"></span>
                                Freelancer Terverifikasi
                            </div>

                            <h1 class="text-3xl md:text-4xl font-extrabold text-slate-900">
                                {{ $freelancer->nama ?? 'Freelancer' }}
                            </h1>

                            <p class="text-slate-500 mt-2">
                                {{ $freelancer->verifikasiFreelancer->program_studi ?? '-' }}
                                @if ($freelancer->verifikasiFreelancer?->universitas)
                                • {{ $freelancer->verifikasiFreelancer->universitas }}
                                @endif
                            </p>
                        </div>
                    </div>

                    <div class="flex flex-col sm:flex-row gap-3">
                        @if ($jasaAktif->count() > 0)
                        <a href="{{ route('customer.chat.show', $jasaAktif->first()->id) }}"
                            class="inline-flex items-center justify-center px-5 py-3 rounded-xl bg-blue-600 text-white text-sm font-bold hover:bg-blue-700 transition">
                            Hubungi Freelancer
                        </a>
                        @endif
                    </div>
                </div>

                {{-- STATS --}}
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-8">
                    <div class="rounded-2xl bg-slate-50 border border-slate-200 p-5">
                        <p class="text-2xl font-extrabold text-slate-900">
                            {{ $totalReview > 0 ? number_format((float) $ratingRataRata, 1) : '0.0' }}
                        </p>
                        <p class="text-sm text-slate-500 mt-1">
                            Rating
                        </p>
                    </div>

                    <div class="rounded-2xl bg-slate-50 border border-slate-200 p-5">
                        <p class="text-2xl font-extrabold text-slate-900">
                            {{ $totalReview }}
                        </p>
                        <p class="text-sm text-slate-500 mt-1">
                            Review
                        </p>
                    </div>

                    <div class="rounded-2xl bg-slate-50 border border-slate-200 p-5">
                        <p class="text-2xl font-extrabold text-slate-900">
                            {{ $totalJasaAktif }}
                        </p>
                        <p class="text-sm text-slate-500 mt-1">
                            Jasa Aktif
                        </p>
                    </div>

                    <div class="rounded-2xl bg-slate-50 border border-slate-200 p-5">
                        <p class="text-2xl font-extrabold text-slate-900">
                            {{ $totalProyekSelesai }}
                        </p>
                        <p class="text-sm text-slate-500 mt-1">
                            Proyek Selesai
                        </p>
                    </div>
                </div>
            </div>
        </div>

        {{-- INFO DAN JASA --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

            {{-- LEFT --}}
            <div class="lg:col-span-2 space-y-8">

                {{-- JASA --}}
                <div class="bg-white border border-slate-200 rounded-3xl shadow-sm p-6">
                    <div class="flex items-center justify-between gap-4 mb-6">
                        <div>
                            <h2 class="text-xl font-extrabold text-slate-900">
                                Jasa yang Ditawarkan
                            </h2>
                            <p class="text-sm text-slate-500 mt-1">
                                Layanan aktif milik freelancer ini.
                            </p>
                        </div>
                    </div>

                    @if ($jasaAktif->count() > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        @foreach ($jasaAktif as $jasa)
                        <a href="{{ route('customer.jasa.show', $jasa->id) }}"
                            class="group rounded-2xl border border-slate-200 overflow-hidden hover:shadow-lg hover:-translate-y-1 transition bg-white">

                            <div class="h-40 bg-slate-100 overflow-hidden">
                                @if ($jasa->thumbnail)
                                <img src="{{ \App\Services\CloudinaryService::mediaUrl($jasa->thumbnail) }}"
                                    alt="{{ $jasa->nama_jasa }}"
                                    class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                                @else
                                <div class="w-full h-full flex items-center justify-center text-slate-400">
                                    Tidak ada gambar
                                </div>
                                @endif
                            </div>

                            <div class="p-5">
                                <div class="flex items-center justify-between gap-3 mb-3">
                                    <span class="px-3 py-1 rounded-full bg-blue-100 text-blue-700 text-xs font-bold">
                                        {{ $jasa->kategori ?? 'Jasa' }}
                                    </span>

                                    <span class="text-xs text-slate-500">
                                        <span class="inline-flex items-center gap-1">
                                            <svg xmlns="http://www.w3.org/2000/svg"
                                                class="w-4 h-4 text-yellow-400 fill-current"
                                                viewBox="0 0 24 24">
                                                <path d="M12 3.5l2.65 5.37 5.93.86-4.29 4.18 1.01 5.9L12 17.02 6.7 19.81l1.01-5.9-4.29-4.18 5.93-.86L12 3.5z" />
                                            </svg>

                                            {{ $jasa->reviews_count > 0 ? number_format((float) $jasa->rating_rata_rata, 1) : '0.0' }}
                                        </span>
                                    </span>
                                </div>

                                <h3 class="font-extrabold text-slate-900 group-hover:text-blue-700 transition line-clamp-2">
                                    {{ $jasa->nama_jasa }}
                                </h3>

                                <p class="text-sm text-slate-500 mt-2 line-clamp-2">
                                    {{ $jasa->deskripsi }}
                                </p>

                                <p class="text-blue-600 font-extrabold mt-4">
                                    Rp {{ number_format($jasa->harga, 0, ',', '.') }}
                                </p>
                            </div>
                        </a>
                        @endforeach
                    </div>
                    @else
                    <div class="rounded-2xl border border-dashed border-slate-300 p-8 text-center">
                        <p class="font-bold text-slate-700">
                            Belum ada jasa aktif.
                        </p>
                        <p class="text-sm text-slate-500 mt-1">
                            Freelancer ini belum memiliki layanan yang sedang aktif.
                        </p>
                    </div>
                    @endif
                </div>

                {{-- REVIEW --}}
                <div class="bg-white border border-slate-200 rounded-3xl shadow-sm p-6">
                    <h2 class="text-xl font-extrabold text-slate-900">
                        Review Customer
                    </h2>

                    <p class="text-sm text-slate-500 mt-1 mb-6">
                        Ulasan terbaru untuk freelancer ini.
                    </p>

                    @if ($reviews->count() > 0)
                    <div class="space-y-4">
                        @foreach ($reviews as $review)
                        <div class="rounded-2xl border border-slate-200 p-5">
                            <div class="flex items-start justify-between gap-4">
                                <div>
                                    <p class="font-bold text-slate-900">
                                        {{ $review->customer->nama ?? 'Customer' }}
                                    </p>

                                    <p class="text-xs text-slate-500 mt-1">
                                        {{ $review->jasa->nama_jasa ?? '-' }}
                                    </p>
                                </div>

                                <div class="text-sm font-bold text-yellow-500">
                                    {{ str_repeat('★', (int) $review->rating) }}
                                    <span class="text-slate-300">
                                        {{ str_repeat('★', 5 - (int) $review->rating) }}
                                    </span>
                                </div>
                            </div>

                            <p class="text-sm text-slate-600 leading-relaxed mt-4">
                                {{ $review->ulasan ?? '-' }}
                            </p>
                        </div>
                        @endforeach
                    </div>
                    @else
                    <div class="rounded-2xl border border-dashed border-slate-300 p-8 text-center">
                        <p class="font-bold text-slate-700">
                            Belum ada review.
                        </p>
                        <p class="text-sm text-slate-500 mt-1">
                            Review akan muncul setelah customer menyelesaikan pesanan.
                        </p>
                    </div>
                    @endif
                </div>
            </div>

            {{-- RIGHT --}}
            <div class="space-y-8">

                {{-- DETAIL --}}
                <div class="bg-white border border-slate-200 rounded-3xl shadow-sm p-6">
                    <h2 class="text-xl font-extrabold text-slate-900">
                        Informasi Freelancer
                    </h2>

                    <div class="mt-6 space-y-5 text-sm">
                        <div>
                            <p class="text-slate-500">
                                Universitas
                            </p>
                            <p class="font-bold text-slate-900 mt-1">
                                {{ $freelancer->verifikasiFreelancer->universitas ?? '-' }}
                            </p>
                        </div>

                        <div>
                            <p class="text-slate-500">
                                Program Studi
                            </p>
                            <p class="font-bold text-slate-900 mt-1">
                                {{ $freelancer->verifikasiFreelancer->program_studi ?? '-' }}
                            </p>
                        </div>

                        <div>
                            <p class="text-slate-500">
                                Bergabung Sejak
                            </p>
                            <p class="font-bold text-slate-900 mt-1">
                                {{ optional($freelancer->created_at)->format('d M Y') }}
                            </p>
                        </div>
                    </div>
                </div>

                {{-- PORTOFOLIO --}}
                <div class="bg-white border border-slate-200 rounded-3xl shadow-sm p-6">
                    <h2 class="text-xl font-extrabold text-slate-900">
                        Portofolio
                    </h2>

                    <p class="text-sm text-slate-500 mt-1 mb-6">
                        Karya atau contoh hasil pekerjaan freelancer.
                    </p>

                    @if ($portofolios->count() > 0)
                    <div class="space-y-4">
                        @foreach ($portofolios as $portofolio)
                        <div class="rounded-2xl border border-slate-200 p-4">
                            <h3 class="font-bold text-slate-900">
                                {{ $portofolio->judul_portofolio }}
                            </h3>

                            <p class="text-sm text-slate-500 mt-2 line-clamp-3">
                                {{ $portofolio->deskripsi }}
                            </p>

                            @if ($portofolio->file_portofolio)
                            <a href="{{ \App\Services\CloudinaryService::mediaUrl($portofolio->file_portofolio) }}"
                                target="_blank"
                                class="inline-flex items-center mt-4 text-sm font-bold text-blue-600 hover:underline">
                                Lihat File Portofolio
                            </a>
                            @endif
                        </div>
                        @endforeach
                    </div>
                    @else
                    <div class="rounded-2xl border border-dashed border-slate-300 p-6 text-center">
                        <p class="font-bold text-slate-700">
                            Belum ada portofolio.
                        </p>
                    </div>
                    @endif
                </div>

            </div>
        </div>
    </div>
</section>
@endsection