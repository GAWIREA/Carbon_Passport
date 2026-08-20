<aside class="sidebar">
  <div class="brand">
    <div class="brand-logo">🌱</div>
    <div>
      <div class="brand-name">EcoTrack</div>
      <div class="brand-tag">Track your daily carbon!</div>
    </div>
  </div>

  @auth
    <div class="role-switcher">
      <div class="role-switcher-label">Role Aktif</div>
      <div class="role-btn active" style="cursor:default; justify-content:center;">
        @if(auth()->user()->isAdmin())
          🛡️ Admin HR
        @elseif(auth()->user()->isSeller())
          🏪 Seller
        @else
          👤 Pengguna
        @endif
      </div>
    </div>

    @if(auth()->user()->isUser())
      <div class="nav-label">Dashboard Pengguna</div>
      <a href="{{ route('user.dashboard') }}" class="nav-item {{ ($active ?? '') === 'dashboard' ? 'active' : '' }}">🏠 Beranda</a>
      <a href="{{ route('user.tracking') }}" class="nav-item {{ ($active ?? '') === 'tracking' ? 'active' : '' }}">📡 Tracking Emisi</a>
      <a href="{{ route('user.history') }}" class="nav-item {{ ($active ?? '') === 'history' ? 'active' : '' }}">📋 Riwayat &amp; Detail Emisi</a>
      <a href="{{ route('user.recommendations') }}" class="nav-item {{ ($active ?? '') === 'recommendations' ? 'active' : '' }}">💡 Bank Misi &amp; Produk Pendukung</a>
      <a href="{{ route('user.leaderboard') }}" class="nav-item {{ ($active ?? '') === 'leaderboard' ? 'active' : '' }}">🏆 Leaderboard</a>
      <a href="{{ route('user.achievements') }}" class="nav-item {{ ($active ?? '') === 'achievements' ? 'active' : '' }}">🎖️ Pencapaian</a>
    @elseif(auth()->user()->isSeller())
      <div class="nav-label">Dashboard Seller</div>
      <a href="{{ route('seller.dashboard') }}" class="nav-item {{ ($active ?? '') === 'dashboard' ? 'active' : '' }}">📊 Ringkasan Penjualan</a>
      <a href="{{ route('seller.catalog') }}" class="nav-item {{ ($active ?? '') === 'catalog' ? 'active' : '' }}">🗂️ Katalog Produk</a>
      <a href="{{ route('seller.orders') }}" class="nav-item {{ ($active ?? '') === 'orders' ? 'active' : '' }}">📦 Daftar Pesanan</a>
    @elseif(auth()->user()->isAdmin())
      <div class="nav-label">Dashboard Admin HR</div>
      <a href="{{ route('admin.dashboard') }}" class="nav-item {{ ($active ?? '') === 'dashboard' ? 'active' : '' }}">🌍 ESG &amp; Analytics</a>
      <a href="{{ route('admin.users') }}" class="nav-item {{ ($active ?? '') === 'users' ? 'active' : '' }}">👥 User &amp; Seller</a>
      <a href="{{ route('admin.cms') }}" class="nav-item {{ ($active ?? '') === 'cms' ? 'active' : '' }}">📰 CMS Tips Harian</a>
    @endif
  @endauth

  <div class="sidebar-foot" style="margin-top:auto;">
    @auth
      <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit" class="nav-item" style="width:100%; background:none; border:none; cursor:pointer; color:inherit; text-align:left; padding:10px 16px; border-radius:10px;">🚪 Keluar</button>
      </form>
    @endauth
    <div style="margin-top:8px; font-size:11px; color:var(--text-light, #94a3b8);">EcoTrack · Beta</div>
  </div>
</aside>
