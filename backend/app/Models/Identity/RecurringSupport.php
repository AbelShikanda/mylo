<?php

namespace App\Models\Identity;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class RecurringSupport extends Model
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

    protected $table = 'recurring_support';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'uuid';

    protected $fillable = [
        'writer_id', 'reader_id', 'amount', 'currency', 'interval',
        'next_due', 'status',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'next_due' => 'datetime',
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
