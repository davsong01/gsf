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

    
}
