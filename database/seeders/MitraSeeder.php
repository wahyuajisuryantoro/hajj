<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Mitra;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class MitraSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('mitras')->insert([
            'code' => '0000000078',
            'username' => 'novaria',
            'password' => Hash::make('password'),
            'referral_code' => 'c5f8934',
            'level' => 'mitra',
            'name' => 'Novaria Anglea',
            'sex' => 'P',
            'phone' => '081234567890',
            'status' => 'active',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}

