<?php

namespace App\Models\Identity;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Tag extends Model
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

    protected $table = 'tags';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'uuid';

    protected $fillable = ['name', 'slug'];

    public function articles()
    {
        return $this->belongsToMany(Article::class, 'article_tags');
    }
}
