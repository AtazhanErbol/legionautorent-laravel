<?php

namespace App\Models;

use App\Services\CmsAudit;
use App\Services\CmsValidation;
use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasName;
use Filament\Panel;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable implements FilamentUser, HasName
{
    protected $table = 'auth_user';

    protected $guarded = [];

    public $timestamps = false;

    protected $hidden = ['password', 'remember_token'];

    protected $casts = ['is_staff' => 'boolean', 'is_superuser' => 'boolean', 'is_active' => 'boolean'];

    protected static function booted(): void
    {
        static::saving(function ($user) {
            foreach (['first_name', 'last_name', 'email'] as $field) {
                $user->$field ??= '';
            }
            if (! $user->password) {
                CmsValidation::fail('password', 'Укажите пароль.');
            }
            $user->date_joined ??= now();
        });
        static::saved(fn ($user) => CmsAudit::record($user, $user->wasRecentlyCreated ? 1 : 2));
    }

    public function groups(): BelongsToMany
    {
        return $this->belongsToMany(Group::class, 'auth_user_groups', 'user_id', 'group_id');
    }

    public function user_permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class, 'auth_user_user_permissions', 'user_id', 'permission_id');
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return $this->is_active && $this->is_staff;
    }

    public function getFilamentName(): string
    {
        return trim($this->first_name.' '.$this->last_name) ?: $this->username;
    }

    public function hasCmsPermission(string $action, string $model): bool
    {
        if (! $this->is_active || ! $this->is_staff) {
            return false;
        }if ($this->is_superuser) {
            return true;
        }if (in_array(strtolower($model), ['user', 'group', 'permission', 'contenttype'])) {
            return false;
        }
        $code = $action.'_'.strtolower($model);

        return $this->user_permissions->contains('codename', $code) || $this->groups->contains(fn ($g) => $g->permissions->contains('codename', $code));
    }
}
