<?php

namespace App\Models\Identity;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class MediaAsset extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'media_assets';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'uuid';

    protected $fillable = [
        'file_path', 'file_type', 'file_size', 'width', 'height',
        'alt_text', 'is_reported',
    ];

    protected $casts = [
        'file_size' => 'integer',
        'width' => 'integer',
        'height' => 'integer',
        'is_reported' => 'boolean',
    ];

    public function owner()
    {
        return $this->morphTo();
    }

    public function uploader()
    {
        return $this->morphTo();
    }
}
