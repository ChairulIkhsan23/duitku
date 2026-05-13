<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Enums\CategoryType;

class Category extends Model
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
        'name',
        'type',
        'icon',
        'color',
        'budget_default',
        'is_default',
        'user_id'
    ];

    /**
     * Casting attribute ke tipe data tertentu
     */
    protected $casts = [
        'budget_default' => 'decimal:2',
        'is_default' => 'boolean',
        'type' => CategoryType::class,
    ];

    /**
     * Relasi: Category dimiliki oleh User (optional)
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relasi: Category memiliki banyak Transaction
     */
    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    /**
     * Relasi: Category memiliki banyak Budget
     */
    public function budgets()
    {
        return $this->hasMany(Budget::class);
    }
}