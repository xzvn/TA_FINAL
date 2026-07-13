<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'id_pesanan',
    'catatan',
    'file_hasil',
    'status_hasil',
    'tanggal_upload',
])]
class HasilPekerjaan extends Model
{
    use HasFactory;

    protected $table = 'hasil_pekerjaans';

    public function pesanan()
    {
        return $this->belongsTo(Pesanan::class, 'id_pesanan');
    }
}
