<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Group extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'group_img_url',
    ];

    protected $casts = [
        'id' => 'integer',
        'users_count' => 'integer',
    ];

    public function users()
    {
        return $this->hasMany(User::class)
            ->whereNotIn('role_id', [1, 3])
            ->where('account_status', 'approved')
            ->whereNull('deleted_at')
            ->with('socialNetwork');
    }
}
