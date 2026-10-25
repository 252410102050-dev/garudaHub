# Panduan Pengguna Garuda-Hub

Garuda-Hub adalah situs lelang proyek IT. Klien memasang proyek, developer mengajukan penawaran, lalu klien memilih pemenang. Aplikasi ini untuk belajar, jadi semua datanya fiktif.

## Pilih panduan sesuai peran

| Peran | Yang bisa dilakukan | Panduan |
| --- | --- | --- |
| Pengunjung (belum masuk) | Melihat dan mencari proyek, melihat penawaran | bagian di bawah |
| Klien | Memasang proyek, memilih pemenang, melihat "Proyek Saya" | [klien.md](klien.md) |
| Developer | Mengajukan penawaran, melihat "Penawaran Saya" | [developer.md](developer.md) |
| Admin | Mengelola pengguna dan proyek | [admin.md](admin.md) |

## Cara mulai

1. Buka alamat situs di peramban (untuk pemakaian lokal biasanya `http://localhost:8000`).
2. Klik **Daftar** di kanan atas.
3. Isi nama, email, dan password (6 sampai 72 karakter).
4. Pilih peran **Klien** atau **Developer**, lalu klik **Daftar**.
5. Klik **Masuk**, isi email dan password, lalu klik **Masuk**.
6. Untuk keluar, klik **Keluar** di kanan atas.

Akun admin tidak bisa dibuat lewat pendaftaran. Peran admin diberikan oleh admin lain atau lewat data awal aplikasi.

## Melihat dan mencari proyek (tanpa masuk)

1. Buka halaman **Proyek**. Daftar proyek tampil dari yang terbaru.
2. Ketik kata di kotak pencarian (judul atau kategori), lalu klik **Cari**.
3. Klik judul proyek untuk melihat deskripsi, budget, batas lelang, dan semua penawaran.

Status proyek:
- **terbuka**: penawaran masih bisa diajukan (selama batas lelang belum lewat).
- **ditutup**: pemenang sudah dipilih.

## Pesan galat yang umum

- "Token keamanan tidak valid": muat ulang halaman, lalu coba lagi.
- "Anda tidak berhak mengakses halaman ini": peran Anda tidak sesuai dengan halaman itu.
- "Email atau password salah": periksa kembali isian Anda.

## Catatan

Hanya gunakan data fiktif. Jangan memasukkan data pribadi atau password asli yang Anda pakai di tempat lain.
