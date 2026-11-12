<?php

namespace App\Models\Identity;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Transaction extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'transactions';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'uuid';

    protected $fillable = [
        'reference', 'customer_type', 'customer_id', 'writer_id', 'type',
        'amount', 'currency', 'payment_method', 'provider', 'provider_reference',
        'status', 'idempotency_key',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    public function writer()
    {
        return $this->belongsTo(Writer::class);
    }

    public function paymentAttempts()
    {
        return $this->hasMany(PaymentAttempt::class);
    }

    public function commission()
    {
        return $this->hasOne(Commission::class);
    }

    public function refund()
    {
        return $this->hasOne(Refund::class);
    }
}
