<?php

use App\Models\RecruiterProfile;
use Illuminate\Database\Seeder;

class RecruiterProfileTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $phone = '000-000-0000';

        // Recruiter
        // for ($i=2; $i < 7; $i++) { 
        //     $address = array_rand(array_flip(array('Danang, VN', 'Hanoi, VN', 'HCM, VN', 'Haiphong, VN')), 1);

        //     RecruiterProfile::create([
        //         'contact_email' => 'company' . $i . '@gmail.com',
        //         'company_name' => 'The company '. $i,
        //         'logo_image_link' => null,
        //         'description' => 'The company '. $i,
        //         'phone_number' => sprintf('%s%04d', substr($phone, 0, -4), rand(0, 9999)),
        //         'verify' => null,
        //         'address' => $address,
        //         'company_size' => rand(100, 10000),
        //         'company_industry' => 'The company '. $i,
        //         'tax_code' => 'ADSasdaskd2ead',
        //         'user_id' => $i
        //     ]);
        // }

        // Student -> Recruiter
        for ($i=12; $i < 17; $i++) { 
            $address = array_rand(array_flip(array('Danang, VN', 'Hanoi, VN', 'HCM, VN', 'Haiphong, VN')), 1);

            RecruiterProfile::create([
                'contact_email' => 'company' . $i . '@gmail.com',
                'company_name' => 'The company '. $i,
                'logo_image_link' => null,
                'description' => 'The company '. $i,
                'phone_number' => sprintf('%s%04d', substr($phone, 0, -4), rand(0, 9999)),
                'verify' => null,
                'address' => $address,
                'company_size' => rand(100, 10000),
                'company_industry' => 'The company '. $i,
                'tax_code' => 'ADSasdaskd2ead',
                'user_id' => $i
            ]);
        }
    }
}
