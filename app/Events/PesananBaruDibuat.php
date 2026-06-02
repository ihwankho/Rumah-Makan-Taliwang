<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow; // PASTIKAN PAKAI INI
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

// WAJIB tambahkan implements ShouldBroadcastNow
class PesananBaruDibuat implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $pesan;
    public $nomor_meja;

    // Data yang mau dikirim ke dapur
    public function __construct($pesan, $nomor_meja)
    {
        $this->pesan = $pesan;
        $this->nomor_meja = $nomor_meja;
    }

    // Tentukan "frekuensi radio" tempat dapur mendengarkan
    public function broadcastOn(): array
    {
        return [
            new Channel('dapur-channel'),
        ];
    }
}
