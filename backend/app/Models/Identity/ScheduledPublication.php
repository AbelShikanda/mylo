<?php

namespace App\Models\Identity;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ScheduledPublication extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'scheduled_publications';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'uuid';

    protected $fillable = ['article_id', 'publish_at', 'status'];

    public function article()
    {
        return $this->belongsTo(Article::class);
    }
}
