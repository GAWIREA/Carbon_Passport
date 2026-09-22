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
    <div style="display: flex; gap: 16px; align-items: center;">
        <a href="{{ route('user.orders') }}" class="btn btn-outline" style="padding: 10px 16px; display: flex; align-items: center; gap: 8px;">
            <span style="font-size: 18px;">🛍️</span> Riwayat Penukaran
        </a>
        <div style="background: var(--bg-card); padding: 12px 24px; border-radius: 12px; display: flex; align-items: center; gap: 12px; box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
            <span style="font-size: 24px;">🪙</span>
            <div>
                <div style="font-size: 12px; color: var(--text-light);">Koin Tersedia</div>
                <div style="font-size: 20px; font-weight: 700; color: #F5A623;">{{ number_format(auth()->user()->coins ?? 0, 0, ',', '.') }}</div>
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
        
        <div class="bento" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 24px;">
            @foreach($products as $product)
            <div class="card product-card" style="display: flex; flex-direction: column; padding: 16px; transition: transform 0.2s;">
                <div style="font-size: 64px; text-align: center; padding: 32px 0; background: var(--bg-body); border-radius: 12px; margin-bottom: 16px;">
                    {{ $product['image'] }}
                </div>
                <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 8px;">
                    <span class="badge badge-grey" style="font-size: 10px;">{{ $product['category'] }}</span>
                    <span class="badge" style="font-size: 10px; background: #E8F5E9; color: #2E7D32;">{{ ucfirst($product['type']) }}</span>
                </div>
                <h3 style="font-size: 16px; margin: 0 0 12px 0; flex-grow: 1;">{{ $product['name'] }}</h3>
                <div style="display: flex; justify-content: space-between; align-items: center; margin-top: auto;">
                    <div style="font-weight: bold; color: #F5A623; display: flex; flex-direction: column; gap: 4px;">
                        <span>🪙 {{ number_format($product['coin_price'], 0, ',', '.') }}</span>
                        @if($product['idr_price'] > 0)
                        <span style="font-size: 12px; color: #2ECC71;">Rp {{ number_format($product['idr_price'], 0, ',', '.') }}</span>
                        @endif
                    </div>
                    <a href="{{ route('user.marketplace.detail', $product['id']) }}" class="btn btn-primary" style="padding: 6px 12px; font-size: 12px; text-decoration: none;">Lihat</a>
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
    .product-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 16px rgba(0,0,0,0.1);
    }
</style>
@endpush

