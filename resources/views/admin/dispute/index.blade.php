@extends('layouts.admin')

@section('title', 'Daftar Sengketa - Admin JasaKampus')
@section('page-title', 'Aduan')

@section('content')
<div class="max-w-7xl mx-auto">

    @php
    $selectedDispute = $selectedDispute ?? null;
    $chatRiwayat = $chatRiwayat ?? collect();
    @endphp

    @if (session('success'))
    <div class="mb-6 px-5 py-4 bg-green-100 border border-green-200 text-green-700 rounded-xl">
        {{ session('success') }}
    </div>
    @endif

    @if ($errors->any())
    <div class="mb-6 px-5 py-4 bg-red-100 border border-red-200 text-red-700 rounded-xl">
        {{ $errors->first() }}
    </div>
    @endif

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5 mb-6">
        <div class="flex flex-col lg:flex-row lg:items-end lg:justify-between gap-5">
            <div>
                <h3 class="text-lg font-bold text-slate-900">
                    Filter Aduan / Dispute
                </h3>
                <p class="text-sm text-slate-500 mt-1">
                    Filter sengketa berdasarkan status dan tanggal pengajuan.
                </p>
            </div>

            <form method="GET" action="{{ route('admin.disputes.index') }}"
                class="grid grid-cols-1 md:grid-cols-4 gap-3 w-full lg:w-auto">

                <select name="status_dispute"
                    class="rounded-xl border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="">Semua Status</option>
                    <option value="pending" {{ request('status_dispute') === 'pending' ? 'selected' : '' }}>
                        Pending
                    </option>
                    <option value="diproses" {{ request('status_dispute') === 'diproses' ? 'selected' : '' }}>
                        Diproses
                    </option>
                    <option value="refund" {{ request('status_dispute') === 'refund' ? 'selected' : '' }}>
                        Refund
                    </option>
                    <option value="lanjutkan_pesanan" {{ request('status_dispute') === 'lanjutkan_pesanan' ? 'selected' : '' }}>
                        Lanjutkan Pesanan
                    </option>
                    <option value="ditolak" {{ request('status_dispute') === 'ditolak' ? 'selected' : '' }}>
                        Ditolak
                    </option>
                    <option value="selesai" {{ request('status_dispute') === 'selesai' ? 'selected' : '' }}>
                        Selesai
                    </option>
                </select>

                <input type="date"
                    name="tanggal_mulai"
                    value="{{ request('tanggal_mulai') }}"
                    class="rounded-xl border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500">

                <input type="date"
                    name="tanggal_selesai"
                    value="{{ request('tanggal_selesai') }}"
                    class="rounded-xl border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500">

                <div class="flex gap-2">
                    <button type="submit"
                        class="px-4 py-2 bg-blue-600 text-white rounded-xl text-sm font-bold hover:bg-blue-700">
                        Filter
                    </button>

                    <a href="{{ route('admin.disputes.index') }}"
                        class="px-4 py-2 bg-slate-100 text-slate-700 rounded-xl text-sm font-bold hover:bg-slate-200">
                        Reset
                    </a>
                </div>
            </form>
        </div>
    </div>


    <div class="grid grid-cols-1 xl:grid-cols-12 gap-6">

        {{-- LEFT: DAFTAR SENGKETA --}}
        <div class="xl:col-span-4">
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="p-5 border-b border-slate-200">
                    <div class="flex items-center justify-between gap-3">
                        <div>
                            <h2 class="text-lg font-bold text-slate-900">
                                Daftar Sengketa
                            </h2>
                            <p class="text-sm text-slate-500">
                                {{ $disputes->count() }} kasus sesuai filter
                            </p>
                        </div>

                        <button class="w-9 h-9 rounded-xl border border-slate-200 text-slate-500 hover:bg-slate-50">
                            ⏷
                        </button>
                    </div>
                </div>

                <div class="divide-y divide-slate-100 max-h-[760px] overflow-y-auto">
                    @forelse ($disputes as $dispute)
                    @php
                    $isActive = $selectedDispute && $selectedDispute->id === $dispute->id;

                    $priorityClass = match ($dispute->status_dispute) {
                    'pending' => 'text-red-600',
                    'diproses' => 'text-orange-600',
                    'refund' => 'text-red-600',
                    'lanjutkan_pesanan' => 'text-green-600',
                    default => 'text-slate-500',
                    };

                    $priorityText = match ($dispute->status_dispute) {
                    'pending' => 'HIGH PRIORITY',
                    'diproses' => 'MEDIUM',
                    'refund' => 'REFUNDED',
                    'lanjutkan_pesanan' => 'RELEASED',
                    default => 'CLOSED',
                    };
                    @endphp

                    <a href="{{ route('admin.disputes.index', array_merge(request()->only([
        'status_dispute',
        'tanggal_mulai',
        'tanggal_selesai',
    ]), ['dispute' => $dispute->id])) }}"


                        class="block p-5 transition {{ $isActive ? 'bg-blue-50' : 'hover:bg-slate-50' }}">
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0">
                                <p class="text-[11px] font-bold {{ $priorityClass }}">
                                    {{ $priorityText }}
                                </p>

                                <h3 class="font-bold text-sm text-slate-900 mt-1">
                                    #DS-{{ str_pad($dispute->id, 4, '0', STR_PAD_LEFT) }}
                                    {{ $dispute->pesanan->jasa->nama_jasa ?? 'Dispute Pesanan' }}
                                </h3>

                                <p class="text-sm text-slate-500 mt-1">
                                    "{{ \Illuminate\Support\Str::limit($dispute->alasan_dispute, 80) }}"
                                </p>

                                <div class="flex items-center gap-2 mt-3">
                                    <div class="w-6 h-6 rounded-full bg-slate-200 text-slate-700 flex items-center justify-center text-[10px] font-bold">
                                        {{ strtoupper(substr($dispute->customer->nama ?? 'C', 0, 1)) }}
                                    </div>

                                    <p class="text-xs text-slate-500">
                                        {{ $dispute->customer->nama ?? '-' }}
                                        vs
                                        {{ $dispute->freelancer->nama ?? '-' }}
                                    </p>
                                </div>
                            </div>

                            <p class="text-[11px] text-slate-400 shrink-0">
                                {{ $dispute->created_at->diffForHumans() }}
                            </p>
                        </div>
                    </a>
                    @empty
                    <div class="p-10 text-center">
                        <div class="text-4xl mb-3">⚖️</div>
                        <p class="text-sm text-slate-500">
                            Belum ada sengketa.
                        </p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- RIGHT: DETAIL --}}
        <div class="xl:col-span-8">
            @if ($selectedDispute)
            @php
            $pesanan = $selectedDispute->pesanan;
            $pembayaran = $pesanan->pembayaran ?? null;

            $statusClass = match ($selectedDispute->status_dispute) {
            'pending' => 'bg-yellow-100 text-yellow-700',
            'diproses' => 'bg-blue-100 text-blue-700',
            'refund' => 'bg-red-100 text-red-700',
            'lanjutkan_pesanan' => 'bg-green-100 text-green-700',
            default => 'bg-slate-100 text-slate-700',
            };
            @endphp

            <div class="space-y-6">

                {{-- DETAIL HEADER --}}
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
                    <div class="flex flex-col 2xl:flex-row 2xl:items-start 2xl:justify-between gap-5">
                        <div>
                            <div class="flex items-center gap-3 flex-wrap">
                                <h1 class="text-xl font-bold text-slate-900">
                                    Dispute Detail: #DS-{{ str_pad($selectedDispute->id, 4, '0', STR_PAD_LEFT) }}
                                </h1>

                                <span class="px-3 py-1 rounded-full text-xs font-bold {{ $statusClass }}">
                                    {{ strtoupper(str_replace('_', ' ', $selectedDispute->status_dispute)) }}
                                </span>
                            </div>

                            <p class="text-sm text-slate-500 mt-2">
                                Proyek:
                                <span class="font-semibold text-slate-700">
                                    {{ $pesanan->jasa->nama_jasa ?? '-' }}
                                </span>
                            </p>

                            <p class="text-sm text-slate-500 mt-1">
                                Nominal Transaksi:
                                <span class="font-bold text-blue-600">
                                    Rp {{ number_format($pesanan->total_harga ?? 0, 0, ',', '.') }}
                                </span>
                            </p>
                        </div>

                        @if (in_array($selectedDispute->status_dispute, ['pending', 'diproses']))
                        <div class="flex flex-col sm:flex-row gap-3">
                            <form method="POST"
                                action="{{ route('admin.disputes.refund', $selectedDispute->id) }}"
                                onsubmit="return confirm('Yakin ingin mengembalikan uang customer?')">
                                @csrf

                                <input type="hidden"
                                    name="keputusan_admin"
                                    value="Admin memutuskan dana dikembalikan kepada customer berdasarkan hasil peninjauan bukti, riwayat komunikasi, dan status pekerjaan.">

                                <button type="submit"
                                    class="w-full sm:w-auto px-5 py-3 border border-red-300 bg-red-50 text-red-600 rounded-xl text-sm font-bold hover:bg-red-100">
                                    Kembalikan Uang Pelanggan
                                </button>
                            </form>

                            <form method="POST"
                                action="{{ route('admin.disputes.release', $selectedDispute->id) }}"
                                onsubmit="return confirm('Yakin ingin melepaskan dana ke freelancer?')">
                                @csrf

                                <input type="hidden"
                                    name="keputusan_admin"
                                    value="Admin memutuskan dana dilepaskan kepada freelancer berdasarkan hasil peninjauan bukti, riwayat komunikasi, dan status pekerjaan.">

                                <button type="submit"
                                    class="w-full sm:w-auto px-5 py-3 bg-blue-600 text-white rounded-xl text-sm font-bold hover:bg-blue-700">
                                    Lepaskan Dana ke Freelancer
                                </button>
                            </form>
                        </div>
                        @endif
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

                    {{-- CENTER DETAIL --}}
                    <div class="lg:col-span-8 space-y-6">

                        {{-- PARTICIPANTS --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5">
                                <div class="flex items-center justify-between">
                                    <p class="text-xs font-bold text-slate-400 uppercase">
                                        Pelanggan
                                    </p>
                                    <span class="text-xs text-blue-500">ⓘ</span>
                                </div>

                                <div class="mt-4 flex items-center gap-4">
                                    <div class="w-12 h-12 rounded-full bg-slate-200 text-slate-700 flex items-center justify-center font-bold">
                                        {{ strtoupper(substr($selectedDispute->customer->nama ?? 'C', 0, 1)) }}
                                    </div>

                                    <div>
                                        <p class="font-bold text-slate-900">
                                            {{ $selectedDispute->customer->nama ?? '-' }}
                                        </p>
                                        <p class="text-sm text-slate-500">
                                            {{ $selectedDispute->customer->email ?? '-' }}
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5">
                                <div class="flex items-center justify-between">
                                    <p class="text-xs font-bold text-slate-400 uppercase">
                                        Freelancer
                                    </p>
                                    <span class="text-xs text-blue-500">ⓘ</span>
                                </div>

                                <div class="mt-4 flex items-center gap-4">
                                    <div class="w-12 h-12 rounded-full bg-cyan-100 text-cyan-700 flex items-center justify-center font-bold">
                                        {{ strtoupper(substr($selectedDispute->freelancer->nama ?? 'F', 0, 1)) }}
                                    </div>

                                    <div>
                                        <p class="font-bold text-slate-900">
                                            {{ $selectedDispute->freelancer->nama ?? '-' }}
                                        </p>
                                        <p class="text-sm text-slate-500">
                                            {{ $selectedDispute->freelancer->email ?? '-' }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- ALASAN DISPUTE --}}
                        <div class="bg-red-50 rounded-2xl border border-red-200 p-5">
                            <h3 class="font-bold text-red-700">
                                Alasan Dispute dari Customer
                            </h3>

                            <p class="text-sm text-red-600 mt-3 leading-relaxed">
                                {{ $selectedDispute->alasan_dispute }}
                            </p>
                        </div>

                        {{-- RIWAYAT DISKUSI / CHAT --}}
                        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5">
                            <div class="flex items-center justify-between">
                                <h3 class="font-bold text-slate-900">
                                    Riwayat Diskusi Proyek
                                </h3>

                                <span class="text-xs text-blue-600 font-semibold">
                                    {{ $chatRiwayat->count() }} pesan
                                </span>
                            </div>

                            <p class="text-sm text-slate-500 mt-1">
                                Riwayat chat antara customer dan freelancer terkait proyek ini.
                            </p>

                            <div class="mt-5 rounded-2xl border border-slate-200 bg-slate-50 p-5 space-y-4 max-h-[520px] overflow-y-auto">
                                @if ($chatRiwayat->count() > 0)
                                @foreach ($chatRiwayat as $chat)
                                @php
                                $isCustomer = $chat->pengirim_id === $selectedDispute->id_customer;
                                $isFreelancer = $chat->pengirim_id === $selectedDispute->id_freelancer;
                                @endphp

                                <div class="{{ $isFreelancer ? 'ml-auto' : '' }} max-w-[85%]">
                                    <div class="rounded-2xl p-4
                                                    {{ $isFreelancer ? 'bg-blue-600 text-white' : 'bg-white border border-slate-200 text-slate-700' }}">

                                        <div class="flex items-center justify-between gap-3 mb-2">
                                            <p class="text-xs font-bold {{ $isFreelancer ? 'text-blue-100' : 'text-slate-500' }}">
                                                @if ($isCustomer)
                                                Customer - {{ $chat->pengirim->nama ?? 'Customer' }}
                                                @elseif ($isFreelancer)
                                                Freelancer - {{ $chat->pengirim->nama ?? 'Freelancer' }}
                                                @else
                                                {{ $chat->pengirim->nama ?? 'Pengguna' }}
                                                @endif
                                            </p>

                                            <p class="text-[11px] {{ $isFreelancer ? 'text-blue-100' : 'text-slate-400' }}">
                                                {{ $chat->created_at->format('d M Y H:i') }}
                                            </p>
                                        </div>

                                        <p class="text-sm leading-relaxed">
                                            {{ $chat->pesan }}
                                        </p>

                                        @if ($chat->lampiran)
                                        <a href="{{ \App\Services\CloudinaryService::mediaUrl($chat->lampiran) }}"
                                            target="_blank"
                                            class="inline-block mt-3 text-xs font-bold underline {{ $isFreelancer ? 'text-white' : 'text-blue-600' }}">
                                            Lihat Lampiran Chat
                                        </a>
                                        @endif
                                    </div>
                                </div>
                                @endforeach
                                @else
                                <div class="text-center py-10">
                                    <div class="text-4xl mb-3">💬</div>

                                    <h4 class="font-bold text-slate-900">
                                        Belum ada riwayat chat
                                    </h4>

                                    <p class="text-sm text-slate-500 mt-2">
                                        Percakapan antara customer dan freelancer belum tersedia.
                                    </p>
                                </div>
                                @endif
                            </div>
                        </div>

                        {{-- CATATAN MODERATOR --}}
                        <div class="bg-blue-50 rounded-2xl border border-blue-200 p-5">
                            <h3 class="font-bold text-blue-700">
                                Catatan Penting Moderator
                            </h3>

                            <p class="text-sm text-blue-600 mt-3 leading-relaxed">
                                Berdasarkan riwayat komunikasi, bukti pendukung, status revisi, dan status escrow,
                                admin harus memastikan keputusan dilakukan secara objektif. Jika kesalahan berada pada freelancer,
                                dana dapat dikembalikan kepada customer. Jika pekerjaan sudah sesuai, dana dapat dilepaskan kepada freelancer.
                            </p>

                            @if ($selectedDispute->keputusan_admin)
                            <div class="mt-4 p-4 bg-white border border-blue-200 rounded-xl">
                                <p class="text-xs text-slate-500">
                                    Keputusan Admin
                                </p>

                                <p class="text-sm text-slate-700 mt-1">
                                    {{ $selectedDispute->keputusan_admin }}
                                </p>
                            </div>
                            @endif
                        </div>
                    </div>

                    {{-- RIGHT SIDEBAR --}}
                    <div class="lg:col-span-4 space-y-6">

                        {{-- BUKTI --}}
                        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5">
                            <h3 class="font-bold text-slate-900">
                                Bukti Pendukung
                            </h3>

                            <div class="mt-4 space-y-3">
                                @if ($selectedDispute->bukti_dispute)
                                <a href="{{ \App\Services\CloudinaryService::mediaUrl($selectedDispute->bukti_dispute) }}"
                                    target="_blank"
                                    class="flex items-center gap-3 p-3 rounded-xl border border-slate-200 hover:bg-slate-50">
                                    <div class="w-10 h-10 rounded-lg bg-slate-100 flex items-center justify-center">
                                        📄
                                    </div>

                                    <div>
                                        <p class="text-sm font-bold text-slate-900">
                                            Bukti Dispute
                                        </p>
                                        <p class="text-xs text-slate-400">
                                            Klik untuk membuka file
                                        </p>
                                    </div>
                                </a>
                                @else
                                <div class="p-4 border border-dashed border-slate-300 rounded-xl text-sm text-slate-500">
                                    Tidak ada file bukti.
                                </div>
                                @endif
                            </div>
                        </div>

                        {{-- LINI MASA --}}
                        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5">
                            <h3 class="font-bold text-slate-900">
                                Lini Masa Kasus
                            </h3>

                            <div class="mt-5 space-y-5">
                                <div class="flex gap-3">
                                    <div class="w-2.5 h-2.5 rounded-full bg-red-500 mt-1.5"></div>
                                    <div>
                                        <p class="text-sm font-bold text-slate-800">
                                            Dispute Dibuat
                                        </p>
                                        <p class="text-xs text-slate-400">
                                            {{ $selectedDispute->created_at->format('d M Y H:i') }}
                                        </p>
                                    </div>
                                </div>

                                <div class="flex gap-3">
                                    <div class="w-2.5 h-2.5 rounded-full bg-blue-500 mt-1.5"></div>
                                    <div>
                                        <p class="text-sm font-bold text-slate-800">
                                            Status Pesanan
                                        </p>
                                        <p class="text-xs text-slate-400">
                                            {{ strtoupper(str_replace('_', ' ', $pesanan->status_pesanan ?? '-')) }}
                                        </p>
                                    </div>
                                </div>

                                <div class="flex gap-3">
                                    <div class="w-2.5 h-2.5 rounded-full bg-green-500 mt-1.5"></div>
                                    <div>
                                        <p class="text-sm font-bold text-slate-800">
                                            Status Escrow
                                        </p>
                                        <p class="text-xs text-slate-400">
                                            {{ strtoupper(str_replace('_', ' ', $pembayaran->status_escrow ?? '-')) }}
                                        </p>
                                    </div>
                                </div>

                                @if ($selectedDispute->tanggal_diproses)
                                <div class="flex gap-3">
                                    <div class="w-2.5 h-2.5 rounded-full bg-slate-500 mt-1.5"></div>
                                    <div>
                                        <p class="text-sm font-bold text-slate-800">
                                            Keputusan Admin
                                        </p>
                                        <p class="text-xs text-slate-400">
                                            {{ \Carbon\Carbon::parse($selectedDispute->tanggal_diproses)->format('d M Y H:i') }}
                                        </p>
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>

                        {{-- ACTION --}}
                        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5">
                            <div class="space-y-3">
                                <button type="button"
                                    onclick="window.print()"
                                    class="w-full px-4 py-3 rounded-xl border border-slate-300 text-slate-700 font-bold hover:bg-slate-50">
                                    Cetak Laporan
                                </button>

                                <a href="{{ route('admin.disputes.index') }}"
                                    class="block w-full text-center px-4 py-3 rounded-xl border border-slate-300 text-slate-700 font-bold hover:bg-slate-50">
                                    Kembali ke Panel Aduan
                                </a>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
            @else
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-12 text-center">
                <div class="text-4xl mb-3">⚖️</div>

                <h3 class="text-xl font-bold text-slate-900">
                    Belum ada dispute
                </h3>

                <p class="text-sm text-slate-500 mt-2">
                    Dispute dari customer akan muncul di halaman ini.
                </p>
            </div>
            @endif
        </div>

    </div>
</div>
<x-auto-refresh :seconds="30" />
@endsection