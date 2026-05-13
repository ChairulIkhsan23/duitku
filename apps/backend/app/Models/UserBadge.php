<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class UserBadge extends Model
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
        'user_id',
        'badge_id',
        'awarded_at',
        'progress_data'
    ];

    /**
     * Casting attribute ke tipe data tertentu
     */
    protected $casts = [
        'awarded_at' => 'datetime',
        'progress_data' => 'array',
    ];

    /**
     * Relasi: UserBadge dimiliki oleh User
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relasi: UserBadge dimiliki oleh Badge
     */
    public function badge()
    {
        return $this->belongsTo(Badge::class);
    }
}