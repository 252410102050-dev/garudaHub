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
