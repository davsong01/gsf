<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Food;
use App\Models\User;
use App\Models\Email;
use App\Models\Hostel;
use App\Models\Chapter;
use App\Models\Payment;
use App\Models\Setting;
use Illuminate\Support\Str;
use App\Models\EmailContact;
use Illuminate\Http\Request;
use App\Models\CriticalEmail;
use App\Mail\NotificationEmail;
use App\Models\ConferenceEdition;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Validation\ValidationException;
use Intervention\Image\Facades\Image as Image;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public $edition = null;
    
    public function __construct(Request $request = null)
    {
        $this->edition = activeConferenceEdition();
        
    }
    protected function getMonths()
    {
        $months = [
            'January' => 1,
            'February' => 2,
            'March' => 3,
            'April' => 4,
            'May' => 5,
            'June' => 6,
            'July' => 7,
            'August' => 8,
            'September' => 9,
            'October' => 10,
            'November' => 11,
            'December' => 12,
        ];

        return $months;
    }

    protected function getPortfolios()
    {
        $portfolios = [
            'NCP',
            'General Secretary',
            'President',
            'Field Pastor',
            'Assistant General Secretary',
            'Assistant Publicity Secretary',
            'Assistant Music Director 1',
            'Assistant Music Director 2',
            'Evangelism Secretary 1',
            'Vice President',
            'Assistant Bible Studies Secretary',
            'Special Duty',
            'Head of Musician',
            'Sisters Cord',
            'Assistant Sis Cord 1',
            'Organizing Secretary 1',
            'Editor In Chief',
            'Technical Director 1',
            'Music Director',
            'Financial Secretary',
            'Publicity Secretary',
            'Technical Director 2',
            'Bible Study Secretary',
            'Treasurer',
            'Assistant Sis Cord 2',
            'Assistant Sis Cord 2 (North)',
            'Prayer Secretary',
            'Assistant Prayer Secretary (East)',
            'Assistant Prayer Secretary (West)',
            'Assistant Prayer Secretary (North)',
            'Evangelism Secretary 2(North)',
            'Evangelism Secretary2 (East)',
            'Organizing Secretary 2',
            'Health Officer',
            'Drama Secretary/ Liaison Officer (Alumni)',
            'Transport Secretary',
            'Special Duty',
        ];

        return $portfolios;
    }

    public function conferenceEdition()
    {
        return ConferenceEdition::where('status', 'active')->first();
    }

    public function getCommunityPortfolios()
    {
        $portfolios = [
            1 => 'Admin',
            2 => 'Member',
            3 => 'President',
            4 => 'Publicity Secretary',
            5 => 'Media Coordinator',
            6 => 'Assistant Publicity Secretary',
            7 => 'General Secretary',
            8 => 'Assistant General Secretary',
            9 => 'Assistant Music Director 1',
            10 => 'Assistant Music Director 2',
            11 => 'Evangelism Secretary 1',
            12 => 'Vice President',
            13 => 'Assistant Bible Studies Secretary',
            14 => 'Special Duty',
            15 => 'Head of Musicians',
            16 => 'Sister Cordinator',
            17 => 'Assistant Sister Cordinator 1',
            18 => 'Assistant Sister Cordinator 2',
            19 => 'Organizing Secretary 1',
            20 => 'Organizing Secretary 2',
            21 => 'Editor In Chief',
            22 => 'Technical Director 1',
            23 => 'Technical Director 2',
            24 => 'Music Director',
            25 => 'Financial Secretary',
            27 => 'Technical Director 2',
            28 => 'Bible Study Secretary',
            29 => 'Treasurer',
            31 => 'Assistant Sis Cord 1',
            32 => 'Assistant Sis Cord 2',
            33 => 'Prayer Secretary',
            34 => 'Assistant Prayer Secretary',
            35 => 'Health Officer',
            36 => 'Drama Secretary',
            37 => 'Alumni Liaison Officer',
            38 => 'Transport Secretary',
            39 => 'Special Duty',
            40 => 'Worker'
        ];

        return $portfolios;
    }

    protected function getDescriptions()
    {
        $descriptions = [
            'Tithe of tithes',
            'Thanksgiving '
        ];

        return $descriptions;
    }

    protected function sendEmail($data, $return_error = null)
    {
        // $data['type'] = $email->type;
        // $data['recipient'] = $email->recipient;
        // $data['content'] = $email->content;
        // $data['subject'] = $email->subject;
        // $data['attachments'] = $email->attachments;
        try {
            Mail::to($data['recipient'])->send(new NotificationEmail($data));
            
            return [
                'message'=>'success'
            ];
        } catch (\Exception $e) {
            \Log::error($e->getMessage());
            return [
                'error' => $e->getMessage(),
            ];
        }
    }

    protected function saveContactForm($user_id, $type, $name, $email, $phone, $message)
    {
        EmailContact::create([
            'user_id' => $user_id,
            'type' => $type,
            'name' => $name,
            'email' => $email,
            'phone' => $phone,
            'message' => $message
        ]);
    }
    protected function createNewFood(User $user)
    {
        $collection = Food::where('level', $user->level == 'Moderator' ? 'Participant' : $user->level)
            ->orderBy('allocation', 'ASC')->get(); // sort by the lowest allocation
        // Iterate through the collection
        $iterator = 0;
        if (!$user->food_id)
            $collection->each(function ($item) use (&$iterator, $user, $collection) {
                $iterator++;
                if ($item->capacity != $item->allocation) {
                    $user->food_id = $item->id;
                    $user->save();

                    $item->allocation++;
                    $item->save();
                    return false; // break outta each
                } else if ($item->capacity == $item->allocation && $collection->count() == $iterator) { // the last loop
                    return false; // breaks outta each
                }
            });
    }

    protected function uploadImage($image, $location, $width = null, $height = null)
    {
        $imgName = time() . rand(11111111, 9999999) . '.' . $image->getClientOriginalExtension();
        
        if ($width && $height) {
            $image = Image::make($image)->resize($width, $height);
        } else {
            $image = Image::make($image);
        }
        
        $image->save($location . '/' . $imgName);
        
        return $location . '/' . $imgName;
    }

    protected function uploadFile($file, $location)
    {
        $fileName = time() . rand(11111111, 9999999) . '.' . $file->getClientOriginalExtension();
        $file->move($location, $fileName );

        return $location . '/' . $fileName;
    }


    //Files must be in public and folder name must be appended to the filename for this to work
    protected function deleteImage($image)
    {
        // dd($image);
        if (file_exists($image))
            unlink($image);
        return;
    }

    protected function getDeleteLink($route, $id)
    {
        return sprintf(
            '<form id="%s" action="%s" method="POST"> <input type="hidden" name="_token" value="%s">
                    <input type="hidden" name="_method" value="DELETE">
                        <a class="actions" data-toggle="tooltip" title="Delete record" href="#" onClick="event.preventDefault(); if(confirm(\'Tis record will be trashed\')) document.getElementById(\'%s\').submit();" 
                        >
                    <i class="bx bx-trash actions"></i></a>
                </form>',
            $id,
            route($route, $id),
            csrf_token(),
            $id
        );
    }

    protected function getEditLink($route, $id)
    {
        return sprintf(
            '<a class="actions" data-toggle="tooltip" title="View/Edit User" href="%s" onClick="javascript:editRow(this)"><i class="bx bxs-edit actions"></i></a>',
            route($route, $id)
        );
    }

   

    // public function createUser($data)
    // {
    //     dd($data);
    //     $user = User::UpdateOrCreate(['email' => $data['email']], [
    //         'name' => $data['name'],
    //         'phone' => $data['phone'],
    //         'sex' => $data['sex'] ?? null,
    //         'chapter_id' => $data['chapter']??null,
    //         'passport' => $data['passport']??null,
    //         'slug' => Str::slug($data['name']),
    //         'password' => $data['password'],
    //         'role' => $data['role'] ?? 2
    //     ]);

    //     return $user;
    // }
   


    // public function createPayment($data, $user)
    // {
    //     $payment = Payment::UpdateOrCreate(['user_id' => $user->id, 'conference_edition_id' => $data['conference_edition_id']], [
    //         'user_id' => $user->id,
    //         'registration_status' => 'Complete',
    //         'slot' => $data['slot'],
    //         'type' => $data['type'],
    //         'slot_filled' => isset($data['slot_filled']) ? $data['slot_filled'] : 0,
    //         'level' => $data['level'],
    //         'amount_paid' => $data['amount'] ?? $data['amount_paid'],
    //         'payment_type' => $data['payment_type'],
    //         'transid' => $data['transid'],
    //         'uploaded_by' => $data['uploaded_by'] ?? null,
    //         'conference_edition_id' => $data['conference_edition_id']
    //     ]);

    //     return $payment;
    // }
}
