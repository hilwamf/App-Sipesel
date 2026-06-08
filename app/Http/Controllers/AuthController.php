<?php

namespace App\Http\Controllers;

use App\Models\Kios;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function showLogin()
    {
        if ($cookie = request()->cookie('remember_user')) {
            $parts = explode('|', $cookie);
            if (count($parts) === 2) {
                $user = User::where('username', $parts[0])->first();
                if ($user) { Auth::login($user, true); return $this->redirectByRole($user->role); }
            }
        }
        if (Auth::check()) return $this->redirectByRole(Auth::user()->role);
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $user = User::where('username', $request->username)->first();
        if (!$user || !Hash::check($request->password, $user->password)) {
            return back()->withErrors(['username' => 'Username atau password salah!'])->withInput(['username' => $request->username]);
        }

        Auth::login($user, $request->boolean('remember'));
        $response = $this->redirectByRole($user->role);
        if ($request->boolean('remember')) {
            $response->withCookie(cookie('remember_user', $user->username.'|'.$user->role, 3));
        }
        return $response;
    }

    public function showRegister()
    {
        $kiosList = Kios::where('status', 'kosong')->orderBy('no_kios')->get(['no_kios', 'lokasi']);
        return view('auth.register', compact('kiosList'));
    }

    public function register(Request $request)
    {
        $request->validate([
            'nama'       => 'required|string|max:100',
            'username'   => 'required|string|unique:users,username',
            'email'      => 'required|email|unique:users,email',
            'nomor_hp'   => 'required|string',
            'password'   => 'required|string|min:6',
            'gender'     => 'required|in:Laki-laki,Perempuan',
            'role'       => 'required|in:pedagang,pengawas',
            'foto_profil'=> 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ], [
            'username.unique' => 'Username sudah digunakan!',
            'email.unique'    => 'Email sudah terdaftar!',
        ]);

        // Handle upload foto profil
        $fotoPath = null;
        if ($request->hasFile('foto_profil')) {
            $file     = $request->file('foto_profil');
            $filename = 'profil_'.time().'_'.Str::random(6).'.'.$file->getClientOriginalExtension();
            $file->move(public_path('uploads/profil'), $filename);
            $fotoPath = 'uploads/profil/'.$filename;
        }

        $noKios = $request->role === 'pedagang' ? $request->no_kios : null;

        User::create([
            'nama'        => $request->nama,
            'username'    => $request->username,
            'email'       => $request->email,
            'nomor_hp'    => $request->nomor_hp,
            'password'    => Hash::make($request->password),
            'gender'      => $request->gender,
            'role'        => $request->role,
            'no_kios'     => $noKios,
            'foto_profil' => $fotoPath,
        ]);

        // Update status kios menjadi terisi setelah pedagang berhasil daftar
        if ($noKios) {
            Kios::where('no_kios', $noKios)->update(['status' => 'terisi']);
        }

        return redirect()->route('login')->with('success', 'Berhasil daftar! Silakan login.');
    }

    public function logout()
    {
        Auth::logout();
        return redirect()->route('login')->withCookie(Cookie::forget('remember_user'));
    }

    protected function redirectByRole(string $role)
    {
        return match ($role) {
            'admin'    => redirect()->route('admin.dashboard'),
            'pengawas' => redirect()->route('pengawas.dashboard'),
            default    => redirect()->route('pedagang.dashboard'),
        };
    }
}