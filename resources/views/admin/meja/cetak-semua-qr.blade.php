<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Semua QR Code</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* Pengaturan Khusus untuk Print */
        @media print {
            @page { margin: 1cm; }
            body { background: white; }
            /* Memaksa elemen ini menjadi blok sebaris agar muat 2 kolom di kertas A4 */
            .print-grid { display: block !important; }
            .qr-card { 
                display: inline-block; 
                width: 46%; 
                margin: 1.5%; 
                page-break-inside: avoid; /* Mencegah kotak terpotong di ujung kertas */
                border: 2px dashed #e5e7eb; /* Garis putus-putus untuk panduan menggunting */
            }
        }
    </style>
</head>
<body class="bg-gray-100 min-h-screen p-6 md:p-10 print:p-0 print:bg-white">

    {{-- Header & Tombol (Akan disembunyikan saat di-print berkat print:hidden) --}}
    <div class="max-w-5xl mx-auto mb-8 flex flex-col sm:flex-row justify-between items-center print:hidden bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
        <div class="mb-4 sm:mb-0 text-center sm:text-left">
            <h1 class="text-2xl font-bold text-gray-800">Cetak Semua QR Code</h1>
            <p class="text-gray-500 mt-1">Total: <span class="font-bold text-orange-600">{{ count($qrData) }}</span> Meja siap dicetak.</p>
        </div>
        <div class="flex gap-3">
            <button onclick="window.close()" class="px-5 py-2.5 rounded-xl border border-gray-300 font-semibold text-gray-600 hover:bg-gray-50 transition-colors">Batal</button>
            <button onclick="window.print()" class="px-5 py-2.5 rounded-xl bg-orange-600 text-white font-semibold hover:bg-orange-700 shadow-lg shadow-orange-200 transition-all flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                Cetak Sekarang
            </button>
        </div>
    </div>

    {{-- Grid QR Code --}}
    <div class="max-w-5xl mx-auto grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 print-grid">
        @forelse($qrData as $data)
            <div class="qr-card bg-white p-6 rounded-3xl shadow-sm border border-gray-100 text-center print:shadow-none print:rounded-none">
                
                <div class="mb-4">
                    <h2 class="text-xl font-black text-gray-800">SCAN DI SINI</h2>
                    <p class="text-gray-500 text-xs font-medium mt-1">Untuk memesan menu</p>
                </div>

                <div class="inline-block p-3 bg-white border-4 border-orange-600 rounded-2xl mb-4">
                    {!! $data['qr_code'] !!}
                </div>

                <div>
                    <div class="text-xs font-bold text-gray-400 uppercase tracking-widest">Nomor Meja</div>
                    <div class="text-4xl font-black text-orange-600 mt-1">{{ $data['nomor_meja'] }}</div>
                </div>

            </div>
        @empty
            <div class="col-span-full text-center py-20 text-gray-500 print:hidden">
                Belum ada data meja untuk dicetak.
            </div>
        @endforelse
    </div>

</body>
</html>