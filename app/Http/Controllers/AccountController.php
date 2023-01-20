<?php

namespace App\Http\Controllers;

use PDF;
use App\Food;
use App\User;
use App\Zone;
use App\Field;
use App\Hostel;
use App\Chapter;
use App\Setting;
use App\Donation;
use App\Material;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Intervention\Image\Facades\Image;
use Illuminate\Database\Eloquent\Collection;

class AccountController extends Controller
{
	

	public function index()
	{

		if(auth()->user()->isSubAdmin() && auth()->user()->isMember()){// Only sub admins who are members
			$users = User::where('chapter_id', auth()->user()->chapter_id)
				->where('id', '<>', auth()->user()->id)
				->where('status', 0);

			$alumnis = User::where('chapter_id', auth()->user()->chapter_id)
				->where('id', '<>', auth()->user()->id)
				->where('status', 1);
			
			return view('admin.index', compact('users', 'alumnis'));
			
		}elseif(auth()->user()->isAdmin()){
			$allusers = User::all();
			$users = $allusers->where('status', 0);
			$alumnis = $allusers->where('status', 1);
			
			$fields = Field::all();
			$zones = Zone::all();
			$chapters = Chapter::get();

			return view('admin.index', compact('users', 'alumnis', 'chapters', 'fields', 'zones'));

		}else{
			$user = auth()->user();
			$chapters = Chapter::all();
			$portfolios = $this->getCommunityPortfolios();
			$sessions = range(date('1982'), date('Y'));
			
			$president = $user->campus->stakeholder ?? null;
			if($president){
				$president = $president->where('role', 'President')->first();
			}
	
			return view('admin.users.profile', compact('chapters', 'user', 'president', 'portfolios', 'sessions'));
		}

	}
}
