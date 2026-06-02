@extends('layouts.admin')

@section('title', 'Dashboard')
@section('header_title', 'Selamat Datang di Dashboard Admin')

@push('styles')
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet">
<style>
    body { font-family: 'Plus Jakarta Sans', sans-serif; }
    .font-mono-num { font-family: 'DM Mono', monospace; }
    .kpi-card { transition: transform .2s ease, box-shadow .2s ease; }
    .kpi-card:hover { transform: translateY(-3px); box-shadow: 0 12px 32px -8px rgba(0,0,0,.1); }
    .status-dot::before {
        content: '';
        display: inline-block;
        width: 6px; height: 6px;
        border-radius: 50%;
        background: currentColor;
        margin-right: 5px;
        vertical-align: middle;
    }
    .chart-container { position: relative; height: 220px; }
</style>
@endpush

@section('content')

{{-- ── PAGE HEADER ── --}}
<div class="flex items-end justify-between mb-7">
    <div>
        <p class="text-xs font-semibold tracking-widest text-orange-500 uppercase mb-1">Restoran Dashboard</p>
        <h1 class="text-2xl font-extrabold text-gray-900 tracking-tight">Overview Hari Ini</h1>
        <p class="text-sm text-gray-400 mt-0.5">{{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}</p>
    </div>
</div>

{{-- ── KPI CARDS ── --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">

    {{-- Pesanan Baru --}}
    <div class="kpi-card bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
        <div class="flex items-center justify-between mb-4">
            <div class="w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center">
                <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
            </div>
            <span class="text-xs font-semibold text-blue-500 bg-blue-50 px-2.5 py-1 rounded-full">Hari ini</span>
        </div>
        <p class="text-xs text-gray-400 font-medium uppercase tracking-wider mb-1">Pesanan Baru</p>
        <p class="text-3xl font-extrabold text-gray-900 font-mono-num">{{ $pesananBaru }}</p>
    </div>

    {{-- Meja Terisi --}}
    <div class="kpi-card bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
        <div class="flex items-center justify-between mb-4">
            <div class="w-10 h-10 rounded-xl bg-amber-50 flex items-center justify-center">
                <svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
            </div>
            <span class="text-xs font-semibold text-amber-500 bg-amber-50 px-2.5 py-1 rounded-full">{{ $totalMeja }} total</span>
        </div>
        <p class="text-xs text-gray-400 font-medium uppercase tracking-wider mb-1">Meja Terisi</p>
        <p class="text-3xl font-extrabold text-gray-900 font-mono-num">
            {{ $mejaTerisi }}<span class="text-base font-medium text-gray-300 ml-1">/ {{ $totalMeja }}</span>
        </p>
    </div>

    {{-- Pendapatan --}}
    <div class="kpi-card bg-gradient-to-br from-orange-500 to-amber-400 rounded-2xl shadow-sm shadow-orange-100 p-5 text-white">
        <div class="flex items-center justify-between mb-4">
            <div class="w-10 h-10 rounded-xl bg-white/20 flex items-center justify-center">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                </svg>
            </div>
            <span class="text-xs font-semibold bg-white/20 px-2.5 py-1 rounded-full">Hari ini</span>
        </div>
        <p class="text-xs font-medium uppercase tracking-wider text-white/70 mb-1">Pendapatan</p>
        <p class="text-xl font-extrabold font-mono-num leading-tight">
            Rp {{ number_format($pendapatanHariIni, 0, ',', '.') }}
        </p>
    </div>

    {{-- Perlu Perhatian --}}
    <div class="kpi-card bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
        <div class="flex items-center justify-between mb-4">
            <div class="w-10 h-10 rounded-xl bg-red-50 flex items-center justify-center">
                <svg class="w-5 h-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                </svg>
            </div>
            <span class="text-xs font-semibold text-red-400 bg-red-50 px-2.5 py-1 rounded-full">Perlu Aksi</span>
        </div>
        <p class="text-xs text-gray-400 font-medium uppercase tracking-wider mb-1">Menu Tidak Aktif</p>
        <p class="text-3xl font-extrabold text-red-500 font-mono-num">{{ $menuPerluPerhatian }}</p>
    </div>

</div>

{{-- ── CHARTS ── --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-6">

    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
        <div class="flex items-center justify-between mb-5">
            <div>
                <h3 class="font-bold text-gray-800 text-sm">Top Menu</h3>
                <p class="text-xs text-gray-400 mt-0.5">Paling banyak terjual</p>
            </div>
            <span class="text-xs text-gray-400 bg-gray-50 border border-gray-100 px-3 py-1.5 rounded-full font-medium">Bulan Ini</span>
        </div>
        <div class="chart-container">
            <canvas id="topMenuChart"></canvas>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
        <div class="flex items-center justify-between mb-5">
            <div>
                <h3 class="font-bold text-gray-800 text-sm">Tren Pengunjung</h3>
                <p class="text-xs text-gray-400 mt-0.5">Jumlah pengunjung harian</p>
            </div>
            <span class="text-xs text-gray-400 bg-gray-50 border border-gray-100 px-3 py-1.5 rounded-full font-medium">7 Hari</span>
        </div>
        <div class="chart-container">
            <canvas id="visitorChart"></canvas>
        </div>
    </div>

</div>

{{-- ── ORDER TABLE ── --}}
<div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
        <div>
            <h3 class="font-bold text-gray-800 text-sm">Pesanan Masuk</h3>
            <p class="text-xs text-gray-400 mt-0.5">Daftar pesanan hari ini</p>
        </div>
        @if(!isset($showAll) || !$showAll)
            <a href="{{ route('admin.admin.dashboard', ['all' => 1]) }}"
               class="text-xs font-semibold text-orange-500 bg-orange-50 hover:bg-orange-100 transition-colors px-4 py-2 rounded-xl">
                Lihat Semua →
            </a>
        @else
            <a href="{{ route('admin.admin.dashboard') }}"
               class="text-xs font-semibold text-orange-500 bg-orange-50 hover:bg-orange-100 transition-colors px-4 py-2 rounded-xl">
                ← Terbaru
            </a>
        @endif
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-gray-50/70">
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Waktu</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Meja</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Pelanggan</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Tipe</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Total</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($pesananMasuk as $pesanan)
                <tr class="hover:bg-gray-50/50 transition-colors">
                    <td class="px-6 py-4 font-mono-num text-xs text-gray-400 font-medium">
                        {{ $pesanan->created_at->format('H:i') }}
                    </td>
                    <td class="px-6 py-4">
                        <span class="inline-flex items-center justify-center w-8 h-8 rounded-xl bg-orange-50 border border-orange-100 text-orange-600 font-bold font-mono-num text-xs">
                            {{ $pesanan->meja->nomor_meja ?? '-' }}
                        </span>
                    </td>
                    <td class="px-6 py-4 font-semibold text-gray-700">
                        {{ $pesanan->pelanggan->nama_pelanggan ?? '-' }}
                    </td>
                    <td class="px-6 py-4">
                        @if($pesanan->tipe_pesanan === 'bungkus')
                            <span class="inline-flex items-center gap-1.5 text-xs font-semibold text-sky-600 bg-sky-50 border border-sky-100 px-2.5 py-1 rounded-lg">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                                Bungkus
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1.5 text-xs font-semibold text-emerald-600 bg-emerald-50 border border-emerald-100 px-2.5 py-1 rounded-lg">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                                Makan Ditempat
                            </span>
                        @endif
                    </td>
                    <td class="px-6 py-4 font-mono-num text-sm font-semibold text-gray-800">
                        Rp {{ number_format($pesanan->total_harga, 0, ',', '.') }}
                    </td>
                    <td class="px-6 py-4">
                        @if($pesanan->status_pesanan === 'menunggu')
                            <span class="status-dot inline-flex items-center text-xs font-semibold text-amber-600 bg-amber-50 border border-amber-100 px-3 py-1.5 rounded-full">Menunggu</span>
                        @elseif($pesanan->status_pesanan === 'diproses')
                            <span class="status-dot inline-flex items-center text-xs font-semibold text-blue-600 bg-blue-50 border border-blue-100 px-3 py-1.5 rounded-full">Diproses</span>
                        @elseif($pesanan->status_pesanan === 'selesai')
                            <span class="status-dot inline-flex items-center text-xs font-semibold text-emerald-600 bg-emerald-50 border border-emerald-100 px-3 py-1.5 rounded-full">Selesai</span>
                        @elseif($pesanan->status_pesanan === 'dibayar')
                            <span class="status-dot inline-flex items-center text-xs font-semibold text-gray-500 bg-gray-50 border border-gray-100 px-3 py-1.5 rounded-full">Dibayar</span>
                        @else
                            <span class="status-dot inline-flex items-center text-xs font-semibold text-gray-500 bg-gray-50 border border-gray-100 px-3 py-1.5 rounded-full">{{ ucfirst($pesanan->status_pesanan) }}</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-16 text-center">
                        <div class="flex flex-col items-center gap-3">
                            <div class="w-12 h-12 rounded-2xl bg-gray-50 flex items-center justify-center">
                                <svg class="w-6 h-6 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2"/>
                                </svg>
                            </div>
                            <p class="text-sm text-gray-400 font-medium">Belum ada pesanan hari ini</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    Chart.defaults.font.family = "'Plus Jakarta Sans', sans-serif";
    Chart.defaults.font.size = 11;
    Chart.defaults.color = '#9ca3af';

    // ── TOP MENU CHART ──
    const topMenuCtx = document.getElementById('topMenuChart').getContext('2d');
    const topMenuData = {!! json_encode($topMenus->map(function($item) {
        return ['name' => $item->menu->nama ?? 'Unknown', 'count' => $item->total_terjual ?? 0];
    })) !!};

    new Chart(topMenuCtx, {
        type: 'bar',
        data: {
            labels: topMenuData.length ? topMenuData.map(i => i.name) : ['Tidak ada data'],
            datasets: [{
                data: topMenuData.length ? topMenuData.map(i => i.count) : [0],
                backgroundColor: topMenuData.map((_, idx) => `rgba(249,115,22,${Math.max(0.2, 1 - idx * 0.15)})`),
                borderRadius: 8,
                borderSkipped: false,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                x: { grid: { display: false }, border: { display: false } },
                y: {
                    beginAtZero: true,
                    grid: { color: '#f3f4f6' },
                    border: { display: false },
                    ticks: { stepSize: 1 }
                }
            },
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#1f2937',
                    padding: 10,
                    cornerRadius: 10,
                    titleColor: '#f9fafb',
                    bodyColor: '#9ca3af',
                    callbacks: { label: ctx => ` ${ctx.parsed.y} terjual` }
                }
            }
        }
    });

    // ── VISITOR TREND CHART ──
    const visitorCtx = document.getElementById('visitorChart').getContext('2d');
    const visitorData = {!! json_encode($trenPengunjung) !!};

    const grad = visitorCtx.createLinearGradient(0, 0, 0, 220);
    grad.addColorStop(0, 'rgba(249,115,22,0.15)');
    grad.addColorStop(1, 'rgba(249,115,22,0)');

    new Chart(visitorCtx, {
        type: 'line',
        data: {
            labels: visitorData.map(i => i.tanggal),
            datasets: [{
                data: visitorData.map(i => i.jumlah),
                borderColor: '#f97316',
                backgroundColor: grad,
                tension: 0.4,
                fill: true,
                pointBackgroundColor: '#fff',
                pointBorderColor: '#f97316',
                pointBorderWidth: 2.5,
                pointRadius: 4,
                pointHoverRadius: 6,
                borderWidth: 2.5,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                x: { grid: { display: false }, border: { display: false } },
                y: {
                    beginAtZero: true,
                    grid: { color: '#f3f4f6' },
                    border: { display: false },
                    ticks: { stepSize: 1 }
                }
            },
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#1f2937',
                    padding: 10,
                    cornerRadius: 10,
                    titleColor: '#f9fafb',
                    bodyColor: '#9ca3af',
                    callbacks: { label: ctx => ` ${ctx.parsed.y} pengunjung` }
                }
            }
        }
    });
</script>
@endpush