<?php

namespace App\Models\Identity;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class AdminRole extends Model
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

    protected $table = 'admin_roles';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'uuid';

    protected $fillable = [
        'name',
        'slug',
        'description',
    ];

    public function permissions()
    {
        return $this->belongsToMany(AdminPermission::class, 'admin_role_permissions', 'admin_role_id', 'admin_permission_id')->withTimestamps();
    }

    public function administrators()
    {
        return $this->belongsToMany(Administrator::class, 'admin_user_roles', 'admin_role_id', 'administrator_id')->withTimestamps();
    }
}
