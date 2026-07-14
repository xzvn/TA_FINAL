@extends('layouts.freelancer')

@section('title', 'Jasa Saya - JasaKampus')
@section('page-title', 'Jasa Saya')

@section('content')
<div class="mb-8 flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
    <div>
        <h1 class="text-3xl font-bold text-slate-900">Jasa Saya</h1>
        <p class="text-sm text-slate-500 mt-1">
            Kelola daftar jasa dan thumbnail yang ditampilkan kepada customer.
        </p>
    </div>

    <a href="{{ route('freelancer.jasa.create') }}"
        class="inline-flex items-center justify-center px-5 py-3 bg-blue-600 text-white rounded-lg text-sm font-semibold hover:bg-blue-700">
        + Tambah Jasa
    </a>
</div>

@if (session('success'))
<div class="mb-6 px-5 py-4 bg-green-100 text-green-700 rounded-xl border border-green-200">
    {{ session('success') }}
</div>
@endif

@if ($errors->any())
<div class="mb-6 px-5 py-4 bg-red-50 text-red-700 rounded-xl border border-red-200">
    <p class="font-bold">Gambar belum dapat disimpan.</p>
    <ul class="mt-2 list-disc list-inside text-sm">
        @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

<div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
    <div class="px-6 py-5 border-b border-slate-200">
        <h3 class="font-semibold text-slate-800">Daftar Jasa</h3>
        <p class="text-sm text-slate-500">
            Gambar baru disimpan di Cloudinary agar tetap tersedia setelah Railway melakukan redeploy.
        </p>
    </div>

    @if ($jasa->count() > 0)
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6 p-6">
        @foreach ($jasa as $item)
        <div class="border border-slate-200 rounded-xl overflow-hidden hover:shadow-md transition bg-white flex flex-col">
            <div class="h-40 bg-slate-100 flex items-center justify-center overflow-hidden">
                @if ($item->thumbnail)
                <img src="{{ \App\Services\CloudinaryService::mediaUrl($item->thumbnail) }}"
                    alt="{{ $item->nama_jasa }}"
                    onerror="this.onerror=null;this.src='{{ asset('images/placeholder-service.svg') }}';"
                    class="w-full h-full object-cover">
                @else
                <img src="{{ asset('images/placeholder-service.svg') }}"
                    alt="Gambar jasa belum tersedia"
                    class="w-full h-full object-cover">
                @endif
            </div>

            <div class="p-5 flex-1 flex flex-col">
                <div class="flex items-center justify-between mb-3 gap-2">
                    <span class="px-3 py-1 bg-blue-100 text-blue-700 rounded-full text-xs font-bold">
                        {{ $item->kategori }}
                    </span>

                    @if ($item->status_jasa === 'active')
                    <span class="px-3 py-1 bg-green-100 text-green-700 rounded-full text-xs font-bold">AKTIF</span>
                    @elseif ($item->status_jasa === 'pending')
                    <span class="px-3 py-1 bg-yellow-100 text-yellow-700 rounded-full text-xs font-bold">PENDING</span>
                    @elseif ($item->status_jasa === 'inactive')
                    <span class="px-3 py-1 bg-slate-100 text-slate-700 rounded-full text-xs font-bold">NONAKTIF</span>
                    @else
                    <span class="px-3 py-1 bg-red-100 text-red-700 rounded-full text-xs font-bold">DITOLAK</span>
                    @endif
                </div>

                <h4 class="text-lg font-bold text-slate-900">{{ $item->nama_jasa }}</h4>
                <p class="text-sm text-slate-500 mt-2 line-clamp-3">{{ $item->deskripsi }}</p>

                <div class="mt-4 flex items-center justify-between">
                    <div>
                        <p class="text-xs text-slate-400">Mulai dari</p>
                        <p class="text-lg font-bold text-blue-600">
                            Rp {{ number_format($item->harga, 0, ',', '.') }}
                        </p>
                    </div>
                    <div class="text-right">
                        <p class="text-xs text-slate-400">Estimasi</p>
                        <p class="text-sm font-semibold text-slate-700">{{ $item->estimasi_pengerjaan }}</p>
                    </div>
                </div>

                <form action="{{ route('freelancer.jasa.thumbnail.update', $item) }}"
                    method="POST"
                    enctype="multipart/form-data"
                    class="mt-5 pt-4 border-t border-slate-200">
                    @csrf
                    @method('PATCH')

                    <label for="thumbnail-{{ $item->id }}" class="block text-xs font-bold text-slate-700 mb-2">
                        {{ $item->thumbnail ? 'Ganti thumbnail' : 'Tambahkan thumbnail' }}
                    </label>
                    <input id="thumbnail-{{ $item->id }}"
                        type="file"
                        name="thumbnail"
                        accept="image/jpeg,image/png,image/webp"
                        required
                        class="block w-full text-xs text-slate-600 file:mr-3 file:rounded-lg file:border-0 file:bg-blue-50 file:px-3 file:py-2 file:font-bold file:text-blue-700 hover:file:bg-blue-100">
                    <p class="mt-2 text-xs text-slate-400">JPG, PNG, atau WebP. Maksimal 5 MB.</p>
                    <button type="submit"
                        class="mt-3 w-full px-4 py-2.5 bg-slate-900 text-white rounded-lg text-sm font-bold hover:bg-slate-800">
                        Simpan Gambar
                    </button>
                </form>
            </div>
        </div>
        @endforeach
    </div>
    @else
    <div class="p-10 text-center">
        <div class="w-16 h-16 mx-auto rounded-full bg-blue-100 text-blue-600 flex items-center justify-center text-2xl mb-4">▣</div>
        <h3 class="text-lg font-bold text-slate-900">Belum ada jasa</h3>
        <p class="text-sm text-slate-500 mt-2">
            Mulai buat jasa pertama Anda agar customer dapat menemukan layanan Anda.
        </p>
        <a href="{{ route('freelancer.jasa.create') }}"
            class="inline-block mt-5 px-5 py-3 bg-blue-600 text-white rounded-lg font-semibold hover:bg-blue-700">
            Buat Jasa Pertama
        </a>
    </div>
    @endif
</div>
@endsection
