<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Admin - Edit Kategori</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-50 min-h-screen">

  <div class="bg-white border-b shadow-sm">
    <div class="max-w-7xl mx-auto px-6 py-6 flex items-center justify-between">
      <div>
        <div class="text-2xl font-bold">Edit Kategori</div>
        <div class="text-gray-500">Ubah kategori menu</div>
      </div>

      <a href="{{ route('admin.kategori-menu.index') }}"
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

    <form method="POST" action="{{ route('admin.kategori-menu.update', $kategori->id) }}"
          class="bg-white rounded-2xl shadow-sm border p-6 space-y-4">
      @csrf
      @method('PUT')

      <div>
        <label class="text-sm font-semibold">Nama Kategori</label>
        <input type="text" name="nama"
               value="{{ old('nama', $kategori->nama) }}"
               class="mt-1 w-full border rounded-xl px-3 py-2"
               required>
      </div>

      <button class="w-full bg-orange-600 text-white font-bold py-3 rounded-xl hover:bg-orange-700">
        Update Kategori
      </button>
    </form>

  </div>

</body>
</html>
