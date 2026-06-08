@extends('layouts.sidebar-admin')
@section('title','Edit Profil - SIPESEL')
@section('page-title','Edit Profil')
@section('page-sub','Perbarui data diri Anda')

@section('content')
@if(session('success'))
<div class="bg-green-50 border border-green-200 text-green-800 rounded-xl px-5 py-3 mb-5 flex items-center gap-2 text-sm">
    <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
    {{ session('success') }}
</div>
@endif
@if($errors->any())
<div class="bg-red-50 border border-red-200 text-red-700 rounded-xl px-5 py-3 mb-5 text-sm">{{ $errors->first() }}</div>
@endif

<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    {{-- Banner --}}
    <div class="bg-gradient-to-r from-green-700 to-green-600 px-6 py-5 flex items-center gap-4">
        <div class="w-14 h-14 rounded-full bg-yellow-400 flex items-center justify-center text-green-900 font-black text-xl flex-shrink-0">
            {{ strtoupper(substr($user->username, 0, 1)) }}
        </div>
        <div class="text-white">
            <p class="font-bold text-lg">{{ $user->nama }}</p>
            <p class="text-green-200 text-sm">{{ $user->username }} · <span class="capitalize">{{ $user->role }}</span></p>
        </div>
    </div>

    {{-- Form landscape 2 kolom --}}
    <form method="POST" action="{{ route('profil.update') }}" class="p-6">
        @csrf
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            {{-- Kolom kiri --}}
            <div class="space-y-4">
                <h3 class="font-semibold text-gray-700 text-sm border-b border-gray-100 pb-2">Informasi Pribadi</h3>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Nama Lengkap</label>
                    <input type="text" name="nama" value="{{ old('nama', $user->nama) }}" required
                        class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-green-500 transition">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Username</label>
                    <input type="text" value="{{ $user->username }}" disabled
                        class="w-full px-4 py-2.5 border border-gray-100 rounded-xl text-sm bg-gray-50 text-gray-400 cursor-not-allowed">
                    <p class="text-xs text-gray-400 mt-1">Username tidak dapat diubah</p>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Email</label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                        class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-green-500 transition">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Nomor HP</label>
                    <input type="text" name="nomor_hp" value="{{ old('nomor_hp', $user->nomor_hp) }}" required
                        class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-green-500 transition">
                </div>
            </div>

            {{-- Kolom kanan --}}
            <div class="space-y-4">
                <h3 class="font-semibold text-gray-700 text-sm border-b border-gray-100 pb-2">Keamanan Akun</h3>
                <div class="bg-gray-50 rounded-xl p-4 text-xs text-gray-500 leading-relaxed">
                    Kosongkan field password jika tidak ingin mengganti password. Password minimal 6 karakter.
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Password Baru</label>
                    <input type="password" name="password" placeholder="Min. 6 karakter"
                        class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-green-500 transition">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Konfirmasi Password</label>
                    <input type="password" name="password_confirmation" placeholder="Ulangi password baru"
                        class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-green-500 transition">
                </div>
                <div class="pt-4 flex gap-3">
                    <button type="submit" class="flex-1 py-2.5 bg-green-700 hover:bg-green-800 text-white font-semibold rounded-xl transition text-sm">
                        Simpan Perubahan
                    </button>
                    <a href="javascript:history.back()" class="flex-1 py-2.5 border border-gray-200 text-gray-600 font-semibold rounded-xl text-center hover:bg-gray-50 transition text-sm">
                        Batal
                    </a>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection