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
    <div style="background: var(--bg-card); padding: 12px 24px; border-radius: 12px; display: flex; align-items: center; gap: 12px; box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
        <span style="font-size: 24px;">🪙</span>
        <div>
            <div style="font-size: 12px; color: var(--text-light);">Koin Tersedia</div>
            <div style="font-size: 20px; font-weight: 700; color: #F5A623;">{{ number_format(auth()->user()->coins ?? 0, 0, ',', '.') }}</div>
        </div>
    </div>
</div>

<div class="filter-tabs" style="margin-bottom: 24px;">
    <button class="filter-tab active" data-cat="all">Semua Reward</button>
    <button class="filter-tab" data-cat="Transportasi">Transportasi</button>
    <button class="filter-tab" data-cat="Makanan">Makanan</button>
    <button class="filter-tab" data-cat="Lifestyle">Lifestyle</button>
</div>

<div class="bento" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 24px;">
    @foreach($products as $product)
    <div class="card product-card" data-cat="{{ $product['category'] }}" style="display: flex; flex-direction: column; padding: 16px; transition: transform 0.2s;">
        <div style="font-size: 64px; text-align: center; padding: 32px 0; background: var(--bg-body); border-radius: 12px; margin-bottom: 16px;">
            {{ $product['image'] }}
        </div>
        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 8px;">
            <span class="badge badge-grey" style="font-size: 10px;">{{ $product['category'] }}</span>
            <span class="badge" style="font-size: 10px; background: #E8F5E9; color: #2E7D32;">{{ ucfirst($product['type']) }}</span>
        </div>
        <h3 style="font-size: 16px; margin: 0 0 12px 0; flex-grow: 1;">{{ $product['name'] }}</h3>
        <div style="display: flex; justify-content: space-between; align-items: center; margin-top: auto;">
            <div style="font-weight: bold; color: #F5A623; display: flex; align-items: center; gap: 4px;">
                🪙 {{ number_format($product['point_price'], 0, ',', '.') }}
            </div>
            <a href="{{ route('user.marketplace.detail', $product['id']) }}" class="btn btn-primary" style="padding: 6px 12px; font-size: 12px; text-decoration: none;">Lihat</a>
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
<script>
document.querySelectorAll('.filter-tab[data-cat]').forEach(btn=>{
  btn.addEventListener('click', ()=>{
    document.querySelectorAll('.filter-tab[data-cat]').forEach(b=>b.classList.remove('active'));
    btn.classList.add('active');
    const cat = btn.dataset.cat;
    document.querySelectorAll('.product-card').forEach(r=> r.style.display = (cat==='all'||r.dataset.cat===cat) ? 'flex' : 'none');
  });
});
</script>
@endpush
