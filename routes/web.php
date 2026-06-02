<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PemesananController;
use App\Http\Controllers\DapurController;
use App\Http\Controllers\KasirController;
use App\Http\Controllers\Admin\MenuController;
use App\Http\Controllers\Admin\KategoriMenuController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\LaporanController;
use App\Http\Controllers\Admin\MejaController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\DashboardController;


Route::get('/menu/{meja}', [PemesananController::class, 'index'])->name('menu.index');
Route::post('/cart/add/{menu}', [PemesananController::class, 'add'])->name('cart.add');
Route::post('/cart/min/{menu}', [PemesananController::class, 'min'])->name('cart.min');
Route::get('/menu/{meja}/checkout', [PemesananController::class, 'checkout'])->name('menu.checkout');
Route::post('/menu/{meja}/confirm', [PemesananController::class, 'confirmOrder'])->name('menu.confirm');
Route::get('/menu/{meja}/pesanan', [PemesananController::class, 'pesanan'])->name('menu.pesanan');

Route::get('/', [AuthController::class, 'show'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.process');

Route::middleware(['auth'])->group(function () {

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // ==========================================
    // AREA ADMIN (Role 1)
    // ==========================================
    Route::middleware(['role:1'])->group(function () {
        Route::prefix('admin')->name('admin.')->group(function () {
            Route::resource('menu', MenuController::class);
            Route::resource('kategori-menu', KategoriMenuController::class);
            Route::resource('users', UserController::class);
            Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan.index');
            Route::get('/meja/cetak-semua-qr', [MejaController::class, 'cetakSemuaQr'])->name('meja.cetak-semua-qr');
            Route::get('/meja/{id}/cetak-qr', [MejaController::class, 'cetakQr'])->name('meja.cetak-qr');
            Route::resource('meja', MejaController::class);
            Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');
        });
    });



    // ==========================================
    // AREA KASIR (Role 2)
    // ==========================================
    Route::middleware(['role:2,1'])->group(function () {
        Route::get('/kasir', [KasirController::class, 'index'])->name('kasir.index');
        Route::get('/kasir/meja/{meja}', [KasirController::class, 'detailMeja'])->name('kasir.detail');
        Route::delete('/kasir/meja/{meja}/batalkan-baru', [KasirController::class, 'batalkanPesananBaru'])->name('kasir.meja.batalkan-baru');
        Route::post('/kasir/pembayaran/{meja}', [KasirController::class, 'bayar'])->name('kasir.bayar');
        Route::get('/kasir/nota/{pesanan}', [KasirController::class, 'nota'])->name('kasir.nota');

        Route::get('/kasir/meja/{meja}/tambah', [KasirController::class, 'tambahPesananForm'])->name('kasir.tambah.form');
        Route::post('/kasir/meja/{meja}/tambah', [KasirController::class, 'tambahPesananSimpan'])->name('kasir.tambah.simpan');
        Route::post('/kasir/meja/{meja}/hapus-cart', [KasirController::class, 'hapusFromCart'])->name('kasir.hapus.cart');
        Route::post('/kasir/meja/{meja}/konfirmasi-cart', [KasirController::class, 'konfirmasiCart'])->name('kasir.konfirmasi.cart');
        Route::post('/kasir/meja/{meja}/batalkan-cart', [KasirController::class, 'batalkanCart'])->name('kasir.batalkan.cart');
        Route::delete('/kasir/pesanan/{pesanan}/item/{detail}', [KasirController::class, 'deleteItem'])->name('kasir.item.destroy'); //hapus item tambahan
        Route::post('/kasir/menu/{menu}/toggle', [KasirController::class, 'toggleMenuStatus'])->name('kasir.menu.toggle');
        Route::post('/kasir/cek-meja', [KasirController::class, 'prosesPilihMeja'])->name('kasir.proses-pilih-meja');
    });

    // ==========================================
    // AREA DAPUR (Role 3)
    // ==========================================
    Route::middleware(['role:3,1'])->group(function () {
        Route::get('/dapur', [DapurController::class, 'index'])->name('dapur.index');
        Route::post('/dapur/{pesanan}/selesai', [DapurController::class, 'selesai'])->name('dapur.selesai');
        Route::post('/dapur/menu/{menu}/toggle', [DapurController::class, 'toggleMenuStatus'])->name('dapur.menu.toggle');
        Route::post('/dapur/{pesananId}/detail/{detailId}/delete', [DapurController::class, 'deleteDetailPesanan'])->name('dapur.detail.delete');
        Route::post('/dapur/{pesananId}/detail/{detailId}/update', [DapurController::class, 'updateDetailPesanan'])->name('dapur.detail.update');
        Route::post('/dapur/{pesananId}/detail/{detailId}/replace', [DapurController::class, 'replaceDetailPesanan'])->name('dapur.detail.replace');
    });
});
