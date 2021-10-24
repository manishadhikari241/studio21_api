<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    protected $roles = [];

    public function __construct()
    {
        $this->roles = array(
            array('role_name' => 'general'),
            array('role_name' => 'super-admin'),
            array('role_name' => 'representative'),
        );
    }

    public function run()
    {

        DB::table('roles')->insert($this->roles);
    }
}
