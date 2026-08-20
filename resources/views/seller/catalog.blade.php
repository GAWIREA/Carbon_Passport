@extends('layouts.app')
@section('title','Katalog Produk')
@section('page-title','Manajemen Katalog Produk')

@php
  $active = 'catalog';
  $catalogData = [
    ['name'=>'Voucher Commuter Line 50rb','icon'=>'🚆','cat'=>'Transportasi','pts'=>800,'idr'=>'Rp 50.000','stok'=>120,'status'=>'Aktif'],
    ['name'=>'Diskon Servis Sepeda 30%','icon'=>'🚲','cat'=>'Transportasi','pts'=>650,'idr'=>'Rp 75.000','stok'=>45,'status'=>'Aktif'],
    ['name'=>'Tumbler Stainless 500ml','icon'=>'🧉','cat'=>'Makanan','pts'=>400,'idr'=>'Rp 89.000','stok'=>8,'status'=>'Stok Menipis'],
    ['name'=>'Smart Plug Hemat Energi','icon'=>'🔌','cat'=>'Energi & Listrik','pts'=>900,'idr'=>'Rp 149.000','stok'=>0,'status'=>'Habis'],
  ];
  $badgeMap = ['Aktif'=>'badge-green','Stok Menipis'=>'badge-orange','Habis'=>'badge-red'];
@endphp

@section('content')
<div class="page-head">
  <div><h1>Manajemen Katalog Produk</h1><p>Kelola produk ramah lingkungan yang kamu jual.</p></div>
  <button class="btn btn-primary" onclick="showToast('📦 Form tambah produk baru dibuka (simulasi).')">+ Tambah Produk Baru</button>
</div>

<div class="card">
  <div class="table-wrap">
    <table>
      <thead><tr><th>Produk</th><th>Kategori Emisi</th><th>Harga (Poin)</th><th>Harga (Rp)</th><th>Stok</th><th>Status</th></tr></thead>
      <tbody>
        @foreach($catalogData as $p)
          <tr>
            <td><div class="table-title-cell"><div class="table-thumb">{{ $p['icon'] }}</div>{{ $p['name'] }}</div></td>
            <td>{{ $p['cat'] }}</td>
            <td>{{ number_format($p['pts'],0,',','.') }} Pts</td>
            <td>{{ $p['idr'] }}</td>
            <td>{{ $p['stok'] }} unit</td>
            <td><span class="badge {{ $badgeMap[$p['status']] }}">{{ $p['status'] }}</span></td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</div>
@endsection
