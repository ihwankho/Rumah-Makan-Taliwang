<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Kasir - Daftar Meja</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-50 min-h-screen">
  @php
    $totalMeja = $mejas->count();
    $aktifCount = 0;

    foreach($mejas as $m) {
      if(isset($pesananTerakhir[$m->id])) {
        $aktifCount++;
      }
    }
  @endphp


 {{-- Header --}}
  <div class="bg-white border-b shadow-sm">
    <div class="max-w-7xl mx-auto px-6 py-6 flex items-center justify-between">

       <div class="flex items-center space-x-4">
  <div class="flex-shrink-0">
    <img src="{{ asset('storage/menus/logo.png') }}" alt="Logo" class="w-12 h-12 object-cover rounded-full">
  </div>
  <div class="flex flex-col justify-center">
    <h1 class="text-2xl font-bold text-gray-800 leading-tight">Kasir</h1>
    <p class="text-sm text-gray-500">Manajemen Pembayaran</p>
  </div>
</div>


      {{-- Ringkasan --}}
      <div class="flex items-center gap-3">
        <div class="max-w-7xl mx-auto px-6 py-6 flex items-center justify-between">
  <a href="#"
     class="px-4 py-2 rounded-lg border border-gray-200 bg-white text-gray-800 font-semibold hover:bg-gray-50 shadow-sm">
      Buat Pesanan
  </a>
  <div>
  <div class="ml-5">
        <button
          type="button"
          class="px-4 py-2 rounded-lg border border-gray-200 bg-white text-gray-800 font-semibold hover:bg-gray-50 shadow-sm">
          Kelola Menu
        </button>
      </div>
      </div>
</div>
        <div class="bg-green-100 border border-green-200 text-green-800 px-4 py-2 rounded-xl">
          <div class="text-xs font-semibold">Meja Aktif</div>
          <div class="text-lg font-bold">{{ $aktifCount }}</div>
        </div>

        <div class="bg-gray-100 border border-gray-200 text-gray-800 px-4 py-2 rounded-xl">
          <div class="text-xs font-semibold">Total Meja</div>
          <div class="text-lg font-bold">{{ $totalMeja }}</div>
        </div>
      </div>
    </div>
  </div>

  {{-- Notif --}}
  @if(session('success'))
    <div class="max-w-7xl mx-auto px-6 pt-6">
      <div class="bg-green-200 border border-green-300 text-green-900 px-6 py-4 rounded-xl text-lg font-semibold">
        {{ session('success') }}
      </div>
    </div>
  @endif

  {{-- Content --}}
  <div class="max-w-7xl mx-auto px-6 py-8">


    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8">

      @foreach($mejas as $meja)
        @php
          $pesanan = $pesananTerakhir[$meja->id] ?? null;
          $aktif = $pesanan ? true : false;
        @endphp

        <a href="{{ route('kasir.detail', $meja->id) }}"
           class="bg-white rounded-2xl shadow-md border border-gray-100 p-8 hover:shadow-lg hover:scale-[1.02] transition duration-200 h-[280px] flex flex-col justify-between">

          {{-- Header Meja --}}
          <div>
            <div class="flex items-center justify-between mb-4">
              <div class="text-3xl font-bold text-gray-800"> Meja {{ $meja->nomor_meja }}</div>

              @if($aktif)
                <span class="px-4 py-2 rounded-full text-sm font-bold bg-green-600 text-white shadow-md">
                  🟢 Aktif
                </span>
              @else
                <span class="px-4 py-2 rounded-full text-sm font-bold bg-gray-300 text-gray-700">
                  ⚪ Nonaktif
                </span>
              @endif
            </div>

            <div class="border-t border-gray-200 mb-4"></div>

            {{-- Isi Pesanan --}}
            @if($aktif)
              <div class="space-y-2">
                <div class="text-base text-gray-600">🧍 Pelanggan</div>
                <div class="text-xl font-semibold text-gray-900">
                  {{ $pesanan->pelanggan->nama_pelanggan ?? '-' }}
                </div>

                <div class="text-base text-gray-700 mt-1">
                  {{ $pesanan->tipe_pesanan === 'bungkus' ? '📦 Dibungkus' : '🍽️ Makan di Tempat' }}
                  • {{ $pesanan->created_at->format('H:i') }}
                </div>
              </div>
            @else
              <div class="text-lg text-gray-600">
                Belum ada pesanan
              </div>
            @endif
          </div>

          {{-- Footer --}}
          <div class="text-sm text-gray-500 mt-4">
            Klik untuk melihat detail tagihan
          </div>

        </a>
      @endforeach

    </div>

  </div>

</body>
</html>
