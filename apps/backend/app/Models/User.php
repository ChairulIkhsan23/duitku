<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Laravel\Sanctum\HasApiTokens;


class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasUuids, Notifiable, HasFactory;

    protected $keyType = 'string';
    public $incrementing = false;

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

    protected $hidden = ['password', 'remember_token'];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
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

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }
    
    public function budgets()
    {
        return $this->hasMany(Budget::class);
    }
    
    public function categories()
    {
        return $this->hasMany(Category::class);
    }

    public function badges()
    {
        return $this->belongsToMany(Badge::class, 'user_badges')
            ->withPivot('awarded_at', 'progress_data')
            ->withTimestamps();
    }

    public function userBadges()
    {
        return $this->hasMany(UserBadge::class);
    }

    public function scheduledReports()
    {
        return $this->hasMany(ScheduledReport::class);
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    public function insights()
    {
        return $this->hasMany(Insight::class);
    }

    public function keywordMappings()
    {
        return $this->hasMany(KeywordMapping::class, 'created_by');
    }

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
