<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Menu;

class PemesananCartTest extends TestCase
{
    use RefreshDatabase;

    // TC-01: Menambah menu baru
    public function test_tambah_menu_baru_ke_keranjang()
    {
        $menu = Menu::factory()->create(['harga' => 20000]);

        // Menggunakan route /cart/add dari web.php
        $response = $this->postJson("/cart/add/{$menu->id}");

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'item_qty' => 1,
                'cart_qty' => 1,
                'cart_total' => '20.000',
            ]);

        $this->assertEquals(1, session("cart.{$menu->id}.qty"));
    }

    // TC-02: Menambah qty menu yang sudah ada
    public function test_tambah_qty_menu_yang_sudah_ada()
    {
        $menu = Menu::factory()->create(['harga' => 15000]);

        // Panggil 2 kali
        $this->postJson("/cart/add/{$menu->id}");
        $response = $this->postJson("/cart/add/{$menu->id}");

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'item_qty' => 2,
                'cart_qty' => 2,
                'cart_total' => '30.000',
            ]);
    }

    // TC-03: Mengurangi qty menu
    public function test_kurangi_qty_menu_di_keranjang()
    {
        $menu = Menu::factory()->create(['harga' => 10000]);

        // Tambah 2 kali, lalu kurangi 1 kali menggunakan route /cart/min
        $this->postJson("/cart/add/{$menu->id}");
        $this->postJson("/cart/add/{$menu->id}");

        $response = $this->postJson("/cart/min/{$menu->id}");

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'item_qty' => 1,
                'cart_qty' => 1,
                'cart_total' => '10.000',
            ]);
    }

    // TC-04: Mengurangi qty hingga habis
    public function test_kurangi_qty_hingga_item_terhapus()
    {
        $menu = Menu::factory()->create(['harga' => 25000]);

        // Tambah 1 kali, lalu kurangi 1 kali
        $this->postJson("/cart/add/{$menu->id}");
        $response = $this->postJson("/cart/min/{$menu->id}");

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'item_qty' => 0,
                'cart_qty' => 0,
                'cart_total' => '0',
            ]);

        // Pastikan item benar-benar hilang dari session
        $this->assertNull(session("cart.{$menu->id}"));
    }

    // TC-05: Mengurangi menu yang tidak ada di keranjang
    public function test_kurangi_menu_yang_tidak_ada_di_keranjang()
    {
        $menu = Menu::factory()->create();

        $response = $this->postJson("/cart/min/{$menu->id}");

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'item_qty' => 0,
                'cart_qty' => 0,
                'cart_total' => '0',
            ]);
    }
}
