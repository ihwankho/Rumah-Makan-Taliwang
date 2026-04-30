<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pembayaran extends Model
{
    protected $table = 'pembayarans';

    protected $fillable = [
        'id_pesanan',
        'id_kasir',
        'metode_pembayaran',
        'total_bayar',
        'status_pembayaran',
        'tanggal_bayar',
    ];

    public function pesanan()
    {
        return $this->belongsTo(Pesanan::class, 'id_pesanan');
    }
}
