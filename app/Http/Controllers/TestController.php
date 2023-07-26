<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Http\Controllers\Controller;

class TestController extends Controller
{
    public function index(){
        $users = User::all();
   

        foreach($users as $user){
            // $user->update(['slug' => Str::slug($user->name)]);
            // if($user->level == 'Participant' || $user->level == 'Choir' || $user->level == 'Moderator' || $user->level == 'Medical'){
            //     $user->update([
            //         'role' => 2,
            //         'status' => 0,
            //         'level' => NULL
            //     ]);
            // }
            // if($user->level == 'Alumni' || $user->level == 'Nec'){
            //     $user->update([
            //         'role' => 2,
            //         'status' => 1,
            //         'level' => NULL
            //     ]);
            // }
            Payment::Create([
               'user_id' => $user->id,
                'purpose' => '2021Conference',
                'hostel_id' => $user->hostel_id,
                'food_id' => $user->food_id,
                'slot' => $user->slot,
               'slot_filled' => $user->slot_filled,
               'type' => $user->type,//individual:1, fellowship:2,alumni:3,Nec:4,Donations:5,
               'level' => $user->level,//Admin 
               'official' => $user->official,
                'level'=> 'Participant',
               'amount_paid' => $user->amount,
               'payment_type' => $user->payment_type,
               'transid' => $user->transid,
               'uploaded_by' => $user->uploaded_by,
                'registration_status' => $user->registration_status,
            ]);
        }
        dd(User::where('level', 'Participant')->count(). 'Participants still remaining' . ' ,'.$users->count(). ' found');
    }
}
