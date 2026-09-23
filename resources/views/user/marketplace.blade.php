@extends('layouts.app')
@section('title','Marketplace & Tukar Koin')
@section('page-title','Marketplace & Tukar Koin')

@php
  $active = 'marketplace'; // assuming we might add it to sidebar later
@endphp

@section('content')
<div class="page-head">
    <div>
        <h1>Marketplace & Tukar Koin</h1>
        <p>Tukarkan koin yang telah kamu kumpulkan dengan berbagai produk menarik.</p>
    </div>
    <div style="display: flex; gap: 16px; align-items: stretch; flex-wrap: nowrap; width: 100%;">
        <a href="{{ route('user.orders') }}" style="background: #FFFFFF; padding: 12px 16px; border-radius: 12px; display: flex; align-items: center; justify-content: center; gap: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.04); text-decoration: none; color: var(--text-dark); border: 1px solid var(--border); flex: 1; min-width: 0;">
            <span style="font-size: 24px;">🛍️</span> 
            <div style="font-size: 13px; font-weight: 600; line-height: 1.3;">Riwayat<br>Penukaran</div>
        </a>
        <div style="background: #FFFFFF; padding: 12px 16px; border-radius: 12px; display: flex; align-items: center; justify-content: center; gap: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.04); flex: 1; min-width: 0;">
            <span style="font-size: 24px;">🪙</span>
            <div>
                <div style="font-size: 11px; color: var(--text-light); line-height: 1.2;">Koin<br>Tersedia</div>
                <div style="font-size: 16px; font-weight: 700; color: #F5A623; margin-top: 2px;">{{ number_format(auth()->user()->coins ?? 0, 0, ',', '.') }}</div>
            </div>
        </div>
    </div>
</div>

<div class="marketplace-container" style="display: flex; flex-direction: column; gap: 32px; margin-bottom: 24px;">
    @foreach($groupedProducts as $category => $products)
    <div class="category-section">
        <h2 style="font-size: 18px; color: var(--text-dark); margin-bottom: 16px; display: flex; align-items: center; gap: 8px;">
            <span style="display: inline-block; width: 4px; height: 20px; background: var(--primary); border-radius: 4px;"></span>
            {{ $category }}
        </h2>
        
        <div class="marketplace-grid bento">
            @foreach($products as $product)
            <div class="card product-card">
                <div class="product-icon-box">
                    {{ $product['image'] }}
                </div>
                <div class="product-badges">
                    <span class="badge badge-grey">{{ $product['category'] }}</span>
                    <span class="badge badge-type">{{ ucfirst($product['type']) }}</span>
                </div>
                <h3 class="product-title">{{ $product['name'] }}</h3>
                <div class="product-price-action">
                    <div class="product-price-box">
                        <span class="coin-price">🪙 {{ number_format($product['coin_price'], 0, ',', '.') }}</span>
                        @if($product['idr_price'] > 0)
                        <span class="idr-price">Rp {{ number_format($product['idr_price'], 0, ',', '.') }}</span>
                        @endif
                    </div>
                    <a href="{{ route('user.marketplace.detail', $product['id']) }}" class="btn btn-primary product-btn">Lihat</a>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endforeach
</div>
@endsection

@push('scripts')
<style>
    .marketplace-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
        gap: 24px;
    }
    .product-card {
        display: flex;
        flex-direction: column;
        padding: 16px;
        transition: transform 0.2s;
    }
    .product-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 16px rgba(0,0,0,0.1);
    }
    .product-icon-box {
        font-size: 64px;
        text-align: center;
        padding: 32px 0;
        background: var(--bg-body);
        border-radius: 12px;
        margin-bottom: 16px;
    }
    .product-badges {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 8px;
    }
    .product-badges .badge-grey {
        font-size: 10px;
    }
    .product-badges .badge-type {
        font-size: 10px;
        background: #E8F5E9;
        color: #2E7D32;
    }
    .product-title {
        font-size: 16px;
        margin: 0 0 12px 0;
        flex-grow: 1;
    }
    .product-price-action {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: auto;
    }
    .product-price-box {
        font-weight: bold;
        color: #F5A623;
        display: flex;
        flex-direction: column;
        gap: 4px;
    }
    .coin-price {
        font-size: 14px;
    }
    .idr-price {
        font-size: 14px;
        color: #2ECC71;
    }
    .product-btn {
        padding: 6px 12px;
        font-size: 12px;
        text-decoration: none;
    }
    
    @media (max-width: 640px) {
        .marketplace-grid {
            grid-template-columns: repeat(2, 1fr) !important;
            gap: 12px !important;
        }
        .product-card {
            padding: 12px !important;
        }
        .product-icon-box {
            padding: 12px 0 !important;
            font-size: 36px !important;
            margin-bottom: 8px !important;
        }
        .product-badges {
            flex-direction: column !important;
            gap: 4px !important;
            align-items: flex-start !important;
            margin-bottom: 8px !important;
        }
        .product-badges .badge {
            font-size: 9px !important;
            padding: 4px 6px !important;
            width: fit-content !important;
            text-align: left !important;
        }
        .product-title {
            font-size: 12px !important;
            margin-bottom: 8px !important;
            text-align: left !important;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            min-height: 30px;
            white-space: normal !important;
            line-height: 1.25;
        }
        .product-price-action {
            flex-direction: column !important;
            align-items: flex-start !important;
            gap: 8px !important;
            width: 100%;
        }
        .product-price-box {
            align-items: flex-start !important;
            gap: 2px !important;
        }
        .coin-price {
            font-size: 12px !important;
        }
        .idr-price {
            font-size: 12px !important;
        }
        .product-btn {
            width: 100%;
            font-size: 11px !important;
            padding: 6px 0 !important;
            text-align: center !important;
            display: block !important;
        }
    }
</style>
@endpush

