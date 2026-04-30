@extends('layouts.app')

@section('title', 'pemesanan') 

@section('header')
  <div class="bg-orange-600 text-white w-full h-32 md:h-48 overflow-hidden">
   <img src="{{ asset('images/Logo.png') }}" class="w-full h-full object-cover object-top">
  </div>
@endsection

@section('content')

  {{-- Tabs kategori --}}
  <div class="bg-white border-b sticky top-0 z-10"> 
    <div class="flex overflow-x-auto justify-center scrollbar-hide">
      <a href="{{ route('menu.index', $meja) }}" class="px-6 py-3 text-sm font-medium whitespace-nowrap border-b-2 {{ !$kategoriId ? 'border-orange-600 text-orange-600' : 'border-transparent text-gray-600' }}">Semua</a>
      @foreach($kategoris as $kat)
        <a href="{{ route('menu.index', $meja) }}?kategori={{ $kat->id }}" class="px-6 py-3 text-sm font-medium whitespace-nowrap border-b-2 {{ (string)$kategoriId === (string)$kat->id ? 'border-orange-600 text-orange-600' : 'border-transparent text-gray-600' }}">{{ $kat->nama }}</a>
      @endforeach
    </div>
  </div>

  {{-- List menu --}}
  <div class="p-4 pb-28 grid grid-cols-2 gap-4">
    @foreach($menus as $menu)
      @php
        $qty = $cart[$menu->id]['qty'] ?? 0;
      @endphp

      <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden flex flex-col">
        <div class="p-3 flex flex-col h-full">
          <div class="w-full h-32 md:h-40 rounded-xl bg-gray-100 flex items-center justify-center mb-3 overflow-hidden shrink-0">
            <img class="max-w-full max-h-full object-contain" src="{{ asset('storage/' . $menu->gambar) }}" alt="{{ $menu->nama }}">
          </div>

          <div class="font-bold text-sm md:text-base mb-1 line-clamp-2 h-10 md:h-12">{{ $menu->nama }}</div>
          <div class="text-[10px] md:text-xs text-gray-500 mb-3 line-clamp-2 flex-grow">{{ $menu->deskripsi }}</div>

          <div class="mt-auto">
            <div class="text-orange-600 font-bold text-sm md:text-base mb-2">
              Rp {{ number_format($menu->harga, 0, ',', '.') }}
            </div>

            <div class="flex items-center justify-between gap-1">
              {{-- HAPUS FORM, GANTI DENGAN ONCLICK --}}
              <button type="button" onclick="updateCart({{ $menu->id }}, 'min')" class="w-7 h-7 md:w-8 md:h-8 rounded-lg border border-gray-300 flex items-center justify-center font-bold text-gray-600 hover:bg-gray-50">-</button>

              {{-- TAMBAHKAN ID PADA QTY --}}
              <div id="qty-{{ $menu->id }}" class="text-sm md:text-base font-semibold">{{ $qty }}</div>

              {{-- HAPUS FORM, GANTI DENGAN ONCLICK --}}
              <button type="button" onclick="updateCart({{ $menu->id }}, 'add')" class="w-7 h-7 md:w-8 md:h-8 rounded-lg bg-orange-600 text-white flex items-center justify-center font-bold hover:bg-orange-700">+</button>
            </div>
          </div>
        </div>
      </div>
    @endforeach
  </div>

  {{-- Bottom bar keranjang --}}
  @if($cartQty > 0)
    <div id="bottom-bar" class="fixed bottom-0 w-full max-w-md mx-auto left-0 right-0 p-4 z-20"> 
      <a href="{{ route('menu.checkout', $meja) }}" class="bg-orange-600 text-white rounded-2xl shadow-xl px-5 py-4 flex items-center justify-between">
        <div class="flex items-center gap-2">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 1 0 0 4 2 2 0 0 0 0-4zm-8 2a2 2 0 1 1-4 0 2 2 0 0 1 4 0z"></path>
          </svg>
          {{-- TAMBAHKAN ID PADA QTY KERANJANG BAWAH --}}
          <span class="font-semibold">Keranjang (<span id="bottom-cart-qty">{{ $cartQty }}</span>)</span>
        </div>
        {{-- TAMBAHKAN ID PADA TOTAL HARGA BAWAH --}}
        <div id="bottom-cart-total" class="font-bold text-lg">Rp {{ number_format($cartTotal, 0, ',', '.') }}</div>
      </a>
    </div>
  @endif
@endsection

@push('scripts')
<script>
  function updateCart(menuId, action) {
    // Tentukan URL route tujuan (pastikan URL ini sama dengan route di web.php kamu)
    let url = action === 'add' ? `/cart/add/${menuId}` : `/cart/min/${menuId}`;

    fetch(url, {
      method: 'POST',
      headers: {
        'X-CSRF-TOKEN': '{{ csrf_token() }}',
        'Accept': 'application/json',
        'Content-Type': 'application/json'
      }
    })
    .then(response => response.json())
    .then(data => {
      if(data.status === 'success') {
        // 1. Update angka qty di menu yang sedang diklik
        document.getElementById(`qty-${menuId}`).innerText = data.item_qty;
        
        // 2. Cek apakah elemen bottom bar ada di layar
        const bottomQty = document.getElementById('bottom-cart-qty');
        const bottomTotal = document.getElementById('bottom-cart-total');
        
        if (bottomQty && bottomTotal) {
           // Jika ada, update angkanya
           bottomQty.innerText = data.cart_qty;
           bottomTotal.innerText = 'Rp ' + data.cart_total;

           // Jika keranjang dikurangi sampai 0, reload agar bottom bar hilang
           if (data.cart_qty === 0) {
               location.reload();
           }
        } else {
           // Jika bottom bar belum ada (karena tadinya 0 item), dan sekarang jadi 1 item
           // Reload halaman sekali saja untuk memunculkan bottom bar
           if (data.cart_qty > 0) {
               location.reload(); 
           }
        }
      }
    })
    .catch(error => console.error('Error updating cart:', error));
  }
</script>
@endpush