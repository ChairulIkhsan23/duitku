<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class UserBadge extends Model
{
    use HasUuids;
    
    protected $keyType = 'string';
    public $incrementing = false;
    
    protected $fillable = [
        'id', 
        'user_id', 
        'badge_id', 
        'awarded_at', 
        'progress_data'
    ];
    
    protected $casts = [
        'awarded_at' => 'datetime',
        'progress_data' => 'array',
    ];
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    public function badge()
    {
        return $this->belongsTo(Badge::class);
    }
}
