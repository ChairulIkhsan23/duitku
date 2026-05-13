<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Enums\PeriodType;

class Insight extends Model
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
        'period_type',
        'period_start',
        'period_end',
        'data',
        'is_read',
        'generated_at',
    ];

    /**
     * Casting attribute ke tipe data tertentu
     */
    protected $casts = [
        'period_start' => 'date',
        'period_end' => 'date',
        'data' => 'array',
        'is_read' => 'boolean',
        'generated_at' => 'datetime',
        'period_type' => PeriodType::class,
    ];

    /**
     * Relasi: Insight dimiliki oleh User
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Tandai insight sebagai sudah dibaca
     */
    public function markAsRead()
    {
        $this->update(['is_read' => true]);
    }

    /**
     * Scope: hanya data yang belum dibaca
     */
    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    /**
     * Scope: filter berdasarkan periode
     */
    public function scopeByPeriod($query, $type)
    {
        return $query->where('period_type', $type);
    }
}