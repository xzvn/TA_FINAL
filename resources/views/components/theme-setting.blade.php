@php
$currentTheme = old(
'theme',
auth()->user()->theme ?? 'light'
);
@endphp

<div class="rounded-2xl border border-slate-200 bg-white p-6">
    <div class="grid gap-6 md:grid-cols-2 md:items-center">

        <div>
            <h3 class="text-xl font-bold text-slate-900">
                Tema Tampilan
            </h3>

            <p class="mt-2 text-sm text-slate-500">
                Pilih tema terang atau gelap untuk tampilan akun kamu.
            </p>
        </div>

        <div class="flex flex-wrap gap-4 md:justify-end">

            <label
                for="theme-light"
                class="cursor-pointer">

                <input
                    id="theme-light"
                    type="radio"
                    name="theme"
                    value="light"
                    class="peer sr-only"
                    {{ $currentTheme === 'light'
                        ? 'checked'
                        : '' }}>

                <span
                    class="inline-flex min-w-40 items-center justify-center rounded-xl border border-slate-300 bg-white px-7 py-4 font-bold text-slate-800 transition
                    hover:border-blue-400 hover:bg-blue-50
                    peer-checked:border-blue-600
                    peer-checked:bg-blue-600
                    peer-checked:text-white
                    peer-focus-visible:ring-4
                    peer-focus-visible:ring-blue-200">

                    ☀️&nbsp; Terang
                </span>
            </label>

            <label
                for="theme-dark"
                class="cursor-pointer">

                <input
                    id="theme-dark"
                    type="radio"
                    name="theme"
                    value="dark"
                    class="peer sr-only"
                    {{ $currentTheme === 'dark'
                        ? 'checked'
                        : '' }}>

                <span
                    class="inline-flex min-w-40 items-center justify-center rounded-xl border border-slate-300 bg-white px-7 py-4 font-bold text-slate-800 transition
                    hover:border-blue-400 hover:bg-blue-50
                    peer-checked:border-blue-600
                    peer-checked:bg-blue-600
                    peer-checked:text-white
                    peer-focus-visible:ring-4
                    peer-focus-visible:ring-blue-200">

                    🌙&nbsp; Gelap
                </span>
            </label>
        </div>
    </div>
</div>