<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Badge extends Model
{
    use HasUuids;

    /**
     * Primary key menggunakan UUID (string)
     */
    protected $keyType = 'string';
    public $incrementing = false;

    /**
     * Field yang boleh di-mass assign
     */
    protected $fillable = [
        'id',
        'name',
        'slug',
        'description',
        'requirement',
        'icon',
        'color',
        'points',
        'trigger',
    ];

    /**
     * Cast attribute ke tipe data tertentu
     */
    protected $casts = [
        'requirement' => 'array',
        'points' => 'integer',
    ];

    /**
     * Relasi: Badge memiliki banyak UserBadge
     */
    public function userBadges()
    {
        return $this->hasMany(UserBadge::class);
    }

    /**
     * Relasi many-to-many dengan User
     * melalui tabel pivot user_badges
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'user_badges')
            ->withPivot('awarded_at', 'progress_data')
            ->withTimestamps();
    }
}