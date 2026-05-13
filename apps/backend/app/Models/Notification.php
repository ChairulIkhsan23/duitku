<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Enums\NotificationType;

class Notification extends Model
{
    use HasUuids, HasFactory;

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
        'type',
        'title',
        'body',
        'data',
        'is_read',
        'read_at'
    ];

    /**
     * Casting attribute ke tipe data tertentu
     */
    protected $casts = [
        'data' => 'array',
        'is_read' => 'boolean',
        'read_at' => 'datetime',
        'type' => NotificationType::class,
    ];

    /**
     * Relasi: Notification dimiliki oleh User
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Tandai notification sebagai sudah dibaca
     */
    public function markAsRead()
    {
        $this->update([
            'is_read' => true,
            'read_at' => now()
        ]);
    }
}