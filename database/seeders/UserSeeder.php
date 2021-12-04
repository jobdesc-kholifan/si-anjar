<?php

namespace Database\Seeders;

use App\Models\Authorization\User;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{

    /* @var User|Relation */
    protected $user;

    public function __construct()
    {
        $this->user = new User();
    }

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $types = findConfig()->in([\DBTypes::statusActive,\DBTypes::roleSuperuser]);

        $this->user->create([
            'full_name' => 'System Application',
            'email' => 'system@application',
            'phone_number' => '1234567890',
            'role_id' => $types->get(\DBTypes::roleSuperuser)->getId(),
            'user_name' => 'system.application',
            'user_password' => Hash::make('d3v4pp$123'),
            'status_id' => $types->get(\DBTypes::statusActive)->getId(),
        ]);
    }
}
