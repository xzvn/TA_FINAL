@extends('layouts.freelancer')

@section('title', 'Chat Freelancer - JasaKampus')
@section('page-title', 'Chat')

@section('content')
<div class="mb-8">
    <h1 class="text-3xl font-bold text-slate-900">
        Chat Customer
    </h1>
    <p class="text-sm text-slate-500 mt-1">
        Lihat pesan dari customer yang tertarik dengan jasa Anda.
    </p>
</div>

<div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
    <div class="px-6 py-5 border-b border-slate-200">
        <h3 class="font-semibold text-slate-800">
            Daftar Percakapan
        </h3>
        <p class="text-sm text-slate-500">
            Percakapan akan muncul setelah customer mengirim pesan melalui detail jasa.
        </p>
    </div>

    @if ($conversations->count() > 0)
    <div class="divide-y divide-slate-100">
        @foreach ($conversations as $chat)
        <a href="{{ route('freelancer.chat.show', [$chat->jasa->id, $chat->customer->id]) }}"
            class="block px-6 py-5 hover:bg-slate-50 transition">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-full bg-blue-600 text-white flex items-center justify-center font-bold">
                        {{ strtoupper(substr($chat->customer->nama ?? 'C', 0, 1)) }}
                    </div>

                    <div>
                        <h4 class="font-bold text-slate-900">
                            {{ $chat->customer->nama ?? 'Customer' }}
                        </h4>

                        <p class="text-sm text-slate-500">
                            Jasa: {{ $chat->jasa->nama_jasa ?? '-' }}
                        </p>

                        <p class="text-sm text-slate-600 mt-1 line-clamp-1">
                            {{ $chat->pesan }}
                        </p>
                    </div>
                </div>

                <div class="text-right">
                    <p class="text-xs text-slate-400">
                        {{ ($chat->waktu_kirim ?? $chat->created_at)->timezone('Asia/Jakarta')->format('H:i') }} WIB
                    </p>

                    <span class="inline-block mt-2 px-3 py-1 bg-green-100 text-green-700 rounded-full text-xs font-bold">
                        Masuk
                    </span>
                </div>
            </div>
        </a>
        @endforeach
    </div>
    @else
    <div class="p-10 text-center">
        <div class="w-16 h-16 mx-auto rounded-full bg-blue-100 text-blue-600 flex items-center justify-center text-2xl mb-4">
            💬
        </div>

        <h3 class="text-lg font-bold text-slate-900">
            Belum ada chat masuk
        </h3>

        <p class="text-sm text-slate-500 mt-2">
            Chat akan muncul di sini setelah customer menghubungi Anda dari halaman detail jasa.
        </p>
    </div>
    @endif
</div>
@endsection