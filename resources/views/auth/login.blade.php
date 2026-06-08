<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0">
    <title>Login - SIPESEL</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Montserrat:wght@700;800&display=swap" rel="stylesheet">
    <style>body{font-family:'Poppins',sans-serif;}</style>
</head>
<body class="min-h-screen flex items-center justify-center bg-cover bg-center"
      style="background-image: url('{{ asset('images/kios-pasar.jpg.jpeg') }}');">

<div class="absolute inset-0 bg-black/50"></div>

<div class="relative z-10 bg-white/10 backdrop-blur-lg p-8 md:p-10 rounded-2xl w-[90%] max-w-sm text-white text-center shadow-2xl">
    <img src="{{ asset('images/logo-sipesel.png') }}" class="w-24 mx-auto mb-4" alt="Logo SIPESEL">
    <h2 class="text-xl md:text-2xl font-semibold mb-6">LOGIN</h2>

    @if(session('success'))
    <div class="bg-green-500/80 text-white text-sm p-3 rounded-lg mb-4">{{ session('success') }}</div>
    @endif
    @if($errors->any())
    <div class="bg-red-500/80 text-white text-sm p-3 rounded-lg mb-4">{{ $errors->first() }}</div>
    @endif

    <form action="{{ route('login') }}" method="POST" class="space-y-4">
        @csrf
        <input type="text" name="username" placeholder="Username" required autocomplete="off"
            class="w-full p-3 rounded-lg bg-white text-black outline-none focus:ring-2 focus:ring-yellow-400"
            value="{{ old('username') }}">
        <input type="password" name="password" placeholder="Password" required
            class="w-full p-3 rounded-lg bg-white text-black outline-none focus:ring-2 focus:ring-yellow-400">

        <div class="flex items-center gap-2 text-sm text-left">
            <input type="checkbox" name="remember" id="remember" class="w-4 h-4 accent-yellow-400">
            <label for="remember" class="text-gray-200 cursor-pointer">Remember me</label>
        </div>

        <button type="submit" class="w-full bg-green-700 py-3 rounded-lg font-semibold hover:bg-green-800 transition shadow-lg">
            LOGIN
        </button>
    </form>

    <p class="text-sm text-gray-200 mt-6">
        Belum punya akun?
        <a href="{{ route('register') }}" class="text-yellow-400 font-semibold hover:underline">Daftar di sini</a>
    </p>
</div>

</body>
</html>