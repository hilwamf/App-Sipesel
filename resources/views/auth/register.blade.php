@extends('layouts.auth')
@section('title', 'Register SIPESEL')
@section('body-attrs', 'style="background-image:url(\''.asset('images/kios2.jpg').'\');" class="min-h-screen bg-cover bg-center flex items-center justify-center p-4"')

@section('content')
<div class="absolute inset-0 bg-black/60"></div>
<div class="relative z-10 flex w-full max-w-5xl rounded-2xl overflow-hidden shadow-2xl">

    <div class="hidden md:flex w-1/2 bg-gradient-to-br from-green-800 to-green-900 flex-col items-center justify-center text-white p-8 text-center">
        <h1 class="text-5xl font-bold text-yellow-400 mb-3" style="font-family:'Montserrat',sans-serif;">SIPESEL</h1>
        <p class="text-lg opacity-90">Sistem Pengelolaan Sewa Kios</p>
    </div>

    <div class="w-full md:w-1/2 bg-white p-8 md:p-10">
        <h2 class="text-2xl font-semibold text-green-800 mb-6">Form Pendaftaran</h2>

        @if($errors->any())
            <div class="mb-4 p-3 rounded-lg bg-red-100 text-red-800 border border-red-300">{{ $errors->first() }}</div>
        @endif

        <form id="registerForm" method="POST" action="{{ route('register') }}" autocomplete="off" class="space-y-4">
            @csrf
            <input type="text" name="nama" placeholder="Nama Lengkap" required autocomplete="off"
                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-600 outline-none"
                value="{{ old('nama') }}">

            <input type="text" name="username" placeholder="Username" required autocomplete="new-password"
                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-600 outline-none"
                value="{{ old('username') }}">

            <input type="email" name="email" placeholder="Email" required autocomplete="off"
                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-600 outline-none"
                value="{{ old('email') }}">

            <input type="text" name="nomor_hp" placeholder="Nomor HP" required autocomplete="off"
                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-600 outline-none"
                value="{{ old('nomor_hp') }}">

            <input type="password" name="password" placeholder="Password" required autocomplete="new-password"
                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-600 outline-none">

            <select id="role" name="role" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-600 outline-none bg-white text-gray-700">
                <option value="" disabled {{ !old('role') ? 'selected' : '' }}>Pilih Peran...</option>
                <option value="pedagang" {{ old('role')=='pedagang'?'selected':'' }}>Pedagang</option>
                <option value="pengawas" {{ old('role')=='pengawas'?'selected':'' }}>Pengawas</option>
            </select>

            <div id="kios_container" style="display:none;">
                <input type="text" id="no_kios" name="no_kios" placeholder="Nomor Kios (Contoh: A-01)"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-600 outline-none"
                    value="{{ old('no_kios') }}">
            </div>

            <div>
                <p class="text-sm text-gray-700 font-medium mb-2">Gender</p>
                <div class="flex gap-6">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="radio" name="gender" value="Laki-laki" required class="w-4 h-4 text-green-600"
                            {{ old('gender')=='Laki-laki'?'checked':'' }}>
                        <span class="text-gray-700">Laki-laki</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="radio" name="gender" value="Perempuan" required class="w-4 h-4 text-green-600"
                            {{ old('gender')=='Perempuan'?'checked':'' }}>
                        <span class="text-gray-700">Perempuan</span>
                    </label>
                </div>
            </div>

            <button type="submit" class="w-full bg-green-700 text-white py-3 rounded-lg font-semibold hover:bg-green-800 transition">
                DAFTAR
            </button>
        </form>

        <p class="text-sm text-center mt-6 text-gray-600">
            Sudah punya akun?
            <a href="{{ route('login') }}" class="text-green-700 font-semibold hover:underline">Login sekarang</a>
        </p>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const roleSelect     = document.getElementById('role');
    const kiosContainer  = document.getElementById('kios_container');
    const kiosInput      = document.getElementById('no_kios');

    function toggleKios() {
        if (roleSelect.value === 'pedagang') {
            kiosContainer.style.display = 'block';
            kiosInput.setAttribute('required','required');
        } else {
            kiosContainer.style.display = 'none';
            kiosInput.removeAttribute('required');
            kiosInput.value = '';
        }
    }
    roleSelect.addEventListener('change', toggleKios);
    toggleKios();
});
</script>
@endsection
