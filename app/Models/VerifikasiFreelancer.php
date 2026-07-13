<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'id_freelancer',
    'email_kampus',
    'universitas',
    'program_studi',
    'file_ktm',
    'catatan_admin',
    'status_verifikasi',
    'tanggal_pengajuan',
    'tanggal_verifikasi',
])]
class VerifikasiFreelancer extends Model
{
    use HasFactory;

    public function freelancer()
    {
        return $this->belongsTo(User::class, 'id_freelancer');
    }
}