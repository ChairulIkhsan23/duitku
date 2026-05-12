<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Enums\CategoryType;

class Category extends Model
{
    use HasUuids, HasFactory;
    
    protected $keyType = 'string';
    public $incrementing = false;
    
    protected $fillable = [
        'id', 
        'name', 
        'type', 
        'icon', 
        'color', 
        'budget_default', 
        'is_default', 
        'user_id'
    ];
    
    protected $casts = [
        'budget_default' => 'decimal:2',
        'is_default' => 'boolean',
        'type' => CategoryType::class,
    ];
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }
    
    public function budgets()
    {
        return $this->hasMany(Budget::class);
    }
}
