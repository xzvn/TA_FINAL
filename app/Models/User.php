<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\Notifikasi;

#[Fillable([
    'nama',
    'email',
    'role',
    'no_hp',
    'alamat',
    'foto_profil',
    'status_akun',
    'password',
    'theme'
])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    public function verifikasiFreelancer()
    {
        return $this->hasOne(VerifikasiFreelancer::class, 'id_freelancer');
    }

    public function portofolios()
    {
        return $this->hasMany(Portofolio::class, 'id_freelancer');
    }

    public function jasa()
    {
        return $this->hasMany(Jasa::class, 'id_freelancer');
    }

    public function chatDikirim()
    {
        return $this->hasMany(Chat::class, 'pengirim_id');
    }

    public function chatSebagaiCustomer()
    {
        return $this->hasMany(Chat::class, 'id_customer');
    }

    public function chatSebagaiFreelancer()
    {
        return $this->hasMany(Chat::class, 'id_freelancer');
    }
    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function pesananSebagaiCustomer()
    {
        return $this->hasMany(Pesanan::class, 'id_customer');
    }

    public function pesananSebagaiFreelancer()
    {
        return $this->hasMany(Pesanan::class, 'id_freelancer');
    }

    public function reviewsSebagaiCustomer()
    {
        return $this->hasMany(Review::class, 'id_customer');
    }

    public function reviewsSebagaiFreelancer()
    {
        return $this->hasMany(Review::class, 'id_freelancer');
    }

    public function withdrawals()
    {
        return $this->hasMany(Withdrawal::class, 'id_freelancer');
    }

    public function notifikasi()
    {
        return $this->hasMany(Notifikasi::class, 'id_user');
    }

    public function favorites()
    {
        return $this->hasMany(\App\Models\Favorite::class, 'id_customer');
    }
}
