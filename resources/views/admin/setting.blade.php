@extends('layouts.sidebar-admin')
@section('title','Setting Sistem - SIPESEL')
@section('page-title','Setting Sistem')
@section('page-sub','Konfigurasi tarif dan parameter sistem')

@section('content')
@if(session('success'))
<div class="bg-green-50 border border-green-200 text-green-800 rounded-xl px-5 py-3 mb-5 flex items-center gap-2 text-sm">
    <svg class="w-4 h-4 text-green-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
    {{ session('success') }}
</div>
@endif

@php
$tarifNow = \App\Models\Setting::whereIn('nama_setting',['harian_rate','mingguan_rate','bulanan_rate'])->pluck('nilai','nama_setting');
$harian   = (int)($tarifNow['harian_rate']   ?? 5000);
$mingguan = (int)($tarifNow['mingguan_rate'] ?? 35000);
$bulanan  = (int)($tarifNow['bulanan_rate']  ?? 150000);
$settingLain = $settings->whereNotIn('nama_setting',['harian_rate','mingguan_rate','bulanan_rate']);
@endphp

<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
    <div class="bg-white rounded-2xl p-4 shadow-sm border border-gray-100 text-center">
        <p class="text-2xl font-black text-gray-800">{{ $sysStats['total_user'] }}</p>
        <p class="text-xs text-gray-400 mt-1">Total User</p>
    </div>
    <div class="bg-white rounded-2xl p-4 shadow-sm border border-gray-100 text-center">
        <p class="text-2xl font-black text-gray-800">{{ $sysStats['total_kios'] }}</p>
        <p class="text-xs text-gray-400 mt-1">Total Kios</p>
    </div>
    <div class="bg-white rounded-2xl p-4 shadow-sm border border-gray-100 text-center">
        <p class="text-2xl font-black text-gray-800">{{ $sysStats['total_transaksi'] }}</p>
        <p class="text-xs text-gray-400 mt-1">Total Transaksi</p>
    </div>
    <div class="bg-white rounded-2xl p-4 shadow-sm border border-yellow-100 text-center">
        <p class="text-2xl font-black text-yellow-600">{{ $sysStats['total_pending'] }}</p>
        <p class="text-xs text-gray-400 mt-1">Pending</p>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
    <div class="lg:col-span-2 space-y-4">

        {{-- FORM GENERATE - hanya berisi tarif, TIDAK ada nested form --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100">
                <h2 class="font-bold text-gray-800">Tarif Pajak</h2>
                <p class="text-xs text-gray-400 mt-0.5">Ubah tarif lalu klik <strong>Generate & Kirim Notif</strong></p>
            </div>

            <form id="formGenerate" method="POST" action="{{ route('admin.generate-tagihan') }}">
                @csrf
                <div class="divide-y divide-gray-50">
                    <div class="px-5 py-4 flex items-center gap-4 flex-wrap">
                        <div class="flex-1"><p class="font-semibold text-green-700 text-sm">harian_rate</p><p class="text-xs text-gray-400">Tarif pajak harian</p></div>
                        <div class="flex items-center gap-2"><span class="text-xs text-gray-500">Rp</span>
                            <input type="number" name="settings[harian_rate]" value="{{ $harian }}" min="0" required
                                class="px-4 py-2 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-green-500 w-36 font-semibold">
                        </div>
                    </div>
                    <div class="px-5 py-4 flex items-center gap-4 flex-wrap">
                        <div class="flex-1"><p class="font-semibold text-green-700 text-sm">mingguan_rate</p><p class="text-xs text-gray-400">Tarif pajak mingguan</p></div>
                        <div class="flex items-center gap-2"><span class="text-xs text-gray-500">Rp</span>
                            <input type="number" name="settings[mingguan_rate]" value="{{ $mingguan }}" min="0" required
                                class="px-4 py-2 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-green-500 w-36 font-semibold">
                        </div>
                    </div>
                    <div class="px-5 py-4 flex items-center gap-4 flex-wrap">
                        <div class="flex-1"><p class="font-semibold text-green-700 text-sm">bulanan_rate</p><p class="text-xs text-gray-400">Tarif pajak bulanan</p></div>
                        <div class="flex items-center gap-2"><span class="text-xs text-gray-500">Rp</span>
                            <input type="number" name="settings[bulanan_rate]" value="{{ $bulanan }}" min="0" required
                                class="px-4 py-2 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-green-500 w-36 font-semibold">
                        </div>
                    </div>
                </div>
                <div class="px-5 py-4 bg-green-50 border-t border-green-100">
                    <button type="submit" onclick="return confirm('Simpan tarif & kirim notifikasi ke semua pedagang?')"
                        class="w-full py-3 bg-green-700 hover:bg-green-800 text-white font-bold rounded-xl text-sm flex items-center justify-center gap-2 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                        Generate & Kirim Notif ke Semua Pedagang
                    </button>
                    <p class="text-xs text-green-600 text-center mt-2">Tarif tersimpan + notifikasi info tarif terkirim ke semua pedagang</p>
                </div>
            </form>
        </div>

        {{-- SETTING LAIN - setiap row punya form hapus sendiri (bukan nested!) --}}
        @if($settingLain->count() > 0)
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100">
                <h2 class="font-bold text-gray-800 text-sm">Setting Tambahan</h2>
            </div>
            <div class="divide-y divide-gray-50">
                @foreach($settingLain as $s)
                <div class="px-5 py-3 flex items-center gap-3 flex-wrap hover:bg-gray-50">
                    <div class="flex-1 min-w-32">
                        <p class="font-mono text-sm font-semibold text-blue-700">{{ $s->nama_setting }}</p>
                        <p class="text-xs text-gray-400">{{ $s->deskripsi ?? '-' }}</p>
                    </div>
                    <span class="text-sm font-semibold text-gray-700 flex-1">{{ $s->nilai }}</span>
                    {{-- Form hapus BERDIRI SENDIRI, bukan nested --}}
                    <form method="POST" action="{{ route('admin.hapus-setting') }}" onsubmit="return confirm('Hapus setting {{ $s->nama_setting }}?')">
                        @csrf
                        <input type="hidden" name="id_setting" value="{{ $s->id_setting }}">
                        <button type="submit" class="px-3 py-1.5 bg-red-50 hover:bg-red-100 text-red-600 rounded-lg text-xs font-semibold">Hapus</button>
                    </form>
                </div>
                @endforeach
            </div>
        </div>
        @endif

    </div>

    {{-- KANAN --}}
    <div class="space-y-4">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
            <h3 class="font-bold text-gray-800 text-sm mb-3">💰 Tarif Aktif Saat Ini</h3>
            <div class="space-y-3">
                <div class="flex justify-between items-center py-1 border-b border-gray-50">
                    <span class="text-sm text-gray-500">Harian</span>
                    <span class="font-black text-green-700">Rp {{ number_format($harian,0,',','.') }}</span>
                </div>
                <div class="flex justify-between items-center py-1 border-b border-gray-50">
                    <span class="text-sm text-gray-500">Mingguan</span>
                    <span class="font-black text-green-700">Rp {{ number_format($mingguan,0,',','.') }}</span>
                </div>
                <div class="flex justify-between items-center py-1">
                    <span class="text-sm text-gray-500">Bulanan</span>
                    <span class="font-black text-green-700">Rp {{ number_format($bulanan,0,',','.') }}</span>
                </div>
            </div>
            <p class="text-xs text-gray-400 mt-3">Nilai ini langsung tampil di halaman pembayaran pedagang</p>
        </div>



        <div class="bg-blue-50 rounded-2xl p-4 border border-blue-100">
            <p class="text-xs font-semibold text-blue-700 mb-1">ℹ️ Cara pakai</p>
            <p class="text-xs text-blue-600 leading-relaxed">
                1. Ubah nominal tarif di kolom kiri<br>
                2. Klik <strong>Generate & Kirim Notif</strong><br>
                3. Tarif tersimpan & semua pedagang dapat notifikasi otomatis
            </p>
        </div>
    </div>
</div>
@endsection