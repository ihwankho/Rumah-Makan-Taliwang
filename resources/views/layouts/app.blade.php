<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>@yield('title', 'Taliwang App') - Taliwang</title>
  @vite('resources/css/app.css')
</head>
<body class="bg-gray-100 antialiased text-gray-800">
  <div class="max-w-lg mx-auto bg-gray-50 min-h-screen shadow-2xl relative overflow-x-hidden">

  {{-- Header Statis (Opsional jika semua halaman butuh header yang sama) --}}
  @yield('header')

  {{-- Konten Utama --}}
  <main>
    @yield('content')
  </main>
</div>
  {{-- Scripts khusus halaman tertentu --}}
  @stack('scripts')
</body>
</html>