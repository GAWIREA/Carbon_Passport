@extends('layouts.app')
@section('title','Daftar Pesanan')
@section('page-title','Daftar Pesanan')

@php
  $active = 'orders';
  $ordersData = [
    ['id'=>'#ECO-1042','user'=>'Nadia Putri','product'=>'Voucher Commuter Line 50rb','date'=>'05 Jul 2026','status'=>'Dikirim'],
    ['id'=>'#ECO-1041','user'=>'Bayu Setiawan','product'=>'Tumbler Stainless 500ml','date'=>'05 Jul 2026','status'=>'Selesai'],
    ['id'=>'#ECO-1040','user'=>'Sari Dewi','product'=>'Smart Plug Hemat Energi','date'=>'04 Jul 2026','status'=>'Diproses'],
    ['id'=>'#ECO-1039','user'=>'Rian Hidayat','product'=>'Diskon Servis Sepeda 30%','date'=>'03 Jul 2026','status'=>'Selesai'],
  ];
  $badgeMap = ['Diproses'=>'badge-orange','Dikirim'=>'badge-blue','Selesai'=>'badge-green'];
@endphp

@section('content')
<div class="page-head"><div><h1>Daftar Pesanan</h1><p>Pantau pesanan hasil penukaran koin pengguna.</p></div></div>

<div class="card">
  <div class="table-wrap">
    <table>
      <thead><tr><th>ID Pesanan</th><th>Pengguna</th><th>Produk</th><th>Tanggal</th><th>Status</th></tr></thead>
      <tbody>
        @foreach($ordersData as $o)
          <tr>
            <td><strong>{{ $o['id'] }}</strong></td>
            <td>{{ $o['user'] }}</td>
            <td>{{ $o['product'] }}</td>
            <td>{{ $o['date'] }}</td>
            <td><span class="badge {{ $badgeMap[$o['status']] }}">{{ $o['status'] }}</span></td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</div>
@endsection
