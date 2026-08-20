@extends('layouts.app')
@section('title','CMS Tips Harian')
@section('page-title','CMS Berita & Tips Harian')

@php
  $active = 'cms';
  $articleData = [
    ['title'=>'5 Cara Hemat Emisi Saat WFH','date'=>'07 Jul 2026 · Energi & Listrik'],
    ['title'=>'Kenapa Carpooling Bisa Kurangi Jejak Karbon?','date'=>'03 Jul 2026 · Transportasi'],
  ];
@endphp

@section('content')
<div class="page-head"><div><h1>CMS Berita & Tips Harian</h1><p>Tulis dan terbitkan tips pengurangan emisi ke dashboard pengguna.</p></div></div>

<div class="bento">
  <div class="card bento-c2">
    <div class="card-title" style="margin-bottom:14px;">📝 Tulis Artikel Baru</div>
    <div class="form-row"><label class="field-label">Judul Artikel</label><input class="input" type="text" placeholder="Contoh: 5 Cara Hemat Emisi saat WFH"></div>
    <div class="form-row"><label class="field-label">Kategori</label>
      <select><option>Transportasi</option><option>Makanan</option><option>Energi & Listrik</option><option>Umum</option></select>
    </div>
    <div class="form-row"><label class="field-label">Isi Konten</label><textarea placeholder="Tulis tips singkat di sini..."></textarea></div>
    <button class="btn btn-primary" onclick="showToast('📰 Artikel berhasil diterbitkan ke dashboard pengguna.')">Terbitkan Artikel</button>
  </div>

  <div class="card bento-c2">
    <div class="card-title" style="margin-bottom:14px;">📰 Artikel Terbit</div>
    <div style="display:flex; flex-direction:column; gap:10px;">
      @foreach($articleData as $a)
        <div style="display:flex; justify-content:space-between; align-items:center; padding:12px 14px; border:1px solid var(--border); border-radius:12px;">
          <div><div style="font-weight:600; font-size:13.5px;">{{ $a['title'] }}</div><div style="font-size:11.5px; color:var(--text-light); margin-top:2px;">{{ $a['date'] }}</div></div>
          <span class="badge badge-green">Terbit</span>
        </div>
      @endforeach
    </div>
  </div>
</div>
@endsection
