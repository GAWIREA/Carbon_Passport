<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Achievement extends Model
{
    protected $guarded = [];

    /**
     * @return HasMany<UserAchievement, $this>
     */
    public function userAchievements(): HasMany
    {
        return $this->hasMany(UserAchievement::class);
    }
}
