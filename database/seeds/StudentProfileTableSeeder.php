<?php

use App\Models\StudentProfile;
use Illuminate\Database\Seeder;

class StudentProfileTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $phone = '000-000-0000';
        for ($i = 15; $i < 20; $i++) {
            $first_name = array_rand(array_flip(array('Nguyen Thi', 'Vo van', 'Huynh Cong', 'Tran')), 1);
            $last_name = array_rand(array_flip(array('Thanh', 'Van', 'Tien', 'Thu')), 1);
            $nationality = array_rand(array_flip(array('Danang, VN', 'Hanoi, VN', 'HCM, VN', 'Haiphong, VN')), 1);
            $job_title = array_rand(array_flip(array('Designer', 'Developer', 'Tester')), 1);
            
            StudentProfile::create([
                'email' => 'student' . $i . '@gmail.com',
                'first_name' => $first_name,
                'last_name' => $last_name,
                'avatar_link' => null,
                'date_of_birth' => date("Y-m-d", rand(1262055681,1262055681)),
                'phone_number' => sprintf('%s%04d', substr($phone, 0, -4), rand(0, 9999)),
                'nationality' => $nationality,
                'address' => 'xa '.$i.', huyen '.$i,
                'gender' => (bool)random_int(0, 1),
                'over_view' => 'Công ty tui thành lập được '.$i.' năm rồi.',
                'open_for_job' => false,
                'job_title' => $job_title,
                'user_id' => $i
            ]);
        }
    }
}
