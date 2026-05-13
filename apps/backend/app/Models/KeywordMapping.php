<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class KeywordMapping extends Model
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
        'keywords',
        'category_name',
        'confidence',
        'is_active',
        'created_by'
    ];

    /**
     * Casting attribute ke tipe data tertentu
     */
    protected $casts = [
        'keywords' => 'array',
        'confidence' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    /**
     * Relasi: pembuat keyword mapping
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Scope: hanya data aktif
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Cek apakah text cocok dengan keyword mapping
     */
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