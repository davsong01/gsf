<?php

namespace App\Http\Controllers;

use App\Post;
use App\Setting;
use App\Notification;
use Illuminate\Http\File;
use Illuminate\Http\Request;
use App\Mail\NotificationEmail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class PostController extends Controller
{
    public function index()
    {
        if(auth()->user()->permission == 2){
            $i = 1;
            $posts = Post::with('user')->orderBy('created_at', 'desc')->get();

            return view('admin.posts.index', compact('posts', 'i'));
        }

        if(auth()->user()->permission == 1){
            $i = 1;
            $posts = Post::with('user')->whereUserId(auth()->user()->id)->orderBy('created_at', 'desc')->get();

            return view('user.posts', compact('posts', 'i'));
        }
    }

    public function trashed()
    {
        if(auth()->user()->permission == 2){
            $i = 1;
            $posts = Post::with('user')->onlyTrashed()->orderBy('deleted_at', 'desc')->get();

            return view('admin.posts.trashed', compact('posts','i'));
        }return abort(404);
    }

    public function approve($id)
    {
        $post = Post::whereId($id)->first();

        //Update post status
        $post->status = 1;
        $post->save();

        //Update User Wallet
        $post->user->wallet += $post->value;
        $post->user->save();

        //Send mail
        $settings = Setting::first();
        $data = [
            'type' => 'post_approval',
            'name' => $post->user->username,
            'amount' => $post->value,
            'wallet' => $post->user->wallet,
            'date_approved' => now(),
            'subject' => $settings->post_approved_user_notification_title,
            'content' => $settings->post_approved_mail_content
        ];
        Mail::to($post->user->email)->send(new NotificationEmail($data));
        
        return back()->with('message', 'Post has been approved');
    }
    
    public function unapprove($id)
    {
        $post = Post::findOrFail($id);
        $post->status = 0;
        $post->save();

        //Update User Wallet
        $post->user->wallet -= $post->value;
        $post->user->save();

        //send unapproval email
        $settings = Setting::first();
        $data = [
            'type' => 'post_unapproval',
            'name' => $post->user->username,
            'wallet' => $post->user->wallet,
            'subject' => $settings->post_approved_user_notification_title,
            'content' => $settings->post_approved_mail_content
        ];
        // Mail::to($post->user->email)->send(new NotificationEmail($data));
        return redirect(route('posts.index'))->with('message', 'Post has been unapproved');
    }

    public function create()
    {
        if(auth()->user()->permission == 1){
            $f = 0;
            return view('user.create_post');
        }
    }

    public function store(Request $request)
    {
       
        $setting = Setting::first()->toArray();

        if($request->type == 1){
            request()->validate([
            'text' => 'required|min:5',
            'type' => 'required'
            ],

            [
            'type.required' => 'The Post Type field must be selected',
            'images.min' => 'Post content cannot be less than 500 characters!'
            ]);

            $content = $request->text; 
            $type = 1;
            $value = $setting['text_post_value'];
            $videos = NULL;
            $images = NULL;
        }

        if($request->type == 2){
  
            request()->validate([
            'imagestext' => 'required|min:5',
            'type' => 'required',
            'images' =>  'required',
            'images.*' => 'max:3024|mimes:jpg,jpeg,png,bmp'
            ],

            [
            'type.required' => 'The Post Type field must be selected',
            'imagestext.min' => 'Post content cannot be less than 500 characters!',
            'imagestext.required' => 'Post content is required!',
            'images.required' => 'You must choose 3 or more images to upload',
            'images.*' => 'The selected files must all be of type: jpg,jpeg,png or bmp'
            ]);

            //check if files are upto 3
            $count = count($request->images);
            if($count < 3 ){
                return back()->with('error', 'You must select 3 or more images to upload');
            }

            $images = array();
            //Store images
            foreach($request->images as $file){
                $filename = date('d-M-Y-s') . '-'. pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                $extension = $file->getClientOriginalExtension();              
                $filePath = $file->storeAs('posts', $filename.'.'.$extension,'uploads'); 
                array_push($images, $filename.'.'.$extension );
            }

            $type = $request->type;
            $content = $request->imagestext;
            $value = $setting['image_post_value'];
            $images = json_encode($images);
            $videos = NULL;

        }
 
        if($request->type == 3){
            
            // dd($request->all());
            request()->validate([
            'videotext' => 'required|min:5',
            'type' => 'required',
            'videos' =>  'required',
            'videos.*' => 'max:120000|mimes:mp4,3gp,avi,wmv,webm',
            'images' =>  'sometimes',
            'images.*' => 'max:3024|mimes:jpg,jpeg,png,bmp'
            ],

            [
            'type.required' => 'The Post Type field must be selected',
            'videotext.min' => 'Post content cannot be less than 500 characters!',
            'videotext.required' => 'Post content is required!',
            'videos.required' => 'You must upload at least one video',
            'videos.*' => 'The selected videos must all be of type: mp4,3gp,avi,wmv,webm',
            'images.*' => 'The selected images must all be of type: jpg,jpeg,png or bmp'
            ]);

           
            if($request->has('images')){
                $images = array();
                //Store images
                foreach($request->images as $file){
                    $filename = date('d-M-Y-s') . '-'. pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                    $extension = $file->getClientOriginalExtension();                   
                    $filePath = $file->storeAs('posts', $filename.'.'.$extension,'uploads'); 
                    array_push($images, $filename.'.'.$extension );
                }
            }else $images = NULL;
            
            if($request->has('videos')){
                $videos = array();
                //Store videos
                foreach($request->videos as $file){
                    $filename = date('d-M-Y-s') . '-'. pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                    $extension = $file->getClientOriginalExtension();
                    Storage::disk('uploads')->putFileAs('posts', new File($file->path()), $filename.'.'.$extension);
                    array_push($videos, $filename.'.'.$extension );
                }
            }else $videos = NULL;
 
            $type = $request->type;
            $content = $request->videotext;
            $value = $setting['video_post_value'];
            $images = json_encode($images);
            $videos = json_encode($videos);

        }

        //Store values in database
        try{
            $new_post = Post::create([
                'user_id' => Auth::user()->id,
                'type' => $type,
                'value' => $value,
                'content' => $content,
                'images' => $images,
                'videos' => $videos,
            ]);

        }catch(\Illuminate\Database\QueryException $e){
            $error = $e->getMessage();
            return back()->with('error', $error);
        }
            
    //Send admin mail
    return back()->with('message', 'Post created successfully');
    }
    

    public function show(Post $post)
    {
        //
    }

    public function edit(Post $post)
    {
        if(auth()->user()->permission == 1){
            return view('user.edit_post')->with('post', $post);
        }

        if(auth()->user()->permission == 2){
            return view('admin.posts.edit')->with('post', $post);
        }
        
    }

    
    public function update(Request $request, Post $post)
    {
        $post->content = $request->text;
        $post->save();

        return back()->with('message', 'Content change is succesful');
    }

     public function replaceFile(Request $request)
    {
       if($request->has('image')){
        // dd($request->image);

        $files = Post::whereId($request->pid)->first();
        
        $files_array = json_decode($files->images, true);

            foreach ($files_array as $key => $value) {
                if ($value == $request->old_image) {

                    //Store new image to disk
                    $filename = date('d-M-Y-s') . '-'. pathinfo($request->image->getClientOriginalName(), PATHINFO_FILENAME);
                    $extension = $request->image->getClientOriginalExtension();
                    $filePath = $request->image->storeAs('posts', $filename.'.'.$extension,'uploads'); 

                    //delete old image from server
                    unlink( base_path() . '/uploads/posts'.'/'. $request->old_image);
                    //replace in array
                    $files_array[$key] = $filename.'.'.$extension;

                    
                }
            }

        $files->images = json_encode($files_array);

        $files->save();

        return back()->with('message', 'Image change is succesful');
         
       }

        if($request->has('video')){
        // dd($request->video);

        $files = Post::whereId($request->pid)->first();
        
        $files_array = json_decode($files->videos, true);

            foreach ($files_array as $key => $value) {
                if ($value == $request->old_video) {

                    //Store new video to disk
                    $filename = date('d-M-Y-s') . '-'. pathinfo($request->video->getClientOriginalName(), PATHINFO_FILENAME);
                    $extension = $request->video->getClientOriginalExtension();
                    Storage::disk('uploads')->putFileAs('posts', new File($request->video->path()), $filename.'.'.$extension);
                    // $filePath = $request->video->storeAs('posts', $filename.'.'.$extension,'uploads'); 

                    //delete old video from server
                    unlink( base_path() . '/uploads/posts'.'/'. $request->old_video);
                    //replace in array
                    $files_array[$key] = $filename.'.'.$extension;

                    
                }
            }

        $files->videos = json_encode($files_array);

        $files->save();

        return back()->with('message', 'video change is succesful');
         
       }
    }
// pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)
    public function destroy($id)
    {
        $post = Post::findOrFail($id);
        if(auth()->user()->permission == 2){
            $thepost = Post::withTrashed()->whereId($post->id)->firstOrFail();

            if($thepost->trashed()){
                $thepost->forceDelete();

                return redirect('posts')->with('message', 'Post has been deleted forever');
            
            } else {
                
                $thepost->delete();

                return redirect('posts')->with('message', 'Post has been trashed');
            }      
        }
        
    }

    public function userdelete($id)
    {
        $post = Post::findOrFail($id);
        if(auth()->user()->id <> $post->user_id){
            return back()->with('warning', 'Unauthorized Action'); 
        }

        $post->forceDelete();

        return back()->with('message', 'Your post has been deleted successfully'); 
    } 

    public function restore($id)
    {
        $post = Post::withTrashed()->where('id', $id)->firstOrFail();

        $post->restore();

       return redirect(route('posts.index'))->with('message', 'Post has been restored');
    }

    public function getfile($filename){
        $realpath = base_path() . '/uploads/posts'. '/' .$filename;
        return response()->download($realpath);
    }
}