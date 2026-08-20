@extends('layouts.app')

@section('title', 'Cari Teman')
@section('page-title', 'Cari Teman')

@section('content')
<div class="page-head">
    <div>
        <a href="{{ route('user.profile') }}" style="font-size: 14px; color: var(--primary); margin-bottom: 8px; display: inline-block;">&larr; Kembali ke Profil</a>
        <h1>Cari Teman</h1>
        <p>Temukan rekan kerja dan lihat progres karbon mereka.</p>
    </div>
</div>

<div class="card" style="margin-bottom: 24px;">
    <form action="{{ route('user.friends.add') }}" method="GET" style="display: flex; gap: 12px; align-items: center;">
        <input type="text" name="q" class="input" placeholder="Cari berdasarkan nama atau username..." value="{{ request('q') }}" style="flex: 1;">
        <button type="submit" class="btn btn-primary" style="padding: 10px 24px;">Cari</button>
    </form>
</div>

@if(isset($query))
    <h3 style="font-size: 16px; margin-bottom: 16px;">Hasil Pencarian untuk "{{ $query }}"</h3>
    
    @if(count($users) > 0)
        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 16px;">
            @foreach($users as $u)
                <div class="card" style="display: flex; flex-direction: row; align-items: center; justify-content: space-between; padding: 16px;">
                    <div style="display: flex; align-items: center; gap: 12px;">
                        @if($u->avatar)
                            <img src="{{ Storage::url($u->avatar) }}" alt="Avatar" style="width: 48px; height: 48px; border-radius: 50%; object-fit: cover;">
                        @else
                            <div class="user-avatar" style="width: 48px; height: 48px; font-size: 24px; background: var(--primary-light); display: flex; align-items: center; justify-content: center;">👤</div>
                        @endif
                        <div>
                            <div style="font-weight: 700; color: var(--text-dark); font-size: 15px;">{{ $u->name }}</div>
                            <div style="font-size: 12px; color: var(--text-light);">&commat;{{ $u->username }}</div>
                        </div>
                    </div>
                    <a href="{{ route('user.public.profile', $u->id) }}" class="btn btn-outline btn-sm" style="border-radius: 20px;">Lihat Profil</a>
                </div>
            @endforeach
        </div>
    @else
        <div class="card" style="text-align: center; padding: 40px;">
            <div style="font-size: 40px; margin-bottom: 16px;">🔍</div>
            <h3 style="margin-bottom: 8px;">Tidak ada hasil</h3>
            <p style="color: var(--text-light);">Coba gunakan kata kunci lain untuk mencari rekan Anda.</p>
        </div>
    @endif
@endif

@endsection
