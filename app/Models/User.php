<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    // Role constants
    public const ROLE_SUPER_ADMIN = 'Super Admin';
    public const ROLE_INVENTORY_MANAGER = 'Inventory Manager';
    public const ROLE_INSPECTOR = 'Inspector';
    public const ROLE_DEPARTMENT_USER = 'Department User';

    // All roles recognized by the system
    public const ROLES = [
        self::ROLE_SUPER_ADMIN,
        self::ROLE_INVENTORY_MANAGER,
        self::ROLE_INSPECTOR,
        self::ROLE_DEPARTMENT_USER,
    ];

    // Roles that can be self-registered
    public const REGISTRABLE_ROLES = [
        self::ROLE_INVENTORY_MANAGER,
        self::ROLE_INSPECTOR,
        self::ROLE_DEPARTMENT_USER,
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'role',
        'password',
        'is_admin',
        'login_attempts',
        'locked_until',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'deleted_at',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'deleted_at' => 'datetime',
            'password' => 'hashed',
            'is_admin' => 'boolean',
        ];
    }

    // Deployed items relationship can be added here if needed in the future
}
