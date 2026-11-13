<?php

namespace App\Models\Identity;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Comment extends Model
{

    protected static function booted(): void
    {
        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = (string) Str::uuid();
            }
        });
    }
    use HasFactory, SoftDeletes;

    protected $table = 'comments';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'uuid';

    protected $fillable = [
        'article_id', 'writer_id', 'reader_id', 'parent_id', 'content',
        'is_approved', 'is_reported',
    ];

    protected $casts = [
        'is_approved' => 'boolean',
        'is_reported' => 'boolean',
    ];

    public function article()
    {
        return $this->belongsTo(Article::class);
    }

    public function writer()
    {
        return $this->belongsTo(Writer::class);
    }

    public function reader()
    {
        return $this->belongsTo(Reader::class);
    }

    public function parent()
    {
        return $this->belongsTo(Comment::class, 'parent_id');
    }

    public function replies()
    {
        return $this->hasMany(Comment::class, 'parent_id');
    }
}
