<!-- satu baris per tugas. Pemilik: orchestrator. Dibaca: builder (barisnya saja), reviewer -->
# Daftar Tugas

Status: todo · proses · review · selesai · eskalasi
Penolakan: jumlah review blocking (0–3)

| ID | Modul | Fitur | Kesulitan | Status | Penolakan | Hasil review / catatan non-blocking |
| --- | --- | --- | --- | --- | --- | --- |
| T0 | skema (M0) `modul/skema` | F1–F7 data | rendah | selesai | 0 | Non-blocking: samakan COLLATE tabel (baris 18,35,50); proyek 5 terbuka tapi batas lewat (kasus uji); is_winner satu per proyek dijaga M4 |
| T1 | inti (M1) `modul/inti` + stub kontrak semua modul | semua | sedang | selesai | 0 | Non-blocking: index.php:31 regex pakai $ (terima \n akhir), ganti \z |
| T2 | auth (M2) `modul/auth` — ditulis ulang | F1 | tinggi | selesai | 0 | Non-blocking: auth_login ~195 cost seed 12 vs rehash default (selisih waktu kecil); alur DB F1 belum diuji manual (tanpa MySQL) |
| T3 | proyek (M3) `modul/proyek` | F2, F3 | sedang | selesai | 0 | Non-blocking: pasang date_default_timezone_set di inti; batas lelang lampau ditolak (tidak di spec); detail proyek 501 sampai M4 |
| T4 | lelang (M4) `modul/lelang` | F4, F5 | sedang | todo | 0 | |
| T5 | dasbor (M5) `modul/dasbor` | F6 | rendah | todo | 0 | |
| T6 | admin (M6) `modul/admin` — kelola user ditulis ulang | F7 | sedang | todo | 0 | |
