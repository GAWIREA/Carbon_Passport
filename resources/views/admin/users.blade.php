@extends('layouts.app')
@section('title','User & Seller Management')
@section('page-title','User & Seller Management')

@php
  $active = 'users';
  $userMgmtData = [
    ['name'=>'Nadia Putri','dept'=>'Marketing','pts'=>2480,'status'=>'Aktif'],
    ['name'=>'Bayu Setiawan','dept'=>'Engineering','pts'=>4210,'status'=>'Aktif'],
    ['name'=>'Doni Kurniawan','dept'=>'Finance','pts'=>980,'status'=>'Diblokir'],
  ];
  $sellerMgmtData = [
    ['name'=>'Green Living Store','cat'=>'Produk Rumah Tangga','doc'=>'NPWP, SIUP','status'=>'Terverifikasi'],
    ['name'=>'Bike Repair Hub','cat'=>'Jasa Servis Sepeda','doc'=>'KTP, Portofolio','status'=>'Menunggu'],
  ];
  $badgeMap = ['Aktif'=>'badge-green','Diblokir'=>'badge-red','Terverifikasi'=>'badge-green','Menunggu'=>'badge-grey'];
@endphp

@section('content')
<div class="page-head"><div><h1>User & Seller Management</h1><p>Kelola akun pengguna dan verifikasi pendaftaran seller.</p></div></div>

<div class="card" style="margin-bottom:18px;">
  <div class="card-title" style="margin-bottom:14px;">👥 Akun Pengguna</div>
  <div class="table-wrap">
    <table>
      <thead><tr><th>Nama</th><th>Divisi</th><th>Poin</th><th>Status</th><th>Aksi</th></tr></thead>
      <tbody>
        @foreach($userMgmtData as $u)
          <tr>
            <td>{{ $u['name'] }}</td><td>{{ $u['dept'] }}</td><td>{{ number_format($u['pts'],0,',','.') }}</td>
            <td><span class="badge {{ $badgeMap[$u['status']] }}">{{ $u['status'] }}</span></td>
            <td><button class="btn btn-outline btn-sm" onclick="showToast('Status akun {{ $u['name'] }} diperbarui.')">{{ $u['status']==='Aktif' ? 'Blokir' : 'Aktifkan' }}</button></td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</div>

<div class="card">
  <div class="card-title" style="margin-bottom:14px;">🏪 Verifikasi Seller Baru</div>
  <div class="table-wrap">
    <table>
      <thead><tr><th>Nama Toko</th><th>Kategori Produk</th><th>Dokumen</th><th>Status</th><th>Aksi</th></tr></thead>
      <tbody>
        @foreach($sellerMgmtData as $s)
          <tr>
            <td>{{ $s['name'] }}</td><td>{{ $s['cat'] }}</td><td>{{ $s['doc'] }}</td>
            <td><span class="badge {{ $badgeMap[$s['status']] }}">{{ $s['status'] }}</span></td>
            <td>
              @if($s['status']==='Menunggu')
                <button class="btn btn-secondary btn-sm" onclick="showToast('Seller {{ $s['name'] }} diverifikasi.')">Verifikasi</button>
              @else
                <span style="color:var(--text-light); font-size:12.5px;">—</span>
              @endif
            </td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</div>
@endsection
