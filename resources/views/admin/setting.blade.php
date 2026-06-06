<!DOCTYPE html>
<html lang="id">
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0"><title>Setting Sistem - SIPESEL</title>
<script src="https://cdn.tailwindcss.com"></script>
<link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@700;800&family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>body{font-family:'Poppins',sans-serif;}</style>
</head>
<body style="background-image:url('{{ asset('images/kios2.jpg') }}');" class="relative min-h-screen text-white bg-cover bg-center">
<div class="absolute inset-0 bg-black/40"></div>
@include('layouts.admin-header')

<section class="relative z-10 px-6 md:px-20 py-8 min-h-screen">
<div class="max-w-5xl mx-auto">
    <div class="mb-6"><h1 class="text-3xl font-bold">Setting Sistem</h1><p class="text-sm opacity-80">Konfigurasi parameter sistem SIPESEL</p></div>

    @if(session('success'))<div class="bg-green-500/30 border border-green-400 text-green-200 px-4 py-3 rounded-lg mb-4">✅ {{ session('success') }}</div>@endif
    @if(session('error'))<div class="bg-red-500/30 border border-red-400 text-red-200 px-4 py-3 rounded-lg mb-4">❌ {{ session('error') }}</div>@endif

    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white/10 backdrop-blur-md rounded-xl p-4 border border-white/20 text-center">
            <p class="text-2xl font-bold">{{ $sysStats['total_user'] }}</p><p class="text-xs opacity-60 mt-1">Total User</p>
        </div>
        <div class="bg-white/10 backdrop-blur-md rounded-xl p-4 border border-white/20 text-center">
            <p class="text-2xl font-bold">{{ $sysStats['total_kios'] }}</p><p class="text-xs opacity-60 mt-1">Total Kios</p>
        </div>
        <div class="bg-white/10 backdrop-blur-md rounded-xl p-4 border border-white/20 text-center">
            <p class="text-2xl font-bold">{{ $sysStats['total_transaksi'] }}</p><p class="text-xs opacity-60 mt-1">Total Transaksi</p>
        </div>
        <div class="bg-yellow-500/20 backdrop-blur-md rounded-xl p-4 border border-yellow-400/30 text-center">
            <p class="text-2xl font-bold text-yellow-300">{{ $sysStats['total_pending'] }}</p><p class="text-xs opacity-60 mt-1">Pending</p>
        </div>
    </div>

    <div class="bg-white/10 backdrop-blur-md rounded-xl border border-white/20 mb-6">
        <div class="px-6 py-4 border-b border-white/10">
            <h2 class="font-bold text-lg flex items-center gap-2">
                <svg class="w-5 h-5 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                Konfigurasi Parameter
            </h2>
            <p class="text-xs opacity-60 mt-1">Edit nilai dan klik Simpan Semua</p>
        </div>
        @if($settings->count() > 0)
        <form method="POST" action="{{ route('admin.save-setting') }}">@csrf
            <input type="hidden" name="action" value="save_setting">
            <div class="divide-y divide-white/10">
                @foreach($settings as $s)
                <div class="px-6 py-4 flex items-center gap-4 flex-wrap">
                    <div class="flex-1 min-w-48">
                        <p class="font-mono text-yellow-300 text-sm">{{ $s->nama_setting }}</p>
                        <p class="text-xs opacity-60 mt-0.5">{{ $s->deskripsi ?? '' }}</p>
                    </div>
                    <div class="flex items-center gap-2 flex-1 min-w-40">
                        <input type="text" name="settings[{{ $s->nama_setting }}]" value="{{ $s->nilai }}"
                            class="w-full bg-white/10 border border-white/30 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-yellow-400">
                        <form method="POST" action="{{ route('admin.hapus-setting') }}" class="inline" onsubmit="return confirm('Hapus setting ini?')">
                            @csrf
                            <input type="hidden" name="id_setting" value="{{ $s->id_setting }}">
                            <button type="submit" class="bg-red-500/30 hover:bg-red-500/50 text-red-300 px-2 py-2 rounded-lg text-xs transition-all whitespace-nowrap">Hapus</button>
                        </form>
                    </div>
                    <p class="text-xs opacity-40 w-full md:w-auto">Diperbarui: {{ $s->updated_at ? $s->updated_at->format('d M Y H:i') : '-' }}</p>
                </div>
                @endforeach
            </div>
            <div class="px-6 py-4 border-t border-white/10">
                <button type="submit" class="bg-yellow-400 hover:bg-yellow-300 text-green-900 font-bold px-6 py-2 rounded-lg transition-all">💾 Simpan Semua Setting</button>
            </div>
        </form>
        @else
        <div class="px-6 py-8 text-center opacity-50">Belum ada setting. Tambahkan melalui form di bawah.</div>
        @endif
    </div>

    <div class="bg-white/10 backdrop-blur-md rounded-xl border border-white/20 mb-6">
        <div class="px-6 py-4 border-b border-white/10"><h2 class="font-bold text-lg">➕ Tambah Setting Baru</h2></div>
        <div class="px-6 py-4">
            <form method="POST" action="{{ route('admin.tambah-setting') }}" class="grid grid-cols-1 md:grid-cols-3 gap-3">
                @csrf
                <div><label class="text-xs opacity-70 block mb-1">Nama Setting (key)</label>
                    <input type="text" name="nama_setting" required placeholder="cth: max_upload_mb" class="w-full bg-white/10 border border-white/30 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-yellow-400">
                </div>
                <div><label class="text-xs opacity-70 block mb-1">Nilai</label>
                    <input type="text" name="nilai" required placeholder="cth: 5" class="w-full bg-white/10 border border-white/30 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-yellow-400">
                </div>
                <div><label class="text-xs opacity-70 block mb-1">Deskripsi</label>
                    <input type="text" name="deskripsi" placeholder="cth: Ukuran max upload file (MB)" class="w-full bg-white/10 border border-white/30 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-yellow-400">
                </div>
                <div class="md:col-span-3">
                    <button type="submit" class="bg-green-500/80 hover:bg-green-500 text-white font-semibold px-6 py-2 rounded-lg text-sm transition-all">Tambah Setting</button>
                </div>
            </form>
        </div>
    </div>

    <div class="bg-white/10 backdrop-blur-md rounded-xl border border-white/20">
        <div class="px-6 py-4 border-b border-white/10"><h2 class="font-bold text-lg">🔧 Utilitas Sistem</h2></div>
        <div class="px-6 py-4 space-y-4">
            <div class="bg-white/5 rounded-lg p-4 border border-white/10">
                <div class="flex items-center justify-between flex-wrap gap-3">
                    <div>
                        <h3 class="font-semibold">Generate Tagihan Bulanan</h3>
                        <p class="text-xs opacity-60 mt-1">Buat tagihan otomatis untuk semua pedagang aktif di bulan {{ now()->locale('id')->isoFormat('MMMM YYYY') }}.</p>
                    </div>
                    <form method="POST" action="{{ route('admin.generate-tagihan') }}">@csrf
                        <button type="submit" onclick="return confirm('Generate tagihan untuk bulan {{ now()->locale('id')->isoFormat('MMMM YYYY') }}?')" class="bg-blue-500/80 hover:bg-blue-500 text-white font-semibold px-4 py-2 rounded-lg text-sm transition-all">Generate Tagihan</button>
                    </form>
                </div>
            </div>
            <div class="bg-white/5 rounded-lg p-4 border border-white/10">
                <h3 class="font-semibold mb-2">Info Aplikasi</h3>
                <div class="grid grid-cols-2 gap-2 text-xs opacity-70">
                    <span>Versi PHP:</span><span>{{ phpversion() }}</span>
                    <span>Server Time:</span><span>{{ now()->format('d M Y H:i:s') }}</span>
                    <span>Aplikasi:</span><span>SIPESEL v1.0 (Laravel)</span>
                    <span>Environment:</span><span>{{ app()->environment() }}</span>
                </div>
            </div>
        </div>
    </div>
</div>
</section>
<footer class="bg-green-800 text-green-300 text-center text-xs py-4 mt-10 relative z-10">&copy; 2026 <span class="text-yellow-400 font-bold">SIPESEL</span> — Admin Panel.</footer>
</body>
</html>
