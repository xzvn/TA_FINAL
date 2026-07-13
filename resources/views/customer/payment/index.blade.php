@extends('layouts.customer')

@section('title', 'Pembayaran - JasaKampus')

@section('content')
<section class="px-6 py-6">
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl font-bold text-slate-900">
                    Riwayat Pembayaran
                </h1>
                <p class="text-sm text-slate-500 mt-1">
                    Daftar pembayaran dari pesanan yang pernah kamu buat.
                </p>
            </div>

            <a href="{{ route('customer.order.index') }}"
                class="px-4 py-2 bg-blue-600 text-white rounded-xl text-sm font-bold hover:bg-blue-700">
                Lihat Pesanan
            </a>
        </div>

        @if ($pembayarans->count() > 0)
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-slate-200 text-left text-slate-500">
                        <th class="py-3 px-4">Order</th>
                        <th class="py-3 px-4">Jasa</th>
                        <th class="py-3 px-4">Freelancer</th>
                        <th class="py-3 px-4">Nominal</th>
                        <th class="py-3 px-4">Transaksi</th>
                        <th class="py-3 px-4">Escrow</th>
                        <th class="py-3 px-4">Tanggal</th>
                        <th class="py-3 px-4">Aksi</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-slate-100">
                    @foreach ($pembayarans as $pembayaran)
                    <tr>
                        <td class="py-4 px-4 font-bold text-slate-900">
                            #{{ $pembayaran->pesanan->id ?? '-' }}
                        </td>

                        <td class="py-4 px-4 text-slate-700">
                            {{ $pembayaran->pesanan->jasa->nama_jasa ?? '-' }}
                        </td>

                        <td class="py-4 px-4 text-slate-700">
                            {{ $pembayaran->pesanan->jasa->freelancer->nama ?? '-' }}
                        </td>

                        <td class="py-4 px-4 font-bold text-blue-700">
                            Rp {{ number_format($pembayaran->gross_amount, 0, ',', '.') }}
                        </td>

                        <td class="py-4 px-4">
                            <span class="px-3 py-1 rounded-full text-xs font-bold bg-blue-100 text-blue-700">
                                {{ strtoupper($pembayaran->transaction_status ?? 'pending') }}
                            </span>
                        </td>

                        <td class="py-4 px-4">
                            <span class="px-3 py-1 rounded-full text-xs font-bold
                                        @if ($pembayaran->status_escrow === 'ditahan') bg-yellow-100 text-yellow-700
                                        @elseif ($pembayaran->status_escrow === 'dicairkan') bg-green-100 text-green-700
                                        @elseif ($pembayaran->status_escrow === 'dikembalikan') bg-red-100 text-red-700
                                        @else bg-slate-100 text-slate-600
                                        @endif">
                                {{ strtoupper(str_replace('_', ' ', $pembayaran->status_escrow ?? '-')) }}
                            </span>
                        </td>

                        <td class="py-4 px-4 text-slate-500">
                            {{ $pembayaran->tanggal_bayar ? \Carbon\Carbon::parse($pembayaran->tanggal_bayar)->format('d M Y, H:i') : '-' }}
                        </td>

                        <td class="py-4 px-4">
                            @if ($pembayaran->pesanan)
                            <a href="{{ route('customer.payment.show', $pembayaran->pesanan->id) }}"
                                class="text-blue-600 font-bold hover:underline">
                                Detail
                            </a>
                            @else
                            <span class="text-slate-400">-</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="text-center py-16">
            <p class="text-slate-500">
                Belum ada data pembayaran.
            </p>

            <a href="{{ route('customer.marketplace') }}"
                class="inline-block mt-4 px-5 py-3 bg-blue-600 text-white rounded-xl font-bold hover:bg-blue-700">
                Cari Jasa
            </a>
        </div>
        @endif
    </div>
</section>
@endsection