<?php

namespace App\Models\Identity;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ReaderProfile extends Model
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

    protected $table = 'reader_profiles';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'uuid';

    protected $fillable = [
        'reader_id',
        'display_name',
        'bio',
        'avatar',
        'preferences',
    ];

    protected $casts = [
        'preferences' => 'array',
    ];

    public function reader()
    {
        return $this->belongsTo(Reader::class, 'reader_id');
    }
}
