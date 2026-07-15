<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Portofolio;
use App\Models\User;
use App\Models\VerifikasiFreelancer;
use App\Services\CloudinaryService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class FreelancerRegisterController extends Controller
{
    public function create(): View
    {
        return view('auth.register-freelancer');
    }

    public function store(Request $request): RedirectResponse
    {
        session()->forget('freelancer_register_otp');

        $data = $request->validate(
            [
                'nama' => ['required', 'string', 'max:255'],
                'alamat' => ['required', 'string'],

                'email_kampus' => [
                    'required',
                    'string',
                    'lowercase',
                    'email',
                    'max:255',
                    'unique:users,email',
                    'unique:verifikasi_freelancers,email_kampus',
                    function ($attribute, $value, $fail) {
                        $domain = strtolower(substr(strrchr($value, '@'), 1));

                        $blockedDomains = [
                            'gmail.com',
                            'yahoo.com',
                            'ymail.com',
                            'rocketmail.com',
                            'outlook.com',
                            'hotmail.com',
                            'live.com',
                            'icloud.com',
                            'me.com',
                            'proton.me',
                            'protonmail.com',
                            'aol.com',
                            'mail.com',
                            'zoho.com',
                            'gmx.com',
                        ];

                        if (in_array($domain, $blockedDomains)) {
                            $fail(
                                'Gunakan email kampus atau email institusi pendidikan, bukan email pribadi.'
                            );
                        }
                    },
                ],

                'universitas' => ['required', 'string', 'max:255'],
                'program_studi' => ['nullable', 'string', 'max:255'],

                'file_ktm' => [
                    'required',
                    'file',
                    'mimes:jpg,jpeg,png,pdf',
                    'max:2048',
                ],

                'file_portofolio' => [
                    'required',
                    'file',
                    'mimes:jpg,jpeg,png,webp,pdf,doc,docx,ppt,pptx',
                    'max:5120',
                ],

                'password' => [
                    'required',
                    'confirmed',
                    Rules\Password::defaults(),
                ],
            ],
            [
                'nama.required' =>
                'Nama lengkap wajib diisi.',

                'alamat.required' =>
                'Alamat domisili wajib diisi.',

                'email_kampus.required' =>
                'Email kampus wajib diisi.',

                'email_kampus.email' =>
                'Format email kampus tidak valid.',

                'email_kampus.unique' =>
                'Email kampus tersebut sudah pernah digunakan.',

                'universitas.required' =>
                'Nama kampus atau universitas wajib diisi.',

                'file_ktm.required' =>
                'File KTM wajib diunggah.',

                'file_ktm.file' =>
                'KTM yang diunggah harus berupa file.',

                'file_ktm.mimes' =>
                'KTM harus berformat JPG, JPEG, PNG, atau PDF.',

                'file_ktm.max' =>
                'Ukuran file KTM maksimal 2 MB.',

                'file_portofolio.required' =>
                'File portofolio wajib diunggah.',

                'file_portofolio.file' =>
                'Portofolio yang diunggah harus berupa file.',

                'file_portofolio.mimes' =>
                'Portofolio harus berformat JPG, JPEG, PNG, WEBP, PDF, DOC, DOCX, PPT, atau PPTX.',

                'file_portofolio.max' =>
                'Ukuran file portofolio maksimal 1 MB.',

                'password.required' =>
                'Password wajib diisi.',

                'password.confirmed' =>
                'Konfirmasi password tidak sama dengan password.',
            ]
        );

        $emailFolder = str_replace(['@', '.'], '_', strtolower($data['email_kampus']));

        $ktmPath = CloudinaryService::uploadFile(
            $request->file('file_ktm'),
            'jasakampus/freelancer/' . $emailFolder . '/ktm'
        );

        $portofolioPath = CloudinaryService::uploadFile(
            $request->file('file_portofolio'),
            'jasakampus/freelancer/' . $emailFolder . '/portfolio'
        );

        $pin = (string) random_int(100000, 999999);

        session([
            'freelancer_register_otp' => [
                'data' => [
                    'nama' => $data['nama'],
                    'alamat' => $data['alamat'],
                    'email_kampus' => $data['email_kampus'],
                    'universitas' => $data['universitas'],
                    'program_studi' => $data['program_studi'] ?? null,
                    'password' => Hash::make($data['password']),
                ],
                'file_ktm' => $ktmPath,
                'file_portofolio' => $portofolioPath,
                'pin' => $pin,
                'expired_at' => now()->addMinutes(10)->toDateTimeString(),
            ],
        ]);

        $this->sendOtpEmail($data['email_kampus'], $data['nama'], $pin);

        return redirect()
            ->route('freelancer.register')
            ->with('show_otp_modal', true)
            ->with('success', 'Kode OTP telah dikirim ke email kampus: ' . $data['email_kampus']);
    }

    public function verifyOtp(Request $request): RedirectResponse
    {
        $request->validate([
            'pin' => ['required', 'digits:6'],
        ]);

        $pending = session('freelancer_register_otp');

        if (! $pending) {
            return redirect()
                ->route('freelancer.register')
                ->withErrors(['pin' => 'Tidak ada pendaftaran freelancer yang perlu diverifikasi.']);
        }

        if (now()->greaterThan(\Carbon\Carbon::parse($pending['expired_at']))) {
            session()->forget('freelancer_register_otp');

            return redirect()
                ->route('freelancer.register')
                ->withErrors(['pin' => 'OTP sudah kedaluwarsa. Silakan daftar ulang.']);
        }

        if ($request->pin !== $pending['pin']) {
            return redirect()
                ->route('freelancer.register')
                ->withErrors(['pin' => 'OTP yang kamu masukkan salah.'])
                ->withInput()
                ->with('show_otp_modal', true);
        }

        $user = DB::transaction(function () use ($pending) {
            $data = $pending['data'];

            $user = User::create([
                'nama' => $data['nama'],
                'email' => $data['email_kampus'],
                'role' => 'freelancer',
                'alamat' => $data['alamat'],
                'status_akun' => 'active',
                'password' => $data['password'],
            ]);

            VerifikasiFreelancer::create([
                'id_freelancer' => $user->id,
                'email_kampus' => $data['email_kampus'],
                'universitas' => $data['universitas'],
                'program_studi' => $data['program_studi'],
                'file_ktm' => $pending['file_ktm'],
                'status_verifikasi' => 'pending',
                'tanggal_pengajuan' => now(),
            ]);

            Portofolio::create([
                'id_freelancer' => $user->id,
                'judul_portofolio' => 'Portofolio Awal',
                'deskripsi' => 'Portofolio yang diunggah saat pendaftaran freelancer.',
                'file_portofolio' => $pending['file_portofolio'],
            ]);

            return $user;
        });

        session()->forget('freelancer_register_otp');

        Auth::login($user);

        return redirect()
            ->route('dashboard')
            ->with('success', 'Pendaftaran freelancer berhasil. Akun Anda menunggu verifikasi admin.');
    }

    public function resendOtp(): RedirectResponse
    {
        $pending = session('freelancer_register_otp');

        if (! $pending) {
            return redirect()
                ->route('freelancer.register')
                ->withErrors(['pin' => 'Tidak ada pendaftaran freelancer yang perlu dikirim ulang OTP-nya.']);
        }

        $pin = (string) random_int(100000, 999999);

        $pending['pin'] = $pin;
        $pending['expired_at'] = now()->addMinutes(10)->toDateTimeString();

        session(['freelancer_register_otp' => $pending]);

        $this->sendOtpEmail(
            $pending['data']['email_kampus'],
            $pending['data']['nama'],
            $pin
        );

        return redirect()
            ->route('freelancer.register')
            ->with('show_otp_modal', true)
            ->with('success', 'OTP baru telah dikirim ke email kampus kamu.');
    }

    private function sendOtpEmail(string $email, string $nama, string $pin): void
    {
        Mail::raw(
            "Halo {$nama},\n\nKode OTP pendaftaran freelancer JasaKampus kamu adalah: {$pin}\n\nKode ini berlaku selama 10 menit.\n\nJika kamu tidak merasa mendaftar sebagai freelancer, abaikan email ini.",
            function ($message) use ($email, $nama) {
                $message->to($email, $nama)
                    ->subject('OTP Pendaftaran Freelancer - JasaKampus');
            }
        );
    }

    public function cancelOtp(): RedirectResponse
    {
        session()->forget('freelancer_register_otp');

        return redirect()
            ->route('freelancer.register')
            ->with('success', 'Silakan masukkan ulang email kampus yang benar.');
    }
}
