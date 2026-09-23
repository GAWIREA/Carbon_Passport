<header class="topbar">
  <style>
    .mobile-brand { display: none; font-weight: 800; font-size: 1.2rem; color: var(--primary); }
    .mobile-stats { display: none; }
    @media(max-width: 768px) {
      .topbar { position: relative; }
      .topbar-title { display: none; }
      .mobile-brand { 
        display: block; 
        position: absolute;
        left: 50%;
        top: 50%;
        transform: translate(-50%, -50%);
        margin: 0;
      }
      .gamification-stats, .cart-icon, .bell { display: none !important; }
      .user-chip > span { display: none; }
      .mobile-stats { 
        display: flex; 
        justify-content: space-around; 
        padding: 12px; 
        background: #F8F9FA; 
        border-bottom: 1px solid #eee;
        margin: -8px -16px 8px -16px;
      }
    }
  </style>
  <div style="display:flex; align-items:center; gap:12px;">
    <button class="menu-toggle" id="menuToggle">☰</button>
    <div class="topbar-title">@yield('page-title', 'Beranda')</div>
    <div class="mobile-brand">Ecotrack</div>
  </div>
  <div class="topbar-right" style="display:flex; align-items:center; gap:16px;">
    <!-- Gamification Stats -->
    <div class="gamification-stats" style="display:flex; gap:12px; background:var(--bg-card, #fff); padding:4px 12px; border-radius:20px; box-shadow:0 2px 4px rgba(0,0,0,0.05);">
      <div title="Streak Api" style="display:flex; align-items:center; gap:4px; font-weight:bold; color:#FF5722;">
        🔥 <span>{{ auth()->check() ? (auth()->user()->current_streak ?? 0) : 0 }}</span>
      </div>
      <div style="width:1px; background:#eee;"></div>
      <div title="Poin Leaderboard" style="display:flex; align-items:center; gap:4px; font-weight:bold; color:#3498DB;">
        🏆 <span>{{ auth()->check() ? number_format(auth()->user()->monthly_points ?? 0, 0, ',', '.') : 0 }}</span>
      </div>
      <div style="width:1px; background:#eee;"></div>
      <div title="Koin Belanja" style="display:flex; align-items:center; gap:4px; font-weight:bold; color:#F5A623;">
        🪙 <span>{{ auth()->check() ? number_format(auth()->user()->coins ?? 0, 0, ',', '.') : 0 }}</span>
      </div>
    </div>

    <!-- Keranjang Belanja -->
    <a href="{{ route('user.marketplace') }}" class="cart-icon" title="Keranjang" style="position:relative; text-decoration:none; color:inherit; font-size:1.2rem;">
      🛒
    </a>

    <a href="{{ route('user.notifications') }}" class="bell" style="text-decoration:none;">🔔</a>
    <div class="dropdown">
      <div class="user-chip" id="profileDropdownToggle" style="cursor: pointer;">
        <span>Hi, {{ auth()->user()?->name ?? 'Guest' }}</span>
        @if(auth()->check() && auth()->user()->avatar)
            <img src="{{ Storage::url(auth()->user()->avatar) }}" alt="Avatar" style="width: 35px; height: 35px; border-radius: 50%; object-fit: cover; margin-left: 8px;">
        @else
            <div class="user-avatar">👤</div>
        @endif
      </div>
      <div class="dropdown-menu" id="profileDropdownMenu">
        <div class="mobile-stats">
          <div title="Streak Api" style="display:flex; align-items:center; gap:4px; font-weight:bold; color:#FF5722; font-size:12px;">
            🔥 <span>{{ auth()->check() ? (auth()->user()->current_streak ?? 0) : 0 }}</span>
          </div>
          <div title="Poin Leaderboard" style="display:flex; align-items:center; gap:4px; font-weight:bold; color:#3498DB; font-size:12px;">
            🏆 <span>{{ auth()->check() ? number_format(auth()->user()->monthly_points ?? 0, 0, ',', '.') : 0 }}</span>
          </div>
          <div title="Koin Belanja" style="display:flex; align-items:center; gap:4px; font-weight:bold; color:#F5A623; font-size:12px;">
            🪙 <span>{{ auth()->check() ? number_format(auth()->user()->coins ?? 0, 0, ',', '.') : 0 }}</span>
          </div>
        </div>
        <a href="{{ route('user.marketplace') }}" class="dropdown-item">🛒 Keranjang</a>
        <a href="{{ route('user.notifications') }}" class="dropdown-item">🔔 Notifikasi</a>
        <a href="{{ route('user.profile') }}" class="dropdown-item">👤 Profil</a>
        <a href="{{ route('user.settings') }}" class="dropdown-item">⚙️ Pengaturan</a>
        <div class="dropdown-divider"></div>
        <form action="{{ route('logout') }}" method="POST" style="margin:0;">
          @csrf
          <button type="submit" class="dropdown-item" style="color: var(--danger);">
            🚪 Logout
          </button>
        </form>
      </div>
    </div>
  </div>
</header>

<script>
  // Dropdown toggle logic
  const profileToggle = document.getElementById('profileDropdownToggle');
  const profileMenu = document.getElementById('profileDropdownMenu');
  
  if(profileToggle && profileMenu) {
    profileToggle.addEventListener('click', (e) => {
      e.stopPropagation();
      profileMenu.classList.toggle('show');
    });
    
    // Close when clicking outside
    document.addEventListener('click', (e) => {
      if (!profileMenu.contains(e.target) && !profileToggle.contains(e.target)) {
        profileMenu.classList.remove('show');
      }
    });
  }
</script>
