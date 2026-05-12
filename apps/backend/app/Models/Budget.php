<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Budget extends Model
{
    use HasUuids, HasFactory;
    
    protected $keyType = 'string';
    public $incrementing = false;
    
    protected $fillable = [
        'id', 
        'user_id', 
        'category_id', 
        'month_year', 
        'limit_amount', 
        'spent_amount', 
        'notification_sent'
    ];
    
    protected $casts = [
        'limit_amount' => 'decimal:2',
        'spent_amount' => 'decimal:2',
        'notification_sent' => 'array',
        'month_year' => 'date',
    ];
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function getPercentageAttribute()
    {
        if ($this->limit_amount <= 0) return 0;
        return round(($this->spent_amount / $this->limit_amount) * 100, 2);
    }
    
    public function getRemainingAttribute()
    {
        return max(0, $this->limit_amount - $this->spent_amount);
    }
    
    public function isOverspent(): bool
    {
        return $this->spent_amount > $this->limit_amount;
    }
    
    public function isWarning(): bool
    {
        return !$this->isOverspent() && $this->percentage >= 80;
    }
}
