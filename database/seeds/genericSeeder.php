<?php

use App\Food;
use App\Post;
use App\User;
use App\Hostel;
use App\Chapter;
use App\Setting;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class genericSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $generalPassword = Hash::make('12345654321');
        
        $settings = Setting::create([
            'registration_fee' => 2000,
            'official_email' => 'davedeloper@gmail.com',
            'alumni_fee' => 2500,
            'start_date' => now(),
            'end_date' => now(),
            'close_registration' => '2021-02-23 21:24:34',
            'conference_theme' => 'ABOVE ONLY',
            'conference_overview' => '<p>Arising from the continuous growth in key service industries of financial services and telecommunications sectors of the West Africa economies, in 2014, key customer service professionals from the region with background in service delivery in banking and telecommunications started a network of like minds educating and imparting customer service skills and training in this spheres.</p><br>

            <p>This network combined education and best practices translating to grooming of service officers and operating systems for organizations. While Nigeria and Ghana network of professionals pioneered this frontier, the network also attracted customer service practitioners from Cote D’Ivore, The Gambia and Senegal. </p>'
        ]);

        $hostel1 = Hostel::create([
            'name' => 'Joshua',
            'type' => 'Male',
            'level' => 'Participant',
        ]);

        $hostel2 = Hostel::create([
            'name' => 'Abigael',
            'type' => 'Female',
             'level' => 'Participant',
        ]);
        $hostel3 = Hostel::create([
            'name' => 'Moses',
            'type' => 'Male',
            'level' => 'Alumni',
        ]);

        $hostel4 = Hostel::create([
            'name' => 'Deborah',
            'type' => 'Female',
            'level' => 'Nec',
        ]);

        $food1 = Food::create([
            'name' => 'Foodstand1',
            'level' => 'Nec',
        ]);

        $food2 = Food::create([
            'name' => 'Foodstand2',
            'level' => 'Participant',
            
        ]);

         $food2 = Food::create([
            'name' => 'Foodstand2',
            'level' => 'Alumni',
            
        ]);

        $chapter1 = Chapter::create([
            'name' => 'The Polytechnic Ibadan',           
        ]);

        $chapter2 = Chapter::create([
            'name' => 'Federal Polytechnic Ado-ekiti main campus (FEDPOADO)',           
        ]);


        $chapter3 = Chapter::create([
            'name' => 'Ogun State Institute of Technology Igbesa (OGITECH)',           
        ]);

        $chapter4 = Chapter::create([
            'name' => 'Rufus Giwa Polytechnic, Owo (RUGIPO)',           
        ]);
    }
}