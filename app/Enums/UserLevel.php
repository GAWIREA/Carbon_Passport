<?php

namespace App\Enums;

enum UserLevel: int
{
    // Tunas (Sprout) — Level 1-5
    case TunasV = 1;
    case TunasIV = 2;
    case TunasIII = 3;
    case TunasII = 4;
    case TunasI = 5;

    // Daun (Leaf) — Level 6-10
    case DaunV = 6;
    case DaunIV = 7;
    case DaunIII = 8;
    case DaunII = 9;
    case DaunI = 10;

    // Pohon (Tree) — Level 11-15
    case PohonV = 11;
    case PohonIV = 12;
    case PohonIII = 13;
    case PohonII = 14;
    case PohonI = 15;

    // Bumi (Earth) — Level 16-20
    case BumiV = 16;
    case BumiIV = 17;
    case BumiIII = 18;
    case BumiII = 19;
    case BumiI = 20;

    // Matahari (Sun) — Level 21-25
    case MatahariV = 21;
    case MatahariIV = 22;
    case MatahariIII = 23;
    case MatahariII = 24;
    case MatahariI = 25;

    /**
     * XP thresholds for each level.
     *
     * @return array<int, int>
     */
    public static function xpThresholds(): array
    {
        return [
            1 => 0,
            2 => 100,
            3 => 250,
            4 => 450,
            5 => 700,
            6 => 1000,
            7 => 1400,
            8 => 1900,
            9 => 2500,
            10 => 3200,
            11 => 4000,
            12 => 5000,
            13 => 6200,
            14 => 7600,
            15 => 9200,
            16 => 11000,
            17 => 13000,
            18 => 15500,
            19 => 18500,
            20 => 22000,
            21 => 26000,
            22 => 30500,
            23 => 35500,
            24 => 41000,
            25 => 47000,
        ];
    }

    /**
     * Get the tier name (Tunas, Daun, Pohon, Bumi, Matahari).
     */
    public function tier(): string
    {
        return match (true) {
            $this->value <= 5 => 'Tunas',
            $this->value <= 10 => 'Daun',
            $this->value <= 15 => 'Pohon',
            $this->value <= 20 => 'Bumi',
            default => 'Matahari',
        };
    }

    /**
     * Get the stage within the tier (V, IV, III, II, I).
     */
    public function stage(): string
    {
        $stages = ['V', 'IV', 'III', 'II', 'I'];
        $positionInTier = ($this->value - 1) % 5;

        return $stages[$positionInTier];
    }

    /**
     * Get the icon emoji for the tier.
     */
    public function icon(): string
    {
        return match (true) {
            $this->value <= 5 => '🌱',
            $this->value <= 10 => '🌿',
            $this->value <= 15 => '🌳',
            $this->value <= 20 => '🌍',
            default => '☀️',
        };
    }

    /**
     * Get the full display label (e.g. "🌱 Tunas V").
     */
    public function label(): string
    {
        return $this->icon() . ' ' . $this->tier() . ' ' . $this->stage();
    }

    /**
     * Get XP required to reach this level.
     */
    public function xpThreshold(): int
    {
        return self::xpThresholds()[$this->value];
    }

    /**
     * Get XP required to reach the next level (null if max).
     */
    public function xpForNextLevel(): ?int
    {
        $thresholds = self::xpThresholds();

        if ($this->value >= 25) {
            return null;
        }

        return $thresholds[$this->value + 1];
    }

    /**
     * Calculate the current level from total XP.
     */
    public static function fromXp(int $xp): self
    {
        $thresholds = self::xpThresholds();
        $level = 1;

        foreach ($thresholds as $lvl => $threshold) {
            if ($xp >= $threshold) {
                $level = $lvl;
            }
        }

        return self::from($level);
    }

    /**
     * Get XP progress percentage toward the next level.
     */
    public function progressPercent(int $currentXp): float
    {
        $currentThreshold = $this->xpThreshold();
        $nextThreshold = $this->xpForNextLevel();

        if ($nextThreshold === null) {
            return 100.0;
        }

        $range = $nextThreshold - $currentThreshold;

        if ($range <= 0) {
            return 100.0;
        }

        $progress = $currentXp - $currentThreshold;

        return min(100.0, round(($progress / $range) * 100, 1));
    }

    /**
     * Get the color associated with this tier for UI.
     */
    public function color(): string
    {
        return match (true) {
            $this->value <= 5 => '#4CAF50',
            $this->value <= 10 => '#66BB6A',
            $this->value <= 15 => '#8D6E63',
            $this->value <= 20 => '#42A5F5',
            default => '#FFA726',
        };
    }
}
