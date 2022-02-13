<?php

use App\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = User::create([
            'name' => 'admin',
            'email' => 'admin@gmail.com',
            'email_verified_at' => Carbon::now(),
            'password' => bcrypt('123123123'),
            'role_id' => 1
        ]);

        // for ($i = 1; $i < 11; $i++) {
        //     $user = User::create([
        //         'name' => 'Recruiter ' . $i,
        //         'email' => 'recruiter' . $i . '@gmail.com',
        //         'password' => bcrypt('123123123'),
        //         'role_id' => 2
        //     ]);
        // }

        // for ($i = 1; $i < 11; $i++) {
        //     $user = User::create([
        //         'name' => 'Student '.$i,
        //         'email' => 'student'.$i.'@gmail.com',
        //         'password' => bcrypt('123123123'),
        //         'role_id' => 3
        //     ]);
        // }

        // $user = User::create([
        //     'name' => 'Ho Van Khoi',
        //     'email' => 'hvkhoi.99@gmail.com',
        //     'password' => bcrypt('123123123'),
        //     'role_id' => 3
        // ]);
    }
}
