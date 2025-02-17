<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Follower extends Model
{
    use HasFactory;

    const UPDATED_AT = null;

    protected $fillable = [
        'user_id',
        'follower_id',
        'status'
    ];

    public function follower()
    {
        return $this->belongsTo(User::class, 'follower_id');
    }

    public function blockers()
    {
        return $this->hasMany(Block::class, 'user_id');
    }
}
