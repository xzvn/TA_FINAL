@php
$jumlahNotifikasiBaru = auth()->check()
? \App\Models\Notifikasi::where('id_user', auth()->id())
->where('dibaca', false)
->count()
: 0;
@endphp

<a href="{{ route('notifikasi.index') }}"
    aria-label="Buka notifikasi"
    class="relative inline-flex items-center justify-center w-10 h-10 rounded-xl border transition
    {{ request()->routeIs('notifikasi.*')
        ? 'bg-blue-600 border-blue-600 text-white shadow-sm'
        : 'bg-white border-slate-200 text-slate-600 hover:bg-slate-50 hover:text-blue-700' }}">

    <svg xmlns="http://www.w3.org/2000/svg"
        class="w-5 h-5"
        fill="none"
        viewBox="0 0 24 24"
        stroke="currentColor"
        stroke-width="1.9">
        <path stroke-linecap="round" stroke-linejoin="round"
            d="M15 17h5l-1.4-1.4A2 2 0 0118 14.17V11a6 6 0 10-12 0v3.17a2 2 0 01-.6 1.43L4 17h5" />
        <path stroke-linecap="round" stroke-linejoin="round"
            d="M9 17a3 3 0 006 0" />
    </svg>

    @if ($jumlahNotifikasiBaru > 0)
    <span class="absolute -top-1 -right-1 min-w-[20px] h-5 px-1.5 inline-flex items-center justify-center rounded-full bg-red-600 text-white text-[11px] font-extrabold ring-2 ring-white">
        {{ $jumlahNotifikasiBaru > 99 ? '99+' : $jumlahNotifikasiBaru }}
    </span>
    @endif
</a>