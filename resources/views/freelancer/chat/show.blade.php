@extends('layouts.freelancer')

@section('title', 'Detail Chat - JasaKampus')
@section('page-title', 'Detail Chat')

@section('content')
<div class="max-w-5xl mx-auto">
    <a href="{{ route('freelancer.chat.index') }}"
        class="inline-block mb-6 text-blue-600 font-semibold hover:underline">
        ← Kembali ke Daftar Chat
    </a>

    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">

        <div class="px-6 py-5 border-b border-slate-200 flex items-center justify-between">
            <div>
                <h1 class="text-xl font-bold text-slate-900">
                    Chat dengan {{ $customer->nama }}
                </h1>
                <p class="text-sm text-slate-500 mt-1">
                    Jasa: {{ $jasa->nama_jasa }}
                </p>
            </div>

            <span class="px-3 py-1 bg-blue-100 text-blue-700 rounded-full text-xs font-bold">
                Customer
            </span>
        </div>

        @if (session('success'))
        <div class="m-6 px-5 py-4 bg-green-100 text-green-700 rounded-xl border border-green-200">
            {{ session('success') }}
        </div>
        @endif

        <div class="p-6 h-[420px] overflow-y-auto bg-slate-50 space-y-4">
            @forelse ($chats as $chat)
            @if ($chat->pengirim_id === Auth::id())
            <div class="flex justify-end">
                <div class="max-w-md bg-blue-600 text-white rounded-2xl rounded-tr-none px-5 py-3">
                    <p class="text-sm">
                        {{ $chat->pesan }}
                    </p>

                    @if ($chat->lampiran)
                    <a href="{{ \App\Services\CloudinaryService::mediaUrl($chat->lampiran) }}"
                        target="_blank"
                        class="block mt-2 text-xs underline">
                        Lihat Lampiran
                    </a>
                    @endif

                    <p class="text-[11px] text-blue-100 mt-2">
                        {{ $chat->created_at->format('d M Y H:i') }}
                    </p>
                </div>
            </div>
            @else
            <div class="flex justify-start">
                <div class="max-w-md bg-white border border-slate-200 rounded-2xl rounded-tl-none px-5 py-3">
                    <p class="text-xs font-semibold text-slate-500 mb-1">
                        {{ $chat->pengirim->nama ?? 'Customer' }}
                    </p>

                    <p class="text-sm text-slate-700">
                        {{ $chat->pesan }}
                    </p>

                    @if ($chat->lampiran)
                    <a href="{{ \App\Services\CloudinaryService::mediaUrl($chat->lampiran) }}"
                        target="_blank"
                        class="block mt-2 text-xs text-blue-600 underline">
                        Lihat Lampiran
                    </a>
                    @endif

                    <p class="text-[11px] text-slate-400 mt-2">
                        {{ $chat->created_at->format('d M Y H:i') }}
                    </p>
                </div>
            </div>
            @endif
            @empty
            <div class="h-full flex items-center justify-center text-center">
                <div>
                    <h3 class="text-lg font-bold text-slate-900">
                        Belum ada pesan
                    </h3>
                    <p class="text-sm text-slate-500 mt-2">
                        Percakapan akan muncul di sini.
                    </p>
                </div>
            </div>
            @endforelse
        </div>

        <div class="p-6 border-t border-slate-200 bg-white">
            <form method="POST"
                action="{{ route('freelancer.chat.store', [$jasa->id, $customer->id]) }}"
                enctype="multipart/form-data"
                class="space-y-4">
                @csrf

                <div>
                    <textarea name="pesan"
                        rows="3"
                        class="w-full rounded-lg border-slate-300 focus:border-blue-500 focus:ring-blue-500"
                        placeholder="Tulis balasan untuk customer..."
                        required>{{ old('pesan') }}</textarea>

                    @error('pesan')
                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center justify-between gap-4">
                    <div class="flex-1">
                        <input type="file"
                            name="lampiran"
                            class="w-full rounded-lg border border-slate-300 p-2 text-sm">

                        @error('lampiran')
                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <button type="submit"
                        class="px-6 py-3 bg-blue-600 text-white rounded-lg font-semibold hover:bg-blue-700">
                        Kirim Balasan
                    </button>
                </div>
            </form>
        </div>

    </div>
</div>
@endsection