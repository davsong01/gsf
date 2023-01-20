<?php

namespace App;
use App\User;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Post extends Model
{
    use SoftDeletes;
    
    protected $guarded = [];

    public function getTypeAttribute($value)
    {
        if($value == 1){
            $value = 'Text';
        }

        if($value == 2){
            $value = 'Image';
        }

        if($value == 3){
            $value = 'Video';
        }

        return $value;
    }

    protected $casts = [
        'images' => 'json',
        'videos' => 'json'
    ];

    public function user(){
        return $this->belongsTo(User::class);
    }

}
