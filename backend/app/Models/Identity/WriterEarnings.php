<?php

namespace App\Models\Identity;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class WriterEarnings extends Model
{
    use HasFactory;

    protected static function booted(): void
    {
        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = (string) Str::uuid();
            }
        });
    }

    protected $table = 'writer_earnings';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'uuid';

    protected $fillable = [
        'writer_id', 'contributions', 'book_sales', 'platform_deductions',
        'available_balance', 'pending_balance', 'withdrawn_amount',
    ];

    protected $casts = [
        'contributions' => 'decimal:2',
        'book_sales' => 'decimal:2',
        'platform_deductions' => 'decimal:2',
        'available_balance' => 'decimal:2',
        'pending_balance' => 'decimal:2',
        'withdrawn_amount' => 'decimal:2',
    ];

    public function writer()
    {
        return $this->belongsTo(Writer::class);
    }
}
