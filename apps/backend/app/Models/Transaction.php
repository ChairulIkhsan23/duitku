<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use App\Enums\TransactionType;

class Transaction extends Model
{
    use HasUuids;
    
    protected $keyType = 'string';
    public $incrementing = false;
    
    protected $fillable = [
        'id', 
        'user_id', 
        'category_id', 
        'amount', 
        'type', 
        'date', 
        'note', 
        'photo_url', 
        'is_duplicate', 
        'location_name', 
        'metadata'
    ];
    
    protected $casts = [
        'amount' => 'decimal:2',
        'date' => 'date',
        'is_duplicate' => 'boolean',
        'metadata' => 'array',
        'type' => TransactionType::class,
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
