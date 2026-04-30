<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Admin - Tambah Menu</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-50 min-h-screen">

  <div class="bg-white border-b shadow-sm">
    <div class="max-w-7xl mx-auto px-6 py-6 flex items-center justify-between">
      <div>
        <div class="text-2xl font-bold">Tambah Menu</div>
        <div class="text-gray-500">Tambah menu baru</div>
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

    <form method="POST" action="{{ route('admin.menu.store') }}" enctype="multipart/form-data"
          class="bg-white rounded-2xl shadow-sm border p-6 space-y-4">
      @csrf

      <div>
        <label class="text-sm font-semibold">Kategori</label>
        <select name="kategori_menu_id" class="mt-1 w-full border rounded-xl px-3 py-2" required>
          @foreach($kategoris as $kat)
            <option value="{{ $kat->id }}">{{ $kat->nama }}</option>
          @endforeach
        </select>
      </div>

      <div>
        <label class="text-sm font-semibold">Nama Menu</label>
        <input type="text" name="nama" class="mt-1 w-full border rounded-xl px-3 py-2" required>
      </div>

      <div>
        <label class="text-sm font-semibold">Deskripsi</label>
        <textarea name="deskripsi" class="mt-1 w-full border rounded-xl px-3 py-2" rows="3"></textarea>
      </div>

      <div>
        <label class="text-sm font-semibold">Harga</label>
        <input type="number" name="harga" class="mt-1 w-full border rounded-xl px-3 py-2" required>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
          <label class="text-sm font-semibold">Perlu Dimasak?</label>
          <select name="perlu_dimasak" class="mt-1 w-full border rounded-xl px-3 py-2" required>
            <option value="1">Ya</option>
            <option value="0">Tidak</option>
          </select>
        </div>

        <div>
          <label class="text-sm font-semibold">Status Menu</label>
          <select name="is_aktif" class="mt-1 w-full border rounded-xl px-3 py-2" required>
            <option value="1">Aktif</option>
            <option value="0">Nonaktif</option>
          </select>
        </div>
      </div>

      <div>
        <label class="text-sm font-semibold">Gambar (Opsional)</label>
        <input type="file" name="gambar" class="mt-1 w-full border rounded-xl px-3 py-2">
      </div>

      <button class="w-full bg-orange-600 text-white font-bold py-3 rounded-xl hover:bg-orange-700">
        Simpan Menu
      </button>
    </form>

  </div>

</body>
</html>
