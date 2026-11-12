<?php

namespace App\Models\Identity;

use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Administrator extends Model
{
    use HasFactory, Notifiable, HasApiTokens, SoftDeletes;

    protected static function booted(): void
    {
        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = (string) Str::uuid();
            }
        });
    }

    protected $table = 'administrators';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'uuid';

    protected $fillable = [
        'email',
        'password',
        'email_verified_at',
        'remember_token',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function profile()
    {
        return $this->hasOne(AdminProfile::class, 'administrator_id');
    }

    public function roles()
    {
        return $this->belongsToMany(AdminRole::class, 'admin_user_roles', 'administrator_id', 'admin_role_id');
    }

    public function permissions()
    {
        return $this->belongsToMany(AdminPermission::class, 'admin_role_permissions', 'admin_role_id', 'admin_permission_id')
            ->usingExistingPivotData();
    }

    public function hasRole($role): bool
    {
        if (is_string($role)) {
            return $this->roles()->where('slug', $role)->exists();
        }
        return $this->roles->contains($role);
    }

    public function hasPermission($permission): bool
    {
        if (is_string($permission)) {
            return $this->roles()->with('permissions')->get()
                ->flatMap->permissions
                ->contains('slug', $permission);
        }
        return false;
    }
}
