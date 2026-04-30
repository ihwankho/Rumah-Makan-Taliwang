<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Admin - Edit Menu</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-50 min-h-screen">

  {{-- Header --}}
  <div class="bg-white border-b shadow-sm">
    <div class="max-w-7xl mx-auto px-6 py-6 flex items-center justify-between">
      <div>
        <div class="text-2xl font-bold">Edit Menu</div>
        <div class="text-gray-500">Ubah data menu restoran</div>
      </div>

      <a href="{{ route('admin.menu.index') }}"
         class="px-4 py-2 rounded-xl border hover:bg-gray-100">
        Kembali
      </a>
    </div>
  </div>

  <div class="max-w-3xl mx-auto px-6 py-6">

    @if($errors->any())
      <div class="bg-red-100 border border-red-200 text-red-800 px-4 py-3 rounded-xl mb-4">
        <ul class="list-disc pl-5">
          @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    <form method="POST"
          action="{{ route('admin.menu.update', $menu->id) }}"
          enctype="multipart/form-data"
          class="bg-white rounded-2xl shadow-sm border p-6 space-y-4">
      @csrf
      @method('PUT')

      <div>
        <label class="text-sm font-semibold">Kategori</label>
        <select name="kategori_menu_id" class="mt-1 w-full border rounded-xl px-3 py-2" required>
          @foreach($kategoris as $kat)
            <option value="{{ $kat->id }}" {{ $menu->kategori_menu_id == $kat->id ? 'selected' : '' }}>
              {{ $kat->nama }}
            </option>
          @endforeach
        </select>
      </div>

      <div>
        <label class="text-sm font-semibold">Nama Menu</label>
        <input type="text" name="nama"
               value="{{ old('nama', $menu->nama) }}"
               class="mt-1 w-full border rounded-xl px-3 py-2" required>
      </div>

      <div>
        <label class="text-sm font-semibold">Deskripsi</label>
        <textarea name="deskripsi"
                  class="mt-1 w-full border rounded-xl px-3 py-2"
                  rows="3">{{ old('deskripsi', $menu->deskripsi) }}</textarea>
      </div>

      <div>
        <label class="text-sm font-semibold">Harga</label>
        <input type="number" name="harga"
               value="{{ old('harga', $menu->harga) }}"
               class="mt-1 w-full border rounded-xl px-3 py-2" required>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
          <label class="text-sm font-semibold">Perlu Dimasak?</label>
          <select name="perlu_dimasak" class="mt-1 w-full border rounded-xl px-3 py-2" required>
            <option value="1" {{ $menu->perlu_dimasak ? 'selected' : '' }}>Ya</option>
            <option value="0" {{ !$menu->perlu_dimasak ? 'selected' : '' }}>Tidak</option>
          </select>
        </div>

        <div>
          <label class="text-sm font-semibold">Status Menu</label>
          <select name="is_aktif" class="mt-1 w-full border rounded-xl px-3 py-2" required>
            <option value="1" {{ $menu->is_aktif ? 'selected' : '' }}>Aktif</option>
            <option value="0" {{ !$menu->is_aktif ? 'selected' : '' }}>Nonaktif</option>
          </select>
        </div>
      </div>

      {{-- Preview gambar lama --}}
      <div>
        <label class="text-sm font-semibold">Gambar Saat Ini</label>

        @if($menu->gambar)
          <div class="mt-2">
            <img src="{{ asset('storage/'.$menu->gambar) }}"
                 class="w-full max-h-56 rounded-2xl object-cover border">
          </div>
        @else
          <div class="mt-2 w-full h-40 rounded-2xl bg-gray-100 border flex items-center justify-center text-gray-500">
            Tidak ada gambar
          </div>
        @endif
      </div>

      {{-- Upload gambar baru --}}
      <div>
        <label class="text-sm font-semibold">Ganti Gambar (Opsional)</label>
        <input type="file" name="gambar" class="mt-1 w-full border rounded-xl px-3 py-2">
        <div class="text-xs text-gray-500 mt-1">
          Jika tidak memilih file, gambar lama akan tetap digunakan.
        </div>
      </div>

      <button class="w-full bg-orange-600 text-white font-bold py-3 rounded-xl hover:bg-orange-700">
        Update Menu
      </button>
    </form>

  </div>

</body>
</html>
