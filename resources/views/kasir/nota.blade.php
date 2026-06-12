<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Nota - Meja {{ $pesanan->meja->nomor_meja ?? '-' }}</title>
  @vite('resources/css/app.css')
  <style>
    /* 1. ATURAN KHUSUS UNTUK PRINTER THERMAL */
    @media print {
      /* Sembunyikan elemen layar (tombol, header layar) */
      .no-print { display: none !important; }
      
      /* Atur ukuran kertas ke thermal 58mm dengan tinggi otomatis */
      @page {
        size: 58mm auto;
        margin: 0;
      }

      body {
        background: white !important;
        margin: 0;
        padding: 0;
        font-family: monospace, sans-serif !important; /* Font struk biasanya monospace */
      }

      /* 2. Format ulang kotak struk agar pas 58mm */
      #area-nota {
        width: 58mm !important;
        max-width: 58mm !important;
        margin: 0 auto !important;
        padding: 4mm !important; /* Padding sangat kecil untuk thermal */
        box-shadow: none !important; /* Hilangkan bayangan */
        border: none !important; /* Hilangkan garis pinggir */
      }

      /* 3. Kecilkan semua teks agar tidak terpotong ke samping */
      * {
        color: black !important; /* Thermal hanya print warna hitam */
      }
      .text-2xl { font-size: 16px !important; line-height: 1.2 !important; }
      .text-xl { font-size: 14px !important; }
      .text-base { font-size: 11px !important; line-height: 1.4 !important; }
      .text-sm { font-size: 10px !important; }
      
      /* Hilangkan jarak berlebih */
      .mb-8 { margin-bottom: 10px !important; }
      .mb-6 { margin-bottom: 8px !important; }
      .my-6 { margin-top: 8px !important; margin-bottom: 8px !important; }
      .p-8 { padding: 0 !important; }
    }
  </style>
</head>

<body class="bg-gray-50 min-h-screen flex flex-col items-center">

  {{-- Header Layar (Akan hilang saat diprint) --}}
  <div class="w-full bg-white border-b shadow-sm no-print mb-8">
    <div class="max-w-3xl mx-auto px-6 py-6 flex items-center justify-between">
      <div>
        <div class="text-2xl font-bold text-gray-800">Preview Nota</div>
        <div class="text-base text-gray-500">Meja {{ $pesanan->meja->nomor_meja ?? '-' }}</div>
      </div>

      <div class="flex gap-3">
        <a href="{{ route('kasir.index') }}"
           class="px-5 py-2 rounded-xl border border-gray-300 hover:bg-gray-100 text-base font-medium text-gray-700">
          Kembali
        </a>

        <button onclick="window.print()"
                class="px-5 py-2 rounded-xl bg-orange-600 text-white text-base font-semibold hover:bg-orange-700 shadow-md">
          🖨️ Cetak Nota
        </button>
      </div>
    </div>
  </div>

  {{-- 
      Area Nota 
      Di layar laptop tampil seperti kotak (max-w-sm).
      Saat di-print, dibajak oleh id="area-nota" menjadi selebar 58mm.
  --}}
  <div id="area-nota" class="bg-white rounded-2xl shadow-xl border border-gray-100 p-8 w-full max-w-sm">

    {{-- Header Nota --}}
    <div class="text-center mb-6">
      <div class="text-xl font-bold text-gray-800 uppercase">Muslim Taliwang</div>
      <div class="text-sm text-gray-500 uppercase mt-1">Bukti Pembayaran</div>
    </div>

    {{-- Info Umum --}}
    <div class="text-base space-y-1 mb-4">
      <div class="flex justify-between">
        <span class="text-gray-500">Tanggal</span>
        <span class="font-bold">{{ now()->format('d-m-Y H:i') }}</span>
      </div>
      <div class="flex justify-between">
        <span class="text-gray-500">Meja</span>
        <span class="font-bold">{{ $pesanan->meja->nomor_meja ?? '-' }}</span>
      </div>
      <div class="flex justify-between">
        <span class="text-gray-500">Pelanggan</span>
        <span class="font-bold truncate max-w-[120px] text-right">{{ $pesanan->pelanggan->nama_pelanggan ?? '-' }}</span>
      </div>
      <div class="flex justify-between">
        <span class="text-gray-500">Tipe</span>
        <span class="font-bold">{{ $pesanan->tipe_pesanan === 'bungkus' ? 'Bungkus' : 'makan di tempat' }}</span>
      </div>
    </div>

    <div class="border-t-2 border-dashed border-gray-300 my-4"></div>

    {{-- List Item --}}
    <div class="space-y-3 text-base">
      @foreach($pesanan->detailPesanans as $detail)
        <div class="flex justify-between">
          <div class="pr-2">
            <div class="font-bold text-gray-800 leading-tight">{{ $detail->menu->nama }}</div>
            <div class="text-sm text-gray-600">
              {{ $detail->jumlah }} x {{ number_format($detail->harga_satuan, 0, ',', '.') }}
            </div>
          </div>
          <div class="font-bold text-gray-800 text-right shrink-0">
            {{ number_format($detail->subtotal, 0, ',', '.') }}
          </div>
        </div>
      @endforeach
    </div>

    <div class="border-t-2 border-dashed border-gray-300 my-4"></div>

    {{-- Total --}}
    <div class="flex justify-between text-xl font-black">
      <span>TOTAL</span>
      <span>{{ number_format($pesanan->total_harga, 0, ',', '.') }}</span>
    </div>
    
    <div class="flex justify-between text-base font-bold text-gray-600 mt-2">
      <span>Bayar ({{ strtoupper($pesanan->pembayaran->metode_pembayaran ?? '-') }})</span>
    </div>

    <div class="text-center text-sm font-bold text-gray-600 mt-8 pt-4 border-t border-gray-200">
      ~ Terima Kasih ~
    </div>

  </div>

</body>
</html>