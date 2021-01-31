<?php

namespace App\Http\Controllers;

use App\Food;
use App\User;
use App\Hostel;
use App\Chapter;
use App\Setting;
use App\Exports\UsersExport;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Facades\Excel;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Schema;


class UserController extends Controller
{

    public function importExport()
    {
       return view('admin.users.import');
    }
    
    
    public function index()
    { 
        $count = 1;
        if(auth()->user()->level == 'Admin'){

            $participants = User::where('level', '<>', 'Admin')->orderBy('created_at', 'desc')->get();
            
        return view('admin.users.index', compact('participants', 'count'));
        }else if(auth()->user()->level == 'Moderator'){
             $participants = User::with(['hostel', 'moderator'])->whereUploadedBy(auth()->user()->id)->orderBy('created_at', 'asc')->get();

            return view('moderator.users.index', compact('participants', 'count'));
        }
        
        return abort(404);
    }

    public function trashed(){
        $count = 1;
        if(auth()->user()->level == 'Admin'){
            $participants = User::onlyTrashed()->where('level', '<>', 'Admin')->orderBy('created_at', 'desc')->get();
            
        return view('admin.users.trashed', compact('participants', 'count'));
        }
    }

     public function getChoir()
    { 
        $count = 1;
        if(auth()->user()->level == 'Admin'){
            $participants = User::with(['hostel', 'moderator'])->whereLevel('Choir')->orderBy('created_at', 'desc')->get();
            
        return view('admin.choir.index', compact('participants', 'count'));
        }
        
        return abort(404);
        
    }

     public function getMedical()
    { 
        $count = 1;
        if(auth()->user()->level == 'Admin'){
            $participants = User::with(['hostel', 'moderator'])->whereLevel('Medical')->orderBy('created_at', 'desc')->get();
            
        return view('admin.medic.index', compact('participants', 'count'));
        }
        
        return abort(404);
        
    }

     public function getNec()
    { 
        $count = 1;
        if(auth()->user()->level == 'Admin'){
            $participants = User::with(['hostel', 'moderator'])->whereLevel('Nec')->orderBy('created_at', 'desc')->get();
            
        return view('admin.nec.index', compact('participants', 'count'));
        }
        
        return abort(404);
        
    }

    public function getOfficial()
    { 
        $count = 1;
        if(auth()->user()->level == 'Admin'){
            $participants = User::with(['hostel', 'moderator'])->whereLevel('Official')->orderBy('created_at', 'desc')->get();
            
        return view('admin.official.index', compact('participants', 'count'));
        }
        
        return abort(404);
        
    }


    public function create()
    {
        $chapters = Chapter::orderBy('name')->get(); //sort in alphabetical order
        $hostels = Hostel::all();
        $foods = Food::all();
        $moderators = User::whereLevel('Moderator')->get();

        if(auth()->user()->level == 'Admin'){
            return view('admin.users.create', compact('chapters', 'hostels', 'foods', 'moderators'));
        }else if(auth()->user()->level == 'Moderator'){
            if(auth()->user()->slot_filled == auth()->user()->slot){
                return back()->with('error', 'You have reached the maximum number of slots you can add');
            }
            return view('moderator.users.create', compact('chapters'));
        }
        
        return back(404);
        
    }

    public function store(Request $request, User $user)
    {
        
        //Handle password
        if($request['password']){
            $password = Hash::make($request['password']);
        }else{ 
            $password = Hash::make($request['phone']);
        }

        //Handle Passport Upload
        //get filename with extensionz 
        $imgName = date('Y-m-d-His') . $request->passport->getClientOriginalName();
        $passport = Image::make($request->passport)->resize(500, 500);
        $passport->save('frontend/passports'.'/'.$imgName);
        $passport = 'frontend/passports/'. $imgName;

 
        //Store block for Admin
        if(auth()->user()->level == 'Admin'){
            $data = $this->validate($request, [
                'name' => 'required|min:3',
                'email' => 'required|unique:users,email',
                'phone' => 'required',
                'amount_paid' => 'required',
                'sex' => 'required',
                'chapter' => 'required|numeric',
                'passport' => 'required|max:200',
                'payment_type' => 'required',
                'transid' => 'required',
                'hostel_id' => 'required|numeric',
                'password' => 'nullable',
                'payment_type' => 'required',
                'food_id' => 'required',
            ]);

          
            $setting = Setting::first();
            $data['password'] = $password;
            $hostel = Hostel::whereId($request->hostel_id)->first();
            $food = Food::whereId($request->food_id)->first();

            // Make sure the right people are in the right hostel
            if($request->level == 'Participant' || $request->level == 'Alumni' || $request->level == 'Nec'){
            
                if($hostel->type <> $request->sex || $hostel->level <> $request->level){
                    return back()->with('error', 'NOT SAVED. Please check the hostel you are trying to assign to this participant');
                }
            }

            if($request->level == 'Participant' || $request->level == 'Alumni'  || $request->level == 'Medical'  || $request->level == 'Nec' || $request->level == 'Choir' ){
            
                if($food->level <> $request->level || $food->level <> $request->level){

                    return back()->with('error', 'NOT SAVED. Please check the food stand you are trying to assign to this participant');
                }
               
            }

            if($request->level == 'Official' || $request->level == 'Medical' || $request->level == 'Official'){

                if($hostel->level <> $request->level){
                    return back()->with('error', 'NOT SAVED. Please check the hostel you are trying to assign to this participant');
                }
               
            }

            //Fill slot, slot filled, type, others for participant
            if($request->level == 'Participant'){
                $prefix = 'AOP';
                $data['slot'] = 1;
                $data['level'] = 'Participant';
                $data['slot_filled'] = 1;
                $data['type'] = 1;

                if($request->amount_paid < $setting->registration_fee){
                    return back()->with('error', 'Participant cannot pay less than registration fee');
                }

                $data['amount_paid'] = $request->amount_paid;
                
            }

            //Fill slot, slot filled, type, others for moderator
            if($request->level == 'Moderator'){

                if($request->amount_paid < $setting->registration_fee){
                    return back()->with('error', 'Moderator cannot pay less than registration fee');
                }

                $prefix = 'AOP';
                $data['slot'] = $request->amount_paid / $setting->registration_fee;
                $data['level'] = 'Moderator';
                $data['slot_filled'] = 1;
                $data['type'] = 2;
                $data['amount_paid'] = $request->amount_paid;
                
            }

            if($request->level == 'Alumni'){

                if($request->amount_paid < $setting->alumni_fee){
                    return back()->with('error', 'Alumni cannot pay less than alumni minimum fee');
                }

                $prefix = 'AOA';
                $data['slot'] = 1;
                $data['level'] = 'Alumni';
                $data['slot_filled'] = 1;
                $data['type'] = 3;
                $data['amount_paid'] = $request->amount_paid;
                
            }
               
            if($request->level == 'Nec'){

                if($request->amount_paid < $setting->alumni_fee){
                    return back()->with('error', 'Nec cannot pay less than alumni minimum fee');
                }

                $prefix = 'AON';
                $data['slot'] = 1;
                $data['level'] = 'Nec';
                $data['slot_filled'] = 1;
                $data['type'] = 4;
                $data['amount_paid'] = $request->amount_paid;
                
            }

            if($request->level == 'Choir'){

                if($request->amount_paid < $setting->registration_fee){
                    return back()->with('error', 'Nec cannot pay less than registration fee');
                }

                $prefix = 'AOC';
                $data['slot'] = 1;
                $data['level'] = 'Choir';
                $data['slot_filled'] = 1;
                $data['type'] = 5;
                $data['amount_paid'] = $request->amount_paid;
                
            }
            
          
            if($request->level == 'Medical'){

                if($request->amount_paid < $setting->registration_fee){
                    return back()->with('error', 'Medical personnel cannot pay less than registration fee');
                }

                $prefix = 'AOP';
                $data['slot'] = 1;
                $data['level'] = 'Medical';
                $data['slot_filled'] = 1;
                $data['type'] = 1;
                $data['amount_paid'] = $request->amount_paid;
                
            }

            if($request->level == 'Official'){

                if($request->amount_paid < $setting->registration_fee){
                    return back()->with('error', 'Official personnel cannot pay less than registration fee');
                }

                $prefix = 'AOP';
                $data['slot'] = 1;
                $data['level'] = 'Official';
                $data['slot_filled'] = 1;
                $data['type'] = 1;
                $data['amount_paid'] = $request->amount_paid;
                
            }
           $data['passport'] = $passport;
           $data['registration_status'] = 'Complete';
   
            try{  
                $newuser =  User::create($data);
                
                $newuser->update([
                'conference_number' =>'GSF-'.$prefix.'-'.$newuser->id,
                ]);

                //increase hostel allocation
                $hostel->allocation += 1;
                $hostel->save();

                //increase foodstand allocation
                $food->allocation += 1;
                $food->save();

            }catch (\Illuminate\Database\QueryException $ex) {     
                return back()->with('error', $ex);
            }

            return back()->with('message', 'Participant successfully created');


        }
        else if(auth()->user()->level == 'Moderator'){
            $data = $this->validate($request, [
                'name' => 'required|min:3',
                'chapter' => 'required|numeric',
                'email' => 'required|unique:users,email',
                'phone' => 'required',
                'sex' => 'required',
                'passport' => 'required|max:200',
                'password' => 'nullable',
                'payment_type' => 'required',
                'transid' => 'required',
            ]);

            //Check if moderator has slots available
            if(auth()->user()->slot_filled >= auth()->user()->slot){
                return back()->with('warning', 'You can no longer add participants because you have used up all available slots');
            }

            //Assign hostel and food stand here
            

            try{  
                $newuser = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'phone' => $data['phone'],
                'sex' => $data['sex'],
                'chapter' => $data['chapter'],
                'passport' => $passport,
                'type' => 1,
                'level' => 'Participant',
                'uploaded_by' => auth()->user()->id,
                'password' =>  $password,
                'amount_paid' => Setting::select('registration_fee')->first()->value('registration_fee'),
                'registration_status' => 'Complete'
                ]);
    
                $newuser->update([
                'conference_number' =>'GSF-AOP-'.$newuser->id,
                ]);

                auth()->user()->update([
                'slot_filled' => auth()->user()->slot_filled + 1,
                ]);

            }catch (\Illuminate\Database\QueryException $ex) {     
                return back()->with('error', $ex);
            }

            return back()->with('message', 'Participant successfully created, you have '.(auth()->user()->slot - auth()->user()->slot_filled). ' participant slot(s) left');
        
            
        } return abort(404);

    }
    public function show($id)
    {
        //
    }

    public function edit(User $user)
    {
     
        $chapters = Chapter::all();
        
        $hostels = Hostel::all();
        $foods = Food::all();

       if(auth()->user()->level == 'Admin'){         
            return view('admin.users.edit', compact('user', 'hostels', 'foods', 'chapters'));
       }

       if(auth()->user()->level == 'Moderator'){
           if($user->sex == 'Female'){
               $hostels = $hostels->where('type', 'Female');;
           }

           if($user->sex == 'Male'){
               $hostels = $hostels->where('type', 'Male');
           }
           
            return view('moderator.users.edit', compact('user', 'hostels', 'foods', 'chapters'));
       }
       return abort(404);

    }

    public function editChoir($id)
    {
        $user = User::findorfail($id);

        $chapters = Chapter::all();
        $hostels = Hostel::all();
        $foods = Food::all();

       if(auth()->user()->level == 'Admin'){         
            return view('admin.choir.edit', compact('user', 'hostels', 'foods', 'chapters'));
       }
    }

    public function editMedic($id)
    {
        $user = User::findorfail($id);

        $chapters = Chapter::all();
        $hostels = Hostel::all();
        $foods = Food::all();

       if(auth()->user()->level == 'Admin'){         
            return view('admin.medic.edit', compact('user', 'hostels', 'foods', 'chapters'));
       }
    }


    public function editAlumni($id)
    {
        $user = User::findorfail($id);

        $chapters = Chapter::all();
        $hostels = Hostel::all();
        $foods = Food::all();

       if(auth()->user()->level == 'Admin'){         
            return view('admin.alumni.edit', compact('user', 'hostels', 'foods', 'chapters'));
       }
    }

    public function update(Request $request, User $user)
    {
    
        $this->validate($request, [
			'name' => 'required',
			'phone' => 'required',
			'sex' => 'in:Male,Female',
			'chapter' => 'required|exists:chapters,id',
			'passport' => 'nullable|max:200'
        ]);
        
        if($request->has('passport')){
            if(isset($user->passport)){
                unlink( $user->passport);
            }

            $imgName = date('Y-m-d-His') . $request->passport->getClientOriginalName();
           
            $passport = Image::make($request->passport)->resize(500, 500);
            $passport->save('frontend/passports'.'/'.$imgName);               
            $passport = 'frontend/passports/'.$imgName;
            
        }else{
             $passport  = $user->passport;
        }
       
        if(auth()->user()->level == 'Admin'){
        //handle password
            if($request['password']){
                $password = Hash::make($request['password']);
            }else {
                $password = $user->password;
            }

            //Decrease allocation in previous hostel
            if($request->hostel_id != $user->hostel_id){

            }
            //Decrease allocation in previous foodstand
            try{
                $user->name = $request->name;
                $user->email = $request->email;
                $user->phone = $request->phone;
                $user->sex = $request->sex;
                $user->chapter = $request->chapter;
                $user->payment_type = $request->payment_type;
                $user->transid = $request->transid;
                $user->hostel_id = $request->hostel_id;
                $user->password = $password;
                $user->passport = $passport;
                $user->save();
               
            }catch (\Illuminate\Database\QueryException $ex) {
                    $error = $ex->getMessage();        
                    return back()->with('error', $ex);
                }

            return redirect()->back()->with('message', 'Update successful!');
        }else if(auth()->user()->level == 'Moderator'){
            //Moderator updates here
            //Handle Password
            //Handle Passport
            //Assign hostel
            //Assign Food stand
            //Save other user details
        }
        
        return abort(404);

    }
    
    
        public function destroy($id)
        {
            //stop from accidentally deleted self
            if(auth()->user()->id == $id){
                return back()->with('warning', 'I\'m sorry but You cannot delete your self');
            }
            $hostel = Hostel::all();
            $foodstand = Food::all();
            $user= User::withTrashed()->where('id', $id)->firstOrFail();

            if($user->trashed()){
                if(isset($user->passport)){
                    unlink( $user->passport);
                }
                $user->forceDelete();

                return back()->with('message', 'Participant has been deleted forever');
            
            } else {
                
                
                //Get user hostel
                if($user->hostel){
                    $hostel = $hostel->where('id', $user->hostel->id)->first();
                    $hostel->allocation -= 1;
                    $hostel->save();
                };

                //Get user foodstand
                if($user->food){
                    $food = $foodstand->where('id', $user->food->id)->first();
                    $food->allocation -= 1;
                    $food->save();
                };

                $user->delete();

                auth()->user()->slot_filled -= 1;
                auth()->user()->save();

                return back()->with('message', 'Record has been deleted!');
            }
        }

		public function export(){
			$user = new User();
		$tableColumns = Schema::getColumnListing('users');
		$columnsForeign = [
			'hostel_id' => 'hostel_name',
			'food_id' => 'food_name',
			'chapter' => 'chapter'
		];

		foreach ($user->getHidden() as $key => $value) {
			if (($k = array_search($value, $tableColumns)) !== false) {
				unset($tableColumns[$k]);
			}
		}
		foreach($columnsForeign as $key => $value){
				if(in_array($key, $tableColumns)){
					$tableColumns[array_search($key, $tableColumns)] = $value;
				}
			}
		return Excel::download(new UsersExport($tableColumns), 'users_exported.xlsx');
	}

	public function import(Request $request)
	{
		$data = $this->validate($request, [
			'file' => 'required|mimes:xlsx,csv',
		]);


		try {
			// Excel::import(new ImportAllocations, request()->file('file'));
		} catch (\Illuminate\Database\QueryException $ex) {
			$error = $ex->getMessage();
			return back()->with('error', $error);
		}
		return redirect(route('allocation.index'))->with('message', 'Data has been imported succesfully');
	}

}
