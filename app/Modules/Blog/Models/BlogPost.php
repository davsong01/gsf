<?php

namespace App\Modules\Blog\Models;

use App\Models\Stakeholder;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class BlogPost extends Model
{
    use SoftDeletes;

    protected $table = 'blog_posts';
    protected $guarded = [];
    protected $casts = [
        'published_at' => 'datetime',
        'allow_comments' => 'boolean',
        'is_featured' => 'boolean',
    ];

    public function author(): MorphTo { return $this->morphTo(); }
    public function categories(): BelongsToMany { return $this->belongsToMany(BlogCategory::class, 'blog_post_category', 'post_id', 'category_id'); }
    public function getCategoryAttribute(): ?BlogCategory { return $this->categories->first(); }
    public function tags(): BelongsToMany { return $this->belongsToMany(BlogTag::class, 'blog_post_tag', 'post_id', 'tag_id'); }
    public function related(): BelongsToMany { return $this->belongsToMany(self::class, 'blog_post_related', 'post_id', 'related_post_id'); }
    public function comments() { return $this->hasMany(BlogComment::class, 'post_id'); }

    public function suggestedRelated(int $limit = 3)
    {
        $tagIds = $this->tags()->pluck('blog_tags.id');
        $categoryIds = $this->categories()->pluck('blog_categories.id');
        if ($categoryIds->isEmpty() && $tagIds->isEmpty()) {
            return self::query()->whereRaw('1 = 0')->get();
        }

        return self::published()
            ->forNetwork($this->scope_type, $this->scope_id)
            ->where($this->getQualifiedKeyName(), '<>', $this->getKey())
            ->where(function (Builder $query) use ($tagIds, $categoryIds) {
                if ($categoryIds->isNotEmpty()) {
                    $query->whereHas('categories', fn (Builder $categories) => $categories->whereIn('blog_categories.id', $categoryIds));
                }
                if ($tagIds->isNotEmpty()) {
                    $query->orWhereHas('tags', fn (Builder $tags) => $tags->whereIn('blog_tags.id', $tagIds));
                }
            })
            ->with('categories')
            ->inRandomOrder()
            ->limit($limit)
            ->get();
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', 'published')->whereNotNull('published_at')->where('published_at', '<=', now());
    }

    public function scopeForNetwork(Builder $query, string $scopeType = 'national', ?int $scopeId = null): Builder
    {
        return $query->where(function (Builder $builder) use ($scopeType, $scopeId) {
            $builder->where('scope_type', 'national');
            if ($scopeType !== 'national' && $scopeId) {
                $builder->orWhere(fn (Builder $nested) => $nested->where('scope_type', $scopeType)->where('scope_id', $scopeId));
            }
        });
    }

    public function getReadingTimeAttribute(): int
    {
        return max(1, (int) ceil(str_word_count(strip_tags((string) $this->body)) / 220));
    }
}
