<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Kasir - Detail Meja {{ $meja->nomor_meja }}</title>
  @vite('resources/css/app.css')
</head>

<body class="bg-gray-50 min-h-screen">
{{-- ========================================== --}}
  {{-- NOTIFIKASI ERROR / SUCCESS (FLOATING)      --}}
  {{-- ========================================== --}}
  @if(session('error'))
    <div id="toast-error" class="fixed top-6 left-1/2 transform -translate-x-1/2 z-[100] transition-all duration-500 ease-in-out">
      <div class="bg-red-50 border border-red-400 text-red-800 px-6 py-3 rounded-2xl shadow-xl text-lg font-bold flex items-center gap-4">
        <span>❌ {{ session('error') }}</span>
        <button onclick="document.getElementById('toast-error').remove()" class="text-red-600 hover:text-red-900 text-2xl leading-none focus:outline-none">
          &times;
        </button>
      </div>
    </div>
    <script>
      setTimeout(() => {
        let toast = document.getElementById('toast-error');
        if (toast) {
          toast.classList.add('opacity-0', '-translate-y-5');
          setTimeout(() => toast.remove(), 500);
        }
      }, 4000);
    </script>
  @endif

  @if(session('success'))
    <div id="toast-success" class="fixed top-6 left-1/2 transform -translate-x-1/2 z-[100] transition-all duration-500 ease-in-out">
      <div class="bg-green-100 border border-green-400 text-green-800 px-6 py-3 rounded-2xl shadow-xl text-lg font-bold flex items-center gap-4">
        <span>✅ {{ session('success') }}</span>
        <button onclick="document.getElementById('toast-success').remove()" class="text-green-600 hover:text-green-900 text-2xl leading-none focus:outline-none">
          &times;
        </button>
      </div>
    </div>
    <script>
      setTimeout(() => {
        let toast = document.getElementById('toast-success');
        if (toast) {
          toast.classList.add('opacity-0', '-translate-y-5');
          setTimeout(() => toast.remove(), 500);
        }
      }, 4000);
    </script>
  @endif
  {{-- ========================================== --}}
  {{-- Header --}}
  <div class="bg-white border-b shadow-sm">
    <div class="max-w-[110rem] mx-auto px-6 py-6 flex items-center justify-between">
      <div>
        <div class="text-2xl font-bold text-gray-800">Meja {{ $meja->nomor_meja }}</div>
        <div class="text-lg text-gray-500">Detail Tagihan</div>
      </div>
<div class="flex items-center gap-3">
      <form id="formHapusPesanan" method="POST" action="{{ route('kasir.meja.batalkan-baru', $meja->id) }}">
        @csrf
        @method('DELETE')
        <button type="button" onclick="bukaModalHapus()"
           class="text-base px-5 py-2 rounded-xl border border-red-300 bg-red-50 hover:bg-red-100 font-medium text-red-700 transition-colors">
          Hapus Pesanan
        </button>
      </form>
      <a href="{{ route('kasir.index') }}"
         class="text-base px-5 py-2 rounded-xl border border-gray-300 hover:bg-gray-100 font-medium text-gray-700 transition-colors">
        Kembali
      </a>
      </div>
    </div>
  </div>

  {{-- Content --}}
  <div class="max-w-[110rem] mx-auto px-6 py-8">

    {{-- Gunakan $daftarPesanan->isEmpty() karena sekarang formatnya Array/Collection --}}
    @if($daftarPesanan->isEmpty())
      <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-12 text-center">
        <div class="text-5xl mb-4">🍽️</div>
        <div class="text-gray-500 text-xl font-medium">Tidak ada pesanan yang aktif untuk meja ini.</div>
      </div>
    @else

      <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

        {{-- Kiri: Daftar Pesanan --}}
        <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm border border-gray-100 p-8 flex flex-col">
          <div class="flex items-center justify-between mb-8">
            <div class="text-xl font-bold text-gray-800">Rincian Item</div>
            <div class="px-4 py-1.5 bg-blue-50 text-blue-700 rounded-lg text-sm font-semibold">
              {{-- Ambil nama pelanggan dari pesanan utama --}}
              Pelanggan: {{ $pesananUtama->pelanggan->nama_pelanggan ?? '-' }}
            </div>
          </div>

          <div class="space-y-6 flex-1">
            {{-- LOOPING PERTAMA: Berdasarkan ID Pesanan (Bisa lebih dari 1 jika ada tambahan bungkus) --}}
            @foreach($daftarPesanan as $index => $trx)
              
              {{-- Header pemisah jika ada lebih dari 1 pesanan --}}
              @if($daftarPesanan->count() > 1)
                <div class="text-sm font-bold text-gray-500 uppercase tracking-widest border-b pb-2 mt-4">
                  Bagian #{{ $index + 1 }} - {{ $trx->tipe_pesanan === 'bungkus' ? 'Bungkus Bawa Pulang' : 'Makan di Tempat' }}
                </div>
              @endif

              <div class="space-y-4 mt-4">
                {{-- LOOPING KEDUA: Item Makanan di dalam pesanan tersebut --}}
                @foreach($trx->detailPesanans as $detail)
                  <div class="flex items-center justify-between border border-gray-100 rounded-2xl p-5 hover:bg-gray-50 transition-all">
                    <div class="flex items-center gap-5">
                      
                      {{-- Logika Tombol Hapus --}}
                      @if(!$detail->menu->perlu_dimasak)
                        {{-- Gunakan $trx->id, bukan $pesanan->id --}}
                        <form method="POST" action="{{ route('kasir.item.destroy', [$trx->id, $detail->id]) }}">
                          @csrf
                          @method('DELETE')
                          <button type="submit" class="p-2.5 text-red-500 hover:bg-red-50 rounded-xl transition-colors group" title="Hapus Item">
                            <svg class="w-6 h-6 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                          </button>
                        </form>
                      @else
                        {{-- Tombol Terkunci (Abu-abu) --}}
                        <div class="p-2.5 text-gray-200 cursor-not-allowed" title="Item masakan hanya bisa dibatalkan melalui Dapur">
                          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                          </svg>
                        </div>
                      @endif

                      <div>
                        <div class="font-bold text-gray-800 text-lg leading-tight">{{ $detail->menu->nama }}</div>
                        <div class="text-sm text-orange-600 mt-1 flex items-center gap-2">
                          {{ $detail->jumlah }} x Rp {{ number_format($detail->harga_satuan, 0, ',', '.') }}
                          @if($detail->menu->perlu_dimasak)
                            <span class="text-[10px] bg-gray-100 text-gray-400 px-2 py-0.5 rounded-md font-bold uppercase tracking-tighter border">Masak</span>
                          @endif
                        </div>
                      </div>
                    </div>
                    
                    <div class="text-right">
                      <div class="font-bold text-gray-900 text-lg">
                        Rp {{ number_format($detail->subtotal, 0, ',', '.') }}
                      </div>
                    </div>
                  </div>
                @endforeach
              </div>
            @endforeach
          </div>

          {{-- Tombol Tambah Pesanan --}}
          <div class="mt-8">
            <a href="{{ route('kasir.tambah.form', $meja->id) }}"
               class="flex items-center justify-center gap-2 w-full bg-orange-50 text-orange-700 border-2 border-dashed border-orange-200 font-bold py-4 rounded-2xl hover:bg-orange-100 hover:border-orange-300 transition-all group">
              <span class="text-xl group-hover:scale-125 transition-transform">+</span>
              Tambah Pesanan (Non Masak)
            </a>
          </div>
        </div>

        {{-- Kanan: Pembayaran --}}
        <div class="lg:sticky lg:top-8 h-fit space-y-6">
          <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
            <div class="text-xl font-bold text-gray-800 mb-6">Ringkasan Tagihan</div>

            <div class="space-y-3 border-b pb-6">
              <div class="flex justify-between text-gray-600">
                <span>Subtotal</span>
                {{-- Gunakan variabel $totalGabungan --}}
                <span>Rp {{ number_format($totalGabungan, 0, ',', '.') }}</span>
              </div>
            </div>

            <div class="flex items-center justify-between py-6">
              <div class="text-lg font-bold text-gray-800">Total Akhir</div>
              <div class="text-2xl font-black text-orange-600 font-mono">
                {{-- Gunakan variabel $totalGabungan --}}
                Rp {{ number_format($totalGabungan, 0, ',', '.') }}
              </div>
            </div>

            {{-- Form Pembayaran mengarah ke $meja->id --}}
            <form method="POST" action="{{ route('kasir.bayar', $meja->id) }}" class="space-y-4">
              @csrf

              <div class="text-sm font-bold text-gray-500 uppercase tracking-widest mb-2">Metode Bayar</div>

              <div class="grid grid-cols-1 gap-3">
                <label class="flex items-center gap-4 border-2 rounded-2xl px-5 py-4 cursor-pointer hover:border-green-500 hover:bg-green-50 transition-all has-[:checked]:border-green-600 has-[:checked]:bg-green-50 group">
                  <input type="radio" name="metode_pembayaran" value="tunai" class="w-5 h-5 text-green-600 focus:ring-green-500" required>
                  <div>
                    <div class="font-bold text-gray-800 group-has-[:checked]:text-green-700">Tunai / Cash</div>
                    <div class="text-xs text-gray-500">Bayar langsung di kasir</div>
                  </div>
                </label>

                <label class="flex items-center gap-4 border-2 rounded-2xl px-5 py-4 cursor-pointer hover:border-blue-500 hover:bg-blue-50 transition-all has-[:checked]:border-blue-600 has-[:checked]:bg-blue-50 group">
                  <input type="radio" name="metode_pembayaran" value="qris" class="w-5 h-5 text-blue-600 focus:ring-blue-500" required>
                  <div>
                    <div class="font-bold text-gray-800 group-has-[:checked]:text-blue-700">QRIS / E-Wallet</div>
                    <div class="text-xs text-gray-500">Scan QR Code statis</div>
                  </div>
                </label>
              </div>
@php
    $masihAdaBelumSelesai = $daftarPesanan->contains(function ($trx) {
        return $trx->status_pesanan != 'selesai';
    });
@endphp
              <button
                type="submit"
                {{ $masihAdaBelumSelesai ? 'disabled' : '' }}
                 class="w-full font-black text-lg py-4 rounded-2xl transition-all shadow-lg mt-4
    {{ $masihAdaBelumSelesai
        ? 'bg-gray-300 text-gray-500 cursor-not-allowed shadow-none'
        : 'bg-green-600 text-white hover:bg-green-700 shadow-green-200 active:scale-[0.98]' }}">
    
    {{ $masihAdaBelumSelesai ? 'PESANAN BELUM SELESAI' : 'KONFIRMASI BAYAR' }}
              </button>
            </form>
          </div>

          <div class="bg-blue-600 rounded-2xl p-6 text-white shadow-lg">
            <div class="flex items-start gap-4">
              <div class="text-2xl">💡</div>
              <div class="text-sm leading-relaxed font-medium">
                Pastikan nominal yang diterima sudah sesuai sebelum menekan tombol konfirmasi. Semua pesanan di meja ini akan tergabung menjadi 1 struk pembayaran.
              </div>
            </div>
          </div>
        </div>

      </div>

    @endif
  </div>
{{-- ========================================== --}}
  {{-- MODAL KONFIRMASI HAPUS PESANAN             --}}
  {{-- ========================================== --}}
  <div id="modalHapus" class="fixed inset-0 z-[200] hidden items-center justify-center bg-black/40 backdrop-blur-sm p-4 transition-opacity duration-300 opacity-0">
    {{-- Kotak Putih Modal --}}
    <div id="modalHapusContent" class="bg-white w-full max-w-sm rounded-3xl p-6 shadow-2xl transform scale-95 transition-transform duration-300">
      
      <div class="flex flex-col items-center text-center">
        {{-- Ikon Peringatan Merah --}}
        <div class="w-16 h-16 bg-red-100 text-red-600 rounded-full flex items-center justify-center mb-4">
          <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
        </div>
        
        <h3 class="text-xl font-bold text-gray-800 mb-2">Batalkan Pesanan?</h3>
        <p class="text-sm text-gray-500 mb-6 leading-relaxed">
          Apakah Anda yakin ingin membatalkan pesanan baru di meja ini? Pesanan yang sudah dimasak tidak akan terhapus.
        </p>
        
        {{-- Tombol Aksi --}}
        <div class="flex gap-3 w-full">
          <button onclick="tutupModalHapus()" type="button" class="flex-1 bg-gray-100 text-gray-700 font-bold py-3 rounded-xl hover:bg-gray-200 transition-colors">
            Kembali
          </button>
          <button onclick="submitHapus()" type="button" class="flex-1 bg-red-600 text-white font-bold py-3 rounded-xl hover:bg-red-700 transition-colors shadow-lg shadow-red-200">
            Ya, Batalkan
          </button>
        </div>
      </div>

    </div>
  </div>
  <script>
    const modalHapus = document.getElementById('modalHapus');
    const modalHapusContent = document.getElementById('modalHapusContent');

    function bukaModalHapus() {
      // 1. Munculkan elemen (hapus 'hidden')
      modalHapus.classList.remove('hidden');
      modalHapus.classList.add('flex');
      
      // 2. Beri jeda 10ms agar browser membaca class flex dulu, lalu jalankan animasi
      setTimeout(() => {
        modalHapus.classList.remove('opacity-0');
        modalHapusContent.classList.remove('scale-95');
        modalHapusContent.classList.add('scale-100');
      }, 10);
    }

    function tutupModalHapus() {
      // 1. Jalankan animasi menyusut dan memudar
      modalHapus.classList.add('opacity-0');
      modalHapusContent.classList.remove('scale-100');
      modalHapusContent.classList.add('scale-95');
      
      // 2. Tunggu animasi selesai (300ms), lalu sembunyikan total
      setTimeout(() => {
        modalHapus.classList.add('hidden');
        modalHapus.classList.remove('flex');
      }, 300);
    }

    function submitHapus() {
      // Tombol saat ditekan akan mengeksekusi form aslinya
      document.getElementById('formHapusPesanan').submit();
    }
  </script>
</body>
</html>