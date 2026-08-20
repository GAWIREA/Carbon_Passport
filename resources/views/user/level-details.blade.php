@extends('layouts.app')

@section('title', 'Detail Level & Progress XP')
@section('page-title', 'Detail Level')

@section('content')
<div class="page-head">
    <div>
        <a href="{{ route('user.profile') }}" style="font-size: 14px; color: var(--primary); margin-bottom: 8px; display: inline-block;">&larr; Kembali ke Profil</a>
        <h1>Perjalanan Level Anda</h1>
        <p>Kumpulkan XP untuk naik ke tingkat tertinggi dan dapatkan hadiah eksklusif!</p>
    </div>
</div>

<!-- Horizontal Scrollable Levels (Season Journey style) -->
<div class="card" style="margin-bottom: 24px; background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%); color: white; border: none; overflow: hidden; position: relative;">
    <div style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: radial-gradient(circle at top right, rgba(255,255,255,0.1), transparent); pointer-events: none;"></div>
    
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px; padding: 0 8px;">
        <h3 style="font-size: 18px; margin: 0; display: flex; align-items: center; gap: 8px;">
            <span style="font-size: 24px;">🏆</span> Peta Pencapaian Level
        </h3>
        <div style="font-size: 14px; font-weight: bold; background: rgba(0,0,0,0.3); padding: 6px 12px; border-radius: 20px;">
            XP Saat Ini: {{ number_format($user->xp, 0, ',', '.') }}
        </div>
    </div>

    <!-- Scroll Container -->
    <div style="display: flex; gap: 16px; overflow-x: auto; padding: 12px 8px 24px 8px; scroll-snap-type: x mandatory;">
        
        @foreach($levels as $lvl)
            @php
                $isAchieved = $user->level > $lvl->value;
                $isCurrent = $user->level === $lvl->value;
                $isLocked = $user->level < $lvl->value;
                
                $cardBg = $isCurrent ? 'rgba(255,255,255,0.2)' : ($isAchieved ? 'rgba(255,255,255,0.05)' : 'rgba(0,0,0,0.2)');
                $cardBorder = $isCurrent ? '2px solid #FFD700' : '2px solid transparent';
                $opacity = $isLocked ? '0.7' : '1';
                
                // Mock Rewards
                $coinReward = 500 + ($lvl->value * 100);
            @endphp
            
            <div style="min-width: 140px; max-width: 140px; background: {{ $cardBg }}; border: {{ $cardBorder }}; border-radius: 12px; padding: 16px; text-align: center; opacity: {{ $opacity }}; scroll-snap-align: center; position: relative; display: flex; flex-direction: column; align-items: center; justify-content: space-between;">
                
                @if($isAchieved)
                    <div style="position: absolute; top: -10px; right: -10px; background: #2ECC71; color: white; width: 24px; height: 24px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 14px; box-shadow: 0 2px 4px rgba(0,0,0,0.2);">✓</div>
                @endif
                
                @if($isLocked)
                    <div style="position: absolute; top: -10px; right: -10px; background: #666; color: white; width: 24px; height: 24px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 12px; box-shadow: 0 2px 4px rgba(0,0,0,0.2);">🔒</div>
                @endif
                
                @if($isCurrent)
                    <div style="position: absolute; top: -12px; left: 50%; transform: translateX(-50%); background: #FFD700; color: #333; font-size: 10px; font-weight: bold; padding: 2px 8px; border-radius: 10px; white-space: nowrap;">SAAT INI</div>
                @endif
                
                <!-- Icon Level -->
                <div style="font-size: 40px; filter: drop-shadow(0 4px 6px rgba(0,0,0,0.3)); margin-bottom: 8px;">
                    {{ $lvl->icon() }}
                </div>
                
                <!-- Nama Level -->
                <div style="font-size: 14px; font-weight: bold; margin-bottom: 4px; color: {{ $isLocked ? '#aaa' : '#fff' }};">{{ $lvl->label() }}</div>
                
                <!-- Target XP -->
                <div style="font-size: 11px; color: rgba(255,255,255,0.7); margin-bottom: 12px;">
                    {{ number_format($lvl->xpThreshold(), 0, ',', '.') }} XP
                </div>
                
                <!-- Garis Pembatas -->
                <div style="width: 100%; height: 1px; background: rgba(255,255,255,0.2); margin-bottom: 12px;"></div>
                
                <!-- Hadiah -->
                <div style="display: flex; flex-direction: column; gap: 8px; width: 100%;">
                    <div style="display: flex; flex-direction: column; align-items: center; background: rgba(0,0,0,0.2); padding: 8px; border-radius: 8px;">
                        <span style="font-size: 18px;">🪙</span>
                        <span style="font-size: 11px; font-weight: bold; margin-top: 4px;">{{ $coinReward }}</span>
                    </div>
                    
                    @if($lvl->value % 5 === 0)
                        <div style="display: flex; flex-direction: column; align-items: center; background: rgba(0,0,0,0.2); padding: 8px; border-radius: 8px;">
                            <span style="font-size: 18px;">🎁</span>
                            <span style="font-size: 10px; font-weight: bold; margin-top: 4px;">Avatar/Frame</span>
                        </div>
                    @endif
                </div>
            </div>
        @endforeach
    </div>
    
    <div style="text-align: center; font-size: 11px; color: rgba(255,255,255,0.6); padding-bottom: 8px;">
        *Geser horizontal untuk melihat semua level
    </div>
</div>

<div style="display: flex; flex-wrap: wrap; gap: 24px;">
    <!-- Bagian Kiri: Progress XP Saat Ini -->
    <div style="flex: 1; min-width: 300px;">
        <div class="card" style="height: 100%;">
            <h3 style="font-size: 16px; margin-bottom: 16px; display: flex; justify-content: space-between; align-items: center;">
                <span>Progres Menuju {{ $user->getLevelInfo()->xpForNextLevel() ? 'Level Berikutnya' : 'Level Maksimal' }}</span>
                <span style="color: var(--primary);">✨</span>
            </h3>
            
            <div style="background: var(--bg); padding: 20px; border-radius: 12px;">
                <div style="display:flex; justify-content:space-between; align-items:center; font-size:14px; font-weight:bold; color:var(--text-dark); margin-bottom: 12px;">
                    <span>{{ $user->getLevelInfo()->label() }}</span>
                    <span style="color:var(--primary-dark);">{{ number_format($user->xp, 0, ',', '.') }} / {{ $user->getLevelInfo()->xpForNextLevel() ? number_format($user->getLevelInfo()->xpForNextLevel(), 0, ',', '.') : 'MAX' }} XP</span>
                </div>
                <div style="width:100%; background:#e0e0e0; height:16px; border-radius:8px; overflow:hidden;">
                    <div style="width:{{ $user->getXpProgress() }}%; background: linear-gradient(90deg, #2ECC71, #27AE60); height:100%; border-radius:8px; transition: width 0.5s ease-in-out;"></div>
                </div>
                
                @if($user->getLevelInfo()->xpForNextLevel())
                    <p style="font-size: 13px; color: var(--text-light); text-align: center; margin-top: 16px; margin-bottom: 0;">
                        Kumpulkan <strong>{{ number_format($user->getXpRemaining(), 0, ',', '.') }} XP</strong> lagi untuk mencapai tingkat selanjutnya. Jangan lupa selesaikan misi harian!
                    </p>
                @else
                    <p style="font-size: 13px; color: var(--text-light); text-align: center; margin-top: 16px; margin-bottom: 0;">
                        Anda telah mencapai level maksimal! Terus pertahankan aksi positif Anda.
                    </p>
                @endif
            </div>
        </div>
    </div>
    
    <!-- Bagian Kanan: Riwayat XP -->
    <div style="flex: 1; min-width: 300px;">
        <div class="card" style="height: 100%;">
            <h3 style="font-size: 16px; margin-bottom: 16px; display: flex; align-items: center; gap: 8px;">
                📅 Riwayat Perolehan XP
            </h3>
            
            <div style="display: flex; flex-direction: column; gap: 12px; max-height: 400px; overflow-y: auto; padding-right: 8px;">
                @forelse($xpHistory as $hist)
                    <div style="display: flex; justify-content: space-between; align-items: center; padding: 12px; background: var(--bg); border-radius: 8px; border-left: 4px solid var(--primary);">
                        <div>
                            <div style="font-size: 14px; font-weight: bold; color: var(--text-dark);">{{ $hist['title'] }}</div>
                            <div style="font-size: 12px; color: var(--text-light); margin-top: 4px;">{{ $hist['date'] }}</div>
                        </div>
                        <div style="font-weight: bold; color: #2ECC71; font-size: 16px;">
                            {{ $hist['xp'] }}
                        </div>
                    </div>
                @empty
                    <div style="text-align: center; padding: 32px 0;">
                        <div style="font-size: 40px; filter: grayscale(100%) opacity(0.5); margin-bottom: 16px;">📝</div>
                        <h4 style="font-size: 16px; font-weight: 700; color: var(--text-dark); margin-bottom: 8px;">Belum ada Riwayat XP</h4>
                        <p style="font-size: 13px; color: var(--text-light);">Anda belum mendapatkan XP. Selesaikan misi harian atau catat aksi untuk mulai mengumpulkan XP!</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

<style>
/* Custom scrollbar untuk container horizontal & vertical */
div::-webkit-scrollbar {
    height: 8px;
    width: 6px;
}
div::-webkit-scrollbar-track {
    background: rgba(255, 255, 255, 0.1);
    border-radius: 4px;
}
.card div::-webkit-scrollbar-track {
    background: var(--bg);
}
div::-webkit-scrollbar-thumb {
    background: rgba(255, 255, 255, 0.3);
    border-radius: 4px;
}
.card div::-webkit-scrollbar-thumb {
    background: var(--border);
}
div::-webkit-scrollbar-thumb:hover {
    background: rgba(255, 255, 255, 0.5);
}
.card div::-webkit-scrollbar-thumb:hover {
    background: var(--text-light);
}
</style>
@endsection
