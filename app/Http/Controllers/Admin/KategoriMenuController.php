<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\KategoriMenu;
use Illuminate\Http\Request;

class KategoriMenuController extends Controller
{
    public function index()
    {
        $kategoris = KategoriMenu::orderBy('nama')->get();
        return view('admin.kategori_menu.index', compact('kategoris'));
    }

    public function create()
    {
        return view('admin.kategori_menu.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:100|unique:kategori_menus,nama',
        ]);

        KategoriMenu::create([
            'nama' => $request->nama,
        ]);

        return redirect()->route('admin.kategori-menu.index')
            ->with('success', 'Kategori berhasil ditambahkan ✅');
    }

    public function edit(KategoriMenu $kategori_menu)
    {
        return view('admin.kategori_menu.edit', [
            'kategori' => $kategori_menu
        ]);
    }

    public function update(Request $request, KategoriMenu $kategori_menu)
    {
        $request->validate([
            'nama' => 'required|string|max:100|unique:kategori_menus,nama,' . $kategori_menu->id,
        ]);

        $kategori_menu->update([
            'nama' => $request->nama,
        ]);

        return redirect()->route('admin.kategori-menu.index')
            ->with('success', 'Kategori berhasil diupdate ✅');
    }

    public function destroy(KategoriMenu $kategori_menu)
    {
        // 
        if ($kategori_menu->menus()->count() > 0) {
            return redirect()->route('admin.kategori-menu.index')
                ->with('error', 'Kategori tidak bisa dihapus karena masih dipakai menu ❌');
        }

        $kategori_menu->delete();

        return redirect()->route('admin.kategori-menu.index')
            ->with('success', 'Kategori berhasil dihapus ✅');
    }
}
