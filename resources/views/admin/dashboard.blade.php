@extends('layouts.app')
@section('title','ESG & Analytics')
@section('page-title','ESG & Analytics Corporate Reporting')

@php
  $active = 'dashboard';

  // KPI
  $totalCo2Reduced    = 3.2;  // ton
  $activeEmployees    = 842;
  $participationRate  = 67;   // %
  $treesEquivalent    = 145;
  $missionCompletion  = 74;   // %

  // Tren pengurangan emisi perusahaan (ton CO₂e)
  $trendLabels = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul'];
  $trendActual = [0.8, 1.1, 1.4, 1.8, 2.3, 2.8, 3.2];
  $trendTarget = [1.0, 1.2, 1.5, 2.0, 2.5, 3.0, 3.5]; // target bulanan

  // Partisipasi per divisi (horizontal bar)
  $divisionParticipation = [
    ['label'=>'Marketing',    'value'=>82,'color'=>'#5B8FFF'],
    ['label'=>'Engineering',  'value'=>76,'color'=>'#2ECC71'],
    ['label'=>'Operations',   'value'=>68,'color'=>'#F5A623'],
    ['label'=>'Finance',      'value'=>54,'color'=>'#A78BFA'],
    ['label'=>'HR',           'value'=>91,'color'=>'#F87171'],
    ['label'=>'Legal',        'value'=>43,'color'=>'#94A3B8'],
  ];

  // Scope 1/2/3 breakdown — Stacked bar (ton CO₂e)
  $scopeLabels  = ['Feb','Mar','Apr','Mei','Jun','Jul'];
  $scope1       = [0.15, 0.18, 0.20, 0.22, 0.24, 0.26]; // Emisi langsung
  $scope2       = [0.30, 0.35, 0.38, 0.42, 0.48, 0.54]; // Energi listrik
  $scope3       = [0.65, 0.77, 0.82, 1.16, 1.58, 2.40]; // Rantai nilai (pengguna)

  // Kontribusi per divisi (donut)
  $divisions = [
    ['label'=>'Marketing','value'=>35,'color'=>'#5B8FFF'],
    ['label'=>'Engineering','value'=>27,'color'=>'#2ECC71'],
    ['label'=>'Operations','value'=>22,'color'=>'#F5A623'],
    ['label'=>'Finance','value'=>16,'color'=>'#D14E44'],
  ];

  // Mission completion rate per minggu (area)
  $missionWeeks  = ['W1 Jun','W2 Jun','W3 Jun','W4 Jun','W1 Jul','W2 Jul'];
  $missionRates  = [58, 63, 69, 71, 74, 74];

  // ESG Score bulanan
  $esgLabels = ['Feb','Mar','Apr','Mei','Jun','Jul'];
  $esgScores = [54, 58, 63, 68, 72, 76];
  $esgTarget = [60, 62, 65, 68, 72, 75];

  // Category totals
  $categoryTotals = [
    ['label'=>'Transportasi','value'=>1.6],
    ['label'=>'Makanan','value'=>0.9],
    ['label'=>'Energi & Listrik','value'=>0.7],
  ];

  // Top performers
  $topPerformers = [
    ['rank'=>1,'name'=>'Bayu Setiawan','dept'=>'Engineering','co2'=>'4.8 ton','pts'=>4210,'pct'=>100],
    ['rank'=>2,'name'=>'Citra Ayu',    'dept'=>'Operations',  'co2'=>'4.5 ton','pts'=>3985,'pct'=>95],
    ['rank'=>3,'name'=>'Zahrotuts',    'dept'=>'Marketing',   'co2'=>'4.1 ton','pts'=>3720,'pct'=>88],
    ['rank'=>4,'name'=>'Sari Dewi',    'dept'=>'HR',          'co2'=>'3.9 ton','pts'=>3450,'pct'=>82],
    ['rank'=>5,'name'=>'Rian Hidayat', 'dept'=>'Finance',     'co2'=>'3.2 ton','pts'=>2980,'pct'=>71],
  ];
@endphp

@section('content')
<div class="page-head">
  <div>
    <h1>ESG Dashboard</h1>
    <p>Ringkasan dampak lingkungan seluruh pengguna — Juli 2026</p>
  </div>
  <a href="{{ route('admin.users') }}" class="btn btn-primary">👥 Kelola Pengguna</a>
</div>

{{-- KPI Row --}}
<div class="bento" style="margin-bottom:18px;">
  <div class="card bento-c1">
    <div class="stat-icon" style="background:var(--secondary-light);">🌍</div>
    <div class="stat-big">{{ $totalCo2Reduced }}<span class="unit">ton</span></div>
    <div class="stat-sub">Total CO₂e Dikurangi</div>
    <div class="stat-delta down" style="color:#2ECC71;font-size:12px;margin-top:6px;">▲ 14% vs bulan lalu</div>
  </div>
  <div class="card bento-c1">
    <div class="stat-icon" style="background:var(--primary-light);">👥</div>
    <div class="stat-big">142<span class="unit">orang</span></div>
    <div class="stat-sub">Pengguna Aktif</div>
    <div class="stat-delta down" style="color:#2ECC71;font-size:12px;margin-top:6px;">▲ 23 bergabung</div>
  </div>
  <div class="card bento-c1">
    <div class="stat-icon" style="background:var(--warning-light);">🎯</div>
    <div class="stat-big">{{ $participationRate }}<span class="unit">%</span></div>
    <div class="stat-sub">Partisipasi Misi Mingguan</div>
    <div style="margin-top:8px;">
      <div class="progress-track"><div class="progress-fill" style="width:{{ $participationRate }}%;background:#F5A623;"></div></div>
    </div>
  </div>
  <div class="card bento-c1">
    <div class="stat-icon" style="background:rgba(167,139,250,0.15);">📋</div>
    <div class="stat-big">{{ $missionCompletion }}<span class="unit">%</span></div>
    <div class="stat-sub">Misi Diselesaikan</div>
    <div style="margin-top:8px;">
      <div class="progress-track"><div class="progress-fill" style="width:{{ $missionCompletion }}%;background:#A78BFA;"></div></div>
    </div>
  </div>
  <div class="card bento-c1">
    <div class="stat-icon" style="background:var(--secondary-light);">🌳</div>
    <div class="stat-big">≈{{ $treesEquivalent }}</div>
    <div class="stat-sub">Setara Pohon Ditanam</div>
    <div class="stat-delta down" style="color:#2ECC71;font-size:12px;margin-top:6px;">berdasarkan CO₂ dikurangi</div>
  </div>
</div>

<div class="bento">

  {{-- ESG Score Trend (Line + Target) --}}
  <div class="card bento-c3 bento-r2">
    <div class="card-head">
      <div>
        <div class="card-title">📊 ESG Score Bulanan</div>
        <p style="font-size:12.5px;color:var(--text-light);margin-top:2px;">Skor 0–100 · Aktual vs Target perusahaan</p>
      </div>
      <span class="card-chip badge-green">Skor: 76</span>
    </div>
    <div class="chart-box"><canvas id="esgChart"></canvas></div>
  </div>

  {{-- Scope 1/2/3 Stacked Bar --}}
  <div class="card bento-c1 bento-r2">
    <div class="card-head">
      <div class="card-title">🏭 Emisi Scope 1/2/3</div>
      <p style="font-size:12.5px;color:var(--text-light);margin-top:2px;">Ton CO₂e per bulan</p>
    </div>
    <div class="chart-box"><canvas id="scopeChart"></canvas></div>
    <div style="display:flex;flex-direction:column;gap:6px;margin-top:14px;">
      <div style="display:flex;align-items:center;gap:8px;font-size:12px;">
        <span style="width:10px;height:10px;border-radius:3px;background:#5B8FFF;flex-shrink:0;"></span>
        <span style="flex:1;">Scope 1 — Emisi Langsung</span>
      </div>
      <div style="display:flex;align-items:center;gap:8px;font-size:12px;">
        <span style="width:10px;height:10px;border-radius:3px;background:#2ECC71;flex-shrink:0;"></span>
        <span style="flex:1;">Scope 2 — Energi Listrik</span>
      </div>
      <div style="display:flex;align-items:center;gap:10px;">
        <span style="width:12px;height:12px;border-radius:3px;background:#F5A623;"></span>
        <span style="flex:1;">Scope 3 — Rantai Nilai Pengguna</span>
      </div>
    </div>
  </div>

  {{-- Partisipasi per Divisi (Horizontal Bar) --}}
  <div class="card bento-c2">
    <div class="card-head">
      <div>
        <div class="card-title">📈 Partisipasi Program ESG</div>
        <p style="font-size:12.5px;color:var(--text-light);margin-top:2px;">% pengguna aktif input emisi minggu ini</p>
      </div>
      <a href="{{ route('admin.users') }}" class="card-arrow" style="text-decoration:none;">›</a>
    </div>
    <div class="chart-box" style="min-height:220px;"><canvas id="divisionChart"></canvas></div>
  </div>

  {{-- Mission Completion Rate (Area) --}}
  <div class="card bento-c2">
    <div class="card-head">
      <div>
        <div class="card-title">🎯 Rasio Sukses Misi</div>
        <p style="font-size:12.5px;color:var(--text-light);margin-top:2px;">% misi yang berhasil diselesaikan pengguna</p>
      </div>
    </div>
    <div class="chart-box"><canvas id="missionChart"></canvas></div>
  </div>

  {{-- Top Performers Table --}}
  <div class="card bento-c2">
    <div class="card-head">
      <div>
        <div class="card-title">🏆 Top 5 Pengguna Terbaik</div>
        <p style="font-size:12.5px;color:var(--text-light);margin-top:2px;">Berdasarkan poin & CO₂ dikurangi bulan ini</p>
      </div>
      <a href="{{ route('admin.users') }}" class="card-arrow" style="text-decoration:none;">›</a>
    </div>
    @foreach($topPerformers as $p)
      <div style="display:flex;align-items:center;gap:10px;padding:10px 0;border-bottom:1px solid var(--border);">
        <div style="width:24px;text-align:center;font-size:16px;font-weight:800;color:{{ $p['rank']===1?'#F5A623':($p['rank']===2?'#94A3B8':($p['rank']===3?'#CD7F32':'var(--text-light)')) }};">
          {{ $p['rank'] }}
        </div>
        <div style="flex:1;min-width:0;">
          <div style="font-size:13px;font-weight:700;color:var(--text-dark);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $p['name'] }}</div>
          <div style="font-size:11px;color:var(--text-light);">{{ $p['dept'] }} · {{ $p['co2'] }}</div>
          <div class="progress-track" style="margin-top:5px;height:4px;">
            <div class="progress-fill" style="width:{{ $p['pct'] }}%;background:#5B8FFF;"></div>
          </div>
        </div>
        <div style="text-align:right;flex-shrink:0;">
          <div style="font-size:13px;font-weight:800;color:var(--primary);">{{ number_format($p['pts'],0,',','.') }}</div>
          <div style="font-size:10.5px;color:var(--text-light);">poin</div>
        </div>
      </div>
    @endforeach
    <a href="{{ route('admin.users') }}" class="btn btn-outline btn-sm" style="margin-top:12px;width:100%;text-align:center;justify-content:center;">Lihat Semua Pengguna</a>
  </div>

  {{-- Kontribusi per Divisi Donut --}}
  <div class="card bento-c1 bento-r2">
    <div class="card-head">
      <div class="card-title">🏢 Kontribusi Divisi</div>
    </div>
    <div class="chart-box" style="max-height:180px;"><canvas id="divisionDonut"></canvas></div>
    <div style="display:flex;flex-direction:column;gap:8px;margin-top:14px;">
      @foreach($divisions as $d)
        <div style="display:flex;align-items:center;gap:9px;">
          <span style="width:10px;height:10px;border-radius:4px;background:{{ $d['color'] }};"></span>
          <span style="font-size:13px;font-weight:600;flex:1;">{{ $d['label'] }}</span>
          <span style="font-size:12.5px;color:var(--text-light);">{{ $d['value'] }}%</span>
        </div>
      @endforeach
    </div>
  </div>

  {{-- Category totals (horizontal bar) --}}
  <div class="card bento-c3">
    <div class="card-head">
      <div class="card-title">🌐 Pengurangan Emisi per Kategori</div>
      <p style="font-size:12.5px;color:var(--text-light);margin-top:2px;">Total ton CO₂e dikurangi perusahaan — Jul 2026</p>
    </div>
    <div class="chart-box" style="min-height:140px;"><canvas id="categoryBar"></canvas></div>
  </div>

</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

// ---- ESG Score ----
tryChart('esgChart', {
  type: 'line',
  data: {
    labels: @json($esgLabels),
    datasets: [
      {
        label: 'ESG Score Aktual',
        data: @json($esgScores),
        borderColor: '#2ECC71', backgroundColor: 'rgba(46,204,113,0.10)',
        fill: true, tension: .4, pointBackgroundColor: '#2ECC71',
        pointRadius: 5, pointBorderColor: '#fff', pointBorderWidth: 2, borderWidth: 3
      },
      {
        label: 'Target',
        data: @json($esgTarget),
        borderColor: '#5B8FFF', backgroundColor: 'transparent',
        borderDash: [6,4], tension: 0, pointRadius: 0, borderWidth: 2
      }
    ]
  },
  options: {
    responsive: true, maintainAspectRatio: false,
    plugins: { legend: { position: 'bottom' } },
    scales: {
      y: { grid: { color: '#F0F3F8' }, min: 40, max: 100, ticks: { callback: v => v + ' pt' } },
      x: { grid: { display: false } }
    }
  }
});

// ---- Scope 1/2/3 Stacked Bar ----
tryChart('scopeChart', {
  type: 'bar',
  data: {
    labels: @json($scopeLabels),
    datasets: [
      { label: 'Scope 1', data: @json($scope1), backgroundColor: '#5B8FFF', borderRadius: 0, stack: 'scope' },
      { label: 'Scope 2', data: @json($scope2), backgroundColor: '#2ECC71', stack: 'scope' },
      { label: 'Scope 3', data: @json($scope3), backgroundColor: '#F5A623', borderRadius: 4, stack: 'scope' },
    ]
  },
  options: {
    responsive: true, maintainAspectRatio: false,
    plugins: { legend: { display: false } },
    scales: {
      y: { stacked: true, grid: { color: '#F0F3F8' }, ticks: { callback: v => v + ' ton' } },
      x: { stacked: true, grid: { display: false } }
    }
  }
});

// ---- Partisipasi per Divisi (Horizontal Bar) ----
tryChart('divisionChart', {
  type: 'bar',
  data: {
    labels: @json(array_column($divisionParticipation,'label')),
    datasets: [{
      data: @json(array_column($divisionParticipation,'value')),
      backgroundColor: @json(array_column($divisionParticipation,'color')),
      borderRadius: 6, maxBarThickness: 22
    }]
  },
  options: {
    indexAxis: 'y', responsive: true, maintainAspectRatio: false,
    plugins: { legend: { display: false } },
    scales: {
      x: { grid: { color: '#F0F3F8' }, min: 0, max: 100, ticks: { callback: v => v + '%' } },
      y: { grid: { display: false } }
    }
  }
});

// ---- Mission Completion Rate ----
tryChart('missionChart', {
  type: 'line',
  data: {
    labels: @json($missionWeeks),
    datasets: [{
      label: '% Selesai',
      data: @json($missionRates),
      borderColor: '#A78BFA', backgroundColor: 'rgba(167,139,250,0.12)',
      fill: true, tension: .4, pointBackgroundColor: '#A78BFA',
      pointRadius: 4, pointBorderColor: '#fff', pointBorderWidth: 2, borderWidth: 2.5
    }]
  },
  options: {
    responsive: true, maintainAspectRatio: false,
    plugins: { legend: { display: false } },
    scales: {
      y: { grid: { color: '#F0F3F8' }, min: 40, max: 100, ticks: { callback: v => v + '%' } },
      x: { grid: { display: false } }
    }
  }
});

// ---- Divisi Donut ----
tryChart('divisionDonut', {
  type: 'doughnut',
  data: {
    labels: @json(array_column($divisions,'label')),
    datasets: [{ data: @json(array_column($divisions,'value')), backgroundColor: @json(array_column($divisions,'color')), borderWidth: 0 }]
  },
  options: { cutout: '68%', plugins: { legend: { display: false } }, responsive: true, maintainAspectRatio: false }
});

// ---- Category Totals (Horizontal Bar) ----
tryChart('categoryBar', {
  type: 'bar',
  data: {
    labels: @json(array_column($categoryTotals,'label')),
    datasets: [{
      data: @json(array_column($categoryTotals,'value')),
      backgroundColor: ['#5B8FFF','#2ECC71','#F5A623'],
      borderRadius: 8, maxBarThickness: 60
    }]
  },
  options: {
    indexAxis: 'y', responsive: true, maintainAspectRatio: false,
    plugins: { legend: { display: false } },
    scales: {
      x: { grid: { color: '#F0F3F8' }, ticks: { callback: v => v + ' ton' } },
      y: { grid: { display: false } }
    }
  }
});

}); // end DOMContentLoaded
</script>
@endpush
