<?php

namespace App\Models\Identity;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class PaymentAttempt extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'payment_attempts';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'uuid';

    protected $fillable = ['transaction_id', 'provider', 'reference', 'raw_response'];

    protected $casts = ['raw_response' => 'array'];

    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }
}
