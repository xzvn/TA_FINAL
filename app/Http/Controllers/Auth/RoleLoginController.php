<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;



class RoleLoginController extends Controller
{
    public function customer(): View
    {
        return view('auth.login-role', [
            'role' => 'customer',
            'title' => 'Masuk Sebagai Customer',
            'subtitle' => 'Masuk untuk mencari jasa, membuat pesanan, dan memantau progres pekerjaan.',
            'storeRoute' => route('login.customer.store'),
            'registerRoute' => route('register'),
            'otherLoginRoute' => route('login.freelancer'),
            'otherLoginText' => 'Masuk sebagai freelancer',
            'accent' => 'blue',
        ]);
    }

    public function freelancer(): View
    {
        return view('auth.login-role', [
            'role' => 'freelancer',
            'title' => 'Masuk Sebagai Freelancer',
            'subtitle' => 'Masuk untuk mengelola jasa, menerima pesanan, dan mengirim progress pekerjaan.',
            'storeRoute' => route('login.freelancer.store'),
            'registerRoute' => route('freelancer.register'),
            'otherLoginRoute' => route('login.customer'),
            'otherLoginText' => 'Masuk sebagai customer',
            'accent' => 'indigo',
        ]);
    }

    public function admin()
    {
        return view('auth.login-admin');
    }

    public function storeAdmin(Request $request)
    {
        return $this->sendLoginOtp($request, 'admin', 'login.admin');
    }

    
    public function storeCustomer(Request $request): RedirectResponse
    {
        return $this->sendLoginOtp($request, 'customer', 'login.customer');
    }

    public function storeFreelancer(Request $request): RedirectResponse
    {
        return $this->sendLoginOtp($request, 'freelancer', 'login.freelancer');
    }

    private function sendLoginOtp(Request $request, string $role, string $loginRoute): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $user = User::where('email', $credentials['email'])
            ->where('role', $role)
            ->first();

        if (! $user || ! Hash::check($credentials['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => 'Email atau password tidak sesuai untuk akun ' . $role . '.',
            ]);
        }

        if ($user->status_akun !== 'active') {
            throw ValidationException::withMessages([
                'email' => 'Akun kamu sedang tidak aktif.',
            ]);
        }

        $pin = (string) random_int(100000, 999999);

        session([
            'login_otp' => [
                'user_id' => $user->id,
                'role' => $role,
                'login_route' => $loginRoute,
                'remember' => $request->boolean('remember'),
                'pin' => $pin,
                'expired_at' => now()->addMinutes(10)->toDateTimeString(),
            ],
        ]);

        $this->sendOtpEmail($user->email, $user->nama ?? $user->email, $pin);

        return redirect()
            ->route($loginRoute)
            ->with('show_login_otp_modal', true)
            ->with('success', 'OTP login telah dikirim ke email: ' . $user->email);
    }

    public function verifyOtp(Request $request): RedirectResponse
    {
        $request->validate([
            'pin' => ['required', 'digits:6'],
        ]);

        $pending = session('login_otp');

        if (! $pending) {
            return redirect()
                ->route('login.customer')
                ->withErrors(['pin' => 'Tidak ada proses login yang perlu diverifikasi.']);
        }

        if (now()->greaterThan(\Carbon\Carbon::parse($pending['expired_at']))) {
            $loginRoute = $pending['login_route'] ?? 'login.customer';

            session()->forget('login_otp');

            return redirect()
                ->route($loginRoute)
                ->withErrors(['pin' => 'OTP login sudah kedaluwarsa. Silakan login ulang.']);
        }

        if ($request->pin !== $pending['pin']) {
            return redirect()
                ->route($pending['login_route'])
                ->withErrors(['pin' => 'OTP yang kamu masukkan salah.'])
                ->withInput()
                ->with('show_login_otp_modal', true);
        }

        $user = User::find($pending['user_id']);

        if (! $user) {
            session()->forget('login_otp');

            return redirect()
                ->route('login.customer')
                ->withErrors(['email' => 'Akun tidak ditemukan.']);
        }

        Auth::login($user, $pending['remember'] ?? false);

        $request->session()->regenerate();
        session()->forget('login_otp');

        return redirect()
            ->route('dashboard')
            ->with('success', 'Login berhasil.');
    }

    public function resendOtp(): RedirectResponse
    {
        $pending = session('login_otp');

        if (! $pending) {
            return redirect()
                ->route('login.customer')
                ->withErrors(['pin' => 'Tidak ada proses login yang perlu dikirim ulang OTP-nya.']);
        }

        $user = User::find($pending['user_id']);

        if (! $user) {
            session()->forget('login_otp');

            return redirect()
                ->route('login.customer')
                ->withErrors(['email' => 'Akun tidak ditemukan.']);
        }

        $pin = (string) random_int(100000, 999999);

        $pending['pin'] = $pin;
        $pending['expired_at'] = now()->addMinutes(10)->toDateTimeString();

        session(['login_otp' => $pending]);

        $this->sendOtpEmail($user->email, $user->nama ?? $user->email, $pin);

        return redirect()
            ->route($pending['login_route'])
            ->with('show_login_otp_modal', true)
            ->with('success', 'OTP login baru telah dikirim ke email kamu.');
    }

    public function cancelOtp(): RedirectResponse
    {
        $pending = session('login_otp');
        $loginRoute = $pending['login_route'] ?? 'login.customer';

        session()->forget('login_otp');

        return redirect()
            ->route($loginRoute)
            ->with('success', 'Silakan login ulang.');
    }

    private function sendOtpEmail(string $email, string $nama, string $pin): void
    {
        Mail::raw(
            "Halo {$nama},\n\nKode OTP login JasaKampus kamu adalah: {$pin}\n\nKode ini berlaku selama 10 menit.\n\nJika kamu tidak merasa login, abaikan email ini.",
            function ($message) use ($email, $nama) {
                $message->to($email, $nama)
                    ->subject('OTP Login - JasaKampus');
            }
        );
    }
}
