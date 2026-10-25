<!-- maks ~1 halaman (60 baris). Pemilik: orchestrator. Dibaca: semua agen (reviewer: fitur terkait saja) -->
# Spesifikasi Produk – Garuda-Hub

## Tujuan
- Website lelang proyek IT: klien memasang proyek, developer menawar, klien memilih pemenang.
- Untuk **studi kasus pembelajaran keamanan** mahasiswa: aplikasi ditulis dengan **praktik PHP standar** (Opsi A, lihat `decisions.md`). Mahasiswa menganalisis kemungkinan celah via `docs/VULNERABILITIES.md`.
- Dipublikasikan di GitHub (lisensi MIT).

## Fitur
| ID | Fitur | Kriteria terima | Data sensitif? |
| --- | --- | --- | --- |
| F1 | Daftar & login | Peran klien, developer, admin; password di-hash (`password_hash`) | ya (dummy saja) |
| F2 | Klien memasang proyek | Judul, deskripsi, kategori, budget min–maks, batas lelang | tidak |
| F3 | Daftar, cari, detail proyek | Cari judul/kategori; detail menampilkan semua penawaran | tidak |
| F4 | Developer mengajukan penawaran | Harga, durasi (hari), proposal; hanya saat lelang terbuka | tidak |
| F5 | Klien memilih pemenang | Status proyek → ditutup, penawaran pemenang ditandai | tidak |
| F6 | Dasbor | Klien: "proyek saya"; developer: "penawaran saya" | tidak |
| F7 | Admin | Kelola (lihat/hapus/ubah peran) user dan proyek | ya (dummy saja) |

## Pendekatan keamanan (Opsi A)
- Kode memakai praktik standar: prepared statement, `htmlspecialchars`, token CSRF, `password_hash`/`password_verify`, cek peran & kepemilikan sisi server.
- Tidak ada sesi hardening keamanan tambahan. Analisis kemungkinan celah + status penanganannya untuk dosen: `docs/VULNERABILITIES.md` (**tidak diunggah ke GitHub**).

## Stack yang direncanakan
- PHP 8 native (tanpa framework), ekstensi mysqli, MySQL/MariaDB.
- Tepat 2 file aplikasi: `index.php` (routing `?page=`, CSS inline, tanpa CDN) dan `database.sql` (skema + data dummy).
- UI bahasa Indonesia. Tanpa payment gateway.

## Dokumen publik (GitHub)
- `README.md` (instalasi + peringatan "jangan dipasang di server publik"), `LICENSE` (MIT), `SECURITY.md`, `CONTRIBUTING.md`, `CHANGELOG.md`.
- Tidak diunggah: `.claude/`, `CLAUDE.md`, `SETUP.md`, `docs/VULNERABILITIES.md`.

## Di luar cakupan (out of scope)
- Pembayaran, escrow, upload file, email/notifikasi, API, tes otomatis.
- Sesi audit/hardening keamanan tambahan (cukup praktik standar).
