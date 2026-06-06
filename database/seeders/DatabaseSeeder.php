<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Kios;
use App\Models\Setting;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Admin user
        User::create([
            'nama'     => 'Administrator',
            'username' => 'admin',
            'email'    => 'admin@sipesel.com',
            'password' => Hash::make('admin123'),
            'nomor_hp' => '081234567890',
            'gender'   => 'Laki-laki',
            'role'     => 'admin',
        ]);

        // Pengawas user
        User::create([
            'nama'     => 'Pengawas Pasar',
            'username' => 'pengawas',
            'email'    => 'pengawas@sipesel.com',
            'password' => Hash::make('pengawas123'),
            'nomor_hp' => '081234567891',
            'gender'   => 'Laki-laki',
            'role'     => 'pengawas',
        ]);

        // Pedagang sample users
        $kiosList = ['A-01', 'A-02', 'B-01', 'B-02'];
        foreach ($kiosList as $i => $kios) {
            User::create([
                'nama'     => 'Pedagang ' . ($i + 1),
                'username' => 'pedagang' . ($i + 1),
                'email'    => 'pedagang' . ($i + 1) . '@sipesel.com',
                'password' => Hash::make('pedagang123'),
                'nomor_hp' => '08123456789' . ($i + 2),
                'gender'   => $i % 2 == 0 ? 'Laki-laki' : 'Perempuan',
                'role'     => 'pedagang',
                'no_kios'  => $kios,
            ]);

            // Create corresponding kios
            Kios::create([
                'no_kios'       => $kios,
                'lokasi'        => 'Blok ' . ($kios[0]) . ' Lantai 1',
                'ukuran'        => 10.00,
                'tarif_bulanan' => 250000,
                'status'        => 'terisi',
            ]);
        }

        // Sample settings
        Setting::create(['nama_setting' => 'tarif_pajak_default', 'nilai' => '250000', 'deskripsi' => 'Tarif pajak bulanan default']);
        Setting::create(['nama_setting' => 'nama_pasar', 'nilai' => 'Pasar Wadungasri', 'deskripsi' => 'Nama pasar']);
        Setting::create(['nama_setting' => 'harian_rate', 'nilai' => '5000', 'deskripsi' => 'Tarif pajak harian']);
        Setting::create(['nama_setting' => 'mingguan_rate', 'nilai' => '35000', 'deskripsi' => 'Tarif pajak mingguan']);
    }
}
