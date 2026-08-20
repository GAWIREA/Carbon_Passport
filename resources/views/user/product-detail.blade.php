@extends('layouts.app')
@section('title', $product['name'] . ' - Detail Reward')
@section('page-title', 'Detail Produk')

@php
  $active = 'marketplace';
@endphp

@section('content')
<div style="margin-bottom: 24px;">
    <a href="{{ route('user.marketplace') }}" style="text-decoration: none; color: var(--text-light); display: inline-flex; align-items: center; gap: 8px;">
        ← Kembali ke Marketplace
    </a>
</div>

<div class="card" style="padding: 32px; display: flex; gap: 48px; flex-wrap: wrap;">
    <!-- Bagian Kiri: Gambar Produk -->
    <div style="flex: 1; min-width: 300px; display: flex; justify-content: center; align-items: center; background: var(--bg-body); border-radius: 16px; padding: 48px;">
        <div style="font-size: 120px; line-height: 1;">
            {{ $product['image'] }}
        </div>
    </div>

    <!-- Bagian Kanan: Detail Produk -->
    <div style="flex: 1.5; min-width: 300px; display: flex; flex-direction: column;">
        <div style="display: flex; gap: 8px; margin-bottom: 16px;">
            <span class="badge badge-grey">{{ $product['category'] }}</span>
            <span class="badge" style="background: #E8F5E9; color: #2E7D32;">{{ ucfirst($product['type']) }}</span>
            @if($product['stock'] > 0)
                <span class="badge" style="background: #FFF3E0; color: #E65100;">Sisa {{ $product['stock'] }}</span>
            @else
                <span class="badge" style="background: #FFEBEE; color: #C62828;">Stok Habis</span>
            @endif
        </div>

        <h1 style="font-size: 28px; margin: 0 0 16px 0;">{{ $product['name'] }}</h1>
        
        <div style="font-size: 24px; font-weight: bold; color: #F5A623; display: flex; align-items: center; gap: 8px; margin-bottom: 8px;">
            🪙 {{ number_format($product['coin_price'], 0, ',', '.') }} <span style="font-size: 14px; font-weight: normal; color: var(--text-light);">koin</span>
        </div>
        
        <div style="font-size: 13px; font-weight: 600; color: #5B8FFF; background: #EEF1F5; padding: 6px 12px; border-radius: 8px; display: inline-flex; align-items: center; gap: 6px; margin-bottom: 24px; width: fit-content; border-bottom: 1px solid #eee;">
            ✨ Bonus Pembelian: <span style="font-size: 14px; font-weight: 800;">+150 XP</span>
        </div>

        <h3 style="font-size: 16px; margin: 0 0 8px 0;">Deskripsi Produk</h3>
        <p style="color: var(--text-main); line-height: 1.6; margin-bottom: 32px;">
            {{ $product['description'] }}
        </p>

        <div style="margin-top: auto; display: flex; gap: 16px;">
            <button class="btn btn-outline" style="flex: 1; padding: 12px; font-size: 16px; font-weight: bold; display: flex; justify-content: center; align-items: center; gap: 8px;">
                🛒 Masukkan Keranjang
            </button>
            <form action="{{ route('user.marketplace.buy', $product['id']) }}" method="POST" style="flex: 1; display:flex;">
                @csrf
                <button type="submit" class="btn btn-primary" style="flex: 1; padding: 12px; font-size: 16px; font-weight: bold; display: flex; justify-content: center; align-items: center; gap: 8px; {{ $product['stock'] <= 0 ? 'opacity: 0.5; cursor: not-allowed;' : '' }}" {{ $product['stock'] <= 0 ? 'disabled' : '' }}>
                    🪙 Beli Sekarang
                </button>
            </form>
        </div>
        
        @if($product['coin_price'] > ($user->coins ?? 0))
            <div style="margin-top: 12px; font-size: 12px; color: #E74C3C; text-align: center;">
                Koin kamu belum mencukupi untuk menukar produk ini. Selesaikan misi untuk dapatkan koin!
            </div>
        @endif
    </div>
</div>

<!-- Informasi Tambahan -->
<div class="bento" style="margin-top: 24px;">
    <div class="card" style="flex: 1;">
        <h3 style="margin-top: 0;">Syarat & Ketentuan</h3>
        <ul style="padding-left: 20px; color: var(--text-main); margin-bottom: 0;">
            <li>Voucher/Produk yang sudah ditukar tidak dapat dikembalikan.</li>
            <li>Pastikan email yang terdaftar aktif untuk pengiriman voucher digital.</li>
            <li>Pengiriman produk fisik memakan waktu 3-5 hari kerja.</li>
        </ul>
    </div>
    <div class="card" style="flex: 1;">
        <h3 style="margin-top: 0;">Cara Pembelian</h3>
        <ol style="padding-left: 20px; color: var(--text-main); margin-bottom: 0;">
            <li>Klik tombol <strong>Beli Sekarang</strong>.</li>
            <li>Koin akan otomatis terpotong dari saldo kamu.</li>
            <li>Kamu akan langsung menerima bonus XP.</li>
            <li>Cek status pembelian di menu Riwayat.</li>
        </ol>
    </div>
</div>
@endsection
