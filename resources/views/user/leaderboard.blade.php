@extends('layouts.app')
@section('title','Leaderboard')
@section('page-title','Leaderboard')

@php
  $active = 'leaderboard';
@endphp

@section('content')
<div class="page-head"><div><h1>Leaderboard Bulan Ini</h1><p>Top 3 pengguna akan menerima reward khusus di akhir bulan.</p></div></div>

<div class="card">
  @foreach($leaderboard as $u)
    <div class="lb-row {{ ($u['me'] ?? false) ? 'me' : ($u['rank']<=3 ? 'top' : '') }}" style="padding:13px 10px;">
      <div class="lb-rank {{ $u['medal'] ?? '' }}" style="width:34px;height:34px;font-size:14px;">{{ $u['rank'] }}</div>
      @if($u['avatar'])
        <img src="{{ Storage::url($u['avatar']) }}" alt="Avatar" class="lb-avatar" style="width: 36px; height: 36px; border-radius: 50%; object-fit: cover;">
      @else
        <div class="lb-avatar" style="width:36px;height:36px;font-size:17px;">{{ $u['emoji'] }}</div>
      @endif
      <div>
        <a href="{{ route('user.public.profile', $u['id']) }}" class="lb-name" style="font-size:14px; text-decoration: none; color: inherit;">
          {{ $u['name'] }} {{ ($u['me'] ?? false) ? '(Kamu)' : '' }}
        </a>
        <div style="font-size:11.5px; color:var(--text-light);">{{ $u['dept'] }}</div>
      </div>
      <div class="lb-pts" style="font-size:15px;">{{ number_format($u['pts'],0,',','.') }} <span style="font-size:11px; font-weight:normal; color:var(--text-light);">Poin</span></div>
    </div>
  @endforeach
</div>
@endsection
