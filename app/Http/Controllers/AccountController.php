<?php

namespace App\Http\Controllers;

use PDF;
use App\User;
use App\Hostel;
use App\Chapter;
use App\Food;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Intervention\Image\Facades\Image;
use Illuminate\Database\Eloquent\Collection;

class AccountController extends Controller
{
	public function getCard($id)
	{

		$user = User::whereId($id)->first();

		if (auth()->user()->level == 'Participant') {
			$user = Auth::user();
			if ($user->registration_status != 'Complete') {
				return back()->with('error', 'You must complete registration before viewing this resource');
			}
		}

		if (auth()->user()->level == 'Moderator') {

			if ($user->uploaded_by != auth()->user()->id) {
				return abort(404);
			}

			if ($user->registration_status != 'Complete') {
				return back()->with('error', 'You must complete registration before viewing this resource');
			}
		}

		return view('card.id')->with('user', $user);
	}

	public function index()
	{
		$chapters = Chapter::all();

		if (auth()->user()->level == 'Admin') {
			$registered_participants = User::where('level', '<>', 'Admin')->count();
			return view('admin.index', compact('registered_participants'));
		} elseif (auth()->user()->level == 'Participant' || auth()->user()->level == 'Alumni' || auth()->user()->level == 'Nec') {

			return view('participant.index', compact('chapters'));
		} elseif (auth()->user()->level == 'Moderator') {

			$participants = User::with(['hostel', 'moderator'])->whereUploadedBy(auth()->user()->id)->orderBy('created_at', 'desc')->get();

			$pending_registration = $participants->where('registration_status', 'Pending');

			$completed_registration = $participants->where('registration_status', 'Complete');

			return view('moderator.index', compact('chapters', 'pending_registration', 'completed_registration', 'participants'));
		}
	}

	public function update(Request $request, $id)
	{

		$this->validate($request, [
			'name' => 'required',
			'phone' => 'required',
			'sex' => 'in:Male,Female',
			'chapter' => 'nullable',
			'passport' => 'nullable|max:200|mimes:jpeg,jpg,png'
		]);

		$user = User::findOrFail($id);
		$hostels = Hostel::orderBy('allocation', 'ASC')->get();

		//if the user->hostel_id is set and type and level corresponds to the user current request hostel type and level, return back with success,
		// dd($request->all(), $user);
		if (
			$user->hostel_id &&
			$user->sex == $request->sex &&
			$user->chapter == $request->chapter &&
			$user->phone == $request->phone &&
			$user->name == $request->name &&
			!$request->hasFile('passport')
		) {

			return redirect()->route('account')->with('message', ':) Great, looks like you didnt make any changes.');
		} else if (!$user->hostel_id) { // first time users - PERFECT
			$this->createNewFood($user); // it doesnt matter where you place this, it excutes once - CORRECT
			return $this->createOrUpdateHostel(
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
					$user->sex = $request->sex ?: $user->sex; // gender
					$user->name = $request->name ?: $user->name; // name
					$user->phone = $request->phone ?: $user->phone; // phone
					$user->password = $request->password ? Hash::make($request->password) : $user->password; // password

					// Handle passport upload
					if ($request->hasFile('passport') && $request->file('passport')->isValid()) {
						$imgName = $request->passport->getClientOriginalName();
						$passport = Image::make($request->passport)->resize(500, 500);
						$passport->save('frontend/passports/' . date('Y-m-d-His') . $imgName);
						$image_path = $passport->dirname . '/' . $passport->basename;

						if (file_exists($user->passport))
							unlink($user->passport);

						$passport->destroy();
						$user->passport = $image_path;
					}
					if($user->registration_status != 'Complete')
						$user->registration_status = 'Complete';
					$user->save(); // save the changes if any
					return redirect()->route('account')->with('message', ':), updated was successful');
				}
				if ($item->capacity == $item->allocation && $collection->count() == $iterator) { // the last loop
					return redirect()->route('account')->with('error', ':(, this is not you it\'s us, looks like there is no hostel available.');
				}
			}
		)->first(); // map always return collection array;
	}

	/**
	 * Allocate the next avialable food to the $user
	 * @param App\User $user the user needing the allocation **NOTE** [$user eloguent object it passed, to implement the save method]
	 */
	private function createNewFood(User $user)
	{
		$collection = Food::where('level', $user->level)
			->orderBy('allocation', 'ASC')->get(); // sort by the lowest allocation
		// Iterate through the collection
		$iterator = 0;

		if (!$user->food_id)
			$collection->each(function ($item) use (&$iterator, $user, $collection) {
				$iterator++;
				if ($item->capacity != $item->allocation) {
					$user->food_id = $item->id;
					$user->save();

					$item->allocation++;
					$item->save();
					return false; // break outta each
				} else if ($item->capacity == $item->allocation && $collection->count() == $iterator) { // the last loop
					return false; // breaks outta each
				}
			});
	}

}
