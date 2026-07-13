<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'id_pesanan',
    'id_jasa',
    'id_customer',
    'id_freelancer',
    'rating',
    'rating_kualitas',
    'rating_komunikasi',
    'rating_waktu',
    'rating_profesionalisme',
    'ulasan',
    'rekomendasi',
    'foto_review',
])]
class Review extends Model
{
    use HasFactory;

    public function pesanan()
    {
        return $this->belongsTo(Pesanan::class, 'id_pesanan');
    }

    public function jasa()
    {
        return $this->belongsTo(Jasa::class, 'id_jasa');
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
