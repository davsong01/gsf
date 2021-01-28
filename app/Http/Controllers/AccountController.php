<?php

namespace App\Http\Controllers;

use PDF;
use App\User;
use App\Chapter;
use App\Hostel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Collection;
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

			$pending_registration = $participants->where('pending_registration', '=', 'Pending');

			$completed_registration = $participants->where('registrationStatus', '=', ' Complete');

			return view('moderator.index', compact('chapters', 'pending_registration', 'completed_registration', 'participants'));
		}
	}

	public function update(Request $request, $id)
	{

		$this->validate($request, [
			'name' => 'required',
			'email' => 'required|unique:users,email,' . $id,
			'phone' => 'required',
			'sex' => 'in:Male,Female',
			'amount_paid' => 'required',
			'payment_type' => 'required',
			'chapter' => 'required|exists:chapters,id',
			'passport' => 'nullable|max:200|mimes:jpeg,jpg,png'
		]);

		$user = User::findOrFail($id);
		$hostels = Hostel::orderBy('allocation', 'ASC')->get();

		$flagged = false;
		$index = 0;
		$total_hostel = $hostels->count();

		// $hostels->map(function ($item, $key) use (&$flagged, $user, $total_hostel, &$index, $hostels, $request) {
		// $index++;
		// Before anything check if there is a space in the hostel
		// if ($item->capacity != $item->allocation && !$flagged) {
		// 	// Has user been given a hostel
		// 	if ($user->hostel_id) {
		// 		$queried_hostel = $hostels->where('id', $user->hostel_id)->first();

		// 		$queried_hostel // find the hostel from the sorted
		// 			? $queried_hostel->allocation-- : $user->hostel->allocation--; // , this is the only way to reduce the allocation effectively
		// 		$queried_hostel ? $queried_hostel->save() : $user->hostel->save(); // and reduce by one or access the hostel with the user model [not effective, shouldnt even exist in the algorithm]	
		// 	}
		// 	$flagged = true;
		// 	$item->allocation++;
		// 	$item->save();
		// 	$user->hostel_id = $item->id;
		// 	$user->save();
		// 	return redirect()->route('account')->with('success', 'Updated successfully');
		// } else if (!$flagged && $total_hostel == $index) { // we are at last iteration and no allocation was made
		// 	return redirect()->route('account')->with('error', 'Ops this is us not you, we are out of hostel.');
		// }

		//if the user->hostel_id is set and type and level corresponds to the user current request hostel type and level, return back with success,
		if ($user->hostel_id && $user->type == $request->type && $user->level == $request->level) {
			return redirect()->route('account')->with('message', ':) Great, looks like you didnt make any changes.');
		} else if (!$user->hostel_id) { // first time users
		return $this->createNewHostel(
				$user,
				$request->level ?: $user->level, // the user might be changing levels 
				$request->sex ?: $user->sex, //the user might be changing gender
				$hostels, // use the same collection for efficiency
				$request
			);
		} else if ($user->hostel_id) { // user is set but is updating something[hostel, gender, level]
			return $this->createOrUpdateHostel(
				$user,
				$request->level ?: $user->level, // the user might be changing levels 
				$request->sex ?: $user->sex, //the user might be changing gender
				$hostels, // use the same collection for efficiency
				$request
			);
		}
		return redirect()->route('account')->with('error', ':( this is not you. It\'s us, looks like we could not complete your request, please contact the admin, let us hope he know what to do.');
		// });
	}

	/**
	 * Allocate the next avialable hostel to the $user
	 * @param App\User $user the user needing the allocation **NOTE** [$user eloguent object it passed, to implement the save method]
	 * @param string $level the level of the user to query or the $request->new_level and sort the hostel by level
	 * @param string $gender the gender of the user to query or the $request->new_gender and sort the hostel by gender
	 * @param  \Illuminate\Http\Request  $request
	 * @param Illuminate\Support\Collection $hostel_collection for eager loading already queried hostel is passed
	 * @return \Illuminate\Http\Response
	 */

	private function createNewHostel(User $user, string $level, string $gender, Collection $hostel_collection, Request $request)
	{
		$collection = $hostel_collection->where('level', $level)->where('type', $gender)
			->sortBy('allocation'); // sort by the lowest allocation

		// Iterate through the collection
		$iterator = 0;
		return $collection->map(
			function ($item, $key) use ($user, $hostel_collection, $collection, $request, $iterator) {
				$iterator++;
				if ($item->capacity != $item->allocation) {
					// check if user has an associated hostel
					$user_hostel = $hostel_collection
					->where('id', $user->hostel_id)->first(); // you want to make sure you are querying the global as you can get
					$user_hostel // find the hostel from the sorted
						? $user_hostel->allocation-- : null; // , this is the only way to reduce the allocation effectively
					$user_hostel ? $user_hostel->save() : null; // and reduce by one

					$flagged = true; // This is flagged to break outta loop on time if we made changes ealy enough
					$item->allocation++; // increase the numbers of allocation in the corresponding hostel
					$item->save(); // remember to save the hostel 
					$user->hostel_id = $item->id; // update the user hostel_id if required
					$user->sex = $request->sex ?: $user->sex;
					$user->save(); // save the changes if any

					return redirect()->route('account')->with('message', ':) Great, update was successful');
				}
				if ($item->capacity == $item->allocation && $collection->count() == $iterator) { // the last loop
					return redirect()->route('account')->with('error', ':( Ops, this is not you it\'s us, looks like there is no hostel available at the moment.');
				}
			}
		)->first(); // map always return collection array;
	}

	/**
	 * Allocate the next avialable hostel to the $user
	 * @param App\User $user the user needing the allocation **NOTE** [$user eloguent object it passed, to implement the save method]
	 * @param string $level the level of the user to query or the $request->new_level and sort the hostel by level
	 * @param string $gender the gender of the user to query or the $request->new_gender and sort the hostel by gender
	 * @param Illuminate\Support\Collection $hostel_collection for eager loading already queried hostel is passed
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */

	private function createOrUpdateHostel(User $user, string $level, string $gender, Collection $hostel_collection, Request $request)
	{
		$collection = $hostel_collection->where('level', $level)->where('type', $gender)
			->sortBy('allocation'); // sort by the lowest allocation

		// Iterate through the collection
		$iterator = 0;
		return $collection->map(
			function ($item, $key) use ($user, $hostel_collection, $collection, $request, $iterator) {
				$iterator++;
				if ($item->capacity != $item->allocation) {
					// check if user has an associated hostel
					$user_hostel = $hostel_collection
					->where('id', $user->hostel_id)->first(); // you want to make sure you are querying the global as you can get
					$user_hostel // find the hostel from the sorted
						? $user_hostel->allocation-- : null; // , this is the only way to reduce the allocation effectively
					$user_hostel ? $user_hostel->save() : null; // and reduce by one

					$flagged = true; // This is flagged to break outta loop on time if we made changes ealy enough
					$item->allocation++; // increase the numbers of allocation in the corresponding hostel
					$item->save(); // remember to save the hostel 
					$user->hostel_id = $item->id; // update the user hostel_id if required
					$user->sex = $request->sex ?: $user->sex;
					$user->save(); // save the changes if any

					return redirect()->route('account')->with('message', ':) Great, updated was successful');
				}
				if ($item->capacity == $item->allocation && $collection->count() == $iterator) { // the last loop
					return redirect()->route('account')->with('error', ':( Ops, this is not you it\'s us, looks like there is no hostel available.');
				}
			}
		)->first(); // map always return collection array;
	}

	//[Validation Done] is request->sex set, if not, return back with error('Gender is required, I think the validation already takes care of this, we will retain this validation even in the excel upload) else continue

	//[Done]locate user
	//Check if user->hostel_id is set(if user has hostel)

	//[Done]CHECK 1
	//if the user->hostel_id is set and type and level corresponds to the user current request hostel type and level, return back with success, 

	//[Done] CHECK 2
	//if the user->hostel_id is NULL, call method: createNewHostel(user_id, $request->sex, level)

	//[Done] private method createNewHostel(user_id, sex, level)
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
