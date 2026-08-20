@extends('layouts.app')
@section('title','Bank Misi & Produk Pendukung')
@section('page-title','Bank Misi & Produk Pendukung')

@php
  $active = 'recommendations';
@endphp

@section('content')
<div class="page-head" style="margin-bottom: 24px;">
  <h1>Bank Misi & Produk Pendukung 💡</h1>
  <p>Kumpulkan poin dan koin dengan menyelesaikan misi mingguan dan rekomendasi harian, lalu tukarkan koinmu dengan berbagai produk menarik.</p>
</div>

<div class="card" style="margin-bottom:18px; padding: 16px 24px;">
  <div style="display: flex; align-items: center; gap: 16px;">
    <div style="font-size: 14px; font-weight: 600; color: var(--text-dark);">Filter Kategori:</div>
    <div class="filter-tabs" style="margin: 0; padding: 0;">
      <button class="filter-tab cat-tab active" data-cat="all">Semua</button>
      <button class="filter-tab cat-tab" data-cat="weekly">⭐ Misi Mingguan</button>
      <button class="filter-tab cat-tab" data-cat="daily">🎯 Misi Harian</button>
      <button class="filter-tab cat-tab" data-cat="product">🛒 Produk Pendukung</button>
    </div>
  </div>
</div>

<!-- Section: Misi Mingguan -->
<div class="card" style="margin-bottom:24px; padding: 24px; background: linear-gradient(to right, #FFF, #FAFAFF); border: 1px solid var(--border);">
  <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 16px;">
    <div>
        <h2 style="margin-top:0; margin-bottom: 4px; font-size: 18px; color: var(--text-dark); display: flex; align-items: center; gap: 8px;">
            ⭐ Misi Mingguan
        </h2>
        <span style="font-size: 12px; font-weight: normal; color: var(--text-light);">Selesaikan sebelum batas waktu habis! Progress akan otomatis bertambah saat kamu mencatat emisi.</span>
    </div>
    <span class="badge badge-blue" style="font-size: 12px; padding: 4px 10px;">{{ collect($weeklyMissions)->where('status','done')->count() }} / {{ count($weeklyMissions) }} Selesai</span>
  </div>
  
  <div class="recList section-list" data-expanded="false" style="display: flex; flex-direction: column; gap: 12px;">
    @foreach($weeklyMissions as $m)
      @php $pct = min(100, round($m['progress'] / $m['target'] * 100)); @endphp
      <div class="rec-card" data-cat="weekly" style="display: flex; flex-direction: column; padding: 16px; border: 1px solid {{ $m['status'] === 'done' ? 'rgba(46,204,113,0.3)' : 'var(--border)' }}; border-radius: 12px; background: {{ $m['status'] === 'claimed' ? '#F5F5F5' : ($m['status'] === 'done' ? 'rgba(46,204,113,0.02)' : '#FFF') }}; box-shadow: 0 1px 3px rgba(0,0,0,0.02); opacity: {{ $m['status'] === 'claimed' ? '0.6' : '1' }};">
        <div style="display: flex; align-items: center; justify-content: space-between; gap: 12px;">
          <div style="flex: 1; min-width: 0; display: flex; align-items: center; gap: 12px;">
            <div style="width: 44px; height: 44px; border-radius: 10px; background: {{ $m['color'] }}22; display: flex; align-items: center; justify-content: center; font-size: 20px; flex-shrink: 0;">
              {{ $m['icon'] }}
            </div>
            <div>
              <h3 style="margin: 0 0 4px 0; font-size: 14.5px; font-weight: 700; color: var(--text-dark); line-height: 1.3;">
                {{ $m['title'] }}
              </h3>
              <div style="display: flex; align-items: center; gap: 8px; flex-wrap: wrap;">
                <span class="badge" style="font-size: 10px; padding: 2px 8px; background: {{ $m['color'] }}18; color: {{ $m['color'] }};">{{ $m['category'] }}</span>
                @if($m['status'] === 'done' || $m['status'] === 'claimed')
                  <span style="font-size: 11.5px; color: #2ECC71; font-weight: 600;">✓ Selesai</span>
                @else
                  <span style="font-size: 11.5px; color: #F5A623; font-weight: 500;">⏳ Sisa {{ $m['daysLeft'] }} hari</span>
                @endif
              </div>
            </div>
          </div>
          <div style="display: flex; flex-direction: column; align-items: flex-end; gap: 6px; flex-shrink: 0;">
              <div style="display:flex; flex-direction:column; gap:6px; align-items:flex-end; margin-bottom:8px;">
                <span style="background:rgba(91,143,255,0.15); color:var(--primary); padding:4px 8px; border-radius:8px; font-size:12px; font-weight:700; display:inline-block;">✨ +{{ $m['reward_points'] }} Poin</span>
                <span style="background:rgba(245,166,35,0.15); color:#d88c14; padding:4px 8px; border-radius:8px; font-size:12px; font-weight:700; display:inline-block;">🪙 +{{ $m['reward_coins'] }} Koin</span>
              </div>
              @if($m['status'] === 'done')
                <form action="{{ route('user.weekly-mission.claim', $m['id']) }}" method="POST" style="margin: 0;" onsubmit="return confirm('Klaim reward untuk misi mingguan ini?');">
                  @csrf
                  <button type="submit" class="btn btn-sm" style="background:#2ECC71;color:white;border-radius:20px;padding:4px 14px;font-size:11.5px;font-weight:600;border:none;cursor:pointer;">Klaim Reward</button>
                </form>
              @elseif($m['status'] === 'claimed')
                <span style="font-size: 11.5px; color: #2ECC71; font-weight: 600;">✓ Diklaim</span>
              @else
                <a href="{{ route('user.tracking') }}" class="btn btn-outline btn-sm" style="padding: 4px 14px; border-radius: 20px; font-weight: 600; font-size: 11.5px; text-decoration: none;">Catat Emisi</a>
              @endif
          </div>
        </div>
        <div style="margin-top: 14px; padding-top: 14px; border-top: 1px dashed var(--border);">
          <div class="progress-label" style="font-size: 11.5px; margin-bottom: 6px; display: flex; justify-content: space-between;">
              <span style="color: var(--text-light);">{{ $m['progress'] }} dari {{ $m['target'] }} {{ $m['type'] }}</span>
              <b style="color: {{ $m['color'] }};">{{ $pct }}%</b>
          </div>
          <div class="progress-track" style="height: 6px; background: #EEE; border-radius: 10px; overflow: hidden;">
              <div class="progress-fill" style="width:{{ $pct }}%; height: 100%; background: {{ $m['color'] }}; border-radius: 10px;"></div>
          </div>
        </div>
      </div>
    @endforeach
  </div>
  <div style="text-align: center; margin-top: 16px;">
    <button class="btn btn-outline btn-sm btn-show-more" style="display: none; border-radius: 20px; font-weight: 600; padding: 6px 16px;">Lihat Lebih Banyak ↓</button>
  </div>
</div>

<!-- Section: Misi Harian -->
<div class="card" style="margin-bottom:24px; padding: 24px;">
  <h2 style="margin-top:0; margin-bottom: 16px; font-size: 18px; color: var(--text-dark); display: flex; align-items: center; gap: 8px;">
      🎯 Rekomendasi Misi Harian
      <span style="font-size: 12px; font-weight: normal; color: var(--text-light);">(Selesaikan misi untuk mendapatkan poin tambahan)</span>
  </h2>
  
  <div class="recList section-list" data-expanded="false" style="display: flex; flex-direction: column; gap: 16px;">
    @foreach($recommendations as $i => $r)
      @if($r['type'] === 'action')
        @php $pct = round(($r['done']/($r['target'] ?? 1))*100); @endphp
        <div class="rec-card" data-cat="daily" style="display: flex; flex-direction: column; padding: 16px; border: 1px solid var(--border); border-radius: 12px; background: {{ (isset($r['status']) && $r['status'] === 'claimed') ? '#F5F5F5' : '#FFF' }}; box-shadow: 0 1px 3px rgba(0,0,0,0.02); margin-bottom: 0; opacity: {{ (isset($r['status']) && $r['status'] === 'claimed') ? '0.6' : '1' }};">
          <div style="display: flex; align-items: center; justify-content: space-between; gap: 12px;">
            <!-- Kiri: Info Misi -->
            <div style="flex: 1; min-width: 0; display: flex; align-items: center; gap: 12px;">
              <div style="width: 44px; height: 44px; border-radius: 10px; background: {{ $r['bg'] }}; display: flex; align-items: center; justify-content: center; font-size: 20px; flex-shrink: 0;">
                {{ $r['icon'] }}
              </div>
              <div>
                <h3 style="margin: 0 0 4px 0; font-size: 14.5px; font-weight: 700; color: var(--text-dark); line-height: 1.3;">
                  {{ $r['title'] }}
                </h3>
                <div style="display: flex; align-items: center; gap: 8px; flex-wrap: wrap;">
                  <span class="badge badge-grey" style="font-size: 10px; padding: 2px 8px;">{{ $r['cat'] }}</span>
                  <span style="font-size: 11.5px; color: var(--text-main); font-weight: 500;">🌿 {{ $r['impact'] }}</span>
                </div>
              </div>
            </div>
            
            <!-- Kanan: Reward & Tombol -->
            <div style="display: flex; flex-direction: column; align-items: flex-end; gap: 6px; flex-shrink: 0;">
                <div style="display:flex; flex-direction:column; gap:6px; align-items:flex-end; margin-bottom:8px;">
                  <span style="background:rgba(91,143,255,0.15); color:var(--primary); padding:4px 8px; border-radius:8px; font-size:12px; font-weight:700; display:inline-block;">✨ +{{ $r['reward_points'] }} Poin</span>
                  <span style="background:rgba(245,166,35,0.15); color:#d88c14; padding:4px 8px; border-radius:8px; font-size:12px; font-weight:700; display:inline-block;">🪙 +{{ $r['reward_coins'] }} Koin</span>
                </div>
                @if(isset($r['status']) && $r['status'] === 'done')
                    <form action="{{ route('user.daily-mission.claim') }}" method="POST" style="display:inline; margin: 0;" onsubmit="return confirm('Klaim reward untuk misi harian ini?');">
                        @csrf
                        <input type="hidden" name="title" value="{{ $r['title'] }}">
                        <input type="hidden" name="reward_points" value="{{ $r['reward_points'] }}">
                        <input type="hidden" name="reward_coins" value="{{ $r['reward_coins'] }}">
                        <button type="submit" class="btn btn-sm" style="background:#2ECC71;color:white;border-radius:20px;padding:4px 14px;font-size:11.5px;font-weight:600;border:none;cursor:pointer;">Klaim Reward</button>
                    </form>
                @elseif(isset($r['status']) && $r['status'] === 'claimed')
                    <span style="font-size: 11.5px; color: #2ECC71; font-weight: 600;">✓ Diklaim</span>
                @else
                    <a href="{{ route('user.tracking') }}" class="btn btn-outline btn-sm" style="padding: 4px 14px; border-radius: 20px; font-weight: 600; font-size: 11.5px; text-decoration: none; border-color: var(--primary); color: var(--primary);">Catat Emisi</a>
                @endif
            </div>
          </div>

          <!-- Bawah: Progress -->
          <div style="margin-top: 14px; padding-top: 14px; border-top: 1px dashed var(--border);">
            <div class="progress-label" style="font-size: 11.5px; margin-bottom: 6px; display: flex; justify-content: space-between;">
                <span style="color: var(--text-light);">Progres Misi ({{ $r['done'] }} dari {{ $r['target'] ?? 1 }})</span>
                <b style="color: var(--primary);">{{ $pct }}%</b>
            </div>
            <div class="progress-track" style="height: 6px; background: #EEE; border-radius: 10px; overflow: hidden;">
                <div class="progress-fill" style="width:{{ $pct }}%; height: 100%; background: var(--primary); border-radius: 10px;"></div>
            </div>
          </div>
        </div>
      @endif
    @endforeach
  </div>
  <div style="text-align: center; margin-top: 16px;">
    <button class="btn btn-outline btn-sm btn-show-more" style="display: none; border-radius: 20px; font-weight: 600; padding: 6px 16px;">Lihat Lebih Banyak ↓</button>
  </div>
</div>

<!-- Section: Produk Rekomendasi -->
<div class="card" style="margin-bottom:24px; padding: 24px;">
  <div style="font-size: 14px; font-weight: bold; margin-bottom: 12px;">Produk Pendukung
      <span style="font-size: 12px; font-weight: normal; color: var(--text-light);">(Tukar koinmu dengan produk ini)</span>
    </div>
  
  <div class="recList section-list" data-expanded="false" style="display: flex; flex-direction: column; gap: 16px;">
    @foreach($recommendations as $i => $r)
      @if($r['type'] === 'product')
        <div class="rec-card" data-cat="product" style="display: flex; align-items: stretch; gap: 16px; padding: 16px; border: 1px solid var(--border); background: #FFF; box-shadow: 0 2px 8px rgba(0,0,0,0.04); border-radius: 16px; transition: transform 0.2s;">
          <!-- Left: Big Icon -->
          <div style="width: 80px; flex-shrink: 0; background: {{ $r['bg'] }}; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 36px;">
            {{ $r['icon'] }}
          </div>

          <!-- Middle: Content -->
          <div style="flex-grow: 1; display: flex; flex-direction: column; justify-content: center;">
            <h3 style="margin: 0 0 8px 0; font-size: 16px; color: var(--text-dark); line-height: 1.4;">{{ $r['title'] }}</h3>
            
            <div style="display:flex; align-items:center; gap:8px; margin-bottom: 8px;">
              <span class="badge badge-grey" style="font-size: 11px;">{{ $r['cat'] }}</span>
            </div>
            
            <div style="font-size: 13px; color: var(--text-main); font-weight: 500;">
              🌿 {{ $r['impact'] }}
            </div>
          </div>

          <!-- Right: CTA Section -->
          <div style="width: 160px; flex-shrink: 0; border-left: 1px dashed #E0E0E0; padding-left: 16px; display: flex; flex-direction: column; justify-content: center; align-items: center; text-align: center;">
            <div style="font-size: 20px; font-weight: 800; color: #F5A623; line-height: 1.2;">
              🪙 {{ $r['coin_price'] }} Koin
            </div>
            @if(isset($r['price']))
              <div style="font-size: 11px; color: var(--text-light); margin-top: 4px;">{{ $r['price'] }}</div>
            @endif
            <div style="margin-top: 12px; width: 100%;">
              <a href="{{ route('user.marketplace.detail', $r['product_id'] ?? 1) }}" class="btn btn-outline btn-sm" style="width: 100%; padding: 8px; border-radius: 20px; font-weight: 600; text-decoration: none; display: block; border-color: #F5A623; color: #F5A623;">Lihat Detail</a>
            </div>
          </div>
        </div>
      @endif
    @endforeach
  </div>
  <div style="text-align: center; margin-top: 16px;">
    <button class="btn btn-outline btn-sm btn-show-more" style="display: none; border-radius: 20px; font-weight: 600; padding: 6px 16px;">Lihat Lebih Banyak ↓</button>
  </div>
</div>
@endsection

@push('scripts')
<script>
let catFilter = 'all';
function applyFilters(){
  document.querySelectorAll('.section-list').forEach(list => {
    let visibleCount = 0;
    const isExpanded = list.dataset.expanded === 'true';
    const showMoreBtn = list.parentElement.querySelector('.btn-show-more');
    const parentCard = list.closest('.card');
    
    list.querySelectorAll('.rec-card').forEach(card => {
      const matchCat = catFilter === 'all' || card.dataset.cat === catFilter;
      if(matchCat) {
        visibleCount++;
        if(visibleCount > 3 && !isExpanded) {
           card.style.display = 'none';
        } else {
           card.style.display = 'flex';
        }
      } else {
        card.style.display = 'none';
      }
    });

    if(parentCard) {
       parentCard.style.display = (visibleCount === 0 && catFilter !== 'all') ? 'none' : 'block';
    }

    if(showMoreBtn) {
       if(visibleCount > 3 && !isExpanded) {
          showMoreBtn.style.display = 'inline-block';
          showMoreBtn.textContent = 'Lihat ' + (visibleCount - 3) + ' Lainnya ↓';
       } else {
          showMoreBtn.style.display = 'none';
       }
    }
  });
}

document.querySelectorAll('.cat-tab').forEach(btn=>{
  btn.addEventListener('click', ()=>{
    document.querySelectorAll('.cat-tab').forEach(b=>b.classList.remove('active'));
    btn.classList.add('active');
    catFilter = btn.dataset.cat;
    
    // Reset toggle expansion on filter change
    document.querySelectorAll('.section-list').forEach(list => list.dataset.expanded = 'false');
    applyFilters();
  });
});

document.querySelectorAll('.btn-show-more').forEach(btn => {
  btn.addEventListener('click', (e) => {
    const list = e.target.parentElement.parentElement.querySelector('.section-list');
    if(list) {
      list.dataset.expanded = 'true';
      applyFilters();
    }
  });
});

// Jalankan saat load
applyFilters();
</script>
@endpush
