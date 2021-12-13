<?php

use App\Models\Recruitment;
use App\Models\RecruitmentTag;
use Illuminate\Database\Seeder;

class RecruitmentTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $keys_id = array(1, 2, 3, 4, 5, 6, 7, 8);

        for ($i = 1; $i < 110; $i++) {
            $position = array_rand(array_flip(array('fresher', 'intern', 'senior', 'designer')), 1);
            $arr_size = rand(2, 5);
            $hashtags_id = array_rand(array_flip($keys_id), $arr_size);
            $new_recruiment = Recruitment::create([
                'title' => 'bài tuyển dụng số ' . $i,
                'position' => $position,
                'is_full_time' => (bool)random_int(0, 1),
                'job_category' => 'Software engineer',
                'location' => 'Danang, VN',
                'description' => 'Dô liền chớ đợi chi nữa.',
                'requirement' => 'Biết code',
                'min_salary' => rand(100, 500),
                'max_salary' => rand(1000, 2000),
                'benefits' => 'Lợi ích nhiều chứ.',
                'expiry_date' => '2021-12-15',
                'is_closed' => false,
                'user_id' => rand(2, 22),
            ]);

            foreach ($hashtags_id as $hashtag_id) {
                RecruitmentTag::create([
                    'recruitment_id' => $new_recruiment->id,
                    'hashtag_id' => $hashtag_id
                ]);
            }
        }

        // Recruitment::create([
        //     'title' => '',
        //     'position' => '',
        //     'is_full_time' => '',
        //     'job_category' => '',
        //     'location' => '',
        //     'description' => '',
        //     'requirement' => '',
        //     'min_salary' => '',
        //     'max_salary' => '',
        //     'benefits' => '',
        //     'expiry_date' => '',
        //     'is_closed' => false,
        //     'user_id' => '',
        // ]);
    }
}
