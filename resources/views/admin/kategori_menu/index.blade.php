@extends('layouts.admin') 
@section('title', 'Kategori Menu') 
@section('header_title', 'Kelola Kategori Menu') 

@section('content') 
  {{-- Bagian Atas: Info & Tombol Tambah --}} 
  <div class="flex justify-end mb-6"> 
    <a href="{{ route('admin.kategori-menu.create') }}" 
       class="bg-green-600 text-white px-5 py-2.5 rounded-xl font-semibold hover:bg-green-700 inline-flex items-center transition-colors"> 
      + Tambah Kategori 
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
            <th class="p-4 font-semibold text-gray-700">Nama Kategori</th> 
            <th class="p-4 font-semibold text-gray-700">Jumlah Menu</th> 
            <th class="p-4 font-semibold text-gray-700">Aksi</th> 
          </tr> 
        </thead> 

        <tbody class="divide-y divide-gray-100"> 
          @foreach($kategoris as $kat) 
            <tr class="hover:bg-gray-50 transition-colors"> 
              <td class="p-4 font-medium text-gray-800">{{ $kat->nama }}</td> 
              <td class="p-4 text-gray-600">{{ $kat->menus()->count() }}</td> 
              <td class="p-4"> 
                <div class="flex gap-2"> 
                  <a href="{{ route('admin.kategori-menu.edit', $kat->id) }}" 
                     class="px-4 py-1.5 rounded-lg border border-gray-200 text-gray-700 hover:bg-gray-100 transition-colors"> 
                    Edit 
                  </a> 

                  <form method="POST" 
                        action="{{ route('admin.kategori-menu.destroy', $kat->id) }}" 
                        onsubmit="return confirm('Yakin ingin menghapus kategori ini?')"> 
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