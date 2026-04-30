@extends('layouts.admin')
@section('title', 'Laporan Penjualan')
@section('header_title', 'Laporan Penjualan')

@section('content')
  {{-- Filter Tanggal --}}
  <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 mb-6 no-print">
    <form method="GET" action="{{ route('admin.laporan.index') }}"
          class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
      
      <div>
        <label class="block text-sm font-semibold text-gray-700 mb-1">Tanggal Mulai</label>
        <input type="date" name="mulai" value="{{ $tanggalMulai }}"
               class="w-full border border-gray-300 rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-orange-600 focus:border-orange-600 outline-none transition-all">
      </div>

      <div>
        <label class="block text-sm font-semibold text-gray-700 mb-1">Tanggal Selesai</label>
        <input type="date" name="selesai" value="{{ $tanggalSelesai }}"
               class="w-full border border-gray-300 rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-orange-600 focus:border-orange-600 outline-none transition-all">
      </div>

      <div class="md:col-span-2">
        <button type="submit" class="w-full bg-green-600 text-white font-bold py-2.5 rounded-xl hover:bg-green-700 transition-colors shadow-sm">
          Tampilkan Data
        </button>
      </div>
    </form>
  </div>

  {{-- Ringkasan--}}
  <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 flex flex-col justify-center">
      <div class="text-sm font-medium text-gray-500 mb-1">Total Transaksi</div>
      <div class="text-3xl font-bold text-gray-800">{{ $totalTransaksi }}</div>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 flex flex-col justify-center">
      <div class="text-sm font-medium text-gray-500 mb-1">Total Tunai</div>
      <div class="text-2xl font-bold text-green-600">
        Rp {{ number_format($totalTunai, 0, ',', '.') }}
      </div>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 flex flex-col justify-center">
      <div class="text-sm font-medium text-gray-500 mb-1">Total Non Tunai (QRIS)</div>
      <div class="text-2xl font-bold text-blue-600">
        Rp {{ number_format($totalNonTunai, 0, ',', '.') }}
      </div>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 flex flex-col justify-center bg-orange-50 border-orange-100">
      <div class="text-sm font-medium text-orange-800 mb-1">Total Pendapatan</div>
      <div class="text-3xl font-bold text-orange-600">
        Rp {{ number_format($totalPendapatan, 0, ',', '.') }}
      </div>
    </div>
  </div>

  {{-- Tabel Laporan --}}
  <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="overflow-x-auto">
      <table class="w-full text-sm text-left">
        <thead class="bg-gray-50 border-b">
          <tr>
            <th class="p-4 font-semibold text-gray-700">Tanggal</th>
            <th class="p-4 font-semibold text-gray-700">Meja</th>
            <th class="p-4 font-semibold text-gray-700">Pelanggan</th>
            <th class="p-4 font-semibold text-gray-700">Metode</th>
            <th class="p-4 font-semibold text-gray-700">Total</th>
          </tr>
        </thead>

        <tbody class="divide-y divide-gray-100">
          @forelse($pembayarans as $p)
            <tr class="hover:bg-gray-50 transition-colors">
              <td class="p-4 text-gray-600">
                {{ \Carbon\Carbon::parse($p->tanggal_bayar)->format('d M Y, H:i') }}
              </td>
              <td class="p-4 font-medium text-gray-800">
                Meja {{ $p->pesanan->meja->nomor_meja ?? '-' }}
              </td>
              <td class="p-4 text-gray-600">
                {{ $p->pesanan->pelanggan->nama_pelanggan ?? '-' }}
              </td>
              <td class="p-4">
                @if(strtolower($p->metode_pembayaran) == 'tunai')
                  <span class="px-3 py-1 rounded-lg bg-green-50 text-green-700 font-semibold border border-green-100 text-xs uppercase tracking-wide">Tunai</span>
                @else
                  <span class="px-3 py-1 rounded-lg bg-blue-50 text-blue-700 font-semibold border border-blue-100 text-xs uppercase tracking-wide">QRIS</span>
                @endif
              </td>
              <td class="p-4 font-bold text-orange-600">
                Rp {{ number_format($p->total_bayar, 0, ',', '.') }}
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="5" class="p-10 text-center text-gray-500">
                <div class="flex flex-col items-center justify-center">
                  <svg class="w-12 h-12 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                  Tidak ada transaksi pada periode tanggal ini.
                </div>
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

@endsection