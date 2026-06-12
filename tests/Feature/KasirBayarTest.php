<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Meja;
use App\Models\Pesanan;
use App\Models\DetailPesanan;
use App\Models\Menu;

class KasirBayarTest extends TestCase
{
    use RefreshDatabase;

    private $userKasir;

    protected function setUp(): void
    {
        parent::setUp();
        $this->userKasir = User::factory()->create(['role' => 2]);
    }

    // TC-01: Bayar ketika tidak ada pesanan selesai (lempar exception)
    public function test_bayar_gagal_karena_tidak_ada_pesanan_selesai()
    {
        $this->withoutExceptionHandling();

        $meja = Meja::factory()->create();

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Tidak ada pesanan aktif untuk dibayar.');

        // Menggunakan rute POST /kasir/pembayaran/{meja}
        $this->actingAs($this->userKasir)
            ->post("/kasir/pembayaran/{$meja->id}", [
                'metode_pembayaran' => 'tunai'
            ]);
    }

    // TC-02: Bayar satu pesanan tunggal (Tanpa penggabungan)
    public function test_bayar_berhasil_satu_pesanan_tunggal()
    {
        $meja = Meja::factory()->create();
        $pesanan = Pesanan::factory()->create([
            'id_meja' => $meja->id,
            'status_pesanan' => 'selesai',
            'total_harga' => 50000
        ]);

        $response = $this->actingAs($this->userKasir)
            ->post("/kasir/pembayaran/{$meja->id}", [
                'metode_pembayaran' => 'tunai'
            ]);

        $response->assertRedirect(route('kasir.nota', $pesanan->id));
        $this->assertDatabaseHas('pesanans', [
            'id' => $pesanan->id,
            'status_pesanan' => 'dibayar'
        ]);
        $this->assertDatabaseHas('pembayarans', [
            'id_pesanan' => $pesanan->id,
            'metode_pembayaran' => 'tunai'
        ]);
    }

    // TC-03: Bayar banyak pesanan tanpa item duplikat (Pindah item)
    public function test_bayar_gabung_multi_pesanan_tanpa_item_duplikat()
    {
        $meja = Meja::factory()->create();
        $menuA = Menu::factory()->create(['harga' => 10000]);
        $menuB = Menu::factory()->create(['harga' => 20000]);

        $pesanan1 = Pesanan::factory()->create(['id_meja' => $meja->id, 'status_pesanan' => 'selesai', 'total_harga' => 10000]);
        DetailPesanan::factory()->create(['id_pesanan' => $pesanan1->id, 'id_menu' => $menuA->id, 'jumlah' => 1, 'subtotal' => 10000]);

        $pesanan2 = Pesanan::factory()->create(['id_meja' => $meja->id, 'status_pesanan' => 'selesai', 'total_harga' => 20000, 'created_at' => now()->addMinute()]);
        DetailPesanan::factory()->create(['id_pesanan' => $pesanan2->id, 'id_menu' => $menuB->id, 'jumlah' => 1, 'subtotal' => 20000]);

        $this->actingAs($this->userKasir)
            ->post("/kasir/pembayaran/{$meja->id}", ['metode_pembayaran' => 'tunai']);

        $this->assertDatabaseMissing('pesanans', ['id' => $pesanan2->id]);
        $this->assertDatabaseHas('detail_pesanans', ['id_pesanan' => $pesanan1->id, 'id_menu' => $menuB->id]);
        $this->assertDatabaseHas('pesanans', ['id' => $pesanan1->id, 'total_harga' => 30000]);
    }

    // TC-04: Bayar banyak pesanan dengan item duplikat (Gabung qty)
    public function test_bayar_gabung_multi_pesanan_dengan_item_duplikat()
    {
        $meja = Meja::factory()->create();
        $menuA = Menu::factory()->create(['harga' => 10000]);

        $pesanan1 = Pesanan::factory()->create(['id_meja' => $meja->id, 'status_pesanan' => 'selesai', 'total_harga' => 10000]);
        DetailPesanan::factory()->create(['id_pesanan' => $pesanan1->id, 'id_menu' => $menuA->id, 'jumlah' => 1, 'subtotal' => 10000]);

        $pesanan2 = Pesanan::factory()->create(['id_meja' => $meja->id, 'status_pesanan' => 'selesai', 'total_harga' => 20000, 'created_at' => now()->addMinute()]);
        DetailPesanan::factory()->create(['id_pesanan' => $pesanan2->id, 'id_menu' => $menuA->id, 'jumlah' => 2, 'subtotal' => 20000]);

        $this->actingAs($this->userKasir)
            ->post("/kasir/pembayaran/{$meja->id}", ['metode_pembayaran' => 'qris']);

        $this->assertDatabaseHas('detail_pesanans', [
            'id_pesanan' => $pesanan1->id,
            'id_menu' => $menuA->id,
            'jumlah' => 3,
            'subtotal' => 30000
        ]);

        $this->assertDatabaseHas('pesanans', ['id' => $pesanan1->id, 'total_harga' => 30000]);
        $this->assertDatabaseMissing('pesanans', ['id' => $pesanan2->id]);
    }
}
