<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Throwable;

class PasswordResetLinkController extends Controller
{
    public function create(): View
    {
        return view('auth.forgot-password');
    }

    /**
     * @throws ValidationException
     */
    public function store(
        Request $request
    ): RedirectResponse {
        $request->validate(
            [
                'email' => [
                    'required',
                    'email',
                ],
            ],
            [
                'email.required' =>
                'Alamat email wajib diisi.',

                'email.email' =>
                'Format alamat email tidak valid.',
            ]
        );

        try {
            $status = Password::sendResetLink(
                $request->only('email')
            );
        } catch (Throwable $exception) {
            Log::error(
                'Gagal mengirim email reset password.',
                [
                    'email' =>
                    $request->string('email')
                        ->toString(),

                    'exception' =>
                    $exception->getMessage(),
                ]
            );

            return back()
                ->withInput(
                    $request->only('email')
                )
                ->withErrors([
                    'email' =>
                    'Email reset password belum dapat dikirim. Silakan coba kembali.',
                ]);
        }

        return $status ===
            Password::RESET_LINK_SENT
            ? back()->with(
                'status',
                __($status)
            )
            : back()
            ->withInput(
                $request->only('email')
            )
            ->withErrors([
                'email' => __($status),
            ]);
    }
}
