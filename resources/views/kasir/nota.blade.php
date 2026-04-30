<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Nota - Meja {{ $pesanan->meja->nomor_meja ?? '-' }}</title>
  <script src="https://cdn.tailwindcss.com"></script>

  <style>
    @media print {
      .no-print { display: none !important; }
      body { background: white !important; }
    }
  </style>
</head>

<body class="bg-gray-50 min-h-screen">

  {{-- Header --}}
  <div class="bg-white border-b shadow-sm no-print">
    <div class="max-w-3xl mx-auto px-6 py-6 flex items-center justify-between">
      <div>
        <div class="text-2xl font-bold text-gray-800"> Nota Pembayaran</div>
        <div class="text-base text-gray-500"> Meja {{ $pesanan->meja->nomor_meja ?? '-' }}</div>
      </div>

      <div class="flex gap-3">
        <a href="{{ route('kasir.index') }}"
           class="px-5 py-2 rounded-xl border border-gray-300 hover:bg-gray-100 text-base font-medium text-gray-700">
          Kembali
        </a>

        <button onclick="window.print()"
                class="px-5 py-2 rounded-xl bg-orange-600 text-white text-base font-semibold hover:bg-orange-700">
          Cetak Nota
        </button>
      </div>
    </div>
  </div>

  {{-- Nota --}}
  <div class="max-w-3xl mx-auto px-6 py-8">

    @if(session('success'))
      <div class="bg-green-100 border border-green-200 text-green-800 px-6 py-4 rounded-xl mb-6 text-base font-semibold no-print">
        {{ session('success') }}
      </div>
    @endif

    <div class="bg-white rounded-2xl shadow-md border border-gray-100 p-8">

      {{-- Header Nota --}}
      <div class="text-center mb-8">
        <div class="text-2xl font-bold text-gray-800">Rumah Makan Muslim Taliwang</div>
        <div class="text-base text-gray-500">Bukti Pembayaran</div>
        <div class="mt-2 text-green-600 font-semibold text-sm">✅ Pembayaran berhasil</div>
      </div>

      {{-- Info Umum --}}
      <div class="text-base space-y-2 mb-6">
        <div class="flex justify-between">
          <span class="text-gray-600">Tanggal</span>
          <span class="font-semibold">{{ now()->format('d-m-Y H:i') }}</span>
        </div>
        <div class="flex justify-between">
          <span class="text-gray-600">Meja</span>
          <span class="font-semibold">{{ $pesanan->meja->nomor_meja ?? '-' }}</span>
        </div>
        <div class="flex justify-between">
          <span class="text-gray-600">Pelanggan</span>
          <span class="font-semibold">{{ $pesanan->pelanggan->nama_pelanggan ?? '-' }}</span>
        </div>
        <div class="flex justify-between">
          <span class="text-gray-600">Tipe</span>
          <span class="font-semibold">
            {{ $pesanan->tipe_pesanan === 'bungkus' ? 'Dibungkus' : 'Makan di Tempat' }}
          </span>
        </div>
        <div class="flex justify-between">
          <span class="text-gray-600">Metode Bayar</span>
          <span class="font-semibold uppercase">
            {{ $pesanan->pembayaran->metode_pembayaran ?? '-' }}
          </span>
        </div>
      </div>

      <div class="border-t border-gray-300 my-6"></div>

      {{-- List Item --}}
      <div class="space-y-4 text-base">
        @foreach($pesanan->detailPesanans as $detail)
          <div class="flex justify-between">
            <div>
              <div class="font-semibold text-gray-800">{{ $detail->menu->nama }}</div>
              <div class="text-sm text-gray-500">
                {{ $detail->jumlah }} x Rp {{ number_format($detail->harga_satuan, 0, ',', '.') }}
              </div>
            </div>
            <div class="font-semibold text-gray-800">
              Rp {{ number_format($detail->subtotal, 0, ',', '.') }}
            </div>
          </div>
        @endforeach
      </div>

      <div class="border-t border-gray-300 my-6"></div>

      {{-- Total --}}
      <div class="flex justify-between text-xl font-bold">
        <span>TOTAL</span>
        <span class="text-orange-600">
          Rp {{ number_format($pesanan->total_harga, 0, ',', '.') }}
        </span>
      </div>

      <div class="text-center text-sm text-gray-500 mt-8">
        Terima kasih 🙏
      </div>

    </div>
  </div>

</body>
</html>
