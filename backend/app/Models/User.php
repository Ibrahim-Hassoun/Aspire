<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
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
            'password' => 'hashed',
        ];
    }

    /**
     * Check if user is admin (id = 1)
     *
     * @return bool
     */
    public function isAdmin(): bool
    {
        return $this->id === 1;
    }

    /**
     * Check if user is moderator
     *
     * @return bool
     */
    public function isModerator(): bool
    {
        return $this->role === 'moderator';
    }

    /**
     * Check if user has admin privileges (id = 1 regardless of role field)
     *
     * @return bool
     */
    public function hasAdminPrivileges(): bool
    {
        return $this->id === 1;
    }

    /**
     * Check if user can delete products
     *
     * @return bool
     */
    public function canDeleteProducts(): bool
    {
        return $this->hasAdminPrivileges();
    }

    /**
     * Get user role display name
     *
     * @return string
     */
    public function getRoleDisplayName(): string
    {
        return $this->hasAdminPrivileges() ? 'Admin' : 'Moderator';
    }
    
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }
}
