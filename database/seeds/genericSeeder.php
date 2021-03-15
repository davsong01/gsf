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
            'alumni_fee' => 1000,
            'start_date' => now(),
            'end_date' => now(),
            'close_registration' => '2021-02-23 21:24:34',
            'conference_theme' => 'ABOVE ONLY',
            'conference_overview' => "<p>The earnest expectation of the world from a student is excellence. <br><br>
            Excellence is a state of producing good quality be it in career or in business. It is important to possess the spirit of Excellence as it''s a distinguishing factor in the world. Excellence puts one in a position which is to be <b>Above Only</b>.<br><br> 

            'Above only'... A conference where expectations will be met for greater exploits. A place where grace will be received to go beyond limits.</p><br>"
        ]);

        $admin1 = User::Create([
            'name' => 'Admin 1',
            'conference_number' => 'davsong01@gmail.com',
            'email' => 'davsong01@gmail.com',
            'sex' => 'Male',
            'type' => '4',
            'level' => 'Admin',
            'registration_status' => 'Complete',
            'password' => $generalPassword
        ]);

        $hostel1 = Hostel::create([
            'name' => 'Joshua',
            'type' => 'Male',
            'capacity' => 20,
            'level' => 'Participant',
        ]);

        $hostel2 = Hostel::create([
            'name' => 'Abigael',
            'type' => 'Female',
            'capacity' => 10,
            'level' => 'Participant',
        ]);
        $hostel3 = Hostel::create([
            'name' => 'Moses',
            'type' => 'Male',
            'capacity' => 20,
            'level' => 'Alumni',
        ]);

        $hostel4 = Hostel::create([
            'name' => 'Deborah',
            'type' => 'Female',
            'capacity' => 15,
            'level' => 'Nec',
        ]);

        $food1 = Food::create([
            'name' => 'Foodstand1',
            'level' => 'Nec',
            'capacity' => 20,
        ]);

        $food2 = Food::create([
            'name' => 'Foodstand2',
            'level' => 'Participant',
            'capacity' => 20,
            
        ]);

         $food2 = Food::create([
            'name' => 'Foodstand2',
            'level' => 'Alumni',
            'capacity' => 20,
        ]);

         $campuses = ['The Polytechnic Ibadan', 'Lagos State University (LASU)', 'Federal Polytechnic Ado-ekiti main campus (FEDPOADO)', 'Ogun State Institute of Technology Igbesa (OGITECH)', 'Rufus Giwa Polytechnic, Owo (RUGIPO)', 'University of Ilorin (UNILORIN)', 'Adekunle Ajasin University Akungba-Akoko (AAUA)', 'University of Benin (UNIBEN)', 'Federal University of Technology, Akure (FUTA)', 'Tai Solarin College of Education (TASCE)', 'Kwara State Polythenic (KWARAPOLY)', 'University of Abuja(UNIABUJA)', 'Federal College of Education, Osiele, (FCE ABK)', 'Tai Solarin University of Education (TASUED)', 'Osun State University (UNIOSUN) Ikire Campus' , 'Ekiti State University (EKSU)', 'Michael Otedola College of Education, Epe (MOCPED)', 'Lagos State Polythenic (LASPOTECH)', 'Obafemi Awolowo University (OAU)', 'Adeyemi Federal University of Education (Formerly, Adeyemi College of Education)', 'Adeniran Ogunsanya College of Education (AOCOED)', 'University of Lagos (UNILAG)', 'Yaba College of Technology (YABATECH)', 'Federal Polytechnic Offa (FEDPOFFA)', 'Olabisi Onabanjo University (OOU)', 'Federal Polytechnic Ede (FEDPOEDE)', 'Federal University of Agriculture, Abeokuta (FUNAAB)', 'Federal Polytechnic Ilaro', 'University of Ibadan (UI)', 'Ibarapa Polytechnic Eruwa, Ibarapa', 'Federal College of Education (SPECIAL), Oyo', 'Ladoke Akintola University of Technology (LAUTECH)', 'Moshood Abiola Polytechnic, Abeokuta (MAPOLY)', 'Osun State College of Technology, Esa-Oke (OSCOTECH)', 'Federal Colleges of Animal Health, Production Technology and Agriculture, Plantation, Moor Plantation, Ibadan', 'Crown Polytechnic, Ado Ekiti', 'Federal University, Oye-Ekiti (FUOYE) Ikole Campus', 'Olabisi Onabanjo University Teaching Hospital (OOUTH)', 'Federal College of Agriculture, Akure', 'National Open University (NOUN) Akure Study Centre', 'University of Portharcourt (UNIPORT)', 'Ambrose Alli University, Ekpoma (AAU)', 'College of Health Science and Technology, Ijero, Ekiti State', 'Osun State University (UNIOSUN) Oshogbo Campus', 'Federal Polytechnic, Nekede, Owerri, Imo State', 'Federal Polythenic, Auchi, Edo State', 'Kwara State College of Education, Oro', 'Ondo State College of Health Technology, Akure', 'All State College of Education, Ero, ONDO State', 'Federal University of Petroleum Resources, Effurun, Warri, Delta State', 'Ondo State University of Science and Technology, Igbokoda-Okitipupa, Ondo State', 'School of Nursing and Midwifery, Akure', 'College of Education/UNN Ikere Ekiti Chapter, Ekiti State', 'Ogun State College of Health Technology, Ilese Ijebu', 'Federal College of Education, Okene, Kogi State', 'College of Health, Offa, Kwara State', 'Oke-Ogun Polytechnic, Saki (TOPS)', 'Gateway Polytechnic, Saapade (GAPOSSAA)', 'Osun State College of Education, Ila-Orangun', 'Osun State College of Education, Ilesa', 'Federal University, Oye-Ekiti (FUOYE) Oye Campus', 'Osun State University (UNIOSUN), Okuku Campus', 'Olabisi Onabanjo University, (OOU) Ibogun Campus', 'Federal College of Education (TECHNICAL) Akoka, Yaba, Lagos', 'Olabisi Onabanjo University College of Agricultural Science, Ayetoro (OOUCAS)', 'Delta State University, Abraka (DELSU)', 'College of Education/DELSU, Agbor Chapter', 'Osun State Polytechnic, Iree, Osun State', 'Kogi State University, Anyigba', 'Kwara State University (KWASU)', 'Achievers University, Owo', 'Federal University of Technology Minna (FUTMINNA)', 'Federal Polytechnic Bida', 'Federal Polytechnic Ado, Satellite Campus', 'Edo State Polytechnic, Usen', 'Federal Polytechnic Ile-Oluji (FEDPOLEL)', 'Emmanuel Alayande College of Education, Oyo (EACOED)', 'College of Medicine, University of Lagos (MEDILAG)', 'College of Education, Lanlate', 'Federal Polytechnic, Nasarawa', 'School Of Health Science and Technology, Idah, Kogi State', 'Kogi State Polytechnic, Lokoja, Kogi State', 'Osun State College of Health Technology, Ilesha', 'Osun State University, Ipetu Ijesa Campus', 'Abraham Adesanya Polytechnic, Ijebu Igbo',
        ];

        for($a = 0; $a < count($campuses); $a++){
            // print_r($campuses[$a]);
           Chapter::create([
                'name' => $campuses[$a],
            ]);
        }

    }
}