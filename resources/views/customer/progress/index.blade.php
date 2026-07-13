@extends('layouts.customer')

@section('title', 'Progress Pesanan - JasaKampus')

@section('content')
<section class="px-6 py-6">
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl font-bold text-slate-900">
                    Progress Pesanan
                </h1>
                <p class="text-sm text-slate-500 mt-1">
                    Pantau perkembangan pekerjaan dari pesanan yang sudah dibayar.
                </p>
            </div>

            <a href="{{ route('customer.order.index') }}"
                class="px-4 py-2 bg-blue-600 text-white rounded-xl text-sm font-bold hover:bg-blue-700">
                Lihat Pesanan
            </a>
        </div>

        @if ($pesanans->count() > 0)
        <div class="space-y-5">
            @foreach ($pesanans as $pesanan)
            @php
            $totalProgress = $pesanan->progressPekerjaans->count();
            $progressPercent = match ($pesanan->status_pesanan) {
            'dibayar' => 10,
            'diproses' => 50,
            'revisi' => 70,
            'menunggu_approve' => 90,
            'selesai' => 100,
            default => 0,
            };
            @endphp

            <div class="border border-slate-200 rounded-2xl p-5 hover:shadow-sm transition">
                <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                    <div>
                        <h2 class="font-bold text-slate-900">
                            #{{ $pesanan->id }} - {{ $pesanan->jasa->nama_jasa ?? '-' }}
                        </h2>

                        <p class="text-sm text-slate-500 mt-1">
                            Freelancer: {{ $pesanan->jasa->freelancer->nama ?? '-' }}
                        </p>
                    </div>

                    <span class="px-4 py-2 rounded-full text-xs font-bold
                                @if ($pesanan->status_pesanan === 'selesai') bg-green-100 text-green-700
                                @elseif ($pesanan->status_pesanan === 'menunggu_approve') bg-blue-100 text-blue-700
                                @elseif ($pesanan->status_pesanan === 'revisi') bg-yellow-100 text-yellow-700
                                @else bg-slate-100 text-slate-700
                                @endif">
                        {{ strtoupper(str_replace('_', ' ', $pesanan->status_pesanan)) }}
                    </span>
                </div>

                <div class="mt-5">
                    <div class="flex items-center justify-between text-sm mb-2">
                        <span class="font-semibold text-slate-700">
                            Progress Pekerjaan
                        </span>
                        <span class="font-bold text-blue-700">
                            {{ $progressPercent }}%
                        </span>
                    </div>

                    <div class="w-full h-3 bg-slate-100 rounded-full overflow-hidden">
                        <div class="h-full bg-blue-600 rounded-full"
                            style="width: {{ $progressPercent }}%">
                        </div>
                    </div>
                </div>

                <div class="mt-5 bg-slate-50 rounded-xl p-4">
                    @if ($totalProgress > 0)
                    <p class="text-sm font-bold text-slate-800 mb-3">
                        Progress Terbaru
                    </p>

                    <div class="space-y-3">
                        @foreach ($pesanan->progressPekerjaans->take(3) as $progress)
                        <div class="border-l-4 border-blue-500 pl-4">
                            <p class="text-sm font-semibold text-slate-800">
                                {{ $progress->judul_progress ?? 'Progress Pekerjaan' }}
                            </p>

                            <p class="text-xs text-slate-500 mt-1">
                                {{ $progress->created_at->format('d M Y, H:i') }}
                            </p>

                            @if (!empty($progress->deskripsi))
                            <p class="text-sm text-slate-600 mt-2">
                                {{ $progress->deskripsi }}
                            </p>
                            @endif
                        </div>
                        @endforeach
                    </div>
                    @else
                    <p class="text-sm text-slate-500">
                        Belum ada progress yang diunggah freelancer.
                    </p>
                    @endif
                </div>

                <div class="mt-5 flex justify-end">
                    <a href="{{ route('customer.order.show', $pesanan->id) }}"
                        class="px-4 py-2 bg-blue-600 text-white rounded-xl text-sm font-bold hover:bg-blue-700">
                        Detail Pesanan
                    </a>
                </div>
            </div>
            @endforeach
        </div>
        @else
        <div class="text-center py-16">
            <p class="text-slate-500">
                Belum ada progress pekerjaan.
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