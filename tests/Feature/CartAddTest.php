<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Menu;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CartAddTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function menambah_menu_ke_cart_yang_masih_kosong()
    {
        $menu = Menu::factory()->create();

        $this->post(route('cart.add', $menu->id));

        $cart = session('cart');

        $this->assertArrayHasKey($menu->id, $cart);
        $this->assertEquals(1, $cart[$menu->id]['qty']);
    }

    /** @test */
    public function menambah_menu_yang_sudah_ada_di_cart()
    {
        $menu = Menu::factory()->create();

        // pemanggilan pertama → IF TRUE
        $this->post(route('cart.add', $menu->id));

        // pemanggilan kedua → IF FALSE
        $this->post(route('cart.add', $menu->id));

        $cart = session('cart');

        $this->assertEquals(2, $cart[$menu->id]['qty']);
    }
}
