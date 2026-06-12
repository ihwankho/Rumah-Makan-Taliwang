<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;

class AuthLoginTest extends TestCase
{
    // Ini memastikan tabel dihapus & dibuat ulang HANYA di database rumahmakan_test
    use RefreshDatabase;

    // TC-01: Login Gagal
    public function test_login_gagal_karena_kredensial_salah()
    {
        $response = $this->post('/login', [
            'username' => 'user_tidak_ada',
            'password' => 'password_salah',
        ]);

        $response->assertStatus(302);
        $response->assertSessionHas('error', 'Username atau password salah ❌');
        $this->assertGuest();
    }

    // TC-02: Login Admin (Role 1)
    public function test_login_berhasil_sebagai_admin()
    {
        $admin = User::factory()->create([
            'username' => 'admin123',
            'password' => bcrypt('123456'),
            'role' => 1,
        ]);

        $response = $this->post('/login', [
            'username' => 'admin123',
            'password' => '123456',
        ]);

        $this->assertAuthenticatedAs($admin);
        $response->assertRedirect('/admin/dashboard');
    }

    // TC-03: Login Kasir (Role 2)
    public function test_login_berhasil_sebagai_kasir()
    {
        $kasir = User::factory()->create([
            'username' => 'kasir123',
            'password' => bcrypt('123456'),
            'role' => 2,
        ]);

        $response = $this->post('/login', [
            'username' => 'kasir123',
            'password' => '123456',
        ]);

        $this->assertAuthenticatedAs($kasir);
        $response->assertRedirect('/kasir');
    }

    // TC-04: Login Dapur (Role selain 1 & 2)
    public function test_login_berhasil_sebagai_dapur()
    {
        $dapur = User::factory()->create([
            'username' => 'dapur123',
            'password' => bcrypt('123456'),
            'role' => 3,
        ]);

        $response = $this->post('/login', [
            'username' => 'dapur123',
            'password' => '123456',
        ]);

        $this->assertAuthenticatedAs($dapur);
        $response->assertRedirect('/dapur');
    }
}
