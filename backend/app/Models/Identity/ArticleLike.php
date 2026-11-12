<?php

namespace App\Models\Identity;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ArticleLike extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'article_likes';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'uuid';

    protected static function booted(): void
    {
        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = (string) Str::uuid();
            }
        });
    }

    protected $fillable = ['article_id', 'reader_id'];

    public function article()
    {
        return $this->belongsTo(Article::class);
    }

    public function reader()
    {
        return $this->belongsTo(Reader::class);
    }
}
