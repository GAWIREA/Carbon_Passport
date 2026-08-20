@extends('layouts.app')

@section('title', 'Pengaturan Umum')
@section('page-title', 'Pengaturan')

@section('content')
<div class="page-head">
    <div>
        <h1>Pengaturan Umum</h1>
        <p>Sesuaikan pengalaman penggunaan aplikasi Anda.</p>
    </div>
</div>

<div class="card" style="max-width: 600px;">
    <h3 style="margin-bottom: 16px; font-size: 16px;">Notifikasi</h3>
    <div style="display: flex; flex-direction: column; gap: 12px; margin-bottom: 24px;">
        <label style="display: flex; align-items: center; gap: 10px; font-size: 14px;">
            <input type="checkbox" checked style="width: 16px; height: 16px;">
            Aktifkan notifikasi email
        </label>
        <label style="display: flex; align-items: center; gap: 10px; font-size: 14px;">
            <input type="checkbox" checked style="width: 16px; height: 16px;">
            Pengingat misi harian
        </label>
    </div>

    <div class="dropdown-divider" style="margin-bottom: 24px;"></div>

    <h3 style="margin-bottom: 16px; font-size: 16px;">Privasi & Keamanan</h3>
    <div class="form-row">
        <label class="field-label">Kata Sandi Baru</label>
        <input type="password" class="input" placeholder="Masukkan kata sandi baru">
    </div>
    <div class="form-row" style="margin-bottom: 24px;">
        <label class="field-label">Konfirmasi Kata Sandi Baru</label>
        <input type="password" class="input" placeholder="Ulangi kata sandi baru">
    </div>
    
    <div class="form-row">
        <button class="btn btn-primary">Simpan Pengaturan</button>
    </div>
</div>
@endsection
