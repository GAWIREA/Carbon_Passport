<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Masuk — EcoTrack</title>
<meta name="description" content="Masuk ke EcoTrack — platform pelacakan jejak karbon dan gamifikasi hijau.">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('css/app.css') }}">
<style>
  :root {
    --login-bg: linear-gradient(135deg, #0f172a 0%, #1a2744 50%, #0d2b1e 100%);
    --card-glass: rgba(255,255,255,0.06);
    --card-border: rgba(255,255,255,0.12);
    --input-bg: rgba(255,255,255,0.08);
    --input-border: rgba(255,255,255,0.15);
    --input-focus: rgba(91,143,255,0.5);
    --text-white: #f1f5f9;
    --text-muted: #94a3b8;
    --accent: #5B8FFF;
    --accent-hover: #4477ee;
    --green: #2ECC71;
  }

  *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

  body {
    font-family: 'Inter', sans-serif;
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    background: var(--login-bg);
    position: relative;
    overflow: hidden;
  }

  /* Animated background orbs */
  body::before {
    content: '';
    position: fixed;
    width: 600px; height: 600px;
    background: radial-gradient(circle, rgba(91,143,255,0.15) 0%, transparent 70%);
    top: -200px; left: -200px;
    border-radius: 50%;
    animation: orb1 8s ease-in-out infinite alternate;
  }
  body::after {
    content: '';
    position: fixed;
    width: 500px; height: 500px;
    background: radial-gradient(circle, rgba(46,204,113,0.12) 0%, transparent 70%);
    bottom: -150px; right: -150px;
    border-radius: 50%;
    animation: orb2 10s ease-in-out infinite alternate;
  }
  @keyframes orb1 { from { transform: translate(0,0) scale(1); } to { transform: translate(60px,40px) scale(1.1); } }
  @keyframes orb2 { from { transform: translate(0,0) scale(1); } to { transform: translate(-50px,-30px) scale(1.08); } }

  .login-wrapper {
    position: relative;
    z-index: 1;
    width: 100%;
    max-width: 460px;
    padding: 20px;
    animation: fadeUp 0.6s cubic-bezier(0.16,1,0.3,1);
  }
  @keyframes fadeUp { from { opacity:0; transform:translateY(24px); } to { opacity:1; transform:translateY(0); } }

  /* Brand */
  .brand-header {
    text-align: center;
    margin-bottom: 32px;
  }
  .brand-icon {
    width: 64px; height: 64px;
    background: linear-gradient(135deg, #5B8FFF, #2ECC71);
    border-radius: 20px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 28px;
    margin-bottom: 16px;
    box-shadow: 0 8px 32px rgba(91,143,255,0.4);
  }
  .brand-name {
    font-family: 'Plus Jakarta Sans', sans-serif;
    font-size: 28px;
    font-weight: 800;
    color: var(--text-white);
    letter-spacing: -0.5px;
  }
  .brand-tagline {
    font-size: 14px;
    color: var(--text-muted);
    margin-top: 4px;
  }

  /* Card */
  .login-card {
    background: var(--card-glass);
    border: 1px solid var(--card-border);
    border-radius: 24px;
    padding: 36px;
    backdrop-filter: blur(20px);
    -webkit-backdrop-filter: blur(20px);
    box-shadow: 0 24px 60px rgba(0,0,0,0.4);
  }

  .card-title {
    font-family: 'Plus Jakarta Sans', sans-serif;
    font-size: 20px;
    font-weight: 700;
    color: var(--text-white);
    margin-bottom: 6px;
  }
  .card-sub {
    font-size: 13.5px;
    color: var(--text-muted);
    margin-bottom: 28px;
  }

  /* Form */
  .form-group {
    margin-bottom: 18px;
  }
  .form-label {
    display: block;
    font-size: 13px;
    font-weight: 600;
    color: var(--text-muted);
    margin-bottom: 8px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
  }
  .form-input {
    width: 100%;
    padding: 13px 16px;
    background: var(--input-bg);
    border: 1px solid var(--input-border);
    border-radius: 12px;
    color: var(--text-white);
    font-size: 15px;
    font-family: 'Inter', sans-serif;
    transition: border-color 0.2s, box-shadow 0.2s;
    outline: none;
  }
  .form-input::placeholder { color: rgba(148,163,184,0.5); }
  .form-input:focus {
    border-color: var(--accent);
    box-shadow: 0 0 0 3px var(--input-focus);
  }
  .form-input.is-invalid { border-color: #f87171; }

  .error-msg {
    font-size: 12.5px;
    color: #f87171;
    margin-top: 6px;
    display: flex;
    align-items: center;
    gap: 5px;
  }

  /* Remember me row */
  .form-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 24px;
  }
  .checkbox-label {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 13.5px;
    color: var(--text-muted);
    cursor: pointer;
  }
  .checkbox-label input[type="checkbox"] {
    width: 16px; height: 16px;
    accent-color: var(--accent);
    cursor: pointer;
  }

  /* Submit button */
  .btn-login {
    width: 100%;
    padding: 14px;
    background: linear-gradient(135deg, #5B8FFF, #4477ee);
    color: #fff;
    border: none;
    border-radius: 12px;
    font-family: 'Plus Jakarta Sans', sans-serif;
    font-size: 15px;
    font-weight: 700;
    cursor: pointer;
    transition: transform 0.15s, box-shadow 0.15s;
    box-shadow: 0 4px 20px rgba(91,143,255,0.4);
    position: relative;
    overflow: hidden;
  }
  .btn-login:hover {
    transform: translateY(-1px);
    box-shadow: 0 8px 28px rgba(91,143,255,0.5);
  }
  .btn-login:active { transform: translateY(0); }

  /* Demo accounts */
  .demo-section {
    margin-top: 28px;
    padding-top: 24px;
    border-top: 1px solid var(--card-border);
  }
  .demo-title {
    font-size: 12px;
    font-weight: 600;
    color: var(--text-muted);
    text-transform: uppercase;
    letter-spacing: 0.8px;
    margin-bottom: 12px;
    text-align: center;
  }
  .demo-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 8px;
  }
  .demo-btn {
    padding: 10px 8px;
    background: rgba(255,255,255,0.05);
    border: 1px solid var(--card-border);
    border-radius: 10px;
    text-align: center;
    cursor: pointer;
    transition: background 0.15s, border-color 0.15s, transform 0.1s;
    text-decoration: none;
  }
  .demo-btn:hover {
    background: rgba(91,143,255,0.12);
    border-color: rgba(91,143,255,0.4);
    transform: translateY(-1px);
  }
  .demo-btn:active { transform: translateY(0); }
  .demo-role {
    font-size: 18px;
    margin-bottom: 3px;
  }
  .demo-label {
    font-size: 11px;
    font-weight: 600;
    color: var(--text-white);
    display: block;
  }
  .demo-email {
    font-size: 10px;
    color: var(--text-muted);
    display: block;
    margin-top: 2px;
    word-break: break-all;
  }
</style>
</head>
<body>

<div class="login-wrapper">
  <div class="brand-header">
    <div class="brand-icon">🌱</div>
    <div class="brand-name">EcoTrack</div>
    <div class="brand-tagline">Carbon Tracking &amp; Gamification Platform</div>
  </div>

  <div class="login-card">
    <div class="card-title">Selamat datang kembali 👋</div>
    <div class="card-sub">Masuk untuk melacak jejak karbon Anda</div>

    <form method="POST" action="{{ route('login') }}" id="login-form">
      @csrf

      <div class="form-group">
        <label class="form-label" for="email">Email</label>
        <input
          id="email"
          type="email"
          name="email"
          class="form-input {{ $errors->has('email') ? 'is-invalid' : '' }}"
          value="{{ old('email') }}"
          placeholder="email@perusahaan.id"
          autocomplete="email"
          autofocus
          required
        >
        @error('email')
          <div class="error-msg">⚠ {{ $message }}</div>
        @enderror
      </div>

      <div class="form-group">
        <label class="form-label" for="password">Password</label>
        <input
          id="password"
          type="password"
          name="password"
          class="form-input {{ $errors->has('password') ? 'is-invalid' : '' }}"
          placeholder="••••••••"
          autocomplete="current-password"
          required
        >
        @error('password')
          <div class="error-msg">⚠ {{ $message }}</div>
        @enderror
      </div>

      <div class="form-row">
        <label class="checkbox-label">
          <input type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
          Ingat saya
        </label>
      </div>

      <button type="submit" class="btn-login" id="btn-login">
        Masuk ke EcoTrack →
      </button>
    </form>

    <div class="demo-section">
      <div class="demo-title">🎮 Akun Demo — password: <code style="color:#5B8FFF">password</code></div>
      <div class="demo-grid">
        <button type="button" class="demo-btn" onclick="fillDemo('user@ecotrack.id')" id="demo-user">
          <div class="demo-role">👤</div>
          <span class="demo-label">Pengguna</span>
          <span class="demo-email">user@ecotrack.id</span>
        </button>
        <button type="button" class="demo-btn" onclick="fillDemo('seller@ecotrack.id')" id="demo-seller">
          <div class="demo-role">🏪</div>
          <span class="demo-label">Seller</span>
          <span class="demo-email">seller@ecotrack.id</span>
        </button>
        <button type="button" class="demo-btn" onclick="fillDemo('admin@ecotrack.id')" id="demo-admin">
          <div class="demo-role">🛡️</div>
          <span class="demo-label">Admin HR</span>
          <span class="demo-email">admin@ecotrack.id</span>
        </button>
      </div>
    </div>
  </div>
</div>

<script>
function fillDemo(email) {
  document.getElementById('email').value = email;
  document.getElementById('password').value = 'password';
  document.getElementById('email').focus();
}
</script>
</body>
</html>
