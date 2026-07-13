<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


#[Fillable([
    'id_customer',
    'id_freelancer',
    'id_jasa',
    'deskripsi_kebutuhan',
    'file_requirement',
    'total_harga',
    'status_pesanan',
    'jumlah_revisi',
    'batas_revisi',
    'tanggal_pesan',
    'deadline',
])]
class Pesanan extends Model
{
    use HasFactory;

    public function customer()
    {
        return $this->belongsTo(User::class, 'id_customer');
    }

    public function freelancer()
    {
        return $this->belongsTo(User::class, 'id_freelancer');
    }

    public function jasa()
    {
        return $this->belongsTo(Jasa::class, 'id_jasa');
    }

    public function pembayaran()
    {
        return $this->hasOne(Pembayaran::class, 'id_pesanan');
    }

    public function progressPekerjaans()
    {
        return $this->hasMany(ProgressPekerjaan::class, 'id_pesanan');
    }

    public function hasilPekerjaan()
    {
        return $this->hasOne(HasilPekerjaan::class, 'id_pesanan');
    }

    public function revisis()
    {
        return $this->hasMany(Revisi::class, 'id_pesanan');
    }

    public function review()
    {
        return $this->hasOne(Review::class, 'id_pesanan');
    }

    public function dispute()
    {
        return $this->hasOne(Dispute::class, 'id_pesanan');
    }
}
