@extends('layouts.freelancer')

@section('title', 'Proyek Page - JasaKampus')
@section('page-title', 'Proyek')

@section('content')
@php
$progresses = $pesanan->progressPekerjaans->sortByDesc('created_at');
$progressTerakhir = $progresses->first();
$progressSekarang = $pesanan->progressPekerjaans->max('persentase_progress') ?? 0;
$sisaRevisi = ($pesanan->batas_revisi ?? 3) - ($pesanan->jumlah_revisi ?? 0);

$revisis = $pesanan->revisis ?? collect();

$revisiTerbuka = $revisis
->whereIn('status_revisi', ['diajukan', 'diproses'])
->sortByDesc('created_at')
->first();

$revisiTerakhir = $revisis
->sortByDesc('created_at')
->first();
@endphp

<div class="max-w-7xl mx-auto">
    <div class="mb-6">
        <p class="text-xs text-slate-500">
            Dashboard / Proyek / {{ $pesanan->jasa->nama_jasa ?? 'Proyek Aktif' }}
        </p>

        <div class="mt-2 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div>
                <h1 class="text-2xl md:text-3xl font-bold text-slate-900">
                    Proyek Aktif
                </h1>
                <p class="text-sm text-slate-500 mt-1">
                    Kelola update progress, revisi, dan komunikasi proyek dari satu halaman.
                </p>
            </div>

            <div class="flex flex-wrap gap-2">
                <span class="px-3 py-1 bg-blue-100 text-blue-700 rounded-full text-xs font-bold">
                    {{ $pesanan->status_pesanan === 'diproses' ? 'Sedang Berjalan' : strtoupper(str_replace('_', ' ', $pesanan->status_pesanan)) }}
                </span>

                <span class="px-3 py-1 bg-red-100 text-red-600 rounded-full text-xs font-bold">
                    {{ max(0, $sisaRevisi) }} Hari Tersisa
                </span>
            </div>
        </div>
    </div>

    @if (session('success'))
    <div class="mb-6 px-5 py-4 bg-green-100 text-green-700 rounded-xl border border-green-200">
        {{ session('success') }}
    </div>
    @endif

    <div class="grid grid-cols-1 xl:grid-cols-12 gap-6">

        {{-- LEFT CONTENT --}}
        <div class="xl:col-span-8 space-y-6">

            {{-- PROJECT HEADER --}}
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
                <div class="flex flex-col lg:flex-row lg:items-start justify-between gap-5">
                    <div>
                        <p class="text-xs text-slate-500">Proyek Aktif</p>
                        <h2 class="text-xl font-bold text-slate-900 mt-1">
                            {{ $pesanan->jasa->nama_jasa ?? 'Proyek' }}
                        </h2>

                        <p class="text-sm text-slate-500 mt-2">
                            Customer:
                            <span class="font-semibold text-slate-800">
                                {{ $pesanan->customer->nama ?? '-' }}
                            </span>
                        </p>
                    </div>

                    <div class="flex flex-wrap gap-2">
                        <span class="px-3 py-1 bg-blue-100 text-blue-700 rounded-full text-xs font-bold">
                            {{ $progressSekarang }}% Progress
                        </span>

                        <span class="px-3 py-1 bg-orange-100 text-orange-700 rounded-full text-xs font-bold">
                            {{ ($pesanan->jumlah_revisi ?? 0) }} Revisi Aktif
                        </span>
                    </div>
                </div>
            </div>

            {{-- PROGRESS CARD --}}
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h3 class="font-bold text-slate-900">
                            Progress Kerja
                        </h3>
                        <p class="text-sm text-slate-500 mt-1">
                            Update progress secara berkala agar customer dapat memantau perkembangan proyek.
                        </p>
                    </div>

                    <span class="text-2xl font-bold text-blue-600">
                        {{ $progressSekarang }}%
                    </span>
                </div>

                <div class="w-full h-3 bg-slate-100 rounded-full overflow-hidden">
                    <div class="h-3 bg-blue-600 rounded-full transition-all duration-300"
                        style="width: {{ $progressSekarang }}%;">
                    </div>
                </div>

                <div class="grid grid-cols-5 gap-3 mt-6 text-center">
                    <div>
                        <div class="w-8 h-8 mx-auto rounded-full bg-blue-600 text-white flex items-center justify-center text-xs font-bold">✓</div>
                        <p class="text-[11px] text-slate-500 mt-2">Proyek Dimulai</p>
                    </div>

                    <div>
                        <div class="w-8 h-8 mx-auto rounded-full {{ $progressSekarang >= 25 ? 'bg-blue-600 text-white' : 'bg-slate-100 text-slate-400' }} flex items-center justify-center text-xs font-bold">✓</div>
                        <p class="text-[11px] text-slate-500 mt-2">Pengerjaan Awal</p>
                    </div>

                    <div>
                        <div class="w-8 h-8 mx-auto rounded-full {{ $progressSekarang >= 50 ? 'bg-blue-600 text-white' : 'bg-slate-100 text-slate-400' }} flex items-center justify-center text-xs font-bold">✓</div>
                        <p class="text-[11px] text-slate-500 mt-2">Revisi Awal</p>
                    </div>

                    <div>
                        <div class="w-8 h-8 mx-auto rounded-full {{ $progressSekarang >= 75 ? 'bg-blue-600 text-white' : 'bg-slate-100 text-slate-400' }} flex items-center justify-center text-xs font-bold">✓</div>
                        <p class="text-[11px] text-slate-500 mt-2">Sedang Review</p>
                    </div>

                    <div>
                        <div class="w-8 h-8 mx-auto rounded-full {{ $progressSekarang >= 100 ? 'bg-blue-600 text-white' : 'bg-slate-100 text-slate-400' }} flex items-center justify-center text-xs font-bold">✓</div>
                        <p class="text-[11px] text-slate-500 mt-2">Pekerjaan Selesai</p>
                    </div>
                </div>
            </div>


                {{-- FORM UPDATE PROGRESS --}}
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
                <h3 class="font-bold text-slate-900">
                    Buat Update Progress
                </h3>

                <p class="text-sm text-slate-500 mt-1">
                    Isi detail perkembangan terbaru proyek.
                </p>

                {{-- DEBUG SEMENTARA --}}
                <p class="text-xs text-slate-400 mt-2">
                    Status: {{ $pesanan->status_pesanan }} | Progress: {{ $progressSekarang }}%
                </p>

                @if (
                in_array($pesanan->status_pesanan, ['dibayar', 'diproses', 'revisi'])
                && $progressSekarang < 100
                    )
                    <form method="POST"
                    action="{{ route('freelancer.progress.store', $pesanan->id) }}"
                    enctype="multipart/form-data"
                    class="mt-6 space-y-6">
                    @csrf

                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">
                            Judul Update
                        </label>

                        <input type="text"
                            name="judul_progress"
                            value="{{ old('judul_progress') }}"
                            class="w-full rounded-xl border-slate-300 focus:border-blue-500 focus:ring-blue-500"
                            placeholder="Contoh: Desain antarmuka beranda selesai"
                            required>

                        @error('judul_progress')
                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-3">
                            Persentase Selesai
                        </label>

                        <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                            @foreach ([25, 50, 75, 100] as $value)
                            <label class="cursor-pointer">
                                <input type="radio"
                                    name="persentase_progress"
                                    value="{{ $value }}"
                                    class="peer hidden"
                                    {{ old('persentase_progress') == $value ? 'checked' : '' }}
                                    required>

                                <div class="rounded-xl border border-slate-300 px-4 py-3 text-center text-sm font-semibold
                                peer-checked:bg-blue-600 peer-checked:text-white peer-checked:border-blue-600
                                hover:border-blue-400">
                                    {{ $value }}%
                                </div>
                            </label>
                            @endforeach
                        </div>

                        @error('persentase_progress')
                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">
                            Deskripsi Status Saat Ini
                        </label>

                        <textarea name="deskripsi_progress"
                            rows="5"
                            class="w-full rounded-xl border-slate-300 focus:border-blue-500 focus:ring-blue-500"
                            placeholder="Jelaskan apa yang sudah dikerjakan pada update ini..."
                            required>{{ old('deskripsi_progress') }}</textarea>

                        @error('deskripsi_progress')
                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">
                            Lampiran File
                        </label>

                        <input type="file"
                            name="file_progress"
                            class="w-full rounded-xl border border-slate-300 bg-white p-3 text-sm">

                        @error('file_progress')
                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <button type="submit"
                        class="w-full px-5 py-3 bg-blue-600 text-white rounded-xl font-semibold hover:bg-blue-700">
                        Publikasikan Update
                    </button>
                    </form>

                    @elseif (
                    in_array($pesanan->status_pesanan, ['dibayar', 'diproses', 'revisi'])
                    && $progressSekarang >= 100
                    )
                    <div class="mt-6 p-6 bg-green-50 border border-green-200 rounded-2xl text-center">
                        <div class="text-4xl mb-3">✅</div>

                        <h4 class="font-bold text-green-700">
                            Progress sudah mencapai 100%
                        </h4>

                        @if ($pesanan->status_pesanan === 'revisi')
                        <p class="text-sm text-green-600 mt-2">
                            Customer meminta revisi pada progress 100%. Silakan langsung unggah hasil akhir yang sudah diperbaiki.
                        </p>
                        @else
                        <p class="text-sm text-green-600 mt-2">
                            Progress pekerjaan sudah selesai. Silakan serahkan hasil akhir kepada customer.
                        </p>
                        @endif

                        <a href="{{ route('freelancer.hasil.create', $pesanan->id) }}"
                            class="inline-block mt-5 px-6 py-3 bg-green-600 text-white rounded-xl font-bold hover:bg-green-700">
                            Serahkan Hasil Akhir
                        </a>
                    </div>

                    @else
                    <div class="mt-6 bg-slate-50 rounded-2xl border border-slate-200 p-6">
                        <h3 class="font-bold text-slate-900">
                            Progress Tidak Dapat Diunggah
                        </h3>

                        <p class="text-sm text-slate-500 mt-2">
                            Status pesanan saat ini adalah
                            <span class="font-bold">{{ strtoupper(str_replace('_', ' ', $pesanan->status_pesanan)) }}</span>,
                            sehingga progress belum dapat diunggah.
                        </p>
                    </div>
                    @endif
                
             </div>


        {{-- HISTORY --}}
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
            <div class="flex items-center justify-between mb-5">
                <div>
                    <h3 class="font-bold text-slate-900">
                        Riwayat Progress
                    </h3>
                    <p class="text-sm text-slate-500 mt-1">
                        Semua update yang pernah diunggah.
                    </p>
                </div>
            </div>

            @if ($progresses->count() > 0)
            <div class="space-y-4">
                @foreach ($progresses as $progress)
                <div class="border border-slate-200 rounded-2xl p-5 bg-slate-50">
                    <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-3">
                        <div>
                            <h4 class="font-semibold text-slate-900">
                                {{ $progress->judul_progress }}
                            </h4>
                            <p class="text-xs text-slate-500 mt-1">
                                Update #{{ $loop->iteration }} • {{ $progress->created_at->format('d M Y H:i') }}
                            </p>
                        </div>

                        <span class="px-3 py-1 bg-blue-100 text-blue-700 rounded-full text-xs font-bold">
                            {{ $progress->persentase_progress }}% Selesai
                        </span>
                    </div>

                    <p class="text-sm text-slate-600 mt-3 leading-relaxed">
                        {{ $progress->deskripsi_progress }}
                    </p>

                    @if ($progress->file_progress)
                    <a href="{{ \App\Services\CloudinaryService::mediaUrl($progress->file_progress) }}"
                        target="_blank"
                        class="inline-block mt-4 text-sm font-semibold text-blue-600 hover:underline">
                        Lihat File Lampiran
                    </a>
                    @endif
                </div>
                @endforeach
            </div>
            @else
            <div class="p-8 bg-slate-50 rounded-2xl border border-slate-200 text-center">
                <div class="text-3xl mb-3">📝</div>
                <h4 class="font-bold text-slate-900">
                    Belum ada riwayat progress
                </h4>
                <p class="text-sm text-slate-500 mt-2">
                    Update pertama kamu akan muncul di sini.
                </p>
            </div>
            @endif
        </div>
    </div>

    {{-- RIGHT SIDEBAR --}}
    <div class="xl:col-span-4 space-y-6">

        {{-- REVISION CARD --}}
        @php
        $revisiTerakhir = $pesanan->revisis->sortByDesc('created_at')->first();
        @endphp
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
            <div class="flex items-center justify-between">
                <h3 class="font-bold text-slate-900">
                    Revisi
                </h3>

                <span class="px-3 py-1 bg-purple-100 text-purple-700 rounded-full text-xs font-bold">
                    {{ $pesanan->jumlah_revisi ?? 0 }} / {{ $pesanan->batas_revisi ?? 3 }}
                </span>
            </div>

            @if ($revisiTerbuka)
            <div class="mt-5 p-4 bg-orange-50 border border-orange-200 rounded-xl">
                <div class="flex items-center justify-between gap-3">
                    <p class="text-sm font-bold text-orange-700">
                        Revisi Aktif
                    </p>

                    <span class="px-3 py-1 rounded-full text-xs font-bold bg-orange-100 text-orange-700">
                        {{ strtoupper($revisiTerbuka->status_revisi) }}
                    </span>
                </div>

                <p class="text-sm font-bold text-slate-900 mt-3">
                    @if (($revisiTerbuka->jenis_revisi ?? 'hasil_akhir') === 'progress')
                    Revisi Progress {{ $revisiTerbuka->persentase_progress }}%
                    @else
                    Revisi Hasil Akhir
                    @endif
                </p>

                <p class="text-sm text-orange-700 mt-2 leading-relaxed">
                    {{ $revisiTerbuka->catatan_revisi }}
                </p>

                <p class="text-xs text-orange-500 mt-3">
                    Diajukan: {{ $revisiTerbuka->created_at->format('d M Y H:i') }}
                </p>
            </div>

            <div class="mt-4 p-4 bg-blue-50 border border-blue-200 rounded-xl">
                <p class="text-sm font-bold text-blue-700">
                    Yang harus dilakukan
                </p>

                @if (($revisiTerbuka->jenis_revisi ?? 'hasil_akhir') === 'progress')
                <p class="text-sm text-blue-600 mt-2">
                    Perbaiki bagian yang diminta customer, lalu upload progress terbaru.
                </p>
                @else
                <p class="text-sm text-blue-600 mt-2">
                    Perbaiki hasil akhir, lalu unggah ulang file hasil akhir.
                </p>
                @endif
            </div>
            @else
            <div class="mt-5 p-4 bg-purple-50 border border-purple-200 rounded-xl">
                <p class="text-sm font-semibold text-purple-700">
                    Tidak ada revisi aktif
                </p>

                <p class="text-sm text-purple-600 mt-2">
                    Belum ada permintaan revisi yang perlu dikerjakan saat ini.
                </p>
            </div>
            @endif

            @if ($revisis->count() > 0)
            <div class="mt-5 border-t border-slate-200 pt-5">
                <p class="text-sm font-bold text-slate-900 mb-3">
                    Riwayat Revisi
                </p>

                <div class="space-y-3">
                    @foreach ($revisis->sortByDesc('created_at')->take(3) as $revisi)
                    <div class="p-3 bg-slate-50 border border-slate-200 rounded-xl">
                        <div class="flex items-center justify-between gap-2">
                            <p class="text-xs font-bold text-slate-700">
                                @if (($revisi->jenis_revisi ?? 'hasil_akhir') === 'progress')
                                Progress {{ $revisi->persentase_progress }}%
                                @else
                                Hasil Akhir
                                @endif
                            </p>

                            <span class="px-2 py-1 rounded-full text-[10px] font-bold
                                @if ($revisi->status_revisi === 'diajukan') bg-orange-100 text-orange-700
                                @elseif ($revisi->status_revisi === 'diproses') bg-blue-100 text-blue-700
                                @else bg-green-100 text-green-700
                                @endif">
                                {{ strtoupper($revisi->status_revisi) }}
                            </span>
                        </div>

                        <p class="text-xs text-slate-500 mt-2 line-clamp-2">
                            {{ $revisi->catatan_revisi }}
                        </p>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>

        {{-- CUSTOMER CHAT CARD --}}
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-full bg-blue-600 text-white flex items-center justify-center font-bold">
                    {{ strtoupper(substr($pesanan->customer->nama ?? 'C', 0, 1)) }}
                </div>

                <div>
                    <h3 class="font-bold text-slate-900">
                        {{ $pesanan->customer->nama ?? 'Customer' }}
                    </h3>
                    <p class="text-xs text-slate-500">
                        Customer proyek ini
                    </p>
                </div>
            </div>

            <p class="text-sm text-slate-600 mt-4 leading-relaxed">
                Gunakan room chat untuk komunikasi lebih lanjut terkait revisi, kebutuhan tambahan, atau klarifikasi pekerjaan.
            </p>

            <a href="{{ route('freelancer.chat.index') }}"
                class="block text-center mt-5 w-full px-5 py-3 bg-blue-600 text-white rounded-xl font-semibold hover:bg-blue-700">
                Buka Room Chat
            </a>
        </div>

        <!-- {{-- FINAL SUBMIT --}}
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
            <h3 class="font-bold text-slate-900">
                Serahkan Proyek Akhir
            </h3>

            <p class="text-sm text-slate-500 mt-2">
                Jika pekerjaan sudah selesai, unggah hasil akhir agar customer dapat melakukan review.
            </p>

            @if (
            in_array($pesanan->status_pesanan, ['diproses', 'revisi'])
            && $progressSekarang >= 75
            && $progressSekarang < 100
                )
                <a href="{{ route('freelancer.hasil.create', $pesanan->id) }}"
                class="block text-center mt-5 w-full px-5 py-3 bg-green-600 text-white rounded-xl font-semibold hover:bg-green-700">
                Serahkan Hasil Akhir
                </a>
                @else
                <button disabled
                    class="mt-5 w-full px-5 py-3 bg-slate-100 text-slate-400 rounded-xl font-semibold cursor-not-allowed">
                    Serahkan Hasil Akhir
                </button>

                @if ($progressSekarang < 75 && in_array($pesanan->status_pesanan, ['dibayar', 'diproses', 'revisi']))
                    <p class="text-xs text-slate-500 mt-3 text-center">
                        Progress minimal harus 75% untuk menyerahkan hasil akhir.
                    </p>
                    @elseif ($pesanan->status_pesanan === 'menunggu_approve')
                    <p class="text-xs text-slate-500 mt-3 text-center">
                        Hasil akhir sudah dikirim dan sedang menunggu approval customer.
                    </p>
                    @elseif ($pesanan->status_pesanan === 'selesai')
                    <p class="text-xs text-slate-500 mt-3 text-center">
                        Pesanan sudah selesai.
                    </p>
                    @endif
                    @endif
        </div> -->

    </div>
</div>
</div>
@endsection