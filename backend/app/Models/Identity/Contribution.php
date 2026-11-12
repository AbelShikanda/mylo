<?php

namespace App\Models\Identity;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Contribution extends Model
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

    protected $table = 'contributions';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'uuid';

    protected $fillable = [
        'writer_id', 'reader_id', 'amount', 'currency', 'type',
        'status', 'transaction_reference',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    public function writer()
    {
        return $this->belongsTo(Writer::class);
    }

    public function reader()
    {
        return $this->belongsTo(Reader::class);
    }
}
