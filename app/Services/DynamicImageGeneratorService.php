<?php

namespace App\Services;

use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
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
            if ($key == 'name') $textAndReplacements[$key] = strtoupper($payment->user->name ?? 'N/A');
            if ($key == 'reg_number') $textAndReplacements[$key] = $payment->transid ?? 'N/A';
            // if ($key == 'hostel') $textAndReplacements[$key] = $payment->hostel->name ?? 'N/A';
            if ($key == 'hostel') {
                $hostelName =  $payment->hostel->name ?? 'N/A';
                $textAndReplacements[$key] = self::limitTextMiddle($hostelName, 30);
            }
            if ($key == 'campus') {
                $campusName =  strtoupper($payment->user->campus->name ?? 'N/A');
                $textAndReplacements[$key] = self::limitTextMiddle($campusName, 28);
            }
        }

        return Self::generate($request->all(), $settings, $textAndReplacements);
    }

    static function limitTextMiddle(string $text, int $maxLength = 40): string
    {
        if (strlen($text) <= $maxLength) {
            return $text;
        }

        $ellipsis = '...';
        $keepLength = $maxLength - strlen($ellipsis);
        $startLength = (int) ceil($keepLength / 2);
        $endLength = (int) floor($keepLength / 2);

        $start = substr($text, 0, $startLength);
        $end = substr($text, -$endLength);

        return $start . $ellipsis . $end;
    }

    public static function generate($request, $settings, $textAndReplaceMents = []){
        if (!empty($request['template'])) {
            $inputImagePath = $request['template'];
        } else {
            $inputImagePath = public_path($settings['template']);
        }

        // if (!is_dir($inputImagePath)) {
        //     mkdir($inputImagePath, 0777, true);
        // }

        $image = Image::make($inputImagePath);
        
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
            'campus' => 'Campus',
        ];
    }

    public static function updateSettings($request, $settings){
        self::deleteAllFilesInAPublicFolder('template_previews');
        //check if new featured image
        if (!empty($request->template)) {
            $name = uniqid(9) . '.' . $request->template->getClientOriginalExtension();
            $request->template->move(public_path('template_files'), $name);

            $request['path'] = 'template_files/' . $name;
        } else {
            $request['path'] = $settings->template_settings['template'] ?? null;
        }

        $data['template_settings'] = self::buildCertificateSettings($request);
        // dd($request->all());
       
        $data['template_settings']['template'] = $request['path'];
        $settings->template_settings = $data['template_settings'];  
        $settings->save();
        
        return;

    }

    public static function buildCertificateSettings($request)
    {
        $auto_certificate_settings = [
            "template_font_size" => $request->template_font_size,
            "template_color" => $request->template_color,
            "template_top_offset" => $request->template_top_offset,
            "template_left_offset" => $request->template_left_offset,
            "template_text_type" => $request->template_text_type,
            "template_text_type_face" => $request->template_text_type_face ?: 'Pesaro-Bold.ttf',
        ];

        $final_array = [];

        $auto_certificate_settings = array_filter($auto_certificate_settings, function ($value) {
            return !is_null($value);
        });

        if ($auto_certificate_settings > 0) {
            foreach ($auto_certificate_settings as $key => $req) {
                foreach ($req as $index => $value) {
                    $final_array[$index][$key] = $value;
                }
            }
        }

        $template_settings = [
            "template" => $request->path,
            "settings" => $final_array
        ];
        
        return $template_settings;
    }

    public static function deleteAllFilesInAPublicFolder($folderName)
    {
        $folderPath = public_path($folderName);

        if (File::exists($folderPath)) {
            $files = File::files($folderPath);

            // Loop through the files and delete each one
            foreach ($files as $file) {
                File::delete($file);
            }

            return true;
        } else {
            return false;
        }
    }
}


