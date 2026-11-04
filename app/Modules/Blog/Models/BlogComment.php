<?php

namespace App\Modules\Blog\Models;

use App\Models\User;
use App\Models\Chapter;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BlogComment extends Model
{
    protected $table = 'blog_comments';
    protected $guarded = [];

    public function post(): BelongsTo { return $this->belongsTo(BlogPost::class, 'post_id'); }
    public function user(): BelongsTo { return $this->belongsTo(User::class); }
    public function chapter(): BelongsTo { return $this->belongsTo(Chapter::class); }
}
