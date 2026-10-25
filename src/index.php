<?php
// Garuda-Hub - lelang proyek IT (PHP 8.1+ native, mysqli). Semua data dummy.
declare(strict_types=1);

// ===== M1: inti =====
ini_set('display_errors', '0');
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

const ROLES = ['klien', 'developer', 'admin'];
const SELF_ROLES = ['klien', 'developer'];
const STATUS = ['terbuka', 'ditutup'];

// Peta rute: halaman => [handler, metode yang diizinkan]
const ROUTES = [
    'home'      => ['page_home', ['GET']],
    'project'   => ['page_project', ['GET']],
    'register'  => ['page_register', ['GET', 'POST']],
    'login'     => ['page_login', ['GET', 'POST']],
    'logout'    => ['page_logout', ['POST']],
    'post'      => ['page_post', ['GET', 'POST']],
    'bid'       => ['page_bid', ['POST']],
    'pick'      => ['page_pick', ['POST']],
    'dashboard' => ['page_dashboard', ['GET']],
    'admin'     => ['page_admin', ['GET', 'POST']],
];

function e(mixed $v): string { return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }
function rp(int|string $n): string { return 'Rp ' . number_format((int)$n, 0, ',', '.'); }
// Hanya URL relatif "?page=..." yang diizinkan (cegah open redirect).
function go(string $url): never {
    if (!preg_match('/^\?page=[a-z]+(&[A-Za-z0-9_=%.-]*)?$/', $url)) $url = '?page=home';
    header('Location: ' . $url);
    exit;
}
function flash(?string $msg = null, string $type = 'ok'): ?array {
    if (session_status() !== PHP_SESSION_ACTIVE) return null;
    if ($msg !== null) { $_SESSION['flash'] = [$msg, $type]; return null; }
    $f = $_SESSION['flash'] ?? null;
    unset($_SESSION['flash']);
    return $f;
}
function status_badge(string $s): string {
    return '<span class="badge ' . ($s === 'terbuka' ? 'open' : 'closed') . '">' . e($s) . '</span>';
}

function db(): mysqli {
    static $c = null;
    if ($c === null) {
        $c = new mysqli(
            getenv('GH_DB_HOST') ?: '127.0.0.1',
            getenv('GH_DB_USER') ?: 'root',
            getenv('GH_DB_PASS') ?: '',
            getenv('GH_DB_NAME') ?: 'garuda_hub',
            (int)(getenv('GH_DB_PORT') ?: 3306)
        );
        $c->set_charset('utf8mb4');
    }
    return $c;
}
// Prepared statement; SELECT -> array baris, lainnya -> jumlah baris terpengaruh.
function q(string $sql, string $types = '', array $params = []): array|int {
    $st = db()->prepare($sql);
    if ($types !== '') $st->bind_param($types, ...$params);
    $st->execute();
    $res = $st->get_result();
    if ($res instanceof mysqli_result) { $rows = $res->fetch_all(MYSQLI_ASSOC); $st->close(); return $rows; }
    $n = $st->affected_rows;
    $st->close();
    return $n;
}
function tx(callable $fn): mixed {
    $d = db();
    $d->begin_transaction();
    try {
        $r = $fn();
        $d->commit();
        return $r;
    } catch (Throwable $ex) {
        $d->rollback();
        throw $ex;
    }
}

function abort(int $code, string $msg): never {
    $GLOBALS['GH_ABORTING'] = true; // layout tidak memanggil auth saat halaman galat
    render('Galat ' . $code, '<div class="card"><h1>' . $code . '</h1><p>' . e($msg)
        . ' <a href="?page=home">Kembali ke beranda</a>.</p></div>', $code);
}

function render(string $title, string $body, int $code = 200): never {
    http_response_code($code);
    $u = empty($GLOBALS['GH_ABORTING']) ? current_user() : null;
    $f = flash();
    ?><!DOCTYPE html>
<html lang="id"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title><?= e($title) ?> - Garuda-Hub</title>
<style>
*{box-sizing:border-box}body{margin:0;font-family:system-ui,Segoe UI,Arial,sans-serif;background:#f4f6f8;color:#1f2933;line-height:1.5}
a{color:#0b5cad;text-decoration:none}a:hover{text-decoration:underline}
header{background:#7a1020;color:#fff}header .in{max-width:1000px;margin:auto;padding:12px 16px;display:flex;flex-wrap:wrap;align-items:center;gap:14px}
header a{color:#fff}header .brand{font-weight:700;font-size:1.3rem;margin-right:auto}
main{max-width:1000px;margin:20px auto;padding:0 16px}
.card{background:#fff;border-radius:8px;padding:18px;margin-bottom:16px;box-shadow:0 1px 3px rgba(0,0,0,.1)}
h1{font-size:1.5rem;margin:0 0 14px}h2{font-size:1.15rem;margin:0 0 10px}
label{display:block;margin:10px 0 4px;font-weight:600}
input,select,textarea{width:100%;padding:8px;border:1px solid #c5ccd3;border-radius:6px;font:inherit}
textarea{min-height:110px}
.btn,button{display:inline-block;background:#7a1020;color:#fff;border:0;border-radius:6px;padding:8px 16px;font:inherit;cursor:pointer}
.btn.sec,button.sec{background:#52606d}.btn.danger,button.danger{background:#b42318}.btn.sm,button.sm{padding:3px 10px;font-size:.85rem}
.btn:hover{text-decoration:none;opacity:.9}
table{width:100%;border-collapse:collapse}th,td{text-align:left;padding:8px;border-bottom:1px solid #e4e7eb;vertical-align:top}th{background:#f0f2f5}
.badge{padding:2px 9px;border-radius:12px;font-size:.8rem;color:#fff}.badge.open{background:#1a7f37}.badge.closed{background:#6b7280}.badge.win{background:#b7791f}
.flash{padding:10px 14px;border-radius:6px;margin-bottom:14px}.flash.ok{background:#d9f2e0}.flash.err{background:#fde2e0}
.muted{color:#6b7280;font-size:.9rem}.row{display:flex;gap:10px;flex-wrap:wrap}.row>*{flex:1;min-width:140px}
form.inline{display:inline}form.inline select{width:auto;padding:3px}
.winner{background:#fff8e1}.search{display:flex;gap:8px}.search input{flex:1}.search button{flex:none}
footer{text-align:center;color:#6b7280;font-size:.85rem;margin:30px 0}
</style></head><body>
<header><div class="in"><a class="brand" href="?page=home">Garuda-Hub</a>
<a href="?page=home">Proyek</a>
<?php if ($u): ?>
  <?php if ($u['role'] === 'klien'): ?><a href="?page=post">Pasang Proyek</a><?php endif; ?>
  <?php if ($u['role'] !== 'admin'): ?><a href="?page=dashboard">Dasbor</a><?php endif; ?>
  <?php if ($u['role'] === 'admin'): ?><a href="?page=admin">Admin</a><?php endif; ?>
  <span><?= e($u['nama']) ?> (<?= e($u['role']) ?>)</span>
  <form class="inline" method="post" action="?page=logout"><?= csrf_field() ?><button class="sm sec" type="submit">Keluar</button></form>
<?php else: ?><a href="?page=login">Masuk</a><a href="?page=register">Daftar</a><?php endif; ?>
</div></header>
<main>
<?php if ($f): ?><div class="flash <?= e($f[1]) ?>"><?= e($f[0]) ?></div><?php endif; ?>
<?= $body ?>
</main>
<footer>Garuda-Hub - aplikasi pembelajaran. Hanya data dummy; jangan dipasang di server publik.</footer>
</body></html><?php
    exit;
}

function internal_error(Throwable $ex): never {
    error_log('Garuda-Hub error: ' . get_class($ex) . ': ' . $ex->getMessage());
    abort(500, 'Terjadi kesalahan pada server. Silakan coba lagi nanti.');
}
set_exception_handler('internal_error');

// ===== M2: auth =====
function session_boot(): void { abort(501, 'Belum diimplementasikan.'); }
function current_user(): ?array { abort(501, 'Belum diimplementasikan.'); }
function need_role(string ...$roles): array { abort(501, 'Belum diimplementasikan.'); }
function csrf(): string { abort(501, 'Belum diimplementasikan.'); }
function csrf_field(): string { abort(501, 'Belum diimplementasikan.'); }
function check_csrf(): void { abort(501, 'Belum diimplementasikan.'); }
function auth_register(string $nama, string $email, string $pw, string $role): string { abort(501, 'Belum diimplementasikan.'); }
function auth_login(string $email, string $pw): string { abort(501, 'Belum diimplementasikan.'); }
function auth_logout(): void { abort(501, 'Belum diimplementasikan.'); }
function page_register(): void { abort(501, 'Belum diimplementasikan.'); }
function page_login(): void { abort(501, 'Belum diimplementasikan.'); }
function page_logout(): void { abort(501, 'Belum diimplementasikan.'); }

// ===== M3: proyek =====
function project_find(int $id): ?array { abort(501, 'Belum diimplementasikan.'); }
function project_list(string $cari): array { abort(501, 'Belum diimplementasikan.'); }
function project_is_open(array $p): bool { abort(501, 'Belum diimplementasikan.'); }
function project_create(int $clientId, array $in): string { abort(501, 'Belum diimplementasikan.'); }
function page_home(): void { abort(501, 'Belum diimplementasikan.'); }
function page_project(): void { abort(501, 'Belum diimplementasikan.'); }
function page_post(): void { abort(501, 'Belum diimplementasikan.'); }

// ===== M4: lelang =====
function bids_for_project(int $pid): array { abort(501, 'Belum diimplementasikan.'); }
function bid_create(int $devId, int $pid, int $harga, int $durasi, string $proposal): string { abort(501, 'Belum diimplementasikan.'); }
function bid_pick(int $clientId, int $pid, int $bidId): string { abort(501, 'Belum diimplementasikan.'); }
function page_bid(): void { abort(501, 'Belum diimplementasikan.'); }
function page_pick(): void { abort(501, 'Belum diimplementasikan.'); }

// ===== M5: dasbor =====
function client_projects(int $uid): array { abort(501, 'Belum diimplementasikan.'); }
function developer_bids(int $uid): array { abort(501, 'Belum diimplementasikan.'); }
function page_dashboard(): void { abort(501, 'Belum diimplementasikan.'); }

// ===== M6: admin =====
function admin_set_role(int $actorId, int $uid, string $role): string { abort(501, 'Belum diimplementasikan.'); }
function admin_delete_user(int $actorId, int $uid): string { abort(501, 'Belum diimplementasikan.'); }
function admin_delete_project(int $pid): string { abort(501, 'Belum diimplementasikan.'); }
function page_admin(): void { abort(501, 'Belum diimplementasikan.'); }

// ===== M1: router =====
$page = (string)($_GET['page'] ?? 'home');
if (!isset(ROUTES[$page])) abort(404, 'Halaman tidak ditemukan.');
[$handler, $methods] = ROUTES[$page];
if (!in_array($_SERVER['REQUEST_METHOD'] ?? 'GET', $methods, true)) {
    header('Allow: ' . implode(', ', $methods));
    abort(405, 'Metode tidak diizinkan.');
}
session_boot();
if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') check_csrf();
$handler();
