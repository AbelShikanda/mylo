<?php

namespace App\Models\Identity;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class HomepageSection extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'homepage_sections';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'uuid';

    protected $fillable = ['name', 'type', 'sort_order', 'is_active', 'config'];

    protected $casts = [
        'sort_order' => 'integer',
        'is_active' => 'boolean',
        'config' => 'array',
    ];
}
