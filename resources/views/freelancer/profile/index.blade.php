@extends('layouts.freelancer')

@section('title', 'Profil Freelancer - JasaKampus')
@section('page-title', 'Profil Freelancer')

@section('content')
<div class="mb-8">
    <h1 class="text-3xl font-bold text-slate-900">
        Profil Freelancer
    </h1>
    <p class="text-sm text-slate-500 mt-1">
        Kelola informasi akun freelancer, status verifikasi, dan data portofolio Anda.
    </p>
</div>

@if (session('success'))
<div class="mb-6 p-4 rounded-xl bg-green-50 text-green-700 text-sm font-semibold">
    {{ session('success') }}
</div>
@endif

@if ($errors->any())
<div class="mb-6 p-4 rounded-xl bg-red-50 text-red-700 text-sm">
    <p class="font-bold mb-2">Ada data yang belum sesuai:</p>
    <ul class="list-disc ml-5 space-y-1">
        @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

<div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
    {{-- FORM PROFIL --}}
    <div class="xl:col-span-2 bg-white rounded-xl border border-slate-200 shadow-sm p-6">
        <h2 class="text-xl font-bold text-slate-900 mb-5">
            Informasi Akun
        </h2>

        <form action="{{ route('freelancer.profile.update') }}"
            method="POST"
            enctype="multipart/form-data"
            class="space-y-6">
            @csrf
            @method('PUT')

            <div class="flex items-center gap-5">
                <div id="previewAvatar"
                    class="relative w-24 h-24 max-w-24 max-h-24 min-w-24 min-h-24 rounded-full bg-blue-600 text-white flex items-center justify-center overflow-hidden text-3xl font-bold shrink-0">

                    @if ($user->foto_profil)
                    <img id="previewAvatarImage"
                        src="{{ \App\Services\CloudinaryService::mediaUrl($user->foto_profil) }}"
                        alt="Foto Profil"
                        class="absolute inset-0 w-full h-full object-cover rounded-full">
                    @else
                    <span id="previewAvatarInitial">
                        {{ strtoupper(substr($user->nama ?? $user->email, 0, 1)) }}
                    </span>

                    <img id="previewAvatarImage"
                        src=""
                        alt="Foto Profil"
                        class="hidden absolute inset-0 w-full h-full object-cover rounded-full">
                    @endif
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">
                        Foto Profil
                    </label>

                    <input
                        type="file"
                        id="foto_profil"
                        name="foto_profil"
                        accept="image/png,image/jpeg,image/jpg,image/webp"
                        class="block w-full text-sm text-slate-600 border border-slate-300 rounded-xl cursor-pointer bg-white focus:outline-none">

                    <p class="text-xs text-slate-400 mt-2">
                        Pilih gambar, lalu crop agar foto profil terlihat rapi.
                    </p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">
                        Nama
                    </label>
                    <input type="text"
                        name="nama"
                        value="{{ old('nama', $user->nama) }}"
                        class="w-full border border-slate-300 rounded-xl px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                </div>

                <input type="email"
                    value="{{ $user->email }}"
                    disabled
                    class="w-full border border-slate-200 bg-slate-100 text-slate-500 rounded-xl px-4 py-3 cursor-not-allowed">

                <p class="text-xs text-slate-400 mt-2">
                    Email freelancer tidak dapat diubah karena digunakan untuk verifikasi email kampus.
                </p>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">
                        No HP
                    </label>
                    <input type="text"
                        name="no_hp"
                        value="{{ old('no_hp', $user->no_hp) }}"
                        class="w-full border border-slate-300 rounded-xl px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">
                        Role
                    </label>
                    <input type="text"
                        value="{{ ucfirst($user->role) }}"
                        disabled
                        class="w-full border border-slate-200 bg-slate-100 text-slate-500 rounded-xl px-4 py-3">
                </div>
            </div>

            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-2">
                    Alamat
                </label>
                <textarea name="alamat"
                    rows="4"
                    class="w-full border border-slate-300 rounded-xl px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:outline-none">{{ old('alamat', $user->alamat) }}</textarea>
            </div>
            <x-theme-setting />
            <div class="flex justify-end gap-3">
                <a href="{{ route('dashboard') }}"
                    class="px-5 py-3 rounded-xl border border-slate-300 text-slate-700 font-bold hover:bg-slate-50">
                    Kembali
                </a>

                <button type="submit"
                    class="px-5 py-3 rounded-xl bg-blue-600 text-white font-bold hover:bg-blue-700">
                    Simpan Perubahan
                </button>
                
            </div>
            
        </form>
    </div>

    {{-- Modal Crop Foto Profil --}}
    <div id="cropModal"
        class="fixed inset-0 bg-black/60 z-[9999] hidden items-center justify-center px-4">

        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-200 flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-bold text-slate-900">
                        Crop Foto Profil
                    </h3>
                    <p class="text-sm text-slate-500">
                        Sesuaikan posisi foto agar tampil rapi sebagai avatar.
                    </p>
                </div>

                <button type="button"
                    id="cancelCrop"
                    class="w-9 h-9 rounded-lg border border-slate-200 text-slate-600 hover:bg-slate-100">
                    ×
                </button>
            </div>

            <div class="p-6">
                <div class="w-full max-h-[420px] bg-slate-100 rounded-xl overflow-hidden flex items-center justify-center">
                    <img id="cropImage" class="max-w-full max-h-[420px]" alt="Preview Crop">
                </div>

                <div class="mt-6 flex justify-end gap-3">
                    <button type="button"
                        id="cancelCropBottom"
                        class="px-5 py-3 rounded-xl border border-slate-300 text-slate-700 font-semibold hover:bg-slate-50">
                        Batal
                    </button>

                    <button type="button"
                        id="useCrop"
                        class="px-5 py-3 rounded-xl bg-blue-600 text-white font-semibold hover:bg-blue-700">
                        Gunakan Foto
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- STATUS VERIFIKASI --}}
    <div class="space-y-6">
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6">
            <h2 class="text-xl font-bold text-slate-900 mb-4">
                Status Verifikasi
            </h2>

            @php
            $status = $verifikasi?->status_verifikasi;
            $warnaStatus = match ($status) {
            'approved' => 'bg-green-100 text-green-700',
            'pending' => 'bg-yellow-100 text-yellow-700',
            'rejected' => 'bg-red-100 text-red-700',
            default => 'bg-slate-100 text-slate-700',
            };
            @endphp

            <span class="inline-block px-4 py-2 rounded-full text-sm font-bold {{ $warnaStatus }}">
                {{ $status ? ucfirst($status) : 'Belum Ada' }}
            </span>

            <div class="mt-5 text-sm text-slate-600 space-y-2">
                <p>
                    <span class="font-semibold">Nama:</span>
                    {{ $user->nama }}
                </p>
                <p>
                    <span class="font-semibold">Email:</span>
                    {{ $user->email }}
                </p>
                <p>
                    <span class="font-semibold">Bergabung:</span>
                    {{ $user->created_at->format('d M Y') }}
                </p>
            </div>

            @if ($status === 'rejected')
            <div class="mt-5 p-4 rounded-xl bg-red-50 text-red-700 text-sm">
                <p class="font-bold">Catatan Admin:</p>
                <p class="mt-1">
                    {{ $verifikasi?->catatan_admin ?? 'Tidak ada catatan.' }}
                </p>
            </div>
            @endif
        </div>

        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6">
            <h2 class="text-xl font-bold text-slate-900 mb-4">
                Portofolio
            </h2>

            <p class="text-3xl font-bold text-blue-600">
                {{ $portofolios->count() }}
            </p>
            <p class="text-sm text-slate-500 mt-1">
                Total portofolio yang terhubung dengan akun ini.
            </p>

            <a href="{{ route('freelancer.portfolio.index') }}"
                class="inline-block mt-5 px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-semibold hover:bg-blue-700">
                Lihat Portofolio
            </a>
        </div>
    </div>
    @push('scripts')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/cropperjs@1.6.2/dist/cropper.min.css">
    <script src="https://cdn.jsdelivr.net/npm/cropperjs@1.6.2/dist/cropper.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const input = document.getElementById('foto_profil');
            const modal = document.getElementById('cropModal');
            const image = document.getElementById('cropImage');
            const useCrop = document.getElementById('useCrop');
            const cancelCrop = document.getElementById('cancelCrop');
            const cancelCropBottom = document.getElementById('cancelCropBottom');

            let cropper = null;
            let originalFileName = 'foto-profil.jpg';

            if (!input || !modal || !image || !useCrop) {
                return;
            }

            function openModal() {
                modal.classList.remove('hidden');
                modal.classList.add('flex');
            }

            function closeModal() {
                modal.classList.add('hidden');
                modal.classList.remove('flex');

                if (cropper) {
                    cropper.destroy();
                    cropper = null;
                }

                image.src = '';
            }

            input.addEventListener('change', function(event) {
                const file = event.target.files[0];

                if (!file) {
                    return;
                }

                if (!file.type.startsWith('image/')) {
                    alert('File harus berupa gambar.');
                    input.value = '';
                    return;
                }

                originalFileName = file.name;

                const reader = new FileReader();

                reader.onload = function(e) {
                    image.src = e.target.result;
                    openModal();

                    if (cropper) {
                        cropper.destroy();
                    }

                    cropper = new Cropper(image, {
                        aspectRatio: 1,
                        viewMode: 1,
                        dragMode: 'move',
                        autoCropArea: 1,
                        responsive: true,
                        background: false,
                        cropBoxResizable: true,
                        cropBoxMovable: true,
                    });
                };

                reader.readAsDataURL(file);
            });

            useCrop.addEventListener('click', function() {
                if (!cropper) {
                    return;
                }

                cropper.getCroppedCanvas({
                    width: 500,
                    height: 500,
                    imageSmoothingEnabled: true,
                    imageSmoothingQuality: 'high',
                }).toBlob(function(blob) {
                    if (!blob) {
                        alert('Gagal crop foto.');
                        return;
                    }

                    const croppedFile = new File(
                        [blob],
                        originalFileName, {
                            type: 'image/jpeg'
                        }
                    );

                    const dataTransfer = new DataTransfer();
                    dataTransfer.items.add(croppedFile);

                    input.files = dataTransfer.files;

                    closeModal();
                }, 'image/jpeg', 0.9);
            });

            cancelCrop.addEventListener('click', function() {
                input.value = '';
                closeModal();
            });

            cancelCropBottom.addEventListener('click', function() {
                input.value = '';
                closeModal();
            });
        });
    </script>
    @endpush
</div>
@endsection