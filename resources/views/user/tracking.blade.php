@extends('layouts.app')
@section('title','Tracking Emisi')
@section('page-title','Tracking Emisi')

@php $active = 'tracking'; @endphp

@section('content')
<div class="page-head">
  <div>
    <h1>Tracking Emisi</h1>
    <p>Catat aktivitas karbon harian kamu — emisi maupun penghematan.</p>
  </div>
</div>

{{-- XP Status Banner --}}
@php
  $xpRemaining = max(0, $maxXpInputs - $todayInputCount);
@endphp
<div id="xp-status-banner" class="card" style="margin-bottom:18px; padding:14px 18px; display:flex; align-items:center; gap:12px; background:{{ $xpRemaining > 0 ? 'var(--secondary-light)' : 'var(--bg)' }}; border:1px solid {{ $xpRemaining > 0 ? 'var(--primary)' : 'var(--border)' }};">
  <span style="font-size:22px;">{{ $xpRemaining > 0 ? '⭐' : '✅' }}</span>
  <div>
    @if($xpRemaining > 0)
      <div style="font-weight:700; font-size:14px; color:var(--primary-dark);">XP Hari Ini: <strong>{{ $todayInputCount }}/{{ $maxXpInputs }}</strong> input sudah dapat XP</div>
      <div style="font-size:12px; color:var(--text-light);">Kamu masih bisa mendapatkan XP dari <strong>{{ $xpRemaining }}</strong> input berikutnya hari ini.</div>
    @else
      <div style="font-weight:700; font-size:14px; color:var(--text-dark);">XP hari ini sudah penuh ({{ $maxXpInputs }}/{{ $maxXpInputs }})</div>
      <div style="font-size:12px; color:var(--text-light);">Aktivitas tetap dicatat, tapi tidak ada XP tambahan hingga esok hari.</div>
    @endif
  </div>
</div>

{{-- Sinkronisasi Otomatis --}}
<div class="card" style="margin-bottom:18px;">
  <div class="card-title" style="margin-bottom:14px;">🔗 Sinkronisasi Otomatis</div>
  @foreach([
    ['icon'=>'🏃','name'=>'Strava','sub'=>'Lacak aktivitas jalan/lari/sepeda otomatis','status'=>false],
    ['icon'=>'⌚','name'=>'Garmin','sub'=>'Sinkronisasi data perjalanan dari smartwatch','status'=>false],
    ['icon'=>'🗺️','name'=>'Google Maps Timeline','sub'=>'Deteksi moda transportasi harian','status'=>true],
  ] as $s)
    <div style="display:flex; align-items:center; justify-content:space-between; padding:12px 14px; border:1px solid var(--border); border-radius:12px; margin-bottom:10px;">
      <div style="display:flex; align-items:center; gap:12px;">
        <div style="width:36px;height:36px;border-radius:9px;display:flex;align-items:center;justify-content:center;font-size:17px;background:var(--bg);">{{ $s['icon'] }}</div>
        <div><div style="font-weight:600; font-size:14px;">{{ $s['name'] }}</div><div style="font-size:12px; color:var(--text-light);">{{ $s['sub'] }}</div></div>
      </div>
      @if($s['status'])
        <span class="badge badge-green">✓ Terhubung</span>
      @else
        <button class="btn btn-outline btn-sm sync-btn">Hubungkan</button>
      @endif
    </div>
  @endforeach
</div>

{{-- Input Manual --}}
<div class="card">
  <div class="card-title" style="margin-bottom:14px;">✍️ Input Manual</div>
  <div class="filter-tabs">
    <button class="filter-tab active" data-tab="tab-manual">Input Angka</button>
    <button class="filter-tab" data-tab="tab-scan">Guided AI Scan</button>
  </div>

  <div class="tab-panel" id="tab-manual">
    @if($errors->any())
      <div style="background:#FEF2F2; border:1px solid #FCA5A5; border-radius:10px; padding:12px 16px; margin-bottom:16px; font-size:13px; color:#B91C1C;">
        <strong>⚠️ Terjadi kesalahan:</strong>
        <ul style="margin:6px 0 0 16px; padding:0;">
          @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    @if(session('error'))
      <div style="background:#FEF2F2; border:1px solid #FCA5A5; border-radius:10px; padding:12px 16px; margin-bottom:16px; font-size:13px; color:#B91C1C;">
        ⚠️ {{ session('error') }}
      </div>
    @endif

    <form action="{{ route('user.tracking.store') }}" method="POST" id="trackingForm">
      @csrf
      <div class="bento" style="grid-template-columns:repeat(2,1fr); gap:14px; margin-bottom:16px;">

        {{-- Kategori --}}
        <div class="form-row">
          <label class="field-label" for="input-category">Kategori</label>
          <select name="category" id="input-category" class="input" required>
            <option value="">— Pilih Kategori —</option>
            @foreach($categoryLabels as $key => $label)
              <option value="{{ $key }}" {{ old('category') === $key ? 'selected' : '' }}>
                {{ $label }}
              </option>
            @endforeach
          </select>
        </div>

        {{-- Aktivitas — diisi ulang oleh JS --}}
        <div class="form-row">
          <label class="field-label" for="input-activity">Jenis Aktivitas</label>
          <select name="activity_type" id="input-activity" class="input" required disabled>
            <option value="">— Pilih Kategori dulu —</option>
          </select>
        </div>

        {{-- Jumlah --}}
        <div class="form-row">
          <label class="field-label" for="input-amount">Jumlah</label>
          <input
            name="amount"
            id="input-amount"
            class="input"
            type="number"
            step="0.01"
            min="0.01"
            placeholder="Contoh: 10"
            value="{{ old('amount') }}"
            required
          >
        </div>

        {{-- Satuan (read-only, diisi JS) --}}
        <div class="form-row">
          <label class="field-label" for="input-unit">Satuan</label>
          <input
            id="input-unit"
            class="input"
            type="text"
            placeholder="Pilih aktivitas dulu"
            readonly
            style="background:var(--bg); color:var(--text-light); cursor:not-allowed;"
          >
        </div>

        {{-- Tanggal --}}
        <div class="form-row" style="grid-column: span 2;">
          <label class="field-label">Tanggal</label>
          <input class="input" type="date" value="{{ date('Y-m-d') }}" readonly style="background:var(--bg); color:var(--text-light); cursor:not-allowed; max-width:200px;">
        </div>

      </div>

      {{-- Preview Panel (real-time) --}}
      <div id="preview-panel" style="display:none; background:var(--bg); border-radius:14px; padding:18px 20px; margin-bottom:18px; border:1px solid var(--border);">
        <div style="font-size:12px; font-weight:700; text-transform:uppercase; letter-spacing:0.08em; color:var(--text-light); margin-bottom:12px;">
          Prediksi Dampak
        </div>
        <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px;">

          {{-- CO₂ --}}
          <div id="preview-co2-card" style="border-radius:10px; padding:14px; text-align:center; background:var(--card-bg);">
            <div id="preview-co2-icon" style="font-size:20px; margin-bottom:6px;"></div>
            <div id="preview-co2-value" style="font-size:22px; font-weight:800; margin-bottom:4px; line-height:1.1;"></div>
            <div id="preview-co2-label" style="font-size:11px; color:var(--text-light); font-weight:600;"></div>
          </div>

          {{-- XP --}}
          <div id="preview-xp-card" style="background:linear-gradient(135deg,#F0FFF4,#DCFCE7); border-radius:10px; padding:14px; text-align:center;">
            <div style="font-size:20px; margin-bottom:6px;">⭐</div>
            <div id="preview-xp-value" style="font-size:22px; font-weight:800; color:#16A34A; margin-bottom:4px; line-height:1.1;"></div>
            <div id="preview-xp-label" style="font-size:11px; color:#166534; font-weight:600;"></div>
          </div>

        </div>
        <div id="preview-xp-cap-notice" style="display:none; margin-top:12px; font-size:12px; color:var(--text-light); text-align:center; background:#FFF7ED; border-radius:8px; padding:8px 12px; border:1px solid #FED7AA;">
          ℹ️ XP hari ini sudah penuh ({{ $maxXpInputs }}/{{ $maxXpInputs }}). CO₂ tetap dicatat tanpa XP.
        </div>
      </div>

      <button type="submit" class="btn btn-primary" id="submit-btn" style="margin-top:4px;" disabled>
        Simpan Catatan
      </button>
    </form>
  </div>

  <div class="tab-panel" id="tab-scan" style="display:none;">
    <div class="scan-box" id="scanBox">
      <div style="font-size:34px; margin-bottom:10px;">📷</div>
      <div style="font-weight:700; margin-bottom:4px;">Posisikan Struk Bensin atau Tiket Perjalanan Anda di dalam Kotak</div>
      <div style="font-size:13px; color:var(--text-light);">Klik untuk simulasi upload foto struk</div>
    </div>
    <div class="scan-result" id="scanResult" style="display:none; margin-top:16px; padding:16px; background:var(--secondary-light); border-radius:12px; align-items:center; gap:14px;">
      <span style="font-size:26px;">🧾</span>
      <div>
        <div style="font-weight:700; font-size:13.5px;">Struk SPBU Pertamina terdeteksi</div>
        <div style="font-size:12.5px; color:var(--text-light); margin-top:2px;">Pertalite 8.2 Liter · Estimasi emisi <strong style="color:var(--text-dark)">6.1 kg CO₂e</strong></div>
        <button class="btn btn-secondary btn-sm" style="margin-top:8px;" onclick="showToast('Ditambahkan ke catatan emisi.')">+ Tambahkan ke Catatan</button>
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
(function () {
  // ── Catalog & constants injected from server ──────────────────────
  const CATALOG       = @json($activityCatalog);
  const XP_PER_KG     = {{ $xpPerKgCo2 }};
  const LOG_XP        = {{ $logXp }};
  const MAX_XP_INPUTS = {{ $maxXpInputs }};
  const TODAY_INPUTS  = {{ $todayInputCount }};

  // ── DOM refs ──────────────────────────────────────────────────────
  const catSel        = document.getElementById('input-category');
  const actSel        = document.getElementById('input-activity');
  const amountInput   = document.getElementById('input-amount');
  const unitInput     = document.getElementById('input-unit');
  const previewPanel  = document.getElementById('preview-panel');
  const previewCo2Val = document.getElementById('preview-co2-value');
  const previewCo2Lbl = document.getElementById('preview-co2-label');
  const previewCo2Ico = document.getElementById('preview-co2-icon');
  const previewXpVal  = document.getElementById('preview-xp-value');
  const previewXpLbl  = document.getElementById('preview-xp-label');
  const xpCapNotice   = document.getElementById('preview-xp-cap-notice');
  const submitBtn     = document.getElementById('submit-btn');

  // ── Restore old input after validation error ──────────────────────
  const oldCategory = "{{ old('category', '') }}";
  const oldActivity = "{{ old('activity_type', '') }}";

  // ── Category change → repopulate activities ───────────────────────
  catSel.addEventListener('change', function () {
    const cat = this.value;
    actSel.innerHTML = '';

    if (!cat || !CATALOG[cat]) {
      actSel.add(new Option('— Pilih Kategori dulu —', ''));
      actSel.disabled = true;
      resetPreview();
      submitBtn.disabled = true;
      return;
    }

    actSel.add(new Option('— Pilih Aktivitas —', ''));
    // STRICT: only activities that belong to THIS category
    Object.entries(CATALOG[cat]).forEach(([key, def]) => {
      actSel.add(new Option(def.label, key));
    });
    actSel.disabled = false;
    resetPreview();
    submitBtn.disabled = true;
  });

  // ── Activity change → update unit, update preview ─────────────────
  actSel.addEventListener('change', updateUnitAndPreview);
  amountInput.addEventListener('input', updatePreview);

  function updateUnitAndPreview() {
    const def = getSelectedDef();
    if (def) {
      unitInput.value = def.unit;
    } else {
      unitInput.value = '';
    }
    updatePreview();
  }

  function updatePreview() {
    const def    = getSelectedDef();
    const amount = parseFloat(amountInput.value);

    if (!def || !amount || amount <= 0) {
      resetPreview();
      submitBtn.disabled = !def || !amount || amount <= 0 ? true : false;
      return;
    }

    const co2    = parseFloat((amount * def.co2_per_unit).toFixed(3));
    const isSaving = def.type === 'saving';

    // XP calculation
    let xp = 0;
    const xpCapped = TODAY_INPUTS >= MAX_XP_INPUTS;
    if (!xpCapped) {
      if (isSaving) {
        xp = Math.min(100, Math.max(5, Math.round(co2 * XP_PER_KG)));
      } else {
        xp = LOG_XP;
      }
    }

    // CO₂ card
    if (isSaving) {
      previewCo2Ico.textContent     = '🌿';
      previewCo2Val.textContent     = '−' + co2.toLocaleString('id-ID') + ' kg';
      previewCo2Val.style.color     = '#16A34A';
      previewCo2Lbl.textContent     = 'CO₂ Dihemat';
    } else {
      previewCo2Ico.textContent     = '⚠️';
      previewCo2Val.textContent     = '+' + co2.toLocaleString('id-ID') + ' kg';
      previewCo2Val.style.color     = '#DC2626';
      previewCo2Lbl.textContent     = 'CO₂ Dihasilkan';
    }

    // XP card
    previewXpVal.textContent = xpCapped ? '0 XP' : '+' + xp + ' XP';
    previewXpLbl.textContent = xpCapped
      ? 'XP hari ini penuh'
      : (isSaving ? 'XP Aktivitas Hijau' : 'XP Logging Data');

    xpCapNotice.style.display = xpCapped ? 'block' : 'none';
    previewPanel.style.display = 'block';
    submitBtn.disabled = false;
  }

  function getSelectedDef() {
    const cat = catSel.value;
    const act = actSel.value;
    if (!cat || !act || !CATALOG[cat] || !CATALOG[cat][act]) return null;
    return CATALOG[cat][act];
  }

  function resetPreview() {
    previewPanel.style.display = 'none';
    xpCapNotice.style.display  = 'none';
  }

  // ── Restore old values (after validation failure) ─────────────────
  if (oldCategory) {
    catSel.value = oldCategory;
    catSel.dispatchEvent(new Event('change'));
    if (oldActivity) {
      // Wait for activity list to be populated
      setTimeout(() => {
        actSel.value = oldActivity;
        actSel.dispatchEvent(new Event('change'));
      }, 0);
    }
  }

  // ── Tab switching ─────────────────────────────────────────────────
  document.querySelectorAll('.filter-tab[data-tab]').forEach(tab => {
    tab.addEventListener('click', () => {
      document.querySelectorAll('.filter-tab[data-tab]').forEach(t => t.classList.remove('active'));
      document.querySelectorAll('.tab-panel').forEach(p => p.style.display = 'none');
      tab.classList.add('active');
      document.getElementById(tab.dataset.tab).style.display = 'block';
    });
  });

  // ── Sync buttons (UI only) ────────────────────────────────────────
  document.querySelectorAll('.sync-btn').forEach(b => b.addEventListener('click', function () {
    this.innerHTML = '<span class="spinner"></span>';
    setTimeout(() => {
      this.outerHTML = '<span class="badge badge-green">✓ Terhubung</span>';
      showToast('Sinkronisasi berhasil.');
    }, 1200);
  }));

  // ── AI Scan (demo) ────────────────────────────────────────────────
  document.getElementById('scanBox').addEventListener('click', function () {
    const box = this, result = document.getElementById('scanResult');
    box.innerHTML = '<span class="spinner"></span> <span style="margin-left:8px; font-weight:600; color:var(--primary-dark);">Menganalisis struk dengan AI OCR...</span>';
    setTimeout(() => {
      result.style.display = 'flex';
      box.innerHTML = '<div style="font-size:34px;">✅</div><div style="font-weight:700;">Scan Berhasil!</div><div style="font-size:13px; color:var(--text-light);">Klik untuk memindai struk lain</div>';
    }, 1600);
  });
}());
</script>
@endpush
