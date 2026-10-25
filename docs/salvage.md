<!-- maks 60 baris -->
# Audit Salvage – prototype/ → src/ (mode evolve)

Acuan: `docs/spec.md` (F1–F7), kontrak di `docs/architecture.md`. Baris = `prototype/index.php` kecuali disebut lain.

| Bagian prototype | Lokasi | Modul | Keputusan | Alasan |
| --- | --- | --- | --- | --- |
| Skema `projects`, `bids` | `mocks/database.sql:18-40` | M0 | refactor | Kolom sudah sesuai F2/F4. Perlu ditambah FK CASCADE, ENUM status, CHECK, UNIQUE (project_id, developer_id), dan indeks |
| Skema + dummy `users` | `mocks/database.sql:9-16, 43-51` | M0 | **tulis ulang** | Data pribadi. Perlu email UNIQUE, role ENUM, `password` 255; dummy ditulis ulang tetap fiktif (`example.com`) |
| Dummy proyek & bid | `mocks/database.sql:54-80` | M0 | pertahankan | Fiktif dan konsisten dengan ID user dummy |
| Konfigurasi DB via `getenv` | 6-10 | M1 | pertahankan | Sudah sesuai keputusan 2026-10-25 |
| Helper `e`, `rp`, `flash`, `status_badge` | 15-21, 51 | M1 | pertahankan | Sederhana dan benar |
| `go()` | 17 | M1 | refactor | Batasi ke URL relatif `?page=` |
| `db()`, `q()` | 22-36 | M1 | refactor | Logika sudah benar (prepared statement). Tambah return type `array\|int` dan helper `tx()` dengan rollback |
| Layout `render()` + CSS inline | 53-97 | M1 | refactor | Pertahankan tampilan. Tambah parameter `$code`; tombol "Keluar" diganti form POST + CSRF |
| Router `switch` + catch galat | 99-105, 350-358 | M1 | refactor | Diganti peta `ROUTES` dengan batasan metode (405), tambah `abort()` dan penangkap `Throwable` |
| `session_start`, `user()`, `need_role()` | 4, 37-43 | M2 | **tulis ulang** | Auth (F1). Peran diambil dari sesi dan tidak diperbarui setelah admin mengubah peran atau menghapus user; flag cookie sesi belum diatur |
| CSRF `csrf`, `csrf_field`, `check_csrf` | 44-50, 102 | M2 | **tulis ulang** | Bagian dari auth. Konsep boleh dirujuk, tetapi kode ditulis ulang |
| Daftar (`register`) | 171-193 | M2 | **tulis ulang** | Auth + data pribadi. Password minimal 6 karakter (tetap seperti prototype); duplikat email ditangani lewat UNIQUE |
| Masuk (`login`) | 195-213 | M2 | **tulis ulang** | Auth. Respons belum setara waktu untuk email tak dikenal; tambah `password_needs_rehash` |
| Keluar (`logout`) via GET | 215-220 | M2 | **tulis ulang** | Rentan CSRF logout. Diganti POST + CSRF |
| Beranda/cari (`home`) | 107-128 | M3 | pertahankan | Sesuai F3, sudah memakai prepared statement + `e()`. Hanya dipindah ke `page_home()`/`project_list()` |
| Detail proyek (`project`) | 130-169 | M3/M4 | refactor | Logika benar. Dipecah ke `project_find`, `project_is_open`, `bids_for_project` |
| Pasang proyek (`post`) | 222-247 | M3 | pertahankan | Validasi F2 lengkap, `client_id` diambil dari sesi. Dipindah ke `project_create()` |
| Ajukan penawaran (`bid`) | 249-263 | M4 | refactor | Benar. Cek duplikat diganti/didukung UNIQUE; tangkap error duplikat dengan pesan ramah |
| Pilih pemenang (`pick`) | 265-280 | M4 | refactor | Ada race: cek status dilakukan di luar transaksi, dan tidak ada rollback. Pindahkan cek ke dalam `tx()` + `SELECT … FOR UPDATE` |
| Dasbor (`dashboard`) | 282-306 | M5 | pertahankan | Sesuai F6, query difilter `uid` |
| Admin: kelola user (ubah peran, hapus) | 308-324, 333, 335-342 | M6 | **tulis ulang** | Data pribadi (email user). Hapus manual diganti FK CASCADE; validasi peran ke `ROLES` |
| Admin: hapus/daftar proyek | 325-330, 334, 343-348 | M6 | refactor | Logika benar; cukup memakai CASCADE + `tx()` |
| Halaman 404 | 350-352 | M1 | pertahankan | Dipindah ke router |

## Catatan
- Pembayaran: tidak ada di prototype maupun spec, jadi tidak ada yang perlu di-salvage.
- `prototype/` tetap utuh sebagai arsip; builder menyalin bagian berlabel "pertahankan/refactor" ke `src/` per modul, bukan seluruh file sekaligus.
- Bagian "tulis ulang" tidak boleh disalin dari prototype. Builder menulisnya dari kontrak di `architecture.md` §3 (M2, M6).
