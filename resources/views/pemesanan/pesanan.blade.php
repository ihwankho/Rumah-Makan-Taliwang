@extends('layouts.app')

@section('title', 'Detail Pesanan')

@section('header')
  <div class="bg-orange-600 text-white p-4 sticky top-0 z-10 shadow-sm">
    <div class="flex flex-col">
      <div class="text-xs opacity-90">Pesanan Aktif Saya</div>
      <div class="font-bold text-lg leading-tight">Meja {{ $infoMeja->nomor_meja ?? '-' }}</div>
      <div class="text-xs opacity-90 mt-1">
        {{ $infoPelanggan->nama_pelanggan ?? '-' }}
      </div>
    </div>
  </div>
@endsection

@section('content')
  {{-- Notifikasi --}}
  @if(session('success'))
    <div class="p-4 pb-0">
      <div class="bg-green-500 text-white px-4 py-3 rounded-xl shadow-lg flex items-center gap-2">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0" viewBox="0 0 20 20" fill="currentColor">
          <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
        </svg>
        <span class="font-medium text-sm">{{ session('success') }}</span>
      </div>
    </div>
  @endif

  <div class="pb-32">
    {{-- Looping Semua Pesanan Aktif (Makan Ditempat & Bungkus) --}}
    @foreach($pesanans as $pesanan)
      <div class="mt-4 px-4">
        {{-- Info Status Per Pesanan --}}
        <div class="bg-white rounded-t-2xl shadow-sm border border-gray-100 p-4 border-b-0">
          <div class="flex items-center justify-between mb-3">
            {{-- Badge Tipe Pesanan --}}
            <span class="px-2.5 py-1 bg-gray-100 text-gray-700 text-[10px] font-black uppercase rounded-lg tracking-wider">
              {{ $pesanan->tipe_pesanan === 'bungkus' ? '📦 BUNGKUS' : '🍽️ MAKAN DI TEMPAT' }}
            </span>
            {{-- Badge Status --}}
            <div class="px-3 py-1 rounded-full text-xs font-bold bg-orange-100 text-orange-600 uppercase">
              {{ $pesanan->status_pesanan }}
            </div>
          </div>
          
          <div class="flex items-center justify-between pt-3 border-t border-dashed">
            <div class="text-sm text-gray-500">Waktu Transaksi</div>
            <div class="font-semibold text-sm">{{ $pesanan->created_at->format('d M Y, H:i') }}</div>
          </div>
        </div>

        {{-- List Menu Per Pesanan --}}
        <div class="bg-gray-50 p-3 rounded-b-2xl border border-gray-100 border-t-0 space-y-2">
          
          @if($pesanan->detailPesanans->isEmpty())
             {{-- Jika semua menu yang dipesan tidak perlu dimasak (misal: hanya pesan es teh) --}}
             <div class="text-center py-4 text-xs text-gray-400 italic">
                Menu langsung disiapkan oleh kasir.
             </div>
          @else
             @foreach($pesanan->detailPesanans as $detail)
              <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-3 flex gap-3">
                <div class="w-16 h-16 rounded-lg bg-gray-50 overflow-hidden flex items-center justify-center shrink-0">
                  <img class="max-w-full max-h-full object-cover"
                       src="{{ asset('storage/'.$detail->menu->gambar) }}"
                       alt="{{ $detail->menu->nama }}">
                </div>

                <div class="flex-1 flex flex-col justify-center">
                  <div class="font-bold text-sm text-gray-800 line-clamp-1">{{ $detail->menu->nama }}</div>
                  <div class="text-[10px] text-gray-400 mt-0.5">Qty: {{ $detail->jumlah }} x Rp {{ number_format($detail->harga_satuan, 0, ',', '.') }}</div>
                  <div class="text-orange-600 font-bold text-sm mt-1">
                    Rp {{ number_format($detail->subtotal, 0, ',', '.') }}
                  </div>
                </div>
              </div>
             @endforeach
          @endif
          
        </div>
      </div>
    @endforeach
  </div>

  {{-- Bottom bar --}}
  <div class="fixed bottom-0 left-0 right-0 p-4 bg-gradient-to-t from-gray-50 via-gray-50 to-transparent max-w-lg mx-auto z-20">
    <div class="bg-white rounded-2xl shadow-[0_-10px_40px_rgba(0,0,0,0.1)] border border-gray-100 p-4">
      <div class="flex items-center justify-between">
        <div>
          <div class="text-[10px] uppercase tracking-wider text-gray-400 font-bold">Total Bayar Semua Pesanan</div>
          <div class="font-black text-xl text-orange-600">
            Rp {{ number_format($totalSemuaPesanan, 0, ',', '.') }}
          </div>
        </div>

        <a href="{{ route('menu.index', $mejaId) }}"
           class="bg-orange-600 text-white font-bold px-6 py-3 rounded-xl hover:bg-orange-700 transition-colors shadow-lg shadow-orange-200">
          Pesan Lagi
        </a>
      </div>
    </div>
  </div>
@endsection