# Countdown

Website untuk membuat hitung mundur menuju acara penting dan membagikannya melalui tautan unik.

## Fitur

- Buat, edit, hapus, dan buka kembali countdown tersimpan.
- Hitung mundur hari, jam, menit, dan detik dengan koreksi waktu server.
- Input dan tampilan WIB; waktu disimpan dalam UTC.
- Tautan berbagi acak untuk melihat acara, salin tautan, dan layar penuh.
- Tampilan responsif dengan pesan saat acara dimulai.

## Teknologi

Laravel 13, Blade, SQLite, HTML5, CSS3, JavaScript, dan Vite. Memerlukan PHP 8.3+ beserta ekstensi SQLite, Composer, dan Node.js 22.12+.

## Menjalankan

```sh
composer install
cp .env.example .env
php artisan key:generate
php -r "file_exists('database/database.sqlite') || touch('database/database.sqlite');"
php artisan migrate
npm ci
npm run build
php artisan serve --host=127.0.0.1
```

Buka http://127.0.0.1:8000. Untuk pengembangan aset, jalankan `npm run dev` di terminal lain.

Aplikasi ini untuk demo lokal. Pengelolaan hanya aktif pada lingkungan `local`, koneksi loopback, dan host `localhost`/`127.0.0.1`/`[::1]`. Jika memakai MAMP, arahkan document root ke folder `public` dan gunakan PHP 8.3+. Jangan mengekspos pengelolaan anonim ke internet; tambahkan autentikasi sebelum deployment publik. Tautan `/c/{token}` hanya untuk melihat dan memerlukan alamat server yang dapat dijangkau penerima.

Jalankan pengujian dengan `php artisan test` dan `npm test`.
