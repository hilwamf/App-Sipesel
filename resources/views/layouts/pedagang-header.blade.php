<header class="bg-green-800 shadow-xl sticky top-0 z-50">
    <div class="border-b border-green-600/50 py-2 px-6">
        <div class="max-w-6xl mx-auto flex items-center justify-between">
            <span class="font-extrabold text-yellow-400 text-2xl tracking-widest drop-shadow-sm" style="font-family:'Montserrat',sans-serif;">SIPESEL</span>
            <span class="text-green-300 text-xs font-medium tracking-wide hidden sm:block">Sistem Informasi Pembayaran Pajak Pasar</span>
            <div class="flex items-center gap-3">
                @if(isset($showNotif) && $showNotif)
                <!-- Bell Notifikasi -->
                <div class="relative">
                    <button onclick="toggleNotif()" class="relative p-2 rounded-lg hover:bg-white/10 transition-all" title="Notifikasi">
                        <svg class="w-5 h-5 text-green-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                        </svg>
                        @if(isset($unread) && $unread > 0)
                        <span class="absolute -top-1 -right-1 w-5 h-5 bg-red-500 text-white text-xs font-bold rounded-full flex items-center justify-center">
                            {{ $unread > 9 ? '9+' : $unread }}
                        </span>
                        @endif
                    </button>
                    <div id="notifPanel" class="absolute right-0 top-12 w-80 bg-green-900/95 backdrop-blur-md border border-white/20 rounded-xl shadow-2xl overflow-hidden z-50" style="display:none;">
                        <div class="px-4 py-3 border-b border-white/10 flex items-center justify-between">
                            <p class="font-semibold text-sm text-white">Notifikasi</p>
                            <button onclick="toggleNotif()" class="text-white/50 hover:text-white text-xs">Tutup</button>
                        </div>
                        @if(isset($notifikasi) && $notifikasi->count() > 0)
                        <div class="max-h-72 overflow-y-auto">
                            @foreach($notifikasi as $n)
                            <div class="px-4 py-3 border-b border-white/5 hover:bg-white/5 transition-all {{ !$n->dibaca ? 'bg-yellow-500/10' : '' }}">
                                <div class="flex gap-3">
                                    <div class="w-8 h-8 rounded-full bg-yellow-500/30 flex items-center justify-center flex-shrink-0 mt-0.5">
                                        <svg class="w-4 h-4 text-yellow-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                                        </svg>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-xs text-white/90 leading-relaxed">{{ $n->pesan }}</p>
                                        <p class="text-xs text-white/40 mt-1">{{ $n->created_at->format('d M Y, H:i') }}</p>
                                    </div>
                                    @if(!$n->dibaca)
                                    <div class="w-2 h-2 bg-yellow-400 rounded-full mt-1.5 flex-shrink-0"></div>
                                    @endif
                                </div>
                            </div>
                            @endforeach
                        </div>
                        @else
                        <div class="px-4 py-8 text-center text-white/40 text-sm">Tidak ada notifikasi</div>
                        @endif
                    </div>
                </div>
                @endif
                <div class="w-8 h-8 rounded-full bg-yellow-400 flex items-center justify-center text-green-900 font-bold text-sm">
                    {{ strtoupper(substr($user->username, 0, 1)) }}
                </div>
                <span class="text-green-100 text-sm font-medium hidden sm:block">{{ $user->username }}</span>
            </div>
        </div>
    </div>
    <nav class="max-w-6xl mx-auto px-6 flex items-center gap-1 py-2">
        <a href="{{ route('pedagang.dashboard') }}" class="px-4 py-2 rounded-lg text-sm font-medium transition-all {{ request()->routeIs('pedagang.dashboard') ? 'bg-yellow-400/20 border border-yellow-400/50 text-yellow-300 font-semibold' : 'text-green-200 hover:bg-green-600/50 hover:text-white' }}">Dashboard</a>
        <a href="{{ route('pedagang.pembayaran') }}" class="px-4 py-2 rounded-lg text-sm font-medium transition-all {{ request()->routeIs('pedagang.pembayaran') ? 'bg-yellow-400/20 border border-yellow-400/50 text-yellow-300 font-semibold' : 'text-green-200 hover:bg-green-600/50 hover:text-white' }}">Pembayaran</a>
        <a href="{{ route('pedagang.riwayat') }}" class="px-4 py-2 rounded-lg text-sm font-medium transition-all {{ request()->routeIs('pedagang.riwayat') ? 'bg-yellow-400/20 border border-yellow-400/50 text-yellow-300 font-semibold' : 'text-green-200 hover:bg-green-600/50 hover:text-white' }}">Riwayat Bayar</a>
        <div class="ml-auto">
            <form action="{{ route('logout') }}" method="POST" class="inline">
                @csrf
                <button type="submit" class="px-4 py-2 rounded-lg bg-red-500/80 hover:bg-red-500 text-white text-sm font-semibold transition-all">Keluar</button>
            </form>
        </div>
    </nav>
</header>
