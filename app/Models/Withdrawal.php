<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'id_freelancer',
    'jumlah_pencairan',
    'nama_bank',
    'nomor_rekening',
    'nama_pemilik_rekening',
    'status_withdrawal',
    'catatan_admin',
    'tanggal_pengajuan',
    'tanggal_diproses',
])]
class Withdrawal extends Model
{
    use HasFactory;

    public function freelancer()
    {
        return $this->belongsTo(User::class, 'id_freelancer');
    }
}
