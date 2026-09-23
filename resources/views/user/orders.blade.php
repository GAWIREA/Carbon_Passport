@extends('layouts.app')
@section('title', 'Riwayat Penukaran Koin')
@section('page-title', 'Riwayat Penukaran')

@php
  $active = 'marketplace';
@endphp

@section('content')

<div style="margin-bottom: 24px;">
    <a href="{{ route('user.marketplace') }}" style="text-decoration: none; color: var(--text-light); display: inline-flex; align-items: center; gap: 8px;">
        ← Kembali ke Marketplace
    </a>
</div>

<div class="page-head" style="margin-bottom: 24px;">
    <div>
        <h1>Riwayat Penukaran 🛍️</h1>
        <p>Semua produk dan layanan yang telah kamu dapatkan.</p>
    </div>
</div>

@if($orders->isEmpty())
<div class="card" style="padding: 48px; text-align: center;">
    <div style="font-size: 48px; margin-bottom: 16px;">🛒</div>
    <h3 style="margin: 0 0 8px 0;">Belum ada penukaran</h3>
    <p style="color: var(--text-light); margin: 0 0 24px 0;">Kamu belum pernah menukarkan koin atau membeli produk apapun.</p>
    <a href="{{ route('user.marketplace') }}" class="btn btn-primary">Lihat Marketplace</a>
</div>
@else
<style>
    .order-list-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 16px;
    }
    @media (max-width: 640px) {
        .order-list-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="order-list-grid">
    @foreach($orders as $order)
    <div class="card" style="padding: 16px; display: flex; flex-direction: row; align-items: center; gap: 16px;">
        {{-- Ikon --}}
        <div style="width: 48px; height: 48px; background: #F8F9FA; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 24px; flex-shrink: 0;">
            @if($order->product && $order->product->name === 'Streak Pemulihan')
                🔥
            @elseif($order->product)
                🎁
            @else
                📦
            @endif
        </div>
        
        {{-- Teks 3 Baris --}}
        <div style="flex: 1; min-width: 0;">
            <h4 style="margin: 0 0 4px 0; font-size: 14px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ $order->product ? $order->product->name : 'Produk Tidak Diketahui' }}</h4>
            
            <div style="font-size: 11px; color: var(--text-light); display: flex; align-items: center; flex-wrap: wrap; gap: 6px; margin-bottom: 4px;">
                <span>{{ $order->created_at->translatedFormat('d M Y') }}</span>
                @if($order->product && $order->product->co2_reduction > 0)
                    <span style="color: #2E7D32; font-weight: 600;">🌿 -{{ $order->product->co2_reduction }} kg CO2</span>
                @endif
            </div>
            
            <div style="display: flex; align-items: center; gap: 6px; font-size: 12px;">
                @if($order->payment_method === 'coins')
                    <span style="font-weight: bold; color: #F5A623;">🪙 {{ number_format($order->total_coins, 0, ',', '.') }}</span>
                @else
                    <span style="font-weight: bold; color: #2ECC71;">Rp {{ number_format($order->total_idr, 0, ',', '.') }}</span>
                @endif
                <span style="color: var(--text-light); font-size: 11px;">• {{ ucfirst($order->status) }}</span>
            </div>
        </div>
        
        {{-- Badge Kanan --}}
        <div style="flex-shrink: 0;">
            <div style="width: 32px; height: 32px; background: #E8F5E9; color: #2E7D32; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 14px; font-weight: bold; box-shadow: 0 2px 4px rgba(46,125,50,0.1);">
                ✓
            </div>
        </div>
    </div>
    @endforeach
</div>

    {{-- Compact Custom Pagination --}}
    @if($orders->lastPage() > 1)
      <div style="margin-top:24px; display:flex; align-items:center; justify-content:center; gap:6px; flex-wrap:wrap;">

        {{-- Prev --}}
        @if($orders->onFirstPage())
          <span style="padding:6px 12px; border-radius:8px; border:1px solid var(--border); color:var(--text-light); font-size:13px; cursor:not-allowed; opacity:0.45; user-select:none;">‹ Prev</span>
        @else
          <a href="{{ $orders->previousPageUrl() }}" style="padding:6px 12px; border-radius:8px; border:1px solid var(--border); color:var(--text-dark); font-size:13px; text-decoration:none; font-weight:600; transition:background 0.15s;" onmouseover="this.style.background='var(--bg)'" onmouseout="this.style.background=''">‹ Prev</a>
        @endif

        {{-- Page Numbers --}}
        @php
          $currentPage = $orders->currentPage();
          $lastPage    = $orders->lastPage();
          $window      = 2; // pages on each side of current
          $start       = max(1, $currentPage - $window);
          $end         = min($lastPage, $currentPage + $window);
        @endphp

        @if($start > 1)
          <a href="{{ $orders->url(1) }}" style="padding:6px 10px; border-radius:8px; border:1px solid var(--border); color:var(--text-dark); font-size:13px; text-decoration:none; min-width:34px; text-align:center;">1</a>
          @if($start > 2)<span style="padding:6px 4px; font-size:13px; color:var(--text-light);">…</span>@endif
        @endif

        @for($p = $start; $p <= $end; $p++)
          @if($p === $currentPage)
            <span style="padding:6px 10px; border-radius:8px; background:var(--primary); color:#fff; font-size:13px; font-weight:700; min-width:34px; text-align:center;">{{ $p }}</span>
          @else
            <a href="{{ $orders->url($p) }}" style="padding:6px 10px; border-radius:8px; border:1px solid var(--border); color:var(--text-dark); font-size:13px; text-decoration:none; min-width:34px; text-align:center;">{{ $p }}</a>
          @endif
        @endfor

        @if($end < $lastPage)
          @if($end < $lastPage - 1)<span style="padding:6px 4px; font-size:13px; color:var(--text-light);">…</span>@endif
          <a href="{{ $orders->url($lastPage) }}" style="padding:6px 10px; border-radius:8px; border:1px solid var(--border); color:var(--text-dark); font-size:13px; text-decoration:none; min-width:34px; text-align:center;">{{ $lastPage }}</a>
        @endif

        {{-- Next --}}
        @if($orders->hasMorePages())
          <a href="{{ $orders->nextPageUrl() }}" style="padding:6px 12px; border-radius:8px; border:1px solid var(--border); color:var(--text-dark); font-size:13px; text-decoration:none; font-weight:600; transition:background 0.15s;" onmouseover="this.style.background='var(--bg)'" onmouseout="this.style.background=''">Next ›</a>
        @else
          <span style="padding:6px 12px; border-radius:8px; border:1px solid var(--border); color:var(--text-light); font-size:13px; cursor:not-allowed; opacity:0.45; user-select:none;">Next ›</span>
        @endif

        <span style="font-size:12px; color:var(--text-light); margin-left:8px;">
          {{ $orders->firstItem() }}–{{ $orders->lastItem() }} dari {{ $orders->total() }} data
        </span>
      </div>
    @else
      {{-- Single page: just show count --}}
      <div style="margin-top:24px; text-align:center; font-size:12px; color:var(--text-light);">
        Menampilkan {{ $orders->total() }} data
      </div>
    @endif
@endif
@endsection
