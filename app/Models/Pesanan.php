<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Pesanan extends Model
{
    use HasFactory;
    protected $table = 'pesanans';

    protected $fillable = [
        'id_meja',
        'id_pelanggan',
        'tipe_pesanan',
        'status_pesanan',
        'total_harga',
        'session_id',
    ];

    public function meja()
    {
        return $this->belongsTo(Meja::class, 'id_meja');
    }

    public function pelanggan()
    {
        return $this->belongsTo(Pelanggan::class, 'id_pelanggan');
    }

    public function detailPesanans()
    {
        return $this->hasMany(DetailPesanan::class, 'id_pesanan');
    }

    public function pembayaran()
    {
        return $this->hasOne(Pembayaran::class, 'id_pesanan');
    }
}
