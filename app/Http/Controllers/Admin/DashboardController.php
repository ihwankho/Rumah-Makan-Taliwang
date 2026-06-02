<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Pesanan;
use App\Models\Meja;
use App\Models\Menu;
use App\Models\DetailPesanan;
use App\Models\Pembayaran;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // Data untuk hari ini
        $today = Carbon::today();

        // 1. Pesanan baru hari ini
        $pesananBaru = Pesanan::whereDate('created_at', $today)->count();

        // 2. Meja terisi (meja yang ada pesanan aktif)
        $totalMeja = Meja::count();
        $mejaTerisi = Pesanan::where('status_pesanan', '!=', 'dibayar')
            ->whereDate('created_at', $today)
            ->distinct('id_meja')
            ->count('id_meja');

        // 3. Pendapatan hari ini
        $pendapatanHariIni = Pembayaran::whereDate('tanggal_bayar', $today)
            ->where('status_pembayaran', 'dibayar')
            ->sum('total_bayar');

        // 4. Menu yang perlu perhatian (stock kosong atau tidak aktif)
        $menuPerluPerhatian = Menu::where('is_aktif', false)->count();

        // 5. Top menu hari ini (berdasarkan jumlah terjual)
        $topMenus = DetailPesanan::with('menu')
            ->join('pesanans', 'detail_pesanans.id_pesanan', '=', 'pesanans.id')
            ->whereMonth('pesanans.created_at', now()->month)
            ->whereYear('pesanans.created_at', now()->year)
            ->select('id_menu', DB::raw('SUM(jumlah) as total_terjual'))
            ->groupBy('id_menu')
            ->orderBy('total_terjual', 'desc')
            ->limit(10)
            ->get();

        // 6. Tren pengunjung (14 hari terakhir)
        $trenPengunjung = [];
        for ($i = 13; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $count = Pesanan::whereDate('created_at', $date)->count();
            $trenPengunjung[] = [
                'tanggal' => $date->format('d/m'),
                'jumlah' => $count
            ];
        }

        // 7. Pesanan masuk hari ini
        $showAll = $request->boolean('all');
        $pesananQuery = Pesanan::with(['pelanggan', 'meja'])
            ->whereDate('created_at', $today)
            ->orderBy('created_at', 'desc');

        if (!$showAll) {
            $pesananQuery->limit(10);
        }

        $pesananMasuk = $pesananQuery->get();

        return view('admin.dashboard.index', compact(
            'pesananBaru',
            'mejaTerisi',
            'totalMeja',
            'pendapatanHariIni',
            'menuPerluPerhatian',
            'topMenus',
            'trenPengunjung',
            'pesananMasuk',
            'showAll'
        ));
    }
}
