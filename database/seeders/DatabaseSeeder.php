<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Campaign;
use App\Models\Donation;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Seed Users
        $admin = User::create([
            'name' => 'Admin BantuIn',
            'email' => 'admin@bantuin.org',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
        ]);

        $fundraiser = User::create([
            'name' => 'Rina Sari',
            'email' => 'rina@mail.com',
            'password' => Hash::make('rina123'),
            'role' => 'fundraiser',
        ]);

        $budi = User::create([
            'name' => 'Budi Santoso',
            'email' => 'budi@mail.com',
            'password' => Hash::make('budi123'),
            'role' => 'fundraiser',
        ]);

        $siti = User::create([
            'name' => 'Siti Rahayu',
            'email' => 'siti@mail.com',
            'password' => Hash::make('siti123'),
            'role' => 'fundraiser',
        ]);

        $donatur = User::create([
            'name' => 'Donatur Peduli',
            'email' => 'donatur@mail.com',
            'password' => Hash::make('donatur123'),
            'role' => 'donatur',
        ]);

        // Additional Donatur
        $ayu = User::create([
            'name' => 'Ayu Pramesti',
            'email' => 'ayu@mail.com',
            'password' => Hash::make('ayu123'),
            'role' => 'donatur',
        ]);

        $rizal = User::create([
            'name' => 'Rizal H.',
            'email' => 'rizal@mail.com',
            'password' => Hash::make('rizal123'),
            'role' => 'donatur',
        ]);

        // NOTE: Hapus seeding kampanye dummy agar aplikasi hanya menampilkan data nyata dari pengguna.
    }
}
