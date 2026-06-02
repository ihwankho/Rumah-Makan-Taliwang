<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Meja;
use App\Models\Pelanggan;
use App\Models\Pesanan;
use App\Models\DetailPesanan;
use App\Models\KategoriMenu;
use App\Models\Menu;
use Illuminate\Support\Facades\DB;
use App\Events\PesananBaruDibuat;


class PemesananController extends Controller
{
    public function index(Request $request, $meja)
    {
        $kategoris = KategoriMenu::orderBy('nama')->get();

        $menus = Menu::where('is_aktif', true)
            ->where('perlu_dimasak', true)
            ->with('kategori')
            ->orderBy('nama')
            ->get();

        $cart = session('cart', []);
        $cartQty = array_sum(array_column($cart, 'qty'));
        $cartTotal = array_sum(array_map(fn($item) => $item['qty'] * $item['harga'], $cart));

        return view('pemesanan.index', compact(
            'meja',
            'kategoris',
            'menus',
            'cart',
            'cartQty',
            'cartTotal'
        ));
    }

    public function add(Menu $menu)
    {
        $cart = session('cart', []);

        if (!isset($cart[$menu->id])) {
            $cart[$menu->id] = [
                'id' => $menu->id,
                'nama' => $menu->nama,
                'harga' => $menu->harga,
                'gambar' => $menu->gambar,
                'qty' => 0,
            ];
        }

        $cart[$menu->id]['qty'] += 1;
        session(['cart' => $cart]);

        // Hitung total untuk dikirim ke Javascript
        $cartQty = array_sum(array_column($cart, 'qty'));
        $cartTotal = array_sum(array_map(fn($item) => $item['qty'] * $item['harga'], $cart));

        return response()->json([
            'status' => 'success',
            'item_qty' => $cart[$menu->id]['qty'],
            'cart_qty' => $cartQty,
            'cart_total' => number_format($cartTotal, 0, ',', '.')
        ]);
    }

    public function min(Menu $menu)
    {
        $cart = session('cart', []);
        $itemQty = 0;

        if (isset($cart[$menu->id])) {
            $cart[$menu->id]['qty'] -= 1;
            $itemQty = $cart[$menu->id]['qty'];

            if ($cart[$menu->id]['qty'] <= 0) {
                unset($cart[$menu->id]);
                $itemQty = 0;
            }
        }

        session(['cart' => $cart]);

        $cartQty = array_sum(array_column($cart, 'qty'));
        $cartTotal = array_sum(array_map(fn($item) => $item['qty'] * $item['harga'], $cart));

        return response()->json([
            'status' => 'success',
            'item_qty' => $itemQty,
            'cart_qty' => $cartQty,
            'cart_total' => number_format($cartTotal, 0, ',', '.')
        ]);
    }
    public function checkout($meja)
    {
        $cart = session('cart', []);
        $cartQty = array_sum(array_column($cart, 'qty'));
        $cartTotal = array_sum(array_map(fn($item) => $item['qty'] * $item['harga'], $cart));

        $pesananAktif = Pesanan::where('id_meja', $meja)
            ->where('status_pesanan', '!=', 'dibayar')
            ->first();

        return view('pemesanan.checkout', compact('meja', 'cart', 'cartQty', 'cartTotal', 'pesananAktif'));
    }

    public function confirmOrder(Request $request, $mejaId)
    {
        // 1. Cek apakah ada SEMBARANG pesanan aktif (untuk deteksi meja kosong/terisi)
        $pesananAktif = Pesanan::where('id_meja', $mejaId)
            ->where('status_pesanan', '!=', 'dibayar')
            ->first();

        // Validasi input
        $rules = [
            'tipe' => 'required|in:makan_ditempat,bungkus',
        ];
        if (!$pesananAktif) {
            $rules['nama'] = 'required|string|max:100'; // Nama wajib jika meja kosong
        }
        $request->validate($rules);

        $cart = session('cart', []);
        if (count($cart) === 0) {
            return redirect()->route('menu.checkout', $mejaId)
                ->with('error', 'Keranjang masih kosong!');
        }

        $meja = Meja::find($mejaId);
        if (!$meja) {
            return back()->with('error', 'Meja tidak ditemukan!');
        }

        // Hitung total keranjang saat ini
        $cartTotal = 0;
        foreach ($cart as $item) {
            $cartTotal += ($item['qty'] * $item['harga']);
        }

        $pesanan = DB::transaction(function () use ($request, $mejaId, $cart, $cartTotal, $pesananAktif) {

            // 2. CEK PESANAN DENGAN TIPE YANG SAMA (KUNCI PERBAIKAN LOGIKA)
            $pesananSamaTipe = Pesanan::where('id_meja', $mejaId)
                ->whereNotIn('status_pesanan', ['selesai', 'dibayar'])
                ->where('tipe_pesanan', $request->tipe) // Cari yang tipenya sama persis
                ->first();

            if ($pesananSamaTipe) {
                // ==========================================
                // SKENARIO A: SUDAH ADA PESANAN DENGAN TIPE TERSEBUT
                // (Misal: Nambah es teh untuk diminum di tempat)
                // ==========================================
                foreach ($cart as $item) {
                    // CEK DULU: Apakah menu ini sudah ada di pesanan ini?
                    $detailExisting = DetailPesanan::where('id_pesanan', $pesananSamaTipe->id)
                        ->where('id_menu', $item['id'])
                        ->first();

                    if ($detailExisting) {
                        // JIKA SUDAH ADA: Tambah jumlah (qty) dan hitung ulang subtotalnya
                        $qtyBaru = $detailExisting->jumlah + $item['qty'];
                        $detailExisting->update([
                            'jumlah'   => $qtyBaru,
                            'subtotal' => $qtyBaru * $detailExisting->harga_satuan,
                        ]);
                    } else {
                        // JIKA BELUM ADA: Buat baris baru di nota
                        DetailPesanan::create([
                            'id_pesanan'   => $pesananSamaTipe->id,
                            'id_menu'      => $item['id'],
                            'jumlah'       => $item['qty'],
                            'harga_satuan' => $item['harga'],
                            'subtotal'     => $item['qty'] * $item['harga'],
                        ]);
                    }
                }

                // Update total harga utama secara akurat (menjumlahkan semua subtotal terbaru)
                $totalTerbaru = DetailPesanan::where('id_pesanan', $pesananSamaTipe->id)->sum('subtotal');
                $pesananSamaTipe->update([
                    'total_harga' => $totalTerbaru
                ]);

                $hasilPesanan = $pesananSamaTipe;
            } else {
                // ==========================================
                // SKENARIO B: BELUM ADA PESANAN DENGAN TIPE TERSEBUT
                // (Misal: Meja kosong, ATAU nambah pesanan tapi untuk dibungkus)
                // ==========================================

                if ($pesananAktif) {
                    // Meja ada isinya, pinjam ID pelanggan agar nama tidak dobel
                    $idPelanggan = $pesananAktif->id_pelanggan;
                } else {
                    // Meja benar-benar kosong, buat pelanggan baru
                    $pelanggan = Pelanggan::create([
                        'nama_pelanggan' => $request->nama,
                    ]);
                    $idPelanggan = $pelanggan->id;
                }

                // Buat ID Pesanan baru khusus untuk tipe ini
                $pesananBaru = Pesanan::create([
                    'id_meja'        => $mejaId,
                    'id_pelanggan'   => $idPelanggan,
                    'tipe_pesanan'   => $request->tipe,
                    'status_pesanan' => 'menunggu',
                    'total_harga'    => $cartTotal,
                ]);

                // Karena ini pesanan baru, tidak mungkin ada item duplikat, jadi langsung create
                foreach ($cart as $item) {
                    DetailPesanan::create([
                        'id_pesanan'   => $pesananBaru->id,
                        'id_menu'      => $item['id'],
                        'jumlah'       => $item['qty'],
                        'harga_satuan' => $item['harga'],
                        'subtotal'     => $item['qty'] * $item['harga'],
                    ]);
                }

                $hasilPesanan = $pesananBaru;
            }

            // Kosongkan cart setelah berhasil
            session()->forget('cart');

            return $hasilPesanan;
        });
        event(new PesananBaruDibuat(
            "Ada pesanan baru masuk!!!",
            $meja->nomor_meja
        ));

        // Simpan id pesanan terakhir agar halaman "pesanan saya" bisa tampil
        session(['last_pesanan_id' => $pesanan->id]);

        if ($request->from === 'kasir') {
            return redirect()->route('kasir.index')
                ->with('success', 'Pesanan meja ' . $meja->nomor_meja . ' berhasil diproses ✅');
        }

        return redirect()->route('menu.pesanan', $mejaId)
            ->with('success', 'Pesanan berhasil dibuat!');
    }

    public function pesanan($mejaId)
    {
        $pesananId = session('last_pesanan_id');

        if (!$pesananId) {
            return redirect()->route('menu.index', $mejaId);
        }

        $pesanan = Pesanan::with(['detailPesanans.menu', 'pelanggan', 'meja'])
            ->where('id', $pesananId)
            ->where('id_meja', $mejaId)
            ->first();

        if (!$pesanan) {
            return redirect()->route('menu.index', $mejaId);
        }

        $pesanan->detailPesanans = $pesanan->detailPesanans->filter(fn($detail) => $detail->menu->perlu_dimasak)->values();

        return view('pemesanan.pesanan', compact('mejaId', 'pesanan'));
    }
}
