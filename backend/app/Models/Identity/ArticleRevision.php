<?php

namespace App\Models\Identity;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ArticleRevision extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'article_revisions';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'uuid';

    protected $fillable = ['article_id', 'content', 'changed_by', 'change_summary'];

    protected $casts = ['content' => 'array'];

    public function article()
    {
        return $this->belongsTo(Article::class);
    }
}
