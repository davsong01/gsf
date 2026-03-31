<?php

namespace App\Http\Controllers;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\PaymentProvider;
use App\Services\FileUploadService;
use App\Http\Controllers\Controller;
use App\Services\HttpResponseService;
use App\Http\Requests\PaymentProviderRequest;
use App\Http\Resources\PaymentProviderResource;
use App\Services\MerchantService;

class PaymentProviderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if (!auth()->user()->isAdmin()) {
            return;
        }

        $providers = PaymentProvider::query();

        if ($request->has('status')) {
            $providers->where('status', $request->status);
        }

        $providers = $providers->latest()->get();

        return view('admin.paymentproviders.index', compact('providers'));

    }

    public function create(){
        return view('admin.paymentproviders.edit');
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(PaymentProviderRequest $request)
    {
        $validated = $request->validated();

        if ($request->hasFile('logo')) {
            $validated['logo'] = $this->uploadFile($request->file('logo'), 'images/paymentproviders/' . $validated['slug']);
        }

        PaymentProvider::create($validated);
        return redirect(route('payment-providers.index'))->with('message', 'Operation successful');
    }

    public function edit(PaymentProvider $paymentprovider)
    {
        return view('admin.paymentproviders.edit', compact('paymentprovider'));
    }

    /**
     * Display the specified resource.
     */
    public function show(PaymentProvider $paymentprovider)
    {
        return HttpResponseService::success(
            'Retrieved successfully',
            new PaymentProviderResource($paymentprovider),
            200
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(PaymentProviderRequest $request, PaymentProvider $paymentprovider)
    {
        $validated = $request->validated();

        if ($request->hasFile('logo')) {
            $validated['logo'] = $this->uploadFile(
                $request->file('logo'),
                'images/paymentproviders/' . $paymentprovider->slug
            );
        }

        $paymentprovider->update($validated);

        return redirect()
            ->route('paymentproviders.index')
            ->with('message', 'Payment Provider updated successfully');
    }




    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PaymentProvider $paymentprovider)
    {
        return back();
        // $paymentprovider->delete();
        // return HttpResponseService::success('Deeleted successfully', []);
    }
}
