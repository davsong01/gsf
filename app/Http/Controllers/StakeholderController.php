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
        if (auth()->user()->role == 1) {

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
        $fields = Field::all();
        $zones = Zone::all();
        $chapters = Chapter::all();
        $months = $this->getMonths();
        $portfolios = $this->getPortfolios();

        return view('admin.stakeholders.create', compact('zones', 'fields', 'chapters', 'months', 'portfolios'));
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
            'signature' => 'nullable|mimes:jpeg,jpg,png',
            'name' => 'required',
            'phone' => 'required',
            'email' => 'required',
            'field_id' => 'nullable|numeric',
            'zone_id' => 'nullable|numeric',
            'chapter_id' => 'nullable|numeric',
            'role' => 'required',
            'password' => 'nullable',
            'day' => 'required|numeric',
            'month' => 'required|numeric',
            'year' => 'nullable|numeric',
            'portfolio' => 'nullable'
        ]);
       
        //Handle password
        if ($request['password']) {
			$password = Hash::make($request['password']);
		} else {
			$password = Hash::make($request['12345@GSF2021']);
		}
        if ($request->has('signature')) {
           $filename = $this->uploadImage($request->signature, 'sign', 400, 400);
        }
        
        $stakeholder = Stakeholder::create([
            'signature' => $filename ?? null,
            'name' => $data['name'],
            'phone' => $data['phone'],
            'email' => $data['email'],
            'field_id' => $data['field_id'],
            'zone_id' => $data['zone_id'],
            'chapter_id' => $data['chapter_id'],
            'role' => $data['role'],
            'password' => $password,
            'day' => $data['day'],
            'day' => $data['day'],
            'month' => $data['month'],
            'portfolio' => $data['portfolio'],
        ]);
        // dd($data, $stakeholder);
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
        $months = $this->getMonths();
        $portfolios = $this->getPortfolios();

        return view('admin.stakeholders.edit', compact('stakeholder', 'fields', 'zones', 'chapters', 'portfolios', 'months'));
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

        if($request['role'] == 'President'){
            $stakeholder->chapter_id = $request->chapter_id;
            $stakeholder->portfolio = null;
            $stakeholder->zone_id = null;
            $stakeholder->field_id = null;
        }

        if($request['role'] == 'Zonal Pastor'){
            $stakeholder->zone_id = $request->zone_id;
            $stakeholder->field_id = Zone::whereId($request->zone_id)->value('field_id');
            $stakeholder->chapter_id = null;
            $stakeholder->portfolio = null;
        }

        if($request['role'] == 'Field Pastor'){
            $stakeholder->field_id = $request->field_id;
            $stakeholder->zone_id = null;
            $stakeholder->chapter_id = null;
            $stakeholder->portfolio = null;
        }

        if($request['role'] == 'Portfolio'){
            $stakeholder->portfolio = $request->portfolio;
            $stakeholder->chapter_id = null;
            $stakeholder->field_id = null;
            $stakeholder->zone_id = null;

        }

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
    public function destroy(Stakeholder $id)
    {
        $this->deleteImage($id->signature);
        $this->deleteImage($id->avatar);
        $id->delete();

        return back()->with('message', 'Operation succesful!');
    }
}
