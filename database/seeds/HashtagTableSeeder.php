<?php

use App\Models\Hashtag;
use Illuminate\Database\Seeder;

class HashtagTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Hashtag::create([
            'name' => 'Python'
        ]);
        Hashtag::create([
            'name' => 'PHP'
        ]);
        Hashtag::create([
            'name' => 'Java'
        ]);
        Hashtag::create([
            'name' => 'C++'
        ]);
        Hashtag::create([
            'name' => 'Assembly'
        ]);
        Hashtag::create([
            'name' => 'Nodejs'
        ]);
        Hashtag::create([
            'name' => 'C#'
        ]);
        Hashtag::create([
            'name' => 'iOS'
        ]);
    }
}
