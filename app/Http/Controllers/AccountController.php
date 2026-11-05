<?php

namespace App\Http\Controllers;

use PDF;
use App\Models\Food;
use App\Models\User;
use App\Models\Zone;
use App\Models\Field;
use App\Models\Hostel;
use App\Models\Chapter;
use App\Models\Setting;
use App\Models\Donation;
use App\Models\Material;
use App\Models\StakeholderDesignation;
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
			$chapters = Chapter::actualCampus()->get();

			return view('admin.index', compact('users', 'alumnis', 'chapters', 'fields', 'zones'));
		}else{
			$user = auth()->user();
			$edition = activeConferenceEdition();
			$transactions = $user->transactions()
				->with(['edition', 'conferenceplan'])
				->get();

			return view('admin.users.dashboard', compact('user', 'edition', 'transactions'));
		}

	}

	public function profile()
	{
		$user = auth()->user();
		abort_if($user->isAdmin(), 404);

		$chapters = Chapter::actualCampus()->orderBy('name')->get();
		$portfolios = getCommunityPortfolios();
	$sessions = range((int) date('Y'), 1982);
		$president = $user->campus?->chapterPresident;
		$edition = activeConferenceEdition();
		$campusDesignations = StakeholderDesignation::query()
			->where('type', 'chapter_executive')
			->orderBy('order')
			->get(['id', 'name']);

		return view('admin.users.profile', compact(
			'chapters', 'user', 'president', 'portfolios', 'sessions', 'edition', 'campusDesignations'
		));
	}
}
