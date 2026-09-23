@extends('layouts.app')
@section('title','Leaderboard')
@section('page-title','Leaderboard')

@php
  $active = 'leaderboard';
@endphp

@section('content')
<div class="page-head"><div><h1>Leaderboard Bulan Ini</h1><p>Top 3 pengguna akan menerima reward khusus di akhir bulan.</p></div></div>

<div class="card" style="padding: 0; overflow: hidden;">
  @foreach($leaderboard as $u)
    @php
      $isMe = $u['me'] ?? false;
      $nameColor = $isMe ? '#FFFFFF' : 'var(--text-dark)';
      $deptColor = $isMe ? '#E2E8F0' : 'var(--text-light)';
      $ptsColor = $isMe ? '#FDE047' : 'var(--primary-dark)'; // Yellow for points
      $ptsLabelColor = $isMe ? '#FEF08A' : 'var(--text-light)';
    @endphp
    
    <div class="lb-row {{ $isMe ? 'me' : ($u['rank']<=3 ? 'top' : '') }}" style="padding: 12px 16px; border-bottom: 1px solid var(--border); display: flex; align-items: center; gap: 12px;">
      <div class="lb-rank {{ $u['medal'] ?? '' }}" style="width: 28px; height: 28px; font-size: 12px; display: flex; align-items: center; justify-content: center; border-radius: 50%; font-weight: bold; flex-shrink: 0;">
        {{ $u['rank'] }}
      </div>
      
      @if($u['avatar'])
        <img src="{{ Storage::url($u['avatar']) }}" alt="Avatar" class="lb-avatar" style="width: 34px; height: 34px; border-radius: 50%; object-fit: cover; flex-shrink: 0;">
      @else
        <div class="lb-avatar" style="width: 34px; height: 34px; font-size: 16px; display: flex; align-items: center; justify-content: center; border-radius: 50%; background: var(--bg); flex-shrink: 0;">
          {{ $u['emoji'] }}
        </div>
      @endif
      
      <div style="flex: 1; min-width: 0;">
        <a href="{{ route('user.public.profile', $u['id']) }}" class="lb-name" style="font-size: 13px; font-weight: 700; text-decoration: none; color: {{ $nameColor }}; display: block; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
          {{ $u['name'] }} {!! $isMe ? '<span style="color: #E2E8F0; font-weight: 500;">(Kamu)</span>' : '' !!}
        </a>
        <div style="font-size: 11px; color: {{ $deptColor }}; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; margin-top: 2px;">
          {{ $u['dept'] }}
        </div>
      </div>
      
      <div class="lb-pts" style="font-size: 13px; font-weight: 800; color: {{ $ptsColor }}; text-align: right; flex-shrink: 0;">
        {{ number_format($u['pts'], 0, ',', '.') }} 
        <span style="font-size: 10px; font-weight: 500; color: {{ $ptsLabelColor }}; display: block;">Poin</span>
      </div>
    </div>
  @endforeach
</div>
@endsection
