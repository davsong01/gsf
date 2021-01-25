<?php

namespace App\Http\Controllers;

use App\Post;
use App\User;
use App\Payout;
use App\Chapter;
use App\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    public function index()
    {  
        $chapters = Chapter::all();
   
        return view('welcome', compact('chapters'));
    }

    public function thankyou()
    {  
        return view('thankyou');
    }

}
