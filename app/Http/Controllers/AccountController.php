<?php

namespace App\Http\Controllers;

use App\Chapter;
use App\Hostel;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class AccountController extends Controller
{
	public function index()
	{
		$chapters = Chapter::all();
		if (auth()->user()->level == 'Admin') {
			return view('admin.index');
		} elseif (auth()->user()->level == 'Participant') {
			return view('participant.index', compact('chapters'));
		}
	}

	/**
	 * Update the given user.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  string  $id
	 * @return \Illuminate\Http\Response
	 */
	public function update(Request $request, $id)
	{
		$this->validate($request, [
			'name' => 'required',
			'email' => 'required|unique:users,email,' . $id,
			'phone' => 'required|unique:users,phone,' . $id,
			'sex' => 'in:Male,Female',
			'amount_paid' => 'required',
			'payment_type' => 'required',
			'chapter' => 'required', //|exists:chapters,id
			'passport' => 'required|image'
		]);
			$user = User::findOrFail($id);
		$hostel_sorted = Hostel::where('level', $user->level?: 'Participant')
		->where('type', $user->gender?: 'Male')
		->orderBy('allocation', 'ASC')->get();

		$flagged = false;
			$hostel_sorted->map(function($item, $key) use(&$flagged, $user){

				if($item->capacity != $item->allocation && !$flagged) {
					// Has user been given a hostel
					if($user->hostel_id){
						$user->hostel->allocation--;
						$user->hostel->save();
					} else{
						// $flagged = true;		
						// $item->allocation++;
						// $item->save();
						// $user->hostel_id = $item->id;
						// $user->save();
					}
					$flagged = true;	
					$item->allocation++;
					$item->save();
					$user->hostel_id = $item->id;
					$user->save();
				}
			});
	}

}
