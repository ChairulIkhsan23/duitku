<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class KeywordMapping extends Model
{
    use HasUuids;
    
    protected $keyType = 'string';
    public $incrementing = false;
    
    protected $fillable = [
        'id', 
        'keywords', 
        'category_name', 
        'confidence', 
        'is_active', 
        'created_by'
    ];
    
    protected $casts = [
        'keywords' => 'array',
        'confidence' => 'decimal:2',
        'is_active' => 'boolean',
    ];
    
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
    
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
    
    public function matchKeyword($text)
    {
        $text = strtolower($text);
        
        foreach ($this->keywords as $keyword) {
            if (str_contains($text, strtolower($keyword))) {
                return true;
            }
        }
        
        return false;
    }
}
