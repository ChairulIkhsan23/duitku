<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use App\Enums\FrequencyType;
use App\Enums\ReportFormat;

class ScheduledReport extends Model
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
        'frequency',
        'day_of_week',
        'day_of_month',
        'send_time',
        'email',
        'format',
        'include_charts',
        'is_active'
    ];

    /**
     * Casting attribute ke tipe data tertentu
     */
    protected $casts = [
        'send_time' => 'datetime',
        'include_charts' => 'boolean',
        'is_active' => 'boolean',
        'frequency' => FrequencyType::class,
        'format' => ReportFormat::class,
    ];

    /**
     * Relasi: ScheduledReport dimiliki oleh User
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}