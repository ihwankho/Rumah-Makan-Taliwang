<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Kasir - Tambah Pesanan</title>
  @vite('resources/css/app.css')
</head>

<body class="bg-gray-50 min-h-screen">

  {{-- Header --}}
  <div class="bg-white border-b shadow-sm">
    <div class="max-w-[110rem] mx-auto px-6 py-6 flex items-center justify-between">
      <div>
        <div class="text-2xl font-bold text-gray-800">
          Tambah Pesanan (Non Masak)
        </div>
        <div class="text-base text-gray-500">
          Meja {{ $meja->nomor_meja }} • {{ $pesanan->pelanggan->nama_pelanggan ?? '-' }}
        </div>
      </div>

      <form action="{{ route('kasir.batalkan.cart', $meja->id) }}" method="POST" style="display:inline;">
        @csrf
        <button type="submit" class="px-5 py-2 rounded-xl border border-gray-300 hover:bg-gray-100 text-base font-medium text-gray-700">
          Batal
        </button>
      </form>
    </div>
  </div>

  <div class="max-w-[110rem] mx-auto px-6 py-8">

    @if(session('success'))
      <div class="mb-6 bg-green-100 border border-green-200 text-green-800 px-6 py-4 rounded-xl text-base font-semibold">
        {{ session('success') }}
      </div>
    @endif

    @if(session('error'))
      <div class="mb-6 bg-red-100 border border-red-200 text-red-800 px-6 py-4 rounded-xl text-base font-semibold">
        {{ session('error') }}
      </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
      
      {{-- Left side: Menu Items --}}
      <div class="lg:col-span-2">
        <div class="text-xl font-bold text-gray-800 mb-4">Daftar Menu</div>
        
        <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
          @foreach($menusNonMasak as $m)
            <form method="POST" action="{{ route('kasir.tambah.simpan', $meja->id) }}">
              @csrf
              <input type="hidden" name="menu_id" value="{{ $m->id }}">
              <input type="hidden" name="jumlah" value="1">

              <button type="submit"
                      class="w-full bg-white rounded-2xl shadow-md border p-6 flex flex-col items-center hover:shadow-lg hover:scale-[1.02] transition duration-200">
                <div class="text-lg font-bold text-gray-800 text-center">{{ $m->nama }}</div>
                <div class="text-sm text-gray-500 mt-2">
                  Rp {{ number_format($m->harga, 0, ',', '.') }}
                </div>
                <div class="mt-5 w-full text-center bg-orange-600 text-white py-2 rounded-lg font-semibold">
                  + Tambah
                </div>
              </button>
            </form>
          @endforeach
        </div>
      </div>

      {{-- Right side: Shopping Cart --}}
      <div>
        <div class="bg-white rounded-2xl shadow-sm border p-6 sticky top-6">
          <div class="text-xl font-bold text-gray-800 mb-6">🛒 Keranjang</div>

          @if(count($cart) > 0)
            <div class="space-y-4 mb-6 max-h-96 overflow-y-auto">
              @php $cartTotal = 0 @endphp
              @foreach($cart as $item)
                @php $cartTotal += $item['subtotal'] @endphp
                <div class="border-b pb-4">
                  <div class="flex justify-between items-start mb-2">
                    <div>
                      <div class="font-semibold text-gray-800">{{ $item['nama'] }}</div>
                      <div class="text-sm text-gray-500">
                        {{ $item['jumlah'] }} x Rp {{ number_format($item['harga_satuan'], 0, ',', '.') }}
                      </div>
                    </div>
                    <form action="{{ route('kasir.hapus.cart', $meja->id) }}" method="POST" style="display:inline;">
                      @csrf
                      <input type="hidden" name="menu_id" value="{{ $item['id_menu'] }}">
                      <button type="submit" class="text-red-500 hover:text-red-700 font-bold text-lg">×</button>
                    </form>
                  </div>
                  <div class="text-right font-bold text-orange-600">
                    Rp {{ number_format($item['subtotal'], 0, ',', '.') }}
                  </div>
                </div>
              @endforeach
            </div>

            {{-- Cart Summary --}}
            <div class="border-t pt-4 mb-6">
              <div class="flex justify-between items-center mb-4">
                <span class="text-gray-700">Total Tambahan:</span>
                <span class="text-2xl font-bold text-orange-600">
                  Rp {{ number_format($cartTotal, 0, ',', '.') }}
                </span>
              </div>

              <div class="flex justify-between items-center mb-6 pb-4 border-b">
                <span class="text-gray-700">Total Pesanan Saat Ini:</span>
                <span class="text-xl font-bold text-gray-800">
                  Rp {{ number_format($pesanan->total_harga + $cartTotal, 0, ',', '.') }}
                </span>
              </div>
            </div>

            {{-- Action Buttons --}}
            <div class="space-y-3">
              <form action="{{ route('kasir.konfirmasi.cart', $meja->id) }}" method="POST">
                @csrf
                <button type="submit" class="w-full bg-green-600 text-white font-bold py-3 rounded-xl hover:bg-green-700 transition">
                  ✅ Konfirmasi & Simpan
                </button>
              </form>
            </div>

          @else
            <div class="text-center text-gray-500 py-12">
              <div class="text-5xl mb-3">🛒</div>
              <div>Keranjang kosong</div>
              <div class="text-sm mt-2">Pilih menu untuk ditambahkan</div>
            </div>
          @endif

        </div>
      </div>

    </div>

  </div>

</body>
</html>
