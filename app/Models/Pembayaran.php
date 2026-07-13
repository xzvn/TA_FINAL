<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'id_pesanan',
    'order_id',
    'transaction_id',
    'payment_type',
    'gross_amount',
    'transaction_status',
    'fraud_status',
    'status_escrow',
    'snap_token',
    'tanggal_bayar',
    'tanggal_release',
])]
class Pembayaran extends Model
{
    protected $casts = [
        'tanggal_bayar' => 'datetime',
        'gross_amount' => 'decimal:2',
    ];
    use HasFactory;

    public function pesanan()
    {
        return $this->belongsTo(Pesanan::class, 'id_pesanan');
    }
}
