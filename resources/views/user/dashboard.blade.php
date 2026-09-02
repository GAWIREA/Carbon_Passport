@extends('layouts.app')
@section('title', 'Beranda')
@section('page-title', 'Beranda')

@php
    $active = 'dashboard';
@endphp

@section('content')
    <div class="page-head">
        <div>
            <h1>Halo, {{ $user->name }} 👋</h1>
            <p>Ringkasan jejak karbon dan progres misi pengurangan emisimu.</p>
        </div>
        <a href="{{ route('user.tracking') }}" class="btn btn-primary">+ Catat Emisi Sekarang</a>
    </div>

    <div class="bento">

        {{-- Carbon Status Ring --}}
        <div class="card bento-c1" style="display:flex; flex-direction:column;">
            <div class="card-head">
                <div><span class="card-chip">{{ \Carbon\Carbon::now()->translatedFormat('M Y') }}</span>
                    <div class="card-title">Pengeluaran Emisi Karbon</div>
                </div>
                <a href="{{ route('user.tracking') }}" class="card-arrow" style="text-decoration:none;">›</a>
            </div>
            <div class="chart-box" style="min-height:130px; max-height:150px; position:relative;">
                <canvas id="ringChart"></canvas>
                <div
                    style="position:absolute;inset:0;display:flex;flex-direction:column;align-items:center;justify-content:center;pointer-events:none;">
                    <div style="font-size:22px;font-weight:800;color:var(--text-dark);">{{ $totalCo2Month }}<span
                            style="font-size:12px;font-weight:500;color:var(--text-light);"> kg</span></div>
                    <div style="font-size:11px;color:var(--text-light);">dari {{ $co2TargetMonth }} kg target</div>
                </div>
            </div>
            <div style="text-align:center;margin-top:10px;">
                @if ($deltaMonth > 0)
                    <span class="stat-delta up">▲ +{{ $deltaMonth }} kg</span>
                    <div class="stat-sub" style="margin-top:4px;">Anda membuang karbon lebih banyak dibanding bulan lalu
                    </div>
                @else
                    <span class="stat-delta down">▼ {{ abs($deltaMonth) }} kg</span>
                    <div class="stat-sub" style="margin-top:4px;">Anda menghemat karbon dibanding bulan lalu</div>
                @endif
            </div>

            <div
                style="margin-top:24px; border-top:1px dashed var(--border); padding-top:16px; display:flex; flex-direction:column; flex:1;">
                <div
                    style="font-size:11.5px; font-weight:700; color:var(--text-light); text-transform:uppercase; letter-spacing:0.5px; margin-bottom:14px;">
                    Aktivitas Karbon Terakhir</div>
                <div style="display:flex; flex-direction:column; gap:24px;">
                    @foreach ($recentEmissions as $re)
                        <div style="display:flex; justify-content:space-between; align-items:center;">
                            <div>
                                <div style="font-size:13px; font-weight:600; color:var(--text-dark);">{{ $re['title'] }}
                                </div>
                                <div style="font-size:11px; color:var(--text-light); margin-top:2px;">{{ $re['date'] }}
                                </div>
                            </div>
                            @if ($re['type'] === 'danger')
                                <div
                                    style="font-size:13px; font-weight:800; color:#E74C3C; background:rgba(231,76,60,0.1); padding:4px 10px; border-radius:12px;">
                                    {{ $re['val'] }}</div>
                            @elseif($re['type'] === 'warning')
                                <div
                                    style="font-size:13px; font-weight:800; color:#F39C12; background:rgba(243,156,18,0.1); padding:4px 10px; border-radius:12px;">
                                    {{ $re['val'] }}</div>
                            @else
                                <div
                                    style="font-size:13px; font-weight:800; color:#2ECC71; background:rgba(46,204,113,0.1); padding:4px 10px; border-radius:12px;">
                                    {{ $re['val'] }}</div>
                            @endif
                        </div>
                    @endforeach
                </div>
                @if (isset($totalEmissionCount) && $totalEmissionCount > 4)
                    <div style="text-align:center; margin-top:auto; padding-top:12px; border-top:1px solid var(--border);">
                        <a href="{{ route('user.history') }}"
                            style="font-size:12px; font-weight:600; color:var(--text-light); text-decoration:none;">
                            +{{ $totalEmissionCount - 4 }} riwayat lainnya
                        </a>
                    </div>
                @endif
            </div>
        </div>

        {{-- Climate Identity --}}
        <div class="card bento-c1" style="display:flex; flex-direction:column;">
            <div class="card-head">
                <div>
                    <div class="card-title" style="margin-top:6px;">Progres Level</div>
                </div>
                <a href="{{ route('user.level.details') }}" class="card-arrow" style="text-decoration:none;">›</a>
            </div>

            <div style="display:flex; flex-direction:column; gap:12px; margin-top:4px;">
                <div style="display:flex; align-items:center; justify-content:space-between;">
                    <div style="display:flex; align-items:center; gap:12px;">
                        <div class="stat-icon"
                            style="margin:0; background:{{ $levelInfo->color() }}18; color:{{ $levelInfo->color() }}; width: 42px; height: 42px; font-size: 20px;">
                            {{ $levelInfo->icon() }}</div>
                        <div>
                            <div style="font-size: 16px; font-weight: 800; color: var(--text-dark);">Level
                                {{ $levelInfo->value }}</div>
                            <div style="font-size: 12px; font-weight: 600; color: {{ $levelInfo->color() }};">
                                {{ $levelInfo->label() }}</div>
                        </div>
                    </div>
                    <div style="text-align:right;">
                        @php
                            $currentLvlXp = $xp - $levelInfo->xpThreshold();
                            $nextLvlXp = $levelInfo->xpForNextLevel()
                                ? $levelInfo->xpForNextLevel() - $levelInfo->xpThreshold()
                                : null;
                        @endphp
                        @if ($nextLvlXp)
                            <div style="font-size:14px; font-weight:800; color:var(--primary);">{{ $currentLvlXp }} <span
                                    style="color:var(--text-light); font-size:12px; font-weight:600;">/ {{ $nextLvlXp }}
                                    XP</span></div>
                        @else
                            <div style="font-size:14px; font-weight:800; color:var(--primary);">Max Level</div>
                        @endif
                    </div>
                </div>

                @if ($nextLvlXp)
                    <div class="progress-track" style="height: 8px; border-radius: 12px; background: #EEF1F5;">
                        <div class="progress-fill"
                            style="width:{{ $xpProgress }}%; background: var(--primary); height: 100%; border-radius: 12px;">
                        </div>
                    </div>
                @endif
            </div>

            <div
                style="margin-top:24px; border-top:1px dashed var(--border); padding-top:16px; display:flex; flex-direction:column; flex:1;">
                <div
                    style="font-size:11.5px; font-weight:700; color:var(--text-light); text-transform:uppercase; letter-spacing:0.5px; margin-bottom:14px;">
                    Riwayat XP Terbaru</div>
                <div style="display:flex; flex-direction:column; gap:12px;">
                    @foreach ($recentPoints as $rp)
                        <div style="display:flex; justify-content:space-between; align-items:center;">
                            <div>
                                <div style="font-size:13px; font-weight:600; color:var(--text-dark);">{{ $rp['title'] }}
                                </div>
                                <div style="font-size:11px; color:var(--text-light); margin-top:2px;">{{ $rp['date'] }}
                                </div>
                            </div>
                            <div
                                style="font-size:13px; font-weight:800; color:#2ECC71; background:rgba(46,204,113,0.1); padding:4px 10px; border-radius:12px;">
                                {{ $rp['pts'] }}</div>
                        </div>
                    @endforeach
                </div>
                @if (isset($totalXpCount) && $totalXpCount > 9)
                    <div style="text-align:center; margin-top:auto; padding-top:12px; border-top:1px solid var(--border);">
                        <a href="{{ route('user.history') }}"
                            style="font-size:12px; font-weight:600; color:var(--text-light); text-decoration:none;">
                            +{{ $totalXpCount - 9 }} riwayat lainnya
                        </a>
                    </div>
                @endif
            </div>
        </div>

        {{-- Streak Heatmap --}}
        <div class="card bento-c2" style="display:flex; flex-direction:column;">
            <div class="card-head">
                <div><span class="card-chip">{{ now()->translatedFormat('M Y') }}</span>
                    <div class="card-title" style="margin-top:6px;">🔥 Streak Heatmap</div>
                    <p style="font-size:12.5px;color:var(--text-light);margin-top:2px;">Aktivitas input emisi bulan
                        {{ now()->translatedFormat('F Y') }}</p>
                </div>
                @if($isStreakActiveToday)
                <div
                    style="background: linear-gradient(135deg, #FF9D2E, #FF5C00); color: white; padding: 6px 12px; border-radius: 8px; font-size: 13px; font-weight: 700; box-shadow: 0 4px 10px rgba(255, 92, 0, 0.3); display: inline-flex; align-items: center; gap: 4px;">
                    <span style="font-size:16px;">🔥</span> Streak {{ $streak }} Hari!
                </div>
                @else
                <div
                    style="background: #F1F3F5; color: #ADB5BD; padding: 6px 12px; border-radius: 8px; font-size: 13px; font-weight: 700; border: 1px solid #DEE2E6; display: inline-flex; align-items: center; gap: 4px;">
                    <span style="font-size:16px; filter: grayscale(100%); opacity: 0.5;">🔥</span> Streak {{ $streak }} Hari
                </div>
                @endif
            </div>
            <div style="margin-top:auto;">
                <div class="heatmap-grid">
                    @foreach (['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'] as $dayLabel)
                        <div class="heatmap-label">{{ $dayLabel }}</div>
                    @endforeach
                    @foreach ($heatmap as $idx => $cell)
                        @if ($cell['level'] == -1)
                            <div class="heatmap-cell" style="background: transparent;"></div>
                        @else
                            @php
                                $isFuture = $cell['is_future'] ?? false;
                                $cellClass = 'level-' . $cell['level'];
                                $cellStyle = '';
                                $textColor = $cell['level'] > 0 ? '#fff' : 'var(--text-light)';
                                if ($isFuture) {
                                    $cellClass = '';
                                    $cellStyle = 'background: #fff; border: 1px solid #ccecffff;';
                                    $textColor = 'var(--text-light)';
                                }
                            @endphp
                            <div class="heatmap-cell {{ $cellClass }}"
                                title="{{ $isFuture ? 'Belum dilewati' : 'Intensitas: ' . $cell['level'] }}"
                                style="display:flex; align-items:center; justify-content:center; font-size:11px; font-weight:600; color: {{ $textColor }}; {{ $cellStyle }}">
                                {{ $cell['day'] }}
                            </div>
                        @endif
                    @endforeach
                </div>
                <div class="heatmap-legend">
                    <span>Tidak ada</span>
                    @foreach ([0, 1, 2, 3] as $l)
                        <div class="heatmap-cell level-{{ $l }}"
                            style="width:14px;height:14px;border-radius:3px;"></div>
                    @endforeach
                    <span>Aktif</span>
                </div>
            </div>
        </div>

        {{-- Statistik Gabungan --}}
        <div class="card bento-c2 bento-r2">
            <div class="card-head" style="align-items:flex-start;">
                <div>
                    <span class="card-chip">Jul 2026</span>
                    <div class="card-title" style="margin-top:6px;">📈 Statistik Emisi Karbon</div>
                    <p style="font-size:12.5px;color:var(--text-light);margin-top:2px;">Pilih dan filter statistik</p>
                </div>
                <div style="display:flex; flex-direction:column; align-items:flex-end; gap:8px;">
                    <select id="statTypeSelector" class="form-select-styled">
                        <option value="bulanan">Emisi Bulanan</option>
                        <option value="harian">Emisi Harian</option>
                        <option value="perbandingan">Perbandingan per kategori</option>
                        <option value="kategori">Emisi per Kategori</option>
                    </select>
                </div>
            </div>

            <!-- Filter Buttons Dynamic -->
            <div id="statFilters" style="display:flex; gap:8px; margin-bottom:12px; flex-wrap:wrap; min-height:36px;">
            </div>

            <div id="view-bulanan" class="stat-view" style="display:block;">
                <div class="chart-box"><canvas id="trendChart"></canvas></div>
            </div>

            <div id="view-harian" class="stat-view" style="display:none;">
                <div class="chart-box"><canvas id="dailyChart"></canvas></div>
            </div>

            <div id="view-perbandingan" class="stat-view" style="display:none; position:relative;">
                <div id="tahunan-nav" style="display:none; position:absolute; top:-38px; right:0; gap:6px;">
                    <button id="btnPrevSlide" class="btn btn-outline btn-sm" disabled>&larr; Jan-Jun</button>
                    <button id="btnNextSlide" class="btn btn-outline btn-sm">Jul-Des &rarr;</button>
                </div>
                <div class="chart-box"><canvas id="compareChart"></canvas></div>
            </div>

            <div id="view-kategori" class="stat-view" style="display:none;">
                <div style="display:flex;align-items:center;gap:24px;flex-wrap:wrap;">
                    <div class="chart-box" style="max-width:150px;min-height:150px;"><canvas id="categoryDonut"></canvas>
                    </div>
                    <div id="categoryBreakdownContainer" style="flex:1;min-width:160px;display:flex;flex-direction:column;gap:10px;">
                        @foreach ($categoryBreakdown as $c)
                            <div style="display:flex;align-items:center;gap:9px;">
                                <span
                                    style="width:10px;height:10px;border-radius:4px;background:{{ $c['color'] }};flex-shrink:0;"></span>
                                <span style="font-size:13px;font-weight:600;">{{ $c['label'] }}</span>
                                <span
                                    style="margin-left:auto;font-size:12.5px;color:var(--text-light);">{{ $c['percent'] }}%</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        {{-- Leaderboard --}}
        <div class="card bento-c2 bento-r2" style="display:flex; flex-direction:column;">
            <div class="card-head">
                <div class="card-title">🏆 Leaderboard</div>
                <a href="{{ route('user.leaderboard') }}" class="card-arrow" style="text-decoration:none;">›</a>
            </div>
            <div style="display:flex; flex-direction:column; flex:1; justify-content:space-evenly;">
                @foreach ($leaderboard as $u)
                    <div class="lb-row {{ $u['me'] ?? false ? 'me' : ($u['rank'] <= 3 ? 'top' : '') }}">
                        <div class="lb-rank {{ $u['medal'] ?? '' }}">{{ $u['rank'] }}</div>
                        @if ($u['avatar'])
                            <img src="{{ Storage::url($u['avatar']) }}" alt="Avatar" class="lb-avatar"
                                style="width: 32px; height: 32px; border-radius: 50%; object-fit: cover;">
                        @else
                            <div class="lb-avatar">{{ $u['emoji'] }}</div>
                        @endif
                        <div class="lb-name">{{ $u['name'] }}</div>
                        <div class="lb-pts">{{ number_format($u['pts'], 0, ',', '.') }} <span
                                style="font-size:10.5px; font-weight:600; opacity:0.7;">Poin</span></div>
                    </div>
                @endforeach
            </div>
            <a href="{{ route('user.leaderboard') }}" class="btn btn-outline btn-sm"
                style="margin-top:16px; width:100%; justify-content:center;">Lihat Leaderboard Lengkap</a>
        </div>

    </div>

    <style>
        .bento-3 {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 18px;
            margin-top: 18px;
        }

        @media(max-width: 1100px) {
            .bento-3 {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media(max-width: 640px) {
            .bento-3 {
                grid-template-columns: 1fr;
            }
        }
    </style>

    <div class="bento-3">

        {{-- ===== MISI MINGGUAN ===== --}}
        <div class="card" style="display:flex; flex-direction:column;">
            <a href="{{ route('user.recommendations') }}" class="card-head" style="text-decoration:none;">
                <div>
                    <div class="card-title">🎯 Misi Mingguan</div>
                    <div style="display:flex; align-items:center; gap:8px; margin-top:6px;">
                        <span class="card-chip"
                            style="font-size:11px; padding:2px 8px;">{{ collect($weeklyMissions)->whereIn('status', ['done', 'claimed'])->count() }}/{{ count($weeklyMissions) }}
                            Selesai</span>
                        <span
                            style="font-size:11px; color:#E74C3C; font-weight:600; background:rgba(231,76,60,0.1); padding:2px 8px; border-radius:12px;">⏳
                            <span class="countdown-timer"
                                data-end="{{ \Carbon\Carbon::now()->endOfWeek()->toIso8601String() }}"></span></span>
                    </div>
                </div>
                <div style="display:flex; align-items:center; gap:8px;">
                    <span class="card-arrow" style="margin:0;">›</span>
                </div>
            </a>

            @foreach ($weeklyMissions as $m)
                @php $pct = min(100, round($m['progress'] / $m['target'] * 100)); @endphp
                <div class="mission-item {{ $m['status'] === 'done' ? 'mission-done' : '' }}"
                    style="{{ $m['status'] === 'claimed' ? 'opacity: 0.6; background-color: #F5F5F5;' : '' }}">
                    <div class="mission-header">
                        <div class="mission-body">
                            <div class="mission-title">{{ $m['title'] }}</div>
                            <div class="mission-meta">
                                <span class="mission-tag"
                                    style="background:{{ $m['color'] }}18;color:{{ $m['color'] }};">{{ ucwords(str_replace('_', ' ', $m['category'])) }}</span>
                                @if ($m['status'] === 'done')
                                    <form action="{{ route('user.weekly-mission.claim', $m['id']) }}" method="POST"
                                        style="margin:0;display:inline;" onsubmit="return confirm('Klaim reward untuk misi mingguan ini?');">
                                        @csrf
                                        <button type="submit" class="btn btn-sm"
                                            style="background:#2ECC71;color:white;border-radius:20px;padding:2px 10px;font-size:11px;font-weight:600;border:none;cursor:pointer;">Klaim
                                            Reward</button>
                                    </form>
                                @elseif($m['status'] === 'claimed')
                                    <span
                                        style="background:#2ECC7118;color:#2ECC71;padding:4px 10px;border-radius:12px;font-size:11px;font-weight:600;">✓
                                        Selesai</span>
                                @endif
                            </div>
                        </div>
                        <div class="mission-reward"
                            style="display:flex; flex-direction:column; gap:6px; align-items:flex-end; justify-content:center;">
                            <span
                                style="background:rgba(91,143,255,0.15); color:var(--primary); padding:4px 8px; border-radius:8px; font-size:12px; font-weight:700; display:inline-block;">✨
                                +{{ $m['reward_points'] }} Poin</span>
                            <span
                                style="background:rgba(245,166,35,0.15); color:#d88c14; padding:4px 8px; border-radius:8px; font-size:12px; font-weight:700; display:inline-block;">🪙
                                +{{ $m['reward_coins'] }} Koin</span>
                        </div>
                    </div>
                    <div style="margin-top:10px;">
                        <div class="progress-label">
                            <span>{{ $m['progress'] }}/{{ $m['target'] }} {{ $m['type'] }}</span>
                            <b>{{ $pct }}%</b>
                        </div>
                        <div class="progress-track">
                            <div class="progress-fill"
                                style="width:{{ $pct }}%;background:{{ $m['color'] }};"></div>
                        </div>
                    </div>
                </div>
            @endforeach

            <div
                style="margin-top:auto;padding:10px 14px;background:var(--bg);border-radius:10px;font-size:12.5px;color:var(--text-light);line-height:1.5;">
                💡 <b style="color:var(--text-dark);">Cara kerja misi:</b> Catat emisi karbon kamu (manual atau via
                sinkronisasi) — sistem otomatis mendeteksi & memperbarui progress misi yang sesuai.
            </div>
        </div>



        {{-- ===== MISI HARIAN ===== --}}
        <div class="card" style="display:flex; flex-direction:column;">
            <a href="{{ route('user.recommendations') }}" class="card-head" style="text-decoration:none;">
                <div>
                    <div class="card-title">🎯 Misi Harian</div>
                    <div style="display:flex; align-items:center; gap:8px; margin-top:6px;">
                        <span class="card-chip"
                            style="font-size:11px; padding:2px 8px;">{{ collect($dailyMissions)->whereIn('status', ['done', 'claimed'])->count() }}/{{ count($dailyMissions) }}
                            Selesai</span>
                        <span
                            style="font-size:11px; color:#E74C3C; font-weight:600; background:rgba(231,76,60,0.1); padding:2px 8px; border-radius:12px;">⏳
                            <span class="countdown-timer"
                                data-end="{{ \Carbon\Carbon::now()->endOfDay()->toIso8601String() }}"></span></span>
                    </div>
                </div>
                <div style="display:flex; align-items:center; gap:8px;">
                    <span class="card-arrow" style="margin:0;">›</span>
                </div>
            </a>
            @foreach ($dailyMissions as $r)
                @php $pct = round(($r['done']/($r['target'] ?? 1))*100); @endphp
                <div class="mission-item {{ ($r['status'] ?? '') === 'done' ? 'mission-done' : '' }}"
                    style="{{ ($r['status'] ?? '') === 'claimed' ? 'opacity: 0.6; background-color: #F5F5F5;' : '' }}">
                    <div class="mission-header">
                        <div class="mission-icon" style="background:{{ $r['bg'] }};">{{ $r['icon'] }}</div>
                        <div class="mission-body">
                            <div class="mission-title">{{ $r['title'] }}</div>
                            <div class="mission-meta">
                                <span class="mission-tag"
                                    style="background:#eee;color:var(--text-main);">{{ ucwords(str_replace('_', ' ', $r['cat'])) }}</span>
                                <span style="font-size:11px;color:var(--text-main);">🌿 {{ $r['impact'] }}</span>
                                @if (($r['status'] ?? '') === 'done')
                                    <form action="{{ route('user.weekly-mission.claim', $r['id']) }}" method="POST"
                                        style="margin:0;display:inline;" onsubmit="return confirm('Klaim reward untuk misi harian ini?');">
                                        @csrf
                                        <button type="submit" class="btn btn-sm"
                                            style="background:#2ECC71;color:white;border-radius:20px;padding:2px 10px;font-size:11px;font-weight:600;border:none;cursor:pointer;">Klaim
                                            Reward</button>
                                    </form>
                                @elseif(($r['status'] ?? '') === 'claimed')
                                    <span
                                        style="background:#2ECC7118;color:#2ECC71;padding:4px 10px;border-radius:12px;font-size:11px;font-weight:600;">✓
                                        Diklaim</span>
                                @endif
                            </div>
                        </div>
                        <div class="mission-reward"
                            style="display:flex; flex-direction:column; gap:6px; align-items:flex-end; justify-content:center;">
                            <span
                                style="background:rgba(91,143,255,0.15); color:var(--primary); padding:4px 8px; border-radius:8px; font-size:12px; font-weight:700; display:inline-block;">✨
                                +{{ $r['reward_points'] }} Poin</span>
                            <span
                                style="background:rgba(245,166,35,0.15); color:#d88c14; padding:4px 8px; border-radius:8px; font-size:12px; font-weight:700; display:inline-block;">🪙
                                +{{ $r['reward_coins'] }} Koin</span>
                        </div>
                    </div>
                    <div style="margin-top:10px;">
                        <div class="progress-label">
                            <span>{{ $r['done'] }}/{{ $r['target'] ?? 1 }}</span>
                            <b>{{ $pct }}%</b>
                        </div>
                        <div class="progress-track">
                            <div class="progress-fill" style="width:{{ $pct }}%;background:var(--primary);">
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach

            <div
                style="margin-top:auto;padding:10px 14px;background:var(--bg);border-radius:10px;font-size:12.5px;color:var(--text-light);line-height:1.5;">
                💡 <b style="color:var(--text-dark);">Cara kerja misi:</b> Catat emisi karbon kamu (manual atau via
                sinkronisasi) — sistem otomatis mendeteksi &amp; memperbarui progress misi yang sesuai.
            </div>
        </div>

        {{-- ===== PRODUK PENDUKUNG ===== --}}
        <div class="card" style="display:flex; flex-direction:column;">
            <a href="{{ route('user.recommendations') }}" class="card-head" style="text-decoration:none;">
                <div>
                    <div class="card-title">🛒 Produk Pendukung</div>
                    <p style="font-size:12.5px;color:var(--text-light);margin-top:2px;">Tukarkan koinmu dengan produk ini
                    </p>
                </div>
                <div class="card-arrow">›</div>
            </a>
            @foreach ($productRecs as $r)
                <div class="rec-item">
                    <div class="rec-icon"
                        style="background:{{ $r['bg'] }}; font-size: 24px; display:flex; align-items:center; justify-content:center;">
                        {{ $r['icon'] }}</div>
                    <div class="rec-body">
                        <div class="rec-title">{{ $r['title'] }}</div>
                        <div class="rec-sub">{{ $r['sub'] }}</div>
                        <div class="rec-impact">🌿 {{ $r['impact'] }}</div>
                    </div>
                    <div class="rec-cta">
                        <div class="price" style="color: #F5A623;">🪙 {{ $r['coin_price'] }} Koin</div>
                        @if (isset($r['price']))
                            <div class="subprice">{{ $r['price'] }}</div>
                        @endif
                    </div>
                </div>
            @endforeach
            <a href="{{ route('user.recommendations') }}"
                style="margin-top:auto; font-size:13px; font-weight:600; color:var(--primary); text-align:center; padding-top:16px; text-decoration:none;">Lihat
                produk lainnya &rarr;</a>
        </div>

    </div>
@endsection

@push('scripts')
    <script src="{{ asset('plugins/flatpickr/flatpickr.min.js') }}"></script>
    <script src="{{ asset('plugins/flatpickr/monthSelect.js') }}"></script>
    <script src="{{ asset('plugins/flatpickr/weekSelect.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            // Global chart instances
            window.chartInstances = {};

            // ---- Carbon Status Ring ----
            tryChart('ringChart', {
                type: 'doughnut',
                data: {
                    datasets: [{
                        data: [{{ $totalCo2Month }},
                            {{ max($co2TargetMonth - $totalCo2Month, 0.1) }}
                        ],
                        backgroundColor: ['#5B8FFF', '#EEF1F5'],
                        borderWidth: 0
                    }]
                },
                options: {
                    cutout: '80%',
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            enabled: false
                        }
                    },
                    responsive: true,
                    maintainAspectRatio: false
                }
            });

            // ---- Trend Bulanan ----
            window.chartInstances['trendChart'] = tryChart('trendChart', {
                type: 'line',
                data: {
                    labels: @json(array_column($monthlyTrend, 'label')),
                    datasets: [{
                        label: 'Emisi (kg CO₂e)',
                        data: @json(array_column($monthlyTrend, 'value')),
                        borderColor: '#5B8FFF',
                        backgroundColor: 'rgba(91,143,255,0.10)',
                        fill: true,
                        tension: .4,
                        pointBackgroundColor: '#5B8FFF',
                        pointRadius: 5,
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2,
                        borderWidth: 3
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            grid: {
                                color: '#F0F3F8'
                            },
                            ticks: {
                                callback: v => v + ' kg'
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            }
                        }
                    }
                }
            });

            // ---- Emisi Harian (Sparkline) ----
            window.chartInstances['dailyChart'] = tryChart('dailyChart', {
                type: 'line',
                data: {
                    labels: @json(array_column($dailyEmissions, 'label')),
                    datasets: [{
                        label: 'kg CO₂e',
                        data: @json(array_column($dailyEmissions, 'value')),
                        borderColor: '#F5A623',
                        backgroundColor: 'rgba(245,166,35,0.10)',
                        fill: true,
                        tension: .4,
                        pointBackgroundColor: '#F5A623',
                        pointRadius: 4,
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2,
                        borderWidth: 2.5
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            grid: {
                                color: '#F0F3F8'
                            },
                            ticks: {
                                callback: v => v + ' kg'
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            }
                        }
                    }
                }
            });

            // ---- Category Donut ----
            window.chartInstances['categoryDonut'] = tryChart('categoryDonut', {
                type: 'doughnut',
                data: {
                    labels: @json(array_column($categoryBreakdown, 'label')),
                    datasets: [{
                        data: @json(array_column($categoryBreakdown, 'value')),
                        backgroundColor: @json(array_column($categoryBreakdown, 'color')),
                        borderWidth: 0
                    }]
                },
                options: {
                    cutout: '68%',
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    responsive: true,
                    maintainAspectRatio: false
                }
            });

            // ---- Compare Chart ----
            window.chartInstances['compareChart'] = tryChart('compareChart', {
                type: 'bar',
                data: {
                    labels: @json(array_column($weeklyCompare, 'label')),
                    datasets: [{
                            label: 'Minggu Lalu',
                            data: @json(array_column($weeklyCompare, 'lastWeek')),
                            backgroundColor: '#E2E8F0',
                            borderRadius: 6
                        },
                        {
                            label: 'Minggu Ini',
                            data: @json(array_column($weeklyCompare, 'thisWeek')),
                            backgroundColor: '#5B8FFF',
                            borderRadius: 6
                        },
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        }
                    },
                    scales: {
                        y: {
                            grid: {
                                color: '#F0F3F8'
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            }
                        }
                    }
                }
            });

            // ---- JS Switcher untuk Statistik Gabungan ----
            const statTypeSelector = document.getElementById('statTypeSelector');
            const statViews = document.querySelectorAll('.stat-view');
            const statFilters = document.getElementById('statFilters');

            // ---- Custom Flatpickr Logic ----
            function applyFlatpickr() {
                if (typeof flatpickr === 'undefined') return;
                document.querySelectorAll('input[type="date"].form-input-styled').forEach(el => {
                    if (!el._flatpickr) flatpickr(el, {
                        dateFormat: "Y-m-d"
                    });
                });
                document.querySelectorAll('input[type="month"].form-input-styled').forEach(el => {
                    if (!el._flatpickr) {
                        try {
                            flatpickr(el, {
                                dateFormat: "Y-m",
                                plugins: [new monthSelectPlugin({
                                    shorthand: true,
                                    dateFormat: "Y-m",
                                    altFormat: "M Y"
                                })]
                            });
                        } catch (e) {
                            console.error("MonthSelect plugin error:", e);
                            flatpickr(el, {
                                dateFormat: "Y-m"
                            });
                        }
                    }
                });
                document.querySelectorAll('input[type="week"].form-input-styled').forEach(el => {
                    if (!el._flatpickr) {
                        try {
                            flatpickr(el, {
                                dateFormat: "Y-\\WW",
                                altInput: true,
                                altFormat: "\\W\\e\\e\\k W, Y",
                                plugins: [new weekSelect({})]
                            });
                        } catch (e) {
                            console.error("WeekSelect plugin error:", e);
                            flatpickr(el, {
                                weekNumbers: true
                            });
                        }
                    }
                });
            }

            // ---- Custom Select Dropdown Logic ----
            function applyCustomSelect() {
                document.querySelectorAll('.form-select-styled').forEach(select => {
                    if (select.dataset.customized === 'true') return;
                    select.dataset.customized = 'true';
                    select.style.display = 'none';

                    const wrapper = document.createElement('div');
                    wrapper.className = 'custom-select-wrapper';
                    if (select.style.width) wrapper.style.width = select.style.width;

                    const trigger = document.createElement('div');
                    trigger.className = 'custom-select-trigger';
                    trigger.textContent = select.options[select.selectedIndex]?.text || '';

                    const optionsContainer = document.createElement('div');
                    optionsContainer.className = 'custom-options';

                    Array.from(select.options).forEach((opt, idx) => {
                        const optionEl = document.createElement('div');
                        optionEl.className = 'custom-option';
                        if (idx === select.selectedIndex) optionEl.classList.add('selected');
                        optionEl.textContent = opt.text;

                        optionEl.addEventListener('click', function(e) {
                            e.stopPropagation();
                            trigger.textContent = this.textContent;
                            select.selectedIndex = idx;

                            optionsContainer.querySelectorAll('.custom-option').forEach(
                                el => el.classList.remove('selected'));
                            this.classList.add('selected');

                            optionsContainer.classList.remove('open');
                            trigger.classList.remove('open');

                            select.dispatchEvent(new Event('change', {
                                bubbles: true
                            }));
                        });
                        optionsContainer.appendChild(optionEl);
                    });

                    wrapper.appendChild(trigger);
                    wrapper.appendChild(optionsContainer);
                    select.parentNode.insertBefore(wrapper, select.nextSibling);

                    trigger.addEventListener('click', function(e) {
                        e.stopPropagation();
                        document.querySelectorAll('.custom-select-trigger').forEach(t => {
                            if (t !== trigger) {
                                t.classList.remove('open');
                                t.nextElementSibling.classList.remove('open');
                            }
                        });
                        this.classList.toggle('open');
                        optionsContainer.classList.toggle('open');
                    });
                });
            }

            document.addEventListener('click', function() {
                document.querySelectorAll('.custom-options').forEach(el => el.classList.remove('open'));
                document.querySelectorAll('.custom-select-trigger').forEach(el => el.classList.remove(
                    'open'));
            });

            const filtersHtml = {
                bulanan: `
    <div style="display:flex;align-items:center;gap:6px;background:var(--bg);padding:6px 12px;border-radius:8px;border:1px solid var(--border);">
      <span style="font-size:12px;font-weight:600;color:var(--text-light);">Rentang Bulan:</span>
      <input type="month" id="filterBulanAwal" value="{{ now()->subMonths(5)->format('Y-m') }}" class="form-input-styled" style="width:125px;">
      <span style="font-size:12px;color:var(--text-light);">-</span>
      <input type="month" id="filterBulanAkhir" value="{{ now()->format('Y-m') }}" class="form-input-styled" style="width:125px;">
    </div>
  `,
                harian: `
    <div style="display:flex;align-items:center;gap:6px;background:var(--bg);padding:6px 12px;border-radius:8px;border:1px solid var(--border);">
      <span style="font-size:12px;font-weight:600;color:var(--text-light);">Rentang Hari:</span>
      <input type="date" id="filterHariAwal" value="{{ now()->subDays(6)->format('Y-m-d') }}" class="form-input-styled" style="width:125px;">
      <span style="font-size:12px;color:var(--text-light);">-</span>
      <input type="date" id="filterHariAkhir" value="{{ now()->format('Y-m-d') }}" class="form-input-styled" style="width:125px;">
    </div>
  `,
                perbandingan: `
    <div style="display:flex;align-items:center;gap:6px;background:var(--bg);padding:6px 12px;border-radius:8px;border:1px solid var(--border);">
      <select id="compareType" class="form-select-styled" style="width:110px;">
        <option value="mingguan">Mingguan</option>
        <option value="bulanan">Bulanan</option>
        <option value="tahunan">Tahunan</option>
      </select>
      <div id="compareInputs" style="display:flex;align-items:center;gap:6px;"></div>
    </div>
  `,
                kategori: `
    <div style="display:flex;align-items:center;gap:6px;background:var(--bg);padding:6px 12px;border-radius:8px;border:1px solid var(--border);">
      <select id="catType" class="form-select-styled">
        <option value="all">Semua Kategori</option>
        <option value="transportasi">Transportasi</option>
        <option value="makanan">Makanan</option>
        <option value="energi">Energi &amp; Listrik</option>
        <option value="bahan_bakar">Bahan Bakar</option>
        <option value="limbah">Limbah</option>
        <option value="air">Air</option>
        <option value="energi_terbarukan">Energi Terbarukan</option>
      </select>
      <select id="catPeriodType" class="form-select-styled" style="width:110px;">
        <option value="month">Bulanan</option>
        <option value="week">Mingguan</option>
      </select>
      <input type="month" id="catPeriodInput" value="{{ \Carbon\Carbon::now()->format('Y-m') }}" class="form-input-styled" style="width:130px;">
    </div>
  `
            };

            let currentTahunanSlide = 1;

            function updateCompareInputs(type) {
                const container = document.getElementById('compareInputs');
                if (!container) return;
                if (type === 'mingguan') {
                    container.innerHTML =
                        '<input type="week" id="comp1" value="{{ now()->subWeek()->format('Y-\WW') }}" class="form-input-styled" style="width:125px;"> <span style="font-size:12px;">vs</span> <input type="week" id="comp2" value="{{ now()->format('Y-\WW') }}" class="form-input-styled" style="width:125px;">';
                } else if (type === 'bulanan') {
                    container.innerHTML =
                        '<input type="month" id="comp1" value="{{ now()->subMonth()->format('Y-m') }}" class="form-input-styled" style="width:115px;"> <span style="font-size:12px;">vs</span> <input type="month" id="comp2" value="{{ now()->format('Y-m') }}" class="form-input-styled" style="width:115px;">';
                } else if (type === 'tahunan') {
                    container.innerHTML =
                        '<input type="number" min="2020" max="{{ now()->format('Y') }}" id="comp1" value="{{ now()->subYear()->format('Y') }}" class="form-input-styled" style="width:85px;"> <span style="font-size:12px;">vs</span> <input type="number" min="2020" max="{{ now()->format('Y') }}" id="comp2" value="{{ now()->format('Y') }}" class="form-input-styled" style="width:85px;">';
                }
                applyFlatpickr();
                triggerCompareFetch();
            }

            // ---- AJAX Data Fetcher ----
            function fetchChartData(type, params) {
                const qs = new URLSearchParams({ type, ...params }).toString();
                fetch('{{ route("user.dashboard.chart-data") }}?' + qs)
                    .then(res => res.json())
                    .then(data => {
                        let chart;
                        if (type === 'trend') chart = window.chartInstances['trendChart'];
                        if (type === 'daily') chart = window.chartInstances['dailyChart'];
                        if (type === 'category') chart = window.chartInstances['categoryDonut'];
                        if (type === 'compare') {
                            chart = window.chartInstances['compareChart'];
                            if (params.compareType === 'tahunan') {
                                window._compareTahunanData = data;
                                updateTahunanChart(currentTahunanSlide);
                                return;
                            }
                        }
                        
                        if (chart && data.labels && data.datasets) {
                            chart.data.labels = data.labels;
                            if (type === 'compare') {
                                chart.data.datasets[0].data = data.datasets[0].data;
                                chart.data.datasets[0].label = data.datasets[0].label;
                                chart.data.datasets[1].data = data.datasets[1].data;
                                chart.data.datasets[1].label = data.datasets[1].label;
                            } else if (type === 'category') {
                                chart.data.datasets[0].data = data.datasets[0].data;
                                chart.data.datasets[0].backgroundColor = data.datasets[0].backgroundColor;
                                
                                if (data.breakdown) {
                                    const container = document.getElementById('categoryBreakdownContainer');
                                    if (container) {
                                        container.innerHTML = data.breakdown.map(c => `
                                            <div style="display:flex;align-items:center;gap:9px;">
                                                <span style="width:10px;height:10px;border-radius:4px;background:${c.color};flex-shrink:0;"></span>
                                                <span style="font-size:13px;font-weight:600;">${c.label}</span>
                                                <span style="margin-left:auto;font-size:12.5px;color:var(--text-light);">${c.percent}%</span>
                                            </div>
                                        `).join('');
                                    }
                                }
                            } else {
                                chart.data.datasets[0].data = data.datasets[0].data;
                            }
                            chart.update();
                        }
                    })
                    .catch(err => console.error(err));
            }

            function updateTahunanChart(slide = 1) {
                let chart = window.chartInstances['compareChart'];
                let data = window._compareTahunanData;
                if (!chart || !data || !data.datasets) return;

                currentTahunanSlide = slide;
                if (slide === 1) {
                    chart.data.labels = data.labels_1;
                    chart.data.datasets[0].label = 'Tahun 1 (Jan-Jun)';
                    chart.data.datasets[0].data = data.datasets.year1.slice(0, 6);
                    chart.data.datasets[1].label = 'Tahun 2 (Jan-Jun)';
                    chart.data.datasets[1].data = data.datasets.year2.slice(0, 6);
                    document.getElementById('btnPrevSlide').disabled = true;
                    document.getElementById('btnNextSlide').disabled = false;
                } else {
                    chart.data.labels = data.labels_2;
                    chart.data.datasets[0].label = 'Tahun 1 (Jul-Des)';
                    chart.data.datasets[0].data = data.datasets.year1.slice(6, 12);
                    chart.data.datasets[1].label = 'Tahun 2 (Jul-Des)';
                    chart.data.datasets[1].data = data.datasets.year2.slice(6, 12);
                    document.getElementById('btnPrevSlide').disabled = false;
                    document.getElementById('btnNextSlide').disabled = true;
                }
                chart.update();
            }

            function triggerCompareFetch() {
                let cType = document.getElementById('compareType')?.value || 'mingguan';
                let c1 = document.getElementById('comp1')?.value;
                let c2 = document.getElementById('comp2')?.value;
                if (c1 && c2) {
                    document.getElementById('tahunan-nav').style.display = (cType === 'tahunan') ? 'flex' : 'none';
                    fetchChartData('compare', { compareType: cType, comp1: c1, comp2: c2 });
                }
            }

            if (statFilters && statTypeSelector) {
                statFilters.innerHTML = filtersHtml['bulanan'];
                applyCustomSelect();
                applyFlatpickr();
                statTypeSelector.addEventListener('change', function() {
                    const selected = this.value;
                    statViews.forEach(view => {
                        view.style.display = 'none';
                    });
                    const targetView = document.getElementById('view-' + selected);
                    if (targetView) {
                        targetView.style.display = 'block';
                        if (selected === 'bulanan' && window.chartInstances['trendChart']) window
                            .chartInstances['trendChart'].resize();
                        if (selected === 'harian' && window.chartInstances['dailyChart']) window
                            .chartInstances['dailyChart'].resize();
                        if (selected === 'kategori' && window.chartInstances['categoryDonut']) window
                            .chartInstances['categoryDonut'].resize();
                        if (selected === 'perbandingan' && window.chartInstances['compareChart']) window
                            .chartInstances['compareChart'].resize();
                    }
                    statFilters.innerHTML = filtersHtml[selected] || '';

                    if (selected === 'perbandingan') {
                        updateCompareInputs('mingguan');
                    }
                    applyCustomSelect();
                    applyFlatpickr();
                });
            }

            // Global delegated event listeners for validation & dynamic UI
            document.addEventListener('change', e => {
                // Bulanan Validation & Fetch
                if (e.target.id === 'filterBulanAwal' || e.target.id === 'filterBulanAkhir') {
                    let m1 = document.getElementById('filterBulanAwal').value;
                    let m2 = document.getElementById('filterBulanAkhir').value;
                    if (m1 && m2) {
                        let d1 = new Date(m1);
                        let d2 = new Date(m2);
                        let diff = (d2.getFullYear() - d1.getFullYear()) * 12 + (d2.getMonth() - d1.getMonth());
                        if (diff < 3 || diff > 7) {
                            if (typeof showToast === 'function') showToast('Rentang bulan harus minimal 3 bulan dan maksimal 7 bulan!');
                        } else {
                            fetchChartData('trend', { start: m1, end: m2 });
                        }
                    }
                }

                // Harian Validation & Fetch
                if (e.target.id === 'filterHariAwal' || e.target.id === 'filterHariAkhir') {
                    let m1 = document.getElementById('filterHariAwal').value;
                    let m2 = document.getElementById('filterHariAkhir').value;
                    if (m1 && m2) {
                        let d1 = new Date(m1);
                        let d2 = new Date(m2);
                        let diff = (d2 - d1) / (1000 * 60 * 60 * 24);
                        if (diff < 3 || diff > 7) {
                            if (typeof showToast === 'function') showToast('Rentang hari harus minimal 3 hari dan maksimal 7 hari!');
                        } else {
                            fetchChartData('daily', { start: m1, end: m2 });
                        }
                    }
                }

                // Kategori
                if (e.target.id === 'catType' || e.target.id === 'catPeriodType' || e.target.id === 'catPeriodInput') {
                    if (e.target.id === 'catPeriodType') {
                        const input = document.getElementById('catPeriodInput');
                        if (input._flatpickr) {
                            input._flatpickr.destroy();
                        }
                        input.type = e.target.value === 'month' ? 'month' : 'week';
                        input.value = e.target.value === 'month' ? '{{ \Carbon\Carbon::now()->format('Y-m') }}' : '{{ \Carbon\Carbon::now()->format('Y-\WW') }}';
                        applyFlatpickr();
                    }
                    
                    let cType = document.getElementById('catType').value;
                    let pType = document.getElementById('catPeriodType').value;
                    let pInput = document.getElementById('catPeriodInput').value;
                    
                    if (pInput) {
                        fetchChartData('category', { catType: cType, periodType: pType, periodInput: pInput });
                    }
                }

                // Compare type switch & Inputs
                if (e.target.id === 'compareType') {
                    updateCompareInputs(e.target.value);
                } else if (e.target.id === 'comp1' || e.target.id === 'comp2') {
                    triggerCompareFetch();
                }
            });

            document.addEventListener('click', e => {
                if (e.target.id === 'btnPrevSlide') {
                    updateTahunanChart(1);
                } else if (e.target.id === 'btnNextSlide') {
                    updateTahunanChart(2);
                }
            });

            // ---- Countdown Logic ----
            function updateCountdowns() {
                document.querySelectorAll('.countdown-timer').forEach(el => {
                    const endDateStr = el.getAttribute('data-end');
                    if (!endDateStr) return;

                    const endDate = new Date(endDateStr).getTime();
                    const now = new Date().getTime();
                    const distance = endDate - now;

                    if (distance < 0) {
                        el.innerHTML = "Berakhir";
                        return;
                    }

                    const days = Math.floor(distance / (1000 * 60 * 60 * 24));
                    const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                    const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));

                    if (days > 0) {
                        el.innerHTML = "Berakhir dalam " + days + " hari " + hours + " jam";
                    } else {
                        el.innerHTML = "Berakhir dalam " + hours + " jam " + minutes + " menit";
                    }
                });
            }
            updateCountdowns();
            setInterval(updateCountdowns, 60000); // update every minute

        }); // end DOMContentLoaded
    </script>
@endpush

@push('styles')
    <link rel="stylesheet" href="{{ asset('plugins/flatpickr/flatpickr.min.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/flatpickr/monthSelect.css') }}">
    <style>
        /* ---- Mission Cards ---- */
        .mission-item {
            padding: 14px;
            border: 1px solid var(--border);
            border-radius: 12px;
            margin-bottom: 10px;
            transition: background .2s;
        }

        .mission-item.mission-done {
            background: rgba(46, 204, 113, 0.05);
            border-color: rgba(46, 204, 113, 0.25);
        }

        .mission-header {
            display: flex;
            align-items: flex-start;
            gap: 12px;
        }

        .mission-icon {
            width: 38px;
            height: 38px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            flex-shrink: 0;
        }

        .mission-body {
            flex: 1;
            min-width: 0;
        }

        .mission-title {
            font-size: 13.5px;
            font-weight: 700;
            color: var(--text-dark);
            line-height: 1.3;
        }

        .mission-meta {
            display: flex;
            align-items: center;
            gap: 6px;
            margin-top: 4px;
            flex-wrap: wrap;
        }

        .mission-tag {
            font-size: 10.5px;
            font-weight: 600;
            padding: 2px 8px;
            border-radius: 20px;
            white-space: nowrap;
        }

        .mission-reward {
            text-align: center;
            font-size: 16px;
            font-weight: 800;
            color: var(--primary);
            flex-shrink: 0;
            line-height: 1.1;
        }

        /* ---- Heatmap ---- */
        .heatmap-grid {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 4px;
            margin: 14px 0 10px;
        }

        .heatmap-label {
            font-size: 10px;
            font-weight: 600;
            color: var(--text-light);
            text-align: center;
            padding-bottom: 2px;
        }

        .heatmap-cell {
            aspect-ratio: 1;
            border-radius: 4px;
            transition: transform .15s;
        }

        .heatmap-cell:hover {
            transform: scale(1.2);
        }

        .level-0 {
            background: #EEF1F5;
        }

        .level-1 {
            background: #BFD4FF;
        }

        .level-2 {
            background: #7FABFF;
        }

        .level-3 {
            background: #5B8FFF;
        }

        .heatmap-legend {
            display: flex;
            align-items: center;
            gap: 5px;
            font-size: 11px;
            color: var(--text-light);
            margin-top: 4px;
        }

        .form-select-styled,
        .form-input-styled {
            font-family: 'Inter', sans-serif;
            font-size: 12.5px;
            padding: 6px 10px;
            border-radius: 6px;
            border: 1px solid var(--border);
            outline: none;
            background-color: var(--bg);
            color: var(--text-dark);
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s;
        }

        .form-select-styled:hover,
        .form-input-styled:hover {
            border-color: var(--primary-light);
        }

        .form-select-styled:focus,
        .form-input-styled:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(91, 143, 255, 0.1);
        }

        /* Custom Calendar Input Styling */
        .form-input-styled.flatpickr-input,
        .form-input-styled[type="date"],
        .form-input-styled[type="month"],
        .form-input-styled[type="week"] {
            padding-left: 32px !important;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%2364748B'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: 8px center;
            background-size: 16px;
            position: relative;
            cursor: pointer;
        }

        .form-input-styled.flatpickr-input:hover,
        .form-input-styled[type="date"]:hover,
        .form-input-styled[type="month"]:hover,
        .form-input-styled[type="week"]:hover {
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%235B8FFF'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z'/%3E%3C/svg%3E");
        }

        .form-input-styled[type="date"]::-webkit-calendar-picker-indicator,
        .form-input-styled[type="month"]::-webkit-calendar-picker-indicator,
        .form-input-styled[type="week"]::-webkit-calendar-picker-indicator {
            opacity: 0;
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            width: 100%;
            height: 100%;
            cursor: pointer;
        }

        /* Custom Select Dropdown UI */
        .custom-select-wrapper {
            position: relative;
            display: inline-block;
            user-select: none;
        }

        .custom-select-trigger {
            font-family: 'Inter', sans-serif;
            font-size: 12.5px;
            padding: 6px 26px 6px 12px;
            border-radius: 6px;
            border: 1px solid var(--border);
            background-color: var(--bg);
            color: var(--text-dark);
            font-weight: 500;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: space-between;
            transition: all 0.2s;
            position: relative;
            min-height: 18px;
        }

        .custom-select-trigger:after {
            content: '';
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            border-width: 4px 4px 0 4px;
            border-style: solid;
            border-color: var(--text-light) transparent transparent transparent;
        }

        .custom-select-trigger.open:after {
            border-width: 0 4px 4px 4px;
            border-color: transparent transparent var(--text-light) transparent;
        }

        .custom-select-trigger:hover,
        .custom-select-trigger.open {
            border-color: var(--primary-light);
        }

        .custom-options {
            position: absolute;
            top: 100%;
            left: 0;
            right: auto;
            background: #fff;
            border: 1px solid var(--border);
            border-radius: 6px;
            margin-top: 4px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            z-index: 100;
            display: none;
            overflow: hidden;
            min-width: max-content;
        }

        .custom-options.open {
            display: block;
        }

        .custom-option {
            padding: 8px 12px;
            font-size: 12.5px;
            font-weight: 500;
            color: var(--text-dark);
            cursor: pointer;
            transition: background 0.15s;
        }

        .custom-option:hover,
        .custom-option.selected {
            background: var(--bg);
            color: var(--primary);
        }
    </style>
@endpush
