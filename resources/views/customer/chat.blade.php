@extends('layouts.customer')

@section('title', 'Room Chat Customer - JasaKampus')

@section('content')
@php
$totalPesan = $chats->count();
$lampiranChats = $chats->filter(fn($chat) => !empty($chat->lampiran));
@endphp

<section class="px-6 py-6">
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-slate-900">
            Room Chat Customer
        </h1>
        <p class="text-sm text-slate-500 mt-1">
            Diskusikan kebutuhan proyek dengan freelancer sebelum melanjutkan order.
        </p>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-12 gap-6">

        {{-- LEFT PANEL --}}
        <aside class="xl:col-span-3 space-y-5">
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="px-5 py-4 border-b border-slate-200">
                    <h2 class="text-xs font-bold tracking-wide text-slate-500 uppercase">
                        Informasi Freelancer
                    </h2>
                </div>

                <div class="p-5">
                    <div class="flex items-start gap-3 p-4 rounded-xl border border-slate-200 bg-slate-50">
                        <div class="w-12 h-12 rounded-full bg-blue-600 text-white flex items-center justify-center font-bold">
                            {{ strtoupper(substr($jasa->freelancer->nama ?? 'F', 0, 1)) }}
                        </div>

                        <div>
                            <h3 class="font-bold text-slate-900">
                                {{ $jasa->freelancer->nama ?? 'Freelancer' }}
                            </h3>

                            <p class="text-xs text-slate-500 mt-1">
                                {{ $jasa->freelancer->verifikasiFreelancer->program_studi ?? 'Mahasiswa' }}
                            </p>

                            <p class="text-xs text-slate-500">
                                {{ $jasa->freelancer->verifikasiFreelancer->universitas ?? 'Universitas' }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="px-5 py-4 border-b border-slate-200">
                    <h2 class="text-xs font-bold tracking-wide text-slate-500 uppercase">
                        Cakupan Proyek
                    </h2>
                </div>

                <div class="p-5 space-y-4">
                    <div class="p-4 rounded-xl border border-slate-200 bg-slate-50">
                        <p class="text-sm font-semibold text-slate-800 mb-2">
                            Layanan
                        </p>
                        <p class="text-sm text-slate-600 leading-relaxed">
                            {{ $jasa->nama_jasa }}
                        </p>
                    </div>

                    <div class="p-4 rounded-xl border border-slate-200 bg-slate-50">
                        <p class="text-sm font-semibold text-slate-800 mb-2">
                            Deskripsi Singkat
                        </p>
                        <p class="text-sm text-slate-600 leading-relaxed line-clamp-5">
                            {{ $jasa->deskripsi }}
                        </p>
                    </div>

                    <div class="p-4 rounded-xl border border-slate-200 bg-slate-50">
                        <p class="text-sm font-semibold text-slate-800 mb-2">
                            Estimasi
                        </p>
                        <p class="text-sm text-slate-600">
                            {{ $jasa->estimasi_pengerjaan }}
                        </p>
                    </div>

                    <div class="p-4 rounded-xl border border-slate-200 bg-slate-50">
                        <p class="text-sm font-semibold text-slate-800 mb-2">
                            Anggaran
                        </p>
                        <p class="text-2xl font-bold text-blue-600">
                            Rp {{ number_format($jasa->harga, 0, ',', '.') }}
                        </p>
                        <p class="text-xs text-slate-500 mt-1">
                            Harga jasa awal. Final order mengikuti kesepakatan.
                        </p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="px-5 py-4 border-b border-slate-200">
                    <h2 class="text-xs font-bold tracking-wide text-slate-500 uppercase">
                        Sumber Daya Bersama
                    </h2>
                </div>

                <div class="p-5 space-y-3">
                    @forelse ($lampiranChats as $chat)
                    <a href="{{ \App\Services\CloudinaryService::mediaUrl($chat->lampiran) }}"
                        target="_blank"
                        class="flex items-center justify-between gap-3 p-3 rounded-xl border border-slate-200 bg-slate-50 hover:bg-slate-100">
                        <div class="min-w-0">
                            <p class="text-sm font-semibold text-slate-800 truncate">
                                Lampiran Chat
                            </p>
                            <p class="text-xs text-slate-500">
                                {{ $chat->created_at->format('d M Y H:i') }}
                            </p>
                        </div>

                        <span class="text-blue-600 text-sm font-semibold">
                            Buka
                        </span>
                    </a>
                    @empty
                    <div class="p-4 rounded-xl border border-slate-200 bg-slate-50 text-sm text-slate-500">
                        Belum ada file yang dibagikan.
                    </div>
                    @endforelse
                </div>
            </div>
        </aside>

        {{-- CENTER PANEL --}}
        <div class="xl:col-span-6">
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden min-h-[760px] flex flex-col">

                <div class="px-6 py-5 border-b border-slate-200 flex items-start justify-between gap-4">
                    <div>
                        <h2 class="text-2xl font-bold text-blue-700">
                            {{ $jasa->nama_jasa }}
                        </h2>
                        <p class="text-sm text-slate-500 mt-1">
                            <span class="text-green-600 font-semibold">● Sedang Diskusi Pra-Order</span>
                            · Estimasi selesai {{ $jasa->estimasi_pengerjaan }}
                        </p>
                    </div>

                    <div class="text-right">
                        <p class="text-xs text-slate-500">Total Pesan</p>
                        <p class="text-lg font-bold text-slate-900">{{ $totalPesan }}</p>
                    </div>
                </div>

                <div id="chat-messages"
                    data-current-user-id="{{ Auth::id() }}"
                    data-messages-url="{{ route('customer.chat.messages', $jasa->id) }}"
                    class="flex-1 p-6 bg-slate-50 overflow-y-auto space-y-5 min-h-[500px]">
                    @forelse ($chats as $chat)
                    @if ($chat->pengirim_id === Auth::id())
                    <div class="flex justify-end">
                        <div class="max-w-lg">
                            <div class="flex justify-end mb-1">
                                <span class="px-2 py-1 text-[10px] font-bold text-blue-700 bg-blue-100 rounded-full">
                                    SAYA
                                </span>
                            </div>

                            <div class="bg-blue-600 text-white rounded-2xl rounded-tr-md px-5 py-4 shadow-sm">
                                <p class="text-sm leading-relaxed">
                                    {{ $chat->pesan }}
                                </p>

                                @if ($chat->lampiran)
                                <a href="{{ \App\Services\CloudinaryService::mediaUrl($chat->lampiran) }}"
                                    target="_blank"
                                    class="inline-block mt-3 text-xs underline">
                                    Lihat Lampiran
                                </a>
                                @endif
                            </div>

                            <p class="text-[11px] text-slate-400 mt-2 text-right">
                                {{ $chat->created_at->format('H:i') }}
                            </p>
                        </div>
                    </div>
                    @else
                    <div class="flex justify-start">
                        <div class="max-w-lg">
                            <div class="flex items-center gap-2 mb-1">
                                <div class="w-6 h-6 rounded-full bg-slate-700 text-white flex items-center justify-center text-[10px] font-bold">
                                    {{ strtoupper(substr($chat->pengirim->nama ?? 'F', 0, 1)) }}
                                </div>

                                <span class="text-xs font-semibold text-slate-500">
                                    {{ $chat->pengirim->nama ?? 'Freelancer' }}
                                </span>
                            </div>

                            <div class="bg-slate-200 text-slate-800 rounded-2xl rounded-tl-md px-5 py-4 shadow-sm">
                                <p class="text-sm leading-relaxed">
                                    {{ $chat->pesan }}
                                </p>

                                @if ($chat->lampiran)
                                <a href="{{ \App\Services\CloudinaryService::mediaUrl($chat->lampiran) }}"
                                    target="_blank"
                                    class="inline-block mt-3 text-xs text-blue-600 underline">
                                    Lihat Lampiran
                                </a>
                                @endif
                            </div>

                            <p class="text-[11px] text-slate-400 mt-2">
                                {{ $chat->created_at->format('H:i') }}
                            </p>
                        </div>
                    </div>
                    @endif
                    @empty
                    <div class="h-full flex items-center justify-center text-center">
                        <div>
                            <div class="w-16 h-16 mx-auto rounded-full bg-blue-100 text-blue-600 flex items-center justify-center text-2xl mb-4">
                                💬
                            </div>

                            <h3 class="text-lg font-bold text-slate-900">
                                Belum ada pesan
                            </h3>

                            <p class="text-sm text-slate-500 mt-2">
                                Mulai diskusi pertama dengan freelancer dari room chat ini.
                            </p>
                        </div>
                    </div>
                    @endforelse
                </div>

                <div class="p-5 border-t border-slate-200 bg-white">
                    <form method="POST"
                        action="{{ route('customer.chat.store', $jasa->id) }}"
                        enctype="multipart/form-data"
                        class="space-y-4">
                        @csrf

                        <div class="rounded-2xl border border-slate-300 bg-white overflow-hidden">
                            <div class="px-4 py-3 border-b border-slate-200 flex items-center gap-3 text-slate-500 text-sm">
                                <button type="button" class="font-bold hover:text-blue-600">B</button>
                                <button type="button" class="italic hover:text-blue-600">I</button>
                                <button type="button" class="hover:text-blue-600">↻</button>
                                <button type="button" class="hover:text-blue-600">🔗</button>
                                <button type="button" class="hover:text-blue-600">☺</button>
                            </div>

                            <textarea name="pesan"
                                rows="4"
                                class="w-full border-0 focus:ring-0 text-sm px-4 py-4 resize-none"
                                placeholder="Ketik pesan Anda atau lampirkan file..."
                                required>{{ old('pesan') }}</textarea>
                        </div>

                        @error('pesan')
                        <p class="text-sm text-red-600">{{ $message }}</p>
                        @enderror

                        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                            <div class="flex-1">
                                <input type="file"
                                    name="lampiran"
                                    class="w-full rounded-xl border border-slate-300 p-3 text-sm bg-slate-50">

                                @error('lampiran')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <button type="submit"
                                class="inline-flex items-center justify-center px-6 py-3 bg-blue-600 text-white rounded-full font-bold hover:bg-blue-700 shadow-sm">
                                Kirim Pesan
                            </button>
                        </div>
                    </form>
                </div>

            </div>
        </div>

        {{-- RIGHT PANEL --}}
        <aside class="xl:col-span-3 space-y-5">
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="px-5 py-4 border-b border-slate-200">
                    <h2 class="text-xs font-bold tracking-wide text-slate-500 uppercase">
                        Aktivitas Terbaru
                    </h2>
                </div>

                <div class="p-5 space-y-4">
                    <div class="flex gap-3">
                        <div class="w-8 h-8 rounded-full bg-green-100 text-green-600 flex items-center justify-center text-sm">
                            ✓
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-slate-800">
                                Diskusi dimulai
                            </p>
                            <p class="text-xs text-slate-500">
                                {{ $chats->count() > 0 ? 'Percakapan sudah dimulai antara customer dan freelancer.' : 'Belum ada aktivitas chat.' }}
                            </p>
                        </div>
                    </div>

                    <div class="flex gap-3">
                        <div class="w-8 h-8 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center text-sm">
                            ⓘ
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-slate-800">
                                File terlampir
                            </p>
                            <p class="text-xs text-slate-500">
                                {{ $lampiranChats->count() }} file telah dibagikan di room chat ini.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="px-5 py-4 border-b border-slate-200">
                    <h2 class="text-xs font-bold tracking-wide text-slate-500 uppercase">
                        Statistik Proyek
                    </h2>
                </div>

                <div class="p-5 space-y-5">
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-sm text-slate-600">Kemajuan Kesepahaman</span>
                            <span class="text-sm font-bold text-slate-900">
                                {{ $chats->count() > 0 ? '65%' : '10%' }}
                            </span>
                        </div>

                        @if ($chats->count() > 0)
                        <div class="h-2 bg-blue-600 rounded-full" style="width: 65%;"></div>
                        @else
                        <div class="h-2 bg-blue-600 rounded-full" style="width: 10%;"></div>
                        @endif
                    </div>

                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-sm text-slate-600">Waktu Tersisa</span>
                            <span class="text-sm font-bold text-slate-900">
                                4 Hari
                            </span>
                        </div>

                        <div class="w-full h-2 bg-slate-100 rounded-full">
                            <div class="h-2 bg-purple-500 rounded-full" style="width: 75%"></div>
                        </div>
                    </div>

                    <div class="p-4 rounded-xl bg-slate-50 border border-slate-200">
                        <p class="text-sm text-slate-700">
                            Setelah diskusi selesai, lanjutkan ke order untuk mengaktifkan pembayaran escrow dan progress pekerjaan.
                        </p>
                    </div>

                    <a href="{{ route('customer.order.create', $jasa->id) }}"
                        class="block text-center w-full px-5 py-3 bg-blue-600 text-white rounded-xl font-bold hover:bg-blue-700">
                        Lanjut ke Order
                    </a>
                </div>
            </div>
        </aside>

    </div>
</section>
@endsection

@push('scripts')
<script>
    const chatBox = document.getElementById('chat-messages');
    const currentUserId = Number(chatBox.dataset.currentUserId);
    const messagesUrl = chatBox.dataset.messagesUrl;

    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text ?? '';
        return div.innerHTML;
    }

    function loadMessages() {
        fetch(messagesUrl)
            .then(response => response.json())
            .then(messages => {
                chatBox.innerHTML = '';

                if (messages.length === 0) {
                    chatBox.innerHTML = `
                        <div class="h-full flex items-center justify-center text-center">
                            <div>
                                <div class="w-16 h-16 mx-auto rounded-full bg-blue-100 text-blue-600 flex items-center justify-center text-2xl mb-4">
                                    💬
                                </div>
                                <h3 class="text-lg font-bold text-slate-900">Belum ada pesan</h3>
                                <p class="text-sm text-slate-500 mt-2">
                                    Mulai diskusi pertama dengan freelancer dari room chat ini.
                                </p>
                            </div>
                        </div>
                    `;
                    return;
                }

                messages.forEach(chat => {
                    const isMe = chat.pengirim_id === currentUserId;
                    const pesan = escapeHtml(chat.pesan);
                    const pengirimNama = escapeHtml(chat.pengirim_nama);

                    const lampiran = chat.lampiran ?
                        `<a href="${chat.lampiran}" target="_blank" class="inline-block mt-3 text-xs underline">Lihat Lampiran</a>` :
                        '';

                    const html = isMe ?
                        `
                            <div class="flex justify-end">
                                <div class="max-w-lg">
                                    <div class="flex justify-end mb-1">
                                        <span class="px-2 py-1 text-[10px] font-bold text-blue-700 bg-blue-100 rounded-full">SAYA</span>
                                    </div>
                                    <div class="bg-blue-600 text-white rounded-2xl rounded-tr-md px-5 py-4 shadow-sm">
                                        <p class="text-sm leading-relaxed">${pesan}</p>
                                        ${lampiran}
                                    </div>
                                    <p class="text-[11px] text-slate-400 mt-2 text-right">${chat.waktu}</p>
                                </div>
                            </div>
                        ` :
                        `
                            <div class="flex justify-start">
                                <div class="max-w-lg">
                                    <div class="flex items-center gap-2 mb-1">
                                        <div class="w-6 h-6 rounded-full bg-slate-700 text-white flex items-center justify-center text-[10px] font-bold">
                                            ${(pengirimNama.charAt(0) || 'F').toUpperCase()}
                                        </div>
                                        <span class="text-xs font-semibold text-slate-500">${pengirimNama}</span>
                                    </div>
                                    <div class="bg-slate-200 text-slate-800 rounded-2xl rounded-tl-md px-5 py-4 shadow-sm">
                                        <p class="text-sm leading-relaxed">${pesan}</p>
                                        ${lampiran}
                                    </div>
                                    <p class="text-[11px] text-slate-400 mt-2">${chat.waktu}</p>
                                </div>
                            </div>
                        `;

                    chatBox.insertAdjacentHTML('beforeend', html);
                });

                chatBox.scrollTop = chatBox.scrollHeight;
            });
    }

    loadMessages();
    setInterval(loadMessages, 3000);
</script>
@endpush