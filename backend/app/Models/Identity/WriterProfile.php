<?php

namespace App\Models\Identity;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class WriterProfile extends Model
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

    protected $table = 'writer_profiles';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'uuid';

    protected $fillable = [
        'writer_id',
        'display_name',
        'bio',
        'avatar',
        'earnings_balance',
        'pending_balance',
        'withdrawn_amount',
        'support_active',
    ];

    protected $casts = [
        'earnings_balance' => 'decimal:2',
        'pending_balance' => 'decimal:2',
        'withdrawn_amount' => 'decimal:2',
        'support_active' => 'boolean',
    ];

    public function writer()
    {
        return $this->belongsTo(Writer::class, 'writer_id');
    }
}
