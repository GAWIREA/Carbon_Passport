@extends('layouts.app')
@section('title','Riwayat & Detail Emisi')
@section('page-title','Riwayat & Detail Emisi')

@php
  $active = 'history';

  $categoryMeta = [
    'transportasi'      => ['icon' => '🚗', 'label' => 'Transportasi',       'color' => '#5B8FFF', 'bg' => '#EFF4FF'],
    'energi'            => ['icon' => '⚡', 'label' => 'Konsumsi Energi',     'color' => '#F5A623', 'bg' => '#FFFBEB'],
    'bahan_bakar'       => ['icon' => '⛽', 'label' => 'Bahan Bakar',         'color' => '#EF4444', 'bg' => '#FEF2F2'],
    'limbah'            => ['icon' => '🗑️', 'label' => 'Limbah',             'color' => '#8B5CF6', 'bg' => '#F5F3FF'],
    'air'               => ['icon' => '💧', 'label' => 'Air',                 'color' => '#0EA5E9', 'bg' => '#F0F9FF'],
    'energi_terbarukan' => ['icon' => '🌱', 'label' => 'Energi Terbarukan',  'color' => '#22C55E', 'bg' => '#F0FFF4'],
    'makanan'           => ['icon' => '🍽️', 'label' => 'Makanan',            'color' => '#F59E0B', 'bg' => '#FFFBEB'],
  ];
@endphp

@section('content')
<div class="page-head">
  <div>
    <h1>Riwayat & Detail Emisi</h1>
    <p>Rincian setiap aktivitas karbon yang kamu catat. Data bulan {{ now()->translatedFormat('F Y') }}.</p>
  </div>
</div>

@if(session('status'))
  <div style="background:#F0FFF4; border:1px solid #86EFAC; border-radius:10px; padding:12px 18px; margin-bottom:18px; font-size:13px; color:#166534; font-weight:600;">
    ✅ {{ session('status') }}
  </div>
@endif

{{-- Summary Cards Bulan Ini --}}
<div class="bento" style="margin-bottom:18px; grid-template-columns:repeat(auto-fit, minmax(160px, 1fr)); gap:14px;">
  <div class="card bento-c1">
    <div class="stat-icon" style="background:#FEF2F2;">⚠️</div>
    <div class="stat-big">{{ number_format($monthlyEmitted, 1) }}<span class="unit">kg</span></div>
    <div class="stat-sub">Emisi Bulan Ini</div>
  </div>
  <div class="card bento-c1">
    <div class="stat-icon" style="background:#F0FFF4;">🌿</div>
    <div class="stat-big">{{ number_format($monthlySaved, 1) }}<span class="unit">kg</span></div>
    <div class="stat-sub">Hemat Bulan Ini</div>
  </div>
  <div class="card bento-c1" style="border: 1px solid {{ $monthlyNet <= 0 ? '#86EFAC' : '#FCA5A5' }};">
    <div class="stat-icon" style="background:{{ $monthlyNet <= 0 ? '#F0FFF4' : '#FEF2F2' }};">
      {{ $monthlyNet <= 0 ? '✅' : '📈' }}
    </div>
    <div class="stat-big" style="color:{{ $monthlyNet <= 0 ? '#16A34A' : '#DC2626' }};">
      {{ $monthlyNet <= 0 ? '' : '+' }}{{ number_format($monthlyNet, 1) }}<span class="unit">kg</span>
    </div>
    <div class="stat-sub">Net CO₂ Bulan Ini</div>
  </div>
  <div class="card bento-c1">
    <div class="stat-icon" style="background:var(--primary-light);">📋</div>
    <div class="stat-big">{{ $logs->total() }}<span class="unit" style="font-size:13px;">entri</span></div>
    <div class="stat-sub">Total Catatan</div>
  </div>
</div>

{{-- Tabel Riwayat dengan Filter Kategori --}}
<div class="card">
  {{-- Filter Tabs --}}
  <div class="filter-tabs" style="flex-wrap:wrap; gap:6px; margin-bottom:16px;">
    <button class="filter-tab active" data-cat="all" id="filter-all">Semua</button>
    @foreach($categoryMeta as $key => $meta)
      <button class="filter-tab" data-cat="{{ $key }}">{{ $meta['icon'] }} {{ $meta['label'] }}</button>
    @endforeach
  </div>

  @if($logs->isEmpty())
    <div style="text-align:center; padding:48px 0;">
      <div style="font-size:48px; margin-bottom:16px; opacity:0.4;">📭</div>
      <h3 style="font-weight:700; color:var(--text-dark); margin-bottom:8px;">Belum ada catatan</h3>
      <p style="color:var(--text-light); font-size:14px;">Mulai catat aktivitas karbon kamu di halaman <a href="{{ route('user.tracking') }}" style="color:var(--primary);">Tracking Emisi</a>.</p>
    </div>
  @else
    <div class="table-wrap" style="overflow-x:auto;">
      <table style="width:100%; border-collapse:collapse;">
        <thead>
          <tr>
            <th style="text-align:left; padding:10px 12px; font-size:12px; color:var(--text-light); font-weight:700; border-bottom:1px solid var(--border);">Tanggal</th>
            <th style="text-align:left; padding:10px 12px; font-size:12px; color:var(--text-light); font-weight:700; border-bottom:1px solid var(--border);">Kategori</th>
            <th style="text-align:left; padding:10px 12px; font-size:12px; color:var(--text-light); font-weight:700; border-bottom:1px solid var(--border);">Aktivitas</th>
            <th style="text-align:right; padding:10px 12px; font-size:12px; color:var(--text-light); font-weight:700; border-bottom:1px solid var(--border);">Jumlah</th>
            <th style="text-align:right; padding:10px 12px; font-size:12px; color:var(--text-light); font-weight:700; border-bottom:1px solid var(--border);">CO₂</th>
            <th style="text-align:right; padding:10px 12px; font-size:12px; color:var(--text-light); font-weight:700; border-bottom:1px solid var(--border);">XP</th>
          </tr>
        </thead>
        <tbody>
          @foreach($logs as $log)
            @php
              $meta     = $categoryMeta[$log->category] ?? ['icon' => '📌', 'label' => $log->category, 'color' => '#888', 'bg' => '#f5f5f5'];
              $isSaving = $log->co2_saved > 0;
              $co2Val   = $isSaving ? $log->co2_saved : $log->co2_equivalent;
              $actLabel = \App\Data\ActivityCatalog::find($log->category, $log->activity_type)['label']
                          ?? ucwords(str_replace('_', ' ', $log->activity_type));
            @endphp
            <tr class="hist-row" data-cat="{{ $log->category }}"
                style="border-bottom:1px solid var(--border); transition:background 0.15s;">
              <td style="padding:12px 12px; font-size:13px; color:var(--text-light); white-space:nowrap;">
                {{ \Carbon\Carbon::parse($log->date)->translatedFormat('d M Y') }}
              </td>
              <td style="padding:12px 12px;">
                <span style="display:inline-flex; align-items:center; gap:5px; background:{{ $meta['bg'] }}; color:{{ $meta['color'] }}; border-radius:20px; padding:3px 10px; font-size:12px; font-weight:700; white-space:nowrap;">
                  {{ $meta['icon'] }} {{ $meta['label'] }}
                </span>
              </td>
              <td style="padding:12px 12px; font-size:13.5px; font-weight:600; color:var(--text-dark);">
                {{ $actLabel }}
              </td>
              <td style="padding:12px 12px; font-size:13px; text-align:right; color:var(--text-light); white-space:nowrap;">
                {{ number_format($log->amount, 2) }} {{ $log->unit }}
              </td>
              <td style="padding:12px 12px; text-align:right; font-weight:700; white-space:nowrap; font-size:13.5px; color:{{ $isSaving ? '#16A34A' : '#DC2626' }};">
                {{ $isSaving ? '−' : '+' }}{{ number_format($co2Val, 3) }} kg
                <div style="font-size:10px; font-weight:500; color:var(--text-light);">
                  {{ $isSaving ? 'hemat' : 'emisi' }}
                </div>
              </td>
              <td style="padding:12px 12px; text-align:right; font-size:13px; color:{{ $log->xp_earned > 0 ? '#16A34A' : 'var(--text-light)' }}; font-weight:{{ $log->xp_earned > 0 ? '700' : '400' }}; white-space:nowrap;">
                {{ $log->xp_earned > 0 ? '+' . $log->xp_earned . ' XP' : '—' }}
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>

    {{-- Empty state row (shown by JS when filter yields no results) --}}
    <div id="empty-filter-state" style="display:none; text-align:center; padding:40px 0;">
      <div style="font-size:40px; margin-bottom:12px; opacity:0.4;">🔍</div>
      <div style="font-weight:700; font-size:15px; color:var(--text-dark); margin-bottom:6px;">Tidak ada data pada kategori ini</div>
      <div style="font-size:13px; color:var(--text-light);">Kamu belum mencatat aktivitas karbon pada kategori yang dipilih.<br>Mulai catat di <a href="{{ route('user.tracking') }}" style="color:var(--primary); font-weight:600;">Tracking Emisi</a>.</div>
    </div>

    {{-- Compact Custom Pagination --}}
    @if($logs->lastPage() > 1)
      <div style="margin-top:18px; display:flex; align-items:center; justify-content:center; gap:6px; flex-wrap:wrap;">

        {{-- Prev --}}
        @if($logs->onFirstPage())
          <span style="padding:6px 12px; border-radius:8px; border:1px solid var(--border); color:var(--text-light); font-size:13px; cursor:not-allowed; opacity:0.45; user-select:none;">‹ Prev</span>
        @else
          <a href="{{ $logs->previousPageUrl() }}" style="padding:6px 12px; border-radius:8px; border:1px solid var(--border); color:var(--text-dark); font-size:13px; text-decoration:none; font-weight:600; transition:background 0.15s;" onmouseover="this.style.background='var(--bg)'" onmouseout="this.style.background=''">‹ Prev</a>
        @endif

        {{-- Page Numbers --}}
        @php
          $currentPage = $logs->currentPage();
          $lastPage    = $logs->lastPage();
          $window      = 2; // pages on each side of current
          $start       = max(1, $currentPage - $window);
          $end         = min($lastPage, $currentPage + $window);
        @endphp

        @if($start > 1)
          <a href="{{ $logs->url(1) }}" style="padding:6px 10px; border-radius:8px; border:1px solid var(--border); color:var(--text-dark); font-size:13px; text-decoration:none; min-width:34px; text-align:center;">1</a>
          @if($start > 2)<span style="padding:6px 4px; font-size:13px; color:var(--text-light);">…</span>@endif
        @endif

        @for($p = $start; $p <= $end; $p++)
          @if($p === $currentPage)
            <span style="padding:6px 10px; border-radius:8px; background:var(--primary); color:#fff; font-size:13px; font-weight:700; min-width:34px; text-align:center;">{{ $p }}</span>
          @else
            <a href="{{ $logs->url($p) }}" style="padding:6px 10px; border-radius:8px; border:1px solid var(--border); color:var(--text-dark); font-size:13px; text-decoration:none; min-width:34px; text-align:center;">{{ $p }}</a>
          @endif
        @endfor

        @if($end < $lastPage)
          @if($end < $lastPage - 1)<span style="padding:6px 4px; font-size:13px; color:var(--text-light);">…</span>@endif
          <a href="{{ $logs->url($lastPage) }}" style="padding:6px 10px; border-radius:8px; border:1px solid var(--border); color:var(--text-dark); font-size:13px; text-decoration:none; min-width:34px; text-align:center;">{{ $lastPage }}</a>
        @endif

        {{-- Next --}}
        @if($logs->hasMorePages())
          <a href="{{ $logs->nextPageUrl() }}" style="padding:6px 12px; border-radius:8px; border:1px solid var(--border); color:var(--text-dark); font-size:13px; text-decoration:none; font-weight:600; transition:background 0.15s;" onmouseover="this.style.background='var(--bg)'" onmouseout="this.style.background=''">Next ›</a>
        @else
          <span style="padding:6px 12px; border-radius:8px; border:1px solid var(--border); color:var(--text-light); font-size:13px; cursor:not-allowed; opacity:0.45; user-select:none;">Next ›</span>
        @endif

        <span style="font-size:12px; color:var(--text-light); margin-left:8px;">
          {{ $logs->firstItem() }}–{{ $logs->lastItem() }} dari {{ $logs->total() }} data
        </span>
      </div>
    @else
      {{-- Single page: just show count --}}
      <div style="margin-top:14px; text-align:center; font-size:12px; color:var(--text-light);">
        Menampilkan {{ $logs->total() }} data
      </div>
    @endif
  @endif
</div>

{{-- Per-Kategori Summary Bulan Ini --}}
@if($categoryTotals->isNotEmpty())
<div class="card" style="margin-top:18px;">
  <div class="card-title" style="margin-bottom:14px;">📊 Rincian per Kategori — {{ now()->translatedFormat('F Y') }}</div>
  <div style="display:flex; flex-direction:column; gap:10px;">
    @foreach($categoryMeta as $key => $meta)
      @if(isset($categoryTotals[$key]))
        @php
          $ct       = $categoryTotals[$key];
          $emitted  = round($ct->total_emitted, 2);
          $saved    = round($ct->total_saved, 2);
          $isSavingCat = $saved > 0 && $emitted == 0;
        @endphp
        <div style="display:flex; align-items:center; justify-content:space-between; padding:12px 14px; background:{{ $meta['bg'] }}; border-radius:10px; border-left:3px solid {{ $meta['color'] }};">
          <div style="display:flex; align-items:center; gap:10px;">
            <span style="font-size:18px;">{{ $meta['icon'] }}</span>
            <span style="font-weight:700; font-size:14px; color:var(--text-dark);">{{ $meta['label'] }}</span>
          </div>
          <div style="text-align:right;">
            @if($emitted > 0)
              <div style="font-weight:700; color:#DC2626; font-size:14px;">+{{ number_format($emitted, 2) }} kg CO₂</div>
              <div style="font-size:11px; color:var(--text-light);">emisi</div>
            @endif
            @if($saved > 0)
              <div style="font-weight:700; color:#16A34A; font-size:14px;">−{{ number_format($saved, 2) }} kg CO₂</div>
              <div style="font-size:11px; color:var(--text-light);">dihemat</div>
            @endif
          </div>
        </div>
      @endif
    @endforeach
  </div>
</div>
@endif

@endsection

@push('scripts')
<script>
(function () {
  const filterBtns  = document.querySelectorAll('.filter-tab[data-cat]');
  const rows        = document.querySelectorAll('.hist-row');
  const emptyState  = document.getElementById('empty-filter-state');
  const tableWrap   = document.querySelector('.table-wrap');

  function applyFilter(cat) {
    let visible = 0;
    rows.forEach(row => {
      const show = cat === 'all' || row.dataset.cat === cat;
      row.style.display = show ? '' : 'none';
      if (show) { visible++; }
    });

    // Show/hide empty state
    if (emptyState) {
      emptyState.style.display = visible === 0 ? 'block' : 'none';
    }
  }

  filterBtns.forEach(btn => {
    btn.addEventListener('click', () => {
      filterBtns.forEach(b => b.classList.remove('active'));
      btn.classList.add('active');
      applyFilter(btn.dataset.cat);
    });
  });

  // Row hover effect
  rows.forEach(row => {
    row.addEventListener('mouseenter', () => row.style.background = 'var(--bg)');
    row.addEventListener('mouseleave', () => row.style.background = '');
  });
}());
</script>
@endpush
