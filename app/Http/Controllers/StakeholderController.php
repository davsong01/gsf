<?php

namespace App\Http\Controllers;

use App\Zone;
use App\Field;
use App\Chapter;
use App\Stakeholder;
use Illuminate\Http\File;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;

class StakeholderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    { 
        $count = 1;
        if (auth()->user()->level == 'Admin') {

			$stakeholders = Stakeholder::orderBy('created_at', 'desc')->get();
			return view('admin.stakeholders.index', compact('stakeholders', 'count'));
			
		}else{
            return abort(404);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $zones = Zone::all();
        $fields = Field::all();
        $chapters = Chapter::all();
        return view('admin.stakeholders.create', compact('zones', 'fields', 'chapters'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = $this->validate($request, [
            'signature' => 'required|mimes:jpeg,jpg,png',
            'name' => 'required',
            'phone' => 'required',
            'email' => 'required|unique:stakeholders,email',
            'field_id' => 'required|numeric',
            'zone_id' => 'required|numeric',
            'chapter_id' => 'required|numeric',
            'role' => 'required',
            'password' => 'nullable',
            'day' => 'required|numeric',
            'month' => 'required|numeric',
            'year' => 'nullable|numeric'
        ]);

        //Handle password
        if ($request['password']) {
			$password = Hash::make($request['password']);
		} else {
			$password = Hash::make($request['12345@GSF2021']);
		}

        $filename = date('d-M-Y-s') . '-' . pathinfo($request->signature->getClientOriginalName(), PATHINFO_FILENAME);
        $extension = $request->signature->getClientOriginalExtension();
        Storage::disk('uploads')->putFileAs('signatures', new File($request->signature->path()), $filename.'.'.$extension);
       
        $stakeholder = Stakeholder::create([
            'signature' => $filename . '.' . $extension,
            'name' => $data['name'],
            'phone' => $data['phone'],
            'email' => $data['email'],
            'field_id' => $data['field_id'],
            'zone_id' => $data['zone_id'],
            'chapter_id' => $data['chapter_id'],
            'role' => $data['role'],
            'password' => $password,
            'day' => $data['day'],
            'month' => $data['month'],
            'year' => $data['year'],
        ]);

        return redirect(route('staff.index'))->with('message', 'Operation Successful'); 

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Stakeholder  $stakeholder
     * @return \Illuminate\Http\Response
     */
    public function show(Stakeholder $stakeholder)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Stakeholder  $stakeholder
     * @return \Illuminate\Http\Response
     */
    public function edit(Stakeholder $staff)
    {
        $fields = Field::all();
        $stakeholder = $staff;
        $zones = Zone::all();
        $chapters = Chapter::all();
        
        return view('admin.stakeholders.edit', compact('stakeholder', 'fields', 'zones', 'chapters'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Stakeholder  $stakeholder
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Stakeholder $staff)
    {
         $stakeholder = $staff;
        if ($request['password']) {
			$password = Hash::make($request['password']);
		} else {
			$password = Hash::make($request['12345@GSF2021']);
		}

        if ($request['signature']) {
            if (file_exists(base_path() . '/uploads/signatures' . '/' . $stakeholder->signature))
            unlink( base_path() . '/uploads/signatures' . '/' . $stakeholder->signature);

            $filename = date('d-M-Y-s') . '-' . pathinfo($request->signature->getClientOriginalName(), PATHINFO_FILENAME);
            $extension = $request->signature->getClientOriginalExtension();
            Storage::disk('uploads')->putFileAs('signatures', new File($request->signature->path()), $filename.'.'.$extension);
            $signature = $filename . '.' . $extension;
        }else{
            $signature = $stakeholder->signature;
        }
        
        $stakeholder->signature = $signature;
        $stakeholder->name = $request->name;
        $stakeholder->phone = $request->phone;
        $stakeholder->email = $request->email;
        $stakeholder->field_id = $request->field_id;
        $stakeholder->zone_id = $request->zone_id;
        $stakeholder->chapter_id = $request->chapter_id;
        $stakeholder->role = $request->role;
        $stakeholder->password = $password;
        $stakeholder->day = $request->day;
        $stakeholder->month = $request->month;
        $stakeholder->year = $request->year;

        $stakeholder->save();

        return redirect(route('staff.index'))->with('message', 'Update Successfull');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Stakeholder  $stakeholder
     * @return \Illuminate\Http\Response
     */
    public function destroy(Stakeholder $stakeholder)
    {
        //
    }
}
