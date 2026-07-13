@php
$currentTheme = auth()->user()->theme ?? 'light';
@endphp

<div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 theme-card">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-5">
        <div>
            <h2 class="text-lg font-bold text-slate-900 theme-text">
                Tema Tampilan
            </h2>
            <p class="text-sm text-slate-500 theme-muted mt-1">
                Pilih tema terang atau gelap untuk tampilan akun kamu.
            </p>
        </div>

        <form method="POST" action="{{ route('theme.update') }}" class="flex gap-3">
            @csrf

            <button type="submit"
                name="theme"
                value="light"
                class="px-5 py-3 rounded-xl text-sm font-bold border transition
                {{ $currentTheme === 'light'
                    ? 'bg-blue-600 text-white border-blue-600'
                    : 'bg-white text-slate-700 border-slate-300 hover:bg-slate-50' }}">
                ☀️ Terang
            </button>

            <button type="submit"
                name="theme"
                value="dark"
                class="px-5 py-3 rounded-xl text-sm font-bold border transition
                {{ $currentTheme === 'dark'
                    ? 'bg-blue-600 text-white border-blue-600'
                    : 'bg-white text-slate-700 border-slate-300 hover:bg-slate-50' }}">
                🌙 Gelap
            </button>
        </form>
    </div>
</div>