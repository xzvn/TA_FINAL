@extends('layouts.customer')

@section('title', 'Dashboard Customer - JasaKampus')

@section('content')
<section class="px-4 md:px-6 py-6 md:py-8">
    <div class="max-w-7xl mx-auto space-y-7">

        {{-- HEADER --}}
        <div class="bg-white border border-slate-200 rounded-3xl shadow-sm p-6 md:p-8">
            <div class="flex flex-col xl:flex-row xl:items-center xl:justify-between gap-6">
                <div>
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-blue-50 text-blue-700 text-xs font-bold mb-4">
                        <span class="w-2 h-2 rounded-full bg-blue-600"></span>
                        Marketplace Jasa Mahasiswa
                    </div>

                    <h1 class="text-3xl md:text-4xl font-extrabold text-slate-900 leading-tight">
                        Temukan Layanan Jasa
                    </h1>

                    <p class="text-sm md:text-base text-slate-500 mt-3 leading-relaxed">
                        Ditemukan <span class="font-bold text-slate-900">{{ $jasa->total() }}</span>
                        layanan dari freelancer mahasiswa yang siap membantu kebutuhanmu.
                    </p>
                </div>

                {{-- FILTER --}}
                <form action="{{ route('dashboard') }}" method="GET"
                    class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-3 w-full xl:w-auto">
                    <input type="hidden" name="search" value="{{ request('search') }}">

                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase mb-2">
                            Kategori
                        </label>
                        <select
                            name="kategori"
                            onchange="this.form.submit()"
                            class="w-full rounded-xl border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="">Semua Kategori</option>

                            @foreach ($kategori as $itemKategori)
                            <option value="{{ $itemKategori }}" {{ request('kategori') == $itemKategori ? 'selected' : '' }}>
                                {{ ucfirst($itemKategori) }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase mb-2">
                            Urutkan
                        </label>
                        <select
                            name="sort"
                            onchange="this.form.submit()"
                            class="w-full rounded-xl border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="terlaris" {{ request('sort', 'terlaris') == 'terlaris' ? 'selected' : '' }}>
                                Paling Laris
                            </option>
                            <option value="terbaru" {{ request('sort') == 'terbaru' ? 'selected' : '' }}>
                                Terbaru
                            </option>
                            <option value="harga_terendah" {{ request('sort') == 'harga_terendah' ? 'selected' : '' }}>
                                Harga Terendah
                            </option>
                            <option value="harga_tertinggi" {{ request('sort') == 'harga_tertinggi' ? 'selected' : '' }}>
                                Harga Tertinggi
                            </option>
                        </select>
                    </div>

                    <div class="sm:col-span-2 xl:col-span-1 flex items-end">
                        <a href="{{ route('dashboard') }}"
                            class="w-full inline-flex items-center justify-center px-5 py-3 rounded-xl border border-slate-300 text-slate-700 text-sm font-bold hover:bg-slate-50 transition">
                            Reset Filter
                        </a>
                    </div>
                </form>
            </div>
        </div>

        {{-- INFO SEARCH --}}
        @if (request('search') || request('kategori'))
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 px-5 py-4 bg-blue-50 border border-blue-100 rounded-2xl">
            <p class="text-sm text-blue-700 font-semibold">
                Menampilkan hasil
                @if (request('search'))
                untuk pencarian <span class="font-extrabold">"{{ request('search') }}"</span>
                @endif

                @if (request('kategori'))
                pada kategori <span class="font-extrabold">{{ request('kategori') }}</span>
                @endif
            </p>

            <a href="{{ route('dashboard') }}"
                class="text-sm font-bold text-blue-700 hover:underline">
                Hapus filter
            </a>
        </div>
        @endif

        @if ($jasa->count() > 0)
        {{-- GRID JASA --}}
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
            @foreach ($jasa as $item)
            @php
            $isFavorite = in_array($item->id, $favoriteJasaIds ?? []);
            $rating = $item->rating_rata_rata ?? 0;
            $totalReview = $item->reviews_count ?? 0;
            @endphp

            <article class="group bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden hover:shadow-xl hover:-translate-y-1 transition duration-300 flex flex-col">

                {{-- IMAGE --}}
                <div class="relative h-52 bg-slate-100 overflow-hidden">
                    @if ($item->thumbnail)
                    <img src="{{ \App\Services\CloudinaryService::mediaUrl($item->thumbnail) }}"
                        alt="{{ $item->nama_jasa }}"
                        onerror="this.onerror=null;this.src='{{ asset('images/placeholder-service.svg') }}';"
                        class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                    @else
                    <div class="w-full h-full flex flex-col items-center justify-center bg-slate-100 text-slate-400">
                        <svg xmlns="http://www.w3.org/2000/svg"
                            class="w-12 h-12 mb-2"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                            stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M4 5h16v14H4V5zm3 10l3-3 3 3 2-2 2 2" />
                        </svg>
                        <span class="text-sm font-semibold">
                            Tidak ada gambar
                        </span>
                    </div>
                    @endif

                    <div class="absolute top-3 left-3">
                        <span class="inline-flex items-center px-3 py-1.5 rounded-full bg-white/90 backdrop-blur text-xs font-bold text-blue-700 shadow-sm">
                            {{ $item->kategori ?? 'Jasa' }}
                        </span>
                    </div>

                    <form action="{{ route('customer.favorite.toggle', $item->id) }}"
                        method="POST"
                        class="absolute top-3 right-3">
                        @csrf

                        <button type="submit"
                            class="w-10 h-10 bg-white/95 backdrop-blur rounded-full flex items-center justify-center shadow-sm border border-white/60 transition hover:scale-105
                                    {{ $isFavorite ? 'text-red-500' : 'text-slate-500 hover:text-red-500' }}"
                            title="{{ $isFavorite ? 'Hapus dari favorite' : 'Tambah ke favorite' }}">
                            @if ($isFavorite)
                            <svg xmlns="http://www.w3.org/2000/svg"
                                class="w-5 h-5 fill-current"
                                viewBox="0 0 24 24">
                                <path d="M12 21s-7.5-4.5-10-10.2C.3 6.9 2.8 3.5 6.8 3.5c2 0 3.8 1.1 5.2 2.9 1.4-1.8 3.2-2.9 5.2-2.9 4 0 6.5 3.4 4.8 7.3C19.5 16.5 12 21 12 21z" />
                            </svg>
                            @else
                            <svg xmlns="http://www.w3.org/2000/svg"
                                class="w-5 h-5"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                                stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M21 8.25c0-2.485-2.1-4.5-4.688-4.5-1.934 0-3.597 1.126-4.312 2.733C11.285 4.876 9.622 3.75 7.688 3.75 5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z" />
                            </svg>
                            @endif
                        </button>
                    </form>
                </div>

                {{-- BODY --}}
                <div class="p-5 md:p-6 flex flex-col flex-1">

                    {{-- FREELANCER + RATING --}}
                    <div class="flex items-start justify-between gap-3 mb-4">
                        <div class="flex items-center gap-2 min-w-0">
                            <div class="w-9 h-9 rounded-full bg-slate-900 text-white flex items-center justify-center text-xs font-bold shrink-0 overflow-hidden">
                                @if ($item->freelancer?->foto_profil)
                                <img src="{{ \App\Services\CloudinaryService::mediaUrl($item->freelancer->foto_profil) }}"
                                    alt="Foto Freelancer"
                                    onerror="this.onerror=null;this.src='{{ asset('images/placeholder-avatar.svg') }}';"
                                    class="w-full h-full object-cover">
                                @else
                                {{ strtoupper(substr($item->freelancer->nama ?? 'F', 0, 1)) }}
                                @endif
                            </div>

                            <div class="min-w-0">
                                <p class="text-sm font-bold text-slate-800 truncate">
                                    {{ $item->freelancer->nama ?? 'Freelancer' }}
                                </p>
                                <p class="text-xs text-slate-400 truncate">
                                    Freelancer Terverifikasi
                                </p>
                            </div>
                        </div>

                        <div class="shrink-0">
                            @if ($totalReview > 0)
                            <div class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-yellow-50 text-yellow-600 text-sm font-bold">
                                <svg xmlns="http://www.w3.org/2000/svg"
                                    class="w-4 h-4 fill-current"
                                    viewBox="0 0 24 24">
                                    <path d="M12 3.5l2.65 5.37 5.93.86-4.29 4.18 1.01 5.9L12 17.02 6.7 19.81l1.01-5.9-4.29-4.18 5.93-.86L12 3.5z" />
                                </svg>
                                <span>{{ number_format((float) $rating, 1) }}</span>
                                <span class="text-xs text-slate-400 font-semibold">
                                    ({{ $totalReview }})
                                </span>
                            </div>
                            @else
                            <div class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-slate-100 text-slate-400 text-xs font-bold">
                                Belum ada rating
                            </div>
                            @endif
                        </div>
                    </div>

                    <h2 class="font-extrabold text-slate-900 text-lg leading-snug line-clamp-2 min-h-[56px] group-hover:text-blue-700 transition">
                        {{ $item->nama_jasa }}
                    </h2>

                    @if (!empty($item->deskripsi))
                    <p class="text-sm text-slate-500 mt-3 line-clamp-2 leading-relaxed">
                        {{ $item->deskripsi }}
                    </p>
                    @else
                    <p class="text-sm text-slate-400 mt-3 line-clamp-2 leading-relaxed">
                        Deskripsi layanan belum tersedia.
                    </p>
                    @endif

                    <div class="flex flex-wrap gap-2 mt-4">
                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-blue-50 text-blue-700 rounded-full text-xs font-bold">
                            <svg xmlns="http://www.w3.org/2000/svg"
                                class="w-3.5 h-3.5"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                                stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M4 7h16M4 12h16M4 17h10" />
                            </svg>
                            {{ $item->kategori ?? 'Jasa' }}
                        </span>

                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-emerald-50 text-emerald-700 rounded-full text-xs font-bold">
                            <svg xmlns="http://www.w3.org/2000/svg"
                                class="w-3.5 h-3.5"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                                stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M9 12l2 2 4-4M12 3l7 4v5c0 5-3.5 8-7 9-3.5-1-7-4-7-9V7l7-4z" />
                            </svg>
                            Terverifikasi
                        </span>
                    </div>

                    {{-- FOOTER CARD --}}
                    <div class="mt-auto pt-5">
                        <div class="rounded-2xl bg-slate-50 border border-slate-100 p-4">
                            <div class="flex items-end justify-between gap-4">
                                <div class="min-w-0">
                                    <p class="text-xs text-slate-500 font-semibold">
                                        Estimasi
                                    </p>

                                    <p class="text-sm font-bold text-slate-700 mt-1">
                                        {{ $item->estimasi_pengerjaan ?? '-' }}
                                    </p>
                                </div>

                                <div class="text-right shrink-0">
                                    <p class="text-xs text-slate-500 font-semibold">
                                        Mulai dari
                                    </p>

                                    <p class="text-xl font-extrabold text-blue-600 mt-1">
                                        Rp {{ number_format($item->harga ?? 0, 0, ',', '.') }}
                                    </p>
                                </div>
                            </div>

                            <a href="{{ route('customer.jasa.show', $item->id) }}"
                                class="mt-4 w-full inline-flex items-center justify-center gap-2 px-5 py-3 bg-blue-600 text-white rounded-xl text-sm font-bold hover:bg-blue-700 transition">
                                Lihat Detail
                                <svg xmlns="http://www.w3.org/2000/svg"
                                    class="w-4 h-4"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor"
                                    stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M9 5l7 7-7 7" />
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>
            </article>
            @endforeach
        </div>

        {{-- PAGINATION --}}
        @if ($jasa->hasPages())
        <div class="mt-10 flex flex-wrap justify-center items-center gap-3">
            @if ($jasa->onFirstPage())
            <span class="w-11 h-11 flex items-center justify-center rounded-xl border border-slate-200 text-slate-300 cursor-not-allowed">
                ‹
            </span>
            @else
            <a href="{{ $jasa->previousPageUrl() }}"
                class="w-11 h-11 flex items-center justify-center rounded-xl border border-slate-300 text-slate-600 hover:bg-blue-50 transition">
                ‹
            </a>
            @endif

            @foreach ($jasa->getUrlRange(1, $jasa->lastPage()) as $page => $url)
            @if ($page == $jasa->currentPage())
            <span class="w-11 h-11 flex items-center justify-center rounded-xl bg-blue-600 text-white font-bold">
                {{ $page }}
            </span>
            @elseif ($page <= 3 || $page==$jasa->lastPage() || abs($page - $jasa->currentPage()) <= 1)
                    <a href="{{ $url }}"
                    class="w-11 h-11 flex items-center justify-center rounded-xl border border-slate-300 text-slate-700 hover:bg-blue-50 transition">
                    {{ $page }}
                    </a>
                    @elseif ($page == 4 && $jasa->currentPage() < $jasa->lastPage() - 2)
                        <span class="px-2 text-slate-400">...</span>
                        @endif
                        @endforeach

                        @if ($jasa->hasMorePages())
                        <a href="{{ $jasa->nextPageUrl() }}"
                            class="w-11 h-11 flex items-center justify-center rounded-xl border border-slate-300 text-slate-600 hover:bg-blue-50 transition">
                            ›
                        </a>
                        @else
                        <span class="w-11 h-11 flex items-center justify-center rounded-xl border border-slate-200 text-slate-300 cursor-not-allowed">
                            ›
                        </span>
                        @endif
        </div>
        @endif

        @else
        {{-- EMPTY STATE --}}
        <div class="bg-white border border-slate-200 rounded-3xl p-10 md:p-14 text-center shadow-sm">
            <div class="w-20 h-20 mx-auto rounded-3xl bg-blue-50 text-blue-600 flex items-center justify-center mb-5">
                <svg xmlns="http://www.w3.org/2000/svg"
                    class="w-10 h-10"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                    stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M21 21l-4.35-4.35M10.5 18a7.5 7.5 0 100-15 7.5 7.5 0 000 15z" />
                </svg>
            </div>

            <h3 class="text-xl font-extrabold text-slate-900">
                Belum ada layanan tersedia
            </h3>

            <p class="text-sm text-slate-500 mt-3 max-w-md mx-auto leading-relaxed">
                Layanan akan muncul setelah freelancer membuat jasa dan data jasa sudah disetujui oleh admin.
            </p>

            @if (request('search') || request('kategori'))
            <a href="{{ route('dashboard') }}"
                class="inline-flex items-center justify-center mt-6 px-5 py-3 rounded-xl bg-blue-600 text-white text-sm font-bold hover:bg-blue-700 transition">
                Reset Pencarian
            </a>
            @endif
        </div>
        @endif
    </div>
</section>
@endsection