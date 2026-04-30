<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Meja extends Model
{
    use HasUuids;

    protected $table = 'mejas';

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'nomor_meja',
        'qr_code',
    ];

    public function pesanans()
    {
        return $this->hasMany(Pesanan::class, 'id_meja');
    }
}
