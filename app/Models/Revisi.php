<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'id_pesanan',
    'id_progress',
    'jenis_revisi',
    'persentase_progress',
    'catatan_revisi',
    'status_revisi',
    'tanggal_revisi',
    'tanggal_selesai',
])]
class Revisi extends Model
{
    use HasFactory;

    public function pesanan()
    {
        return $this->belongsTo(Pesanan::class, 'id_pesanan');
    }

    public function progress()
    {
        return $this->belongsTo(ProgressPekerjaan::class, 'id_progress');
    }
}
