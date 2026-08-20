<?php

namespace App\Services;

use App\Enums\UserLevel;
use App\Models\User;

class LevelService
{
    /**
     * Calculate the correct level from total XP.
     */
    public function calculateLevel(int $xp): int
    {
        return UserLevel::fromXp($xp)->value;
    }

    /**
     * Check if a user has leveled up and update accordingly.
     * Automatically awards coins when a level up is detected.
     *
     * @return array{leveled_up: bool, old_level: int, new_level: int, new_level_info: UserLevel, coins_awarded: int}|null
     */
    public function checkLevelUp(User $user): ?array
    {
        $oldLevel = $user->level ?? 1;
        $newLevel = $this->calculateLevel($user->xp ?? 0);

        if ($newLevel > $oldLevel) {
            $coinsAwarded = $this->coinRewardForLevel($newLevel);

            $user->level = $newLevel;
            $user->coins = ($user->coins ?? 0) + $coinsAwarded;
            $user->save();

            return [
                'leveled_up'     => true,
                'old_level'      => $oldLevel,
                'new_level'      => $newLevel,
                'new_level_info' => UserLevel::from($newLevel),
                'coins_awarded'  => $coinsAwarded,
            ];
        }

        return null;
    }

    /**
     * Get XP needed to reach the next level from the current level.
     */
    public function getXpForNextLevel(int $currentLevel): ?int
    {
        $level = UserLevel::from(max(1, min(25, $currentLevel)));

        return $level->xpForNextLevel();
    }

    /**
     * Calculate coin reward for reaching a given level.
     * Formula matches the display in level-details.blade.php: 500 + (level × 100).
     *
     * Level  1 →   600 Koin
     * Level  5 → 1,000 Koin
     * Level 10 → 1,500 Koin
     * Level 25 → 3,000 Koin
     */
    private function coinRewardForLevel(int $level): int
    {
        return 500 + ($level * 100);
    }
}
