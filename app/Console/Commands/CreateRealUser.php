<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Throwable;

class CreateRealUser extends Command
{
    protected $signature = 'app:create-real-user';

    protected $description =
        'Membuat akun pengguna nyata secara aman dan interaktif';

    public function handle(): int
    {
        $this->info('Membuat akun JasaKampus');
        $this->newLine();

        $role = $this->choice(
            'Pilih role akun',
            [
                'admin',
                'customer',
                'freelancer',
            ],
            1
        );

        $nama = trim(
            (string) $this->ask('Nama lengkap')
        );

        $email = strtolower(
            trim(
                (string) $this->ask('Email')
            )
        );

        $noHp = trim(
            (string) $this->ask(
                'Nomor HP, contoh 081234567890'
            )
        );

        $alamat = trim(
            (string) $this->ask('Alamat')
        );

        $password = (string) $this->secret(
            'Password minimal 12 karakter'
        );

        $passwordConfirmation =
            (string) $this->secret(
                'Ulangi password'
            );

        try {
            $validated = Validator::make(
                [
                    'nama' => $nama,
                    'email' => $email,
                    'role' => $role,
                    'no_hp' => $noHp,
                    'alamat' => $alamat,
                    'password' => $password,
                    'password_confirmation' =>
                        $passwordConfirmation,
                ],
                [
                    'nama' => [
                        'required',
                        'string',
                        'max:255',
                    ],

                    'email' => [
                        'required',
                        'email',
                        'max:255',
                        'unique:users,email',
                    ],

                    'role' => [
                        'required',
                        'in:admin,customer,freelancer',
                    ],

                    'no_hp' => [
                        'required',
                        'string',
                        'max:20',
                    ],

                    'alamat' => [
                        'required',
                        'string',
                        'max:1000',
                    ],

                    'password' => [
                        'required',
                        'string',
                        'min:12',
                        'confirmed',
                    ],
                ]
            )->validate();
        } catch (ValidationException $exception) {
            $this->error(
                'Data akun belum valid.'
            );

            foreach (
                $exception->errors()
                as $field => $messages
            ) {
                foreach ($messages as $message) {
                    $this->line(
                        "- {$field}: {$message}"
                    );
                }
            }

            return self::FAILURE;
        }

        $this->newLine();

        $this->table(
            ['Field', 'Nilai'],
            [
                ['Nama', $validated['nama']],
                ['Email', $validated['email']],
                ['Role', $validated['role']],
                ['Nomor HP', $validated['no_hp']],
                ['Alamat', $validated['alamat']],
            ]
        );

        if (
            ! $this->confirm(
                'Apakah data ini sudah benar?',
                false
            )
        ) {
            $this->warn(
                'Pembuatan akun dibatalkan.'
            );

            return self::SUCCESS;
        }

        try {
            $user = DB::transaction(
                function () use (
                    $validated
                ): User {
                    return User::create([
                        'nama' =>
                            $validated['nama'],

                        'email' =>
                            $validated['email'],

                        'role' =>
                            $validated['role'],

                        'no_hp' =>
                            $validated['no_hp'],

                        'alamat' =>
                            $validated['alamat'],

                        'foto_profil' =>
                            null,

                        'status_akun' =>
                            'active',

                        'email_verified_at' =>
                            now(),

                        'password' =>
                            Hash::make(
                                $validated['password']
                            ),

                        'theme' =>
                            'light',
                    ]);
                }
            );
        } catch (Throwable $exception) {
            $this->error(
                'Akun gagal dibuat: ' .
                $exception->getMessage()
            );

            return self::FAILURE;
        }

        $this->newLine();

        $this->info(
            'Akun berhasil dibuat.'
        );

        $this->line(
            'ID    : ' . $user->id
        );

        $this->line(
            'Email : ' . $user->email
        );

        $this->line(
            'Role  : ' . $user->role
        );

        return self::SUCCESS;
    }
}