<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class LoginTestSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('admin')->updateOrInsert(
            ['username' => 'admin'],
            [
                'nama' => 'Admin',
                'password' => Hash::make('Admin123!'),
            ],
        );

        DB::table('registrasi')->updateOrInsert(
            ['nip' => '199001012020011001'],
            [
                'nama_lengkap' => 'Pegawai',
                'email' => 'pegawai@silacek.test',
                'no_hp' => '080000000001',
                'password' => Hash::make('Pegawai123!'),
                'status' => 'disetujui',
            ],
        );
    }
}