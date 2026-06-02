@extends('layouts.app')

@section('title', 'pemesanan') 

@section('header')
  <div class="relative bg-orange-600 text-white w-full h-[140px] md:h-[220px] lg:h-[300px] overflow-hidden">
    {{-- Tombol Batal / Pintu Darurat --}}
    @if(request('from') === 'kasir')
        {{-- Jika datang dari Kasir, kembali ke dashboard Kasir --}}
        <a href="{{ route('kasir.index') }}" class="absolute top-4 left-4 z-20 bg-white/20 hover:bg-white/40 backdrop-blur-sm text-white px-4 py-2 rounded-xl font-semibold flex items-center gap-2 transition-all shadow-sm">
            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            Batal
        </a>
    @endif
   <img src="{{ asset('images/Logo.png') }}" class="w-full h-full object-cover object-top">
  </div>
  <style>
  .scrollbar-hide::-webkit-scrollbar {
      display: none;
  }

  .scrollbar-hide {
      -ms-overflow-style: none;
      scrollbar-width: none;
  }
</style>
@endsection

@section('content')

  {{-- Tabs kategori --}}
<div class="bg-white border-b sticky top-0 z-10">
  <div class="px-4">
    <div class="flex overflow-x-auto scrollbar-hide gap-1 md:justify-center">
      
      {{-- Tombol Semua --}}
      <button type="button" onclick="filterKategori('semua', this)"
         class="tab-btn px-4 py-3 text-sm font-medium whitespace-nowrap border-b-2 border-orange-600 transition-colors">
         Semua
      </button>

      {{-- Looping Tombol Kategori Lainnya --}}
      @foreach($kategoris as $kat)
        <button type="button" onclick="filterKategori('{{ $kat->id }}', this)"
           class="tab-btn px-4 py-3 text-sm font-medium whitespace-nowrap border-b-2 border-transparent text-gray-600 transition-colors">
           {{ $kat->nama }}
        </button>
      @endforeach

    </div>
  </div>
</div>

  {{-- List menu --}}
<div class="p-4 pb-28 grid grid-cols-2 md:grid-cols-3 gap-4">
  @foreach($menus as $menu)
    @php
      $qty = $cart[$menu->id]['qty'] ?? 0;
    @endphp

    {{-- TAMBAHKAN class menu-card, atribut data-kategori, dan efek transisi di div ini --}}
    <div class="menu-card transition-all duration-300 ease-in-out transform scale-100 opacity-100 bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden flex flex-col" 
         data-kategori="{{ $menu->kategori_menu_id }}">
      
      <div class="p-3 flex flex-col h-full">
        <div class="w-full h-32 md:h-40 rounded-xl bg-white flex items-center justify-center mb-3 overflow-hidden shrink-0">
          <img class="max-w-full max-h-full object-contain" src="{{ asset('storage/' . $menu->gambar) }}" alt="{{ $menu->nama }}">
        </div>

        <div class="font-bold text-sm md:text-base mb-1 line-clamp-2 h-10 md:h-12">{{ $menu->nama }}</div>
        <div class="text-[10px] md:text-xs text-gray-500 mb-3 line-clamp-2 flex-grow">{{ $menu->deskripsi }}</div>

        <div class="mt-auto">
          <div class="text-orange-600 font-bold text-sm md:text-base mb-2">
            Rp {{ number_format($menu->harga, 0, ',', '.') }}
          </div>

          <div class="flex items-center justify-between gap-1">
            <button type="button" onclick="updateCart({{ $menu->id }}, 'min')" class="w-7 h-7 md:w-8 md:h-8 rounded-lg border border-gray-300 flex items-center justify-center font-bold text-gray-600 hover:bg-gray-50">-</button>

            <div id="qty-{{ $menu->id }}" class="text-sm md:text-base font-semibold">{{ $qty }}</div>

            <button type="button" onclick="updateCart({{ $menu->id }}, 'add')" class="w-7 h-7 md:w-8 md:h-8 rounded-lg bg-orange-600 text-white flex items-center justify-center font-bold hover:bg-orange-700">+</button>
          </div>
        </div>
      </div>
    </div>
  @endforeach
</div>

  {{-- Bottom bar keranjang (Selalu di-render, tapi pakai class 'hidden' jika kosong) --}}
  <div id="bottom-bar" class="fixed bottom-0 w-full max-w-md mx-auto left-0 right-0 p-4 z-20 transition-all duration-300 transform {{ $cartQty > 0 ? 'translate-y-0 opacity-100' : 'translate-y-full opacity-0 hidden' }}"> 
    <a href="{{ route('menu.checkout', ['meja' => $meja, 'from' => request('from')]) }}" 
       class="bg-orange-600 text-white rounded-2xl shadow-xl px-5 py-4 flex items-center justify-between">
      <input type="hidden" name="from" value="{{ request('from') }}">
      <div class="flex items-center gap-2">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 1 0 0 4 2 2 0 0 0 0-4zm-8 2a2 2 0 1 1-4 0 2 2 0 0 1 4 0z"></path>
        </svg>
        <span class="font-semibold">Keranjang (<span id="bottom-cart-qty">{{ $cartQty }}</span>)</span>
      </div>
      <div id="bottom-cart-total" class="font-bold text-lg">Rp {{ number_format($cartTotal, 0, ',', '.') }}</div>
    </a>
  </div>
@endsection

@push('scripts')
<script>
  function updateCart(menuId, action) {
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
        
        // 2. Ambil elemen Bottom Bar
        const bottomBar = document.getElementById('bottom-bar');
        const bottomQty = document.getElementById('bottom-cart-qty');
        const bottomTotal = document.getElementById('bottom-cart-total');
        
        if (data.cart_qty > 0) {
           // Tampilkan bottom bar dengan animasi meluncur naik
           bottomBar.classList.remove('hidden', 'translate-y-full', 'opacity-0');
           bottomBar.classList.add('translate-y-0', 'opacity-100');
           
           // Update angkanya
           bottomQty.innerText = data.cart_qty;
           bottomTotal.innerText = 'Rp ' + data.cart_total;
        } else {
           // Sembunyikan bottom bar dengan animasi meluncur turun jika keranjang jadi 0
           bottomBar.classList.remove('translate-y-0', 'opacity-100');
           bottomBar.classList.add('translate-y-full', 'opacity-0');
           
           // Tunggu animasi turun selesai (300ms) baru sembunyikan elemennya dari layar
           setTimeout(() => {
               if(document.getElementById('bottom-cart-qty').innerText == "0") {
                   bottomBar.classList.add('hidden');
               }
           }, 300);
        }
      }
    })
    .catch(error => console.error('Error updating cart:', error));
  }
  function filterKategori(kategoriId, elemenTombol) {
    
    // 1. Ubah desain garis bawah dan warna text pada Tab
    const semuaTab = document.querySelectorAll('.tab-btn');
    semuaTab.forEach(tab => {
        tab.classList.remove('border-orange-600', 'text-black');
        tab.classList.add('border-transparent', 'text-gray-600');
    });
    elemenTombol.classList.remove('border-transparent', 'text-gray-600');
    elemenTombol.classList.add('border-orange-600', 'text-black');

    // 2. Saring (Sembunyikan/Tampilkan) Kartu Menu
    const semuaMenu = document.querySelectorAll('.menu-card');
    semuaMenu.forEach(menu => {
        if (kategoriId === 'semua' || menu.getAttribute('data-kategori') === kategoriId) {
            // Tampilkan dengan animasi (karena div Anda pakai flex flex-col, kita kembalikan ke flex)
            menu.style.display = 'flex'; 
            setTimeout(() => {
                menu.style.opacity = '1';
                menu.style.transform = 'scale(1)';
            }, 10);
        } else {
            // Sembunyikan dengan animasi mengecil dan memudar
            menu.style.opacity = '0';
            menu.style.transform = 'scale(0.9)';
            setTimeout(() => {
                menu.style.display = 'none';
            }, 300); // Waktu jeda ini disamakan dengan durasi animasi duration-300
        }
    });
  }
</script>
@endpush