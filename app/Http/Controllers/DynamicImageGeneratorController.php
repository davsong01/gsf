<?php

namespace App\Http\Controllers;

use App\Models\ConferenceEdition;
use Illuminate\Http\Request;
use App\Services\DynamicImageGeneratorService;
use Illuminate\Routing\Controller;

class DynamicImageGeneratorController extends Controller
{
    public function generateTemplatePreview(Request $request, $edition_id)
    {
        try {
            $settings = ConferenceEdition::where('id', $edition_id)->first()->template_settings ?? null;
            $request['location'] = 'template_previews';
            
            $preview = DynamicImageGeneratorService::generatePreview($request, $settings);
            
            return response()->json([
                'preview_image_path' => url($preview['location']),
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'error' => $th->getMessage(),
            ]);
        }
    }

    public function updateSetting(Request $request, $edition_id){
        $settings = ConferenceEdition::where('id', $edition_id)->first();
        DynamicImageGeneratorService::updateSettings($request, $settings);

        return;
    }

    public function generateImage($request, $payment){
        $texts = DynamicImageGeneratorService::textType();
        
        $textAndReplacements = [];
        
        foreach ($texts as $key => $text) {
            if ($key == 'name') $textAndReplacements[$key] = strtoupper($payment->user->name ?? 'N/A');
            if ($key == 'reg_number') $textAndReplacements[$key] = $payment->transid ?? 'N/A';
            // if ($key == 'hostel') $textAndReplacements[$key] = $payment->hostel->name ?? 'N/A';
            if ($key == 'hostel') {
                $hostelName =  $payment->hostel->name ?? 'N/A';
                $textAndReplacements[$key] = DynamicImageGeneratorService::limitTextMiddle($hostelName, 30);
            }
            if ($key == 'campus') {
                $campusName =  strtoupper($payment->user->campus->name ?? 'N/A');
                $textAndReplacements[$key] = DynamicImageGeneratorService::limitTextMiddle($campusName, 28);
            }
        }


        $request['location'] = 'badges';
        $image = DynamicImageGeneratorService::generate($request, $payment->edition->template_settings, $textAndReplacements);
        
        if($image['status']){
            $payment->badge_location = $image['location'];
            $payment->save();
        }

        return;
    }
    
    
}
