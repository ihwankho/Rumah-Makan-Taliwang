<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Menu;
use App\Models\KategoriMenu;

class MenuSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Ambil ID Kategori (Pastikan KategoriMenuSeeder sudah jalan)
        $makanan = KategoriMenu::where('nama', 'Makanan')->first();
        $minuman = KategoriMenu::where('nama', 'Minuman')->first();

        // 2. Daftar Menu Khas Hj. Marfuah
        $menus = [
            // Menu Lama yang sudah Anda buat
            ['kategori_menu_id' => $makanan->id, 'nama' => 'Ayam Bakar Taliwang', 'harga' => 30000, 'deskripsi' => 'Ayam bakar sambal pedas khas Taliwang', 'gambar' => 'menus/ayambakartaliwang.jpeg', 'perlu_dimasak' => true, 'is_aktif' => true],
            ['kategori_menu_id' => $minuman->id, 'nama' => 'jeruk', 'harga' => 10000, 'deskripsi' => 'Jeruk peras alami', 'gambar' => 'menus/jeruk.jpeg', 'perlu_dimasak' => false, 'is_aktif' => true],

            // Penambahan 10 Menu Baru
            ['kategori_menu_id' => $makanan->id, 'nama' => 'Ayam Taliwang Bakar Madu', 'harga' => 55000, 'deskripsi' => 'Ayam kampung utuh pedas manis', 'gambar' => 'menus/taliwang_madu.jpeg', 'perlu_dimasak' => true, 'is_aktif' => true],
            ['kategori_menu_id' => $makanan->id, 'nama' => 'Ayam Taliwang Goreng', 'harga' => 50000, 'deskripsi' => 'Ayam kampung goreng bumbu meresap', 'gambar' => 'menus/taliwang_goreng.jpeg', 'perlu_dimasak' => true, 'is_aktif' => true],
            ['kategori_menu_id' => $makanan->id, 'nama' => 'Pelecing Kangkung Lombok', 'harga' => 15000, 'deskripsi' => 'Kangkung segar sambal pedas tabur kacang', 'gambar' => 'menus/pelecing.jpeg', 'perlu_dimasak' => true, 'is_aktif' => true],
            ['kategori_menu_id' => $makanan->id, 'nama' => 'Beberuk Terong', 'harga' => 12000, 'deskripsi' => 'Lalapan terong dan kacang panjang sambal segar', 'gambar' => 'menus/beberuk.jpeg', 'perlu_dimasak' => false, 'is_aktif' => true],
            ['kategori_menu_id' => $makanan->id, 'nama' => 'Bebek Bakar Taliwang', 'harga' => 65000, 'deskripsi' => 'Bebek bakar empuk bumbu Taliwang', 'gambar' => 'menus/bebek_taliwang.jpeg', 'perlu_dimasak' => true, 'is_aktif' => true],
            ['kategori_menu_id' => $makanan->id, 'nama' => 'Gurame Bakar Plecing', 'harga' => 75000, 'deskripsi' => 'Gurame bakar bumbu plecing pedas mantap', 'gambar' => 'menus/gurame_bakar.jpeg', 'perlu_dimasak' => true, 'is_aktif' => true],
            ['kategori_menu_id' => $minuman->id, 'nama' => 'Es Jeruk Nipis', 'harga' => 8000, 'deskripsi' => 'Jeruk nipis segar penetral pedas', 'gambar' => 'menus/jeruk_nipis.jpeg', 'perlu_dimasak' => false, 'is_aktif' => true],
            ['kategori_menu_id' => $minuman->id, 'nama' => 'Es Beras Kencur', 'harga' => 10000, 'deskripsi' => 'Minuman tradisional dingin dan sehat', 'gambar' => 'menus/beras_kencur.jpeg', 'perlu_dimasak' => false, 'is_aktif' => true],
            ['kategori_menu_id' => $minuman->id, 'nama' => 'Es Teh Sereh', 'harga' => 12000, 'deskripsi' => 'Teh dingin aroma sereh wangi', 'gambar' => 'menus/teh_sereh.jpeg', 'perlu_dimasak' => false, 'is_aktif' => true],
            ['kategori_menu_id' => $minuman->id, 'nama' => 'Es Campur Spesial', 'harga' => 18000, 'deskripsi' => 'Buah, jelly, dan santan kelapa manis', 'gambar' => 'menus/es_campur.jpeg', 'perlu_dimasak' => false, 'is_aktif' => true],
            // Minuman Kemasan (Tidak Perlu Dimasak)
            ['kategori_menu_id' => $minuman->id, 'nama' => 'Sprite', 'harga' => 7000, 'deskripsi' => 'Minuman soda kemasan botol', 'gambar' => 'menus/sprite.jpeg', 'perlu_dimasak' => false, 'is_aktif' => true],
            ['kategori_menu_id' => $minuman->id, 'nama' => 'Pocari Sweat', 'harga' => 8000, 'deskripsi' => 'Minuman isotonik kemasan botol', 'gambar' => 'menus/pocari.jpeg', 'perlu_dimasak' => false, 'is_aktif' => true],
            ['kategori_menu_id' => $minuman->id, 'nama' => 'Teh Botol Sosro', 'harga' => 6000, 'deskripsi' => 'Teh melati dalam kemasan botol', 'gambar' => 'menus/tehbotol.jpeg', 'perlu_dimasak' => false, 'is_aktif' => true],
            ['kategori_menu_id' => $minuman->id, 'nama' => 'Air Mineral 600ml', 'harga' => 4000, 'deskripsi' => 'Air minum kemasan botol sedang', 'gambar' => 'menus/airmineral.jpeg', 'perlu_dimasak' => false, 'is_aktif' => true],

            // Snack / Makanan Tambahan (Tidak Perlu Dimasak)
            ['kategori_menu_id' => $makanan->id, 'nama' => 'Kerupuk Putih Kaleng', 'harga' => 2000, 'deskripsi' => 'Kerupuk mawar renyah', 'gambar' => 'menus/kerupuk_putih.jpeg', 'perlu_dimasak' => false, 'is_aktif' => true],
            ['kategori_menu_id' => $makanan->id, 'nama' => 'Kerupuk Kulit Sapi', 'harga' => 5000, 'deskripsi' => 'Kerupuk kulit sapi asli yang gurih', 'gambar' => 'menus/kerupuk_kulit.jpeg', 'perlu_dimasak' => false, 'is_aktif' => true],
            ['kategori_menu_id' => $makanan->id, 'nama' => 'Emping Melinjo', 'harga' => 5000, 'deskripsi' => 'Emping melinjo goreng gurih', 'gambar' => 'menus/emping.jpeg', 'perlu_dimasak' => false, 'is_aktif' => true],
            ['kategori_menu_id' => $makanan->id, 'nama' => 'Peyek Kacang', 'harga' => 3000, 'deskripsi' => 'Peyek kacang tanah garing', 'gambar' => 'menus/peyek.jpeg', 'perlu_dimasak' => false, 'is_aktif' => true],
        ];

        // 3. Proses Insert atau Update agar tidak duplikat
        foreach ($menus as $item) {
            Menu::updateOrInsert(
                ['nama' => $item['nama']], // Unik berdasarkan nama
                array_merge($item, ['created_at' => now(), 'updated_at' => now()])
            );
        }
    }
}
