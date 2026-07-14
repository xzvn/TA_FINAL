@extends('layouts.customer')

@section('title', 'Favorite Saya - JasaKampus')

@section('content')
<section class="px-6 py-6">
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-slate-900">
            Favorite Saya
        </h1>
        <p class="text-sm text-slate-500 mt-1">
            Daftar jasa yang kamu simpan sebagai favorite.
        </p>
    </div>

    @if ($favorites->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
            @foreach ($favorites as $favorite)
                @php
                    $item = $favorite->jasa;
                @endphp

                @if ($item)
                    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden hover:shadow-md transition flex flex-col">
                        <div class="relative h-48 bg-slate-100">
                            @if ($item->thumbnail)
                                <img src="{{ \App\Services\CloudinaryService::mediaUrl($item->thumbnail) }}"
                                     alt="{{ $item->nama_jasa }}"
                                     onerror="this.onerror=null;this.src='{{ asset('images/placeholder-service.svg') }}';"
                                     class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full flex items-center justify-center bg-slate-100 text-5xl">
                                    🖼️
                                </div>
                            @endif

                            <form action="{{ route('customer.favorite.toggle', $item->id) }}"
                                  method="POST"
                                  class="absolute top-3 right-3">
                                @csrf

                                <button type="submit"
                                        class="w-9 h-9 bg-white/90 rounded-full flex items-center justify-center shadow-sm text-red-500">
                                    ♥
                                </button>
                            </form>
                        </div>

                        <div class="p-5 flex flex-col flex-1">
                            <div class="flex items-start justify-between gap-3 mb-3">
                                <div class="flex items-center gap-2 min-w-0">
                                    <div class="w-8 h-8 rounded-full bg-slate-800 text-white flex items-center justify-center text-xs font-bold shrink-0">
                                        {{ strtoupper(substr($item->freelancer->nama ?? 'F', 0, 1)) }}
                                    </div>

                                    <span class="text-sm font-semibold text-slate-700 truncate">
                                        {{ $item->freelancer->nama ?? 'Freelancer' }}
                                    </span>
                                </div>
                            </div>

                            <h3 class="font-bold text-slate-900 text-lg leading-snug line-clamp-2 min-h-[56px]">
                                {{ $item->nama_jasa }}
                            </h3>

                            <div class="flex flex-wrap gap-2 mt-4">
                                <span class="px-3 py-1.5 bg-blue-50 text-blue-700 rounded-lg text-xs font-bold uppercase">
                                    {{ $item->kategori }}
                                </span>

                                <span class="px-3 py-1.5 bg-purple-50 text-purple-700 rounded-lg text-xs font-bold uppercase">
                                    Terverifikasi
                                </span>
                            </div>

                            <div class="mt-auto pt-5 border-t border-slate-100">
                                <div class="flex items-end justify-between gap-4">
                                    <div>
                                        <p class="text-xs text-slate-500">
                                            Mulai dari
                                        </p>

                                        <p class="text-sm text-slate-500 mt-1">
                                            {{ $item->estimasi_pengerjaan }}
                                        </p>
                                    </div>

                                    <div class="text-right shrink-0">
                                        <p class="text-xl font-bold text-blue-600">
                                            Rp {{ number_format($item->harga, 0, ',', '.') }}
                                        </p>

                                        <a href="{{ route('customer.jasa.show', $item->id) }}"
                                           class="inline-block mt-3 px-5 py-2 bg-blue-600 text-white rounded-lg text-sm font-semibold hover:bg-blue-700">
                                            Detail
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            @endforeach
        </div>
    @else
        <div class="bg-white border border-slate-200 rounded-xl p-12 text-center">
            <div class="w-16 h-16 mx-auto rounded-full bg-red-100 text-red-500 flex items-center justify-center text-2xl mb-4">
                ♥
            </div>

            <h3 class="text-lg font-bold text-slate-900">
                Belum ada jasa favorite
            </h3>

            <p class="text-sm text-slate-500 mt-2">
                Klik icon love pada card jasa untuk menyimpan jasa ke daftar favorite.
            </p>

            <a href="{{ route('dashboard') }}"
               class="inline-block mt-5 px-5 py-3 bg-blue-600 text-white rounded-xl font-semibold hover:bg-blue-700">
                Cari Jasa
            </a>
        </div>
    @endif
</section>
@endsection