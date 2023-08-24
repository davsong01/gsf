<?php

namespace App\Http\Controllers;

use App\Models\Nec;
use Illuminate\Http\Request;

class NecController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $nec = Nec::orderBy('order', 'ASC')->get();
        $count = 1;
        return view('admin.nec.index')->with('nec', $nec)->with('count', $count);

    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.nec.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $this->validateUser($request);
        if ($request['passport']) {
            $passport = $this->uploadImage($request->passport, 'frontend/passports', 400, 400);

            $data['passport'] = $passport;
        }

        try {
            Nec::updateOrCreate($data);
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }

        return redirect(route('nec.index'))->with('message', 'Operation Successful');

    }

    /**
     * Display the specified resource.
     */
    public function show(Nec $nec)
    {
       
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Nec $nec)
    {
        return view('admin.nec.edit')->with('nec',$nec);
        
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Nec $nec)
    {
        $data = $this->validateUser($request);
        if ($request['passport']) {
            $passport = $this->uploadImage($request->passport, 'frontend/passports', 500, 500);

            $data['passport'] = $passport;
        }

        try {
            $nec->update($data);
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with('message', 'Operation Successful');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Nec $nec)
    {
        
    }

    public function delete($id){
        $nec = Nec::find($id);
        $nec->delete();
        return redirect(route('nec.index'))->with('message', 'Delete Successful');
    }

    private function validateUser($request)
    {
        $data = $this->validate($request, [
            "name" => "required",
            "email" => "required",
            "phone" => "required",
            "office" => "required",
            "bday" => "nullable",
            "order" => "required|numeric",
            "gender" => "required",
            "passport" => "nullable",
        ]);

        return $data;
    }

}
