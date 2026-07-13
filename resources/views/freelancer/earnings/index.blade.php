@extends('layouts.freelancer')

@section('title', 'Penghasilan Saya - JasaKampus')
@section('page-title', 'Penghasilan Saya')

@section('content')
<div class="max-w-7xl mx-auto space-y-6">

    {{-- HEADER --}}
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
        <div>
            <h1 class="text-2xl md:text-3xl font-bold text-slate-900">
                Penghasilan Saya
            </h1>

            <p class="text-sm text-slate-500 mt-1">
                Pantau penghasilan, pendapatan tertunda, dan riwayat penarikan Anda.
            </p>
        </div>

        <div class="flex flex-wrap gap-3">
            <button type="button"
                class="px-4 py-2 bg-white border border-slate-200 text-slate-700 rounded-xl text-sm font-semibold hover:bg-slate-50">
                Laporan PDF
            </button>

            <a href="{{ route('freelancer.withdrawals.index') }}"
                class="px-4 py-2 bg-blue-600 text-white rounded-xl text-sm font-semibold hover:bg-blue-700">
                Tarik Dana
            </a>
        </div>
    </div>

    {{-- SUMMARY CARDS --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-5">
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5">
            <div class="flex items-center justify-between">
                <span class="w-10 h-10 rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center">
                    💰
                </span>
                <span class="text-xs text-green-600 font-bold">+12%</span>
            </div>

            <p class="text-sm text-slate-500 mt-4">Total Penghasilan</p>
            <h2 class="text-2xl font-bold text-slate-900 mt-1">
                Rp {{ number_format($totalPenghasilan, 0, ',', '.') }}
            </h2>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5">
            <div class="flex items-center justify-between">
                <span class="w-10 h-10 rounded-xl bg-purple-100 text-purple-600 flex items-center justify-center">
                    🏦
                </span>
                <span class="text-xs text-slate-400">Escrow</span>
            </div>

            <p class="text-sm text-slate-500 mt-4">Saldo Tertunda</p>
            <h2 class="text-2xl font-bold text-slate-900 mt-1">
                Rp {{ number_format($saldoDitahan, 0, ',', '.') }}
            </h2>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5">
            <div class="flex items-center justify-between">
                <span class="w-10 h-10 rounded-xl bg-orange-100 text-orange-600 flex items-center justify-center">
                    🧾
                </span>
                <span class="text-xs text-green-600 font-bold">Cair</span>
            </div>

            <p class="text-sm text-slate-500 mt-4">Saldo Tersedia</p>
            <h2 class="text-2xl font-bold text-slate-900 mt-1">
                Rp {{ number_format($saldoCair, 0, ',', '.') }}
            </h2>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5">
            <div class="flex items-center justify-between">
                <span class="w-10 h-10 rounded-xl bg-green-100 text-green-600 flex items-center justify-center">
                    📦
                </span>
                <span class="text-xs text-blue-600 font-bold">Selesai</span>
            </div>

            <p class="text-sm text-slate-500 mt-4">Proyek Selesai</p>
            <h2 class="text-2xl font-bold text-slate-900 mt-1">
                {{ $totalPesananSelesai }} Proyek
            </h2>
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-12 gap-6">

        {{-- LEFT CONTENT --}}
        <div class="xl:col-span-8 space-y-6">

            {{-- ANALISIS PENDAPATAN --}}
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
                <div class="flex items-center justify-between gap-4">
                    <div>
                        <h2 class="text-lg font-bold text-slate-900">
                            Analisis Pendapatan
                        </h2>
                    </div>

                    <div class="flex gap-2 text-xs">
                        <button class="px-3 py-1 bg-slate-100 text-slate-600 rounded-lg font-semibold">Mingguan</button>
                        <button class="px-3 py-1 bg-blue-600 text-white rounded-lg font-semibold">Bulanan</button>
                        <button class="px-3 py-1 bg-slate-100 text-slate-600 rounded-lg font-semibold">Tahunan</button>
                    </div>
                </div>

                <div class="mt-8 h-64 flex items-end gap-5">
                    @foreach ($monthlyPendapatan as $item)
                    @php
                    $height = $item['total'] > 0
                    ? max(18, ($item['total'] / $maxMonthly) * 190)
                    : 12;
                    @endphp

                    <div class="flex-1 flex flex-col items-center justify-end">
                        <div class="w-full max-w-14 bg-blue-300 rounded-t-xl"
                            style="height: {{ $height }}px;">
                        </div>

                        <p class="text-xs text-slate-400 mt-3">
                            {{ $item['bulan'] }}
                        </p>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- STATUS PEKERJAAN AKTIF --}}
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="p-6 border-b border-slate-200 flex items-center justify-between">
                    <div>
                        <h2 class="text-lg font-bold text-slate-900">
                            Status Pekerjaan Aktif
                        </h2>
                    </div>

                    <span class="px-3 py-1 bg-orange-100 text-orange-700 rounded-full text-xs font-bold">
                        {{ $pesanans->whereIn('status_pesanan', ['diproses', 'revisi', 'menunggu_approve'])->count() }} Pekerjaan
                    </span>
                </div>

                @php
                $pekerjaanAktif = $pesanans->whereIn('status_pesanan', ['diproses', 'revisi', 'menunggu_approve'])->take(4);
                @endphp

                @if ($pekerjaanAktif->count() > 0)
                <div class="divide-y divide-slate-100">
                    @foreach ($pekerjaanAktif as $pesanan)
                    <div class="p-5 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                        <div>
                            <p class="text-sm font-bold text-slate-900">
                                #AL-{{ str_pad($pesanan->id, 4, '0', STR_PAD_LEFT) }}
                            </p>

                            <p class="text-sm text-slate-600 mt-1">
                                {{ $pesanan->jasa->nama_jasa ?? '-' }}
                            </p>

                            <p class="text-xs text-slate-400 mt-1">
                                {{ $pesanan->created_at->format('d M Y') }}
                            </p>
                        </div>

                        <div class="text-right">
                            <span class="px-3 py-1 rounded-full text-xs font-bold
                                        @if ($pesanan->status_pesanan === 'diproses') bg-blue-100 text-blue-700
                                        @elseif ($pesanan->status_pesanan === 'revisi') bg-orange-100 text-orange-700
                                        @else bg-purple-100 text-purple-700
                                        @endif">
                                {{ strtoupper(str_replace('_', ' ', $pesanan->status_pesanan)) }}
                            </span>

                            <p class="text-sm font-bold text-blue-600 mt-2">
                                Rp {{ number_format($pesanan->total_harga, 0, ',', '.') }}
                            </p>
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="p-8 text-center text-sm text-slate-500">
                    Tidak ada pekerjaan aktif.
                </div>
                @endif
            </div>

            {{-- RIWAYAT TRANSAKSI --}}
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="p-6 border-b border-slate-200 flex items-center justify-between">
                    <h2 class="text-lg font-bold text-slate-900">
                        Riwayat Transaksi
                    </h2>

                    <button class="px-3 py-1 bg-slate-100 text-slate-600 rounded-lg text-xs font-bold">
                        Filter
                    </button>
                </div>

                @if ($pesanans->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-slate-50 text-slate-500">
                            <tr>
                                <th class="text-left px-6 py-3">ID</th>
                                <th class="text-left px-6 py-3">Tanggal</th>
                                <th class="text-left px-6 py-3">Layanan</th>
                                <th class="text-left px-6 py-3">Jumlah</th>
                                <th class="text-left px-6 py-3">Status</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-slate-100">
                            @foreach ($pesanans as $pesanan)
                            @php
                            $escrow = $pesanan->pembayaran?->status_escrow ?? 'belum_ditahan';

                            $escrowClass = match ($escrow) {
                            'ditahan' => 'bg-blue-100 text-blue-700',
                            'dicairkan' => 'bg-green-100 text-green-700',
                            'dikembalikan' => 'bg-red-100 text-red-700',
                            default => 'bg-slate-100 text-slate-700',
                            };
                            @endphp

                            <tr>
                                <td class="px-6 py-4 font-bold text-blue-600">
                                    #{{ $pesanan->id }}
                                </td>

                                <td class="px-6 py-4 text-slate-500">
                                    {{ $pesanan->created_at->format('d M Y') }}
                                </td>

                                <td class="px-6 py-4 text-slate-700">
                                    {{ $pesanan->jasa->nama_jasa ?? '-' }}
                                </td>

                                <td class="px-6 py-4 font-bold text-slate-900">
                                    Rp {{ number_format($pesanan->total_harga, 0, ',', '.') }}
                                </td>

                                <td class="px-6 py-4">
                                    <span class="px-3 py-1 rounded-full text-xs font-bold {{ $escrowClass }}">
                                        {{ strtoupper(str_replace('_', ' ', $escrow)) }}
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="p-8 text-center text-sm text-slate-500">
                    Belum ada transaksi.
                </div>
                @endif
            </div>

            {{-- RIWAYAT PENARIKAN DANA --}}
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="p-6 border-b border-slate-200">
                    <h2 class="text-lg font-bold text-slate-900">
                        Riwayat Penarikan Dana
                    </h2>
                </div>

                <div class="p-8 text-center">
                    <div class="text-4xl mb-3">🏦</div>
                    <h3 class="font-bold text-slate-900">
                        Belum ada riwayat penarikan
                    </h3>
                    <p class="text-sm text-slate-500 mt-2">
                        Riwayat akan muncul setelah fitur pencairan dana diaktifkan.
                    </p>
                </div>
            </div>

        </div>

        {{-- RIGHT SIDEBAR --}}
        <div class="xl:col-span-4 space-y-6">

            {{-- RINGKASAN FINANSIAL --}}
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
                <h3 class="font-bold text-slate-900">
                    Ringkasan Finansial
                </h3>

                <div class="mt-5 space-y-4">
                    <div class="flex justify-between">
                        <span class="text-sm text-slate-500">Bulan ini</span>
                        <span class="text-sm font-bold text-slate-900">
                            Rp {{ number_format($saldoCair, 0, ',', '.') }}
                        </span>
                    </div>

                    <div class="flex justify-between">
                        <span class="text-sm text-slate-500">Dana cair</span>
                        <span class="text-sm font-bold text-green-600">
                            Rp {{ number_format($saldoCair, 0, ',', '.') }}
                        </span>
                    </div>

                    <div class="flex justify-between">
                        <span class="text-sm text-slate-500">Dana tertunda</span>
                        <span class="text-sm font-bold text-blue-600">
                            Rp {{ number_format($saldoDitahan, 0, ',', '.') }}
                        </span>
                    </div>

                    <div class="pt-4 border-t border-slate-200 flex justify-between">
                        <span class="text-sm text-slate-500">Target bulanan</span>
                        <span class="text-sm font-bold text-blue-600">
                            Rp {{ number_format($targetBulanan, 0, ',', '.') }}
                        </span>
                    </div>
                </div>
            </div>

            {{-- TARIK DANA --}}
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
                <h3 class="font-bold text-slate-900">
                    Tarik Dana
                </h3>

                <p class="text-sm text-slate-500 mt-2">
                    Saldo tersedia
                </p>

                <p class="text-2xl font-bold text-blue-600 mt-1">
                    Rp {{ number_format($saldoCair, 0, ',', '.') }}
                </p>

                <div class="mt-5">
                    <div class="flex justify-between text-xs mb-2">
                        <span class="text-slate-500">Target Bulanan</span>
                        <span class="font-bold text-blue-600">{{ $persenTarget }}%</span>
                    </div>

                    <div class="w-full h-3 bg-slate-100 rounded-full overflow-hidden">
                        <div class="h-3 bg-blue-600 rounded-full"
                            style="width: {{ $persenTarget }}%;">
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-4 gap-2 mt-5 text-xs">
                    <button class="py-2 bg-slate-100 rounded-lg font-semibold">25%</button>
                    <button class="py-2 bg-slate-100 rounded-lg font-semibold">50%</button>
                    <button class="py-2 bg-slate-100 rounded-lg font-semibold">75%</button>
                    <button class="py-2 bg-slate-100 rounded-lg font-semibold">100%</button>
                </div>

                <a href="{{ route('freelancer.withdrawals.index') }}"
                    class="block text-center mt-5 w-full px-5 py-3 bg-blue-600 text-white rounded-xl font-bold hover:bg-blue-700">
                    Permintaan Penarikan
                </a>
            </div>

            {{-- LAYANAN TERLARIS --}}
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
                <h3 class="font-bold text-slate-900">
                    Layanan Terlaris
                </h3>

                @php
                $layananTerlaris = $pesanans
                ->groupBy('id_jasa')
                ->map(fn ($items) => [
                'nama' => $items->first()->jasa->nama_jasa ?? '-',
                'total' => $items->sum('total_harga'),
                'jumlah' => $items->count(),
                ])
                ->sortByDesc('total')
                ->take(3);
                @endphp

                @if ($layananTerlaris->count() > 0)
                <div class="mt-5 space-y-4">
                    @foreach ($layananTerlaris as $layanan)
                    <div class="flex items-center justify-between gap-3">
                        <div>
                            <p class="text-sm font-bold text-slate-900">
                                {{ $layanan['nama'] }}
                            </p>
                            <p class="text-xs text-slate-500">
                                {{ $layanan['jumlah'] }} pesanan
                            </p>
                        </div>

                        <p class="text-sm font-bold text-blue-600">
                            Rp {{ number_format($layanan['total'], 0, ',', '.') }}
                        </p>
                    </div>
                    @endforeach
                </div>
                @else
                <p class="text-sm text-slate-500 mt-5">
                    Belum ada layanan.
                </p>
                @endif
            </div>

            {{-- UPDATE FINANSIAL --}}
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
                <h3 class="font-bold text-slate-900">
                    Update Finansial
                </h3>

                <div class="mt-5 space-y-4">
                    <div class="p-4 bg-green-50 border border-green-200 rounded-xl">
                        <p class="text-sm font-bold text-green-700">
                            Pendapatan Bertambah
                        </p>
                        <p class="text-xs text-green-600 mt-1">
                            Pendapatan akan bertambah ketika customer menyetujui pekerjaan.
                        </p>
                    </div>

                    <div class="p-4 bg-blue-50 border border-blue-200 rounded-xl">
                        <p class="text-sm font-bold text-blue-700">
                            Dana Escrow
                        </p>
                        <p class="text-xs text-blue-600 mt-1">
                            Dana ditahan sampai pekerjaan selesai dan disetujui customer.
                        </p>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection