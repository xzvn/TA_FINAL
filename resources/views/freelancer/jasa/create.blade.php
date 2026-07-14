@extends('layouts.freelancer')

@section('title', 'Tambah Jasa - JasaKampus')
@section('page-title', 'Tambah Jasa')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-3xl font-bold text-slate-900">Tambah Jasa Baru</h1>
            <p class="text-sm text-slate-500 mt-1">
                Buat layanan jasa yang akan ditampilkan kepada customer.
            </p>
        </div>

        <a href="{{ route('freelancer.jasa.index') }}"
            class="inline-flex items-center justify-center px-5 py-3 bg-white border border-slate-200 rounded-lg text-sm font-semibold hover:bg-slate-50">
            Kembali
        </a>
    </div>

    <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6">
        <form method="POST" action="{{ route('freelancer.jasa.store') }}" enctype="multipart/form-data" class="space-y-6">
            @csrf

            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-2">Nama Jasa</label>
                <input type="text"
                    name="nama_jasa"
                    value="{{ old('nama_jasa') }}"
                    class="w-full rounded-lg border-slate-300 focus:border-blue-500 focus:ring-blue-500"
                    placeholder="Contoh: Desain Logo Profesional"
                    required>
                @error('nama_jasa')
                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-2">Kategori</label>
                <select name="kategori" id="kategori"
                    class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm text-slate-700 focus:border-blue-500 focus:ring-blue-500"
                    required>
                    <option value="">Pilih Kategori</option>
                    @foreach (config('jasakampus.kategori_jasa') as $kategori)
                    <option value="{{ $kategori }}" {{ old('kategori') === $kategori ? 'selected' : '' }}>
                        {{ $kategori }}
                    </option>
                    @endforeach
                </select>
                @error('kategori')
                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-2">Deskripsi Jasa</label>
                <textarea name="deskripsi"
                    rows="5"
                    class="w-full rounded-lg border-slate-300 focus:border-blue-500 focus:ring-blue-500"
                    placeholder="Jelaskan detail layanan, benefit, dan batasan pekerjaan."
                    required>{{ old('deskripsi') }}</textarea>
                @error('deskripsi')
                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Harga</label>
                    <input type="number"
                        name="harga"
                        value="{{ old('harga') }}"
                        class="w-full rounded-lg border-slate-300 focus:border-blue-500 focus:ring-blue-500"
                        placeholder="Contoh: 50000"
                        min="1000"
                        required>
                    @error('harga')
                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Estimasi Pengerjaan</label>
                    <input type="text"
                        name="estimasi_pengerjaan"
                        value="{{ old('estimasi_pengerjaan') }}"
                        class="w-full rounded-lg border-slate-300 focus:border-blue-500 focus:ring-blue-500"
                        placeholder="Contoh: 3 hari"
                        required>
                    @error('estimasi_pengerjaan')
                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-2">Thumbnail Jasa</label>
                <input type="file"
                    name="thumbnail"
                    class="w-full rounded-lg border border-slate-300 p-2"
                    accept="image/jpeg,image/png,image/webp">
                <p class="text-xs text-slate-500 mt-1">
                    JPG, PNG, atau WebP. Maksimal 5 MB. Gambar akan disimpan permanen di Cloudinary.
                </p>
                @error('thumbnail')
                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex justify-end gap-3 pt-4">
                <a href="{{ route('freelancer.jasa.index') }}"
                    class="px-5 py-3 bg-slate-100 text-slate-700 rounded-lg font-semibold hover:bg-slate-200">
                    Batal
                </a>
                <button type="submit"
                    class="px-6 py-3 bg-blue-600 text-white rounded-lg font-semibold hover:bg-blue-700">
                    Simpan Jasa
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
