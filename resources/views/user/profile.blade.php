@extends('layouts.app')

@section('title', 'Profil Pengguna')
@section('page-title', 'Profil Pengguna')

@section('content')
<div class="page-head">
    <div>
        <h1>Profil Saya</h1>
        <p>Ringkasan aktivitas dan pencapaian Anda.</p>
    </div>
</div>

<div class="card" style="width: 100%; border-top: 5px solid var(--primary);">
    <div style="display: flex; gap: 20px; align-items: flex-start; flex-wrap: wrap;">
        
        <!-- Kolom Kiri: Avatar -->
        <div>
            @if($user->avatar)
                <img src="{{ Storage::url($user->avatar) }}" alt="Avatar" style="width: 120px; height: 120px; border-radius: 50%; object-fit: cover; box-shadow: var(--shadow-sm);">
            @else
                <div class="user-avatar" style="width: 120px; height: 120px; font-size: 60px; flex-shrink: 0; box-shadow: var(--shadow-sm); display: flex; align-items: center; justify-content: center; background: var(--secondary-light);">👤</div>
            @endif
        </div>
        
        <!-- Kolom Kanan: Info & Aksi -->
        <div style="flex: 1; min-width: 250px;">
            <!-- Top Row: Info & Badge -->
            <div style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 12px; margin-bottom: 16px; width: 100%;">
                <div>
                    <h2 style="font-size: 28px; margin-bottom: 4px; font-weight: 800;">{{ $user->name }}</h2>
                    <div style="display: flex; align-items: center; gap: 12px;">
                        <div style="font-size: 15px; color: var(--text-light);">&commat;{{ $user->username }}</div>
                        <a href="{{ route('user.profile.edit') }}" class="btn btn-outline btn-sm" style="border-radius: 20px; font-size: 12px; padding: 4px 16px;">edit</a>
                    </div>
                </div>
                <div class="badge badge-green" style="white-space: nowrap; font-size: 13px; padding: 6px 14px;">bergabung sejak {{ strtolower($joinedAt) }}</div>
            </div>
            
            <!-- Level Progress Bar -->
            <a href="{{ route('user.level.details') }}" style="display:flex; flex-direction:column; justify-content:center; gap:8px; background:var(--bg-card, #fff); padding:16px 20px; border-radius:12px; box-shadow:0 2px 4px rgba(0,0,0,0.05); text-decoration:none; color:inherit; width: 100%; border: 1px solid var(--border); transition: transform 0.2s; margin-bottom: 20px;" onmouseover="this.style.transform='scale(1.01)'" onmouseout="this.style.transform='scale(1)'">
                <div style="display:flex; justify-content:space-between; align-items:center; font-size:14px; font-weight:bold; color:var(--text-dark);">
                    <span>{{ $levelInfo->label() }}</span>
                    <span style="color:var(--text-dark);">{{ number_format($user->xp, 0, ',', '.') }}/{{ number_format($levelInfo->xpForNextLevel(), 0, ',', '.') }} XP</span>
                </div>
                <div style="width:100%; background:#e0e0e0; height:12px; border-radius:6px; overflow:hidden;">
                    <div style="width:{{ $xpProgress }}%; background:#2ECC71; height:100%; border-radius:6px;"></div>
                </div>
            </a>

            <!-- Action Buttons -->
            <div style="display: flex; gap: 12px; flex-wrap: wrap; justify-content: flex-start; align-items: center;">
                <a href="{{ route('user.friends.add') }}" class="btn" style="gap: 8px; display: inline-flex; align-items: center; font-weight: bold; border-radius: 20px; padding: 8px 20px; background-color: #E8F5E9; color: #4CAF50; border: none; text-decoration: none;">
                    <span style="font-size: 16px;">👤+</span> Tambah teman
                </a>
                <button type="button" class="btn" style="gap: 8px; display: inline-flex; align-items: center; font-weight: bold; border-radius: 20px; padding: 8px 20px; background-color: #5C82FF; color: white; border: none;">
                    <span style="font-size: 16px;">🔗</span> Bagikan Profil
                </button>
            </div>
        </div>
    </div>

    <div class="dropdown-divider" style="margin: 24px 0;"></div>

    <!-- Statistik Ringkasan -->
    <div class="bento" style="margin-top: 16px;">
        <!-- Streak Card -->
        <div class="card bento-c1" style="background: linear-gradient(135deg, var(--warning-light), #fff); text-align: center; justify-content: center; align-items: center; padding: 30px 20px;">
            <div style="font-size: 40px; margin-bottom: 12px;">🔥</div>
            <div style="font-family: var(--font-head); font-size: 32px; font-weight: 800; color: var(--warning); line-height: 1;">{{ $streak }}</div>
            <div style="font-size: 13.5px; font-weight: 600; color: var(--text-light); margin-top: 8px;">Hari Streak Berturut-turut</div>
        </div>

        <!-- Pengikut Card -->
        <div class="card bento-c1" style="text-align: center; justify-content: center; align-items: center; padding: 30px 20px; cursor: pointer;" onclick="document.getElementById('followersModal').classList.add('show')">
            <div style="font-size: 40px; margin-bottom: 12px;">👥</div>
            <div style="font-family: var(--font-head); font-size: 32px; font-weight: 800; color: var(--primary-dark); line-height: 1;">{{ $followers }}</div>
            <div style="font-size: 13.5px; font-weight: 600; color: var(--text-light); margin-top: 8px;">Pengikut</div>
            <div style="font-size: 11px; color: var(--primary); margin-top: 4px;">Lihat Semua &rarr;</div>
        </div>

        <!-- Mengikuti Card -->
        <div class="card bento-c1" style="text-align: center; justify-content: center; align-items: center; padding: 30px 20px; cursor: pointer;" onclick="document.getElementById('followingModal').classList.add('show')">
            <div style="font-size: 40px; margin-bottom: 12px;">🚶</div>
            <div style="font-family: var(--font-head); font-size: 32px; font-weight: 800; color: var(--secondary); line-height: 1;">{{ $following }}</div>
            <div style="font-size: 13.5px; font-weight: 600; color: var(--text-light); margin-top: 8px;">Mengikuti</div>
            <div style="font-size: 11px; color: var(--primary); margin-top: 4px;">Lihat Semua &rarr;</div>
        </div>

        <!-- Pencapaian Singkat -->
        <div class="card bento-c1" style="background: var(--bg); display: flex; flex-direction: column; justify-content: center; padding: 24px;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
                <h3 style="font-size: 15px; margin: 0;">Lencana Teratas</h3>
                <div style="display: flex; gap: 8px;">
                    <button onclick="document.getElementById('editBadgesModal').classList.add('show')" style="font-size: 11px; color: var(--primary); background: none; border: none; cursor: pointer; padding: 0;">Edit</button>
                    <a href="{{ route('user.achievements') }}" style="font-size: 11px; color: var(--primary); text-decoration: none;">Lihat Semua &rarr;</a>
                </div>
            </div>
            
            <div style="display: flex; flex-direction: column; gap: 12px;">
                @if(isset($displayedAchievements) && $displayedAchievements->count() > 0)
                    @foreach($displayedAchievements as $da)
                    <div style="display: flex; align-items: center; gap: 12px;">
                        <div style="font-size: 24px; filter: drop-shadow(0 2px 2px rgba(0,0,0,0.1));">{{ $da->achievement->icon }}</div>
                        <div>
                            <div style="font-weight: 700; font-size: 14px; color: var(--text-dark);">{{ $da->achievement->name }}</div>
                            <div style="font-size: 11px; color: var(--text-light);">{{ $da->achievement->category }}</div>
                        </div>
                    </div>
                    @endforeach
                @else
                    <div style="text-align: center; padding: 12px 0;">
                        <div style="font-size: 24px; filter: grayscale(100%) opacity(0.5); margin-bottom: 8px;">🏆</div>
                        <div style="font-size: 12px; color: var(--text-light);">Belum ada lencana yang dipamerkan.</div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
<!-- Modal Pengikut -->
<div class="modal-overlay" id="followersModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center;">
    <div class="card" style="width: 100%; max-width: 400px; max-height: 80vh; overflow-y: auto;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
            <h3 style="font-size: 18px;">Pengikut</h3>
            <button onclick="document.getElementById('followersModal').classList.remove('show')" style="font-size: 20px; color: var(--text-light);">&times;</button>
        </div>
        <div style="display: flex; flex-direction: column; gap: 12px;">
            @if(isset($user))
                @forelse($user->followers as $f)
                    <a href="{{ route('user.public.profile', $f->id) }}" style="display: flex; align-items: center; gap: 12px; padding: 8px; border-radius: 8px; transition: .2s;">
                        <div class="user-avatar" style="width: 40px; height: 40px; font-size: 20px;">👤</div>
                        <div style="font-weight: 600; color: var(--text-dark);">{{ $f->name }}</div>
                    </a>
                @empty
                    <p style="color: var(--text-light); text-align: center; padding: 20px 0;">Belum ada pengikut.</p>
                @endforelse
            @endif
        </div>
    </div>
</div>

<!-- Modal Mengikuti -->
<div class="modal-overlay" id="followingModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center;">
    <div class="card" style="width: 100%; max-width: 400px; max-height: 80vh; overflow-y: auto;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
            <h3 style="font-size: 18px;">Mengikuti</h3>
            <button onclick="document.getElementById('followingModal').classList.remove('show')" style="font-size: 20px; color: var(--text-light);">&times;</button>
        </div>
        <div style="display: flex; flex-direction: column; gap: 12px;">
            @if(isset($user))
                @forelse($user->followings as $f)
                    <a href="{{ route('user.public.profile', $f->id) }}" style="display: flex; align-items: center; gap: 12px; padding: 8px; border-radius: 8px; transition: .2s;">
                        <div class="user-avatar" style="width: 40px; height: 40px; font-size: 20px;">👤</div>
                        <div style="font-weight: 600; color: var(--text-dark);">{{ $f->name }}</div>
                    </a>
                @empty
                    <p style="color: var(--text-light); text-align: center; padding: 20px 0;">Belum mengikuti siapapun.</p>
                @endforelse
            @endif
        </div>
    </div>
</div>

<!-- Modal Edit Lencana -->
<div class="modal-overlay" id="editBadgesModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center;">
    <div class="card" style="width: 100%; max-width: 400px; max-height: 80vh; overflow-y: auto;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
            <h3 style="font-size: 18px;">Pilih Lencana Dipamerkan</h3>
            <button onclick="document.getElementById('editBadgesModal').classList.remove('show')" style="font-size: 20px; color: var(--text-light); background: none; border: none; cursor: pointer;">&times;</button>
        </div>
        <p style="font-size: 12px; color: var(--text-light); margin-bottom: 16px;">Pilih maksimal 5 lencana untuk ditampilkan di profil Anda.</p>
        <form action="{{ route('user.profile.achievements.update') }}" method="POST">
            @csrf
            @method('PUT')
            <div style="display: flex; flex-direction: column; gap: 12px; margin-bottom: 20px;">
                @if(isset($userAchievements) && $userAchievements->count() > 0)
                    @foreach($userAchievements as $ua)
                        <label style="display: flex; align-items: center; gap: 12px; cursor: pointer; padding: 8px; border: 1px solid var(--border); border-radius: 8px;">
                            <input type="checkbox" name="achievements[]" value="{{ $ua->achievement_id }}" class="badge-checkbox" {{ $ua->is_displayed ? 'checked' : '' }} onchange="checkMaxBadges(this)">
                            <div style="font-size: 24px;">{{ $ua->achievement->icon }}</div>
                            <div style="flex: 1;">
                                <div style="font-weight: 700; font-size: 14px;">{{ $ua->achievement->name }}</div>
                                <div style="font-size: 11px; color: var(--text-light);">{{ $ua->achievement->category }}</div>
                            </div>
                        </label>
                    @endforeach
                @else
                    <p style="font-size: 13px; color: var(--text-light); text-align: center;">Anda belum memiliki lencana.</p>
                @endif
            </div>
            <button type="submit" class="btn btn-primary" style="width: 100%;">Simpan Perubahan</button>
        </form>
    </div>
</div>

<style>
.modal-overlay.show { display: flex !important; }
.modal-overlay a:hover { background: var(--bg); }
</style>

<script>
    document.querySelectorAll('.modal-overlay').forEach(modal => {
        modal.addEventListener('click', (e) => {
            if (e.target === modal) {
                modal.classList.remove('show');
            }
        });
    });

    function checkMaxBadges(checkbox) {
        const checkedBoxes = document.querySelectorAll('.badge-checkbox:checked');
        if (checkedBoxes.length > 5) {
            checkbox.checked = false;
            alert('Maksimal 5 lencana yang dapat dipamerkan.');
        }
    }
</script>
