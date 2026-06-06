<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLogin()
    {
        // Cek remember me cookie
        if ($cookie = request()->cookie('remember_user')) {
            $parts = explode('|', $cookie);
            if (count($parts) === 2) {
                [$savedUsername, $savedRole] = $parts;
                $user = User::where('username', $savedUsername)->first();
                if ($user) {
                    Auth::login($user, true);
                    return $this->redirectByRole($user->role);
                }
            }
        }

        if (Auth::check()) {
            return $this->redirectByRole(Auth::user()->role);
        }

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

        // Set remember me cookie (3 menit seperti aslinya)
        $response = $this->redirectByRole($user->role);
        if ($request->boolean('remember')) {
            $cookieValue = $user->username . '|' . $user->role;
            $response->withCookie(cookie('remember_user', $cookieValue, 3));
        }

        return $response;
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'nama'     => 'required|string|max:100|regex:/^[a-zA-Z0-9\' ]+$/',
            'username' => 'required|string|unique:users,username',
            'email'    => 'required|email|unique:users,email',
            'nomor_hp' => 'required|string',
            'password' => 'required|string|min:6',
            'gender'   => 'required|in:Laki-laki,Perempuan',
            'role'     => 'required|in:pedagang,pengawas',
        ], [
            'nama.regex'        => 'Nama hanya boleh huruf, angka, spasi dan tanda petik!',
            'username.unique'   => 'Username sudah digunakan!',
            'email.unique'      => 'Email sudah terdaftar!',
        ]);

        User::create([
            'nama'     => $request->nama,
            'username' => $request->username,
            'email'    => $request->email,
            'nomor_hp' => $request->nomor_hp,
            'password' => Hash::make($request->password),
            'gender'   => $request->gender,
            'role'     => $request->role,
            'no_kios'  => $request->role === 'pedagang' ? $request->no_kios : null,
        ]);

        return redirect()->route('login')->with('success', 'Berhasil daftar 🎉 Silakan login!');
    }

    public function logout()
    {
        Auth::logout();
        $response = redirect()->route('login');
        $response->withCookie(Cookie::forget('remember_user'));
        return $response;
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
