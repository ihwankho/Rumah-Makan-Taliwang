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
        // 1. Ambil SEMUA pesanan aktif di meja ini (bisa lebih dari 1)
        $daftarPesanan = Pesanan::with(['detailPesanans.menu', 'pelanggan', 'meja'])
            ->where('id_meja', $meja->id)
            ->where('status_pesanan', '!=', 'dibayar')
            ->orderBy('created_at', 'asc') // Urutkan dari yang paling pertama
            ->get();

        if ($daftarPesanan->isEmpty()) {
            return redirect()->route('kasir.index')->with('error', 'Meja tidak memiliki pesanan aktif.');
        }

        // 2. Hitung total gabungan dari semua pesanan
        $totalGabungan = $daftarPesanan->sum('total_harga');

        // 3. Ambil data pesanan utama (pesanan yang pertama kali dibuat)
        $pesananUtama = $daftarPesanan->first();

        // Kirim 'daftarPesanan' dan 'totalGabungan' ke view
        return view('kasir.detail', compact('meja', 'daftarPesanan', 'pesananUtama', 'totalGabungan'));
    }

    // PERHATIKAN: Parameter berubah dari Pesanan $pesanan menjadi Meja $meja
    public function bayar(Request $request, Meja $meja)
    {
        $request->validate([
            'metode_pembayaran' => 'required|in:tunai,qris',
        ]);

        // Kita gunakan transaksi DB agar aman
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

            // Pesanan pertama akan menjadi "Wadah Utama" struk
            $pesananUtama = $daftarPesanan->first();

            // JIKA ADA PESANAN TAMBAHAN (BUNGKUS/LAINNYA), GABUNGKAN KE WADAH UTAMA
            if ($daftarPesanan->count() > 1) {
                $totalTambahan = 0;

                foreach ($daftarPesanan->skip(1) as $pesananLain) {

                    foreach ($pesananLain->detailPesanans as $detailLain) {
                        // KUNCI PERBAIKAN: Cek apakah menu ini sudah ada di Pesanan Utama?
                        $detailUtama = DetailPesanan::where('id_pesanan', $pesananUtama->id)
                            ->where('id_menu', $detailLain->id_menu)
                            ->first();

                        if ($detailUtama) {
                            // JIKA ADA: Tambahkan Qty dan Subtotalnya saja
                            $detailUtama->update([
                                'jumlah' => $detailUtama->jumlah + $detailLain->jumlah,
                                'subtotal' => $detailUtama->subtotal + $detailLain->subtotal,
                            ]);
                            // Hapus item dari pesanan cangkang
                            $detailLain->delete();
                        } else {
                            // JIKA BELUM ADA: Cukup pindahkan ID Pesanannya ke Pesanan Utama
                            $detailLain->update(['id_pesanan' => $pesananUtama->id]);
                        }
                    }

                    $totalTambahan += $pesananLain->total_harga;

                    // Hapus pesanan cangkang karena isinya sudah dipindah atau dihapus
                    $pesananLain->delete();
                }

                // Update total harga pesanan utama
                $pesananUtama->update([
                    'total_harga' => $pesananUtama->total_harga + $totalTambahan
                ]);
            }

            // --- MULAI PROSES PEMBAYARAN NORMAL ---
            Pembayaran::create([
                'id_pesanan' => $pesananUtama->id, // Pakai ID Pesanan Utama yang sudah digabung
                'id_kasir' => auth()->id(),
                'metode_pembayaran' => $request->metode_pembayaran,
                'total_bayar' => $pesananUtama->total_harga,
                'status_pembayaran' => 'dibayar',
                'tanggal_bayar' => now(),
            ]);

            $pesananUtama->update([
                'status_pesanan' => 'dibayar',
            ]);

            return $pesananUtama->id; // Lempar ID ini untuk dicetak ke Nota
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

        return redirect()->route('kasir.tambah.form', $meja->id);
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
        // 1. Ambil HANYA pesanan yang masih bisa dibatalkan (belum selesai/dibayar)
        $pesanansBaru = Pesanan::where('id_meja', $meja->id)
            ->whereNotIn('status_pesanan', ['selesai', 'dibayar'])
            ->get();

        // 2. Jika tidak ada pesanan baru sama sekali
        if ($pesanansBaru->isEmpty()) {
            return redirect()->route('kasir.detail', $meja->id)
                ->with('error', 'Gagal! Semua pesanan di meja ini sudah selesai dimasak.');
        }

        // 3. JIKA ADA: Hapus pesanan yang masih baru tersebut
        DB::transaction(function () use ($pesanansBaru) {
            $pesananIds = $pesanansBaru->pluck('id');

            // Hapus detail menunya
            DetailPesanan::whereIn('id_pesanan', $pesananIds)->delete();

            // Hapus induk pesanannya
            Pesanan::whereIn('id', $pesananIds)->delete();
        });

        // 4. CEK SISA PESANAN: Apakah masih ada pesanan (yang sudah selesai) di meja ini?
        $sisaPesanan = Pesanan::where('id_meja', $meja->id)->exists();

        if (!$sisaPesanan) {
            // Jika meja jadi kosong melompong, kembali ke depan (index)
            return redirect()->route('kasir.index')
                ->with('success', 'Seluruh pesanan di meja ' . $meja->nomor_meja . ' berhasil dibatalkan.');
        } else {
            // Jika masih ada sisa pesanan yang sudah dimasak, tetap di halaman detail
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
