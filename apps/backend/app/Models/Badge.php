<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Badge extends Model
{
    use HasUuids;
    
    protected $keyType = 'string';
    public $incrementing = false;
    
    protected $fillable = [
        'id', 
        'name', 
        'slug', 
        'description', 
        'requirement', 
        'icon', 
        'color', 
        'points'
    ];
    
    protected $casts = [
        'requirement' => 'array',
        'points' => 'integer',
    ];
    
    public function userBadges()
    {
        return $this->hasMany(UserBadge::class);
    }
    
    public function users()
    {
        return $this->belongsToMany(User::class, 'user_badges')
            ->withPivot('awarded_at', 'progress_data')
            ->withTimestamps();
    }
}
