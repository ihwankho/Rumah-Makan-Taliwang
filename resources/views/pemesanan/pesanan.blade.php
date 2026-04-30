@extends('layouts.app')

@section('title', 'Detail Pesanan')

@section('header')
  <div class="bg-orange-600 text-white p-4">
    <div class="flex flex-col">
      <div class="text-xs opacity-90">Pesanan Saya</div>
      <div class="font-bold text-lg leading-tight">Meja {{ $pesanan->meja->nomor_meja ?? '-' }}</div>
      <div class="text-xs opacity-90 mt-1">
        {{ $pesanan->pelanggan->nama_pelanggan ?? '-' }} • 
        {{ $pesanan->tipe_pesanan === 'bungkus' ? 'Bungkus' : 'Makan di tempat' }}
      </div>
    </div>
  </div>
@endsection

@section('content')
  {{-- Notifikasi --}}
  @if(session('success'))
    <div class="p-4">
      <div class="bg-green-500 text-white px-4 py-3 rounded-xl shadow-lg flex items-center gap-2">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
          <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
        </svg>
        <span class="font-medium">{{ session('success') }}</span>
      </div>
    </div>
  @endif

  {{-- Info Status --}}
  <div class="px-4 mt-2">
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4">
      <div class="flex items-center justify-between">
        <div class="text-sm text-gray-500">Status Pesanan</div>
        <div class="px-3 py-1 rounded-full text-xs font-bold bg-orange-100 text-orange-600 uppercase">
          {{ $pesanan->status_pesanan }}
        </div>
      </div>
      <div class="flex items-center justify-between mt-3 pt-3 border-t border-dashed">
        <div class="text-sm text-gray-500">Waktu Transaksi</div>
        <div class="font-semibold text-sm">{{ $pesanan->created_at->format('d M Y, H:i') }}</div>
      </div>
    </div>
  </div>

  {{-- List Pesanan --}}
  <div class="p-4 space-y-3 pb-32">
    <div class="text-sm font-bold text-gray-700 ml-1">Detail Menu</div>
    @foreach($pesanan->detailPesanans as $detail)
      <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-3 flex gap-4">
        <div class="w-20 h-20 rounded-xl bg-gray-50 overflow-hidden flex items-center justify-center shrink-0">
          <img class="max-w-full max-h-full object-cover"
               src="{{ asset('storage/'.$detail->menu->gambar) }}"
               alt="{{ $detail->menu->nama }}">
        </div>

        <div class="flex-1 flex flex-col justify-center">
          <div class="font-bold text-gray-800 line-clamp-1">{{ $detail->menu->nama }}</div>
          <div class="text-xs text-gray-400 mt-0.5">Qty: {{ $detail->jumlah }} x Rp {{ number_format($detail->harga_satuan, 0, ',', '.') }}</div>
          <div class="text-orange-600 font-bold mt-1">
            Rp {{ number_format($detail->subtotal, 0, ',', '.') }}
          </div>
        </div>
      </div>
    @endforeach
  </div>

  {{-- Bottom bar --}}
  <div class="fixed bottom-0 left-0 right-0 p-4 bg-gradient-to-t from-gray-50 via-gray-50 to-transparent max-w-lg mx-auto">
    <div class="bg-white rounded-2xl shadow-[0_-10px_40px_rgba(0,0,0,0.1)] border border-gray-100 p-4">
      <div class="flex items-center justify-between">
        <div>
          <div class="text-[10px] uppercase tracking-wider text-gray-400 font-bold">Total Bayar</div>
          <div class="font-black text-xl text-orange-600">
            Rp {{ number_format($pesanan->total_harga, 0, ',', '.') }}
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