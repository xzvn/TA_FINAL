@extends('layouts.freelancer')

@section('title', 'Portofolio Saya - JasaKampus')
@section('page-title', 'Portofolio Saya')

@section('content')
<div class="mb-8">
    <h1 class="text-3xl font-bold text-slate-900">
        Portofolio Saya
    </h1>

    <p class="mt-1 text-sm text-slate-500">
        Daftar portofolio freelancer Anda.
    </p>
</div>

<div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
    @if ($portofolios->isNotEmpty())
    <div class="grid grid-cols-1 gap-6 md:grid-cols-2 xl:grid-cols-3">
        @foreach ($portofolios as $portofolio)
        @php
        /*
        * Mendukung beberapa kemungkinan nama kolom.
        * Setelah mengetahui nama kolom yang sebenarnya,
        * bagian ini dapat disederhanakan.
        */
        $portfolioFile =
        $portofolio->file_portofolio
        ?? $portofolio->portfolio_file
        ?? $portofolio->file_path
        ?? $portofolio->file_url
        ?? $portofolio->url
        ?? $portofolio->dokumen
        ?? $portofolio->gambar
        ?? null;

        $portfolioUrl = null;

        if (filled($portfolioFile)) {
        $portfolioUrl = \Illuminate\Support\Str::startsWith(
        $portfolioFile,
        ['http://', 'https://']
        )
        ? $portfolioFile
        : \App\Services\CloudinaryService::mediaUrl(
        $portfolioFile
        );
        }

        $mimeType = strtolower(
        (string) (
        $portofolio->mime_type
        ?? $portofolio->file_type
        ?? ''
        )
        );

        $urlPath = $portfolioUrl
        ? parse_url($portfolioUrl, PHP_URL_PATH)
        : '';

        $extension = strtolower(
        pathinfo(
        $urlPath ?: '',
        PATHINFO_EXTENSION
        )
        );

        $isPdf =
        str_contains($mimeType, 'pdf')
        || $extension === 'pdf';

        $isImage =
        str_starts_with($mimeType, 'image/')
        || in_array(
        $extension,
        [
        'jpg',
        'jpeg',
        'png',
        'webp',
        'gif',
        'avif',
        ],
        true
        );

        $title =
        $portofolio->judul
        ?? $portofolio->nama_portofolio
        ?? 'Portofolio #' . $portofolio->id;

        $description =
        $portofolio->deskripsi
        ?? $portofolio->keterangan
        ?? 'Tidak ada deskripsi.';
        @endphp

        <article class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            {{-- PREVIEW FILE --}}
            @if ($portfolioUrl)
            @if ($isImage)
            <a
                href="{{ $portfolioUrl }}"
                target="_blank"
                rel="noopener noreferrer"
                class="block overflow-hidden bg-slate-100">

                <img
                    src="{{ $portfolioUrl }}"
                    alt="{{ $title }}"
                    loading="lazy"
                    class="h-56 w-full object-cover transition duration-300 hover:scale-105"
                    onerror="this.closest('a').classList.add('hidden');">
            </a>

            @elseif ($isPdf)
            <div class="flex h-56 flex-col items-center justify-center bg-red-50 px-6 text-center">
                <div class="flex h-16 w-16 items-center justify-center rounded-2xl bg-red-100 text-3xl">
                    📄
                </div>

                <p class="mt-3 text-sm font-bold text-red-700">
                    Dokumen PDF
                </p>

                <p class="mt-1 text-xs text-red-500">
                    Klik tombol di bawah untuk membuka dokumen.
                </p>
            </div>

            @else
            <div class="flex h-56 flex-col items-center justify-center bg-slate-100 px-6 text-center">
                <div class="flex h-16 w-16 items-center justify-center rounded-2xl bg-slate-200 text-3xl">
                    📎
                </div>

                <p class="mt-3 text-sm font-bold text-slate-700">
                    File Portofolio
                </p>

                <p class="mt-1 text-xs text-slate-500">
                    Preview tidak tersedia untuk format ini.
                </p>
            </div>
            @endif
            @else
            <div class="flex h-56 flex-col items-center justify-center bg-slate-100 px-6 text-center">
                <div class="flex h-16 w-16 items-center justify-center rounded-2xl bg-slate-200 text-3xl">
                    🖼️
                </div>

                <p class="mt-3 text-sm font-bold text-slate-700">
                    File tidak tersedia
                </p>

                <p class="mt-1 text-xs text-slate-500">
                    Data file portofolio belum tersimpan.
                </p>
            </div>
            @endif

            {{-- INFORMASI PORTOFOLIO --}}
            <div class="p-5">
                <h3 class="text-lg font-bold text-slate-900">
                    {{ $title }}
                </h3>

                <p class="mt-2 text-sm leading-relaxed text-slate-500">
                    {{ $description }}
                </p>

                @if ($portofolio->created_at)
                <p class="mt-3 text-xs text-slate-400">
                    Ditambahkan
                    {{ $portofolio->created_at->format('d M Y, H:i') }}
                </p>
                @endif

                @if ($portfolioUrl)
                <a
                    href="{{ $portfolioUrl }}"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="mt-5 inline-flex w-full items-center justify-center rounded-xl bg-blue-600 px-5 py-3 text-sm font-bold text-white transition hover:bg-blue-700">

                    Lihat Portofolio
                </a>
                @endif
            </div>
        </article>
        @endforeach
    </div>
    @else
    <div class="rounded-xl bg-slate-50 p-8 text-center">
        <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-slate-200 text-3xl">
            🗂️
        </div>

        <p class="mt-4 font-bold text-slate-700">
            Belum ada portofolio
        </p>

        <p class="mt-1 text-sm text-slate-500">
            Portofolio yang Anda tambahkan akan muncul di sini.
        </p>
    </div>
    @endif
</div>
@endsection