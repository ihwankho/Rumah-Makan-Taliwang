<?php

namespace App\Http\Controllers;

use App\Models\Meja;
use App\Models\Pesanan;
use App\Models\Pembayaran;
use App\Models\Menu;
use App\Models\DetailPesanan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KasirController extends Controller
{
    public function index()
    {
        $mejas = Meja::orderByRaw('CAST(nomor_meja AS UNSIGNED) ASC')->get();

        // Ambil pesanan terakhir per meja yang statusnya selesai (siap dibayar)
        $pesananTerakhir = Pesanan::with(['pelanggan'])
            ->where('status_pesanan', 'menunggu')
            ->orderBy('created_at', 'desc')
            ->get()
            ->groupBy('id_meja')
            ->map(fn($items) => $items->first()); // ambil yang paling terbaru per meja

        return view('kasir.index', compact('mejas', 'pesananTerakhir'));
    }


    public function detailMeja(Meja $meja)
    {
        $pesanan = Pesanan::with(['detailPesanans.menu', 'pelanggan', 'meja'])
            ->where('id_meja', $meja->id)
            ->where('status_pesanan', '!=', 'dibayar')  // ✅ semua kecuali dibayar
            ->latest()
            ->first();

        return view('kasir.detail', compact('meja', 'pesanan'));
    }


    public function bayar(Request $request, Pesanan $pesanan)
    {
        $request->validate([
            'metode_pembayaran' => 'required|in:tunai,qris',
        ]);

        DB::transaction(function () use ($request, $pesanan) {

            Pembayaran::create([
                'id_pesanan' => $pesanan->id,
                'id_kasir' => 1, // sementara (nanti ambil dari auth user)
                'metode_pembayaran' => $request->metode_pembayaran,
                'total_bayar' => $pesanan->total_harga,
                'status_pembayaran' => 'dibayar',
                'tanggal_bayar' => now(),
            ]);

            $pesanan->update([
                'status_pesanan' => 'dibayar',
            ]);
        });

        return redirect()->route('kasir.nota', $pesanan->id)
            ->with('success', 'Pembayaran berhasil ✅');
    }
    public function nota(Pesanan $pesanan)
    {
        $pesanan->load(['detailPesanans.menu', 'pelanggan', 'meja', 'pembayaran']);

        return view('kasir.nota', compact('pesanan'));
    }

    public function tambahPesananForm(Meja $meja)
    {
        $pesanan = Pesanan::with(['detailPesanans.menu', 'pelanggan', 'meja'])
            ->where('id_meja', $meja->id)
            ->where('status_pesanan', '!=', 'dibayar')  // ✅ semua kecuali dibayar
            ->latest()
            ->first();

        if (!$pesanan) {
            return redirect()->route('kasir.detail', $meja->id)
                ->with('error', 'Tidak ada pesanan aktif untuk meja ini.');
        }

        $menusNonMasak = Menu::where('is_aktif', true)
            ->where('perlu_dimasak', false)
            ->orderBy('nama')
            ->get();

        // Ambil keranjang sementara dari session
        $cart = session("cart_meja_{$meja->id}", []);

        return view('kasir.tambah_pesanan', compact('meja', 'pesanan', 'menusNonMasak', 'cart'));
    }

    public function tambahPesananSimpan(Request $request, Meja $meja)
    {
        $request->validate([
            'menu_id' => 'required|exists:menus,id',
            'jumlah' => 'required|integer|min:1|max:50',
        ]);

        $menu = Menu::find($request->menu_id);

        if ($menu->perlu_dimasak) {
            return back()->with('error', 'Menu ini tidak bisa ditambah dari kasir (karena perlu dimasak).');
        }

        // Ambil keranjang dari session
        $cart = session("cart_meja_{$meja->id}", []);

        $jumlahTambah = (int) $request->jumlah;
        $subtotal = $menu->harga * $jumlahTambah;

        // Cek apakah menu ini sudah ada di cart
        $itemKey = null;
        foreach ($cart as $key => $item) {
            if ($item['id_menu'] == $menu->id) {
                $itemKey = $key;
                break;
            }
        }

        if ($itemKey !== null) {
            // Jika sudah ada, tambahkan jumlahnya
            $cart[$itemKey]['jumlah'] += $jumlahTambah;
            $cart[$itemKey]['subtotal'] = $cart[$itemKey]['jumlah'] * $menu->harga;
        } else {
            // Jika belum ada, tambahkan item baru ke cart
            $cart[] = [
                'id_menu' => $menu->id,
                'nama' => $menu->nama,
                'harga_satuan' => $menu->harga,
                'jumlah' => $jumlahTambah,
                'subtotal' => $subtotal,
            ];
        }

        // Simpan keranjang ke session
        session(["cart_meja_{$meja->id}" => $cart]);

        return redirect()->route('kasir.tambah.form', $meja->id)
            ->with('success', 'Menu ditambahkan ke keranjang ✅');
    }

    public function hapusFromCart(Request $request, Meja $meja)
    {
        $request->validate([
            'menu_id' => 'required|exists:menus,id',
        ]);

        $cart = session("cart_meja_{$meja->id}", []);

        // Hapus item dari cart
        $cart = array_filter($cart, function ($item) use ($request) {
            return $item['id_menu'] != $request->menu_id;
        });

        // Reset array key
        $cart = array_values($cart);

        // Simpan kembali ke session
        session(["cart_meja_{$meja->id}" => $cart]);

        return redirect()->route('kasir.tambah.form', $meja->id)
            ->with('success', 'Item dihapus dari keranjang ✅');
    }

    public function konfirmasiCart(Meja $meja)
    {
        $pesanan = Pesanan::where('id_meja', $meja->id)
            ->where('status_pesanan', '!=', 'dibayar')
            ->latest()
            ->first();

        if (!$pesanan) {
            return redirect()->route('kasir.detail', $meja->id)
                ->with('error', 'Tidak ada pesanan aktif untuk meja ini.');
        }

        $cart = session("cart_meja_{$meja->id}", []);

        if (empty($cart)) {
            return redirect()->route('kasir.tambah.form', $meja->id)
                ->with('error', 'Keranjang kosong!');
        }

        DB::transaction(function () use ($cart, $pesanan) {
            $totalTambah = 0;

            foreach ($cart as $item) {
                // Cek apakah menu ini sudah ada di detail pesanan
                $detail = DetailPesanan::where('id_pesanan', $pesanan->id)
                    ->where('id_menu', $item['id_menu'])
                    ->first();

                if ($detail) {
                    // Jika sudah ada, update qty dan subtotal
                    $newJumlah = $detail->jumlah + $item['jumlah'];
                    $newSubtotal = $newJumlah * $item['harga_satuan'];

                    $detail->update([
                        'jumlah' => $newJumlah,
                        'harga_satuan' => $item['harga_satuan'],
                        'subtotal' => $newSubtotal,
                    ]);

                    $totalTambah += $newSubtotal - $detail->subtotal;
                } else {
                    // Jika belum ada, buat baru
                    DetailPesanan::create([
                        'id_pesanan' => $pesanan->id,
                        'id_menu' => $item['id_menu'],
                        'jumlah' => $item['jumlah'],
                        'harga_satuan' => $item['harga_satuan'],
                        'subtotal' => $item['subtotal'],
                    ]);

                    $totalTambah += $item['subtotal'];
                }
            }

            // Update total pesanan
            $pesanan->update([
                'total_harga' => $pesanan->total_harga + $totalTambah
            ]);
        });

        // Hapus cart dari session
        session()->forget("cart_meja_{$meja->id}");

        return redirect()->route('kasir.detail', $meja->id)
            ->with('success', 'Pesanan berhasil disimpan ✅');
    }

    public function batalkanCart(Meja $meja)
    {
        // Hapus cart dari session
        session()->forget("cart_meja_{$meja->id}");

        return redirect()->route('kasir.detail', $meja->id)
            ->with('info', 'Penambahan pesanan dibatalkan.');
    }
}
