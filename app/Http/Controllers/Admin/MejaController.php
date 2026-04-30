<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Meja;
use Illuminate\Http\Request;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class MejaController extends Controller
{
    public function index()
    {
        $mejas = Meja::orderByRaw('CAST(nomor_meja AS UNSIGNED) ASC')->get();
        return view('admin.meja.index', compact('mejas'));
    }

    public function store(Request $request)
    {
        $lastNumber = Meja::max('nomor_meja');

        $next = $lastNumber ? ((int)$lastNumber + 1) : 1;

        Meja::create([
            'nomor_meja' => (string)$next,
        ]);

        return redirect()->route('admin.meja.index')
            ->with('success', "Meja $next berhasil ditambahkan ✅");
    }

    public function edit(Meja $meja)
    {
        return view('admin.meja.edit', compact('meja'));
    }

    public function update(Request $request, Meja $meja)
    {
        $request->validate([
            'nomor_meja' => 'required|string|max:10|unique:mejas,nomor_meja,' . $meja->id,
        ]);

        $meja->update([
            'nomor_meja' => $request->nomor_meja,
        ]);

        return redirect()->route('admin.meja.index')
            ->with('success', 'Meja berhasil diupdate ✅');
    }

    public function destroy(Meja $meja)
    {
        $meja->delete();

        return redirect()->route('admin.meja.index')
            ->with('success', 'Meja berhasil dihapus ✅');
    }
    public function cetakQr($id)
    {
        $meja = Meja::findOrFail($id);

        $url = route('menu.index', $meja->id);

        $qrCode = QrCode::size(250)->margin(1)->generate($url);

        return view('admin.meja.cetak-qr', compact('meja', 'qrCode'));
    }
    public function cetakSemuaQr()
    {
        $mejas = Meja::orderByRaw('CAST(nomor_meja AS UNSIGNED) ASC')->get();
        $qrData = [];
        foreach ($mejas as $meja) {
            $url = route('menu.index', $meja->id);
            $qrData[] = [
                'nomor_meja' => $meja->nomor_meja,
                'qr_code' => QrCode::size(200)->margin(1)->generate($url)
            ];
        }

        return view('admin.meja.cetak-semua-qr', compact('qrData'));
    }
}
