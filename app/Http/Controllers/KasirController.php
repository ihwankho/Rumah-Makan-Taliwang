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

        // 
        $pesananTerakhir = Pesanan::with(['pelanggan'])
            ->where('status_pesanan', '!=', 'dibayar')
            ->orderBy('created_at', 'desc')
            ->get()
            ->groupBy('id_meja')
            ->map(fn($items) => $items->first());

        $menus = Menu::orderBy('nama')->get();

        return view('kasir.index', compact('mejas', 'pesananTerakhir', 'menus'));
    }


    public function detailMeja(Meja $meja)
    {
        // 
        $daftarPesanan = Pesanan::with(['detailPesanans.menu', 'pelanggan', 'meja'])
            ->where('id_meja', $meja->id)
            ->where('status_pesanan', '!=', 'dibayar')
            ->orderBy('created_at', 'asc') 
            ->get();

        if ($daftarPesanan->isEmpty()) {
            return redirect()->route('kasir.index')->with('error', 'Meja tidak memiliki pesanan aktif.');
        }

        // 
        $totalGabungan = $daftarPesanan->sum('total_harga');

        //
        $pesananUtama = $daftarPesanan->first();

        //
        return view('kasir.detail', compact('meja', 'daftarPesanan', 'pesananUtama', 'totalGabungan'));
    }

    // 
    public function bayar(Request $request, Meja $meja)
    {
        $request->validate([
            'metode_pembayaran' => 'required|in:tunai,qris',
        ]);

        // 
        $idPesananNota = DB::transaction(function () use ($request, $meja) {

            // Ambil SEMUA pesanan aktif di meja beserta isi itemnya
            $daftarPesanan = Pesanan::with('detailPesanans')
                ->where('id_meja', $meja->id)
                ->where('status_pesanan', 'selesai')
                ->orderBy('created_at', 'asc')
                ->get();

            if ($daftarPesanan->isEmpty()) {
                throw new \Exception('Tidak ada pesanan aktif untuk dibayar.');
            }

            //
            $pesananUtama = $daftarPesanan->first();

            // 
            if ($daftarPesanan->count() > 1) {
                $totalTambahan = 0;

                foreach ($daftarPesanan->skip(1) as $pesananLain) {

                    foreach ($pesananLain->detailPesanans as $detailLain) {
                        // 
                        $detailUtama = DetailPesanan::where('id_pesanan', $pesananUtama->id)
                            ->where('id_menu', $detailLain->id_menu)
                            ->first();

                        if ($detailUtama) {
                            //
                            $detailUtama->update([
                                'jumlah' => $detailUtama->jumlah + $detailLain->jumlah,
                                'subtotal' => $detailUtama->subtotal + $detailLain->subtotal,
                            ]);
                            //
                            $detailLain->delete();
                        } else {
                            // 
                            $detailLain->update(['id_pesanan' => $pesananUtama->id]);
                        }
                    }

                    $totalTambahan += $pesananLain->total_harga;

                    // 
                    $pesananLain->delete();
                }

                // 
                $pesananUtama->update([
                    'total_harga' => $pesananUtama->total_harga + $totalTambahan
                ]);
            }

            //PROSES PEMBAYARAN
            Pembayaran::create([
                'id_pesanan' => $pesananUtama->id,
                'id_kasir' => auth()->id(),
                'metode_pembayaran' => $request->metode_pembayaran,
                'total_bayar' => $pesananUtama->total_harga,
                'status_pembayaran' => 'dibayar',
                'tanggal_bayar' => now(),
            ]);

            $pesananUtama->update([
                'status_pesanan' => 'dibayar',
            ]);

            return $pesananUtama->id;
        });

        return redirect()->route('kasir.nota', $idPesananNota)
            ->with('success', 'Pembayaran berhasil diproses ✅');
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
            ->where('status_pesanan', '!=', 'dibayar')
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

        // Cek  cart
        $itemKey = null;
        foreach ($cart as $key => $item) {
            if ($item['id_menu'] == $menu->id) {
                $itemKey = $key;
                break;
            }
        }

        if ($itemKey !== null) {
            // 
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

        //
        session(["cart_meja_{$meja->id}" => $cart]);

        return redirect()->route('kasir.tambah.form', $meja->id);
    }

    public function hapusFromCart(Request $request, Meja $meja)
    {
        $request->validate([
            'menu_id' => 'required|exists:menus,id',
        ]);

        $cart = session("cart_meja_{$meja->id}", []);

        // Hapus item cart
        $cart = array_filter($cart, function ($item) use ($request) {
            return $item['id_menu'] != $request->menu_id;
        });

        // Reset array key
        $cart = array_values($cart);

        // Simpan kembali ke session
        session(["cart_meja_{$meja->id}" => $cart]);

        return redirect()->route('kasir.tambah.form', $meja->id);
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

    public function batalkanPesananBaru(Meja $meja)
    {
        // Ambil  pesanan
        $pesanansBaru = Pesanan::where('id_meja', $meja->id)
            ->whereNotIn('status_pesanan', ['selesai', 'dibayar'])
            ->get();

        //
        if ($pesanansBaru->isEmpty()) {
            return redirect()->route('kasir.detail', $meja->id)
                ->with('error', 'Gagal! Semua pesanan di meja ini sudah selesai dimasak.');
        }

        //Hapus pesanan yang masih baru
        DB::transaction(function () use ($pesanansBaru) {
            $pesananIds = $pesanansBaru->pluck('id');

            // Hapus detail menunya
            DetailPesanan::whereIn('id_pesanan', $pesananIds)->delete();

            // Hapus induk pesanannya
            Pesanan::whereIn('id', $pesananIds)->delete();
        });

        //CEK SISA PESANAN
        $sisaPesanan = Pesanan::where('id_meja', $meja->id)->exists();

        if (!$sisaPesanan) {
            // 
            return redirect()->route('kasir.index')
                ->with('success', 'Seluruh pesanan di meja ' . $meja->nomor_meja . ' berhasil dibatalkan.');
        } else {
            //
            return redirect()->route('kasir.detail', $meja->id)
                ->with('success', 'Pesanan tambahan berhasil dibatalkan');
        }
    }

    public function deleteItem($pesananId, $detailId)
    {
        $pesanan = Pesanan::findOrFail($pesananId);
        $detail = $pesanan->detailPesanans()->findOrFail($detailId);

        $pesanan->update([
            'total_harga' => $pesanan->total_harga - $detail->subtotal
        ]);

        $detail->delete();

        return back()->with('success', 'Item pesanan berhasil dihapus dari tagihan.');
    }
    public function toggleMenuStatus(Request $request, Menu $menu)
    {
        $menu->update([
            'is_aktif' => !$menu->is_aktif,
        ]);
        if ($request->wantsJson() || $request->ajax() || $request->header('Accept') == 'application/json') {
            return response()->json([
                'success' => true,
                'message' => 'Status menu "' . $menu->nama . '" berhasil diperbarui.',
                'is_aktif' => $menu->is_aktif
            ]);
        }

        return back()->with('success', 'Status menu "' . $menu->nama . '" berhasil diperbarui.');
    }
    public function prosesPilihMeja(Request $request)
    {
        $request->validate([
            'nomor_meja' => 'required'
        ]);

        // Cari meja berdasarkan nomor yang diinput
        $meja = Meja::where('nomor_meja', $request->nomor_meja)->first();

        if (!$meja) {
            return back()->with('error', "Meja nomor {$request->nomor_meja} tidak ditemukan.");
        }

        // Arahkan ke halaman pemesanan pelanggan
        return redirect()->route('menu.index', ['meja' => $meja->id, 'from' => 'kasir']);
    }
}
