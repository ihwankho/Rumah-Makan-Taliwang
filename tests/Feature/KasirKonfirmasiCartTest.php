<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Meja;
use App\Models\Pesanan;
use App\Models\DetailPesanan;
use App\Models\Menu;

class KasirKonfirmasiCartTest extends TestCase
{
    use RefreshDatabase;

    private $userKasir;

    protected function setUp(): void
    {
        parent::setUp();
        // Role 2 berdasarkan middleware ['role:2,1']
        $this->userKasir = User::factory()->create(['role' => 2]);
    }

    // TC-01: Konfirmasi cart ketika tidak ada pesanan aktif di meja
    public function test_konfirmasi_cart_gagal_karena_tidak_ada_pesanan_aktif()
    {
        $meja = Meja::factory()->create();

        // Menggunakan rute POST /kasir/meja/{meja}/konfirmasi-cart
        $response = $this->actingAs($this->userKasir)
            ->post("/kasir/meja/{$meja->id}/konfirmasi-cart");

        $response->assertRedirect();
        $response->assertSessionHas('error');
    }

    // TC-02: Konfirmasi cart ketika cart kosong
    public function test_konfirmasi_cart_gagal_karena_keranjang_kosong()
    {
        $meja = Meja::factory()->create();
        Pesanan::factory()->create([
            'id_meja' => $meja->id,
            'status_pesanan' => 'menunggu'
        ]);

        $response = $this->actingAs($this->userKasir)
            ->post("/kasir/meja/{$meja->id}/konfirmasi-cart");

        $response->assertRedirect();
        $response->assertSessionHas('error', 'Keranjang kosong!');
    }

    // TC-03: Konfirmasi cart dengan item baru (Create detail baru)
    public function test_konfirmasi_cart_berhasil_buat_item_baru()
    {
        $meja = Meja::factory()->create();
        $pesanan = Pesanan::factory()->create([
            'id_meja' => $meja->id,
            'status_pesanan' => 'menunggu',
            'total_harga' => 0
        ]);
        $menu = Menu::factory()->create(['harga' => 20000]);

        session(["cart_meja_{$meja->id}" => [
            [
                'id_menu' => $menu->id,
                'harga_satuan' => 20000,
                'jumlah' => 2,
                'subtotal' => 40000
            ]
        ]]);

        $response = $this->actingAs($this->userKasir)
            ->post("/kasir/meja/{$meja->id}/konfirmasi-cart");

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('detail_pesanans', [
            'id_pesanan' => $pesanan->id,
            'id_menu' => $menu->id,
            'jumlah' => 2,
            'subtotal' => 40000
        ]);
        $this->assertDatabaseHas('pesanans', ['id' => $pesanan->id, 'total_harga' => 40000]);
        $this->assertNull(session("cart_meja_{$meja->id}"));
    }

    // TC-04: Konfirmasi cart dengan item yang sudah ada (Update qty item lama)
    public function test_konfirmasi_cart_berhasil_update_item_lama_duplikat()
    {
        $meja = Meja::factory()->create();
        $pesanan = Pesanan::factory()->create([
            'id_meja' => $meja->id,
            'status_pesanan' => 'menunggu',
            'total_harga' => 20000
        ]);
        $menu = Menu::factory()->create(['harga' => 20000]);

        DetailPesanan::factory()->create([
            'id_pesanan' => $pesanan->id,
            'id_menu' => $menu->id,
            'jumlah' => 1,
            'subtotal' => 20000
        ]);

        session(["cart_meja_{$meja->id}" => [
            [
                'id_menu' => $menu->id,
                'harga_satuan' => 20000,
                'jumlah' => 2,
                'subtotal' => 40000
            ]
        ]]);

        $response = $this->actingAs($this->userKasir)
            ->post("/kasir/meja/{$meja->id}/konfirmasi-cart");

        $response->assertRedirect();

        // 1 (lama) + 2 (baru) = 3
        $this->assertDatabaseHas('detail_pesanans', [
            'id_pesanan' => $pesanan->id,
            'id_menu' => $menu->id,
            'jumlah' => 3,
            'subtotal' => 60000
        ]);
    }
}
