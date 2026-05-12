<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Enums\PeriodType;

class Insight extends Model
{
    use HasUuids, HasFactory;
    
    protected $keyType = 'string';
    public $incrementing = false;
    
    protected $fillable = [
        'id', 
        'user_id', 
        'period_type', 
        'period_start', 
        'period_end', 
        'data', 
        'is_read', 
        'generated_at'
    ];
    
    protected $casts = [
        'period_start' => 'date',
        'period_end' => 'date',
        'data' => 'array',
        'is_read' => 'boolean',
        'generated_at' => 'datetime',
        'period_type' => PeriodType::class,
    ];
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    public function markAsRead()
    {
        $this->update(['is_read' => true]);
    }
    
    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }
    
    public function scopeByPeriod($query, $type)
    {
        return $query->where('period_type', $type);
    }
}
