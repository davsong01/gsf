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
        $settings = ConferenceEdition::where('id', $edition_id)->first()->template_settings ?? null;
        $request['location'] = 'template_previews';
        
        DynamicImageGeneratorService::generatePreview($request, $settings);

        // if (!empty($request['name_font_weight'])) {
        //     $data['counter'] = count($request['name_font_weight']);
        // } else {
        //     $data['counter'] = count($setting);

        // $data['template'] = $request['template'];

        // $data['size'] = !empty($request['name_font_size'][$i]) ? $request['name_font_size'][$i] : $certificate_settings['settings'][$i]['name_font_size'];
        // $data['color'] = !empty($request['color'][$i]) ? $request['color'][$i] : $certificate_settings['settings'][$i]['color'];
        // $data['top_offset'] = !empty($request['top_offset'][$i]) ? $request['top_offset'][$i] : $certificate_settings['settings'][$i]['top_offset'];
        // $data['left_offset'] = !empty($request['left_offset'][$i]) ? $request['left_offset'][$i] : $certificate_settings['settings'][$i]['left_offset'];
        // $data['font_weight'] = !empty($request['name_font_weight'][$i]) ? $request['name_font_weight'][$i] : ($certificate_settings['settings'][$i]['name_font_weight'] ?? 10);
        // $data['text_type_face'] = !empty($request['text_type_face'][$i]) ? $request['text_type_face'][$i] : ($certificate_settings['settings'][$i]['text_type_face'] ?? 'Pesaro-Bold.ttf');


        // $data['text_type'] = !empty($request['text_type'][$i]) ? $request['text_type'][$i] : $certificate_settings['settings'][$i]['text_type'];
        //     // Get text
        //     $text = $textAndReplaceMents[$text_type] ?? 'N/A';

        //     // End text
        //     $image->text($text, $left_offset, $top_offset, function ($font) use ($size, $color, $font_weight, $text_type_face) {
        //         $font->file(public_path('certificate_fonts/' . $text_type_face));
        //         $font->size($size);
        //         $font->color($color);
        //         $font->weight($font_weight);
        //     });
        // }

        // $name = uniqid(9) . '.jpg';

        // $outputImagePath = $location . '/' . $name;





        // try {
        //     $certificate = DynamicImageGeneratorService::generate($request->all(), $program_id, $location);

        //     return response()->json([
        //         'preview_image_path' => '/certificate_previews/' . $certificate['name'],
        //     ]);
        // } catch (\Throwable $th) {
        //     return response()->json([
        //         'error' => $th->getMessage(),
        //     ]);
        // }
    }

    
}
