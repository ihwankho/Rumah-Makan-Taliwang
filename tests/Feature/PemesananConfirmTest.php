<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;
use App\Models\Meja;
use App\Models\Menu;
use App\Models\Pelanggan;
use App\Models\Pesanan;
use App\Models\DetailPesanan;
use App\Events\PesananBaruDibuat;

class PemesananConfirmTest extends TestCase
{
    use RefreshDatabase;

    // TC-01: Gagal karena keranjang kosong
    public function test_gagal_checkout_karena_keranjang_kosong()
    {
        $meja = Meja::factory()->create();

        // Simulasikan POST tanpa mengisi session cart
        $response = $this->post("/menu/{$meja->id}/confirm", [
            'tipe' => 'makan_ditempat',
            'nama' => 'Pelanggan Fiktif',
        ]);

        // Ekspektasi: Redirect ke halaman checkout dengan pesan error
        $response->assertRedirect(route('menu.checkout', $meja->id));
        $response->assertSessionHas('error', 'Keranjang masih kosong!');
    }

    // TC-02: Skenario B (Meja Kosong -> Pelanggan Baru -> Pesanan Baru) + Redirect Default
    public function test_meja_kosong_buat_pelanggan_dan_pesanan_baru()
    {
        Event::fake(); // Tahan notifikasi event agar tidak error

        $meja = Meja::factory()->create();
        $menu = Menu::factory()->create(['harga' => 15000]);

        // Isi keranjang
        session(['cart' => [
            $menu->id => ['id' => $menu->id, 'harga' => 15000, 'qty' => 2]
        ]]);

        $response = $this->post("/menu/{$meja->id}/confirm", [
            'tipe' => 'makan_ditempat',
            'nama' => 'Budi',
        ]);

        // Cek database: Pelanggan Budi tercipta, Pesanan baru tercipta
        $this->assertDatabaseHas('pelanggans', ['nama_pelanggan' => 'Budi']);
        $this->assertDatabaseHas('pesanans', [
            'id_meja' => $meja->id,
            'tipe_pesanan' => 'makan_ditempat',
            'total_harga' => 30000 // 15000 * 2
        ]);

        // Ekspektasi: Keranjang kosong, redirect ke rute pesanan pelanggan
        $this->assertNull(session('cart'));
        $response->assertRedirect(route('menu.pesanan', $meja->id));
    }

    // TC-03: Skenario B (Meja Terisi -> Tipe Beda (Bungkus) -> Pinjam ID Pelanggan)
    public function test_meja_terisi_pesan_tipe_berbeda_maka_pinjam_pelanggan_lama()
    {
        Event::fake();
        $meja = Meja::factory()->create();
        $pelanggan = Pelanggan::factory()->create(['nama_pelanggan' => 'Andi']);
        $menu = Menu::factory()->create(['harga' => 10000]);

        // Kondisi Awal: Andi sedang makan di tempat (pesanan aktif)
        Pesanan::create([
            'id_meja' => $meja->id,
            'id_pelanggan' => $pelanggan->id,
            'tipe_pesanan' => 'makan_ditempat',
            'status_pesanan' => 'menunggu',
            'total_harga' => 50000,
        ]);

        session(['cart' => [
            $menu->id => ['id' => $menu->id, 'harga' => 10000, 'qty' => 1]
        ]]);

        // Andi nambah pesanan, tapi untuk BUNGKUS
        $response = $this->post("/menu/{$meja->id}/confirm", [
            'tipe' => 'bungkus',
            // Tidak mengirim nama karena pesananAktif sudah ada
        ]);

        // Ekspektasi: Pesanan baru tercipta dengan tipe bungkus, tapi ID pelanggan tetap ID Andi
        $this->assertDatabaseHas('pesanans', [
            'id_pelanggan' => $pelanggan->id,
            'tipe_pesanan' => 'bungkus',
        ]);
        // Pastikan jumlah pelanggan tetap 1 (tidak ada duplikasi nama)
        $this->assertDatabaseCount('pelanggans', 1);
    }

    // TC-04: Skenario A (Tipe Sama -> Gabung Pesanan -> Update Menu Lama & Create Menu Baru) + Redirect Kasir
    public function test_gabung_pesanan_jika_tipe_sama_serta_redirect_ke_kasir()
    {
        Event::fake();
        $meja = Meja::factory()->create(['nomor_meja' => 'A1']);
        $pelanggan = Pelanggan::factory()->create();

        $menuLama = Menu::factory()->create(['harga' => 10000]); // Es Teh
        $menuBaru = Menu::factory()->create(['harga' => 20000]); // Nasi Goreng

        // Kondisi Awal: Sudah ada pesanan makan_ditempat dengan 1 Es Teh
        $pesananEksisting = Pesanan::create([
            'id_meja' => $meja->id,
            'id_pelanggan' => $pelanggan->id,
            'tipe_pesanan' => 'makan_ditempat',
            'status_pesanan' => 'menunggu',
            'total_harga' => 10000,
        ]);
        DetailPesanan::create([
            'id_pesanan' => $pesananEksisting->id,
            'id_menu' => $menuLama->id,
            'jumlah' => 1,
            'harga_satuan' => 10000,
            'subtotal' => 10000,
        ]);

        // Skenario: Nambah 1 Es Teh lagi (Menu Lama) dan 1 Nasi Goreng (Menu Baru) via KASIR
        session(['cart' => [
            $menuLama->id => ['id' => $menuLama->id, 'harga' => 10000, 'qty' => 1],
            $menuBaru->id => ['id' => $menuBaru->id, 'harga' => 20000, 'qty' => 1],
        ]]);

        $response = $this->post("/menu/{$meja->id}/confirm", [
            'tipe' => 'makan_ditempat',
            'from' => 'kasir' // Simulasi tombol dipencet oleh kasir
        ]);

        // Ekspektasi 1: Update Qty Es Teh menjadi 2
        $this->assertDatabaseHas('detail_pesanans', [
            'id_menu' => $menuLama->id,
            'jumlah' => 2,
            'subtotal' => 20000, // 10000 * 2
        ]);

        // Ekspektasi 2: Nasi Goreng dimasukkan sebagai baris baru
        $this->assertDatabaseHas('detail_pesanans', [
            'id_menu' => $menuBaru->id,
            'jumlah' => 1,
            'subtotal' => 20000,
        ]);

        // Ekspektasi 3: Total harga pesanan utama ter-update (20000 + 20000 = 40000)
        $this->assertDatabaseHas('pesanans', [
            'id' => $pesananEksisting->id,
            'total_harga' => 40000,
        ]);

        // Ekspektasi 4: Redirect ke kasir karena ada parameter from=kasir
        $response->assertRedirect(route('kasir.index'));
        $response->assertSessionHas('success', 'Pesanan meja A1 berhasil diproses ✅');
    }
}

