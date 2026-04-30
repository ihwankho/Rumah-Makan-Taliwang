<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Dapur - Daftar Pesanan</title>
@vite('resources/css/app.css')
  <style>
    .snap-x-mandatory {
      scroll-snap-type: x mandatory;
    }
    .snap-start {
      scroll-snap-align: start;
    }
  </style>
</head>

<body class="bg-gray-50 min-h-screen">

  {{-- Topbar --}}
  <div class="bg-white border-b shadow-sm">
    <div class="max-w-[110rem] mx-auto px-6 py-6 flex items-center gap-3">
      <div class="w-12 h-12 rounded-xl bg-orange-100 flex items-center justify-center text-2xl">
        👨‍🍳
      </div>
      <div>
        <div class="text-2xl font-bold">Dapur</div>
        <div class="text-lg text-gray-600">Daftar Pesanan Masuk</div>
      </div>

      <div class="ml-auto">
        <button
          id="openKelolaMenuButton"
          type="button"
          class="px-4 py-2 rounded-lg border border-gray-200 bg-white text-gray-800 font-semibold hover:bg-gray-50 shadow-sm">
          Kelola Menu
        </button>
      </div>
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

  {{-- Modal Edit Pesanan --}}
  <div id="editPesananModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/40 px-4 py-8">
    <div class="w-full max-w-4xl overflow-hidden rounded-3xl bg-white shadow-2xl">
      
      {{-- Modal Header --}}
      <div class="flex items-center justify-between border-b border-gray-100 px-6 py-5 bg-gray-50/50">
        <div>
          <div class="text-xl font-bold text-gray-800">Edit Pesanan</div>
          <div id="editPesananSubtitle" class="text-sm text-gray-500 mt-0.5">-</div>
        </div>
        <button id="editPesananClose" type="button" class="text-gray-400 hover:text-gray-800 text-3xl font-light transition-colors">&times;</button>
      </div>

      {{-- Modal Body --}}
      <div class="max-h-[70vh] overflow-y-auto px-6 py-5">
        <div id="editPesananItems" class="grid gap-4">
          {{-- Items akan diisi via JavaScript --}}
        </div>
      </div>
      
    </div>
  </div>

  {{-- Notifikasi --}}
  @if(session('success'))
    <div class="max-w-7xl mx-auto px-6 pt-6">
      <div class="bg-green-200 border border-green-300 text-green-900 px-6 py-4 rounded-xl text-lg font-semibold">
        {{ session('success') }}
      </div>
    </div>
  @endif

  {{-- Content --}}
  <div class="max-w-[110rem] mx-auto px-6 py-8">
    @if($pesanans->count() === 0)
      <div class="bg-white rounded-2xl p-8 shadow-sm border text-center text-gray-500 text-xl">
        Belum ada pesanan masuk 🙌
      </div>
    @else
      @php
        $chunks = $pesanans->chunk(3);
      @endphp
      {{-- ✅ Horizontal Paging --}}
      <div class="overflow-x-auto snap-x-mandatory scroll-smooth">
        <div class="flex gap-8 pb-4">

          @foreach($chunks as $pageIndex => $chunk)
            <div class="snap-start min-w-full">
              <div class="grid grid-cols-1 md:grid-cols-3 xl:grid-cols-3 gap-8">
                @foreach($chunk as $pesanan)
                  <div class="bg-white rounded-2xl shadow-md border p-8 flex flex-col justify-between h-[calc(100vh-220px)]">
                    {{-- Header Card --}}
                    <div class="flex items-start justify-between gap-4">
                      <div>
                        <div class="text-3xl font-bold">
                          Meja {{ $pesanan->meja->nomor_meja ?? '-' }}
                        </div>
                        <div class="text-lg text-gray-600 mt-2 flex items-center gap-2">
                          ⏱️ {{ $pesanan->created_at->format('H:i') }}
                          <span class="text-gray-400">•</span>
                          {{ $pesanan->pelanggan->nama_pelanggan ?? '-' }}
                        </div>
                      </div>
                      {{-- Label Bungkus / Makan ditempat --}}
                      @if($pesanan->tipe_pesanan === 'bungkus')
                        <span class="px-4 py-2 rounded-lg text-lg font-bold bg-red-600 text-white">
                          Dibungkus
                        </span>
                      @else
                        <span class="px-4 py-2 rounded-lg text-lg font-bold bg-blue-600 text-white">
                          Makan di Tempat
                        </span>
                      @endif
                    </div>
                    <div class="my-6 border-t"></div>
                    {{-- List Item --}}
                    <div class="space-y-4 flex-1 overflow-y-auto pr-2">
                      @foreach($pesanan->detailPesanans as $detail)
                        @if($detail->menu && $detail->menu->perlu_dimasak)
                          
                          <div class="border border-gray-100 rounded-xl p-4 bg-white shadow-sm flex flex-col gap-3 transition-all">
                            
                            {{-- Info Item --}}
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                              <div class="flex gap-3 items-center">
                                <div class="w-10 h-10 rounded-full bg-orange-100 flex items-center justify-center text-orange-600 font-bold text-base border border-orange-200">
                                  {{ $detail->jumlah }}x
                                </div>
                                <div class="text-lg font-bold text-gray-800">
                                  {{ $detail->menu->nama }}
                                </div>
                              </div>
                              
                              {{-- Tombol Aksi (Sembunyi secara default, dikontrol oleh tombol Edit besar) --}}
                              <div class="action-buttons-{{ $pesanan->id }} hidden flex items-center gap-2">
                                <button type="button" class="toggle-edit-btn px-3 py-1.5 bg-gray-50 border border-gray-200 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-100 transition-colors" data-detail-id="{{ $detail->id }}">
                                  ✏️
                                </button>
                                <button type="button" class="delete-btn px-3 py-1.5 bg-red-50 text-red-600 text-sm font-medium rounded-lg hover:bg-red-100 transition-colors" data-detail-id="{{ $detail->id }}" data-pesanan-id="{{ $pesanan->id }}">
                                  🗑️
                                </button>
                              </div>
                            </div>

                            {{-- Form Pilihan Menu (Sembunyi secara default) --}}
                            <div id="edit-form-{{ $detail->id }}" class="edit-form-container-{{ $pesanan->id }} hidden border-t border-gray-100 pt-3 mt-1">
                              <div class="grid gap-2">
                                <label class="text-xs font-medium text-gray-600">Pilih Menu Pengganti:</label>
                                <select class="menu-select border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-orange-500 focus:border-orange-500 outline-none w-full bg-gray-50" data-detail-id="{{ $detail->id }}">
                                  @foreach($menus as $menuOption)
                                    <option value="{{ $menuOption->id }}" {{ $menuOption->id == $detail->id_menu ? 'selected' : '' }}>
                                      {{ $menuOption->nama }} {{ !$menuOption->is_aktif ? '(Tidak Aktif)' : '' }}
                                    </option>
                                  @endforeach
                                </select>
                              </div>
                              <div class="flex justify-end gap-2 mt-3">
                                <button type="button" class="cancel-edit-btn px-4 py-2 border border-gray-200 text-gray-600 text-sm font-medium rounded-lg hover:bg-gray-50 transition-colors" data-detail-id="{{ $detail->id }}">
                                  Batal
                                </button>
                                <button type="button" class="save-btn px-4 py-2 bg-green-600 text-white text-sm font-bold rounded-lg hover:bg-green-700 transition-colors" data-detail-id="{{ $detail->id }}" data-pesanan-id="{{ $pesanan->id }}">
                                  Simpan
                                </button>
                              </div>
                            </div>

                          </div>

                        @endif
                      @endforeach
                    </div>

                    {{-- Tombol Bawah --}}
                    <div class="mt-6 grid grid-cols-2 gap-4">
                      {{-- Tombol Edit Besar --}}
                      <button
                        type="button"
                        class="toggle-edit-mode-btn border border-orange-500 text-orange-600 text-lg font-semibold py-3 rounded-xl hover:bg-orange-50 transition-colors"
                        data-pesanan-id="{{ $pesanan->id }}">
                        ✏️ Edit
                      </button>

                      <form method="POST" action="{{ route('dapur.selesai', $pesanan->id) }}">
                        @csrf
                        <button
                          type="submit"
                          class="bg-green-600 text-white text-lg font-bold py-3 rounded-xl hover:bg-green-700 w-full transition-colors">
                          ✅ Selesai
                        </button>
                      </form>
                    </div>

                  </div>
                @endforeach

              </div>
            </div>
          @endforeach

        </div>
      </div>

      {{-- indikator halaman --}}
      <div class="mt-6 flex justify-center gap-3">
        @foreach($chunks as $i => $chunk)
          <div class="w-4 h-4 rounded-full {{ $loop->first ? 'bg-orange-500' : 'bg-gray-300' }}"></div>
        @endforeach
      </div>

      <div class="text-center text-lg text-gray-600 mt-3">
        Geser ke kanan untuk pesanan berikutnya →
      </div>

    @endif

  </div>

</body>
<script>
  // Logika Interaksi Edit Pesanan In-Page
  document.addEventListener('DOMContentLoaded', function () {
    
    // 1. Tombol Edit Besar (Mengaktifkan/Menonaktifkan Mode Edit pada Kartu)
    document.querySelectorAll('.toggle-edit-mode-btn').forEach(btn => {
      btn.addEventListener('click', function() {
        const pesananId = this.getAttribute('data-pesanan-id');
        const actionContainers = document.querySelectorAll(`.action-buttons-${pesananId}`);
        
        // Toggle visibilitas tombol Ubah & Hapus
        actionContainers.forEach(container => {
          container.classList.toggle('hidden');
        });

        // Cek apakah sedang dalam mode "Tutup" (menggunakan innerText lebih aman)
        if (this.innerText.includes('Tutup')) {
            // Jika ya, KEMBALIKAN ke tampilan Edit awal
            this.innerHTML = '✏️ Edit';
            this.classList.replace('text-gray-500', 'text-orange-600');
            this.classList.replace('border-gray-400', 'border-orange-500');
            
            // Sembunyikan juga form pilih menu jika ada yang sedang terbuka saat Edit ditutup
            document.querySelectorAll(`.edit-form-container-${pesananId}`).forEach(form => {
                 form.classList.add('hidden');
            });
        } else {
            // Jika tidak, UBAH ke tampilan Tutup Edit
            this.innerHTML = '❌ Tutup Edit';
            this.classList.replace('text-orange-600', 'text-gray-500');
            this.classList.replace('border-orange-500', 'border-gray-400');
        }
      });
    });

    // 2. Tombol Ubah Kecil (Membuka Form Pilih Menu Pengganti)
    document.querySelectorAll('.toggle-edit-btn').forEach(btn => {
      btn.addEventListener('click', function() {
        const detailId = this.getAttribute('data-detail-id');
        document.getElementById(`edit-form-${detailId}`).classList.toggle('hidden');
      });
    });

    // 3. Tombol Batal
    document.querySelectorAll('.cancel-edit-btn').forEach(btn => {
      btn.addEventListener('click', function() {
        const detailId = this.getAttribute('data-detail-id');
        document.getElementById(`edit-form-${detailId}`).classList.add('hidden');
      });
    });

    // 4. Tombol Simpan Perubahan
    document.querySelectorAll('.save-btn').forEach(button => {
      button.addEventListener('click', function() {
        const detailId = this.getAttribute('data-detail-id');
        const pesananId = this.getAttribute('data-pesanan-id');
        const menuSelect = document.querySelector(`select.menu-select[data-detail-id='${detailId}']`);
        
        const formData = new FormData();
        formData.append('menu_id', menuSelect.value);
        formData.append('_token', '{{ csrf_token() }}');

        fetch(`/dapur/${pesananId}/detail/${detailId}/replace`, {
          method: 'POST',
          body: formData
        })
        .then(response => {
          if(!response.ok) throw new Error('Gagal menyimpan');
          window.location.reload();
        })
        .catch(error => {
          console.error('Error:', error);
          alert('Gagal menyimpan perubahan item');
        });
      });
    });

    // 5. Tombol Hapus
    document.querySelectorAll('.delete-btn').forEach(btn => {
      btn.addEventListener('click', function() {
        const detailId = this.getAttribute('data-detail-id');
        const pesananId = this.getAttribute('data-pesanan-id');

        if (confirm('Yakin hapus item ini?')) {
          const formData = new FormData();
          formData.append('_token', '{{ csrf_token() }}');

          fetch(`/dapur/${pesananId}/detail/${detailId}/delete`, {
            method: 'POST',
            body: formData
          })
          .then(response => {
            if(!response.ok) throw new Error('Gagal menghapus');
            window.location.reload();
          })
          .catch(error => {
            console.error('Error:', error);
            alert('Gagal menghapus item');
          });
        }
      });
    });

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
        const url = `/dapur/menu/${menuId}/toggle`; 
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
</html>
