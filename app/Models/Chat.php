<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'id_jasa',
    'id_customer',
    'id_freelancer',
    'pengirim_id',
    'pesan',
    'lampiran',
    'waktu_kirim',
])]
class Chat extends Model
{
    protected $casts = [
        'waktu_kirim' => 'datetime',
    ];
    use HasFactory;

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

    public function pengirim()
    {
        return $this->belongsTo(User::class, 'pengirim_id');
    }
}
