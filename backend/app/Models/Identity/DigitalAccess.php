<?php

namespace App\Models\Identity;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class DigitalAccess extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'digital_access';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'uuid';

    protected $fillable = [
        'order_item_id', 'reader_id', 'book_id', 'access_token', 'expires_at',
    ];

    protected $casts = ['expires_at' => 'datetime'];

    public function orderItem()
    {
        return $this->belongsTo(OrderItem::class);
    }

    public function reader()
    {
        return $this->belongsTo(Reader::class);
    }

    public function book()
    {
        return $this->belongsTo(Book::class);
    }
}
