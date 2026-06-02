<?php

namespace App\Http\Controllers;

use App\Models\Pesanan;
use App\Models\Menu;
use Illuminate\Http\Request;

class DapurController extends Controller
{
    public function index()
    {
        $pesanans = Pesanan::with([
            'pelanggan',
            'meja',
            'detailPesanans' => function ($query) {
                $query->whereHas('menu', function ($q) {
                    $q->where('perlu_dimasak', true);
                });
            },
            'detailPesanans.menu'
        ])
            ->whereIn('status_pesanan', ['menunggu', 'diproses'])
            ->whereHas('detailPesanans.menu', function ($q) {
                $q->where('perlu_dimasak', true);
            })
            ->orderBy('created_at', 'asc')
            ->get();
        $menusDropdown = Menu::where('perlu_dimasak', true)
            ->where('is_aktif', true)
            ->orderBy('nama')
            ->get();
        $menus = Menu::orderBy('nama')->get();

        return view('dapur.index', compact('pesanans', 'menus', 'menusDropdown'));
    }

    public function selesai(Pesanan $pesanan)
    {
        $pesanan->update([
            'status_pesanan' => 'selesai'
        ]);

        return back()->with('success', 'Pesanan berhasil ditandai selesai ✅');
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

    public function deleteDetailPesanan($pesananId, $detailId)
    {
        $pesanan = Pesanan::find($pesananId);

        if (!$pesanan) {
            return back()->with('error', 'Pesanan tidak ditemukan.');
        }

        $detail = $pesanan->detailPesanans()->find($detailId);

        if (!$detail) {
            return back()->with('error', 'Item pesanan tidak ditemukan.');
        }

        $pesanan->update([
            'total_harga' => $pesanan->total_harga - $detail->subtotal
        ]);

        $detail->delete();

        return back()->with('success', 'Item berhasil dihapus dari pesanan.');
    }

    public function replaceDetailPesanan(Request $request, $pesananId, $detailId)
    {
        $request->validate([
            'menu_id' => 'required|exists:menus,id',
        ]);

        $pesanan = Pesanan::find($pesananId);

        if (!$pesanan) {
            return back()->with('error', 'Pesanan tidak ditemukan.');
        }

        $detail = $pesanan->detailPesanans()->find($detailId);

        if (!$detail) {
            return back()->with('error', 'Item pesanan tidak ditemukan.');
        }

        $newMenu = Menu::find($request->menu_id);

        if (!$newMenu) {
            return back()->with('error', 'Menu tidak ditemukan.');
        }

        $jumlahTetap = $detail->jumlah;
        $newSubtotal = $newMenu->harga * $jumlahTetap;
        $selisih = $newSubtotal - $detail->subtotal;

        $detail->update([
            'id_menu' => $newMenu->id,
            'harga_satuan' => $newMenu->harga,
            'subtotal' => $newSubtotal,
        ]);

        $pesanan->update([
            'total_harga' => $pesanan->total_harga + $selisih
        ]);

        return back()->with('success', 'Item pesanan berhasil diganti dengan ' . $newMenu->nama);
    }
}
