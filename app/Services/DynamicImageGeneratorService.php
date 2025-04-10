<?php

namespace App\Services;

use App\Models\Payment;
use Illuminate\Http\Request;
use Intervention\Image\Facades\Image;

class DynamicImageGeneratorService
{
    public static function generatePreview(Request $request, $settings=null, $user=null)
    {
        // try {
            $texts = self::textType();

            if (empty($user)) {
                $payment = Payment::with('user','hostel')->where('conference_edition_id', $request['edition_id'])->inRandomOrder()->first();
            }

            $textAndReplacements = [];
            
            foreach($texts as $key=>$text){
                if ($key == 'name') $textAndReplacements[$key] = $payment->user->name ?? 'N/A';
                if ($key == 'reg_number') $textAndReplacements[$key] = $payment->transid ?? 'N/A';
                if ($key == 'hostel') $textAndReplacements[$key] = $payment->hostel->name ?? 'N/A';
            }

            $certificate = Self::generate($request->all(), $settings, $textAndReplacements);

            return response()->json([
                'preview_image_path' => '/template_previews/' . $certificate['name'],
            ]);
        // } catch (\Throwable $th) {
        //     dd($th->getMessage());
        //     return response()->json([
        //         'error' => $th->getMessage(),
        //     ]);
        // }
    }

    public static function generate($request, $settings, $textAndReplaceMents = []){
        
        if (!empty($request['template'])) {
            $inputImagePath = $request['template'];
        } else {
            $inputImagePath = base_path('uploads/' . $settings['template']);
        }

        $image = Image::make($inputImagePath);
        // dd($request);
        if (!empty($request['font_weight'])) {
            $counter = count($request['font_weight']);
        } else {
            $counter = count($settings['settings']);
        }

        if ($image->width() > 4000 || $image->height() > 4000) {
            $image->resize(4000, null, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            });
        }

        for ($i = 0; $i < $counter; $i++) {
            $size = !empty($request['font_size'][$i]) ? $request['font_size'][$i] : $settings['settings'][$i]['font_size'];
            $color = !empty($request['color'][$i]) ? $request['color'][$i] : $settings['settings'][$i]['color'];
            $top_offset = !empty($request['top_offset'][$i]) ? $request['top_offset'][$i] : $settings['settings'][$i]['top_offset'];
            $left_offset = !empty($request['left_offset'][$i]) ? $request['left_offset'][$i] : $settings['settings'][$i]['left_offset'];
            $font_weight = !empty($request['font_weight'][$i]) ? $request['font_weight'][$i] : ($settings['settings'][$i]['font_weight'] ?? 10);
            $text_type_face = !empty($request['text_type_face'][$i]) ? $request['text_type_face'][$i] : ($settings['settings'][$i]['text_type_face'] ?? 'Pesaro-Bold.ttf');


            $text_type = !empty($request['text_type'][$i]) ? $request['text_type'][$i] : $settings['settings'][$i]['text_type'];
            // Get text
            $text = $textAndReplaceMents[$text_type] ?? 'N/A';
        
            // End text
            $image->text($text, $left_offset, $top_offset, function ($font) use ($size, $color, $font_weight, $text_type_face) {
                $font->file(public_path('certificate_fonts/' . $text_type_face));
                $font->size($size);
                $font->color($color);
                $font->weight($font_weight);
            });
        }

        $name = uniqid(9) . '.jpg';
 
        $outputImagePath = $request['location'] . '/' . $name;
        $image->save($outputImagePath);

        return [
            'status' => true,
            'name' => $name,
        ];
    }

    public static function templateFontType(){
        return [
            'Times-New-Roman.ttf' => 'Times New Roman',
            'Times-New-Roman-Bold.ttf' => 'Times New Roman Bold',
            'Pesaro-Bold.ttf' => 'Pesaro-Bold',
            'Edwardian-Script-ITC.ttf' => 'Edwardian Script ITC',
        ];
    }

    public static function textType(){
        return [
            'name' => 'Name',
            'reg_number' => 'Registration Number',
            'hostel' => 'Hostel',
            // 'service_point' => 'Service Point',
            // 'chapter' => 'Chapter',
        ];
    }
}


