<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Kasir - Detail Meja {{ $meja->nomor_meja }}</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-50 min-h-screen">

  {{-- Header --}}
  <div class="bg-white border-b shadow-sm">
    <div class="max-w-7xl mx-auto px-6 py-6 flex items-center justify-between">
      <div>
        <div class="text-2xl font-bold text-gray-800">Meja {{ $meja->nomor_meja }}</div>
        <div class="text-lg text-gray-500">Detail Tagihan</div>
      </div>

      <a href="{{ route('kasir.index') }}"
         class="text-base px-5 py-2 rounded-xl border border-gray-300 hover:bg-gray-100 font-medium text-gray-700">
        Kembali
      </a>
    </div>
  </div>

  {{-- Content --}}
  <div class="max-w-7xl mx-auto px-6 py-8">

    @if(!$pesanan)
      <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8 text-center text-gray-500 text-lg">
        Tidak ada pesanan yang siap dibayar untuk meja ini.
      </div>
    @else

      <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

        {{-- Kiri: daftar pesanan --}}
        <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm border border-gray-100 p-8 flex flex-col justify-between">
          <div class="flex items-center justify-between mb-4">
            <div class="text-xl font-bold text-gray-800">Pesanan</div>
            <div class="text-base text-gray-500">
              {{ $pesanan->pelanggan->nama_pelanggan ?? '-' }}
            </div>
          </div>

          <div class="space-y-4 flex-1">
            @foreach($pesanan->detailPesanans as $detail)
              <div class="flex items-center justify-between border rounded-xl p-4">
                <div>
                  <div class="font-semibold text-gray-800">{{ $detail->menu->nama }}</div>
                  <div class="text-sm text-gray-500">
                    {{ $detail->jumlah }} x Rp {{ number_format($detail->harga_satuan, 0, ',', '.') }}
                  </div>
                </div>
                <div class="font-bold text-gray-900">
                  Rp {{ number_format($detail->subtotal, 0, ',', '.') }}
                </div>
              </div>
            @endforeach
          </div>

         {{-- Tombol Aksi (Edit & Tambah) --}}
          <div class="mt-6 flex flex-col sm:flex-row gap-4">
            {{-- Tombol Edit Pesanan --}}
            <a href=""
               class="flex-1 text-center bg-blue-600 text-white font-bold py-3 rounded-xl hover:bg-blue-700 transition shadow-md">
              Edit Pesanan
            </a>

            {{-- Tombol Tambah Pesanan --}}
            <a href="{{ route('kasir.tambah.form', $meja->id) }}"
               class="flex-1 text-center bg-orange-600 text-white font-bold py-3 rounded-xl hover:bg-orange-700 transition shadow-md">
              + Tambah Pesanan (Non Masak)
            </a>
          </div>
        </div>

        {{-- Kanan: pembayaran --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
          <div class="text-xl font-bold text-gray-800">Pembayaran</div>

          <div class="mt-6 flex items-center justify-between">
            <div class="text-base text-gray-600">Total</div>
            <div class="text-xl font-bold text-orange-600">
              Rp {{ number_format($pesanan->total_harga, 0, ',', '.') }}
            </div>
          </div>

          <form method="POST" action="{{ route('kasir.bayar', $pesanan->id) }}" class="mt-6 space-y-4">
            @csrf

            <div class="text-base font-semibold text-gray-700">Metode Pembayaran</div>

            <label class="flex items-center gap-3 border rounded-xl px-4 py-3 cursor-pointer hover:bg-gray-50">
              <input type="radio" name="metode_pembayaran" value="tunai" required>
              <span class="text-base text-gray-800">Tunai</span>
            </label>

            <label class="flex items-center gap-3 border rounded-xl px-4 py-3 cursor-pointer hover:bg-gray-50">
              <input type="radio" name="metode_pembayaran" value="qris" required>
              <span class="text-base text-gray-800">QRIS (Non Tunai)</span>
            </label>

            <button
              type="submit"
              class="w-full bg-green-600 text-white font-bold py-3 rounded-xl hover:bg-green-700 mt-4">
              Konfirmasi Dibayar
            </button>
          </form>

          <div class="text-sm text-gray-500 mt-4">
            * Jika QRIS, pelanggan scan QRIS statis di kasir.
          </div>
        </div>

      </div>

    @endif
  </div>

</body>
</html>
