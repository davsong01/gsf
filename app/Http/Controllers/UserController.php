<?php

namespace App\Http\Controllers;
use DB;
use App\Food;
use App\User;
use App\Hostel;
use App\Exports\UsersExport;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Facades\Excel;
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
            $participants = User::with(['hostel', 'moderator'])->whereLevel('Participant')->orwhere('level', 'Moderator')->orderBy('created_at', 'desc')->get();
            
        return view('admin.users.index', compact('participants', 'count'));
        }return abort(404);
        
    }

    public function create()
    {
        if(auth()->user()->permission == 2){
            return view('admin.users.create');
        }return back(404);
        
    }

    public function store(Request $request)
    {
        
        if(auth()->user()->permission == 2){
            $data = $this->validate($request, [
                'name' => 'nullable|min:3',
                'username' => 'required|min:3|max:200',
                'email' => 'required|unique:users,email',
                'verify' => 'required',
                'phone' => 'nullable',
                'password' => 'required|min:8',
                'permission' => 'required|numeric',
            ]);

            try{  
                User::create([
                'name' => $request['name'],
                'username' => $request['username'],
                'email' => $request['email'],
                'email_verified_at' => $request['verify'],
                'phone' => $request['phone'],
                'permission' => $request['permission'],
                'password' =>  Hash::make($request['password']),
                ]);
    
            }catch (\Illuminate\Database\QueryException $ex) {
                $error = $ex->getMessage();        
                return back()->with('error', $error);
            }
        
            return back()->with('message', 'User successfully created');
        }return back(404);

    }
    public function show($id)
    {
        //
    }

    public function edit(User $user)
    {
        $chapters = Chapter::all();

       if(auth()->user()->level == 'Admin'){
           if($user->sex == 'Female'){
               $hostels = Hostel::whereType('Female')->whereLevel('Participant')->get();
           }

           if($user->sex == 'Male'){
               $hostels = Hostel::whereType('Male')->whereLevel('Participant')->get();
           }
            
            $foods = Food::all();
            return view('admin.users.edit', compact('user', 'hostels', 'foods', 'chapters'));
       }
       return abort(404);

        
    }

    public function update(Request $request, User $user)
    {
        dd($request->all());
        if(auth()->user()->level == 'Admin'){
        //handle password
            if($request['password']){
                $request['password'] = Hash::make($request['password']);
            }else $request['password'] = $user->password;

            try{
                $user->update($request->all());
            }catch (\Illuminate\Database\QueryException $ex) {
                    $error = $ex->getMessage();        
                    return back()->with('error', $ex);
                }

            return redirect()->back()->with('message', 'Update successful!');
        }return abort(404);

    }
    
    
    public function destroy($id)
    {
        $user= User::findOrFail($id);

        $user->delete();

        return back()->with('message', 'Record has been deleted forever!');
    }

		public function export(){
			
			$user = new User();
			$tableColumns = Schema::getColumnListing('users');

			foreach($user->getHidden() as $key => $value){
				if(($k = array_search($value, $tableColumns)) !== false){
				unset($tableColumns[$k]);
				}
			}
			
			return Excel::download(new UsersExport($tableColumns), 'users_exported.xlsx');
		}
    
}
