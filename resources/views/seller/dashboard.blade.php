@extends('layouts.app')
@section('title','Ringkasan Penjualan')
@section('page-title','Ringkasan Penjualan')

@php
  $active = 'dashboard';

  $storeName = $user->name;

  // KPI
  $totalRedeemed   = 318;
  $revenueMonth    = 24600000;
  $ordersProcessed = 27;
  $rating          = 4.8;

  // Revenue 6 bulan (Juta Rp)
  $revenueLabels = ['Feb','Mar','Apr','Mei','Jun','Jul'];
  $revenueData   = [16.2, 19.8, 14.5, 21.4, 22.7, 24.6];
  $targetRevenue = [18, 18, 18, 20, 22, 24]; // garis target

  // Top 5 Produk terlaris (unit ditukar)
  $topProducts = [
    ['name'=>'Voucher Commuter Line 50rb','units'=>98,'color'=>'#5B8FFF'],
    ['name'=>'Diskon Servis Sepeda 30%',  'units'=>74,'color'=>'#2ECC71'],
    ['name'=>'Tumbler Stainless 500ml',   'units'=>62,'color'=>'#F5A623'],
    ['name'=>'Smart Plug Hemat Energi',   'units'=>51,'color'=>'#A78BFA'],
    ['name'=>'Bibit Pohon Mangrove',      'units'=>33,'color'=>'#F87171'],
  ];

  // Status pesanan donut
  $orderStatus = [
    ['label'=>'Selesai','value'=>181,'color'=>'#2ECC71'],
    ['label'=>'Dikirim','value'=>87, 'color'=>'#5B8FFF'],
    ['label'=>'Diproses','value'=>27,'color'=>'#F5A623'],
    ['label'=>'Dibatalkan','value'=>23,'color'=>'#F87171'],
  ];

  // Penukaran per kategori produk (grouped: poin vs rupiah)
  $categoryLabels  = ['Transportasi','Makanan','Energi','Lainnya'];
  $categoryPoints  = [172, 64, 51, 31]; // unit bayar poin
  $categoryRupiah  = [62, 41, 28, 19];  // unit bayar rupiah

  // Konversi poin bulanan (tren)
  $conversionLabels = ['Feb','Mar','Apr','Mei','Jun','Jul'];
  $conversionData   = [48, 62, 44, 78, 81, 100]; // index 100 = terbaik
@endphp

@section('content')
<div class="page-head">
  <div>
    <h1>Ringkasan Penjualan</h1>
    <p>Toko: <b>{{ $storeName }}</b></p>
  </div>
  <a href="{{ route('seller.catalog') }}" class="btn btn-primary">+ Tambah Produk</a>
</div>

{{-- KPI Row --}}
<div class="bento" style="margin-bottom:18px;">
  <div class="card bento-c1">
    <div class="card-head"><div class="stat-icon" style="background:var(--primary-light);">🎁</div><span class="stat-delta down" style="color:var(--secondary);">▲ 12%</span></div>
    <div class="stat-big">{{ number_format($totalRedeemed) }}</div><div class="stat-sub">Produk Ditukar (Total)</div>
  </div>
  <div class="card bento-c1">
    <div class="card-head"><div class="stat-icon" style="background:var(--secondary-light);">💰</div><span class="stat-delta down" style="color:var(--secondary);">▲ 8%</span></div>
    <div class="stat-big" style="font-size:20px;">Rp {{ number_format($revenueMonth/1000000,1) }}jt</div><div class="stat-sub">Pendapatan Bulan Ini</div>
  </div>
  <div class="card bento-c1">
    <div class="card-head"><div class="stat-icon" style="background:var(--warning-light);">📦</div><span class="stat-delta up">▼ 3%</span></div>
    <div class="stat-big">{{ $ordersProcessed }}</div><div class="stat-sub">Pesanan Diproses</div>
  </div>
  <div class="card bento-c1">
    <div class="card-head"><div class="stat-icon" style="background:var(--danger-light);">⭐</div><span class="stat-delta down" style="color:var(--secondary);">▲ 0.2</span></div>
    <div class="stat-big">{{ $rating }}<span class="unit">/5</span></div><div class="stat-sub">Rating Toko</div>
  </div>
</div>

<div class="bento">

  {{-- Revenue vs Target (Line Chart) --}}
  <div class="card bento-c3 bento-r2">
    <div class="card-head">
      <div>
        <div class="card-title">📈 Revenue vs Target (6 Bulan)</div>
        <p style="font-size:12.5px;color:var(--text-light);margin-top:2px;">Pendapatan aktual vs target bulanan (Juta Rp)</p>
      </div>
      <a href="{{ route('seller.orders') }}" class="card-arrow" style="text-decoration:none;">›</a>
    </div>
    <div class="chart-box"><canvas id="revenueChart"></canvas></div>
  </div>

  {{-- Status Pesanan Donut --}}
  <div class="card bento-c1 bento-r2">
    <div class="card-head">
      <div class="card-title">📦 Status Pesanan</div>
    </div>
    <div class="chart-box" style="max-height:180px;position:relative;">
      <canvas id="orderStatusDonut"></canvas>
    </div>
    <div style="display:flex;flex-direction:column;gap:8px;margin-top:14px;">
      @foreach($orderStatus as $s)
        <div style="display:flex;align-items:center;gap:8px;">
          <span style="width:10px;height:10px;border-radius:3px;background:{{ $s['color'] }};flex-shrink:0;"></span>
          <span style="font-size:13px;font-weight:600;flex:1;">{{ $s['label'] }}</span>
          <span style="font-size:13px;color:var(--text-light);">{{ $s['value'] }}</span>
        </div>
      @endforeach
    </div>
    <a href="{{ route('seller.orders') }}" class="btn btn-outline btn-sm" style="margin-top:14px;width:100%;text-align:center;justify-content:center;">Lihat Semua Pesanan</a>
  </div>

  {{-- Top 5 Produk (Horizontal Bar) --}}
  <div class="card bento-c2">
    <div class="card-head">
      <div>
        <div class="card-title">🏆 Top 5 Produk Terlaris</div>
        <p style="font-size:12.5px;color:var(--text-light);margin-top:2px;">Unit ditukar bulan ini</p>
      </div>
      <a href="{{ route('seller.catalog') }}" class="card-arrow" style="text-decoration:none;">›</a>
    </div>
    <div class="chart-box" style="min-height:200px;"><canvas id="topProductsChart"></canvas></div>
  </div>

  {{-- Penukaran per Kategori (Grouped Bar) --}}
  <div class="card bento-c2">
    <div class="card-head">
      <div>
        <div class="card-title">🗂️ Penukaran per Kategori</div>
        <p style="font-size:12.5px;color:var(--text-light);margin-top:2px;">Pembayaran via Koin vs Rupiah</p>
      </div>
      <a href="{{ route('seller.catalog') }}" class="card-arrow" style="text-decoration:none;">›</a>
    </div>
    <div class="chart-box"><canvas id="categoryGroupChart"></canvas></div>
  </div>

  {{-- Tren Konversi Koin (Area Line) --}}
  <div class="card bento-c2">
    <div class="card-head">
      <div class="card-title">📊 Tren Konversi Koin</div>
      <p style="font-size:12.5px;color:var(--text-light);margin-top:2px;">Indeks aktivitas penukaran (maks=100)</p>
    </div>
    <div class="chart-box"><canvas id="conversionChart"></canvas></div>
  </div>

  {{-- Summary card --}}
  <div class="card bento-c2">
    <div class="card-head">
      <div><span class="card-chip">Jul 2026</span><div class="card-title" style="margin-top:6px;">💡 Insight Bulan Ini</div></div>
    </div>
    <div style="display:flex;flex-direction:column;gap:12px;margin-top:8px;">
      <div style="padding:12px 14px;background:rgba(91,143,255,0.07);border-radius:10px;border-left:3px solid #5B8FFF;">
        <div style="font-size:13px;font-weight:700;color:var(--text-dark);">🚀 Voucher KRL masih jadi jagoan</div>
        <div style="font-size:12.5px;color:var(--text-light);margin-top:4px;">31% dari total penukaran berasal dari kategori Transportasi.</div>
      </div>
      <div style="padding:12px 14px;background:rgba(46,204,113,0.07);border-radius:10px;border-left:3px solid #2ECC71;">
        <div style="font-size:13px;font-weight:700;color:var(--text-dark);">📦 Stok Tumbler menipis</div>
        <div style="font-size:12.5px;color:var(--text-light);margin-top:4px;">Sisa stok 8 unit — segera restok sebelum kehabisan.</div>
      </div>
      <div style="padding:12px 14px;background:rgba(245,166,35,0.07);border-radius:10px;border-left:3px solid #F5A623;">
        <div style="font-size:13px;font-weight:700;color:var(--text-dark);">⭐ Rating naik 0.2 poin</div>
        <div style="font-size:12.5px;color:var(--text-light);margin-top:4px;">Pengguna puas dengan kecepatan pengiriman produk.</div>
      </div>
    </div>
  </div>

</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

// ---- Revenue vs Target ----
tryChart('revenueChart', {
  type: 'line',
  data: {
    labels: @json($revenueLabels),
    datasets: [
      {
        label: 'Pendapatan Aktual (Jt)',
        data: @json($revenueData),
        borderColor: '#5B8FFF', backgroundColor: 'rgba(91,143,255,0.10)',
        fill: true, tension: .4, pointBackgroundColor: '#5B8FFF',
        pointRadius: 5, pointBorderColor: '#fff', pointBorderWidth: 2, borderWidth: 3
      },
      {
        label: 'Target (Jt)',
        data: @json($targetRevenue),
        borderColor: '#2ECC71', backgroundColor: 'transparent',
        borderDash: [6,4], tension: 0, pointRadius: 0, borderWidth: 2
      }
    ]
  },
  options: {
    responsive: true, maintainAspectRatio: false,
    plugins: { legend: { position: 'bottom' } },
    scales: {
      y: { grid: { color: '#F0F3F8' }, ticks: { callback: v => 'Rp ' + v + 'jt' } },
      x: { grid: { display: false } }
    }
  }
});

// ---- Status Pesanan Donut ----
tryChart('orderStatusDonut', {
  type: 'doughnut',
  data: {
    labels: @json(array_column($orderStatus,'label')),
    datasets: [{ data: @json(array_column($orderStatus,'value')), backgroundColor: @json(array_column($orderStatus,'color')), borderWidth: 0 }]
  },
  options: { cutout: '70%', plugins: { legend: { display: false } }, responsive: true, maintainAspectRatio: false }
});

// ---- Top 5 Produk (Horizontal Bar) ----
tryChart('topProductsChart', {
  type: 'bar',
  data: {
    labels: @json(array_column($topProducts,'name')),
    datasets: [{
      data: @json(array_column($topProducts,'units')),
      backgroundColor: @json(array_column($topProducts,'color')),
      borderRadius: 6, maxBarThickness: 28
    }]
  },
  options: {
    indexAxis: 'y', responsive: true, maintainAspectRatio: false,
    plugins: { legend: { display: false } },
    scales: {
      x: { grid: { color: '#F0F3F8' }, ticks: { callback: v => v + ' unit' } },
      y: { grid: { display: false }, ticks: { font: { size: 11 } } }
    }
  }
});

// ---- Kategori Grouped Bar ----
tryChart('categoryGroupChart', {
  type: 'bar',
  data: {
    labels: @json($categoryLabels),
    datasets: [
      { label: 'Bayar Koin',   data: @json($categoryPoints), backgroundColor: '#5B8FFF', borderRadius: 5 },
      { label: 'Bayar Rupiah', data: @json($categoryRupiah), backgroundColor: '#2ECC71', borderRadius: 5 },
    ]
  },
  options: {
    responsive: true, maintainAspectRatio: false,
    plugins: { legend: { position: 'bottom' } },
    scales: {
      y: { grid: { color: '#F0F3F8' }, ticks: { callback: v => v + ' unit' } },
      x: { grid: { display: false } }
    }
  }
});

// ---- Tren Konversi ----
tryChart('conversionChart', {
  type: 'line',
  data: {
    labels: @json($conversionLabels),
    datasets: [{
      label: 'Indeks Konversi',
      data: @json($conversionData),
      borderColor: '#A78BFA', backgroundColor: 'rgba(167,139,250,0.10)',
      fill: true, tension: .4, pointBackgroundColor: '#A78BFA',
      pointRadius: 4, pointBorderColor: '#fff', pointBorderWidth: 2, borderWidth: 2.5
    }]
  },
  options: {
    responsive: true, maintainAspectRatio: false,
    plugins: { legend: { display: false } },
    scales: {
      y: { grid: { color: '#F0F3F8' }, min: 0, max: 110 },
      x: { grid: { display: false } }
    }
  }
});

}); // end DOMContentLoaded
</script>
@endpush
