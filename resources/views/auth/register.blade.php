<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0">
    <title>Register SIPESEL</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Montserrat:wght@700;800&display=swap" rel="stylesheet">
    <style>body{font-family:'Poppins',sans-serif;}</style>
</head>
<body style="background-image:url('{{ asset('images/kios2.jpg') }}');" class="min-h-screen bg-cover bg-center flex items-center justify-center p-4">
<div class="absolute inset-0 bg-black/60"></div>

<div class="relative z-10 flex w-full max-w-5xl rounded-2xl overflow-hidden shadow-2xl">
    <div class="hidden md:flex w-1/2 bg-gradient-to-br from-green-800 to-green-900 flex-col items-center justify-center text-white p-8 text-center">
        <h1 class="text-5xl font-bold text-yellow-400 mb-3" style="font-family:'Montserrat',sans-serif;">SIPESEL</h1>
        <p class="text-lg opacity-90">Sistem Pengelolaan Sewa Kios</p>
        <p class="text-sm opacity-60 mt-4 leading-relaxed">Daftar sekarang dan mulai kelola pembayaran pajak kios Anda secara digital.</p>
    </div>

    <div class="w-full md:w-1/2 bg-white p-8 md:p-10 overflow-y-auto max-h-screen">
        <h2 class="text-2xl font-semibold text-green-800 mb-5">Form Pendaftaran</h2>

        @if($errors->any())
        <div class="mb-4 p-3 rounded-lg bg-red-100 text-red-800 border border-red-300">{{ $errors->first() }}</div>
        @endif

        <form method="POST" action="{{ route('register') }}" enctype="multipart/form-data" autocomplete="off" class="space-y-4">
            @csrf

            {{-- Foto Profil --}}
            <div class="flex flex-col items-center mb-2">
                <div class="relative cursor-pointer" onclick="document.getElementById('fotoInput').click()">
                    <div class="w-24 h-24 rounded-full overflow-hidden border-4 border-green-600 shadow-lg bg-gray-100 flex items-center justify-center">
                        <img id="fotoPreview" src="" class="hidden w-full h-full object-cover">
                        <div id="fotoPlaceholder" class="flex flex-col items-center text-gray-400">
                            <svg class="w-8 h-8 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        </div>
                    </div>
                    <div class="absolute bottom-0 right-0 w-7 h-7 bg-green-600 rounded-full flex items-center justify-center shadow-md">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    </div>
                </div>
                <p class="text-xs text-gray-400 mt-2">Klik untuk upload foto profil (opsional)</p>
                <input type="file" id="fotoInput" name="foto_profil" accept="image/*" class="hidden" onchange="previewFoto(this)">
            </div>

            <input type="text" name="nama" placeholder="Nama Lengkap" required
                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-600 outline-none transition"
                value="{{ old('nama') }}">

            <input type="text" name="username" placeholder="Username" required autocomplete="new-password"
                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-600 outline-none transition"
                value="{{ old('username') }}">

            <input type="email" name="email" placeholder="Email" required
                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-600 outline-none transition"
                value="{{ old('email') }}">

            <input type="text" name="nomor_hp" placeholder="Nomor HP" required
                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-600 outline-none transition"
                value="{{ old('nomor_hp') }}">

            <input type="password" name="password" placeholder="Password (min. 6 karakter)" required autocomplete="new-password"
                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-600 outline-none transition">

            <select id="role" name="role" required
                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-600 outline-none bg-white text-gray-700">
                <option value="" disabled {{ !old('role') ? 'selected' : '' }}>Pilih Peran...</option>
                <option value="pedagang" {{ old('role')=='pedagang'?'selected':'' }}>Pedagang</option>
                <option value="pengawas" {{ old('role')=='pengawas'?'selected':'' }}>Pengawas</option>
            </select>

            {{-- Dropdown Kios - hanya muncul kalau role = pedagang --}}
            <div id="kios_container" style="display:none;">
                <select id="no_kios" name="no_kios"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-600 outline-none bg-white text-gray-700">
                    <option value="" disabled selected>Pilih Nomor Kios...</option>
                    @foreach($kiosList as $kios)
                    <option value="{{ $kios->no_kios }}" {{ old('no_kios')==$kios->no_kios?'selected':'' }}>
                        {{ $kios->no_kios }} — {{ $kios->lokasi }}
                    </option>
                    @endforeach
                    @if($kiosList->isEmpty())
                    <option disabled>Tidak ada kios tersedia saat ini</option>
                    @endif
                </select>
                @if($kiosList->isEmpty())
                <p class="text-xs text-orange-500 mt-1">Semua kios sedang terisi atau maintenance. Hubungi admin.</p>
                @endif
            </div>

            <div>
                <p class="text-sm text-gray-700 font-medium mb-2">Gender</p>
                <div class="flex gap-6">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="radio" name="gender" value="Laki-laki" required class="w-4 h-4 text-green-600" {{ old('gender')=='Laki-laki'?'checked':'' }}>
                        <span class="text-gray-700">Laki-laki</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="radio" name="gender" value="Perempuan" required class="w-4 h-4 text-green-600" {{ old('gender')=='Perempuan'?'checked':'' }}>
                        <span class="text-gray-700">Perempuan</span>
                    </label>
                </div>
            </div>

            <button type="submit" class="w-full bg-green-700 text-white py-3 rounded-lg font-semibold hover:bg-green-800 transition">
                DAFTAR
            </button>
        </form>

        <p class="text-sm text-center mt-5 text-gray-600">
            Sudah punya akun?
            <a href="{{ route('login') }}" class="text-green-700 font-semibold hover:underline">Login sekarang</a>
        </p>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const roleSelect    = document.getElementById('role');
    const kiosContainer = document.getElementById('kios_container');
    const kiosSelect    = document.getElementById('no_kios');
    function toggleKios() {
        if (roleSelect.value === 'pedagang') {
            kiosContainer.style.display = 'block';
            kiosSelect.setAttribute('required','required');
        } else {
            kiosContainer.style.display = 'none';
            kiosSelect.removeAttribute('required');
            kiosSelect.value = '';
        }
    }
    roleSelect.addEventListener('change', toggleKios);
    toggleKios();
});

function previewFoto(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = e => {
            const img = document.getElementById('fotoPreview');
            img.src = e.target.result;
            img.classList.remove('hidden');
            document.getElementById('fotoPlaceholder').style.display = 'none';
        };
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
</body>
</html>