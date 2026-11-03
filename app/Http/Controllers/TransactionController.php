<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Controllers\PaymentController;
use App\Models\Chapter;
use App\Models\ConferenceEdition;
use App\Models\Field;
use App\Models\Payment;
use App\Models\TempUser;
use App\Models\Transaction;
use App\Models\Zone;
use App\Services\ExcelService;
use App\Services\UserService;
use Illuminate\Http\Request;
use PDF;

class TransactionController extends Controller
{
    public function transactions(Request $request, $edition)
	{
        $chapters = Chapter::get();
        $zones = Zone::get();
        $fields = Field::get();
        $edition = ConferenceEdition::find($edition);

        $mainQuery = Transaction::with(['user', 'allocationFields', 'moderator'])->where('conference_edition_id', $edition->id)->latest();

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

         if($request->filled('registration_user_type')){
            $mainQuery = $mainQuery->where('registration_user_type', $request->registration_user_type);
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
            if($request->status == 'pending'){
                $mainQuery = $mainQuery->whereIn('status', ['Pending','Initiated']);
            }else{
                $mainQuery = $mainQuery->where('status', $request->status);
            }
        }

        $countQuery = clone $mainQuery;
        $count = $countQuery->count();

        if ($request->input('action') === 'export') {
            $transactions = $mainQuery->get();
            $exportData = UserService::exportConferenceParticipantsData($transactions, $edition);

            return ExcelService::download(
                $exportData['data']->toArray(),
                array_values($exportData['headers']),
                'transactions.xlsx'
            );
        }else {
            $transactions = $mainQuery->paginate(50);
        }

        return view('conference_management.admin.editions.transactions', compact('transactions', 'edition', 'count', 'chapters', 'zones', 'fields'));
	}

    public function showSingleTransaction($editionId, $transactionId)
    {
        $edition = ConferenceEdition::findOrFail($editionId);
        $transaction = Transaction::with([
            'user',
            'paymentprovider',
            'conferenceplan',
            'allocationFields',
            'resolvedBy',
        ])
            ->where('conference_edition_id', $edition->id)
            ->findOrFail($transactionId);

        $isOnlinePayment = strtolower($transaction->location ?? 'online') !== 'on site'
            && $transaction->paymentprovider
            && in_array($transaction->paymentprovider->slug, ['paystack', 'monnify'], true);

        return view('conference_management.admin.editions.transaction-show', compact(
            'edition',
            'transaction',
            'isOnlinePayment'
        ));
    }

    public function downloadReceipt($editionId, $transactionId)
    {
        $edition = ConferenceEdition::findOrFail($editionId);
        $transaction = Transaction::with(['conferenceplan', 'paymentprovider', 'allocationFields'])
            ->where('conference_edition_id', $edition->id)
            ->findOrFail($transactionId);

        $pdf = PDF::loadView('conference_management.admin.editions.transaction-receipt', compact(
            'edition',
            'transaction'
        ))->setPaper('a4', 'portrait');

        $filename = 'GSF-transaction-' . preg_replace('/[^A-Za-z0-9_-]/', '-', $transaction->transid) . '.pdf';

        return $pdf->download($filename);
    }

    public function requeryProvider(Request $request, $editionId, $transactionId)
    {
        abort_unless(auth()->user() && auth()->user()->conference_role === 'superadmin', 403);

        $edition = ConferenceEdition::findOrFail($editionId);
        $transaction = Transaction::with('paymentprovider')
            ->where('conference_edition_id', $edition->id)
            ->findOrFail($transactionId);

        if (strtolower($transaction->location ?? 'online') === 'on site' || !$transaction->paymentprovider) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Provider requery is only available for online payments.'], 422);
            }
            return back()->with('error', 'Provider requery is only available for online payments.');
        }

        if (!in_array($transaction->paymentprovider->slug, ['paystack', 'monnify'], true)) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'This payment provider does not support transaction requery.'], 422);
            }
            return back()->with('error', 'This payment provider does not support transaction requery.');
        }

        $verification = app(PaymentController::class)->verify($transaction) ?? [];
        $verified = (bool) ($verification['status'] ?? false);
        $querySuccess = (bool) ($verification['query_success'] ?? false);
        $providerResponse = $verification['message'] ?? null;
        $storedResponse = $providerResponse === null
            ? ['message' => $verification['verification_message'] ?? 'Provider returned no response.']
            : json_decode(json_encode($providerResponse), true);

        $transaction->update([
            'api_response' => $storedResponse,
            'provider_reference' => $verification['provider_reference'] ?? $transaction->provider_reference,
        ]);

        \Log::info('Administrator requeried a conference payment provider.', [
            'transaction_id' => $transaction->id,
            'transaction_reference' => $transaction->transid,
            'payment_provider_id' => $transaction->payment_provider_id,
            'provider_query_success' => $querySuccess,
            'transaction_verified' => $verified,
            'expected_total_amount' => $transaction->total_amount,
        ]);

        $feedback = $querySuccess
            ? ($verified
                ? 'Provider query succeeded and confirms the payment details.'
                : 'Provider query succeeded, but the returned transaction does not verify as a completed payment.')
            : 'Provider query failed: ' . ($verification['verification_message'] ?? 'No successful response was received.');

        if ($request->expectsJson()) {
            return response()->json([
                'success' => $querySuccess,
                'query_success' => $querySuccess,
                'transaction_verified' => $verified,
                'message' => $feedback,
                'provider_response' => $storedResponse,
            ], $querySuccess ? 200 : 502);
        }

        return back()->with($querySuccess ? 'message' : 'error', $feedback);
    }

    public function resolveSingleTransaction(Request $request, $editionId, $transactionId)
    {
        abort_unless(auth()->user() && auth()->user()->conference_role === 'superadmin', 403);

        $edition = ConferenceEdition::findOrFail($editionId);
        $transaction = Transaction::where('conference_edition_id', $edition->id)->findOrFail($transactionId);

        if ($transaction->status === 'Complete') {
            return back()->with('message', 'This transaction is already complete.');
        }

        $this->resolveTransaction($transaction, false);
        $transaction->refresh();

        return $transaction->status === 'Complete'
            ? back()->with('message', 'Transaction resolved and registration completed successfully.')
            : back()->with('error', 'The provider did not confirm payment. The transaction remains pending.');
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
                    $this->resolveTransaction($transaction, false);
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


    public function resolveTransaction($transaction, $isCron){
        $request = request(); // get the current request
        $request->merge([
            'admin' => 'admin'
        ]);

        $adminId = $isCron ? 0 : (auth()->user()->id ?? 0);

        $req = new PaymentController();
        $response = $req->handleGatewayCallback($request, $transaction->transid);
        // dd($response );
        if(isset($response->verification_response) && $response->verification_response->status == true){
            $transaction->update([
                'resolved_at' => now(),
                'resolved_by' => $adminId,
            ]);
        }

        return $response;
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
                $temp->update([
                    'status' => $status,
                    'fix_status' => 'closed'
                ]);

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

    public function updateTransaction(Request $request, $id)
    {
        $transaction = Transaction::findOrFail($id);

        $transaction->update([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'transid' => $request->transid,
            'amount_paid' => $request->amount_paid,
            'total_amount' => $request->total_amount,
            'provider_charge' => $request->provider_charge,
            'slot' => $request->slot,
            'registration_user_type' => $request->registration_user_type,
        ]);

        return back()->with('message','Transaction updated successfully');
    }
}
