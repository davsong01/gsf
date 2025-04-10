<?php

namespace App\Services;

use App\Models\Payment;
use Illuminate\Http\Request;
use Intervention\Image\Facades\Image;

class DynamicImageGeneratorService
{
    public static function generatePreview(Request $request, $settings=null, $user=null)
    {
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

        return Self::generate($request->all(), $settings, $textAndReplacements);
    }

    public static function generate($request, $settings, $textAndReplaceMents = []){
        if (!empty($request['template'])) {
            $inputImagePath = $request['template'];
        } else {
            $inputImagePath = base_path('uploads/' . $settings['template']);
        }

        $image = Image::make($inputImagePath);
        // dd($request);
        if (!empty($request['template_font_size'])) {
            $counter = count($request['template_font_size']);
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
            $size = !empty($request['template_font_size'][$i]) ? $request['template_font_size'][$i] : $settings['settings'][$i]['template_font_size'];
            $template_color = !empty($request['template_color'][$i]) ? $request['template_color'][$i] : $settings['settings'][$i]['template_color'];
            $template_top_offset = !empty($request['template_top_offset'][$i]) ? $request['template_top_offset'][$i] : $settings['settings'][$i]['template_top_offset'];
            $template_left_offset = !empty($request['template_left_offset'][$i]) ? $request['template_left_offset'][$i] : $settings['settings'][$i]['template_left_offset'];
            $template_text_type_face = !empty($request['template_text_type_face'][$i]) ? $request['template_text_type_face'][$i] : ($settings['settings'][$i]['template_text_type_face'] ?? 'Pesaro-Bold.ttf');

            $template_text_type = !empty($request['template_text_type'][$i]) ? $request['template_text_type'][$i] : $settings['settings'][$i]['template_text_type'];
            // Get text
            $text = $textAndReplaceMents[$template_text_type] ?? 'N/A';
        
            // End text
            $image->text($text, $template_left_offset, $template_top_offset, function ($font) use ($size, $template_color, $template_text_type_face) {
                $font->file(public_path('template_fonts/' . $template_text_type_face));
                $font->size($size);
                $font->color($template_color);
            });
        }

        $name = uniqid(9) . '.jpg';
 
        $outputImagePath = $request['location'] . '/' . $name;
        $image->save($outputImagePath);

        return [
            'status' => true,
            'name' => $name,
            'location' => $outputImagePath,
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


