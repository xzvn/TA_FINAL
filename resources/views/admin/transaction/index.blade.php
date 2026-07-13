@extends('layouts.admin')

@section('title', 'Pemantauan Escrow - Admin JasaKampus')

@section('content')
<div class="max-w-7xl mx-auto space-y-6">

    {{-- HEADER --}}
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
        <div>
            <h1 class="text-2xl md:text-3xl font-bold text-slate-900">
                Pemantauan Escrow
            </h1>

            <p class="text-sm text-slate-500 mt-1">
                Audit keuangan real-time untuk semua transaksi platform. Pantau arus kas masuk dan dana yang ditahan dengan transparansi penuh.
            </p>
        </div>

        <div class="flex gap-3">
            <button class="px-4 py-2 bg-white border border-slate-200 text-slate-700 rounded-xl text-sm font-semibold hover:bg-slate-50">
                Export CSV
            </button>

            <button class="px-4 py-2 bg-blue-600 text-white rounded-xl text-sm font-semibold hover:bg-blue-700">
                Segarkan Data
            </button>
        </div>
    </div>

    {{-- SUMMARY --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-5">
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5">
            <div class="w-10 h-10 rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center">
                🧾
            </div>

            <p class="text-sm text-slate-500 mt-4">
                Total Escrow Volume
            </p>

            <h2 class="text-2xl font-bold text-slate-900 mt-1">
                Rp {{ number_format($totalEscrowVolume, 0, ',', '.') }}
            </h2>

            <p class="text-xs text-slate-400 mt-2">
                Dana aktif yang sedang ditahan.
            </p>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5">
            <div class="w-10 h-10 rounded-xl bg-purple-100 text-purple-600 flex items-center justify-center">
                📈
            </div>

            <p class="text-sm text-slate-500 mt-4">
                Total Nilai Transaksi
            </p>

            <h2 class="text-2xl font-bold text-slate-900 mt-1">
                Rp {{ number_format($totalNilaiTransaksi, 0, ',', '.') }}
            </h2>

            <p class="text-xs text-green-600 font-semibold mt-2">
                Mengikuti filter yang aktif.
            </p>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5">
            <div class="w-10 h-10 rounded-xl bg-orange-100 text-orange-600 flex items-center justify-center">
                ⏳
            </div>

            <p class="text-sm text-slate-500 mt-4">
                Transaksi Menunggu
            </p>

            <h2 class="text-2xl font-bold text-slate-900 mt-1">
                {{ $transaksiMenunggu }} Proyek
            </h2>

            <p class="text-xs text-slate-400 mt-2">
                Perlu verifikasi pembayaran atau approval.
            </p>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5">
            <div class="w-10 h-10 rounded-xl bg-slate-100 text-slate-600 flex items-center justify-center">
                🛡️
            </div>

            <p class="text-sm text-slate-500 mt-4">
                Tingkat Keamanan
            </p>

            <h2 class="text-2xl font-bold text-slate-900 mt-1">
                {{ $tingkatKeamanan }}%
            </h2>

            <p class="text-xs text-slate-400 mt-2">
                Audit otomatis berjalan lancar.
            </p>
        </div>
    </div>

    {{-- TRANSACTION TABLE --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="p-6 border-b border-slate-200 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div>
                <h2 class="text-lg font-bold text-slate-900">
                    Daftar Transaksi Audit
                </h2>

                <p class="text-sm text-slate-500 mt-1">
                    Total {{ $pesanans->count() }} transaksi sesuai filter.
                </p>
            </div>

            <form method="GET" action="{{ route('admin.transactions.index') }}"
                class="grid grid-cols-1 md:grid-cols-5 gap-3 w-full lg:w-auto">

                <select name="status_transaksi"
                    class="rounded-xl border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value=""> Status Pembayaran</option>
                    <option value="pending" {{ request('status_transaksi') === 'pending' ? 'selected' : '' }}>
                        Pending
                    </option>
                    <option value="settlement" {{ request('status_transaksi') === 'settlement' ? 'selected' : '' }}>
                        Settlement
                    </option>
                    <option value="cancel" {{ request('status_transaksi') === 'cancel' ? 'selected' : '' }}>
                        Cancel
                    </option>
                    <option value="deny" {{ request('status_transaksi') === 'deny' ? 'selected' : '' }}>
                        Deny
                    </option>
                    <option value="expire" {{ request('status_transaksi') === 'expire' ? 'selected' : '' }}>
                        Expire
                    </option>
                </select>

                <select name="status_escrow"
                    class="rounded-xl border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="">Status Escrow</option>
                    <option value="belum_ditahan" {{ request('status_escrow') === 'belum_ditahan' ? 'selected' : '' }}>
                        Belum Ditahan
                    </option>
                    <option value="ditahan" {{ request('status_escrow') === 'ditahan' ? 'selected' : '' }}>
                        Ditahan
                    </option>
                    <option value="dicairkan" {{ request('status_escrow') === 'dicairkan' ? 'selected' : '' }}>
                        Dicairkan
                    </option>
                    <option value="dikembalikan" {{ request('status_escrow') === 'dikembalikan' ? 'selected' : '' }}>
                        Dikembalikan
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

                    <a href="{{ route('admin.transactions.index') }}"
                        class="px-4 py-2 bg-slate-100 text-slate-700 rounded-xl text-sm font-bold hover:bg-slate-200">
                        Reset
                    </a>
                </div>
            </form>
        </div>

        @if ($pesanans->count() > 0)
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 text-slate-500">
                    <tr>
                        <th class="text-left px-6 py-3">Nomor Faktur</th>
                        <th class="text-left px-6 py-3">Customer</th>
                        <th class="text-left px-6 py-3">Freelancer</th>
                        <th class="text-left px-6 py-3">Jumlah</th>
                        <th class="text-left px-6 py-3">Status Pembayaran</th>
                        <th class="text-left px-6 py-3">Status Escrow</th>
                        <th class="text-left px-6 py-3">Aksi</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-slate-100">
                    @foreach ($pesanans as $pesanan)
                    @php
                    $pembayaran = $pesanan->pembayaran;
                    $escrow = $pembayaran?->status_escrow ?? 'belum_ditahan';
                    $paymentStatus = $pembayaran?->transaction_status ?? 'pending';

                    $paymentClass = match ($paymentStatus) {
                    'settlement' => 'bg-green-100 text-green-700',
                    'pending' => 'bg-yellow-100 text-yellow-700',
                    'cancel', 'deny', 'expire' => 'bg-red-100 text-red-700',
                    default => 'bg-slate-100 text-slate-700',
                    };

                    $escrowClass = match ($escrow) {
                    'ditahan' => 'bg-blue-100 text-blue-700',
                    'dicairkan' => 'bg-green-100 text-green-700',
                    'dikembalikan' => 'bg-red-100 text-red-700',
                    default => 'bg-slate-100 text-slate-700',
                    };
                    @endphp

                    <tr class="hover:bg-slate-50">
                        <td class="px-6 py-4">
                            <p class="font-bold text-blue-600">
                                #REV-{{ now()->year }}-{{ str_pad($pesanan->id, 3, '0', STR_PAD_LEFT) }}
                            </p>
                            <p class="text-xs text-slate-400">
                                {{ $pesanan->created_at->format('d M Y') }}
                            </p>
                        </td>

                        <td class="px-6 py-4">
                            <p class="font-bold text-slate-900">
                                {{ $pesanan->customer->nama ?? '-' }}
                            </p>
                            <p class="text-xs text-slate-400">
                                Customer
                            </p>
                        </td>

                        <td class="px-6 py-4">
                            <p class="font-bold text-slate-900">
                                {{ $pesanan->freelancer->nama ?? '-' }}
                            </p>
                            <p class="text-xs text-slate-400">
                                Freelancer
                            </p>
                        </td>

                        <td class="px-6 py-4">
                            <p class="font-bold text-slate-900">
                                Rp {{ number_format($pesanan->total_harga, 0, ',', '.') }}
                            </p>
                        </td>

                        <td class="px-6 py-4">
                            <span class="px-3 py-1 rounded-full text-xs font-bold {{ $paymentClass }}">
                                {{ strtoupper($paymentStatus) }}
                            </span>
                        </td>

                        <td class="px-6 py-4">
                            <span class="px-3 py-1 rounded-full text-xs font-bold {{ $escrowClass }}">
                                {{ strtoupper(str_replace('_', ' ', $escrow)) }}
                            </span>
                        </td>

                        <td class="px-6 py-4">
                            <a href="{{ route('admin.transactions.show', $pesanan->id) }}"
                                class="text-sm font-bold text-blue-600 hover:underline">
                                Lihat Detail
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="p-10 text-center">
            <div class="text-4xl mb-3">💳</div>
            <h3 class="font-bold text-slate-900">
                Belum ada transaksi
            </h3>
            <p class="text-sm text-slate-500 mt-2">
                Transaksi akan muncul setelah customer melakukan pembayaran.
            </p>
        </div>
        @endif
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-12 gap-6">

        {{-- CHART --}}
        <div class="xl:col-span-8 bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
            <h2 class="text-lg font-bold text-slate-900">
                Analitik Volume Mingguan
            </h2>

            <div class="mt-8 h-64 flex items-end gap-5">
                @foreach ($weeklyVolumes as $item)
                @php
                $height = $item['total'] > 0
                ? max(18, ($item['total'] / $maxWeeklyVolume) * 190)
                : 18;
                @endphp

                <div class="flex-1 flex flex-col items-center justify-end">
                    <div class="w-full max-w-14 bg-blue-300 rounded-t-xl"
                        style="height: {{ $height }}px;">
                    </div>

                    <p class="text-xs text-slate-400 mt-3">
                        {{ $item['label'] }}
                    </p>
                </div>
                @endforeach
            </div>
        </div>

        {{-- SECURITY LOG --}}
        <div class="xl:col-span-4 bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
            <h2 class="text-lg font-bold text-slate-900">
                Log Keamanan Terbaru
            </h2>

            <div class="mt-5 space-y-4">
                <div class="p-4 bg-green-50 border border-green-200 rounded-xl">
                    <p class="text-sm font-bold text-green-700">
                        Verifikasi Identitas Selesai
                    </p>
                    <p class="text-xs text-green-600 mt-1">
                        Freelancer berhasil diverifikasi admin.
                    </p>
                </div>

                <div class="p-4 bg-blue-50 border border-blue-200 rounded-xl">
                    <p class="text-sm font-bold text-blue-700">
                        Dana Escrow Dilepaskan
                    </p>
                    <p class="text-xs text-blue-600 mt-1">
                        Transaksi selesai dan dana berhasil dicairkan.
                    </p>
                </div>

                <div class="p-4 bg-orange-50 border border-orange-200 rounded-xl">
                    <p class="text-sm font-bold text-orange-700">
                        Login Tidak Biasa
                    </p>
                    <p class="text-xs text-orange-600 mt-1">
                        Aktivitas login admin perlu dipantau.
                    </p>
                </div>
            </div>

            <button class="mt-5 w-full px-5 py-3 bg-slate-100 text-slate-700 rounded-xl font-bold hover:bg-slate-200">
                Lihat Semua Log
            </button>
        </div>
    </div>
</div>
@endsection