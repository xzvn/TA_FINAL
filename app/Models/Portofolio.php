<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'id_freelancer',
    'judul_portofolio',
    'deskripsi',
    'file_portofolio',
])]
class Portofolio extends Model
{
    use HasFactory;

    public function freelancer()
    {
        return $this->belongsTo(User::class, 'id_freelancer');
    }
}