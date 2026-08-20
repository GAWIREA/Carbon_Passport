@extends('layouts.app')
@section('title','Edit Profil')
@section('page-title','Edit Profil')

@php
  $active = 'profile';
@endphp

@section('content')
<div class="page-head">
    <div>
        <a href="{{ route('user.profile') }}" style="font-size: 14px; color: var(--primary); margin-bottom: 8px; display: inline-block;">&larr; Kembali ke Profil</a>
        <h1>Edit Profil</h1>
        <p>Perbarui informasi akun Anda.</p>
    </div>
</div>

<div class="card" style="max-width: 600px;">
    @if ($errors->any())
        <div style="background: var(--danger); color: white; padding: 12px; border-radius: 8px; margin-bottom: 16px;">
            <ul style="margin: 0; padding-left: 20px;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('user.profile.update') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div style="margin-bottom: 16px;">
            <label style="display: block; font-weight: 600; margin-bottom: 8px;">Foto Profil</label>
            <div style="display: flex; align-items: center; gap: 16px;">
                @if($user->avatar)
                    <img src="{{ Storage::url($user->avatar) }}" alt="Avatar" style="width: 80px; height: 80px; border-radius: 50%; object-fit: cover;">
                @else
                    <div class="user-avatar" style="width: 80px; height: 80px; font-size: 40px; box-shadow: var(--shadow-sm); display: flex; align-items: center; justify-content: center; background: var(--secondary-light);">👤</div>
                @endif
                <div>
                    <input type="file" name="avatar" class="input" accept="image/*">
                    <div style="font-size: 12px; color: var(--text-light); margin-top: 4px;">Maksimal 2MB (JPEG, PNG).</div>
                </div>
            </div>
        </div>

        <div style="margin-bottom: 16px;">
            <label style="display: block; font-weight: 600; margin-bottom: 8px;">Nama Lengkap</label>
            <input type="text" name="name" class="input" value="{{ old('name', $user->name) }}" required>
        </div>

        <div style="margin-bottom: 24px;">
            <label style="display: block; font-weight: 600; margin-bottom: 8px;">Username</label>
            <div style="display: flex; align-items: center; gap: 8px;">
                <span style="color: var(--text-light); font-weight: 600;">@</span>
                <input type="text" id="usernameInput" name="username" class="input" value="{{ old('username', $user->username) }}" style="flex: 1;" required>
            </div>
            <div style="font-size: 12px; color: var(--text-light); margin-top: 4px;">Hanya huruf, angka, titik (.), dan garis bawah (_).</div>
        </div>

        <div style="display: flex; justify-content: flex-end; gap: 12px;">
            <a href="{{ route('user.profile') }}" class="btn btn-outline">Batal</a>
            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
        </div>
    </form>
</div>

<script>
    const usernameInput = document.getElementById('usernameInput');
    if (usernameInput) {
        usernameInput.addEventListener('input', function(e) {
            // Replace spaces with underscores
            let val = e.target.value.replace(/ /g, '_');
            // Remove any character that is not a letter, number, dot, or underscore
            val = val.replace(/[^a-zA-Z0-9_\.]/g, '');
            // Update the input value
            e.target.value = val;
        });
    }
</script>
@endsection
