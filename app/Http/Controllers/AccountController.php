<?php

namespace App\Http\Controllers;

use App\Chapter;
use App\Hostel;
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

		$hostel_sorted = Hostel::orderBy('allocation', 'ASC')->get();
		$flagged = false;
			$hostel_sorted->map(function($item, $key) use(&$flagged){
				if($item->capacity != $item->allocation && !$flagged) {
					$item->allocation++;
					$flagged = true;
					$item->save();
				}
			});
	}

}
