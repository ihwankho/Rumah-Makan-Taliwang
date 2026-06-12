<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Pesanan;
use App\Models\DetailPesanan;
use App\Models\Menu;
use App\Models\User; // Pastikan ini ditambahkan

class DapurDeleteReplacePesananTest extends TestCase
{
    use RefreshDatabase;

    private $userDapur;

    // Fungsi setUp akan dijalankan otomatis SEBELUM setiap test dieksekusi
    protected function setUp(): void
    {
        parent::setUp();
        // Kita buat user bayangan dengan role 3 (Dapur)
        $this->userDapur = User::factory()->create(['role' => 3]);
    }

    // TC-01: Gagal hapus karena pesanan tidak ditemukan
    public function test_gagal_hapus_item_karena_pesanan_tidak_ditemukan()
    {
        // Tambahkan actingAs() untuk simulasi login
        $response = $this->actingAs($this->userDapur)
            ->post("/dapur/9999/detail/1/delete");

        $response->assertRedirect();
        $response->assertSessionHas('error', 'Pesanan tidak ditemukan.');
    }

    // TC-02: Gagal hapus karena item detail tidak ditemukan
    public function test_gagal_hapus_item_karena_detail_tidak_ditemukan()
    {
        $pesanan = Pesanan::factory()->create();

        $response = $this->actingAs($this->userDapur)
            ->post("/dapur/{$pesanan->id}/detail/9999/delete");

        $response->assertRedirect();
        $response->assertSessionHas('error', 'Item pesanan tidak ditemukan.');
    }

    // TC-03: Berhasil hapus item dan verifikasi kalkulasi harga
    public function test_berhasil_hapus_item_dan_total_harga_berkurang()
    {
        $pesanan = Pesanan::factory()->create(['total_harga' => 50000]);
        $menu    = Menu::factory()->create(['harga' => 15000]);
        $detail  = DetailPesanan::factory()->create([
            'id_pesanan'   => $pesanan->id,
            'id_menu'      => $menu->id,
            'jumlah'       => 1,
            'harga_satuan' => 15000,
            'subtotal'     => 15000,
        ]);

        $response = $this->actingAs($this->userDapur)
            ->post("/dapur/{$pesanan->id}/detail/{$detail->id}/delete");

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseMissing('detail_pesanans', ['id' => $detail->id]);
        $this->assertDatabaseHas('pesanans', [
            'id'          => $pesanan->id,
            'total_harga' => 35000,
        ]);
    }

    // TC-04: Gagal ganti item karena pesanan atau detail tidak ditemukan
    public function test_gagal_ganti_item_karena_pesanan_atau_detail_tidak_ditemukan()
    {
        $menu    = Menu::factory()->create();
        $pesanan = Pesanan::factory()->create();

        // Skenario 1: Pesanan tidak ditemukan
        $response1 = $this->actingAs($this->userDapur)
            ->post("/dapur/9999/detail/1/replace", [
                'menu_id' => $menu->id,
            ]);
        $response1->assertRedirect();
        $response1->assertSessionHas('error', 'Pesanan tidak ditemukan.');

        // Skenario 2: Detail tidak ditemukan
        $response2 = $this->actingAs($this->userDapur)
            ->post("/dapur/{$pesanan->id}/detail/9999/replace", [
                'menu_id' => $menu->id,
            ]);
        $response2->assertRedirect();
        $response2->assertSessionHas('error', 'Item pesanan tidak ditemukan.');
    }

    // TC-05: Berhasil ganti item dan verifikasi kalkulasi selisih harga
    public function test_berhasil_ganti_item_dan_total_harga_diperbarui()
    {
        $pesanan   = Pesanan::factory()->create(['total_harga' => 30000]);
        $menuLama  = Menu::factory()->create(['harga' => 10000]);
        $menuBaru  = Menu::factory()->create(['harga' => 20000]);
        $detail    = DetailPesanan::factory()->create([
            'id_pesanan'   => $pesanan->id,
            'id_menu'      => $menuLama->id,
            'jumlah'       => 1,
            'harga_satuan' => 10000,
            'subtotal'     => 10000,
        ]);

        $response = $this->actingAs($this->userDapur)
            ->post("/dapur/{$pesanan->id}/detail/{$detail->id}/replace", [
                'menu_id' => $menuBaru->id,
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('detail_pesanans', [
            'id'           => $detail->id,
            'id_menu'      => $menuBaru->id,
            'harga_satuan' => 20000,
            'subtotal'     => 20000,
        ]);

        $this->assertDatabaseHas('pesanans', [
            'id'          => $pesanan->id,
            'total_harga' => 40000,
        ]);
    }
}
