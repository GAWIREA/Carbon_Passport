<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Enums\UserLevel;
use App\Enums\UserRole;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'role'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'username',
        'avatar',
        'email',
        'password',
        'role',
        'points',
        'total_co2_saved',
        'total_co2_emitted',
        'current_streak',
        'last_activity_date',
        'xp',
        'coins',
        'monthly_points',
        'level',
    ];

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
            'role' => UserRole::class,
        ];
    }

    public function isUser(): bool
    {
        return $this->role === UserRole::User;
    }

    public function isSeller(): bool
    {
        return $this->role === UserRole::Seller;
    }

    public function isAdmin(): bool
    {
        return $this->role === UserRole::Admin;
    }

    // ---- Relationships ----

    /**
     * @return HasMany<CarbonLog, $this>
     */
    public function carbonLogs(): HasMany
    {
        return $this->hasMany(CarbonLog::class);
    }

    /**
     * @return HasMany<UserMission, $this>
     */
    public function userMissions(): HasMany
    {
        return $this->hasMany(UserMission::class);
    }

    /**
     * @return HasMany<Product, $this>
     */
    public function products(): HasMany
    {
        return $this->hasMany(Product::class, 'seller_id');
    }

    /**
     * @return HasMany<Order, $this>
     */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    /**
     * @return HasMany<Order, $this>
     */
    public function sales(): HasMany
    {
        return $this->hasMany(Order::class, 'seller_id');
    }

    /**
     * @return BelongsToMany<User, $this>
     */
    public function followers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'follows', 'following_id', 'follower_id')->withTimestamps();
    }

    /**
     * @return BelongsToMany<User, $this>
     */
    public function followings(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'follows', 'follower_id', 'following_id')->withTimestamps();
    }

    /**
     * @return HasMany<UserAchievement, $this>
     */
    public function userAchievements(): HasMany
    {
        return $this->hasMany(UserAchievement::class);
    }

    // ---- Social Helpers ----

    public function isFollowing(User $user): bool
    {
        return $this->followings()->where('following_id', $user->id)->exists();
    }

    public function isFollowedBy(User $user): bool
    {
        return $this->followers()->where('follower_id', $user->id)->exists();
    }

    // ---- Level & Gamification Helpers ----

    /**
     * Get the UserLevel enum for the current level.
     */
    public function getLevelInfo(): UserLevel
    {
        $level = $this->level ?? 1;
        $clamped = max(1, min(25, $level));

        return UserLevel::from($clamped);
    }

    /**
     * Get XP progress percentage toward the next level.
     */
    public function getXpProgress(): float
    {
        return $this->getLevelInfo()->progressPercent($this->xp ?? 0);
    }

    /**
     * Get XP remaining to reach next level.
     */
    public function getXpRemaining(): ?int
    {
        $nextXp = $this->getLevelInfo()->xpForNextLevel();

        if ($nextXp === null) {
            return null;
        }

        return max(0, $nextXp - ($this->xp ?? 0));
    }

    /**
     * Get achievements that are displayed on the user's profile (max 5).
     *
     * @return \Illuminate\Database\Eloquent\Collection<int, UserAchievement>
     */
    public function displayedAchievements()
    {
        return $this->userAchievements()
            ->where('is_displayed', true)
            ->with('achievement')
            ->limit(5)
            ->get();
    }
}
