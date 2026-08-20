@extends('layouts.app')
@section('title', 'Koleksi Pencapaian (Achievements)')
@section('page-title', 'Pencapaian')

@php
  $active = 'achievements';
@endphp

@section('content')
<div class="page-head" style="margin-bottom: 24px;">
  <div>
    <h1>Piala & Pencapaian 🏆</h1>
    <p>Kumpulkan berbagai lencana dengan menyelesaikan misi dan menjaga konsistensi gaya hidup hijau-mu!</p>
  </div>
</div>

<div class="achievements-container" style="display: flex; flex-direction: column; gap: 32px;">
    @foreach($allAchievements as $category => $achievements)
    <div class="achievement-section">
        <h2 style="font-size: 18px; color: var(--text-dark); margin-bottom: 16px; display: flex; align-items: center; gap: 8px;">
            <span style="display: inline-block; width: 4px; height: 20px; background: var(--primary); border-radius: 4px;"></span>
            {{ $category }}
        </h2>
        
        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 16px;">
            @foreach($achievements as $ach)
                @php
                    $isUnlocked = isset($unlockedAchievements[$ach->id]);
                    $unlockedAt = $isUnlocked ? \Carbon\Carbon::parse($unlockedAchievements[$ach->id])->translatedFormat('d M Y') : null;
                    
                    $currentProgress = $userProgress[$ach->requirement_type] ?? 0;
                    $target = $ach->requirement_value;
                    
                    // Cap the progress so it doesn't exceed target visually
                    $displayProgress = min($currentProgress, $target);
                    $percent = min(100, ($target > 0) ? round(($displayProgress / $target) * 100) : 0);
                    
                    $badgeFilter = $isUnlocked ? 'none' : 'grayscale(100%) opacity(0.6)';
                    $badgeBg = $isUnlocked ? '#FFF9E6' : '#F5F5F5';
                    $borderColor = $isUnlocked ? '#FFD54F' : 'var(--border)';
                @endphp
                
                <div class="card" style="padding: 20px; display: flex; flex-direction: column; align-items: center; text-align: center; border: 1px solid {{ $borderColor }}; box-shadow: 0 2px 4px rgba(0,0,0,0.02); position: relative; overflow: hidden; background: {{ $isUnlocked ? '#FFFCF2' : '#FFF' }};">
                    
                    <!-- Icon -->
                    <div style="width: 72px; height: 72px; background: {{ $badgeBg }}; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 36px; filter: {{ $badgeFilter }}; margin-bottom: 12px; transition: transform 0.3s; cursor: default;" onmouseover="this.style.transform='scale(1.1)'" onmouseout="this.style.transform='scale(1)'">
                        {{ $ach->icon }}
                    </div>
                    
                    <h3 style="font-size: 15px; margin: 0 0 6px 0; color: {{ $isUnlocked ? 'var(--text-dark)' : 'var(--text-main)' }}; font-weight: 700;">
                        {{ $ach->name }}
                    </h3>
                    
                    <p style="font-size: 11.5px; color: var(--text-light); line-height: 1.4; margin: 0 0 16px 0; min-height: 32px;">
                        {{ $ach->description }}
                    </p>
                    
                    @if($isUnlocked)
                        <div style="margin-top: auto; width: 100%;">
                            <div style="font-size: 12px; font-weight: 600; color: #E67E22; background: #FEF9E7; padding: 4px 0; border-radius: 20px; border: 1px solid #FAD7A1;">
                                ✨ Diperoleh {{ $unlockedAt }}
                            </div>
                        </div>
                    @else
                        <div style="margin-top: auto; width: 100%;">
                            <div style="display: flex; justify-content: space-between; font-size: 10px; color: var(--text-light); margin-bottom: 4px;">
                                <span>Progress</span>
                                <span>{{ $displayProgress }} / {{ $target }}</span>
                            </div>
                            <div class="progress-track" style="height: 6px; background: #EEE; border-radius: 10px; overflow: hidden;">
                                <div class="progress-fill" style="width: {{ $percent }}%; height: 100%; background: #BDBDBD; border-radius: 10px;"></div>
                            </div>
                            <div style="font-size: 10px; color: var(--text-light); margin-top: 6px; font-weight: 500;">
                                🎁 Hadiah: +{{ $ach->xp_reward }} XP
                            </div>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    </div>
    @endforeach
</div>
@endsection
