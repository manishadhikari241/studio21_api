<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
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
        DB::table('users')->insert([
            'first_name' => 'Development',
            'last_name' => '01',
            'email' => 'graphics@collectionstock.com',
            'password' => Hash::make('123123123'),
            'phone'=>'0000000',
            'role_id' => 2,
            'otp_verified'=>1
        ]);
    }
}
