<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0">
    <title>@yield('title', 'SIPESEL Admin')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&family=Montserrat:wght@700;800;900&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Poppins', sans-serif; background: #f0f4f8; }
        .sidebar { width: 240px; height: 100vh; position: sticky; top: 0; overflow-y: auto; flex-shrink: 0; background: #0f2417; transition: width .3s; }
        .sidebar.collapsed { width: 68px; }
        .sidebar.collapsed .nav-label,
        .sidebar.collapsed .sidebar-title,
        .sidebar.collapsed .user-info { display: none; }
        .sidebar.collapsed .nav-item { justify-content: center; }
        .sidebar.collapsed .logo-wrap { justify-content: center; }
        .main-content { flex: 1; min-width: 0; min-height: 100vh; transition: all .3s; overflow-y: auto; }
        .nav-item { display: flex; align-items: center; gap: 10px; padding: 10px 16px; border-radius: 10px; cursor: pointer; transition: all .2s; color: rgba(255,255,255,.6); font-size: 13px; font-weight: 500; text-decoration: none; }
        .nav-item:hover { background: rgba(255,255,255,.08); color: white; }
        .nav-item.active { background: #16a34a; color: white; }
        .nav-item .badge { background: #ef4444; color: white; font-size: 10px; font-weight: 700; padding: 1px 6px; border-radius: 99px; margin-left: auto; }
        .card-stat { background: white; border-radius: 16px; padding: 20px; box-shadow: 0 1px 3px rgba(0,0,0,.06); transition: all .2s; }
        .card-stat:hover { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(0,0,0,.1); }
        @media (max-width: 768px) { .sidebar { display: none; } .sidebar.mobile-open { display: flex; position: fixed; z-index: 100; } }
    </style>
    @yield('styles')
</head>
<body class="flex h-screen overflow-hidden">

@php
    $totalPendingNav = \App\Models\Transaksi::where('status','pending')->count();
    $isAdmin = auth()->user()->role === 'admin';
@endphp

{{-- SIDEBAR --}}
<aside id="sidebar" class="sidebar flex flex-col flex-shrink-0">
    {{-- Logo --}}
    <div class="logo-wrap flex items-center gap-3 px-4 py-5 border-b border-white/10">
        <div class="w-9 h-9 bg-green-500 rounded-xl flex items-center justify-center flex-shrink-0">
            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
        </div>
        <span class="sidebar-title font-black text-white text-lg tracking-tight" style="font-family:'Montserrat',sans-serif;">SIPESEL</span>
    </div>

    {{-- Nav --}}
    <nav class="flex-1 px-3 py-4 space-y-1 overflow-y-auto">
        @if($isAdmin)
        <p class="nav-label text-white/30 text-[10px] font-semibold uppercase tracking-widest px-3 mb-2">Menu Utama</p>
        <a href="{{ route('admin.dashboard') }}" class="nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/></svg>
            <span class="nav-label">Dashboard</span>
        </a>
        <a href="{{ route('admin.verifikasi') }}" class="nav-item {{ request()->routeIs('admin.verifikasi') ? 'active' : '' }}">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <span class="nav-label">Verifikasi</span>
            @if($totalPendingNav > 0)<span class="badge nav-label">{{ $totalPendingNav }}</span>@endif
        </a>
        <a href="{{ route('admin.laporan') }}" class="nav-item {{ request()->routeIs('admin.laporan') ? 'active' : '' }}">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            <span class="nav-label">Laporan</span>
        </a>

        <a href="{{ route('admin.berita') }}" class="nav-item {{ request()->routeIs('admin.berita') ? 'active' : '' }}">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/></svg>
            <span class="nav-label">Berita</span>
        </a>
        <p class="nav-label text-white/30 text-[10px] font-semibold uppercase tracking-widest px-3 mt-4 mb-2">Data Master</p>
        <a href="{{ route('admin.users') }}" class="nav-item {{ request()->routeIs('admin.users') ? 'active' : '' }}">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            <span class="nav-label">Pengguna</span>
        </a>
        <a href="{{ route('admin.kios') }}" class="nav-item {{ request()->routeIs('admin.kios') ? 'active' : '' }}">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
            <span class="nav-label">Kios</span>
        </a>
        <a href="{{ route('admin.setting') }}" class="nav-item {{ request()->routeIs('admin.setting') ? 'active' : '' }}">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            <span class="nav-label">Setting</span>
        </a>
        @else
        {{-- Pengawas nav --}}
        <p class="nav-label text-white/30 text-[10px] font-semibold uppercase tracking-widest px-3 mb-2">Menu</p>
        <a href="{{ route('pengawas.dashboard') }}" class="nav-item {{ request()->routeIs('pengawas.dashboard') ? 'active' : '' }}">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/></svg>
            <span class="nav-label">Dashboard</span>
        </a>
        <a href="{{ route('pengawas.monitoring') }}" class="nav-item {{ request()->routeIs('pengawas.monitoring') ? 'active' : '' }}">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
            <span class="nav-label">Monitoring</span>
        </a>
        <a href="{{ route('pengawas.laporan') }}" class="nav-item {{ request()->routeIs('pengawas.laporan') ? 'active' : '' }}">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            <span class="nav-label">Laporan</span>
        </a>
        @endif
    </nav>

    {{-- User + Logout --}}
    <div class="px-3 py-4 border-t border-white/10">
        <div class="flex items-center gap-3 px-2 py-2">
            <div class="w-8 h-8 rounded-full bg-green-500 flex items-center justify-center text-white font-bold text-sm flex-shrink-0">
                {{ strtoupper(substr(auth()->user()->username,0,1)) }}
            </div>
            <div class="user-info flex-1 min-w-0">
                <p class="text-white text-xs font-semibold truncate">{{ auth()->user()->username }}</p>
                <p class="text-white/40 text-[10px] truncate">{{ ucfirst(auth()->user()->role) }}</p>
            </div>
        </div>
        <form action="{{ route('logout') }}" method="POST" class="mt-2">@csrf
            <button type="submit" class="nav-item w-full text-red-400 hover:text-red-300 hover:bg-red-500/10">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                <span class="nav-label">Keluar</span>
            </button>
        </form>
    </div>
</aside>

{{-- MAIN CONTENT --}}
<div class="main-content flex flex-col min-h-screen">

    {{-- TOP BAR --}}
    <header class="bg-white border-b border-gray-100 px-6 py-3 flex items-center justify-between sticky top-0 z-40">
        <div class="flex items-center gap-3">
            <button id="sidebarToggle" class="p-2 rounded-lg hover:bg-gray-100 transition text-gray-500">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
            </button>
            <div>
                <h1 class="font-bold text-gray-800 text-base">@yield('page-title', 'Dashboard')</h1>
                <p class="text-gray-400 text-xs">@yield('page-sub', now()->locale('id')->isoFormat('dddd, D MMMM YYYY'))</p>
            </div>
        </div>
        <div class="flex items-center gap-3">
            @if($isAdmin && $totalPendingNav > 0)
            <a href="{{ route('admin.verifikasi') }}" class="flex items-center gap-2 bg-red-50 text-red-600 px-3 py-1.5 rounded-lg text-xs font-semibold hover:bg-red-100 transition">
                <span class="w-2 h-2 bg-red-500 rounded-full animate-pulse"></span>
                {{ $totalPendingNav }} Pending
            </a>
            @endif
            <a href="{{ route('profil.edit') }}" class="flex items-center gap-2 px-3 py-1.5 rounded-lg border border-gray-200 hover:bg-gray-50 transition">
                <div class="w-6 h-6 rounded-full bg-green-600 flex items-center justify-center text-white font-bold text-xs">{{ strtoupper(substr(auth()->user()->username,0,1)) }}</div>
                <span class="text-sm text-gray-700 hidden sm:block">{{ auth()->user()->username }}</span>
            </a>
        </div>
    </header>

    {{-- PAGE CONTENT --}}
    <main class="flex-1 p-6">
        @yield('content')
    </main>

    <footer class="bg-white border-t border-gray-100 text-center text-xs text-gray-400 py-3">
        &copy; 2026 <span class="text-green-700 font-bold">SIPESEL</span> — Pasar Wadungasri, Sidoarjo
    </footer>
</div>

<script>
const sidebar = document.getElementById('sidebar');
const toggle  = document.getElementById('sidebarToggle');
if(toggle && sidebar) {
    toggle.addEventListener('click', () => {
        sidebar.classList.toggle('collapsed');
        localStorage.setItem('sipesel_sidebar', sidebar.classList.contains('collapsed') ? '1' : '0');
    });
    if(localStorage.getItem('sipesel_sidebar') === '1') sidebar.classList.add('collapsed');
}
@yield('scripts')
</script>
</body>
</html>