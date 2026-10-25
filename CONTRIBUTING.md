# Panduan Kontribusi

Terima kasih atas minat Anda pada Garuda-Hub. Proyek ini untuk pembelajaran, jadi kesederhanaan lebih penting daripada fitur banyak.

## Cara berkontribusi

1. Fork repositori, lalu buat cabang baru dari `main`.
2. Lakukan perubahan kecil dan fokus.
3. Uji manual di komputer lokal (PHP 8.1+, `mysqli`, MySQL/MariaDB; lihat README).
4. Ajukan pull request dengan penjelasan singkat tentang apa dan mengapa.

## Aturan

- Aplikasi tetap terdiri dari dua berkas: `src/index.php` dan `src/database.sql`.
- Tanpa framework, tanpa CDN, tanpa dependensi tambahan.
- Antarmuka dan pesan memakai bahasa Indonesia.
- Pertahankan praktik standar: prepared statement, `htmlspecialchars`, token CSRF, `password_hash`/`password_verify`, cek peran dan kepemilikan di server.
- Hanya data dummy. Jangan menambahkan kredensial, kunci, atau data pribadi asli.
- Di luar cakupan: pembayaran, upload file, email/notifikasi, API.

## Isu

Laporkan bug atau usulan lewat GitHub Issues. Untuk celah keamanan, ikuti [SECURITY.md](SECURITY.md).

Kontribusi Anda dilisensikan di bawah lisensi MIT proyek ini.
