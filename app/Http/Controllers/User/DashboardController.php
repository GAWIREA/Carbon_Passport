<?php

namespace App\Http\Controllers\User;

use App\Data\ActivityCatalog;
use App\Http\Controllers\Controller;
use App\Services\CarbonTrackingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class DashboardController extends Controller
{
    private function getMockLeaderboard(int $limit = 10): array
    {
        $user = Auth::user();
        $users = \App\Models\User::where('role', 'user')
            ->orderByDesc('monthly_points')
            ->take($limit)
            ->get();

        $leaderboard = [];
        $emojis = ['🦊', '🐧', '🐼', '🐸', '🐝', '🐳', '🐨', '🐯', '🐰', '🦁'];
        $medals = ['gold', 'silver', 'bronze'];

        foreach ($users as $index => $u) {
            $isMe = $u->id === $user->id;
            $leaderboard[] = [
                'id' => $u->id,
                'rank' => $index + 1,
                'name' => $u->name,
                'username' => $u->username,
                'avatar' => $u->avatar,
                'dept' => 'Engineering',
                'pts' => $u->monthly_points ?? 0,
                'emoji' => $emojis[$index % 10] ?? '👤',
                'medal' => $medals[$index] ?? null,
                'me' => $isMe,
                'level' => $u->getLevelInfo()->label(),
            ];
        }

        return $leaderboard;
    }

    public function dashboard(): View
    {
        $user = Auth::user();

        $currentMonthStart = now()->startOfMonth();
        $currentMonthEnd = now()->endOfMonth();
        $lastMonthStart = now()->subMonth()->startOfMonth();
        $lastMonthEnd = now()->subMonth()->endOfMonth();

        $totalCo2Month = (float) \App\Models\CarbonLog::where('user_id', $user->id)
            ->whereBetween('date', [$currentMonthStart->toDateString(), $currentMonthEnd->toDateString()])
            ->sum('co2_equivalent');

        $totalCo2LastMonth = (float) \App\Models\CarbonLog::where('user_id', $user->id)
            ->whereBetween('date', [$lastMonthStart->toDateString(), $lastMonthEnd->toDateString()])
            ->sum('co2_equivalent');

        $deltaMonth    = $totalCo2Month - $totalCo2LastMonth;
        $co2TargetMonth= 50.0; // target bulanan kg

        $co2Saved      = $user->total_co2_saved ?? 0;
        $recProgress   = 3;
        $recTotal      = 5;
        $streak        = $user->current_streak ?? 0;
        $isStreakActiveToday = $user->last_activity_date && \Carbon\Carbon::parse($user->last_activity_date)->isToday();
        $points        = $user->monthly_points ?? 0;
        $coins         = $user->coins ?? 0;
        $xp            = $user->xp ?? 0;
        $levelInfo     = $user->getLevelInfo();
        $xpProgress    = $user->getXpProgress();
        $xpRemaining   = $user->getXpRemaining();

        // 1. Monthly Trend (Last 6 months)
        $monthlyTrend = [];
        for ($i = 5; $i >= 0; $i--) {
            $monthStart = now()->subMonthsNoOverflow($i)->startOfMonth();
            $monthEnd = now()->subMonthsNoOverflow($i)->endOfMonth();
            
            $total = \App\Models\CarbonLog::where('user_id', $user->id)
                ->whereBetween('date', [$monthStart->toDateString(), $monthEnd->toDateString()])
                ->sum('co2_equivalent');
                
            $monthlyTrend[] = [
                'label' => $monthStart->translatedFormat('M'),
                'value' => round((float)$total, 2)
            ];
        }

        // 2. Daily Emissions (Last 7 days)
        $dailyEmissions = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            
            $total = \App\Models\CarbonLog::where('user_id', $user->id)
                ->where('date', $date->toDateString())
                ->sum('co2_equivalent');
                
            $dailyEmissions[] = [
                'label' => $date->translatedFormat('D j'),
                'value' => round((float)$total, 2)
            ];
        }

        // 3. Category Breakdown
        // Fetch category breakdown from actual data
        $catLogs = \App\Models\CarbonLog::where('user_id', $user->id)
            ->whereBetween('date', [$currentMonthStart->toDateString(), $currentMonthEnd->toDateString()])
            ->selectRaw('category, SUM(co2_equivalent) as total')
            ->groupBy('category')
            ->pluck('total', 'category')->toArray();

        // If no data this month, try fetching all-time
        if (empty($catLogs)) {
            $catLogs = \App\Models\CarbonLog::where('user_id', $user->id)
                ->selectRaw('category, SUM(co2_equivalent) as total')
                ->groupBy('category')
                ->pluck('total', 'category')->toArray();
        }

        $totalCat = array_sum($catLogs);
        
        $categoryBreakdown = [];
        $catColors = [
            'transportasi' => '#5B8FFF',
            'makanan' => '#2ECC71',
            'energi' => '#F5A623',
            'bahan_bakar' => '#D14E44',
            'limbah' => '#8B6BF2',
            'air' => '#1ABC9C',
            'energi_terbarukan' => '#34495E',
        ];
        
        $catLabels = [
            'transportasi' => 'Transportasi',
            'makanan' => 'Makanan',
            'energi' => 'Energi & Listrik',
            'bahan_bakar' => 'Bahan Bakar',
            'limbah' => 'Limbah',
            'air' => 'Air',
            'energi_terbarukan' => 'Energi Terbarukan',
        ];

        foreach ($catLabels as $k => $label) {
            $v = $catLogs[$k] ?? 0;
            $percent = $totalCat > 0 ? round(($v / $totalCat) * 100) : 0;
            
            $categoryBreakdown[] = [
                'label' => $label,
                'value' => round((float)$v, 2),
                'percent' => $percent,
                'color' => $catColors[$k]
            ];
        }

        // Sort by value descending
        usort($categoryBreakdown, fn($a, $b) => $b['value'] <=> $a['value']);

        // Jika tidak ada data sama sekali
        if ($totalCat == 0) {
            $categoryBreakdown = [
                ['label' => 'Belum ada data', 'value' => 100, 'percent' => 100, 'color' => '#E2E8F0']
            ];
        }

        // 4. Weekly Compare
        $weeklyCompare = [];
        $thisWeekStart = now()->startOfWeek();
        $lastWeekStart = now()->subWeek()->startOfWeek();
        
        for ($i = 0; $i < 7; $i++) {
            $thisDate = $thisWeekStart->copy()->addDays($i);
            $lastDate = $lastWeekStart->copy()->addDays($i);
            
            $thisTotal = \App\Models\CarbonLog::where('user_id', $user->id)
                ->where('date', $thisDate->toDateString())
                ->sum('co2_equivalent');
                
            $lastTotal = \App\Models\CarbonLog::where('user_id', $user->id)
                ->where('date', $lastDate->toDateString())
                ->sum('co2_equivalent');
                
            $weeklyCompare[] = [
                'label' => $thisDate->translatedFormat('D'),
                'thisWeek' => round((float)$thisTotal, 2),
                'lastWeek' => round((float)$lastTotal, 2)
            ];
        }

        $leaderboard = $this->getMockLeaderboard(5);

        // Assign missing missions to user automatically
        $missions = \App\Models\Mission::where('is_active', true)->get();
        foreach ($missions as $mission) {
            \App\Models\UserMission::firstOrCreate([
                'user_id' => $user->id,
                'mission_id' => $mission->id,
            ], [
                'status' => 'active',
                'current_progress' => 0,
                'started_at' => now(),
            ]);
        }

        $userMissions = \App\Models\UserMission::with('mission')
            ->where('user_id', $user->id)
            ->whereIn('status', ['active', 'done', 'claimed'])
            ->get();

        $startOfWeek = now()->startOfWeek();
        $startOfDay = now()->startOfDay();

        $weeklyMissions = [];
        $dailyMissions = [];

        foreach ($userMissions as $um) {
            $m = $um->mission;
            if (!$m) continue;

            $startedAt = \Carbon\Carbon::parse($um->started_at);

            // Reset logic based on mission type
            if ($m->type === 'weekly' && $startedAt->isBefore($startOfWeek)) {
                $um->current_progress = 0;
                $um->status = 'active';
                $um->started_at = now();
                $um->save();
            } elseif ($m->type === 'daily' && $startedAt->isBefore($startOfDay)) {
                $um->current_progress = 0;
                $um->status = 'active';
                $um->started_at = now();
                $um->save();
            }

            $icon = '🎯';
            if ($m->activity_type === 'sepeda') $icon = '🚲';
            elseif ($m->activity_type === 'listrik_plts') $icon = '💡';
            elseif ($m->activity_type === 'makanan_nabati') $icon = '🥗';
            // ... add more if needed

            $activityLabel = $m->category;
            foreach (\App\Data\ActivityCatalog::forJs() as $cat => $acts) {
                if (isset($acts[$m->activity_type])) {
                    $activityLabel = $acts[$m->activity_type]['label'];
                    break;
                }
            }

            $missionData = [
                'id' => $um->id,
                'icon' => $icon,
                'bg' => 'var(--secondary-light)', // For daily view
                'title' => $m->title,
                'cat' => $m->category, // For daily view
                'category' => $m->category, // For weekly view
                'activity_label' => $activityLabel,
                'impact' => 'Sesuai aktivitas', // For daily view
                'type' => $m->activity_type, // (weekly expects type to be activity_type)
                'progress' => $um->current_progress, // for weekly
                'done' => $um->current_progress, // for daily
                'target' => $m->target_amount,
                'reward_points' => $m->reward_points,
                'reward_coins' => $m->reward_coins ?? 0,
                'daysLeft' => max(0, \Carbon\Carbon::parse($um->started_at)->addDays($m->duration_days)->diffInDays(now())),
                'color' => '#5B8FFF',
                'status' => $um->status,
            ];

            if ($m->type === 'weekly') {
                $weeklyMissions[] = $missionData;
            } else {
                $dailyMissions[] = $missionData;
            }
        }

        $sorter = function($m) {
            $status = $m['status'] ?? 'active';
            if ($status === 'claimed') return 2;
            if ($status === 'done') return 1;
            return 0;
        };

        $weeklyMissions = collect($weeklyMissions)->sortBy($sorter)->values()->all();
        $dailyMissions = collect($dailyMissions)->sortBy($sorter)->values()->all();

        // Heatmap: Current Month Calendar
        $currentMonthStart = now()->startOfMonth();
        $daysInMonth = $currentMonthStart->daysInMonth;
        $startDayOfWeek = $currentMonthStart->dayOfWeekIso; // 1 (Mon) to 7 (Sun)
        
        $logsPerDay = \App\Models\CarbonLog::selectRaw('DATE(date) as date_val, COUNT(*) as count')
            ->where('user_id', $user->id)
            ->where('date', '>=', $currentMonthStart->toDateString())
            ->where('date', '<=', now()->endOfMonth()->toDateString())
            ->groupBy('date_val')
            ->pluck('count', 'date_val');

        $heatmap = [];
        
        // Padding awal bulan
        for ($i = 1; $i < $startDayOfWeek; $i++) {
            $heatmap[] = ['level' => -1, 'day' => ''];
        }

        for ($i = 0; $i < $daysInMonth; $i++) {
            $currentDateObj = $currentMonthStart->copy()->addDays($i);
            $currentDate = $currentDateObj->toDateString();
            $count = $logsPerDay->get($currentDate, 0);
            
            $level = 0;
            if ($count >= 5) $level = 3;
            else if ($count >= 3) $level = 2;
            else if ($count >= 1) $level = 1;

            $isFuture = $currentDateObj->startOfDay()->gt(now()->startOfDay());

            $heatmap[] = [
                'level' => $level,
                'day' => $currentDateObj->format('j'),
                'is_future' => $isFuture,
            ];
        }

        // Padding akhir bulan agar konsisten 6 baris (42 kotak)
        while (count($heatmap) < 42) {
            $heatmap[] = ['level' => -1, 'day' => '', 'is_future' => false];
        }

        // Product Recommendations

        // Product Recommendations
        $productRecs = [
            [
                'type' => 'product', 'cat' => 'Transportasi', 'icon' => '🚆', 'bg' => 'var(--primary-light)',
                'title' => 'Voucher Commuter Line 50rb', 'sub' => 'Tukar/beli produk',
                'impact' => 'Cocok dengan kebiasaanmu', 'coin_price' => 500, 'price' => 'atau Rp 50.000',
                'product_id' => 1,
            ],
            [
                'type' => 'product', 'cat' => 'Transportasi', 'icon' => '🚲', 'bg' => 'var(--primary-light)',
                'title' => 'Diskon Servis Sepeda 30%', 'sub' => 'Tukar/beli produk',
                'impact' => 'Dukung kebiasaan bersepeda', 'coin_price' => 400, 'price' => 'atau Rp 75.000',
                'product_id' => 2,
            ],
            [
                'type' => 'product', 'cat' => 'Makanan', 'icon' => '🍱', 'bg' => 'var(--primary-light)',
                'title' => 'Lunch Box Bambu Set', 'sub' => 'Tukar/beli produk',
                'impact' => 'Kurangi kemasan sekali pakai', 'coin_price' => 350, 'price' => 'atau Rp 120.000',
                'product_id' => 3,
            ],
            [
                'type' => 'product', 'cat' => 'Energi & Listrik', 'icon' => '🔌', 'bg' => 'var(--primary-light)',
                'title' => 'Smart Plug Hemat Energi', 'sub' => 'Tukar/beli produk',
                'impact' => 'Kontrol perangkat dari HP', 'coin_price' => 450, 'price' => 'atau Rp 85.000',
                'product_id' => 5,
            ],
        ];

        // Fetch actual recent emissions/savings from carbon_logs
        $recentEmissionsQuery = \App\Models\CarbonLog::where('user_id', $user->id)
            ->orderBy('date', 'desc')
            ->orderBy('created_at', 'desc')
            ->limit(4)
            ->get();

        $recentEmissions = [];
        foreach ($recentEmissionsQuery as $log) {
            $isSaving = $log->co2_saved > 0;
            $co2Val   = $isSaving ? $log->co2_saved : $log->co2_equivalent;
            $recentEmissions[] = [
                'title' => \App\Data\ActivityCatalog::find($log->category, $log->activity_type)['label']
                    ?? ucwords(str_replace('_', ' ', $log->activity_type)),
                'date'  => \Carbon\Carbon::parse($log->date)->translatedFormat('d M Y'),
                'val'   => $isSaving ? '-' . $co2Val . ' kg' : '+' . $co2Val . ' kg',
                'type'  => $isSaving ? 'success' : 'danger',
            ];
        }

        if (empty($recentEmissions)) {
            $recentEmissions = [
                ['title' => 'Belum ada data', 'date' => '-', 'val' => '-', 'type' => 'warning'],
            ];
        }

        $totalEmissionCount = \App\Models\CarbonLog::where('user_id', $user->id)->count();

        // Fetch recent XP earnings (from carbon_logs where xp_earned > 0)
        $totalXpCount = \App\Models\CarbonLog::where('user_id', $user->id)
            ->where('xp_earned', '>', 0)
            ->count();

        $recentXpQuery = \App\Models\CarbonLog::where('user_id', $user->id)
            ->where('xp_earned', '>', 0)
            ->orderBy('date', 'desc')
            ->orderBy('created_at', 'desc')
            ->limit(9)
            ->get();

        $recentPoints = [];
        foreach ($recentXpQuery as $log) {
            $recentPoints[] = [
                'title' => 'Input ' . ucfirst(str_replace('_', ' ', $log->activity_type)),
                'date' => \Carbon\Carbon::parse($log->date)->translatedFormat('d M Y'),
                'pts' => '+' . $log->xp_earned . ' XP',
            ];
        }

        if (empty($recentPoints)) {
            $recentPoints = [
                ['title' => 'Belum ada XP', 'date' => '-', 'pts' => '+0'],
            ];
        }

        // Achievements for dashboard
        $displayedAchievements = $user->displayedAchievements();

        return view('user.dashboard', compact(
            'user', 'totalCo2Month', 'deltaMonth', 'co2TargetMonth', 'co2Saved',
            'recProgress', 'recTotal', 'streak', 'isStreakActiveToday', 'points', 'coins', 'xp', 'levelInfo', 'xpProgress', 'xpRemaining',
            'monthlyTrend', 'dailyEmissions', 'categoryBreakdown', 'weeklyCompare',
            'leaderboard', 'weeklyMissions', 'heatmap', 'dailyMissions', 'productRecs', 'recentEmissions', 'recentPoints',
            'displayedAchievements', 'totalEmissionCount', 'totalXpCount'
        ));
    }

    public function completeDailyMission(Request $request): RedirectResponse
    {
        $request->validate([
            'image' => 'required|image|max:2048',
            'title' => 'required|string',
            'reward_points' => 'required|integer',
            'reward_coins' => 'required|integer',
            'impact' => 'required|numeric',
            'target' => 'required|integer',
        ]);

        $user = Auth::user();

        // Save image
        $path = $request->file('image')->store('daily_missions', 'public');

        // Track progress
        $mission = \App\Models\UserDailyMission::firstOrCreate([
            'user_id' => $user->id,
            'title' => $request->title,
        ], [
            'progress' => 0,
            'target' => $request->target,
            'status' => 'active',
        ]);

        if ($mission->status === 'done') {
            return back()->with('info', 'Misi ini sudah selesai sebelumnya.');
        }

        $mission->progress += 1;

        if ($mission->progress >= $mission->target) {
            $mission->progress = $mission->target;
            $mission->status = 'done';
            
            // Impact is awarded immediately, but points/coins must be claimed
            $user->total_co2_saved = ($user->total_co2_saved ?? 0) + $request->impact;
            $user->save();

            // Log in CarbonLog (points are 0 until claimed, or just record it as impact only)
            \App\Models\CarbonLog::create([
                'user_id' => $user->id,
                'category' => $request->category ?? 'Daily Mission',
                'activity_type' => 'daily_mission',
                'amount' => 1,
                'unit' => 'task',
                'co2_equivalent' => $request->impact,
                'co2_saved' => $request->impact,
                'points_earned' => 0,
                'xp_earned' => 0,
                'date' => now()->toDateString(),
            ]);

            $mission->save();

            return back()->with('success', 'Misi harian "' . $request->title . '" berhasil diselesaikan! Jangan lupa klaim reward-mu.');
        }

        $mission->save();

        return back()->with('success', 'Progress misi harian "' . $request->title . '" bertambah menjadi ' . $mission->progress . '/' . $mission->target . '. Lanjutkan!');
    }

    public function claimWeeklyMission($id): RedirectResponse
    {
        $user = Auth::user();
        $mission = \App\Models\UserMission::with('mission')
            ->where('id', $id)
            ->where('user_id', $user->id)
            ->where('status', 'done')
            ->first();

        if (!$mission) {
            return back()->with('error', 'Misi tidak ditemukan atau belum selesai.');
        }

        $user->monthly_points = ($user->monthly_points ?? 0) + $mission->mission->reward_points;
        $user->coins = ($user->coins ?? 0) + ($mission->mission->reward_coins ?? 0);
        $user->save();

        $mission->status = 'claimed';
        $mission->save();

        return back()->with('success', "Berhasil klaim {$mission->mission->reward_points} Poin & {$mission->mission->reward_coins} Koin!");
    }

    public function claimDailyMission(Request $request): RedirectResponse
    {
        $user = Auth::user();
        $mission = \App\Models\UserDailyMission::where('title', $request->title)
            ->where('user_id', $user->id)
            ->where('status', 'done')
            ->first();

        if (!$mission) {
            return back()->with('error', 'Misi tidak ditemukan atau belum selesai.');
        }

        $user->monthly_points = ($user->monthly_points ?? 0) + $request->reward_points;
        $user->coins = ($user->coins ?? 0) + $request->reward_coins;
        $user->save();

        $mission->status = 'claimed';
        $mission->save();

        return back()->with('success', "Berhasil klaim {$request->reward_points} Poin & {$request->reward_coins} Koin!");
    }

    public function tracking(): View
    {
        return view('user.tracking', [
            'user'            => Auth::user(),
            'activityCatalog' => ActivityCatalog::forJs(),
            'categoryLabels'  => ActivityCatalog::categoryLabels(),
            'xpPerKgCo2'      => CarbonTrackingService::XP_PER_KG_CO2,
            'logXp'           => CarbonTrackingService::LOG_XP,
            'maxXpInputs'     => CarbonTrackingService::MAX_XP_INPUTS_PER_DAY,
            'todayInputCount' => CarbonTrackingService::getTodayInputCount(Auth::id()),
        ]);
    }

    public function profile(): View
    {
        $user = Auth::user();
        $levelInfo = $user->getLevelInfo();
        $xpProgress = $user->getXpProgress();

        // Fetch all user achievements
        $userAchievements = $user->userAchievements()
            ->with('achievement')
            ->orderByDesc('unlocked_at')
            ->get();

        // Displayed achievements (pinned to profile)
        $displayedAchievements = $user->displayedAchievements();

        return view('user.profile', [
            'user' => $user,
            'streak' => $user->current_streak ?? 0,
            'followers' => $user->followers()->count(),
            'following' => $user->followings()->count(),
            'joinedAt' => $user->created_at->translatedFormat('d F Y'),
            'levelInfo' => $levelInfo,
            'xpProgress' => $xpProgress,
            'xpRemaining' => $user->getXpRemaining(),
            'userAchievements' => $userAchievements,
            'displayedAchievements' => $displayedAchievements,
        ]);
    }

    public function editProfile(): View
    {
        return view('user.edit-profile', ['user' => Auth::user()]);
    }

    public function updateProfile(Request $request): RedirectResponse
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|regex:/^[a-zA-Z0-9_\.]+$/|unique:users,username,' . $user->id,
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ], [
            'username.regex' => 'Username hanya boleh berisi huruf, angka, titik, dan garis bawah.',
        ]);

        $user->name = $validated['name'];
        $user->username = $validated['username'];

        if ($request->hasFile('avatar')) {
            $path = $request->file('avatar')->store('avatars', 'public');
            $user->avatar = $path;
        }

        $user->save();

        return redirect()->route('user.profile')->with('status', 'Profil berhasil diperbarui!');
    }

    public function settings(): View
    {
        return view('user.settings', ['user' => Auth::user()]);
    }

    public function storeTracking(Request $request, CarbonTrackingService $service): RedirectResponse
    {
        $validated = $request->validate([
            'category'      => ['required', 'string', Rule::in(ActivityCatalog::categoryKeys())],
            'activity_type' => ['required', 'string', Rule::in(ActivityCatalog::allActivityKeys())],
            'amount'        => 'required|numeric|min:0.01',
        ]);

        // Extra: ensure activity_type belongs to selected category
        $validKeysForCategory = ActivityCatalog::activityKeysForCategory($validated['category']);
        if (! in_array($validated['activity_type'], $validKeysForCategory)) {
            return back()
                ->with('error', 'Aktivitas tidak sesuai dengan kategori yang dipilih.')
                ->withInput();
        }

        $result = $service->logActivity(
            Auth::id(),
            $validated['category'],
            $validated['activity_type'],
            (float) $validated['amount']
        );

        if (! $result['success']) {
            return back()->with('error', $result['message'])->withInput();
        }

        return redirect()->route('user.history')->with('status', $result['message']);
    }

    public function history(): View
    {
        $user = Auth::user();

        // All logs ordered by date desc, with pagination
        $logs = \App\Models\CarbonLog::where('user_id', $user->id)
            ->orderBy('date', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(25);

        // Per-category CO₂ totals for the current month (emission side only)
        $monthStart = \Carbon\Carbon::now()->startOfMonth();
        $monthEnd   = \Carbon\Carbon::now()->endOfMonth();

        $categoryTotals = \App\Models\CarbonLog::where('user_id', $user->id)
            ->whereBetween('date', [$monthStart->toDateString(), $monthEnd->toDateString()])
            ->selectRaw('category, SUM(co2_equivalent) as total_emitted, SUM(co2_saved) as total_saved')
            ->groupBy('category')
            ->get()
            ->keyBy('category');

        // Overall monthly totals
        $monthlyEmitted = $categoryTotals->sum('total_emitted');
        $monthlySaved   = $categoryTotals->sum('total_saved');
        $monthlyNet     = $monthlySaved - $monthlyEmitted;

        return view('user.history', [
            'user'           => $user,
            'logs'           => $logs,
            'categoryTotals' => $categoryTotals,
            'monthlyEmitted' => round($monthlyEmitted, 2),
            'monthlySaved'   => round($monthlySaved, 2),
            'monthlyNet'     => round($monthlyNet, 2),
        ]);
    }

    public function recommendations(): View
    {
        $user = Auth::user();

        // 1. Array Rekomendasi
        $recommendations = [
            [
                'type' => 'action', 'cat' => 'Transportasi', 'icon' => '🚲', 'bg' => 'var(--secondary-light)',
                'title' => 'Catat 1 Aktivitas Transportasi', 'sub' => 'Lacak jejak karbon transportasi hari ini',
                'impact' => 'Sesuai aktivitas', 'reward_points' => 15, 'reward_coins' => 10,
                'target' => 1,
            ],
            [
                'type' => 'action', 'cat' => 'Makanan', 'icon' => '🥗', 'bg' => 'var(--secondary-light)',
                'title' => 'Catat 1 Aktivitas Makanan', 'sub' => 'Lacak jejak karbon makanan hari ini',
                'impact' => 'Sesuai aktivitas', 'reward_points' => 15, 'reward_coins' => 10,
                'target' => 1,
            ],
            [
                'type' => 'action', 'cat' => 'Energi & Listrik', 'icon' => '💡', 'bg' => 'var(--secondary-light)',
                'title' => 'Catat 1 Aktivitas Energi & Listrik', 'sub' => 'Lacak jejak karbon energi hari ini',
                'impact' => 'Sesuai aktivitas', 'reward_points' => 15, 'reward_coins' => 10,
                'target' => 1,
            ],
            [
                'type' => 'product', 'cat' => 'Transportasi', 'icon' => '🚆', 'bg' => 'var(--primary-light)',
                'title' => 'Voucher Commuter Line 50rb', 'sub' => 'Tukar/beli produk',
                'impact' => 'Cocok dengan kebiasaanmu', 'coin_price' => 500, 'price' => 'atau Rp 50.000',
                'product_id' => 1,
            ],
            [
                'type' => 'product', 'cat' => 'Transportasi', 'icon' => '🚲', 'bg' => 'var(--primary-light)',
                'title' => 'Diskon Servis Sepeda 30%', 'sub' => 'Tukar/beli produk',
                'impact' => 'Dukung kebiasaan bersepeda', 'coin_price' => 400, 'price' => 'atau Rp 75.000',
                'product_id' => 2,
            ],
            [
                'type' => 'product', 'cat' => 'Makanan', 'icon' => '🍱', 'bg' => 'var(--primary-light)',
                'title' => 'Lunch Box Bambu Set', 'sub' => 'Tukar/beli produk',
                'impact' => 'Kurangi kemasan sekali pakai', 'coin_price' => 350, 'price' => 'atau Rp 120.000',
                'product_id' => 3,
            ],
            [
                'type' => 'product', 'cat' => 'Energi & Listrik', 'icon' => '🔌', 'bg' => 'var(--primary-light)',
                'title' => 'Smart Plug Hemat Energi', 'sub' => 'Tukar/beli produk',
                'impact' => 'Kontrol perangkat dari HP', 'coin_price' => 450, 'price' => 'atau Rp 85.000',
                'product_id' => 5,
            ],
        ];

        // Ensure these 3 exist in DB for today
        foreach ($recommendations as $rec) {
            if ($rec['type'] === 'action') {
                $existing = \App\Models\UserDailyMission::where('user_id', $user->id)
                    ->where('title', $rec['title'])
                    ->whereDate('created_at', now()->toDateString())
                    ->first();
                
                if (!$existing) {
                    \App\Models\UserDailyMission::create([
                        'user_id' => $user->id,
                        'title' => $rec['title'],
                        'target' => $rec['target'],
                        'progress' => 0,
                        'status' => 'active'
                    ]);
                }
            }
        }

        // 2. Fetch UserDailyMission records for this user FOR TODAY
        $userDaily = \App\Models\UserDailyMission::where('user_id', $user->id)
            ->whereDate('created_at', now()->toDateString())
            ->get()->keyBy('title');

        // 3. Map progress
        foreach ($recommendations as &$rec) {
            if ($rec['type'] === 'action') {
                $title = $rec['title'];
                $target = $rec['target'] ?? 1;

                if (isset($userDaily[$title])) {
                    $rec['done'] = $userDaily[$title]->progress;
                    $rec['status'] = $userDaily[$title]->status;
                } else {
                    $rec['done'] = 0;
                    $rec['status'] = 'active';
                }
                $rec['target'] = $target;
            }
        }

        // We also need the real weeklyMissions here since they were hardcoded in blade
        $userMissions = \App\Models\UserMission::with('mission')
            ->where('user_id', $user->id)
            ->whereIn('status', ['active', 'done', 'claimed'])
            ->get();

        $weeklyMissions = [];
        foreach ($userMissions as $um) {
            $m = $um->mission;
            $icon = '🎯';
            if ($m->activity_type === 'sepeda') {
                $icon = '🚲';
            }
            if ($m->activity_type === 'botol') {
                $icon = '💧';
            }
            if ($m->activity_type === 'krl') {
                $icon = '🚆';
            }

            $weeklyMissions[] = [
                'icon' => $icon,
                'title' => $m->title,
                'category' => $m->category,
                'type' => $m->activity_type,
                'progress' => $um->current_progress,
                'target' => $m->target_amount,
                'reward_points' => $m->reward_points,
                'reward_coins' => $m->reward_coins ?? 0,
                'daysLeft' => max(0, \Carbon\Carbon::parse($um->started_at)->addDays($m->duration_days)->diffInDays(now())),
                'color' => '#5B8FFF',
                'status' => $um->status,
            ];
        }

        $weeklyMissions = collect($weeklyMissions)->sortBy(function($m) {
            $status = $m['status'] ?? 'active';
            if ($status === 'claimed') return 2;
            if ($status === 'done') return 1;
            return 0;
        })->values()->all();

        // Sort recommendations so completed/claimed actions are at the bottom
        $recommendations = collect($recommendations)->sortBy(function($r) {
            $status = $r['status'] ?? 'active';
            if ($status === 'claimed') return 2;
            if ($status === 'done') return 1;
            return 0;
        })->values()->all();

        return view('user.recommendations', [
            'user' => $user,
            'recommendations' => $recommendations,
            'weeklyMissions' => $weeklyMissions,
        ]);
    }

    public function leaderboard(): View
    {
        $user = Auth::user();
        $leaderboard = $this->getMockLeaderboard(10);

        return view('user.leaderboard', [
            'user' => $user,
            'leaderboard' => $leaderboard,
        ]);
    }

    public function achievements(): View
    {
        $user = Auth::user();

        // Ambil semua achievement dan group by category
        $allAchievements = \App\Models\Achievement::all()->groupBy('category');

        // Data progress user untuk setiap requirement_type
        $userProgress = [
            'total_logs' => \App\Models\CarbonLog::where('user_id', $user->id)->count(),
            'streak' => $user->current_streak ?? 0,
            'level' => $user->level ?? 1,
            'total_xp' => $user->xp ?? 0,
            'mission_completed' => \App\Models\UserMission::where('user_id', $user->id)->where('status', 'done')->count(),
            'total_co2_saved' => $user->total_co2_saved ?? 0,
            'followers' => $user->followers()->count(),
        ];

        // Ambil ID achievement yang sudah di-unlock oleh user beserta tanggalnya
        $unlockedAchievements = $user->userAchievements()->pluck('unlocked_at', 'achievement_id')->toArray();

        return view('user.achievements', compact('user', 'allAchievements', 'userProgress', 'unlockedAchievements'));
    }

    public function completeTask(Request $request): RedirectResponse
    {
        return back()->with('status', 'Task selesai! Streak & poin diperbarui.');
    }

    public function marketplace(): View
    {
        // Mock data products (now with coin_price)
        $products = [
            ['id' => 1, 'name' => 'Voucher Commuter Line 50rb', 'category' => 'Transportasi', 'coin_price' => 500, 'type' => 'voucher', 'image' => '🚆'],
            ['id' => 2, 'name' => 'Tumbler Stainless Steel', 'category' => 'Lifestyle', 'coin_price' => 750, 'type' => 'physical', 'image' => '🥤'],
            ['id' => 3, 'name' => 'Voucher Diskon Sayurbox 20%', 'category' => 'Makanan', 'coin_price' => 300, 'type' => 'voucher', 'image' => '🥦'],
            ['id' => 4, 'name' => 'Tas Belanja Ramah Lingkungan', 'category' => 'Lifestyle', 'coin_price' => 400, 'type' => 'physical', 'image' => '🛍️'],
        ];

        return view('user.marketplace', [
            'user' => Auth::user(),
            'products' => $products,
        ]);
    }

    public function productDetail(int $id): View
    {
        // Mock data product detail (now with coin_price)
        $products = [
            1 => [
                'id' => 1, 'name' => 'Voucher Commuter Line 50rb', 'category' => 'Transportasi',
                'coin_price' => 500, 'type' => 'voucher', 'image' => '🚆', 'stock' => 15,
                'description' => 'Tukarkan koinmu dengan voucher Commuter Line senilai Rp 50.000. Berlaku untuk semua rute KRL di Jabodetabek. Voucher akan dikirimkan ke email yang terdaftar.',
            ],
            2 => [
                'id' => 2, 'name' => 'Tumbler Stainless Steel', 'category' => 'Lifestyle',
                'coin_price' => 750, 'type' => 'physical', 'image' => '🥤', 'stock' => 5,
                'description' => 'Kurangi penggunaan plastik sekali pakai dengan Tumbler Stainless Steel eksklusif dari Selaras. Tahan panas dan dingin hingga 12 jam.',
            ],
            3 => [
                'id' => 3, 'name' => 'Voucher Diskon Sayurbox 20%', 'category' => 'Makanan',
                'coin_price' => 300, 'type' => 'voucher', 'image' => '🥦', 'stock' => 50,
                'description' => 'Dapatkan diskon 20% untuk pembelanjaan produk segar di Sayurbox. Dukung petani lokal dan kurangi jejak karbon panganmu.',
            ],
            4 => [
                'id' => 4, 'name' => 'Tas Belanja Ramah Lingkungan', 'category' => 'Lifestyle',
                'coin_price' => 400, 'type' => 'physical', 'image' => '🛍️', 'stock' => 10,
                'description' => 'Tas belanja lipat yang kuat dan praktis dibawa kemana-mana. Alternatif terbaik untuk mengganti kantong plastik.',
            ],
            5 => [
                'id' => 5, 'name' => 'Smart Plug Hemat Energi', 'category' => 'Energi & Listrik',
                'coin_price' => 450, 'type' => 'physical', 'image' => '🔌', 'stock' => 20,
                'description' => 'Smart Plug yang dapat membantu mengontrol perangkat dari HP untuk menghemat listrik.',
            ],
        ];

        $product = $products[$id] ?? null;

        if (! $product) {
            abort(404, 'Produk tidak ditemukan');
        }

        return view('user.product-detail', [
            'user' => Auth::user(),
            'product' => $product,
        ]);
    }

    public function buyProduct(int $id): RedirectResponse
    {
        $user = Auth::user();

        // Mock data product detail (sync with productDetail method)
        $products = [
            1 => ['id' => 1, 'name' => 'Voucher Commuter Line 50rb', 'coin_price' => 500, 'stock' => 15],
            2 => ['id' => 2, 'name' => 'Tumbler Stainless Steel', 'coin_price' => 750, 'stock' => 5],
            3 => ['id' => 3, 'name' => 'Voucher Diskon Sayurbox 20%', 'coin_price' => 300, 'stock' => 50],
            4 => ['id' => 4, 'name' => 'Tas Belanja Ramah Lingkungan', 'coin_price' => 400, 'stock' => 10],
            5 => ['id' => 5, 'name' => 'Smart Plug Hemat Energi', 'coin_price' => 450, 'stock' => 20],
        ];

        $product = $products[$id] ?? null;

        if (! $product) {
            abort(404, 'Produk tidak ditemukan');
        }

        if ($product['stock'] <= 0) {
            return back()->with('error', 'Stok produk habis.');
        }

        if (($user->coins ?? 0) < $product['coin_price']) {
            return back()->with('error', 'Koin tidak cukup untuk membeli produk ini. Koin saat ini: ' . ($user->coins ?? 0));
        }

        // Deduct coins
        $user->coins -= $product['coin_price'];
        
        // Add Bonus XP (Bypassing daily cap)
        $bonusXp = 150;
        $user->xp += $bonusXp;
        $user->save();

        // Check for level up & achievements
        app(\App\Services\LevelService::class)->checkLevelUp($user);
        app(\App\Services\AchievementService::class)->checkAndUnlock($user);

        return back()->with('success', 'Berhasil membeli ' . $product['name'] . '! Kamu mendapatkan bonus +' . $bonusXp . ' XP.');
    }

    public function levelDetails(): View
    {
        $user = Auth::user();
        
        $levels = \App\Enums\UserLevel::cases();
        
        // Fetch XP history
        $xpHistoryQuery = \App\Models\CarbonLog::where('user_id', $user->id)
            ->where('xp_earned', '>', 0)
            ->orderBy('date', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();
            
        $xpHistory = [];
        foreach ($xpHistoryQuery as $log) {
            $xpHistory[] = [
                'title' => 'Aktivitas: ' . ucfirst(str_replace('_', ' ', $log->activity_type)),
                'date' => \Carbon\Carbon::parse($log->date)->translatedFormat('d F Y'),
                'xp' => '+' . $log->xp_earned,
            ];
        }

        return view('user.level-details', compact('user', 'levels', 'xpHistory'));
    }

    public function getChartData(Request $request)
    {
        $user = Auth::user();
        $type = $request->query('type');
        
        $data = ['labels' => [], 'datasets' => []];

        if ($type === 'trend') {
            $start = $request->query('start'); // format 'YYYY-MM'
            $end = $request->query('end'); // format 'YYYY-MM'
            
            if ($start && $end) {
                $startDate = \Carbon\Carbon::createFromFormat('Y-m', $start)->startOfMonth();
                $endDate = \Carbon\Carbon::createFromFormat('Y-m', $end)->startOfMonth();
                
                $monthsDiff = $startDate->diffInMonths($endDate);
                
                $labels = [];
                $values = [];
                
                for ($i = 0; $i <= $monthsDiff; $i++) {
                    $currentMonth = $startDate->copy()->addMonthsNoOverflow($i);
                    $mStart = $currentMonth->copy()->startOfMonth();
                    $mEnd = $currentMonth->copy()->endOfMonth();
                    
                    $total = \App\Models\CarbonLog::where('user_id', $user->id)
                        ->whereBetween('date', [$mStart->toDateString(), $mEnd->toDateString()])
                        ->sum('co2_equivalent');
                        
                    $labels[] = $mStart->translatedFormat('M');
                    $values[] = round((float)$total, 2);
                }
                
                $data['labels'] = $labels;
                $data['datasets'] = [
                    [
                        'label' => 'Emisi (kg CO₂e)',
                        'data' => $values,
                        'borderColor' => '#5B8FFF',
                        'backgroundColor' => 'rgba(91,143,255,0.10)',
                        'fill' => true,
                        'tension' => 0.4,
                        'pointBackgroundColor' => '#5B8FFF',
                        'pointRadius' => 5,
                        'pointBorderColor' => '#fff',
                        'pointBorderWidth' => 2,
                        'borderWidth' => 3
                    ]
                ];
            }
        } elseif ($type === 'daily') {
            $start = $request->query('start'); // format 'Y-m-d'
            $end = $request->query('end'); // format 'Y-m-d'
            
            if ($start && $end) {
                $startDate = \Carbon\Carbon::parse($start);
                $endDate = \Carbon\Carbon::parse($end);
                $daysDiff = $startDate->diffInDays($endDate);
                
                $labels = [];
                $values = [];
                
                for ($i = 0; $i <= $daysDiff; $i++) {
                    $currentDate = $startDate->copy()->addDays($i);
                    
                    $total = \App\Models\CarbonLog::where('user_id', $user->id)
                        ->where('date', $currentDate->toDateString())
                        ->sum('co2_equivalent');
                        
                    $labels[] = $currentDate->translatedFormat('D j');
                    $values[] = round((float)$total, 2);
                }
                
                $data['labels'] = $labels;
                $data['datasets'] = [
                    [
                        'label' => 'kg CO₂e',
                        'data' => $values,
                        'borderColor' => '#F5A623',
                        'backgroundColor' => 'transparent',
                        'tension' => 0.4,
                        'pointBackgroundColor' => '#F5A623',
                        'pointRadius' => 4,
                        'pointBorderColor' => '#fff',
                        'pointBorderWidth' => 2,
                        'borderWidth' => 3
                    ]
                ];
            }
        } elseif ($type === 'category') {
            $catType = $request->query('catType'); // 'all' or specific category
            $periodType = $request->query('periodType'); // 'month' or 'week'
            $periodInput = $request->query('periodInput'); // 'Y-m' or 'Y-Wxx'
            
            $query = \App\Models\CarbonLog::where('user_id', $user->id);
            
            if ($catType && $catType !== 'all') {
                $query->where('category', $catType);
            }
            
            if ($periodType === 'month' && $periodInput) {
                $start = \Carbon\Carbon::createFromFormat('Y-m', $periodInput)->startOfMonth();
                $end = clone $start;
                $end->endOfMonth();
                $query->whereBetween('date', [$start->toDateString(), $end->toDateString()]);
            } elseif ($periodType === 'week' && $periodInput) {
                // e.g. 2026-W27
                $year = (int) substr($periodInput, 0, 4);
                $week = (int) substr($periodInput, 6);
                $start = \Carbon\Carbon::now()->setISODate($year, $week)->startOfWeek();
                $end = clone $start;
                $end->endOfWeek();
                $query->whereBetween('date', [$start->toDateString(), $end->toDateString()]);
            }
            
            $catLogs = $query->selectRaw('category, SUM(co2_equivalent) as total')
                ->groupBy('category')
                ->pluck('total', 'category')->toArray();
                
            $totalCat = array_sum($catLogs);
            $categoryBreakdown = [];
            
            $catColors = [
                'transportasi' => '#5B8FFF', 'makanan' => '#2ECC71', 'energi' => '#F5A623',
                'bahan_bakar' => '#D14E44', 'limbah' => '#8B6BF2', 'air' => '#1ABC9C', 'energi_terbarukan' => '#34495E',
            ];
            $catLabels = [
                'transportasi' => 'Transportasi', 'makanan' => 'Makanan', 'energi' => 'Energi & Listrik',
                'bahan_bakar' => 'Bahan Bakar', 'limbah' => 'Limbah', 'air' => 'Air', 'energi_terbarukan' => 'Energi Terbarukan',
            ];
            
            $categoryBreakdown = [];
            if ($totalCat > 0) {
                foreach ($catLabels as $k => $label) {
                    if ($catType !== 'all' && $catType !== $k) continue;
                    $v = $catLogs[$k] ?? 0;
                    if ($v > 0) {
                        $percent = round(($v / $totalCat) * 100);
                        $categoryBreakdown[] = [
                            'label' => $label,
                            'value' => round((float)$v, 2),
                            'percent' => $percent,
                            'color' => $catColors[$k] ?? '#ccc'
                        ];
                    }
                }
                usort($categoryBreakdown, fn($a, $b) => $b['value'] <=> $a['value']);
            } else {
                $categoryBreakdown = [['label' => 'Belum ada data', 'value' => 100, 'percent' => 100, 'color' => '#E2E8F0']];
            }
            
            $data['labels'] = array_column($categoryBreakdown, 'label');
            $data['datasets'] = [
                [
                    'data' => array_column($categoryBreakdown, 'value'),
                    'backgroundColor' => array_column($categoryBreakdown, 'color'),
                    'borderWidth' => 0
                ]
            ];
            $data['breakdown'] = $categoryBreakdown;
            
        } elseif ($type === 'compare') {
            $compareType = $request->query('compareType'); // mingguan, bulanan, tahunan
            $comp1 = $request->query('comp1');
            $comp2 = $request->query('comp2');
            
            if ($compareType === 'mingguan') {
                // $comp1 = 2026-W26, $comp2 = 2026-W27
                $y1 = (int) substr($comp1, 0, 4); $w1 = (int) substr($comp1, 6);
                $y2 = (int) substr($comp2, 0, 4); $w2 = (int) substr($comp2, 6);
                
                $start1 = \Carbon\Carbon::now()->setISODate($y1, $w1)->startOfWeek();
                $start2 = \Carbon\Carbon::now()->setISODate($y2, $w2)->startOfWeek();
                
                $data['labels'] = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];
                $d1 = []; $d2 = [];
                
                for ($i = 0; $i < 7; $i++) {
                    $dt1 = $start1->copy()->addDays($i);
                    $dt2 = $start2->copy()->addDays($i);
                    
                    $d1[] = round((float)\App\Models\CarbonLog::where('user_id', $user->id)->where('date', $dt1->toDateString())->sum('co2_equivalent'), 2);
                    $d2[] = round((float)\App\Models\CarbonLog::where('user_id', $user->id)->where('date', $dt2->toDateString())->sum('co2_equivalent'), 2);
                }
                
                $data['datasets'] = [
                    ['label' => 'Minggu 1', 'data' => $d1, 'backgroundColor' => '#E2E8F0', 'borderRadius' => 6],
                    ['label' => 'Minggu 2', 'data' => $d2, 'backgroundColor' => '#5B8FFF', 'borderRadius' => 6]
                ];
                
            } elseif ($compareType === 'bulanan') {
                // $comp1 = 2026-06, $comp2 = 2026-07
                $start1 = \Carbon\Carbon::createFromFormat('Y-m', $comp1)->startOfMonth();
                $start2 = \Carbon\Carbon::createFromFormat('Y-m', $comp2)->startOfMonth();
                
                $data['labels'] = ['Minggu 1', 'Minggu 2', 'Minggu 3', 'Minggu 4', 'Minggu 5'];
                $d1 = [0,0,0,0,0]; $d2 = [0,0,0,0,0];
                
                $logs1 = \App\Models\CarbonLog::where('user_id', $user->id)->whereBetween('date', [$start1->toDateString(), $start1->copy()->endOfMonth()->toDateString()])->get();
                foreach($logs1 as $log) {
                    $weekIdx = min(4, floor((\Carbon\Carbon::parse($log->date)->day - 1) / 7));
                    $d1[$weekIdx] += $log->co2_equivalent;
                }
                
                $logs2 = \App\Models\CarbonLog::where('user_id', $user->id)->whereBetween('date', [$start2->toDateString(), $start2->copy()->endOfMonth()->toDateString()])->get();
                foreach($logs2 as $log) {
                    $weekIdx = min(4, floor((\Carbon\Carbon::parse($log->date)->day - 1) / 7));
                    $d2[$weekIdx] += $log->co2_equivalent;
                }
                
                $data['datasets'] = [
                    ['label' => 'Bulan 1', 'data' => $d1, 'backgroundColor' => '#E2E8F0', 'borderRadius' => 6],
                    ['label' => 'Bulan 2', 'data' => $d2, 'backgroundColor' => '#5B8FFF', 'borderRadius' => 6]
                ];
                
            } elseif ($compareType === 'tahunan') {
                // $comp1 = 2025, $comp2 = 2026
                $data['labels_1'] = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun'];
                $data['labels_2'] = ['Jul', 'Ags', 'Sep', 'Okt', 'Nov', 'Des'];
                
                $d1 = array_fill(0, 12, 0); $d2 = array_fill(0, 12, 0);
                
                $logs1 = \App\Models\CarbonLog::where('user_id', $user->id)->whereYear('date', $comp1)->get();
                foreach($logs1 as $log) {
                    $d1[\Carbon\Carbon::parse($log->date)->month - 1] += $log->co2_equivalent;
                }
                $logs2 = \App\Models\CarbonLog::where('user_id', $user->id)->whereYear('date', $comp2)->get();
                foreach($logs2 as $log) {
                    $d2[\Carbon\Carbon::parse($log->date)->month - 1] += $log->co2_equivalent;
                }
                
                // Paging for tahunan is handled on client, send all 12
                $data['datasets'] = [
                    'year1' => $d1,
                    'year2' => $d2
                ];
            }
        }
        
        return response()->json($data);
    }
}
