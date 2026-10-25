<!-- satu baris per keputusan. Pemilik: siapa pun. Dibaca: architect, orchestrator -->
# Log Keputusan

| Tanggal | Keputusan | Alasan |
| --- | --- | --- |
| 2026-10-19 | Kode prototype di `prototype/`, kode final di `src/` | Hook dapat memblokir tulis ke `src/` sebelum gerbang `/approve` |
| 2026-10-25 | Repo git diinisialisasi (branch `main`) dengan `.gitignore` untuk rahasia | Build butuh branch `modul/<nama>` dan diff untuk reviewer |
| 2026-10-25 | prototyper/prototyper-lite tidak diberi pembatas tulis `prototype/` | Keputusan pemilik; `src/` dan `APPROVED.md` tetap dijaga `guard-project.sh` |
| 2026-10-25 | Garuda-Hub: lelang proyek IT, 1 `index.php` + 1 `database.sql`, sengaja tanpa pengamanan | Studi kasus pembelajaran keamanan mahasiswa (keputusan pemilik) |
| 2026-10-25 | Alur prototype/ → /approve → src/; lisensi MIT | Konfirmasi pemilik |
| 2026-10-25 | `.claude/`, `CLAUDE.md`, `SETUP.md`, `docs/VULNERABILITIES.md` di-.gitignore | Tidak dipublikasikan; mahasiswa mencari celah sendiri |
| 2026-10-25 | Opsi A: bangun aplikasi normal (tanpa sesi hardening keamanan). `index.php` gagal ditulis sebagai versi sengaja-rentan (terblokir filter) | Keputusan pemilik setelah penulisan versi rentan terhenti |
| 2026-10-25 | `VULNERABILITIES.md` = daftar kemungkinan celah + lokasi di kode (bahan analisis mahasiswa), memetakan kondisi kode nyata | Keputusan pemilik |
| 2026-10-25 | Kredensial DB dibaca dari env var (getenv), tidak di-hardcode di index.php | Mencegah rahasia ter-commit ke GitHub |
| 2026-10-25 | Password dummy di `database.sql` diganti hash bcrypt; app memakai `password_verify`, CSRF token sesi, prepared statement | Aplikasi normal sesuai Opsi A |
| 2026-10-25 | Gerbang dibuka: /approve evolve, fitur F1–F7 disetujui | Prototype berfungsi penuh & sesuai spec; auth (F1) tetap ditulis ulang |
| 2026-10-25 | Rekomendasi architect: evolve | Struktur prototype rapi, stack sama dengan rencana final, utang teknis kecil (skema tanpa constraint, peran sesi basi, race F5) |
| 2026-10-25 | Tetap 2 file; modul M0–M6 = `src/database.sql` + bagian berlabel di `src/index.php`, dibangun berurutan (tanpa paralel) | Patuh spec "tepat 2 file"; cegah konflik merge antar-branch `modul/<nama>` |
| 2026-10-25 | Data pribadi (tabel/dummy `users`, kelola user di F7) ikut ditulis ulang bersama F1 | Aturan keamanan CLAUDE.md |
| 2026-10-25 | Logout via POST + CSRF; peran dimuat ulang dari DB per request; password minimal 6 karakter (direvisi pemilik); skema pakai UNIQUE/FK CASCADE/ENUM | Praktik standar Opsi A, menutup celah yang ada di prototype (lihat `salvage.md`) |
| 2026-10-25 | Tanpa header X-Frame-Options/CSP (di luar Opsi A) | Keputusan pemilik via Telegram |
| 2026-10-25 | Password minimal tetap 6 karakter | Keputusan pemilik via Telegram |
| 2026-10-25 | Default DB root/password kosong dipertahankan bila env var kosong; README wajib beri peringatan | Keputusan pemilik via Telegram |
| 2026-10-25 | Tes integrasi dijalankan pada MariaDB lokal yang sudah ada (port 3306, DB garuda_hub, data dummy diimpor ulang dari `src/database.sql`); kredensial hanya lewat env var, tidak ditulis ke berkas | Izin pemilik via Telegram; 28 skenario alur + 9 skenario admin lulus |
