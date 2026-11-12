<?php

namespace App\Models\Identity;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Order extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'orders';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'uuid';

    protected $fillable = [
        'reader_id', 'status', 'total', 'currency', 'shipping_address',
    ];

    protected $casts = [
        'total' => 'decimal:2',
        'shipping_address' => 'array',
    ];

    public function reader()
    {
        return $this->belongsTo(Reader::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }
}
