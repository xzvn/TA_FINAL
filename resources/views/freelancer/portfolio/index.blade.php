@extends('layouts.freelancer')

@section('title', 'Portofolio Saya - JasaKampus')
@section('page-title', 'Portofolio Saya')

@section('content')
<div class="mb-8">
    <h1 class="text-3xl font-bold text-slate-900">
        Portofolio Saya
    </h1>
    <p class="text-sm text-slate-500 mt-1">
        Daftar portofolio freelancer Anda.
    </p>
</div>

<div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6">
    @if ($portofolios->count() > 0)
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5">
        @foreach ($portofolios as $portofolio)
        <div class="border border-slate-200 rounded-xl p-5">
            <h3 class="font-bold text-slate-900">
                {{ $portofolio->judul ?? $portofolio->nama_portofolio ?? 'Portofolio #' . $portofolio->id }}
            </h3>

            <p class="text-sm text-slate-500 mt-2">
                {{ $portofolio->deskripsi ?? $portofolio->keterangan ?? 'Tidak ada deskripsi.' }}
            </p>
        </div>
        @endforeach
    </div>
    @else
    <div class="p-6 bg-slate-50 rounded-xl text-slate-500 text-sm">
        Belum ada portofolio.
    </div>
    @endif
</div>
@endsection