<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Admin Dashboard') - Taliwang</title>
    @vite('resources/css/app.css')
</head>
<body class=" antialiased font-sans">

    <div class="flex h-screen overflow-hidden">
        <aside id="sidebar" class="bg-white text-white w-64 flex-shrink-0 hidden md:flex flex-col transition-all duration-300 z-20 border-r border-gray-200">
            <div class="w-full h-32 md:h-40 overflow-hidden bg-white flex items-center justify-center border-r border-gray-200">
    <img 
        src="{{ asset('images/Logoo.png') }}" 
        alt="Logo"
        class="h-24 w-auto object-contain"
    >
</div>
            <nav class="flex-1 overflow-y-auto py-4">
                @php
    $active = 'bg-orange-500 text-white shadow-md rounded-xl mx-3';
    $inactive = 'text-gray-700 hover:bg-orange-100 hover:text-orange-600 rounded-xl mx-3';
@endphp

<ul class="space-y-2 px-2">

    {{-- Dashboard --}}
    <li>
        <a href="{{ route('admin.admin.dashboard') }}"
           class="flex items-center gap-3 px-4 py-3 text-sm font-medium transition-all duration-200
           {{ request()->routeIs('admin.admin.dashboard') ? $active : $inactive }}">

            <img 
                src="{{ asset('images/icons/dashboard.svg') }}"
                alt="Dashboard"
                class="w-5 h-5 object-contain"
            >

            <span>Dashboard</span>
        </a>
    </li>

    {{-- Menu --}}
    <li>
        <a href="{{ route('admin.menu.index') }}"
           class="flex items-center gap-3 px-4 py-3 text-sm font-medium transition-all duration-200
           {{ request()->routeIs('admin.menu.*') ? $active : $inactive }}">

            <img 
                src="{{ asset('images/icons/menu.svg') }}"
                alt="Menu"
                class="w-5 h-5 object-contain"
            >

            <span>Menu</span>
        </a>
    </li>

    {{-- Kategori Menu --}}
    <li>
        <a href="{{ route('admin.kategori-menu.index') }}"
           class="flex items-center gap-3 px-4 py-3 text-sm font-medium transition-all duration-200
           {{ request()->routeIs('admin.kategori-menu.*') ? $active : $inactive }}">

            <img 
                src="{{ asset('images/icons/category.svg') }}"
                alt="Kategori Menu"
                class="w-5 h-5 object-contain"
            >

            <span>Kategori Menu</span>
        </a>
    </li>

    {{-- Daftar Meja --}}
    <li>
        <a href="{{ route('admin.meja.index') }}"
           class="flex items-center gap-3 px-4 py-3 text-sm font-medium transition-all duration-200
           {{ request()->routeIs('admin.meja.*') ? $active : $inactive }}">

            <img 
                src="{{ asset('images/icons/table.svg') }}"
                alt="Daftar Meja"
                class="w-5 h-5 object-contain"
            >

            <span>Daftar Meja</span>
        </a>
    </li>

    {{-- Laporan --}}
    <li>
        <a href="{{ route('admin.laporan.index') }}"
           class="flex items-center gap-3 px-4 py-3 text-sm font-medium transition-all duration-200
           {{ request()->routeIs('admin.laporan.*') ? $active : $inactive }}">

            <img 
                src="{{ asset('images/icons/report.svg') }}"
                alt="Laporan Penjualan"
                class="w-5 h-5 object-contain"
            >

            <span>Laporan Penjualan</span>
        </a>
    </li>

    {{-- Users --}}
    <li>
        <a href="{{ route('admin.users.index') }}"
           class="flex items-center gap-3 px-4 py-3 text-sm font-medium transition-all duration-200
           {{ request()->routeIs('admin.users.*') ? $active : $inactive }}">

            <img 
                src="{{ asset('images/icons/account.svg') }}"
                alt="Akun Pengguna"
                class="w-5 h-5 object-contain"
            >

            <span>Akun Pengguna</span>
        </a>
    </li>

</ul>
            </nav>

            
        </aside>

        {{-- AREA KANAN (Content) --}}
        <div class="flex-1 flex flex-col overflow-hidden relative">
            
            {{-- TOPBAR --}}
            <header class="h-16 bg-white shadow-sm flex items-center justify-between px-6 z-10 border-b border-gray-200">
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
                <div class="relative">
    
    {{-- Button Profile --}}
    <button 
        onclick="toggleProfileMenu()"
        class="flex items-center gap-3 focus:outline-none"
    >
        <div class="text-sm font-medium text-gray-600 hidden md:block">
            Halo, Admin
        </div>

        <div class="h-10 w-10 rounded-full bg-orange-500 flex items-center justify-center text-white font-bold shadow-md hover:scale-105 transition">
            A
        </div>
    </button>

    {{-- Dropdown --}}
    <div 
        id="profileMenu"
        class="hidden absolute right-0 mt-3 w-52 bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden z-50"
    >

        <div class="px-4 py-3 border-b">
            <p class="text-sm font-semibold text-gray-800">Admin</p>
        </div>

        <form method="POST" action="{{ route('logout') }}">
            @csrf

            <button 
                type="submit"
                class="w-full text-left px-4 py-3 text-sm text-red-500 hover:bg-red-50 transition"
            >
                Logout
            </button>
        </form>

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
    function toggleProfileMenu() {
        const menu = document.getElementById('profileMenu');
        menu.classList.toggle('hidden');
    }

    window.addEventListener('click', function(e) {
        const profileMenu = document.getElementById('profileMenu');

        if (!e.target.closest('.relative')) {
            profileMenu.classList.add('hidden');
        }
    });
    </script>

    @stack('scripts')
</body>
</html>