<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'id_pesanan',
    'id_customer',
    'id_freelancer',
    'alasan_dispute',
    'bukti_dispute',
    'status_dispute',
    'keputusan_admin',
    'tanggal_pengajuan',
    'tanggal_diproses',
])]
class Dispute extends Model
{
    use HasFactory;

    public function pesanan()
    {
        return $this->belongsTo(Pesanan::class, 'id_pesanan');
    }

    public function customer()
    {
        return $this->belongsTo(User::class, 'id_customer');
    }

    public function freelancer()
    {
        return $this->belongsTo(User::class, 'id_freelancer');
    }
}
