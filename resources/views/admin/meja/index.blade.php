@extends('layouts.admin')
@section('title', 'Daftar Meja')
@section('header_title', 'Kelola Meja Restoran')

@section('content')

  {{-- Bagian Atas: Info & Tombol Aksi --}}
  <div class="flex justify-end mb-6">
    
    <div class="flex flex-wrap gap-2">
      {{-- Download semua QR (coming soon) --}}
      <a href="{{ route('admin.meja.cetak-semua-qr') }}" target="_blank" 
         class="px-5 py-2.5 rounded-xl border border-gray-300 bg-white hover:bg-gray-50 text-gray-700 font-semibold inline-flex items-center transition-colors shadow-sm">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
        Cetak Semua QR
      </a>

      {{-- Tambah meja otomatis --}}
      <form method="POST" action="{{ route('admin.meja.store') }}" class="inline-block">
        @csrf
        <button type="submit" 
                class="bg-green-600 text-white px-5 py-2.5 rounded-xl font-semibold hover:bg-green-700 inline-flex items-center transition-colors shadow-sm">
          + Tambah Meja
        </button>
      </form>
    </div>
  </div>

  {{-- Notifikasi --}}
  @if(session('success'))
    <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-xl mb-6 flex items-center">
      {{ session('success') }}
    </div>
  @endif

  @if(session('error'))
    <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-xl mb-6 flex items-center">
      {{ session('error') }}
    </div>
  @endif

  {{-- Tabel Content --}}
  <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="overflow-x-auto">
      <table class="w-full text-sm text-left">
        <thead class="bg-gray-50 border-b">
          <tr>
            <th class="p-4 font-semibold text-gray-700">Nomor Meja</th>
            <th class="p-4 font-semibold text-gray-700">QR Code</th>
            <th class="p-4 font-semibold text-gray-700 w-1/4">Aksi</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
          @forelse($mejas as $m)
            <tr class="hover:bg-gray-50 transition-colors">
              <td class="p-4 font-medium text-gray-800 text-lg">
                Meja {{ $m->nomor_meja }}
              </td>

             <td class="p-4">
  {{-- Tombol download QR per meja --}}
  <a href="{{ route('admin.meja.cetak-qr', $m->id) }}" target="_blank"
     class="px-4 py-1.5 rounded-lg border border-gray-200 text-gray-700 hover:bg-gray-100 transition-colors inline-flex items-center gap-2 font-medium text-xs">
    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
    Cetak QR
  </a>
</td>

              <td class="p-4">
                <div class="flex gap-2">
                  <a href="{{ route('admin.meja.edit', $m->id) }}" 
                     class="px-4 py-1.5 rounded-lg border border-gray-200 text-gray-700 hover:bg-gray-100 transition-colors">
                    Edit
                  </a>

                  <form method="POST" action="{{ route('admin.meja.destroy', $m->id) }}" 
                        onsubmit="return confirm('Yakin ingin menghapus meja ini?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="px-4 py-1.5 rounded-lg bg-red-50 text-red-600 border border-red-100 hover:bg-red-100 transition-colors">
                      Hapus
                    </button>
                  </form>
                </div>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="3" class="p-10 text-center text-gray-500">
                <div class="flex flex-col items-center justify-center">
                  {{-- Ikon kosong --}}
                  <svg class="w-12 h-12 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                  Belum ada meja. Klik <span class="font-semibold mx-1 text-gray-600">"+ Tambah Meja"</span> untuk mendaftarkan meja baru.
                </div>
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
@endsection