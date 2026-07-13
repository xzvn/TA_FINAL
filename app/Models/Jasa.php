<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Review;

#[Fillable([
    'id_freelancer',
    'nama_jasa',
    'kategori',
    'deskripsi',
    'harga',
    'estimasi_pengerjaan',
    'thumbnail',
    'status_jasa',
])]
class Jasa extends Model
{
    use HasFactory;

    protected $table = 'jasa';

    public function freelancer()
    {
        return $this->belongsTo(User::class, 'id_freelancer');
    }

    public function chats()
    {
        return $this->hasMany(Chat::class, 'id_jasa');
    }

    public function reviews()
    {
        return $this->hasMany(\App\Models\Review::class, 'id_jasa', 'id');
    }

    public function pesanans()
    {
        return $this->hasMany(\App\Models\Pesanan::class, 'id_jasa', 'id');
    }

    public function favorites()
    {
        return $this->hasMany(\App\Models\Favorite::class, 'id_jasa');
    }
}
