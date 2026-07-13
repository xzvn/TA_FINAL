@extends('layouts.freelancer')

@section('title', 'Jasa Saya - JasaKampus')
@section('page-title', 'Jasa Saya')

@section('content') <div class="mb-8 flex items-start justify-between">
    <div>
        <h1 class="text-3xl font-bold text-slate-900">
            Jasa Saya </h1>
        <p class="text-sm text-slate-500 mt-1">
            Kelola daftar jasa yang Anda tawarkan kepada customer. </p>
    </div>

    ```
    <a href="{{ route('freelancer.jasa.create') }}"
        class="px-5 py-3 bg-blue-600 text-white rounded-lg text-sm font-semibold hover:bg-blue-700">
        + Tambah Jasa
    </a>
</div>

@if (session('success'))
<div class="mb-6 px-5 py-4 bg-green-100 text-green-700 rounded-xl border border-green-200">
    {{ session('success') }}
</div>
@endif

<div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
    <div class="px-6 py-5 border-b border-slate-200">
        <h3 class="font-semibold text-slate-800">
            Daftar Jasa
        </h3>
        <p class="text-sm text-slate-500">
            Semua jasa yang sudah Anda buat akan tampil di sini.
        </p>
    </div>

    @if ($jasa->count() > 0)
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6 p-6">
        @foreach ($jasa as $item)
        <div class="border border-slate-200 rounded-xl overflow-hidden hover:shadow-md transition bg-white">
            <div class="h-40 bg-slate-100 flex items-center justify-center">
                @if ($item->thumbnail)
                <img src="{{ \App\Services\CloudinaryService::mediaUrl($item->thumbnail) }}"
                    alt="{{ $item->nama_jasa }}"
                    class="w-full h-full object-cover">
                @else
                <span class="text-slate-400 text-sm">
                    Belum ada thumbnail
                </span>
                @endif
            </div>

            <div class="p-5">
                <div class="flex items-center justify-between mb-3">
                    <span class="px-3 py-1 bg-blue-100 text-blue-700 rounded-full text-xs font-bold">
                        {{ $item->kategori }}
                    </span>

                    @if ($item->status_jasa === 'active')
                    <span class="px-3 py-1 bg-green-100 text-green-700 rounded-full text-xs font-bold">
                        AKTIF
                    </span>
                    @elseif ($item->status_jasa === 'pending')
                    <span class="px-3 py-1 bg-yellow-100 text-yellow-700 rounded-full text-xs font-bold">
                        PENDING
                    </span>
                    @elseif ($item->status_jasa === 'inactive')
                    <span class="px-3 py-1 bg-slate-100 text-slate-700 rounded-full text-xs font-bold">
                        NONAKTIF
                    </span>
                    @else
                    <span class="px-3 py-1 bg-red-100 text-red-700 rounded-full text-xs font-bold">
                        DITOLAK
                    </span>
                    @endif
                </div>

                <h4 class="text-lg font-bold text-slate-900">
                    {{ $item->nama_jasa }}
                </h4>

                <p class="text-sm text-slate-500 mt-2 line-clamp-3">
                    {{ $item->deskripsi }}
                </p>

                <div class="mt-4 flex items-center justify-between">
                    <div>
                        <p class="text-xs text-slate-400">
                            Mulai dari
                        </p>
                        <p class="text-lg font-bold text-blue-600">
                            Rp {{ number_format($item->harga, 0, ',', '.') }}
                        </p>
                    </div>

                    <div class="text-right">
                        <p class="text-xs text-slate-400">
                            Estimasi
                        </p>
                        <p class="text-sm font-semibold text-slate-700">
                            {{ $item->estimasi_pengerjaan }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @else
    <div class="p-10 text-center">
        <div class="w-16 h-16 mx-auto rounded-full bg-blue-100 text-blue-600 flex items-center justify-center text-2xl mb-4">
            ▣
        </div>

        <h3 class="text-lg font-bold text-slate-900">
            Belum ada jasa
        </h3>

        <p class="text-sm text-slate-500 mt-2">
            Mulai buat jasa pertama Anda agar customer bisa menemukan layanan Anda.
        </p>

        <a href="{{ route('freelancer.jasa.create') }}"
            class="inline-block mt-5 px-5 py-3 bg-blue-600 text-white rounded-lg font-semibold hover:bg-blue-700">
            Buat Jasa Pertama
        </a>
    </div>
    @endif
</div>
```

@endsection