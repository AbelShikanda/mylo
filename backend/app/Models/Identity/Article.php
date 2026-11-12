<?php

namespace App\Models\Identity;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Article extends Model
{
    use HasFactory, SoftDeletes;

    protected static function booted(): void
    {
        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = (string) Str::uuid();
            }
        });
    }

    protected $table = 'articles';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'uuid';

    protected $fillable = [
        'writer_id', 'category_id', 'subcategory_id', 'title', 'slug', 'excerpt',
        'content', 'featured_image', 'status', 'seo_title', 'seo_description',
        'reading_time', 'scheduled_at', 'published_at', 'is_featured', 'is_reported',
    ];

    protected $casts = [
        'content' => 'array',
        'is_featured' => 'boolean',
        'is_reported' => 'boolean',
        'published_at' => 'datetime',
        'scheduled_at' => 'datetime',
    ];

    public function writer()
    {
        return $this->belongsTo(Writer::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function subcategory()
    {
        return $this->belongsTo(Category::class, 'subcategory_id');
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'article_tags');
    }

    public function revisions()
    {
        return $this->hasMany(ArticleRevision::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }
}
