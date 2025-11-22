<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SocialNetwork extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'logo_url',
    ];

    protected $casts = [
        'id' => 'integer',
    ];

    /**
     * Obtener los usuarios que usan esta red social
     */
    public function users()
    {
        return $this->hasMany(User::class);
    }
}
