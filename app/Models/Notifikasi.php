<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Notifikasi extends Model
{
    use HasFactory;

    protected $table = 'notifikasi';

    protected $fillable = [
        'id_user',
        'judul',
        'pesan',
        'tipe',
        'url',
        'dibaca',
        'dibaca_pada',
    ];

    protected $casts = [
        'dibaca' => 'boolean',
        'dibaca_pada' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }
}
