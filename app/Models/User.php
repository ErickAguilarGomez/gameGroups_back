<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'nickname',
        'social_network_id',
        'email',
        'password',
        'role_id',
        'last_seen',
        'birthdate',
        'photo_url',
        'photo_status',
        'photo_rejection_reason',
        'account_status',
        'rejection_reason',
        'group_id',
        'banned_at',
        'ban_reason',
        'banned_by',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'id' => 'integer',
        'role_id' => 'integer',
        'social_network_id' => 'integer',
        'group_id' => 'integer',
        'banned_by' => 'integer',
        'last_seen' => 'datetime',
        'birthdate' => 'date',
        'banned_at' => 'datetime',
    ];

    /**
     * Return true when user has role admin.
     */
    public function isAdmin(): bool
    {
        return $this->role_id === 1;
    }

    public function isAssistant(): bool
    {
        return $this->role_id === 3;
    }

    /**
     * Return true when user is admin or assistant
     */
    public function isAdminOrAssistant(): bool
    {
        return $this->role_id === 1 || $this->role_id === 3;
    }

    /**
     * Return true only if user can ban/delete (only admin)
     */
    public function canModerate(): bool
    {
        return $this->role_id === 1;
    }

    /**
     * Obtener el rol del usuario
     */
    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * Obtener el grupo al que pertenece el usuario
     */
    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    /**
     * Obtener la red social del usuario
     */
    public function socialNetwork()
    {
        return $this->belongsTo(SocialNetwork::class);
    }

    /**
     * Obtener el usuario que baneó a este usuario
     */
    public function bannedBy()
    {
        return $this->belongsTo(User::class, 'banned_by');
    }
}
