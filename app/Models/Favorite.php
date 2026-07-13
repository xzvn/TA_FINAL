<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Favorite extends Model
{
    protected $fillable = [
        'id_customer',
        'id_jasa',
    ];

    public function customer()
    {
        return $this->belongsTo(User::class, 'id_customer');
    }

    public function jasa()
    {
        return $this->belongsTo(Jasa::class, 'id_jasa');
    }
}