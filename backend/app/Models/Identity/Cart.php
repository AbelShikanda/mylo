<?php

namespace App\Models\Identity;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Cart extends Model
{
    protected static function booted(): void
    {
        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = (string) Str::uuid();
            }
        });
    }

    protected $table = 'carts';

    protected $primaryKey = 'id';

    public $incrementing = false;

    protected $keyType = 'uuid';

    protected $fillable = ['reader_id'];

    public function reader()
    {
        return $this->belongsTo(Reader::class);
    }

    public function items()
    {
        return $this->hasMany(CartItem::class);
    }
}
