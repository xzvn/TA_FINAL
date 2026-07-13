@extends('layouts.customer')

@section('title', 'Detail Pesanan - JasaKampus')

@section('content')
@php
$progresses = $pesanan->progressPekerjaans->sortBy('created_at');
$progressTerakhir = $progresses->last();
$progressPersen = $pesanan->progressPekerjaans->max('persentase_progress') ?? 0;
$progressStyle = 'width: ' . $progressPersen . '%;';

$hasil = $pesanan->hasilPekerjaan;
$revisis = $pesanan->revisis ?? collect();
$revisiTerakhir = $revisis->sortByDesc('created_at')->first();

$sisaRevisi = ($pesanan->batas_revisi ?? 3) - ($pesanan->jumlah_revisi ?? 0);

$statusText = strtoupper(str_replace('_', ' ', $pesanan->status_pesanan));

$statusClass = match ($pesanan->status_pesanan) {
'menunggu_pembayaran' => 'bg-yellow-100 text-yellow-700',
'dibayar' => 'bg-blue-100 text-blue-700',
'diproses' => 'bg-indigo-100 text-indigo-700',
'menunggu_approve' => 'bg-purple-100 text-purple-700',
'revisi' => 'bg-orange-100 text-orange-700',
'selesai' => 'bg-green-100 text-green-700',
'dispute' => 'bg-red-100 text-red-700',
default => 'bg-slate-100 text-slate-700',
};

$escrowText = $pesanan->pembayaran
? strtoupper(str_replace('_', ' ', $pesanan->pembayaran->status_escrow))
: 'BELUM ADA';

$escrowClass = match ($pesanan->pembayaran->status_escrow ?? null) {
'ditahan' => 'bg-blue-100 text-blue-700',
'dicairkan' => 'bg-green-100 text-green-700',
'dikembalikan' => 'bg-red-100 text-red-700',
default => 'bg-slate-100 text-slate-700',
};
@endphp

<div class="max-w-7xl mx-auto space-y-6">

    {{-- HEADER --}}
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
        <div>
            <p class="text-sm text-slate-500">
                Dashboard / Pesanan Saya / Order #{{ $pesanan->id }}
            </p>

            <h1 class="text-2xl md:text-3xl font-bold text-slate-900 mt-1">
                Detail Pesanan
            </h1>

            <p class="text-sm text-slate-500 mt-1">
                Pantau progress, hasil akhir, revisi, dan status escrow pesanan kamu.
            </p>
        </div>

        <div class="flex flex-wrap gap-2">
            <span class="px-4 py-2 rounded-full text-xs font-bold {{ $statusClass }}">
                {{ $statusText }}
            </span>

            <span class="px-4 py-2 rounded-full text-xs font-bold {{ $escrowClass }}">
                ESCROW: {{ $escrowText }}
            </span>
        </div>
    </div>

    @if (session('success'))
    <div class="px-5 py-4 bg-green-100 border border-green-200 text-green-700 rounded-xl">
        {{ session('success') }}
    </div>
    @endif

    @if ($errors->any())
    <div class="px-5 py-4 bg-red-100 border border-red-200 text-red-700 rounded-xl">
        {{ $errors->first() }}
    </div>
    @endif

    <div class="grid grid-cols-1 xl:grid-cols-12 gap-6">

        {{-- LEFT CONTENT --}}
        <div class="xl:col-span-8 space-y-6">

            {{-- SERVICE CARD --}}
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="p-6 flex flex-col md:flex-row gap-5">
                    <div class="w-full md:w-48 h-36 bg-slate-100 rounded-2xl overflow-hidden shrink-0">
                        @if ($pesanan->jasa?->thumbnail)
                        <img src="{{ \App\Services\CloudinaryService::mediaUrl($pesanan->jasa->thumbnail) }}"
                            alt="{{ $pesanan->jasa->nama_jasa }}"
                            class="w-full h-full object-cover">
                        @else
                        <div class="w-full h-full flex items-center justify-center text-5xl">
                            💼
                        </div>
                        @endif
                    </div>

                    <div class="flex-1">
                        <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-3">
                            <div>
                                <p class="text-xs text-blue-600 font-bold uppercase">
                                    {{ $pesanan->jasa->kategori ?? 'Jasa' }}
                                </p>

                                <h2 class="text-xl font-bold text-slate-900 mt-1">
                                    {{ $pesanan->jasa->nama_jasa ?? '-' }}
                                </h2>

                                <p class="text-sm text-slate-500 mt-2">
                                    Freelancer:
                                    <span class="font-semibold text-slate-800">
                                        {{ $pesanan->freelancer->nama ?? $pesanan->jasa->freelancer->nama ?? '-' }}
                                    </span>
                                </p>
                            </div>

                            <div class="text-left lg:text-right">
                                <p class="text-xs text-slate-500">Total Pembayaran</p>
                                <p class="text-2xl font-bold text-blue-600">
                                    Rp {{ number_format($pesanan->total_harga, 0, ',', '.') }}
                                </p>
                            </div>
                        </div>

                        <div class="mt-5 p-4 bg-slate-50 rounded-xl border border-slate-200">
                            <p class="text-sm font-bold text-slate-900">
                                Kebutuhan Customer
                            </p>

                            <p class="text-sm text-slate-600 mt-2 leading-relaxed">
                                {{ $pesanan->deskripsi_kebutuhan }}
                            </p>

                            @if ($pesanan->file_requirement)
                            <a href="{{ \App\Services\CloudinaryService::mediaUrl($pesanan->file_requirement) }}"
                                target="_blank"
                                class="inline-block mt-3 text-sm font-semibold text-blue-600 hover:underline">
                                Lihat File Requirement
                            </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- PROGRESS --}}
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
                <div class="flex items-center justify-between gap-4">
                    <div>
                        <h3 class="text-lg font-bold text-slate-900">
                            Progress Pekerjaan
                        </h3>

                        <p class="text-sm text-slate-500 mt-1">
                            Perkembangan pekerjaan yang diunggah oleh freelancer.
                        </p>
                    </div>

                    <span class="text-3xl font-bold text-blue-600">
                        {{ $progressPersen }}%
                    </span>
                </div>

                <div class="mt-5 w-full h-3 bg-slate-100 rounded-full overflow-hidden">
                    <div class="h-3 bg-blue-600 rounded-full transition-all duration-300"
                        style="{{ $progressStyle }}">
                    </div>
                </div>

                <div class="grid grid-cols-4 gap-3 mt-6 text-center">
                    @foreach ([25 => 'Awal', 50 => 'Setengah', 75 => 'Review', 100 => 'Selesai'] as $value => $label)
                    <div>
                        <div class="w-10 h-10 mx-auto rounded-full flex items-center justify-center text-sm font-bold
                                {{ $progressPersen >= $value ? 'bg-blue-600 text-white' : 'bg-slate-100 text-slate-400' }}">
                            ✓
                        </div>
                        <p class="text-xs text-slate-500 mt-2">
                            {{ $label }}
                        </p>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- TIMELINE PROGRESS --}}
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
                <h3 class="text-lg font-bold text-slate-900">
                    Timeline Progress
                </h3>

                @if ($progresses->count() > 0)
                <div class="mt-6 space-y-4">
                    @foreach ($progresses->sortByDesc('created_at') as $progress)
                    <div class="p-5 bg-slate-50 border border-slate-200 rounded-2xl">
                        <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-3">
                            <div>
                                <h4 class="font-bold text-slate-900">
                                    {{ $progress->judul_progress }}
                                </h4>

                                <p class="text-xs text-slate-500 mt-1">
                                    {{ $progress->created_at->format('d M Y H:i') }}
                                </p>
                            </div>

                            <span class="px-3 py-1 bg-blue-100 text-blue-700 rounded-full text-xs font-bold">
                                {{ $progress->persentase_progress }}%
                            </span>
                        </div>

                        <p class="text-sm text-slate-600 mt-3 leading-relaxed">
                            {{ $progress->deskripsi_progress }}
                        </p>

                        @if (
                        $pesanan->status_pesanan === 'diproses'
                        && $sisaRevisi > 0
                        && $progress->id === optional($progressTerakhir)->id
                        )
                        <form method="POST"
                            action="{{ route('customer.order.revision', $pesanan->id) }}"
                            class="mt-5 p-4 bg-white border border-orange-200 rounded-xl">
                            @csrf

                            <input type="hidden" name="id_progress" value="{{ $progress->id }}">

                            <textarea name="catatan_revisi"
                                rows="3"
                                class="w-full rounded-xl border-slate-300 focus:border-orange-500 focus:ring-orange-500 text-sm"
                                placeholder="Jelaskan bagian progress ini yang perlu direvisi..."
                                required></textarea>

                            <button type="submit"
                                class="mt-3 px-4 py-2 bg-orange-500 text-white rounded-xl text-sm font-bold hover:bg-orange-600">
                                Kirim Revisi Progress
                            </button>
                        </form>
                        @endif

                        @if ($progress->file_progress)
                        <a href="{{ \App\Services\CloudinaryService::mediaUrl($progress->file_progress) }}"
                            target="_blank"
                            class="inline-block mt-4 text-sm font-semibold text-blue-600 hover:underline">
                            Lihat Lampiran Progress
                        </a>
                        @endif
                    </div>
                    @endforeach
                </div>
                @else
                <div class="mt-6 p-8 bg-slate-50 rounded-2xl border border-slate-200 text-center">
                    <div class="text-4xl mb-3">📝</div>
                    <h4 class="font-bold text-slate-900">
                        Belum ada progress
                    </h4>
                    <p class="text-sm text-slate-500 mt-2">
                        Freelancer belum mengunggah progress pekerjaan.
                    </p>
                </div>
                @endif
            </div>

            {{-- HASIL AKHIR --}}
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                    <div>
                        <h3 class="text-lg font-bold text-slate-900">
                            Hasil Akhir
                        </h3>

                        <p class="text-sm text-slate-500 mt-1">
                            File final yang dikirim oleh freelancer untuk direview.
                        </p>
                    </div>

                    @if ($hasil)
                    <span class="px-3 py-1 bg-purple-100 text-purple-700 rounded-full text-xs font-bold">
                        {{ strtoupper(str_replace('_', ' ', $hasil->status_hasil)) }}
                    </span>
                    @endif
                </div>

                @if ($hasil)
                <div class="mt-6 p-5 bg-slate-50 border border-slate-200 rounded-2xl">
                    <p class="text-sm font-bold text-slate-900">
                        Catatan Freelancer
                    </p>

                    <p class="text-sm text-slate-600 mt-2 leading-relaxed">
                        {{ $hasil->catatan ?? 'Freelancer telah mengunggah hasil akhir pekerjaan.' }}
                    </p>

                    <div class="mt-5 flex flex-col sm:flex-row gap-3">
                        <a href="{{ \App\Services\CloudinaryService::mediaUrl($hasil->file_hasil) }}"
                            target="_blank"
                            class="px-5 py-3 bg-blue-600 text-white rounded-xl font-bold text-center hover:bg-blue-700">
                            Download Hasil Akhir
                        </a>

                        @if ($pesanan->jasa)
                        <a href="{{ route('customer.chat.show', $pesanan->jasa->id) }}"
                            class="px-5 py-3 bg-slate-100 text-slate-700 rounded-xl font-bold text-center hover:bg-slate-200">
                            Chat Freelancer
                        </a>
                        @endif
                    </div>
                </div>
                @else
                <div class="mt-6 p-8 bg-slate-50 rounded-2xl border border-slate-200 text-center">
                    <div class="text-4xl mb-3">📦</div>
                    <h4 class="font-bold text-slate-900">
                        Hasil akhir belum tersedia
                    </h4>
                    <p class="text-sm text-slate-500 mt-2">
                        Hasil akhir akan muncul setelah freelancer menyerahkan proyek.
                    </p>
                </div>
                @endif
            </div>

            {{-- RIWAYAT REVISI --}}
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
                <h3 class="text-lg font-bold text-slate-900">
                    Riwayat Revisi
                </h3>



                <p class="text-sm text-slate-500 mt-1">
                    Semua permintaan revisi yang pernah diajukan.
                </p>

                @if ($revisis->count() > 0)
                <div class="mt-6 space-y-4">
                    @foreach ($revisis->sortByDesc('created_at') as $revisi)
                    <div class="p-5 bg-orange-50 border border-orange-200 rounded-2xl">
                        <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-3">
                            <div>
                                <p class="font-bold text-orange-800">
                                    Revisi #{{ $loop->iteration }}
                                </p>

                                <p class="text-xs text-orange-500 mt-1">
                                    Diajukan {{ $revisi->created_at->format('d M Y H:i') }}
                                </p>
                            </div>

                            <span class="px-3 py-1 rounded-full text-xs font-bold
                                        @if ($revisi->status_revisi === 'diajukan') bg-orange-100 text-orange-700
                                        @elseif ($revisi->status_revisi === 'diproses') bg-blue-100 text-blue-700
                                        @else bg-green-100 text-green-700
                                        @endif">
                                {{ strtoupper($revisi->status_revisi) }}
                            </span>
                        </div>

                        <p class="text-sm text-orange-700 mt-3 leading-relaxed">
                            {{ $revisi->catatan_revisi }}
                        </p>

                        @if ($revisi->tanggal_selesai)
                        <p class="text-xs text-green-600 mt-3">
                            Selesai: {{ \Carbon\Carbon::parse($revisi->tanggal_selesai)->format('d M Y H:i') }}
                        </p>
                        @endif
                    </div>
                    @endforeach
                </div>
                @else
                <div class="mt-6 p-6 bg-slate-50 border border-slate-200 rounded-2xl text-center">
                    <p class="text-sm text-slate-500">
                        Belum ada revisi pada pesanan ini.
                    </p>
                </div>
                @endif
            </div>
        </div>

        {{-- RIGHT SIDEBAR --}}
        <div class="xl:col-span-4 space-y-6">

            <{{-- ACTION CARD --}}
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
                <h3 class="text-lg font-bold text-slate-900">
                    Aksi Customer
                </h3>

                @if ($pesanan->status_pesanan === 'menunggu_approve' && $hasil)
                <div class="mt-5 p-4 bg-purple-50 border border-purple-200 rounded-xl">
                    <p class="text-sm font-bold text-purple-700">
                        Hasil akhir menunggu review kamu.
                    </p>

                    <p class="text-sm text-purple-600 mt-2">
                        Setujui jika hasil sudah sesuai, atau ajukan revisi jika masih ada yang perlu diperbaiki.
                    </p>
                </div>

                <form method="POST"
                    action="{{ route('customer.order.approve', $pesanan->id) }}"
                    class="mt-5">
                    @csrf

                    <button type="submit"
                        class="w-full px-5 py-3 bg-green-600 text-white rounded-xl font-bold hover:bg-green-700">
                        Approve Pekerjaan
                    </button>
                </form>

                <form method="POST"
                    action="{{ route('customer.order.revision', $pesanan->id) }}"
                    class="mt-5">
                    @csrf

                    <label class="block text-sm font-bold text-slate-700 mb-2">
                        Catatan Revisi
                    </label>

                    <textarea name="catatan_revisi"
                        rows="5"
                        class="w-full rounded-xl border-slate-300 focus:border-orange-500 focus:ring-orange-500 text-sm"
                        placeholder="Jelaskan bagian mana yang perlu direvisi..."
                        required>{{ old('catatan_revisi') }}</textarea>

                    <p class="text-xs text-slate-500 mt-2">
                        Sisa revisi: {{ max(0, $sisaRevisi) }} dari {{ $pesanan->batas_revisi ?? 3 }}
                    </p>

                    <button type="submit"
                        class="mt-3 w-full px-5 py-3 bg-orange-500 text-white rounded-xl font-bold hover:bg-orange-600">
                        Minta Revisi
                    </button>
                </form>

                @elseif ($pesanan->status_pesanan === 'revisi')
                <div class="mt-5 p-4 bg-orange-50 border border-orange-200 rounded-xl">
                    <p class="text-sm font-bold text-orange-700">
                        Revisi sedang diproses.
                    </p>

                    @if ($revisiTerakhir)
                    <p class="text-sm text-orange-600 mt-2">
                        {{ $revisiTerakhir->catatan_revisi }}
                    </p>
                    @endif
                </div>

                @elseif ($pesanan->status_pesanan === 'selesai')
                <div class="mt-5 p-4 bg-green-50 border border-green-200 rounded-xl">
                    <p class="text-sm font-bold text-green-700">
                        Pesanan selesai.
                    </p>

                    <p class="text-sm text-green-600 mt-2">
                        Pekerjaan telah disetujui dan dana escrow telah dicairkan.
                    </p>

                    <a href="{{ route('customer.order.review.create', $pesanan->id) }}"
                        class="block text-center mt-5 w-full px-5 py-3 bg-blue-600 text-white rounded-xl font-bold hover:bg-blue-700">
                        Beri Ulasan
                    </a>
                </div>
                @elseif ($pesanan->status_pesanan === 'selesai')
                <div class="mt-5 p-4 bg-green-50 border border-green-200 rounded-xl">
                    <p class="text-sm font-bold text-green-700">
                        Pesanan selesai.
                    </p>

                    <p class="text-sm text-green-600 mt-2">
                        Pekerjaan telah disetujui dan dana escrow telah dicairkan.
                    </p>

                    <a href="{{ route('customer.order.review.create', $pesanan->id) }}"
                        class="block text-center mt-5 w-full px-5 py-3 bg-blue-600 text-white rounded-xl font-bold hover:bg-blue-700">
                        Beri Ulasan
                    </a>
                </div>

                @else
                <div class="mt-5 p-4 bg-slate-50 border border-slate-200 rounded-xl">
                    <p class="text-sm text-slate-600">
                        Aksi approve/revisi akan aktif setelah freelancer mengirim hasil akhir.
                    </p>
                </div>
                @endif
        </div>

        {{-- DISPUTE CARD --}}
        @if (
        $pesanan->jumlah_revisi >= $pesanan->batas_revisi
        && ! in_array($pesanan->status_pesanan, ['selesai', 'dibatalkan', 'dispute'])
        && ! $pesanan->dispute
        )
        <div class="bg-white rounded-2xl border border-red-200 shadow-sm p-6">
            <h3 class="text-lg font-bold text-red-700">
                Ajukan Dispute
            </h3>

            <p class="text-sm text-slate-500 mt-2">
                Batas revisi sudah tercapai. Jika hasil pekerjaan masih belum sesuai, kamu dapat mengajukan dispute agar admin meninjau pesanan ini.
            </p>

            <form method="POST"
                action="{{ route('customer.order.dispute.store', $pesanan->id) }}"
                enctype="multipart/form-data"
                class="mt-5 space-y-4">
                @csrf

                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">
                        Alasan Dispute
                    </label>

                    <textarea name="alasan_dispute"
                        rows="5"
                        class="w-full rounded-xl border-slate-300 focus:border-red-500 focus:ring-red-500 text-sm"
                        placeholder="Jelaskan masalah pada pesanan ini..."
                        required>{{ old('alasan_dispute') }}</textarea>
                </div>

                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">
                        Bukti Pendukung
                    </label>

                    <input type="file"
                        name="bukti_dispute"
                        class="w-full rounded-xl border border-slate-300 bg-white p-3 text-sm">

                    <p class="text-xs text-slate-400 mt-2">
                        Opsional. Format JPG, PNG, PDF, DOC, atau DOCX. Maksimal 5MB.
                    </p>
                </div>

                <button type="submit"
                    class="w-full px-5 py-3 bg-red-600 text-white rounded-xl font-bold hover:bg-red-700">
                    Kirim Dispute
                </button>
            </form>
        </div>
        @endif

        @if ($pesanan->status_pesanan === 'dispute' && $pesanan->dispute)
        <div class="bg-white rounded-2xl border border-red-200 shadow-sm p-6">
            <h3 class="text-lg font-bold text-red-700">
                Dispute Sedang Diproses
            </h3>

            <div class="mt-5 p-4 bg-red-50 border border-red-200 rounded-xl">
                <p class="text-sm font-bold text-red-700">
                    Status: {{ strtoupper($pesanan->dispute->status_dispute) }}
                </p>

                <p class="text-sm text-red-600 mt-2">
                    {{ $pesanan->dispute->alasan_dispute }}
                </p>

                @if ($pesanan->dispute->bukti_dispute)
                <a href="{{ \App\Services\CloudinaryService::mediaUrl($pesanan->dispute->bukti_dispute) }}"
                    target="_blank"
                    class="inline-block mt-3 text-sm font-bold text-red-700 hover:underline">
                    Lihat Bukti Dispute
                </a>
                @endif

                @if ($pesanan->dispute->keputusan_admin)
                <div class="mt-4 p-3 bg-white border border-red-200 rounded-xl">
                    <p class="text-xs text-slate-500">
                        Keputusan Admin
                    </p>

                    <p class="text-sm text-slate-700 mt-1">
                        {{ $pesanan->dispute->keputusan_admin }}
                    </p>
                </div>
                @endif
            </div>
        </div>
        @endif

        {{-- ORDER SUMMARY --}}
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
            <h3 class="text-lg font-bold text-slate-900">
                Ringkasan Pesanan
            </h3>

            <div class="mt-5 space-y-4">
                <div class="flex justify-between gap-4">
                    <span class="text-sm text-slate-500">Order ID</span>
                    <span class="text-sm font-bold text-slate-900">#{{ $pesanan->id }}</span>
                </div>

                <div class="flex justify-between gap-4">
                    <span class="text-sm text-slate-500">Tanggal Order</span>
                    <span class="text-sm font-bold text-slate-900">
                        {{ $pesanan->created_at->format('d M Y') }}
                    </span>
                </div>

                <div class="flex justify-between gap-4">
                    <span class="text-sm text-slate-500">Status Pesanan</span>
                    <span class="text-sm font-bold text-slate-900">
                        {{ $statusText }}
                    </span>
                </div>

                <div class="flex justify-between gap-4">
                    <span class="text-sm text-slate-500">Revisi</span>
                    <span class="text-sm font-bold text-slate-900">
                        {{ $pesanan->jumlah_revisi ?? 0 }} / {{ $pesanan->batas_revisi ?? 3 }}
                    </span>
                </div>

                <div class="pt-4 border-t border-slate-200">
                    <div class="flex justify-between gap-4">
                        <span class="text-sm text-slate-500">Total</span>
                        <span class="text-xl font-bold text-blue-600">
                            Rp {{ number_format($pesanan->total_harga, 0, ',', '.') }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        {{-- ESCROW CARD --}}
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
            <h3 class="text-lg font-bold text-slate-900">
                Status Escrow
            </h3>

            <div class="mt-5 p-4 rounded-xl border {{ $pesanan->pembayaran ? 'bg-blue-50 border-blue-200' : 'bg-slate-50 border-slate-200' }}">
                <p class="text-sm font-bold {{ $pesanan->pembayaran ? 'text-blue-700' : 'text-slate-700' }}">
                    {{ $escrowText }}
                </p>

                @if ($pesanan->pembayaran?->status_escrow === 'ditahan')
                <p class="text-sm text-blue-600 mt-2">
                    Dana sudah dibayar customer dan sedang ditahan oleh sistem sampai pekerjaan disetujui.
                </p>
                @elseif ($pesanan->pembayaran?->status_escrow === 'dicairkan')
                <p class="text-sm text-green-600 mt-2">
                    Dana sudah dicairkan ke freelancer.
                </p>
                @else
                <p class="text-sm text-slate-500 mt-2">
                    Informasi escrow belum tersedia.
                </p>
                @endif
            </div>

            @if ($pesanan->status_pesanan === 'menunggu_pembayaran')
            <a href="{{ route('customer.payment.show', $pesanan->id) }}"
                class="block text-center mt-5 w-full px-5 py-3 bg-blue-600 text-white rounded-xl font-bold hover:bg-blue-700">
                Lanjut Pembayaran
            </a>
            @endif
        </div>

    </div>
</div>
</div>
@endsection