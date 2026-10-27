# Garuda-Hub (Laravel)

Menulis ulang codenya cuy, gak onok seng kuubah fitur web `garuda-hub-legacy` (PHP native) ke Laravel 13 dengan arsitektur MVC dan Eloquent ORM.

## Fitur
- Daftar dan login (klien atau developer), logout
- Klien: pasang, edit, dan hapus proyek miliknya
- Developer: ajukan penawaran (satu per proyek) dan tarik penawaran
- Klien memilih pemenang (transaksi database: pemenang diterima, lainnya ditolak, proyek ditutup)
- Admin dapat mengedit dan menghapus semua proyek

## Skema database
- `users` (name, email, password, role: klien/developer/admin)
- `projects` (client_id → users, judul, deskripsi, kategori, budget_min, budget_max, batas_lelang, status)
- `bids` (project_id → projects, developer_id → users, harga, durasi, proposal, status; unique per project dan developer)

## Cara menjalankan
1. `cd laravel-app`
2. `composer install`
3. `cp .env.example .env` (di CMD Windows: `copy .env.example .env`) lalu `php artisan key:generate`
4. Buat database MySQL/MariaDB (misalnya `laravel_app`), lalu isi di `.env`:
   `DB_CONNECTION=mysql` (pakai `mariadb` kalau server-nya MariaDB), `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`
5. `php artisan migrate:fresh --seed`
6. `php artisan serve`, lalu buka `http://localhost:8000`

## Akun dummy (password semuanya: `password`)
- Admin: `admin@example.com`
- Klien: `agus@example.com`, `hambali@example.com`, `basnya@example.com`
- Developer: `ngoding@example.com`, `stackoverflow@example.com`, `copas@example.com`, `debug@example.com`
