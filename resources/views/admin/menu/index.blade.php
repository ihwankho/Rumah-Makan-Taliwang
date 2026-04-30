@extends('layouts.admin')
@section('title', 'Menu')
@section('header_title', 'Kelola Menu Restoran')

@section('content')

  {{-- Bagian Atas: Info & Tombol Tambah --}}
  <div class="flex justify-end mb-6">
   

    {{-- Diperbaiki: route mengarah ke admin.menu.create --}}
    <a href="{{ route('admin.menu.create') }}"
       class="bg-green-600 text-white px-5 py-2.5 rounded-xl font-semibold hover:bg-green-700 inline-flex items-center transition-colors">
      + Tambah Menu
    </a>
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
            <th class="p-4 font-semibold text-gray-700">Gambar</th>
            <th class="p-4 font-semibold text-gray-700">Nama</th>
            <th class="p-4 font-semibold text-gray-700">Kategori</th>
            <th class="p-4 font-semibold text-gray-700">Harga</th>
            <th class="p-4 font-semibold text-gray-700">Masak?</th>
            <th class="p-4 font-semibold text-gray-700">Status</th>
            <th class="p-4 font-semibold text-gray-700">Aksi</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
          @foreach($menus as $menu)
            <tr class="hover:bg-gray-50 transition-colors">
              <td class="p-4">
                @if($menu->gambar)
                  <img src="{{ asset('storage/'.$menu->gambar) }}" class="w-16 h-16 rounded-xl object-cover border border-gray-100">
                @else
                  <div class="w-16 h-16 rounded-xl bg-gray-100 border border-gray-200 flex items-center justify-center text-xs text-gray-400 font-medium">
                    No Img
                  </div>
                @endif
              </td>

              <td class="p-4 font-medium text-gray-800">{{ $menu->nama }}</td>
              <td class="p-4 text-gray-600">{{ $menu->kategori->nama ?? '-' }}</td>
              <td class="p-4 font-medium text-orange-600">Rp {{ number_format($menu->harga, 0, ',', '.') }}</td>

              <td class="p-4 text-gray-600">
                {{ $menu->perlu_dimasak ? 'Ya' : 'Tidak' }}
              </td>

              <td class="p-4">
                @if($menu->is_aktif)
                  <span class="px-3 py-1.5 rounded-lg bg-green-50 text-green-700 font-semibold border border-green-100">Aktif</span>
                @else
                  <span class="px-3 py-1.5 rounded-lg bg-gray-50 text-gray-600 font-semibold border border-gray-200">Nonaktif</span>
                @endif
              </td>

              <td class="p-4">
                <div class="flex gap-2">
                  <a href="{{ route('admin.menu.edit', $menu->id) }}"
                     class="px-4 py-1.5 rounded-lg border border-gray-200 text-gray-700 hover:bg-gray-100 transition-colors">
                    Edit
                  </a>

                  <form method="POST" action="{{ route('admin.menu.destroy', $menu->id) }}"
                        onsubmit="return confirm('Yakin ingin menghapus menu ini?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="px-4 py-1.5 rounded-lg bg-red-50 text-red-600 border border-red-100 hover:bg-red-100 transition-colors">
                      Hapus
                    </button>
                  </form>
                </div>
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>
@endsection