<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Kasir - Daftar Meja</title>
  @vite(['resources/css/app.css' , 'resources/js/app.js'])
</head>

<body class="bg-gray-50 min-h-screen overflow-x-hidden">
  @php
    $totalMeja = $mejas->count();
    $aktifCount = 0;

    foreach($mejas as $m) {
      if(isset($pesananTerakhir[$m->id])) {
        $aktifCount++;
      }
    }
  @endphp


 {{-- Header --}}
<div class="bg-white border-b shadow-sm">
    <div class="max-w-[110rem] mx-auto px-6 py-6 flex items-center justify-between">

        {{-- Logo --}}
        <div class="relative">

            <button onclick="forlog()" class="flex items-center space-x-4 focus:outline-none">

                <div class="flex-shrink-0">
                    <img 
                        src="{{ asset('storage/menus/logo.png') }}" 
                        alt="Logo" 
                        class="w-12 h-12 object-cover rounded-full"
                    >
                </div>

                <div class="flex flex-col justify-center text-left">
                    <h1 class="text-2xl font-bold text-gray-800 leading-tight">
                        Kasir
                    </h1>

                    <p class="text-sm text-gray-500">
                        Manajemen Pembayaran
                    </p>
                </div>

            </button>

            {{-- Dropdown --}}
            <div 
                id="profileMenu"
                class="hidden absolute left-0 mt-3 w-52 bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden z-50"
            >

                <div class="px-4 py-3 border-b">
                    <p class="text-sm font-semibold text-gray-800">
                        kasir
                    </p>
                </div>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <button 
                        type="submit"
                        class="w-full text-left px-4 py-3 text-sm text-red-500 hover:bg-red-50 transition"
                    >
                        Logout
                    </button>
                </form>

            </div>

        </div>

        {{-- Ringkasan --}}
        <div class="flex items-center gap-3">

            <button 
                id="openBuatPesananButton"
                type="button"
                class="px-4 py-2 rounded-lg border border-gray-200 bg-white text-gray-800 font-semibold hover:bg-gray-50 shadow-sm">
                Buat Pesanan
            </button>

            <button
                id="openKelolaMenuButton"
                type="button"
                class="px-4 py-2 rounded-lg border border-gray-200 bg-white text-gray-800 font-semibold hover:bg-gray-50 shadow-sm">
                Kelola Menu
            </button>

            <div class="bg-green-100 border border-green-200 text-green-800 px-4 py-2 rounded-xl">
                <div class="text-xs font-semibold">Meja Aktif</div>
                <div class="text-lg font-bold">{{ $aktifCount }}</div>
            </div>

            <div class="bg-gray-100 border border-gray-200 text-gray-800 px-4 py-2 rounded-xl">
                <div class="text-xs font-semibold">Total Meja</div>
                <div class="text-lg font-bold">{{ $totalMeja }}</div>
            </div>

        </div>

    </div>
</div>
{{-- Modal Buat Pesanan Baru --}}
<div id="buatPesananModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/40 px-4 py-8">
  <div class="w-full max-w-md overflow-hidden rounded-3xl bg-white shadow-2xl">
    
    {{-- Modal Header --}}
    <div class="flex items-center justify-between border-b border-gray-100 px-6 py-5 bg-gray-50/50">
      <div class="text-xl font-bold text-gray-800">Buat Pesanan</div>
      <button id="buatPesananClose" type="button" class="text-gray-400 hover:text-gray-800 text-3xl font-light">&times;</button>
    </div>

    {{-- Modal Body --}}
    <form action="{{ route('kasir.proses-pilih-meja') }}" method="POST" class="p-6">
      @csrf
      <div class="mb-6">
        <label for="nomor_meja" class="block text-sm font-medium text-gray-700 mb-2">Nomor Meja Pelanggan</label>
        <input 
          type="number" 
          name="nomor_meja" 
          id="nomor_meja" 
          required 
          min="1"
          placeholder="Contoh: 5"
          class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none text-lg font-semibold"
        >
      </div>

      <button type="submit" class="w-full bg-blue-600 text-white font-bold py-3 rounded-xl hover:bg-blue-700 transition-all shadow-lg shadow-blue-100">
        Buka Menu Pemesanan
      </button>
    </form>
    
  </div>
</div>

  {{-- Modal Kelola Menu --}}
  <div id="kelolaMenuModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/40 px-4 py-8">
    <div class="w-full max-w-3xl overflow-hidden rounded-3xl bg-white shadow-2xl">
      
      {{-- Modal Header --}}
      <div class="flex items-center justify-between border-b border-gray-100 px-6 py-5 bg-gray-50/50">
        <div>
          <div class="text-xl font-bold text-gray-800">Kelola Menu</div>
          <div class="text-sm text-gray-500 mt-0.5">Aktifkan atau nonaktifkan ketersediaan menu di daftar pesanan</div>
        </div>
        <button id="kelolaMenuClose" type="button" class="text-gray-400 hover:text-gray-800 text-3xl font-light transition-colors">&times;</button>
      </div>

      {{-- Modal Body --}}
      <div class="max-h-[70vh] overflow-y-auto px-6 py-5">
        <div class="grid gap-3">
          @foreach($menus as $menu)
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 rounded-2xl border border-gray-100 p-4 transition-all hover:border-gray-200 hover:shadow-sm">
              
              <div class="text-lg font-medium text-gray-800">{{ $menu->nama }}</div>
              <div class="flex items-center justify-between sm:justify-end gap-4 w-full sm:w-auto mt-1 sm:mt-0">
                <span id="status-text-{{ $menu->id }}" class="text-sm font-semibold {{ $menu->is_aktif ? 'text-green-600' : 'text-gray-400' }}">
                  {{ $menu->is_aktif ? 'Tersedia' : 'Habis' }}
                </span>
                <label class="relative inline-flex items-center cursor-pointer">
                  <input type="checkbox" 
                         class="sr-only peer toggle-menu" 
                         data-id="{{ $menu->id }}" 
                         {{ $menu->is_aktif ? 'checked' : '' }}>
                  <div class="w-11 h-6 bg-gray-200 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-green-500"></div>
                </label>

              </div>
            </div>
          @endforeach
        </div>
      </div>
      
    </div>
  </div>

  {{-- TOAST NOTIFICATIONS --}}
  <div class="fixed top-10 left-1/2 transform -translate-x-1/2 z-[100] flex flex-col gap-4 w-full max-w-md px-4">
    
    @if(session('success'))
      <div id="toast-success" class="flex items-center w-full p-5 space-x-4 text-gray-800 bg-white rounded-3xl shadow-2xl border-b-8 border-green-500 transform transition-all duration-500 translate-y-0 opacity-100" role="alert">
        <div class="inline-flex items-center justify-center flex-shrink-0 w-14 h-14 bg-green-100 rounded-2xl">
          <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path>
          </svg>
        </div>
        <div class="flex-1 font-extrabold text-lg">{{ session('success') }}</div>
        <button type="button" onclick="closeToast('toast-success')" class="ml-auto bg-white text-gray-400 hover:text-gray-900 rounded-xl focus:ring-2 focus:ring-gray-300 p-2 hover:bg-gray-100 transition-colors">
          <span class="sr-only">Tutup</span>
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
        </button>
      </div>
    @endif

    @if(session('error'))
      <div id="toast-error" class="flex items-center w-full p-5 space-x-4 text-gray-800 bg-white rounded-3xl shadow-2xl border-b-8 border-red-500 transform transition-all duration-500 translate-y-0 opacity-100" role="alert">
        <div class="inline-flex items-center justify-center flex-shrink-0 w-14 h-14 bg-red-100 rounded-2xl">
          <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"></path>
          </svg>
        </div>
        <div class="flex-1 font-extrabold text-lg">{{ session('error') }}</div>
        <button type="button" onclick="closeToast('toast-error')" class="ml-auto bg-white text-gray-400 hover:text-gray-900 rounded-xl focus:ring-2 focus:ring-gray-300 p-2 hover:bg-gray-100 transition-colors">
          <span class="sr-only">Tutup</span>
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
        </button>
      </div>
    @endif
  </div>

  {{-- Content --}}
  <div class="max-w-[110rem] mx-auto px-6 py-8">


    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 xl:grid-cols-5 gap-8">

      @foreach($mejas as $meja)
        @php
          $pesanan = $pesananTerakhir[$meja->id] ?? null;
          $aktif = $pesanan ? true : false;
        @endphp

        <a href="{{ route('kasir.detail', $meja->id) }}"
           class="bg-white rounded-2xl shadow-md border border-gray-100 p-8 hover:shadow-lg hover:scale-[1.02] transition duration-200 h-[250px] flex flex-col overflow-hidden justify-between">

          {{-- Header Meja --}}
          <div>
            <div class="flex items-center justify-between mb-4">
              <div class="text-3xl font-bold text-gray-800"> Meja {{ $meja->nomor_meja }}</div>

              @if($aktif)
                <span class="px-4 py-2 rounded-full text-sm font-bold bg-green-600 text-white shadow-md">
                  🟢 Aktif
                </span>
              @else
                <span class="px-4 py-2 rounded-full text-sm font-bold bg-gray-300 text-gray-700">
                  ⚪ Nonaktif
                </span>
              @endif
            </div>

            <div class="border-t border-gray-200 mb-4"></div>

            {{-- Isi Pesanan --}}
            @if($aktif)
              <div class="space-y-2">
                <div class="text-xl font-semibold truncate text-gray-900">🧍
                  {{ $pesanan->pelanggan->nama_pelanggan ?? '-' }}
                </div>

                <div class="text-sm text-gray-700 mt-1">-->
                  {{ $pesanan->tipe_pesanan === 'bungkus' ? 'Dibungkus' : ' Makan di Tempat' }}
                  • {{ $pesanan->created_at->format('H:i') }}
                </div>
              </div>
            @else
              <div class="text-lg text-gray-600">
                Belum ada pesanan
              </div>
            @endif
          </div>

          {{-- Footer --}}
          <div class="text-sm text-gray-500 mt-4">
            Klik untuk detail tagihan
          </div>

        </a>
      @endforeach

    </div>

  </div>
<script>
  function forlog() {
        const menu = document.getElementById('profileMenu');
        menu.classList.toggle('hidden');
    }

    window.addEventListener('click', function(e) {
        const profileMenu = document.getElementById('profileMenu');

        if (!e.target.closest('.relative')) {
            profileMenu.classList.add('hidden');
        }
    });
  function closeToast(id) {
    const toast = document.getElementById(id);
    if (toast) {
      toast.classList.remove('translate-y-0', 'opacity-100');
      toast.classList.add('-translate-y-10', 'opacity-0'); 
      setTimeout(() => toast.remove(), 500);
    }
  }

  // Toast Auto-dismiss
  document.addEventListener('DOMContentLoaded', function() {
    if(document.getElementById('toast-success')) {
      setTimeout(() => closeToast('toast-success'), 4000);
    }
    if(document.getElementById('toast-error')) {
      setTimeout(() => closeToast('toast-error'), 4000);
    }
  });
  // Modal Buat Pesanan
document.addEventListener('DOMContentLoaded', function () {
  const openBtn = document.getElementById('openBuatPesananButton');
  const closeBtn = document.getElementById('buatPesananClose');
  const modal = document.getElementById('buatPesananModal');
  const inputMeja = document.getElementById('nomor_meja');

  if (openBtn && closeBtn && modal) {
    openBtn.addEventListener('click', function () {
      modal.classList.remove('hidden');
      modal.classList.add('flex');
      // Fokuskan kursor langsung ke input saat modal terbuka
      setTimeout(() => inputMeja.focus(), 100); 
    });

    closeBtn.addEventListener('click', function () {
      modal.classList.add('hidden');
      modal.classList.remove('flex');
    });

    modal.addEventListener('click', function (event) {
      if (event.target === modal) {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
      }
    });
  }
});
  // Kelola Menu Modal
  document.addEventListener('DOMContentLoaded', function () {
    const openButton = document.getElementById('openKelolaMenuButton');
    const closeButton = document.getElementById('kelolaMenuClose');
    const modal = document.getElementById('kelolaMenuModal');

    if (openButton && closeButton && modal) {
      openButton.addEventListener('click', function () {
        modal.classList.remove('hidden');
        modal.classList.add('flex');
      });

      closeButton.addEventListener('click', function () {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
      });

      modal.addEventListener('click', function (event) {
        if (event.target === modal) {
          modal.classList.add('hidden');
          modal.classList.remove('flex');
        }
      });
    }
  });

  // Toggle Menu Status
  document.addEventListener('DOMContentLoaded', function () {
    const toggleInputs = document.querySelectorAll('.toggle-menu');
    toggleInputs.forEach(input => {
      input.addEventListener('change', function() {
        const menuId = this.getAttribute('data-id');
        const isChecked = this.checked;
        const statusText = document.getElementById(`status-text-${menuId}`);
        const previousState = !isChecked; 
        const url = `/kasir/menu/${menuId}/toggle`; 
        fetch(url, {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
          },
          body: JSON.stringify({
          })
        })
        .then(response => {
           if(!response.ok) {
              throw new Error('Network response was not ok');
           }
           return response; 
        })
        .then(data => {
            if (isChecked) {
              statusText.textContent = 'Tersedia';
              statusText.classList.remove('text-gray-400');
              statusText.classList.add('text-green-600');
            } else {
              statusText.textContent = 'Habis';
              statusText.classList.remove('text-green-600');
              statusText.classList.add('text-gray-400');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Gagal mengubah status menu. Silakan coba lagi.');
            this.checked = previousState;
        });
      });
    });
  });
</script>
</body>
</html>
