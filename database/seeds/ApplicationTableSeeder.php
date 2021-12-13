<?php

use App\Models\Application;
use Illuminate\Database\Seeder;

class ApplicationTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        for ($i=1; $i < 10; $i++) { 
            Application::create([
                'state' => null,
                'is_invited' => null,
                'is_applied' => (bool)random_int(0, 1),
                'is_saved' => (bool)random_int(0, 1),
                'user_id' => 15,
                'recruitment_id' => $i
            ]);
        }

        for ($i=10; $i < 30; $i++) { 
            Application::create([
                'state' => null,
                'is_invited' => null,
                'is_applied' => true,
                'is_saved' => false,
                'user_id' => 20,
                'recruitment_id' => $i
            ]);
        }

        for ($i=40; $i < 50; $i++) { 
            Application::create([
                'state' => null,
                'is_invited' => null,
                'is_applied' => false,
                'is_saved' => true,
                'user_id' => 21,
                'recruitment_id' => $i
            ]);
        }
    }
}
