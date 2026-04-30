<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak QR - Meja {{ $meja->nomor_meja }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* CSS khusus saat print dijalankan */
        @media print {
            @page { margin: 0; }
            body { margin: 1.5cm; }
        }
    </style>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen print:bg-white print:items-start">

    {{-- Kotak Area QR --}}
    <div class="bg-white p-10 rounded-3xl shadow-xl max-w-sm w-full text-center print:shadow-none border border-gray-100 print:border-none print:p-0">
        
        <div class="mb-6">
            <h1 class="text-3xl font-black text-gray-800">SCAN DI SINI</h1>
            <p class="text-gray-500 font-medium mt-1">Untuk memesan menu</p>
        </div>

        {{-- Menampilkan SVG QR Code --}}
        <div class="inline-block p-4 bg-white border-4 border-orange-600 rounded-2xl mb-6">
            {!! $qrCode !!}
        </div>

        <div>
            <div class="text-sm font-bold text-gray-400 uppercase tracking-widest">Nomor Meja</div>
            <div class="text-5xl font-black text-orange-600 mt-1">{{ $meja->nomor_meja }}</div>
        </div>

        {{-- Tombol ini akan hilang saat diprint --}}
        <div class="mt-10 print:hidden flex gap-3 justify-center">
            <button onclick="window.close()" class="px-5 py-2.5 rounded-xl border border-gray-300 font-semibold text-gray-600 hover:bg-gray-50 transition-colors">
                Batal
            </button>
            <button onclick="window.print()" class="px-5 py-2.5 rounded-xl bg-orange-600 text-white font-semibold hover:bg-orange-700 shadow-lg shadow-orange-200 transition-all">
                Cetak Sekarang
            </button>
        </div>
    </div>

    {{-- Script untuk otomatis memunculkan dialog print saat halaman dibuka --}}
    <script>
        window.onload = function() {
            // Uncomment baris di bawah ini jika ingin dialog print langsung otomatis muncul
            // window.print();
        }
    </script>
</body>
</html>