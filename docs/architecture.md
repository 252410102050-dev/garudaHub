<!-- maks 120 baris -->
# Arsitektur – Garuda-Hub (fase build, mode evolve)

## 0. Rekomendasi mode
**evolve.** Struktur prototype sudah rapi: helper terpusat, routing `?page=`, stack sama dengan rencana final, dan praktik Opsi A sudah dipakai. Utang teknisnya kecil: skema belum punya UNIQUE/FK/ENUM, peran di sesi tidak diperbarui, dan ada race condition di F5. Sesuai aturan, F1 (auth) dan bagian data pribadi (tabel `users`, kelola user F7) ditulis ulang. Detail: `salvage.md`.

## 1. Stack final
- PHP 8.1+ native (`declare(strict_types=1)`), ekstensi `mysqli` + `mbstring`, MySQL 8 / MariaDB 10.5+ (InnoDB, utf8mb4).
- Tepat 2 file aplikasi: `src/index.php` (routing `?page=`, CSS inline, tanpa CDN/JS eksternal) dan `src/database.sql` (skema + data dummy).
- Kredensial DB dari `getenv('GH_DB_HOST|GH_DB_PORT|GH_DB_USER|GH_DB_PASS|GH_DB_NAME')`, tidak ada rahasia di repo.
- Tanpa framework, Composer, payment gateway, atau tes otomatis (spec). Verifikasi: `php -l` + uji manual per modul.
- **Modul = bagian berlabel di dalam `src/index.php`** (penanda `// ===== M<n>: <nama> =====`), ditambah `src/database.sql` sebagai M0. Karena semua modul ada di satu file, build harus **berurutan** (tanpa paralel) agar branch `modul/<nama>` tidak konflik.

## 2. Modul (urut berdasarkan ketergantungan)
| # | Modul (branch) | Fitur | Kesulitan | Bergantung pada |
| --- | --- | --- | --- | --- |
| M0 | `skema` – `src/database.sql` | F1–F7 (data) | rendah | – |
| M1 | `inti` – config, db, helper, layout, router, galat | semua | sedang | M0 |
| M2 | `auth` – sesi, CSRF, peran, daftar/masuk/keluar | F1 | tinggi | M1 |
| M3 | `proyek` – daftar/cari/detail, pasang proyek | F2, F3 | sedang | M2 |
| M4 | `lelang` – ajukan penawaran, pilih pemenang | F4, F5 | sedang | M3 |
| M5 | `dasbor` – proyek saya / penawaran saya | F6 | rendah | M4 |
| M6 | `admin` – kelola user & proyek | F7 | sedang | M2, M3 |

## 3. Kontrak antarmuka
Stub yang dibuat **lebih dulu** (di branch `modul/inti`): semua signature di bawah ini dengan isi `abort(501, 'Belum diimplementasikan.')`, plus peta rute lengkap. Setiap modul berikutnya hanya mengganti isi stub miliknya.

**Tipe/konstanta (M1):**
- `const ROLES = ['klien','developer','admin'];` · `const SELF_ROLES = ['klien','developer'];` · `const STATUS = ['terbuka','ditutup'];`
- User sesi: `array{id:int, nama:string, role:'klien'|'developer'|'admin'}`
- Baris proyek: kolom tabel `projects` (+ `klien:string`, `jml:int` jika di-join); baris bid: kolom `bids` (+ `dev:string`).
- Error domain dikembalikan sebagai `string` pesan bahasa Indonesia (`''` = sukses), bukan exception.

**M0 skema** (refactor dari `prototype/mocks/database.sql`):
- `users`: `email` **UNIQUE**, `role ENUM('klien','developer','admin')`, `password VARCHAR(255)`.
- `projects`: `status ENUM('terbuka','ditutup')`, FK `client_id → users(id) ON DELETE CASCADE`, CHECK `budget_max >= budget_min >= 0`, INDEX (`status`), (`client_id`).
- `bids`: FK `project_id → projects ON DELETE CASCADE`, FK `developer_id → users ON DELETE CASCADE`, **UNIQUE (`project_id`,`developer_id`)**, CHECK `harga>0 AND durasi>0`.
- Data dummy: hanya data fiktif, domain `example.com`, password = hash bcrypt (tanpa plaintext di komentar kecuali kredensial demo di README).

**M1 inti:**
- `db(): mysqli` · `q(string $sql, string $types = '', array $params = []): array|int` (selalu prepared statement)
- `tx(callable $fn): mixed` (begin → commit, rollback lalu lempar ulang bila ada exception)
- `e(mixed $v): string` · `rp(int|string $n): string` · `go(string $url): never` (hanya URL relatif `?page=...`)
- `flash(?string $msg = null, string $type = 'ok'): ?array` · `status_badge(string $s): string`
- `render(string $title, string $body, int $code = 200): never` · `abort(int $code, string $msg): never`
- Router: `ROUTES = ['home'=>['page_home',['GET']], 'project'=>…, 'register'=>…, 'login'=>…, 'logout'=>['page_logout',['POST']], 'post'=>…, 'bid'=>['page_bid',['POST']], 'pick'=>['page_pick',['POST']], 'dashboard'=>…, 'admin'=>…]`; metode lain → 405; rute tak dikenal → 404.
- Penanganan galat: `mysqli_sql_exception` dan `Throwable` → `error_log` + halaman 500 generik; `display_errors=0`.

**M2 auth (tulis ulang):**
- `session_boot(): void` – cookie `HttpOnly`, `SameSite=Lax`, `Secure` bila HTTPS, `session.use_strict_mode=1`; dipanggil sebelum router.
- `current_user(): ?array` – sesi hanya menyimpan `uid`; nama & peran **dimuat ulang dari DB** per request (user terhapus → sesi dihapus).
- `need_role(string ...$roles): array` – belum login → redirect `?page=login`; peran salah → 403.
- `csrf(): string` · `csrf_field(): string` · `check_csrf(): void` (semua POST, `hash_equals`).
- `auth_register(string $nama, string $email, string $pw, string $role): string` – role ∈ `SELF_ROLES`, password ≥ 6 karakter (keputusan pemilik), `password_hash(PASSWORD_DEFAULT)`; email duplikat ditangkap dari UNIQUE.
- `auth_login(string $email, string $pw): string` – `password_verify` (verifikasi hash dummy bila email tidak ada, agar waktu respons setara), `session_regenerate_id(true)`, `password_needs_rehash`.
- `auth_logout(): void` – hanya POST + CSRF.
- Handler: `page_register()`, `page_login()`, `page_logout()`.

**M3 proyek:** `project_find(int $id): ?array` · `project_list(string $cari): array` · `project_is_open(array $p): bool` (status `terbuka` dan `batas_lelang >= hari ini`) · `project_create(int $clientId, array $in): string` · handler `page_home()`, `page_project()`, `page_post()`.

**M4 lelang:** `bids_for_project(int $pid): array` · `bid_create(int $devId, int $pid, int $harga, int $durasi, string $proposal): string` · `bid_pick(int $clientId, int $pid, int $bidId): string` – di dalam `tx()` dengan `SELECT … FOR UPDATE` pada proyek, cek kepemilikan + status terbuka + bid milik proyek. Handler `page_bid()`, `page_pick()`.

**M5 dasbor:** `client_projects(int $uid): array` · `developer_bids(int $uid): array` · `page_dashboard()` (peran klien/developer).

**M6 admin (bagian user ditulis ulang):** `admin_set_role(int $actorId, int $uid, string $role): string` · `admin_delete_user(int $actorId, int $uid): string` · `admin_delete_project(int $pid): string` · `page_admin()`. Admin tidak bisa mengubah peran atau menghapus akun sendiri; hapus memakai FK CASCADE.

## 4. Threat model ringkas
Aset: hash password, email/nama user (dummy), sesi/peran, integritas proyek & pemenang lelang, kredensial DB (env).
Aktor: tamu, klien, developer, admin, penyerang luar (mahasiswa penguji di lokal).

| Modul | Ancaman utama | Mitigasi (Opsi A) |
| --- | --- | --- |
| M0 | Data ganda/yatim, kebocoran password | UNIQUE, FK CASCADE, ENUM/CHECK; hanya hash bcrypt dummy |
| M1 | SQLi, XSS, open redirect, bocor pesan galat | `q()` selalu prepared statement; `e()` di setiap output; `go()` hanya relatif; galat generik |
| M2 | Brute force, enumerasi user, fiksasi/pencurian sesi, CSRF, eskalasi peran saat daftar, sesi basi setelah peran diubah | `password_hash`, waktu respons setara, `regenerate_id`, flag cookie, CSRF semua POST, whitelist `SELF_ROLES`, muat ulang peran per request. Rate limit **tidak** dibuat (di luar cakupan, dicatat di VULNERABILITIES) |
| M3 | IDOR saat membuat proyek atas nama orang lain, XSS deskripsi, input tak valid | `client_id` dari sesi, bukan form; validasi panjang/tanggal/budget; `e()` + `nl2br` |
| M4 | Bid setelah ditutup, bid ganda, memilih pemenang di proyek orang lain, race dua pemenang | Cek `project_is_open` sisi server, UNIQUE bid, cek kepemilikan di SQL, transaksi + `FOR UPDATE` |
| M5 | Melihat data user lain | Query difilter `uid` dari sesi |
| M6 | Akses non-admin, CSRF aksi hapus, admin mengunci diri sendiri | `need_role('admin')`, CSRF, larangan aksi pada diri sendiri, whitelist `ROLES` |

## 5. Pembayaran
Di luar cakupan (spec). Tidak ada data kartu, tidak ada tabel atau form pembayaran. Jika kelak ditambah: wajib pakai tokenisasi payment gateway (form/hosted field milik gateway), server hanya menyimpan token/ID transaksi, dan data kartu tidak pernah menyentuh server.
