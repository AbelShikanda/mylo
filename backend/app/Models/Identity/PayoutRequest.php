<?php

namespace App\Models\Identity;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class PayoutRequest extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'payout_requests';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'uuid';

    protected $fillable = ['writer_id', 'amount', 'status', 'processed_at'];

    protected $casts = [
        'amount' => 'decimal:2',
        'processed_at' => 'datetime',
    ];

    public function writer()
    {
        return $this->belongsTo(Writer::class);
    }

    public function records()
    {
        return $this->hasMany(PayoutRecord::class);
    }
}
