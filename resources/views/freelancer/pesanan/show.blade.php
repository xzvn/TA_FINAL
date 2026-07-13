@extends('layouts.freelancer')

@section('title', 'Detail Pesanan - JasaKampus')
@section('page-title', 'Detail Pesanan')

@section('content')
<div class="max-w-5xl mx-auto">
    <a href="{{ route('freelancer.pesanan.index') }}"
        class="inline-block mb-6 text-blue-600 font-semibold hover:underline">
        ← Kembali ke Pesanan Masuk
    </a>

    <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6">
        <div class="flex items-center justify-between border-b border-slate-200 pb-5">
            <div>
                <h1 class="text-2xl font-bold text-slate-900">
                    Detail Pesanan #{{ $pesanan->id }}
                </h1>
                <p class="text-sm text-slate-500 mt-1">
                    {{ $pesanan->created_at->format('d M Y H:i') }}
                </p>
            </div>

            <span class="px-4 py-2 rounded-full text-xs font-bold
                    @if ($pesanan->status_pesanan === 'dibayar') bg-blue-100 text-blue-700
                    @elseif ($pesanan->status_pesanan === 'diproses') bg-yellow-100 text-yellow-700
                    @elseif ($pesanan->status_pesanan === 'selesai') bg-green-100 text-green-700
                    @else bg-slate-100 text-slate-700
                    @endif">
                {{ str_replace('_', ' ', strtoupper($pesanan->status_pesanan)) }}
            </span>
        </div>

        <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <p class="text-xs text-slate-500">Nama Jasa</p>
                <p class="font-semibold text-slate-900">
                    {{ $pesanan->jasa->nama_jasa ?? '-' }}
                </p>
            </div>

            <div>
                <p class="text-xs text-slate-500">Customer</p>
                <p class="font-semibold text-slate-900">
                    {{ $pesanan->customer->nama ?? '-' }}
                </p>
            </div>

            <div>
                <p class="text-xs text-slate-500">Total Harga</p>
                <p class="font-bold text-blue-600 text-xl">
                    Rp {{ number_format($pesanan->total_harga, 0, ',', '.') }}
                </p>
            </div>

            <div>
                <p class="text-xs text-slate-500">Status Escrow</p>
                <p class="font-semibold text-slate-900">
                    {{ $pesanan->pembayaran ? str_replace('_', ' ', strtoupper($pesanan->pembayaran->status_escrow)) : '-' }}
                </p>
            </div>
        </div>

        <div class="mt-6">
            <p class="text-xs text-slate-500 mb-2">Deskripsi Kebutuhan Customer</p>
            <div class="p-4 bg-slate-50 rounded-lg text-sm text-slate-700 leading-relaxed">
                {{ $pesanan->deskripsi_kebutuhan }}
            </div>
        </div>

        @if ($pesanan->file_requirement)
        <div class="mt-6">
            <a href="{{ \App\Services\CloudinaryService::mediaUrl($pesanan->file_requirement) }}"
                target="_blank"
                class="inline-block px-4 py-2 bg-slate-100 text-slate-700 rounded-lg font-semibold hover:bg-slate-200">
                Lihat File Requirement
            </a>
        </div>
        @endif

        <div class="mt-8 p-5 bg-blue-50 border border-blue-200 rounded-xl">
            <h3 class="font-bold text-blue-800">
                Langkah Berikutnya
            </h3>

            <p class="text-sm text-blue-700 mt-1">
                Setelah pesanan dibayar, freelancer dapat mulai mengerjakan proyek dan mengunggah progress pekerjaan.
            </p>

            <a href="{{ route('freelancer.progress.create', $pesanan->id) }}"
                class="block text-center mt-4 w-full px-5 py-3 bg-blue-600 text-white rounded-lg font-semibold hover:bg-blue-700">
                Upload Progress Pekerjaan
            </a>
            @if (in_array($pesanan->status_pesanan, ['diproses', 'revisi']))
            <a href="{{ route('freelancer.hasil.create', $pesanan->id) }}"
                class="block text-center mt-3 w-full px-5 py-3 bg-green-600 text-white rounded-lg font-semibold hover:bg-green-700">
                Upload Hasil Akhir
            </a>
            @endif
            <p class="text-xs text-slate-500 mt-2 text-center">
                Tombol ini akan aktif setelah fitur progress pekerjaan dibuat.
            </p>
        </div>
    </div>
</div>
@endsection