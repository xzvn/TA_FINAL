@extends('layouts.customer')

@section('title', 'Ulasan Saya - JasaKampus')

@section('content')
<section class="px-6 py-6">
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl font-bold text-slate-900">
                    Ulasan Saya
                </h1>
                <p class="text-sm text-slate-500 mt-1">
                    Daftar ulasan yang pernah kamu berikan kepada freelancer.
                </p>
            </div>

            <a href="{{ route('customer.order.index') }}"
                class="px-4 py-2 bg-blue-600 text-white rounded-xl text-sm font-bold hover:bg-blue-700">
                Lihat Pesanan
            </a>
        </div>

        @if ($reviews->count() > 0)
        <div class="space-y-5">
            @foreach ($reviews as $review)
            <div class="border border-slate-200 rounded-2xl p-5 hover:shadow-sm transition">
                <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-4">
                    <div>
                        <h2 class="font-bold text-slate-900">
                            {{ $review->pesanan->jasa->nama_jasa ?? 'Jasa tidak ditemukan' }}
                        </h2>

                        <p class="text-sm text-slate-500 mt-1">
                            Freelancer:
                            <span class="font-semibold text-slate-700">
                                {{ $review->freelancer->nama ?? $review->pesanan->jasa->freelancer->nama ?? '-' }}
                            </span>
                        </p>

                        <p class="text-xs text-slate-400 mt-1">
                            Order #{{ $review->id_pesanan }}
                        </p>
                    </div>

                    <div class="text-left lg:text-right">
                        <div class="inline-flex items-center gap-1 px-4 py-2 rounded-full bg-yellow-100 text-yellow-700 font-bold">
                            ★ {{ number_format($review->rating, 1) }}
                        </div>

                        <p class="text-xs text-slate-400 mt-2">
                            {{ $review->created_at->format('d M Y, H:i') }}
                        </p>
                    </div>
                </div>

                <div class="mt-5 bg-slate-50 rounded-xl p-4">
                    <p class="text-sm text-slate-700 leading-relaxed">
                        {{ $review->komentar ?? $review->ulasan ?? 'Tidak ada komentar.' }}
                    </p>
                </div>

                <div class="mt-5 flex justify-end">
                    @if ($review->pesanan)
                    <a href="{{ route('customer.order.show', $review->pesanan->id) }}"
                        class="px-4 py-2 bg-blue-600 text-white rounded-xl text-sm font-bold hover:bg-blue-700">
                        Detail Pesanan
                    </a>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
        @else
        <div class="text-center py-16">
            <p class="text-slate-500">
                Kamu belum pernah memberikan ulasan.
            </p>

            <a href="{{ route('customer.order.index') }}"
                class="inline-block mt-4 px-5 py-3 bg-blue-600 text-white rounded-xl font-bold hover:bg-blue-700">
                Lihat Pesanan
            </a>
        </div>
        @endif
    </div>
</section>
@endsection