<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pembayaran;
use Illuminate\Http\Request;

class LaporanController extends Controller
{
    public function index(Request $request)
    {

        $tanggalMulai = $request->get('mulai', now()->startOfMonth()->toDateString());

        $tanggalSelesai = $request->get('selesai', now()->endOfMonth()->toDateString());

        $pembayarans = Pembayaran::with(['pesanan.meja', 'pesanan.pelanggan'])
            ->whereDate('tanggal_bayar', '>=', $tanggalMulai)
            ->whereDate('tanggal_bayar', '<=', $tanggalSelesai)
            ->orderBy('tanggal_bayar', 'desc')
            ->get();

        $totalTunai = $pembayarans->where('metode_pembayaran', 'tunai')->sum('total_bayar');
        $totalNonTunai = $pembayarans->where('metode_pembayaran', 'qris')->sum('total_bayar');

        $totalTransaksi = $pembayarans->count();
        $totalPendapatan = $pembayarans->sum('total_bayar');

        return view('admin.laporan.index', compact(
            'pembayarans',
            'tanggalMulai',
            'tanggalSelesai',
            'totalTransaksi',
            'totalPendapatan',
            'totalTunai',
            'totalNonTunai'
        ));
    }
}
