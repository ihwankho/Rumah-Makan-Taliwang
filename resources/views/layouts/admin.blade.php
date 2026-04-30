<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Admin Dashboard') - Taliwang</title>
    @vite('resources/css/app.css')
</head>
<body class="bg-gray-100 text-gray-800 antialiased font-sans">

    <div class="flex h-screen overflow-hidden">
        <aside id="sidebar" class="bg-orange-600 text-white w-64 flex-shrink-0 hidden md:flex flex-col transition-all duration-300 z-20">
            <div class="h-16 flex items-center justify-center border-b border-slate-700">
                <span class="text-xl font-bold tracking-wider uppercase">Admin Panel</span>
            </div>

            <nav class="flex-1 overflow-y-auto py-4">
                <ul class="space-y-1">
                    <li>
                        <a href="{{ route('admin.admin.dashboard') }}" class="block px-6 py-3 text-sm font-medium text-white hover:bg-slate-700 hover:text-white transition-colors">
                            Dashboard
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.menu.index') }}" class="block px-6 py-3 text-sm font-medium text-white hover:bg-slate-700 hover:text-white transition-colors">
                            Menu
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.kategori-menu.index') }}" class="block px-6 py-3 text-sm font-medium text-white hover:bg-slate-700 hover:text-white transition-colors">
                            Kategori Menu
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.meja.index') }}" class="block px-6 py-3 text-sm font-medium text-white hover:bg-slate-700 hover:text-white transition-colors">
                            Daftar Meja
                        </a>
                    </li>
                     <li>
                        <a href="{{ route('admin.laporan.index') }}" class="block px-6 py-3 text-sm font-medium text-white hover:bg-slate-700 hover:text-white transition-colors">
                            Laporan Penjualan
                        </a>
                    </li>
                     <li>
                        <a href="{{ route('admin.users.index') }}" class="block px-6 py-3 text-sm font-medium text-white hover:bg-slate-700 hover:text-white transition-colors">
                            Akun Pengguna
                        </a>
                    </li>
                </ul>
            </nav>

            <div class="p-4 border-t border-slate-700">
                {{-- Form Logout Dummy --}}
                <form method="POST" action="#">
                    @csrf
                    <button type="submit" class="w-full bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded transition-colors text-sm">
                        Logout
                    </button>
                </form>
            </div>
        </aside>

        {{-- AREA KANAN (Content) --}}
        <div class="flex-1 flex flex-col overflow-hidden relative">
            
            {{-- TOPBAR --}}
            <header class="h-16 bg-white shadow-sm flex items-center justify-between px-6 z-10">
                {{-- Tombol Hamburger untuk memunculkan sidebar di layar HP --}}
                <button onclick="toggleSidebar()" class="md:hidden text-gray-500 hover:text-gray-700 focus:outline-none">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>

                {{-- Judul Halaman Aktif (Bisa dinamis pakai yield) --}}
                <div class="font-semibold text-lg text-gray-700 hidden md:block">
                    @yield('header_title', 'Dashboard')
                </div>

                {{-- Info Admin --}}
                <div class="flex items-center gap-3">
                    <div class="text-sm font-medium text-gray-600">Halo, Admin</div>
                    <div class="h-8 w-8 rounded-full bg-orange-600 flex items-center justify-center text-white font-bold">
                        A
                    </div>
                </div>
            </header>

            {{-- MAIN CONTENT AREA --}}
            <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-50 p-4 md:p-6">
                @yield('content')
            </main>

        </div>

        {{-- Overlay Hitam untuk HP (ketika sidebar terbuka) --}}
        <div id="sidebar-overlay" onclick="toggleSidebar()" class="fixed inset-0 bg-black bg-opacity-50 z-10 hidden md:hidden"></div>

    </div>

    {{-- Script untuk Toggle Sidebar di Mode Mobile --}}
    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebar-overlay');
            
            sidebar.classList.toggle('hidden');
            sidebar.classList.toggle('absolute'); // Agar di HP sidebar melayang
            sidebar.classList.toggle('inset-y-0');
            sidebar.classList.toggle('left-0');
            
            overlay.classList.toggle('hidden');
        }
    </script>

    @stack('scripts')
</body>
</html>