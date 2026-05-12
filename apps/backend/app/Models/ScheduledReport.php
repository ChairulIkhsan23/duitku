<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use App\Enums\FrequencyType;
use App\Enums\ReportFormat;

class ScheduledReport extends Model
{
    use HasUuids;
    
    protected $keyType = 'string';
    public $incrementing = false;
    
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
    
    protected $casts = [
        'send_time' => 'datetime',
        'include_charts' => 'boolean',
        'is_active' => 'boolean',
        'frequency' => FrequencyType::class,
        'format' => ReportFormat::class,
    ];
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }

}
