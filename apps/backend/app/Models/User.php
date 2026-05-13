<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasApiTokens, HasUuids, Notifiable, HasFactory;

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
        'email',
        'password',
        'currency_code',
        'initial_balance',
        'streak_days',
        'last_transaction_date',
        'last_streak_date',
        'onboarding_template',
        'is_premium',
        'premium_until',
        'notification_token',
        'settings',
        'avatar'
    ];

    /**
     * Field yang disembunyikan dari response
     */
    protected $hidden = [
        'password',
        'remember_token'
    ];

    /**
     * Casting attribute ke tipe data tertentu
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'initial_balance' => 'decimal:2',
            'is_premium' => 'boolean',
            'premium_until' => 'datetime',
            'settings' => 'array',
        ];
    }

    /**
     * Relasi: User memiliki banyak Transaction
     */
    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    /**
     * Relasi: User memiliki banyak Budget
     */
    public function budgets()
    {
        return $this->hasMany(Budget::class);
    }

    /**
     * Relasi: User memiliki banyak Category
     */
    public function categories()
    {
        return $this->hasMany(Category::class);
    }

    /**
     * Relasi many-to-many dengan Badge
     */
    public function badges()
    {
        return $this->belongsToMany(Badge::class, 'user_badges')
            ->withPivot('awarded_at', 'progress_data')
            ->withTimestamps();
    }

    /**
     * Relasi: pivot table user_badges
     */
    public function userBadges()
    {
        return $this->hasMany(UserBadge::class);
    }

    /**
     * Relasi: User memiliki banyak ScheduledReport
     */
    public function scheduledReports()
    {
        return $this->hasMany(ScheduledReport::class);
    }

    /**
     * Relasi: User memiliki banyak Notification
     */
    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    /**
     * Relasi: User memiliki banyak Insight
     */
    public function insights()
    {
        return $this->hasMany(Insight::class);
    }

    /**
     * Relasi: Keyword mapping yang dibuat user
     */
    public function keywordMappings()
    {
        return $this->hasMany(KeywordMapping::class, 'created_by');
    }

    /**
     * Hitung saldo saat ini (initial + income - expense)
     */
    public function getCurrentBalanceAttribute()
    {
        $totalIncome = $this->transactions()
            ->where('type', 'income')
            ->sum('amount');

        $totalExpense = $this->transactions()
            ->where('type', 'expense')
            ->sum('amount');

        return $this->initial_balance + $totalIncome - $totalExpense;
    }
}