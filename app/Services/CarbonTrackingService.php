<?php

namespace App\Services;

use App\Data\ActivityCatalog;
use App\Models\CarbonLog;
use App\Models\User;
use App\Models\UserMission;
use Illuminate\Support\Carbon;

class CarbonTrackingService
{
    /**
     * Flat XP for logging emission-type activities (reward for data transparency).
     * Proportional XP is NOT used for emissions to avoid rewarding high-emission behavior.
     */
    public const LOG_XP = 5;

    /**
     * XP earned per kg CO₂ saved for saving-type activities.
     * Example: 10 kWh PLTS → 8.7 kg CO₂ saved → 87 XP.
     */
    public const XP_PER_KG_CO2 = 10;

    /** Minimum XP per input (ensures saving activities always give at least something). */
    public const MIN_XP = 5;

    /** Maximum XP per single input (anti-cheat cap). */
    public const MAX_XP_PER_INPUT = 100;

    /**
     * Max daily inputs that earn XP.
     * After this, the activity is still recorded but XP = 0.
     */
    public const MAX_XP_INPUTS_PER_DAY = 3;

    /**
     * Count how many XP-rewarded inputs a user has logged today.
     */
    public static function getTodayInputCount(int $userId): int
    {
        return CarbonLog::where('user_id', $userId)
            ->whereDate('date', Carbon::today())
            ->where('xp_earned', '>', 0)
            ->count();
    }

    /**
     * Process a carbon log entry.
     *
     * CO₂ calculation and plausibility checks are handled server-side using
     * ActivityCatalog — no hidden fields accepted from the form.
     *
     * @return array{success: bool, message: string, co2_amount?: float, xp_earned?: int, xp_capped?: bool, activity_type_label?: string, type?: string, level_up?: array<string, mixed>|null, achievements?: array<int, mixed>}
     */
    public function logActivity(
        int $userId,
        string $category,
        string $activityType,
        float $amount
    ): array {
        // 1. Look up activity definition from catalog
        $def = ActivityCatalog::find($category, $activityType);

        if ($def === null) {
            return [
                'success' => false,
                'message' => 'Aktivitas tidak ditemukan dalam katalog.',
            ];
        }

        // 2. Plausibility / anti-cheat check
        if ($amount > $def['limit']) {
            return [
                'success' => false,
                'message' => "Sistem Anti-Cheat: Nilai {$amount} {$def['unit']} melebihi batas wajar harian ({$def['limit']} {$def['unit']}) untuk aktivitas \"{$def['label']}\".",
            ];
        }

        $co2Amount = round($amount * $def['co2_per_unit'], 4);
        $isSaving  = $def['type'] === 'saving';

        // 3. Calculate XP (capped at MAX_XP_INPUTS_PER_DAY rewarded inputs per day)
        $xpInputsToday = self::getTodayInputCount($userId);
        $xpEarned      = 0;
        $xpCapped      = false;

        if ($xpInputsToday < self::MAX_XP_INPUTS_PER_DAY) {
            if ($isSaving) {
                // Proportional to CO₂ saved — reward real environmental action
                $xpEarned = (int) min(
                    self::MAX_XP_PER_INPUT,
                    max(self::MIN_XP, (int) round($co2Amount * self::XP_PER_KG_CO2))
                );
            } else {
                // Flat XP for logging emissions — reward transparency, not high emissions
                $xpEarned = self::LOG_XP;
            }
        } else {
            $xpCapped = true;
        }

        // 4. Save carbon log (always recorded regardless of XP cap)
        CarbonLog::create([
            'user_id'        => $userId,
            'category'       => $category,
            'activity_type'  => $activityType,
            'amount'         => $amount,
            'unit'           => $def['unit'],
            'co2_equivalent' => $isSaving ? 0 : $co2Amount,
            'co2_saved'      => $isSaving ? $co2Amount : 0,
            'points_earned'  => 0, // poin hanya dari misi, bukan dari tracking
            'xp_earned'      => $xpEarned,
            'date'           => Carbon::today(),
        ]);

        // 5. Update user stats
        $user          = User::find($userId);
        $levelUpResult = null;
        $achievementsUnlocked = [];

        if ($user) {
            // Update streak
            $lastActivity = $user->last_activity_date ? Carbon::parse($user->last_activity_date) : null;

            if (! $lastActivity) {
                $user->current_streak = 1;
            } elseif ($lastActivity->isYesterday()) {
                $user->current_streak += 1;
            } elseif (! $lastActivity->isToday()) {
                $user->current_streak = 1;
            }

            $user->last_activity_date = Carbon::today();
            $user->xp                += $xpEarned;

            // Update the correct CO₂ counter based on activity type
            if ($isSaving) {
                $user->total_co2_saved    += $co2Amount;
            } else {
                $user->total_co2_emitted  += $co2Amount;
            }

            $user->save();

            // 6. Check level up (may award coins automatically via LevelService)
            $levelService  = new LevelService;
            $levelUpResult = $levelService->checkLevelUp($user);

            // 7. Check achievements
            $achievementService   = new AchievementService;
            $achievementsUnlocked = $achievementService->checkAndUnlock($user);

            // If achievements gave XP, re-check level
            if (! empty($achievementsUnlocked)) {
                $user->refresh();
                $levelService->checkLevelUp($user);
            }
        }

        // 8. Update weekly mission progress (triggered by activity_type match)
        $missionCompleted = false;
        $missionReward    = '';

        if ($user) {
            $activeMission = UserMission::where('user_id', $userId)
                ->where('status', 'active')
                ->whereHas('mission', function ($q) use ($activityType) {
                    $q->where('activity_type', $activityType);
                })
                ->with('mission')
                ->first();

            if ($activeMission) {
                $activeMission->current_progress += 1;

                if ($activeMission->current_progress >= $activeMission->mission->target_amount) {
                    $activeMission->status       = 'done';
                    $activeMission->completed_at = now();

                    $missionCompleted = true;
                    $missionReward    = "Misi Selesai! Klaim reward-mu sekarang di Dashboard.";
                }

                $activeMission->save();
            }
        }

        // Update Daily Mission progress
        $categoryToDailyTitle = [
            'transportasi' => 'Catat 1 Aktivitas Transportasi',
            'makanan'      => 'Catat 1 Aktivitas Makanan',
            'energi'       => 'Catat 1 Aktivitas Energi',
        ];
        
        $dailyTitle = $categoryToDailyTitle[$category] ?? null;
        if ($dailyTitle && $user) {
            $dailyMission = \App\Models\UserDailyMission::where('user_id', $user->id)
                ->where('title', $dailyTitle)
                ->where('status', 'active')
                ->whereDate('created_at', now()->toDateString())
                ->first();
                
            if ($dailyMission) {
                $dailyMission->progress += 1;
                if ($dailyMission->progress >= $dailyMission->target) {
                    $dailyMission->status = 'done';
                    // User needs to manually claim this later
                }
                $dailyMission->save();
            }
        }

        // 9. Build response message
        $co2Label = $isSaving
            ? "🌿 -{$co2Amount} kg CO₂ (hemat)"
            : "⚠️ +{$co2Amount} kg CO₂ (emisi)";

        $message = "Aktivitas \"{$def['label']}\" berhasil dicatat! {$co2Label}";

        if ($xpEarned > 0) {
            $message .= " | +{$xpEarned} XP";
        }

        if ($missionCompleted) {
            $message .= " | 🎯 Misi selesai! ({$missionReward})";
        }

        if ($levelUpResult) {
            $coinsAwarded = $levelUpResult['coins_awarded'] ?? 0;
            $message .= " | 🎉 Level Up ke {$levelUpResult['new_level_info']->label()}! +{$coinsAwarded} Koin";
        }

        if (! empty($achievementsUnlocked)) {
            $names = array_map(fn ($a) => $a['achievement']->name, $achievementsUnlocked);
            $message .= ' | 🏆 Achievement: ' . implode(', ', $names);
        }

        return [
            'success'             => true,
            'message'             => $message,
            'co2_amount'          => $co2Amount,
            'activity_type_label' => $def['label'],
            'type'                => $def['type'],
            'xp_earned'           => $xpEarned,
            'xp_capped'           => $xpCapped,
            'level_up'            => $levelUpResult,
            'achievements'        => $achievementsUnlocked,
        ];
    }
}
