<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\KategoriMenu;
use App\Models\Menu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MenuController extends Controller
{
    public function index(Request $request)
    {
        $kategoris = KategoriMenu::orderBy('nama')->get();
        $selectedKategoriId = $request->get('kategori_id');
        $search = $request->get('search');

        $query = Menu::with('kategori');

        if ($selectedKategoriId) {
            $query->where('kategori_menu_id', $selectedKategoriId);
        }

        if ($search) {
            $query->where('nama', 'like', '%' . $search . '%');
        }

        $menus = $query->orderBy('kategori_menu_id')
            ->orderBy('nama')
            ->get();

        return view('admin.menu.index', compact('menus', 'kategoris', 'selectedKategoriId', 'search'));
    }

    public function create()
    {
        $kategoris = KategoriMenu::orderBy('nama')->get();
        return view('admin.menu.create', compact('kategoris'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'kategori_menu_id' => 'required|exists:kategori_menus,id',
            'nama' => 'required|string|max:150',
            'deskripsi' => 'nullable|string',
            'harga' => 'required|integer|min:0',
            'perlu_dimasak' => 'required|in:0,1',
            'is_aktif' => 'required|in:0,1',
            'gambar' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $pathGambar = null;

        if ($request->hasFile('gambar')) {
            $pathGambar = $request->file('gambar')->store('menus', 'public');
        }

        Menu::create([
            'kategori_menu_id' => $request->kategori_menu_id,
            'nama' => $request->nama,
            'deskripsi' => $request->deskripsi,
            'harga' => $request->harga,
            'gambar' => $pathGambar,
            'perlu_dimasak' => (bool) $request->perlu_dimasak,
            'is_aktif' => (bool) $request->is_aktif,
        ]);

        return redirect()->route('admin.menu.index')
            ->with('success', 'Menu berhasil ditambahkan ✅');
    }

    public function edit(Menu $menu)
    {
        $kategoris = KategoriMenu::orderBy('nama')->get();
        return view('admin.menu.edit', compact('menu', 'kategoris'));
    }

    public function update(Request $request, Menu $menu)
    {
        $request->validate([
            'kategori_menu_id' => 'required|exists:kategori_menus,id',
            'nama' => 'required|string|max:150',
            'deskripsi' => 'nullable|string',
            'harga' => 'required|integer|min:0',
            'perlu_dimasak' => 'required|in:0,1',
            'is_aktif' => 'required|in:0,1',
            'gambar' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $pathGambar = $menu->gambar;

        // kalau admin upload gambar baru
        if ($request->hasFile('gambar')) {
            // hapus gambar lama kalau ada
            if ($menu->gambar && Storage::disk('public')->exists($menu->gambar)) {
                Storage::disk('public')->delete($menu->gambar);
            }

            $pathGambar = $request->file('gambar')->store('menus', 'public');
        }

        $menu->update([
            'kategori_menu_id' => $request->kategori_menu_id,
            'nama' => $request->nama,
            'deskripsi' => $request->deskripsi,
            'harga' => $request->harga,
            'gambar' => $pathGambar,
            'perlu_dimasak' => (bool) $request->perlu_dimasak,
            'is_aktif' => (bool) $request->is_aktif,
        ]);

        return redirect()->route('admin.menu.index')
            ->with('success', 'Menu berhasil diupdate ✅');
    }

    public function destroy(Menu $menu)
    {
        // hapus gambar kalau ada
        if ($menu->gambar && Storage::disk('public')->exists($menu->gambar)) {
            Storage::disk('public')->delete($menu->gambar);
        }

        $menu->delete();

        return redirect()->route('admin.menu.index')
            ->with('success', 'Menu berhasil dihapus ✅');
    }
}
