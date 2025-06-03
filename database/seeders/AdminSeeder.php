<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::create([
            'name' => 'ahli-gizi',
            'email' => 'ahligizi@gmail.com',
            'password' => Hash::make('ahligizi123'), // Ganti dengan password yang aman
            'role' => 'ahli-gizi',
        ]);
    }
}
