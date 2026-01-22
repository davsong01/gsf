<?php

namespace App\Http\Controllers;

use App\Models\Nec;
use App\Models\ArchivedNec;
use App\Models\Stakeholder;
use Illuminate\Http\Request;
use App\Models\StakeholderDesignation;

class NecController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $nec = StakeholderDesignation::with(['stakeholder' => function($q) {
            $q->where('status', 'active')
            ->whereIn('role_id', [1,2,3,4,6,7]);
        }])
        ->where('type', 'nec')
        ->orderBy('order')

        ->get();

        return view('admin.nec.index')->with('nec', $nec);
    }

    public function archivedNec()
    {
        $nec = ArchivedNec::orderBy('order', 'ASC')->get();
        $count = 1;
        return view('admin.nec.archived_nec_index')->with('nec', $nec)->with('count', $count);
    }



    public function archiveNec(Request $request){
        $from = $request->from;
        $to = $request->to;

        // if($from < $to){
        //     return back()->with('error', 'From cannot be lesser');
        // }

        // if($from == date('Y')){
            $nec = Nec::where('tenure', $from .'/'. $from+2)->get();
            // dd($nec, $from . '/' . $from + 2);
            $count = 0;
            // dd($nec.$from . '/' . $from + 2);
            foreach($nec as $n){
                $count = $count+1;
                $data = $n->toArray();
                $data['tenure'] = $to.'/'.$to + 2;
                unset($data['id']);
                unset($data['created_at']);
                unset($data['updated_at']);

                if(!empty($data['name'])){
                    ArchivedNec::create($data);
                }
                // $n->delete();
            }
        // }else{
        //     $nec = ArchivedNec::where('tenure', $from)->get();
        //     foreach ($nec as $n) {
        //         $n->update(['tenure' => $to]);
        //     }
        // }
            // dd($nec);
        return back()->with('message', $count. ' Archive successfully moved');
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
