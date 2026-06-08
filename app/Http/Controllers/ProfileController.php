<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function edit()
    {
        $user = Auth::user();

        if ($user->role === 'pedagang') {
            return view('profile.edit-pedagang', compact('user'));
        }

        return view('profile.edit', compact('user'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'nama'     => 'required|string|max:100',
            'email'    => "required|email|unique:users,email,{$user->id_user},id_user",
            'nomor_hp' => 'required|string',
            'password' => 'nullable|string|min:6|confirmed',
        ], [
            'password.confirmed' => 'Konfirmasi password tidak cocok!',
            'email.unique'       => 'Email sudah digunakan akun lain!',
        ]);

        $data = [
            'nama'     => $request->nama,
            'email'    => $request->email,
            'nomor_hp' => $request->nomor_hp,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return back()->with('success', 'Profil berhasil diperbarui!');
    }
}