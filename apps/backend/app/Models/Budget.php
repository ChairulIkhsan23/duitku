<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Enums\BudgetStatus;

class Budget extends Model
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
        'user_id',
        'category_id',
        'month_year',
        'limit_amount',
        'spent_amount',
        'notification_sent'
    ];

    /**
     * Casting attribute ke tipe data tertentu
     */
    protected $casts = [
        'limit_amount' => 'decimal:2',
        'spent_amount' => 'decimal:2',
        'notification_sent' => 'array',
        'month_year' => 'datetime:Y-m-d',
    ];

    /**
     * Attribute tambahan yang ikut direturn di response
     */
    protected $appends = [
        'status'
    ];

    /**
     * Relasi: Budget dimiliki oleh User
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relasi: Budget dimiliki oleh Category
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Persentase penggunaan budget
     */
    public function getPercentageAttribute()
    {
        if ($this->limit_amount <= 0) return 0;

        return round(($this->spent_amount / $this->limit_amount) * 100, 2);
    }

    /**
     * Sisa budget yang belum terpakai
     */
    public function getRemainingAttribute()
    {
        return max(0, $this->limit_amount - $this->spent_amount);
    }

    /**
     * Cek apakah budget sudah overspent
     */
    public function isOverspent(): bool
    {
        return $this->status === BudgetStatus::OVERSPENT;
    }

    /**
     * Cek apakah budget dalam kondisi warning
     */
    public function isWarning(): bool
    {
        return $this->status === BudgetStatus::WARNING;
    }

    /**
     * Status budget berdasarkan persentase penggunaan
     */
    public function getStatusAttribute(): BudgetStatus
    {
        if ($this->percentage >= 100) {
            return BudgetStatus::OVERSPENT;
        }

        if ($this->percentage >= 80) {
            return BudgetStatus::WARNING;
        }

        return BudgetStatus::GOOD;
    }
}