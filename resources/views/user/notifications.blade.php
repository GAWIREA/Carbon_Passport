@extends('layouts.app')

@section('title', 'Notifikasi')
@section('page-title', 'Notifikasi')

@push('styles')
<style>
  .notification-container {
    max-width: 800px;
    margin: 0 auto;
  }
  .notification-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
  }
  .notification-tabs {
    display: flex;
    gap: 12px;
    margin-bottom: 20px;
    border-bottom: 1px solid #eee;
    padding-bottom: 8px;
    overflow-x: auto;
  }
  .tab-btn {
    background: none;
    border: none;
    padding: 8px 16px;
    font-weight: 600;
    color: var(--text-muted, #64748b);
    cursor: pointer;
    border-radius: 20px;
    white-space: nowrap;
    transition: all 0.2s;
  }
  .tab-btn.active {
    background: var(--primary);
    color: white;
  }
  .tab-btn:hover:not(.active) {
    background: #f1f5f9;
  }
  .notification-list {
    display: flex;
    flex-direction: column;
    gap: 12px;
  }
  .notification-item {
    display: flex;
    gap: 16px;
    background: var(--bg-card, #fff);
    padding: 16px;
    border-radius: 12px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.02);
    border-left: 4px solid transparent;
    transition: transform 0.2s;
  }
  .notification-item:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.05);
  }
  .notification-item.unread {
    background: #f8fafc;
    border-left-color: var(--primary);
  }
  .notif-icon {
    font-size: 1.5rem;
    width: 48px;
    height: 48px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #f1f5f9;
    border-radius: 50%;
    flex-shrink: 0;
  }
  .notif-content {
    flex: 1;
  }
  .notif-title {
    font-weight: 700;
    color: var(--text-dark, #1e293b);
    margin-bottom: 4px;
    font-size: 1.05rem;
  }
  .notif-message {
    color: var(--text-muted, #64748b);
    font-size: 0.95rem;
    line-height: 1.4;
    margin-bottom: 8px;
  }
  .notif-meta {
    font-size: 0.8rem;
    color: #94a3b8;
    display: flex;
    justify-content: space-between;
  }
  .mark-read-btn {
    background: none;
    border: none;
    color: var(--primary);
    font-weight: 600;
    cursor: pointer;
    font-size: 0.9rem;
  }
  .mark-read-btn:hover {
    text-decoration: underline;
  }
  .empty-state {
    text-align: center;
    padding: 40px 20px;
    color: var(--text-muted, #64748b);
  }
</style>
@endpush

@section('content')
<div class="notification-container">
  <div class="notification-header">
    <h2 style="margin: 0;">Pusat Notifikasi</h2>
    <button class="mark-read-btn" onclick="markAllRead()">Tandai Semua Dibaca</button>
  </div>

  <div class="notification-tabs">
    <button class="tab-btn active" data-target="all">Semua</button>
    <button class="tab-btn" data-target="gamification">Gamifikasi</button>
    <button class="tab-btn" data-target="tracking">Carbon Tracking</button>
    <button class="tab-btn" data-target="marketplace">Marketplace</button>
    <button class="tab-btn" data-target="system">Sistem & Edukasi</button>
  </div>

  <div class="notification-list" id="notifList">
    <!-- Dummy Data -->
    <div class="notification-item unread" data-category="gamification">
      <div class="notif-icon">🏆</div>
      <div class="notif-content">
        <div class="notif-title">Badge Baru Terbuka!</div>
        <div class="notif-message">Selamat! Anda baru saja mendapatkan Badge Emas "Eco Warrior" atas pencapaian Anda bulan ini.</div>
        <div class="notif-meta">
          <span>Baru saja</span>
        </div>
      </div>
    </div>

    <div class="notification-item unread" data-category="tracking">
      <div class="notif-icon">🌱</div>
      <div class="notif-content">
        <div class="notif-title">Jangan lupa catat emisi Anda hari ini</div>
        <div class="notif-message">Yuk sinkronkan data perjalanan Anda untuk menjaga streak dan mendapatkan poin tambahan!</div>
        <div class="notif-meta">
          <span>2 jam yang lalu</span>
        </div>
      </div>
    </div>

    <div class="notification-item" data-category="marketplace">
      <div class="notif-icon">📦</div>
      <div class="notif-content">
        <div class="notif-title">Pesanan Anda sedang dikirim</div>
        <div class="notif-message">Sedotan Bambu Organik (Isi 10) Anda sedang dalam perjalanan oleh kurir pengiriman.</div>
        <div class="notif-meta">
          <span>Kemarin, 14:30</span>
        </div>
      </div>
    </div>

    <div class="notification-item" data-category="system">
      <div class="notif-icon">💡</div>
      <div class="notif-content">
        <div class="notif-title">Tips Harian: Hemat Energi</div>
        <div class="notif-message">Cabut colokan listrik pada perangkat elektronik yang tidak digunakan dapat menghemat energi hingga 10% di rumah Anda.</div>
        <div class="notif-meta">
          <span>2 hari yang lalu</span>
        </div>
      </div>
    </div>

    <div class="notification-item" data-category="gamification">
      <div class="notif-icon">🔥</div>
      <div class="notif-content">
        <div class="notif-title">Streak Terancam!</div>
        <div class="notif-message">Login hari ini untuk mempertahankan 5 hari beruntun Anda dan raih hadiahnya.</div>
        <div class="notif-meta">
          <span>2 hari yang lalu</span>
        </div>
      </div>
    </div>
  </div>
  
  <div class="empty-state" id="emptyState" style="display: none;">
    <div style="font-size: 3rem; margin-bottom: 16px;">📭</div>
    <div style="font-weight: 600; font-size: 1.1rem; margin-bottom: 8px;">Belum ada notifikasi</div>
    <div style="font-size: 0.95rem;">Semua notifikasi terkait aktivitas Anda akan muncul di sini.</div>
  </div>
</div>
@endsection

@push('scripts')
<script>
  function markAllRead() {
    document.querySelectorAll('.notification-item').forEach(item => {
      item.classList.remove('unread');
    });
    if(typeof showToast === 'function') {
        showToast('Semua notifikasi telah ditandai dibaca');
    } else {
        alert('Semua notifikasi telah ditandai dibaca');
    }
    // Hilangkan dot merah di topbar jika ada
    const ping = document.querySelector('.bell .ping');
    if (ping) ping.style.display = 'none';
  }

  // Filter Logic
  const tabs = document.querySelectorAll('.tab-btn');
  const items = document.querySelectorAll('.notification-item');
  const emptyState = document.getElementById('emptyState');

  tabs.forEach(tab => {
    tab.addEventListener('click', () => {
      // Ubah active state tab
      tabs.forEach(t => t.classList.remove('active'));
      tab.classList.add('active');

      const target = tab.getAttribute('data-target');
      let visibleCount = 0;

      items.forEach(item => {
        if (target === 'all' || item.getAttribute('data-category') === target) {
          item.style.display = 'flex';
          visibleCount++;
        } else {
          item.style.display = 'none';
        }
      });

      if (visibleCount === 0) {
        emptyState.style.display = 'block';
      } else {
        emptyState.style.display = 'none';
      }
    });
  });
</script>
@endpush
