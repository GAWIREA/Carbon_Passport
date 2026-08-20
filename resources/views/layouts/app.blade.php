<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>@yield('title', 'EcoTrack') — Carbon Tracking & Gamification App</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('css/app.css') }}">
{{-- Chart.js — lokal agar tidak bergantung koneksi internet --}}
<script src="{{ asset('js/chart.min.js') }}"></script>
@stack('styles')
</head>
<body>
<div class="app-shell">

  @include('partials.sidebar')

  <div class="main">
    @include('partials.topbar')
    <div class="content">
      @yield('content')
    </div>
  </div>

</div>

<div class="toast" id="toast"></div>

<script>
function showToast(msg){
  const t = document.getElementById('toast');
  t.textContent = msg;
  t.classList.add('show');
  clearTimeout(window.__toastTimer);
  window.__toastTimer = setTimeout(()=>t.classList.remove('show'), 2400);
}
document.getElementById('menuToggle')?.addEventListener('click', ()=>{
  document.querySelector('.sidebar').classList.toggle('open');
});

/* Chart.js global defaults */
if (window.Chart) {
  Chart.defaults.font.family = "'Inter', sans-serif";
  Chart.defaults.color = '#64748B';
  Chart.defaults.plugins.legend.labels.usePointStyle = true;
  Chart.defaults.animation.duration = 600;
}

/* Helper: bungkus init chart dengan try-catch agar satu error tidak menghentikan chart lainnya */
function tryChart(id, config) {
  try {
    const el = document.getElementById(id);
    if (!el) { console.warn('Canvas #' + id + ' tidak ditemukan'); return null; }
    return new Chart(el, config);
  } catch(e) {
    console.error('Gagal render chart #' + id + ':', e);
    return null;
  }
}
</script>
@stack('scripts')
</body>
</html>
