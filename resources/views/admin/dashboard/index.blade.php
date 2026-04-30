@extends('layouts.admin')

@section('title', 'Dashboard')
@section('header_title', 'Overview Hari Ini')

@section('content')
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">

        {{-- Top Menu --}}
        <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-100">
            <h2 class="font-bold text-gray-800 mb-4">Top Menu Bulan Ini</h2>
            <div class="h-64">
    <canvas id="topMenuChart"></canvas>
</div>
        </div>

        {{-- Tren Pengunjung --}}
        <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-100">
            <h2 class="font-bold text-gray-800 mb-4">Tren Pengunjung</h2>
            <div class="h-64">
    <canvas id="visitorChart"></canvas>
</div>
        </div>

    </div>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex items-center gap-4">
            <div class="w-12 h-12 rounded-full bg-blue-100 flex items-center justify-center text-blue-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-width="2" d="M5 9h14l1 12H4L5 9z"/>
                </svg>
            </div>
            <div>
                <div class="text-sm text-gray-500">Pesanan Baru</div>
                <div class="text-2xl font-bold">{{ $pesananBaru }}</div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex items-center gap-4">
            <div class="w-12 h-12 rounded-full bg-orange-100 flex items-center justify-center text-orange-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-width="2" d="M7 20H2v-2a3 3 0 015.356-1.857M17 20h5v-2a3 3 0 00-5.356-1.857"/>
                </svg>
            </div>
            <div>
                <div class="text-sm text-gray-500">Meja Terisi</div>
                <div class="text-2xl font-bold">{{ $mejaTerisi }} <span class="text-sm text-gray-400">/ {{ $totalMeja }}</span></div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex items-center gap-4">
            <div class="w-12 h-12 rounded-full bg-green-100 flex items-center justify-center text-green-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-width="2" d="M12 8c-3 0-3 4 0 4s3 4 0 4"/>
                </svg>
            </div>
            <div>
                <div class="text-sm text-gray-500">Pendapatan Hari Ini</div>
                <div class="text-2xl font-bold">Rp {{ number_format($pendapatanHariIni, 0, ',', '.') }}</div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex items-center gap-4">
            <div class="w-12 h-12 rounded-full bg-red-100 flex items-center justify-center text-red-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-width="2" d="M12 9v2m0 4h.01"/>
                </svg>
            </div>
            <div>
                <div class="text-sm text-gray-500">Perlu Perhatian</div>
                <div class="text-2xl font-bold text-red-600">{{ $menuPerluPerhatian }} <span class="text-sm text-gray-400">Menu Tidak Aktif</span></div>
            </div>
        </div>

    </div>
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-5 border-b flex justify-between items-center">
            <h2 class="font-bold text-gray-800">Pesanan Masuk</h2>
            @if(!isset($showAll) || !$showAll)
                <a href="{{ route('admin.admin.dashboard', ['all' => 1]) }}" class="text-sm text-orange-600">Lihat Semua</a>
            @else
                <a href="{{ route('admin.admin.dashboard') }}" class="text-sm text-orange-600">Tampilkan Terbaru</a>
            @endif
        </div>

        <table class="w-full text-sm table-auto">
            <thead class="bg-gray-50 text-gray-500 text-left">
                <tr>
                    <th class="p-4">Waktu</th>
                    <th class="p-4">Meja</th>
                    <th class="p-4">Pelanggan</th>
                    <th class="p-4">Tipe</th>
                    <th class="p-4">Total</th>
                    <th class="p-4">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse($pesananMasuk as $pesanan)
                <tr class="hover:bg-gray-50">
                    <td class="p-4">{{ $pesanan->created_at->format('H:i') }}</td>
                    <td class="p-4 font-bold">{{ $pesanan->meja->nomor_meja ?? '-' }}</td>
                    <td class="p-4">{{ $pesanan->pelanggan->nama_pelanggan ?? '-' }}</td>
                    <td class="p-4">{{ $pesanan->tipe_pesanan === 'bungkus' ? 'Bungkus' : 'Makan ditempat' }}</td>
                    <td class="p-4 font-bold">Rp {{ number_format($pesanan->total_harga, 0, ',', '.') }}</td>
                    <td class="p-4">
                        @if($pesanan->status_pesanan === 'menunggu')
                            <span class="bg-yellow-100 text-yellow-700 px-2 py-1 rounded text-xs">Menunggu</span>
                        @elseif($pesanan->status_pesanan === 'diproses')
                            <span class="bg-blue-100 text-blue-700 px-2 py-1 rounded text-xs">Diproses</span>
                        @elseif($pesanan->status_pesanan === 'selesai')
                            <span class="bg-green-100 text-green-700 px-2 py-1 rounded text-xs">Selesai</span>
                        @elseif($pesanan->status_pesanan === 'dibayar')
                            <span class="bg-gray-100 text-gray-700 px-2 py-1 rounded text-xs">Dibayar</span>
                        @else
                            <span class="bg-gray-100 text-gray-700 px-2 py-1 rounded text-xs">{{ ucfirst($pesanan->status_pesanan) }}</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="p-8 text-center text-gray-500">
                        Belum ada pesanan hari ini
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Top Menu Chart
    const topMenuCtx = document.getElementById('topMenuChart').getContext('2d');
    const topMenuData = {!! json_encode($topMenus->map(function($item) {
    return [
        'name' => $item->menu->nama ?? 'Unknown',
        'count' => $item->total_terjual ?? 0
    ];
})) !!};

const labels = topMenuData.length ? topMenuData.map(i => i.name) : ['Tidak ada data'];
const data = topMenuData.length ? topMenuData.map(i => i.count) : [0];

    new Chart(topMenuCtx, {
        type: 'bar',
        data: {
            labels: topMenuData.map(item => item.name),
            datasets: [{
                label: 'Terjual',
                data: topMenuData.map(item => item.count),
                backgroundColor: '#22c55e',
                borderColor: '#16a34a',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            },
            plugins: {
                legend: {
                    display: false
                }
            }
        }
        
    });

    // Visitor Trend Chart
    const visitorCtx = document.getElementById('visitorChart').getContext('2d');
    const visitorData = {!! json_encode($trenPengunjung) !!};

    new Chart(visitorCtx, {
        type: 'line',
        data: {
            labels: visitorData.map(item => item.tanggal),
            datasets: [{
                label: 'Pengunjung',
                data: visitorData.map(item => item.jumlah),
                borderColor: 'rgba(59, 130, 246, 1)',
                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            },
            plugins: {
                legend: {
                    display: false
                }
            }
        }
    });
</script>
@endpush