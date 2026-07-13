@extends('layouts.customer')

@section('title', 'Verifikasi PIN - JasaKampus')

@section('content')
<section class="min-h-[calc(100vh-80px)] flex items-center justify-center px-6 py-10">
    <div class="w-full max-w-3xl bg-white rounded-3xl border border-slate-200 shadow-sm p-8 md:p-10 text-center">
        <div class="w-24 h-24 mx-auto rounded-full bg-blue-100 flex items-center justify-center text-5xl mb-7">
            ✉️
        </div>

        <h1 class="text-3xl md:text-4xl font-extrabold text-slate-900">
            Verifikasi Perubahan Profil
        </h1>

        <p class="mt-4 text-slate-500 text-base md:text-lg leading-relaxed">
            Kami telah mengirim PIN 6 digit ke email akun kamu.
            Masukkan PIN tersebut untuk menyimpan perubahan profil.
        </p>

        @if (session('success'))
        <div class="mt-8 rounded-2xl bg-green-50 border border-green-100 px-5 py-4 text-green-700 font-semibold text-left">
            {{ session('success') }}
        </div>
        @else
        <div class="mt-8 rounded-2xl bg-green-50 border border-green-100 px-5 py-4 text-green-700 font-semibold text-left">
            PIN verifikasi telah dikirim ke email kamu: {{ auth()->user()->email }}
        </div>
        @endif

        @if ($errors->any())
        <div class="mt-5 rounded-2xl bg-red-50 border border-red-100 px-5 py-4 text-red-700 text-left">
            @foreach ($errors->all() as $error)
            <p class="font-semibold">{{ $error }}</p>
            @endforeach
        </div>
        @endif

        <form id="pinForm"
            action="{{ route('customer.profile.verify') }}"
            method="POST"
            class="mt-8">
            @csrf

            <input type="hidden" name="pin" id="pinValue">

            <label class="block text-left text-slate-800 font-bold mb-4">
                PIN Verifikasi
            </label>

            <div class="flex justify-center gap-3 md:gap-4">
                @for ($i = 0; $i < 6; $i++)
                    <input
                    type="text"
                    maxlength="1"
                    inputmode="numeric"
                    pattern="[0-9]*"
                    class="pin-box w-12 h-14 md:w-16 md:h-16 text-center text-2xl md:text-3xl font-extrabold text-slate-800 border border-slate-300 rounded-2xl focus:border-blue-500 focus:ring-4 focus:ring-blue-100 outline-none transition"
                    autocomplete="off">
                    @endfor
            </div>

            @error('pin')
            <p class="text-red-600 text-sm font-semibold mt-4">
                {{ $message }}
            </p>
            @enderror

            <div class="mt-8 flex flex-col sm:flex-row justify-between gap-4">
                <a href="{{ route('customer.profile.index') }}"
                    class="px-7 py-4 rounded-2xl border border-slate-300 text-slate-700 font-bold hover:bg-slate-50 text-center">
                    Batal
                </a>

                <button type="submit"
                    id="submitPinButton"
                    class="px-7 py-4 rounded-2xl bg-blue-600 text-white font-bold hover:bg-blue-700">
                    Verifikasi & Simpan
                </button>
            </div>

            @php
            $expiredAt = session('customer_profile_update.expired_at');
            @endphp

            <p class="mt-8 text-sm text-slate-400">
                PIN berlaku selama
                <span
                    id="pinCountdown"
                    class="font-bold text-blue-600"
                    data-expires-at="{{ \Carbon\Carbon::parse($expiredAt)->timestamp * 1000 }}"
                    data-server-now="{{ now()->timestamp * 1000 }}">
                    10:00
                </span>
            </p>
        </form>
    </div>
</section>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('pinForm');
        const pinValue = document.getElementById('pinValue');
        const boxes = Array.from(document.querySelectorAll('.pin-box'));

        if (!form || !pinValue || boxes.length === 0) {
            return;
        }

        boxes[0].focus();

        boxes.forEach((box, index) => {
            box.addEventListener('input', function() {
                this.value = this.value.replace(/\D/g, '');

                if (this.value && index < boxes.length - 1) {
                    boxes[index + 1].focus();
                }

                updateHiddenPin();
            });

            box.addEventListener('keydown', function(event) {
                if (event.key === 'Backspace' && !this.value && index > 0) {
                    boxes[index - 1].focus();
                }
            });

            box.addEventListener('paste', function(event) {
                event.preventDefault();

                const pasted = (event.clipboardData || window.clipboardData)
                    .getData('text')
                    .replace(/\D/g, '')
                    .slice(0, 6);

                pasted.split('').forEach((char, i) => {
                    if (boxes[i]) {
                        boxes[i].value = char;
                    }
                });

                updateHiddenPin();

                const nextIndex = Math.min(pasted.length, boxes.length - 1);
                boxes[nextIndex].focus();
            });
        });

        function updateHiddenPin() {
            pinValue.value = boxes.map(box => box.value).join('');
        }

        form.addEventListener('submit', function(event) {
            updateHiddenPin();

            if (pinValue.value.length !== 6) {
                event.preventDefault();
                alert('Masukkan PIN 6 digit terlebih dahulu.');
                boxes.find(box => !box.value)?.focus();
            }
        });
    });
</script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const countdown = document.getElementById('pinCountdown');
        const submitButton = document.getElementById('submitPinButton');

        if (!countdown) return;

        const expiresAt = Number(countdown.dataset.expiresAt);
        const serverNow = Number(countdown.dataset.serverNow);
        const clientStart = Date.now();

        let timer = null;

        function getCurrentServerTime() {
            return serverNow + (Date.now() - clientStart);
        }

        function updateCountdown() {
            const distance = expiresAt - getCurrentServerTime();

            if (distance <= 0) {
                countdown.textContent = '00:00';
                countdown.classList.remove('text-blue-600');
                countdown.classList.add('text-red-600');

                if (submitButton) {
                    submitButton.disabled = true;
                    submitButton.classList.add('opacity-60', 'cursor-not-allowed');
                    submitButton.textContent = 'PIN Kedaluwarsa';
                }

                if (timer) clearInterval(timer);
                return;
            }

            const minutes = Math.floor(distance / 1000 / 60);
            const seconds = Math.floor((distance / 1000) % 60);

            countdown.textContent =
                String(minutes).padStart(2, '0') + ':' + String(seconds).padStart(2, '0');
        }

        updateCountdown();
        timer = setInterval(updateCountdown, 1000);
    });
</script>

@endpush