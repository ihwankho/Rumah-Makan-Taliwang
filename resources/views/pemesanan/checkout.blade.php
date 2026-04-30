@extends('layouts.app')

@section('title', 'Checkout')

@section('header')
<div class="bg-orange-600 text-white p-4 flex items-center justify-between">
  <div>
    <div class="text-xs opacity-90">Checkout</div>
    <div class="font-bold text-lg">Pesanan Saya</div>
  </div>
  <a href="{{ route('menu.index', $meja) }}" class="text-sm underline">
    Kembali
  </a>
</div>
@endsection

@section('content')
<div class="p-4 pb-28">
  @if($cartQty == 0)
    <div class="bg-white rounded-2xl p-4 shadow-sm border border-gray-100 text-center">
      <div class="font-semibold">Keranjang masih kosong</div>
      <div class="text-sm text-gray-500 mt-1">Silakan pilih menu terlebih dahulu.</div>
      <a href="{{ route('menu.index', $meja) }}"
         class="inline-block mt-4 bg-orange-600 text-white px-4 py-2 rounded-xl font-semibold">
        Kembali ke Menu
      </a>
    </div>
  @else
    {{-- List cart --}}
    <div class="space-y-3">
      @foreach($cart as $item)
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 flex gap-3">
          <div class="w-16 h-16 rounded-xl bg-gray-100 overflow-hidden flex items-center justify-center">
            <img class="max-w-full max-h-full object-contain"
                 src="{{ asset('storage/'.$item['gambar']) }}"
                 alt="{{ $item['nama'] }}">
          </div>

          <div class="flex-1">
            <div class="font-bold">{{ $item['nama'] }}</div>
            <div class="text-xs text-gray-500">Qty: {{ $item['qty'] }}</div>
            <div class="text-orange-600 font-bold mt-1">
              Rp {{ number_format($item['harga'], 0, ',', '.') }}
            </div>
          </div>

          <div class="font-bold text-sm">
            Rp {{ number_format($item['qty'] * $item['harga'], 0, ',', '.') }}
          </div>
        </div>
      @endforeach
    </div>
  @endif
</div>

{{-- Bottom bar total + tombol konfirmasi --}}
@if($cartQty > 0)
  <div class="fixed bottom-0 w-full max-w-lg mx-auto left-0 right-0 p-4 z-20">
    <div class="bg-white rounded-2xl shadow-xl border border-gray-100 p-4">
      <div class="flex items-center justify-between mb-3">
        <div class="text-sm text-gray-500">Total</div>
        <div class="font-bold text-lg text-orange-600">
          Rp {{ number_format($cartTotal, 0, ',', '.') }}
        </div>
      </div>

      <button
        onclick="openModal()"
        class="w-full bg-orange-600 text-white font-bold py-3 rounded-xl hover:bg-orange-700"
      >
        Konfirmasi Pesanan
      </button>
    </div>
  </div>
@endif

{{-- Modal Konfirmasi --}}
<div id="modal" class="fixed inset-0 hidden items-center justify-center bg-black/40 p-4 z-50">
  <div class="bg-white w-full max-w-md rounded-2xl p-5 shadow-xl">
    <div class="flex items-center justify-between mb-4">
      <div class="font-bold text-lg">Konfirmasi Pesanan</div>
      <button onclick="closeModal()" class="text-gray-600 font-bold text-xl">&times;</button>
    </div>

    <form method="POST" action="{{ route('menu.confirm', $meja) }}">
      @csrf
      <label class="text-sm font-semibold">Nama</label>
      <input
        type="text"
        name="nama"
        required
        placeholder="Masukkan nama pelanggan"
        class="mt-1 w-full border rounded-xl px-3 py-2 focus:outline-none focus:ring focus:ring-orange-200"
      >

      <div class="mt-4">
        <div class="text-sm font-semibold mb-2">Tipe Pesanan</div>
        <div class="flex gap-3">
          <label class="flex items-center gap-2 border rounded-xl px-3 py-2 w-full cursor-pointer">
            <input type="radio" name="tipe" value="makan_ditempat" required>
            <span class="text-sm">Makan ditempat</span>
          </label>
          <label class="flex items-center gap-2 border rounded-xl px-3 py-2 w-full cursor-pointer">
            <input type="radio" name="tipe" value="bungkus" required>
            <span class="text-sm">Bungkus</span>
          </label>
        </div>
      </div>

      <button
        type="submit"
        class="mt-5 w-full bg-orange-600 text-white font-bold py-3 rounded-xl hover:bg-orange-700"
      >
        Konfirmasi
      </button>
    </form>
  </div>
</div>
@endsection

@push('scripts')
<script>
  function openModal() {
    const modal = document.getElementById('modal');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
  }

  function closeModal() {
    const modal = document.getElementById('modal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
  }
</script>
@endpush