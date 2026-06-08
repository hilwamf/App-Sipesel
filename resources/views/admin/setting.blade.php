@extends('layouts.sidebar-admin')
@section('title','Setting Sistem - SIPESEL')
@section('page-title','Setting Sistem')
@section('page-sub','Konfigurasi parameter sistem SIPESEL')

@section('content')
@if(session('success'))<div class="bg-green-50 border border-green-200 text-green-800 rounded-xl px-5 py-3 mb-5 text-sm flex items-center gap-2"><svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>{{ session('success') }}</div>@endif

<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
    <div class="bg-white rounded-2xl p-4 shadow-sm border border-gray-100 text-center"><p class="text-2xl font-black text-gray-800">{{ $sysStats['total_user'] }}</p><p class="text-xs text-gray-400 mt-1">Total User</p></div>
    <div class="bg-white rounded-2xl p-4 shadow-sm border border-gray-100 text-center"><p class="text-2xl font-black text-gray-800">{{ $sysStats['total_kios'] }}</p><p class="text-xs text-gray-400 mt-1">Total Kios</p></div>
    <div class="bg-white rounded-2xl p-4 shadow-sm border border-gray-100 text-center"><p class="text-2xl font-black text-gray-800">{{ $sysStats['total_transaksi'] }}</p><p class="text-xs text-gray-400 mt-1">Total Transaksi</p></div>
    <div class="bg-white rounded-2xl p-4 shadow-sm border border-yellow-100 text-center"><p class="text-2xl font-black text-yellow-600">{{ $sysStats['total_pending'] }}</p><p class="text-xs text-gray-400 mt-1">Pending</p></div>
</div>

<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden mb-5">
    <div class="px-5 py-4 border-b border-gray-100"><h2 class="font-bold text-gray-800">Konfigurasi Parameter</h2><p class="text-xs text-gray-400">Edit nilai lalu klik Simpan Semua</p></div>
    @if($settings->count() > 0)
    <form method="POST" action="{{ route('admin.save-setting') }}">@csrf
        <div class="divide-y divide-gray-50">
            @foreach($settings as $s)
            <div class="px-5 py-3 flex items-center gap-4 flex-wrap">
                <div class="flex-1 min-w-40"><p class="font-mono text-sm font-semibold text-green-700">{{ $s->nama_setting }}</p><p class="text-xs text-gray-400">{{ $s->deskripsi ?? '' }}</p></div>
                <div class="flex items-center gap-2">
                    <input type="text" name="settings[{{ $s->nama_setting }}]" value="{{ $s->nilai }}" class="px-4 py-2 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-green-500 w-40">
                    <form method="POST" action="{{ route('admin.hapus-setting') }}" class="inline" onsubmit="return confirm('Hapus?')">@csrf<input type="hidden" name="id_setting" value="{{ $s->id_setting }}"><button class="px-3 py-2 bg-red-50 hover:bg-red-100 text-red-600 rounded-xl text-xs font-semibold">Hapus</button></form>
                </div>
            </div>
            @endforeach
        </div>
        <div class="px-5 py-4 bg-gray-50 border-t border-gray-100"><button type="submit" class="px-5 py-2.5 bg-green-700 hover:bg-green-800 text-white font-semibold rounded-xl text-sm">💾 Simpan Semua</button></div>
    </form>
    @else
    <div class="px-5 py-10 text-center text-gray-400 text-sm">Belum ada setting.</div>
    @endif
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100"><h2 class="font-bold text-gray-800">Tambah Setting Baru</h2></div>
        <div class="px-5 py-4">
            <form method="POST" action="{{ route('admin.tambah-setting') }}" class="space-y-3">@csrf
                <input type="text" name="nama_setting" required placeholder="Nama setting (key)" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-green-500">
                <input type="text" name="nilai" required placeholder="Nilai" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-green-500">
                <input type="text" name="deskripsi" placeholder="Deskripsi (opsional)" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-green-500">
                <button type="submit" class="w-full py-2.5 bg-green-700 hover:bg-green-800 text-white font-semibold rounded-xl text-sm">Tambah Setting</button>
            </form>
        </div>
    </div>
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100"><h2 class="font-bold text-gray-800">Utilitas Sistem</h2></div>
        <div class="px-5 py-4 space-y-3">
            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-xl">
                <div><p class="font-semibold text-gray-800 text-sm">Generate Tagihan</p><p class="text-xs text-gray-400">Bulan {{ now()->locale('id')->isoFormat('MMMM YYYY') }}</p></div>
                <form method="POST" action="{{ route('admin.generate-tagihan') }}">@csrf<button onclick="return confirm('Generate?')" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-xl text-xs">Generate</button></form>
            </div>
            <div class="p-3 bg-gray-50 rounded-xl text-xs text-gray-500 space-y-1">
                <div class="flex justify-between"><span>PHP:</span><span>{{ phpversion() }}</span></div>
                <div class="flex justify-between"><span>Server Time:</span><span>{{ now()->format('d M Y H:i') }}</span></div>
                <div class="flex justify-between"><span>App:</span><span>SIPESEL v1.0</span></div>
            </div>
        </div>
    </div>
</div>
@endsection