@php $totalPending = isset($totalPending) ? $totalPending : \App\Models\Transaksi::where('status','pending')->count(); @endphp
<header class="bg-green-800 shadow-xl sticky top-0 z-50">
    <div class="border-b border-green-600/50 py-2 px-6">
        <div class="max-w-7xl mx-auto flex items-center justify-between">
            <span class="font-extrabold text-yellow-400 text-2xl tracking-widest" style="font-family:'Montserrat',sans-serif;">SIPESEL</span>
            <span class="text-green-300 text-xs font-medium tracking-wide hidden sm:block">Sistem Informasi Pembayaran Pajak Pasar - ADMIN PANEL</span>
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-full bg-yellow-400 flex items-center justify-center text-green-900 font-bold text-sm">
                    {{ strtoupper(substr($user->username, 0, 1)) }}
                </div>
                <span class="text-green-100 text-sm font-medium hidden sm:block">{{ $user->username }}</span>
                <span class="px-2 py-1 bg-red-500/80 text-white text-xs font-bold rounded ml-2">ADMIN</span>
            </div>
        </div>
    </div>
    <nav class="max-w-7xl mx-auto px-6 flex items-center gap-1 py-2 overflow-x-auto">
        
@php $totalPending = $totalPending ?? \App\Models\Transaksi::where('status','pending')->count();
@endphp
@php
            $navItems = [
                ['route'=>'admin.dashboard','label'=>'Dashboard'],
                ['route'=>'admin.verifikasi','label'=>'Verifikasi','badge'=>$totalPending??0],
                ['route'=>'admin.users','label'=>'Users'],
                ['route'=>'admin.kios','label'=>'Kios'],
                ['route'=>'admin.laporan','label'=>'Laporan'],
                ['route'=>'admin.monitoring','label'=>'Monitoring'],
                ['route'=>'admin.setting','label'=>'Setting'],
            ];
        @endphp
        @foreach($navItems as $item)
        <a href="{{ route($item['route']) }}" class="px-4 py-2 rounded-lg text-sm font-medium transition-all whitespace-nowrap flex items-center gap-1 {{ request()->routeIs($item['route']) ? 'bg-yellow-400/20 border border-yellow-400/50 text-yellow-300 font-semibold' : 'text-green-200 hover:bg-green-600/50 hover:text-white' }}">
            {{ $item['label'] }}
            @if(isset($item['badge']) && $item['badge'] > 0)
            <span class="px-2 py-0.5 bg-red-500 text-white text-xs rounded-full">{{ $item['badge'] }}</span>
            @endif
        </a>
        @endforeach
        <div class="ml-auto">
            <form action="{{ route('logout') }}" method="POST" class="inline">@csrf
                <button type="submit" class="px-4 py-2 rounded-lg bg-red-500/80 hover:bg-red-500 text-white text-sm font-semibold transition-all whitespace-nowrap">Keluar</button>
            </form>
        </div>
    </nav>
</header>
