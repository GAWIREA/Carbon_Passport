<?php

namespace App\Services;

use App\Models\Achievement;
use App\Models\CarbonLog;
use App\Models\User;
use App\Models\UserAchievement;
use App\Models\UserMission;

class AchievementService
{
    /**
     * Check all achievements and unlock any that the user now qualifies for.
     *
     * @return array<int, array{achievement: Achievement, xp_reward: int}>
     */
    public function checkAndUnlock(User $user): array
    {
        $unlocked = [];
        $achievements = Achievement::all();
        $existingIds = $user->userAchievements()->pluck('achievement_id')->toArray();

        foreach ($achievements as $achievement) {
            if (in_array($achievement->id, $existingIds)) {
                continue;
            }

            if ($this->meetsRequirement($user, $achievement)) {
                UserAchievement::create([
                    'user_id' => $user->id,
                    'achievement_id' => $achievement->id,
                    'unlocked_at' => now(),
                ]);

                if ($achievement->xp_reward > 0) {
                    $user->xp += $achievement->xp_reward;
                    $user->save();
                }

                $unlocked[] = [
                    'achievement' => $achievement,
                    'xp_reward' => $achievement->xp_reward,
                ];
            }
        }

        return $unlocked;
    }

    /**
     * Check if a user meets the requirement for a specific achievement.
     */
    private function meetsRequirement(User $user, Achievement $achievement): bool
    {
        $value = $achievement->requirement_value;

        return match ($achievement->requirement_type) {
            'total_logs' => CarbonLog::where('user_id', $user->id)->count() >= $value,
            'streak' => ($user->current_streak ?? 0) >= $value,
            'level' => ($user->level ?? 1) >= $value,
            'total_xp' => ($user->xp ?? 0) >= $value,
            'mission_completed' => UserMission::where('user_id', $user->id)
                ->where('status', 'done')
                ->count() >= $value,
            'total_co2_saved' => ($user->total_co2_saved ?? 0) >= $value,
            'followers' => $user->followers()->count() >= $value,
            default => false,
        };
    }
}
