<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'id_pesanan',
    'judul_progress',
    'deskripsi_progress',
    'persentase_progress',
    'file_progress',
    'tanggal_upload',
])]
class ProgressPekerjaan extends Model
{
    use HasFactory;

    protected $table = 'progress_pekerjaans';

    public function pesanan()
    {
        return $this->belongsTo(Pesanan::class, 'id_pesanan');
    }

    public function progressPekerjaans()
    {
        return $this->hasMany(ProgressPekerjaan::class, 'id_pesanan');
    }
}
