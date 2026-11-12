<?php

namespace App\Models\Identity;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ReadingHistory extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'reading_history';
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

    protected $fillable = ['reader_id', 'article_id', 'duration_seconds', 'read_at'];

    protected $casts = [
        'duration_seconds' => 'integer',
        'read_at' => 'datetime',
    ];

    public function reader()
    {
        return $this->belongsTo(Reader::class);
    }

    public function article()
    {
        return $this->belongsTo(Article::class);
    }
}
