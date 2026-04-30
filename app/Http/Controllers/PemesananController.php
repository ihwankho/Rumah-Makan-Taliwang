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


class PemesananController extends Controller
{
    public function index(Request $request, $meja)
    {
        $kategoriId = $request->query('kategori');
        $kategoris = KategoriMenu::orderBy('nama')->get();
        $menusQuery = Menu::where('is_aktif', true)
            ->where('perlu_dimasak', true)
            ->with('kategori');

        if ($kategoriId) {
            $menusQuery->where('kategori_menu_id', $kategoriId);
        }

        $menus = $menusQuery->orderBy('nama')->get();

        $cart = session('cart', []);
        $cartQty = array_sum(array_column($cart, 'qty'));
        $cartTotal = array_sum(array_map(fn($item) => $item['qty'] * $item['harga'], $cart));

        return view('pemesanan.index', compact(
            'meja',
            'kategoris',
            'menus',
            'kategoriId',
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
        $itemQty = 0; // Default qty jika item dihapus

        if (isset($cart[$menu->id])) {
            $cart[$menu->id]['qty'] -= 1;
            $itemQty = $cart[$menu->id]['qty'];

            if ($cart[$menu->id]['qty'] <= 0) {
                unset($cart[$menu->id]);
                $itemQty = 0;
            }
        }

        session(['cart' => $cart]);

        // Hitung total untuk dikirim ke Javascript
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

        return view('pemesanan.checkout', compact('meja', 'cart', 'cartQty', 'cartTotal'));
    }

    public function confirmOrder(Request $request, $mejaId)
    {
        $request->validate([
            'nama' => 'required|string|max:100',
            'tipe' => 'required|in:makan_ditempat,bungkus',
        ]);

        $cart = session('cart', []);

        if (count($cart) === 0) {
            return redirect()->route('menu.checkout', $mejaId)
                ->with('error', 'Keranjang masih kosong!');
        }

        // ✅ Validasi meja
        $meja = Meja::find($mejaId);
        if (!$meja) {
            return back()->with('error', 'Meja tidak ditemukan!');
        }

        $pesanan = DB::transaction(function () use ($request, $mejaId, $cart) {

            // 1) buat pelanggan
            $pelanggan = Pelanggan::create([
                'nama_pelanggan' => $request->nama,
            ]);

            // 2) hitung total
            $totalHarga = 0;
            foreach ($cart as $item) {
                $totalHarga += ($item['qty'] * $item['harga']);
            }

            // 3) buat pesanan
            $pesanan = Pesanan::create([
                'id_meja' => $mejaId,
                'id_pelanggan' => $pelanggan->id,
                'tipe_pesanan' => $request->tipe,
                'status_pesanan' => 'menunggu',
                'total_harga' => $totalHarga,
            ]);

            // 4) buat detail_pesanans
            foreach ($cart as $item) {
                DetailPesanan::create([
                    'id_pesanan' => $pesanan->id,
                    'id_menu' => $item['id'],
                    'jumlah' => $item['qty'],
                    'harga_satuan' => $item['harga'],
                    'subtotal' => $item['qty'] * $item['harga'],
                ]);
            }

            // 5) kosongkan cart
            session()->forget('cart');

            return $pesanan;
        });

        // simpan id pesanan terakhir agar halaman "pesanan saya" bisa tampil
        session(['last_pesanan_id' => $pesanan->id]);

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

        // Filter hanya detail pesanan dengan menu yang perlu dimasak
        $pesanan->detailPesanans = $pesanan->detailPesanans->filter(fn($detail) => $detail->menu->perlu_dimasak)->values();

        return view('pemesanan.pesanan', compact('mejaId', 'pesanan'));
    }
}
