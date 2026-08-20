# EcoTrack — Blade Prototype (Laravel)

Prototipe UI Carbon Tracking & Gamification App dalam format Blade PHP,
dengan dashboard beranda bergaya **bento grid** dan chart analytics (Chart.js).

## Cara Pasang di Project Laravel

1. Punya project Laravel (`laravel new nama-project` kalau belum ada).
2. Copy folder-folder ini ke dalam project:
   - `resources/views/*` → ke `resources/views/`
   - `public/css/app.css` → ke `public/css/app.css`
   - `routes/web.php` → **replace/gabungkan** dengan `routes/web.php` project kamu
3. Jalankan `php artisan serve`, lalu buka `http://localhost:8000/` — otomatis redirect ke `/user/dashboard`.

## Struktur Halaman

| Role | Route | Fitur |
|---|---|---|
| User | `/user/dashboard` | Beranda bento: Carbon Status Ring, progress rekomendasi, Climate Identity, chart tren emisi, leaderboard, breakdown kategori, perbandingan mingguan, feed rekomendasi, offset |
| User | `/user/tracking` | Sinkronisasi otomatis (Strava/Garmin/Maps) + input manual + Guided AI Scan |
| User | `/user/history` | Riwayat & detail emisi per aktivitas, filter kategori |
| User | `/user/recommendations` | **Rekomendasi gabungan**: aksi gratis (habit) + produk tukar/beli, difilter per tipe & kategori |
| User | `/user/leaderboard` | Leaderboard lengkap dengan badge emas/perak/perunggu |
| Seller | `/seller/dashboard` | KPI penjualan + chart |
| Seller | `/seller/catalog` | Manajemen katalog produk |
| Seller | `/seller/orders` | Daftar pesanan |
| Admin | `/admin/dashboard` | ESG & Analytics: tren, kontribusi divisi, total per kategori |
| Admin | `/admin/users` | Manajemen user & verifikasi seller |
| Admin | `/admin/cms` | CMS tips harian |

## Catatan Teknis
- Semua data masih **dummy**, didefinisikan langsung di tiap file Blade lewat blok `@php`.
  Ganti dengan data dari Controller/Model saat backend siap.
- Chart pakai **Chart.js** via CDN (`cdnjs.cloudflare.com`) — butuh koneksi internet saat dibuka.
- Tidak ada dependency CSS eksternal (Tailwind/Bootstrap), semua di `public/css/app.css`.
- Sidebar responsive: di layar <900px berubah jadi off-canvas (tombol ☰ di topbar).
- Konsep **Rekomendasi Pengurangan Emisi** (`/user/recommendations`) menggabungkan dua
  tipe item (`action` = aksi gratis dapat poin, `product` = tukar/beli produk), keduanya
  ditag kategori yang sama dan diberi filter agar user bisa fokus ke satu jenis saja.
