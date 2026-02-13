<?php

namespace App\Http\Controllers;

use App\Models\Zone;
use App\Models\Field;
use App\Models\Chapter;
use App\Models\Payment;
use App\Models\TempUser;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Models\ConferenceEdition;
use App\Http\Controllers\Controller;
use App\Http\Controllers\PaymentController;

class TransactionController extends Controller
{
    public function transactions(Request $request, $edition)
	{
        $chapters = Chapter::get();
        $zones = Zone::get();
        $fields = Field::get();
        $edition = ConferenceEdition::find($edition);

        $mainQuery = Transaction::with(['user', 'allocationFields'])->where('conference_edition_id', $edition->id)->latest();

        if($request->filled('transid')){
            $mainQuery = $mainQuery->where('transid', $request->transid);
        }

        if($request->filled('name')){
            $mainQuery = $mainQuery->whereHas('user', function($q) use ($request){
                $q->where('name', 'like', '%'.$request->name.'%');
            });
        }

        if($request->filled('email')){
            $mainQuery = $mainQuery->where('email', $request->email);
        }

        if($request->filled('phone')){
            $mainQuery = $mainQuery->where('phone', $request->phone);
        }

        if($request->filled('from_date')){
            $mainQuery = $mainQuery->whereDate('created_at', '>=', $request->from_date);
        }

        if($request->filled('to_date')){
            $mainQuery = $mainQuery->whereDate('created_at', '<=', $request->to_date);
        }

        if($request->filled('conference_plan_id')){
            $mainQuery = $mainQuery->where('conference_plan_id', $request->conference_plan_id);
        }

        if($request->filled('registration_status')){
            $mainQuery = $mainQuery->where('registration_status', $request->registration_status);
        }

        if($request->filled('field_id')){
            $mainQuery = $mainQuery->whereHas('allocationFields', function($q) use ($request){
                $q->where('key', 'field_id')->where('value', $request->field_id);
            });
        }

        if($request->filled('zone_id')){
            $mainQuery = $mainQuery->whereHas('allocationFields', function($q) use ($request){
                $q->where('key', 'zone_id')->where('value', $request->zoneid);
            });
        }

        if($request->filled('status')){
            if($request->status_filter == 'pending'){
                $mainQuery = $mainQuery->whereIn('status', ['Pending','Initiated']);
            }else{
                $mainQuery = $mainQuery->where('status', $request->status);
            }
        }

        $countQuery = clone $mainQuery;
        $count = $countQuery->count();

        if ($request->input('action') === 'export') {
            $transactions = $mainQuery->get();
        } else {
            $transactions = $mainQuery->paginate(50);
        }


        return view('conference_management.admin.editions.transactions', compact('transactions', 'edition', 'count', 'chapters', 'zones', 'fields'));
	}

    public function show(Request $request, $id){

        $tempusers = Transaction::find($id);

        $tempusers->delete();

        return back()->with('message', 'Delete succesful');
    }

    public function destroy($id){

    }

    public function bulkAction(Request $request){
        $request->validate([
            'action' => 'required',
            'transactions' => 'required|array'
        ]);
        $action = $request->input('action');
        $ids = $request->input('transactions');

        if ($action == 'delete') {
            Transaction::whereIn('id', $ids)->where('status', '!=', 'Completed')->delete();
            $message = 'Transaction(s) deleted successfully';
        }elseif ($action == 'resolve') {
            $transactions = Transaction::whereIn('id', $ids)->where('status', '!=', 'Completed')->get();

            foreach($transactions->chunk(10) as $transactionChunk){
                foreach($transactionChunk as $transaction){
                    $res = $this->resolveTransaction($transaction, false);
                }
            }
            $message = 'Transactions resolved successfully';
        }else{
            $message = 'Invalid action';

            if($request->ajax()){
                return response()->json(['message' => $message]);
            }

            return back()->with('error', $message);
        }

        if($request->ajax()){
            return response()->json(['message' => $message]);
        }

        return back()->with('message', $message);
    }

    // public function update(Request $request, TempUser $tempuser){
    //     $exists = Payment::where('conference_edition_id', $tempuser->conference_edition_id)->where('transid', $request->transid)->exists();

    //     if ($exists) {
    //         return back()->with('error', 'Transaction ID already exists.');
    //     }

    //     $tempuser->update([
    //         'transid'=>$request->transid,
    //     ]);

    //     return back()->with('message','Update Successful');
    // }
    public function resolveTransaction($transaction, $isCron){
        $request = request(); // get the current request
        $request->merge([
            'admin' => 'admin'
        ]);

        $req = new PaymentController();
        $response = $req->handleGatewayCallback($request, $transaction->transid);
        // if resolved

        // resolved_by = auth()->user() ? auth()->user()->id : null;
        // resolved_at = now();
        if(isset($response) && !empty($response)){
            $response = json_encode($response);
            $transaction->update(['gateway_response' => $response]);

            if($response->status == 'abandoned'){
                $transaction->update(['status' => $response->status]);
            }
        }

        if($isCron){
            return response()->json('Done');
        }
    }

    public function requery(Request $request, $id, $bypassAuth = false){
        if ((auth()->user() && auth()->user()->role == 1 && auth()->user()->conference_role == 'superadmin') || $bypassAuth == true){
            $temp = Transaction::where('id',$id)->first();

            $request->request->add(['reference' => $request->reference]);
            $req = new \App\Http\Controllers\PaymentController();
            $request['setting'] = ConferenceEdition::where('id', $temp->conference_edition_id)->first();

            $response = $req->handleGatewayCallback($request, 'admin');
            $status = $response->status ?? 'Failed';

            // if(isset($response) && !empty($response)){
            //     $response = json_encode($response);
            //     $temp->update(['gateway_response' => $response]);
            // }

            if($status == 'abandoned'){
                $temp->update(['status' => $status]);

                if(isset($request->cron)){
                    return response()->json('An error occured');
                }
                return back()->with('error','Payment Status: '.$status);
            }

            if($status == 'Failed'){
                if (isset($request->cron)) {
                    return response()->json('An error occured');
                }

                return back()->with('error','Payment Status: '.$status);
            }else{
                if (isset($request->cron)) {
                    return response()->json('Done');
                }
                return back()->with('message','Payment Status: '.$status);
            }
		}
    }

    public function setAndVerifyReference(Request $request, $reference, $temp_id){
        $temp = Transaction::where('id',$temp_id)->first();
        if($temp) $temp->update(['transid' => $reference]);

        return redirect(route('tempusers.requery', ['id' => $temp_id, 'reference' => $reference, 'bypass' => true]))->with('message','Reference set successfully');
    }

    public function requeryMultiple(Request $request){
        if (auth()->user()->role == 1 && auth()->user()->conference_role == 'superadmin'){
            $res = [];
            if($request->has('obj')){
                foreach($request->obj as $obj){
                    $temp = Transaction::where('transid',$obj)->first();

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
        $temp = Transaction::find($id);
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
        $temp = Transaction::find($id);
        if(!$temp){
            return back()->with('message','Record not found');
        }

        if($temp->location != 'On Site'){
            return back()->with('error', 'This is not an On Site payment');
        }

        $request->request->add(['reference' => $temp->transid]);

        $req = new \App\Http\Controllers\PaymentController();
        $response = $req->handleGatewayCallback($request, 'admin','','onsite-confirmed');

        if($response){
            return back()->with('message','On Site Registration Succesful');
        }else{
            return back()->with('error','Transaction not found');

        }
    }



}
