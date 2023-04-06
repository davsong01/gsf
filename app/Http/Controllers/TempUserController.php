<?php

namespace App\Http\Controllers;

use App\TempUser;
use App\ConferenceEdition;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class TempUserController extends Controller
{
    public function index(Request $request)
	{
		$count = 1;
        $edition = ConferenceEdition::with(['payments', 'donations'])->find($request->edition);

		if (auth()->user()->role == 1) {
			$participants = TempUser::with('campus')->where('conference_edition_id', $edition->id)->whereIn('status',['Initiated','abandoned'])->orderBy('created_at', 'desc')->get();
            
			return view('admin.temp_users.index', compact('participants', 'count','edition'));
        }
       
        return abort(404);
	}

    public function show(Request $request, $id){
      
        $tempusers = TempUser::find($id);

        $tempusers->delete();

        return back()->with('message', 'Delete succesful');
    }

    public function destroy($id){
       
    }

    public function update(Request $request, TempUser $tempuser){
        $tempuser->update([
            'transid'=>$request->transid,
        ]);

        return back()->with('message','Update Successful');
    }

    public function requery($id, Request $request){
        if (auth()->user()->role == 1 && auth()->user()->conference_role == 'superadmin'){
            $temp = TempUser::where('id',$id)->first();
            
            $request->request->add(['reference' => $request->reference]);
            $req = new \App\Http\Controllers\PaymentController();
            $response = $req->handleGatewayCallback($request, 'admin');
            $status = $response->status ?? 'Failed';
           
            if(isset($response) && !empty($response)){
                $response = json_encode($response);
                $temp->update(['gateway_response' => $response]);
            }

            if($status == 'abandoned'){
                $temp->update(['status' => $status]);
                return back()->with('error','Payment Status: '.$status);
            }

            if($status == 'Failed'){
                return back()->with('error','Payment Status: '.$status);
            }else{
                return back()->with('message','Payment Status: '.$status);
            }
		}
    }

    public function requeryMultiple(Request $request){
        if (auth()->user()->role == 1 && auth()->user()->conference_role == 'superadmin'){
            $res = [];
            if($request->has('obj')){
                foreach($request->obj as $obj){
                    $temp = TempUser::where('transid',$obj)->first();
                    
                    $request->request->add(['reference' => $obj]);
                    
                    $req = new \App\Http\Controllers\PaymentController();
                    $response = $req->handleGatewayCallback($request, 'admin');
                    $status = $response->status ?? 'Unknown';
                
                    if(isset($response) && !empty($response)){
                        $response = json_encode($response);
                        $temp->update(['gateway_response' => $response]);

                        if($status == 'abandoned'){
                            $temp->update(['status' => $status]);
                        }
                    }

                    $res[] = [
                            'reference' => $obj,
                            'status' => $status,
                        ];
                }
            }
           
            return response()->json($res);
		}
    }

    public function confirmTransfer(Request $request, $id){
        $temp = TempUser::find($id);
        if(!$temp){
            return back()->with('message','Record not found');
        }

        $request->request->add(['reference' => $temp->transid]);
                    
        $req = new \App\Http\Controllers\PaymentController();
        $response = $req->handleGatewayCallback($request, 'admin','transfer-confirmed');
  
        if($response){
            return back()->with('message','Operation Succesful');
        }else{
            return back()->with('error','Transaction not found');

        }
    }

    public function confirmOnSiteTransfer(Request $request, $id){
        $temp = TempUser::find($id);
        if(!$temp){
            return back()->with('message','Record not found');
        }
        
        if($temp->location != 'On Site'){
            return back()->with('error', 'This is not an On Site payment');
        }

        $request->request->add(['reference' => $temp->transid]);
                    
        $req = new \App\Http\Controllers\PaymentController();
        $response = $req->handleGatewayCallback($request, 'admin','','onsite-confirmed');
        dd($response, 'response');
        if($response){
            return back()->with('message','Operation Succesful');
        }else{
            return back()->with('error','Transaction not found');

        }
    }

    

}
