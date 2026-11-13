<?php

namespace App\Models\Identity;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class CartItem extends Model
{
    protected static function booted(): void
    {
        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = (string) Str::uuid();
            }
        });
    }

    protected $table = 'cart_items';

    protected $primaryKey = 'id';

    public $incrementing = false;

    protected $keyType = 'uuid';

    protected $fillable = ['cart_id', 'book_id', 'quantity'];

    protected $casts = ['quantity' => 'integer'];

    public function cart()
    {
        return $this->belongsTo(Cart::class);
    }

    public function book()
    {
        return $this->belongsTo(Book::class);
    }
}
