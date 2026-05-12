<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Budget extends Model
{
    use HasUuids;
    
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
}
