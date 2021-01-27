<?php

namespace App\Http\Controllers;

use PDF;
use App\User;
use App\Chapter;
use App\Hostel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class AccountController extends Controller
{
	public function getCard($id){
		
		$user = User::whereId($id)->first();

		if(auth()->user()->level == 'Participant'){
			$user = Auth::user();
			if($user->registration_status != 'Complete'){
				return back()->with('error', 'You must complete registration before viewing this resource');
			}
		}

		if(auth()->user()->level == 'Moderator'){
	
			if($user->uploaded_by != auth()->user()->id ){
				return abort(404);
			}

			if($user->registration_status != 'Complete'){
				return back()->with('error', 'You must complete registration before viewing this resource');
			}
		}
		// dd($user->food->name);
		return view('card.id')->with('user', $user);
		

		
	}

	public function index()
	{
        $chapters = Chapter::all();
        
		if (auth()->user()->level == 'Admin') {
			return view('admin.index');
		} elseif (auth()->user()->level == 'Participant') {

			return view('participant.index', compact('chapters'));
		} elseif (auth()->user()->level == 'Moderator') {

            $participants = User::with(['hostel', 'moderator'])->whereUploadedBy(auth()->user()->id)->orderBy('created_at', 'desc')->get();

            $pending_registration = $participants->where('pending_registration' , '=', 'Pending');

            $completed_registration = $participants->where('registrationStatus', '=',' Complete');

            return view('moderator.index', compact('chapters', 'pending_registration', 'completed_registration', 'participants'));
        }
	}

	public function update(Request $request, $id)
	{
		dd($request->all());
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

	//is request->sex set, if not, return back with error('Gender is required, I think the validation already takes care of this, we will retain this validation even in the excel upload) else continue
	
	//locate user
	//Check if user->hostel_id is set(if user has hostel)

	//CHECK 1
	//if the user->hostel_id is set and type and level corresponds to the user current request hostel type and level, return back with success, 
	
	//CHECK 2
	//if the user->hostel_id is NULL, call method: createNewHostel(user_id, $request->sex, level)

	//private method createNewHostel(user_id, sex, level)
		/*Get collection of all available hostels that:
		*has same level as this user's level
		*Has same type as this user's gender
		*has capacity greater than allocation
		*Iterate through all hostels and assign the one with lowest allocation to this user(consider even distribution)
		*allocation ++ for the assigned hostel
		*save hostel and exit iteration
		*return new hostel_id, end of method
		*/

	//Save user and his new hostel_id
	//End of CHECK 2

	//CHECK 3
	//if the user->hostel_id is set but hostel type(gender) DOES NOT correspond to the user current request->sex (i.e, user has hostel but is updating sex), call method updateUserHostel(user_hostel_id, $request->sex, level)

	//private method updateUserHostel(user_hostel_id, sex, level)
		/*Get user currently assigned hostel
		*Allocation --
		*save this hostel
		*Get collection of all available hostels that:
		*has same level as this user's level
		*Has same type as this user's sex
		*has capacity greater than allocation
		*Iterate through all hostels and assign the one with lowest allocation to this user(consider even distribution)
		*allocation ++ for the assigned hostel
		*save hostel and exit iteration
		*return new hostel_id
		*/

	//Save user and new hostel_id
	//End of CHECK 3

	//CHECK 4
	//Check if user->food is set(if user has foodstand), if yes do nothing... user foodstand cannot be changed(only an admin can do that), else call method: createNewFood(user_id, type)

	//private method createNewFood(user_id, type)
	/**
	 * Get collection of all available foodstands that have has same level as this user's level
	 * has capacity greater than allocation
	 * Iterate through all these foodstand and assign the one with lowest allocation to this user(consider even distribution)
	 * allocation ++ for the assigned foodstand
	 * save foodstand and exit iteration
	 * return new food_id
	 */

	 //Save user and new food_id
	//End of CHECK 4


	

		

	


}
