<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Enums\TransactionType;

class Transaction extends Model
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

    /**
     * Casting attribute ke tipe data tertentu
     */
    protected $casts = [
        'amount' => 'decimal:2',
        'date' => 'date',
        'is_duplicate' => 'boolean',
        'metadata' => 'array',
        'type' => TransactionType::class,
    ];

    /**
     * Relasi: Transaction dimiliki oleh User
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relasi: Transaction dimiliki oleh Category
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}