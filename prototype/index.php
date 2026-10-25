<?php
// Garuda-Hub - prototype lelang proyek IT (PHP 8 + mysqli). Semua data dummy.
declare(strict_types=1);
session_start();

$DB_HOST = getenv('GH_DB_HOST') ?: '127.0.0.1';
$DB_PORT = (int)(getenv('GH_DB_PORT') ?: 3306);
$DB_USER = getenv('GH_DB_USER') ?: 'root';
$DB_PASS = getenv('GH_DB_PASS') ?: '';
$DB_NAME = getenv('GH_DB_NAME') ?: 'garuda_hub';

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

// ---------- helper ----------
function e($v): string { return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }
function rp($n): string { return 'Rp ' . number_format((int)$n, 0, ',', '.'); }
function go(string $url): never { header('Location: ' . $url); exit; }
function flash(?string $msg = null, string $type = 'ok'): ?array {
    if ($msg !== null) { $_SESSION['flash'] = [$msg, $type]; return null; }
    $f = $_SESSION['flash'] ?? null; unset($_SESSION['flash']); return $f;
}
function db(): mysqli {
    global $DB_HOST, $DB_USER, $DB_PASS, $DB_NAME, $DB_PORT;
    static $c = null;
    if ($c === null) { $c = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME, $DB_PORT); $c->set_charset('utf8mb4'); }
    return $c;
}
// Jalankan prepared statement; SELECT -> array baris, lainnya -> jumlah baris terpengaruh.
function q(string $sql, string $types = '', array $params = []) {
    $st = db()->prepare($sql);
    if ($types !== '') $st->bind_param($types, ...$params);
    $st->execute();
    $res = $st->get_result();
    if ($res instanceof mysqli_result) { $rows = $res->fetch_all(MYSQLI_ASSOC); $st->close(); return $rows; }
    $n = $st->affected_rows; $st->close(); return $n;
}
function user(): ?array { return $_SESSION['user'] ?? null; }
function need_role(string ...$roles): array {
    $u = user();
    if (!$u) { flash('Silakan login terlebih dahulu.', 'err'); go('?page=login'); }
    if (!in_array($u['role'], $roles, true)) { http_response_code(403); render('Akses ditolak', '<div class="card"><p>Anda tidak berhak mengakses halaman ini.</p></div>'); }
    return $u;
}
function csrf(): string { return $_SESSION['csrf'] ??= bin2hex(random_bytes(32)); }
function csrf_field(): string { return '<input type="hidden" name="csrf" value="' . e(csrf()) . '">'; }
function check_csrf(): void {
    if (!hash_equals(csrf(), (string)($_POST['csrf'] ?? ''))) {
        http_response_code(400); render('Permintaan tidak valid', '<div class="card"><p>Token formulir tidak valid. Muat ulang halaman dan coba lagi.</p></div>');
    }
}
function status_badge(string $s): string { return '<span class="badge ' . ($s === 'terbuka' ? 'open' : 'closed') . '">' . e($s) . '</span>'; }

function render(string $title, string $body): never {
    $u = user(); $f = flash();
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
  <a href="?page=logout">Keluar</a>
<?php else: ?><a href="?page=login">Masuk</a><a href="?page=register">Daftar</a><?php endif; ?>
</div></header>
<main>
<?php if ($f): ?><div class="flash <?= e($f[1]) ?>"><?= e($f[0]) ?></div><?php endif; ?>
<?= $body ?>
</main>
<footer>Garuda-Hub - prototype pembelajaran. Hanya data dummy; jangan dipasang di server publik.</footer>
</body></html><?php
    exit;
}

// ---------- routing ----------
$page = (string)($_GET['page'] ?? 'home');
$isPost = $_SERVER['REQUEST_METHOD'] === 'POST';
if ($isPost) check_csrf();

try {
    switch ($page) {

    case 'home': // F3
        $s = trim((string)($_GET['q'] ?? ''));
        if ($s !== '') {
            $like = '%' . $s . '%';
            $rows = q("SELECT p.*, u.nama AS klien, (SELECT COUNT(*) FROM bids b WHERE b.project_id=p.id) AS jml
                       FROM projects p JOIN users u ON u.id=p.client_id
                       WHERE p.judul LIKE ? OR p.kategori LIKE ? ORDER BY p.id DESC", 'ss', [$like, $like]);
        } else {
            $rows = q("SELECT p.*, u.nama AS klien, (SELECT COUNT(*) FROM bids b WHERE b.project_id=p.id) AS jml
                       FROM projects p JOIN users u ON u.id=p.client_id ORDER BY p.id DESC");
        }
        $h = '<div class="card"><h1>Daftar Proyek</h1><form class="search" method="get"><input type="hidden" name="page" value="home">'
           . '<input name="q" placeholder="Cari judul atau kategori..." value="' . e($s) . '"><button>Cari</button></form>'
           . ($s !== '' ? '<p class="muted">Hasil pencarian untuk: <b>' . e($s) . '</b> (' . count($rows) . ' proyek)</p>' : '') . '</div>';
        if (!$rows) $h .= '<div class="card"><p>Belum ada proyek yang cocok.</p></div>';
        foreach ($rows as $p) {
            $h .= '<div class="card"><h2><a href="?page=project&id=' . (int)$p['id'] . '">' . e($p['judul']) . '</a> ' . status_badge($p['status']) . '</h2>'
                . '<p class="muted">Kategori: ' . e($p['kategori']) . ' | Klien: ' . e($p['klien']) . ' | Budget: ' . rp($p['budget_min']) . ' - ' . rp($p['budget_max'])
                . ' | Batas: ' . e($p['batas_lelang']) . ' | ' . (int)$p['jml'] . ' penawaran</p>'
                . '<p>' . e(mb_strimwidth($p['deskripsi'], 0, 160, '...')) . '</p></div>';
        }
        render('Proyek', $h);

    case 'project': // F3 + form F4 + F5
        $id = (int)($_GET['id'] ?? 0);
        $r = q("SELECT p.*, u.nama AS klien FROM projects p JOIN users u ON u.id=p.client_id WHERE p.id=?", 'i', [$id]);
        if (!$r) { http_response_code(404); render('Tidak ditemukan', '<div class="card"><p>Proyek tidak ditemukan.</p></div>'); }
        $p = $r[0]; $u = user();
        $bids = q("SELECT b.*, u.nama AS dev FROM bids b JOIN users u ON u.id=b.developer_id WHERE b.project_id=? ORDER BY b.harga ASC", 'i', [$id]);
        $owner = $u && $u['role'] === 'klien' && (int)$u['id'] === (int)$p['client_id'];
        $open = $p['status'] === 'terbuka' && $p['batas_lelang'] >= date('Y-m-d');
        $h = '<div class="card"><h1>' . e($p['judul']) . ' ' . status_badge($p['status']) . '</h1>'
           . '<p class="muted">Kategori: ' . e($p['kategori']) . ' | Klien: ' . e($p['klien']) . ' | Budget: ' . rp($p['budget_min']) . ' - ' . rp($p['budget_max']) . ' | Batas lelang: ' . e($p['batas_lelang']) . '</p>'
           . '<p>' . nl2br(e($p['deskripsi'])) . '</p></div>';
        $h .= '<div class="card"><h2>Penawaran (' . count($bids) . ')</h2>';
        if (!$bids) $h .= '<p>Belum ada penawaran.</p>';
        else {
            $h .= '<table><tr><th>Developer</th><th>Harga</th><th>Durasi</th><th>Proposal</th><th></th></tr>';
            foreach ($bids as $b) {
                $h .= '<tr class="' . ($b['is_winner'] ? 'winner' : '') . '"><td>' . e($b['dev']) . ($b['is_winner'] ? ' <span class="badge win">Pemenang</span>' : '') . '</td>'
                    . '<td>' . rp($b['harga']) . '</td><td>' . (int)$b['durasi'] . ' hari</td><td>' . nl2br(e($b['proposal'])) . '</td><td>';
                if ($owner && $p['status'] === 'terbuka') {
                    $h .= '<form method="post" action="?page=pick" onsubmit="return confirm(\'Pilih penawaran ini sebagai pemenang?\')">' . csrf_field()
                        . '<input type="hidden" name="project_id" value="' . (int)$p['id'] . '"><input type="hidden" name="bid_id" value="' . (int)$b['id'] . '">'
                        . '<button class="sm">Pilih pemenang</button></form>';
                }
                $h .= '</td></tr>';
            }
            $h .= '</table>';
        }
        $h .= '</div>';
        if ($u && $u['role'] === 'developer') {
            if ($open) {
                $h .= '<div class="card"><h2>Ajukan Penawaran</h2><form method="post" action="?page=bid">' . csrf_field()
                    . '<input type="hidden" name="project_id" value="' . (int)$p['id'] . '">'
                    . '<div class="row"><div><label>Harga (Rp)</label><input type="number" name="harga" min="1" required></div>'
                    . '<div><label>Durasi (hari)</label><input type="number" name="durasi" min="1" required></div></div>'
                    . '<label>Proposal</label><textarea name="proposal" required></textarea><p><button>Kirim penawaran</button></p></form></div>';
            } else $h .= '<div class="card"><p class="muted">Lelang sudah ditutup; penawaran tidak dapat diajukan.</p></div>';
        } elseif (!$u && $open) {
            $h .= '<div class="card"><p><a href="?page=login">Masuk</a> sebagai developer untuk mengajukan penawaran.</p></div>';
        }
        render($p['judul'], $h);

    case 'register': // F1
        $err = ''; $v = ['nama' => '', 'email' => '', 'role' => 'klien'];
        if ($isPost) {
            $v = ['nama' => trim((string)($_POST['nama'] ?? '')), 'email' => trim((string)($_POST['email'] ?? '')), 'role' => (string)($_POST['role'] ?? '')];
            $pw = (string)($_POST['password'] ?? '');
            if ($v['nama'] === '' || mb_strlen($v['nama']) > 100) $err = 'Nama wajib diisi (maks. 100 karakter).';
            elseif (!filter_var($v['email'], FILTER_VALIDATE_EMAIL) || strlen($v['email']) > 100) $err = 'Email tidak valid.';
            elseif (!in_array($v['role'], ['klien', 'developer'], true)) $err = 'Peran tidak valid.';
            elseif (strlen($pw) < 6) $err = 'Password minimal 6 karakter.';
            elseif (q("SELECT id FROM users WHERE email=?", 's', [$v['email']])) $err = 'Email sudah terdaftar.';
            else {
                q("INSERT INTO users (nama,email,password,role) VALUES (?,?,?,?)", 'ssss', [$v['nama'], $v['email'], password_hash($pw, PASSWORD_DEFAULT), $v['role']]);
                flash('Pendaftaran berhasil. Silakan masuk.'); go('?page=login');
            }
        }
        $h = '<div class="card" style="max-width:480px;margin:auto"><h1>Daftar Akun</h1>' . ($err ? '<div class="flash err">' . e($err) . '</div>' : '')
           . '<form method="post" action="?page=register">' . csrf_field()
           . '<label>Nama</label><input name="nama" required value="' . e($v['nama']) . '">'
           . '<label>Email</label><input type="email" name="email" required value="' . e($v['email']) . '">'
           . '<label>Password</label><input type="password" name="password" required>'
           . '<label>Daftar sebagai</label><select name="role"><option value="klien"' . ($v['role'] === 'klien' ? ' selected' : '') . '>Klien</option><option value="developer"' . ($v['role'] === 'developer' ? ' selected' : '') . '>Developer</option></select>'
           . '<p><button>Daftar</button></p></form></div>';
        render('Daftar', $h);

    case 'login': // F1
        $err = ''; $email = '';
        if ($isPost) {
            $email = trim((string)($_POST['email'] ?? ''));
            $r = q("SELECT * FROM users WHERE email=?", 's', [$email]);
            if ($r && password_verify((string)($_POST['password'] ?? ''), $r[0]['password'])) {
                session_regenerate_id(true);
                $_SESSION['user'] = ['id' => (int)$r[0]['id'], 'nama' => $r[0]['nama'], 'role' => $r[0]['role']];
                flash('Selamat datang, ' . $r[0]['nama'] . '.');
                go($r[0]['role'] === 'admin' ? '?page=admin' : '?page=dashboard');
            }
            $err = 'Email atau password salah.';
        }
        $h = '<div class="card" style="max-width:480px;margin:auto"><h1>Masuk</h1>' . ($err ? '<div class="flash err">' . e($err) . '</div>' : '')
           . '<form method="post" action="?page=login">' . csrf_field()
           . '<label>Email</label><input type="email" name="email" required value="' . e($email) . '">'
           . '<label>Password</label><input type="password" name="password" required>'
           . '<p><button>Masuk</button> <a href="?page=register">Belum punya akun?</a></p></form></div>';
        render('Masuk', $h);

    case 'logout':
        $_SESSION = [];
        if (ini_get('session.use_cookies')) { $c = session_get_cookie_params(); setcookie(session_name(), '', time() - 3600, $c['path'], $c['domain'], $c['secure'], $c['httponly']); }
        session_destroy();
        session_start(); flash('Anda sudah keluar.');
        go('?page=home');

    case 'post': // F2
        $u = need_role('klien');
        $err = ''; $v = ['judul' => '', 'deskripsi' => '', 'kategori' => '', 'bmin' => '', 'bmax' => '', 'batas' => ''];
        if ($isPost) {
            foreach ($v as $k => $_) $v[$k] = trim((string)($_POST[$k] ?? ''));
            $bmin = (int)$v['bmin']; $bmax = (int)$v['bmax'];
            $d = DateTime::createFromFormat('Y-m-d', $v['batas']);
            if ($v['judul'] === '' || mb_strlen($v['judul']) > 200 || $v['deskripsi'] === '' || $v['kategori'] === '' || mb_strlen($v['kategori']) > 50) $err = 'Judul, deskripsi, dan kategori wajib diisi dengan panjang wajar.';
            elseif ($bmin < 0 || $bmax < $bmin) $err = 'Budget tidak valid (maks harus >= min).';
            elseif (!$d || $d->format('Y-m-d') !== $v['batas'] || $v['batas'] < date('Y-m-d')) $err = 'Batas lelang harus tanggal yang valid dan belum lewat.';
            else {
                q("INSERT INTO projects (client_id,judul,deskripsi,kategori,budget_min,budget_max,batas_lelang) VALUES (?,?,?,?,?,?,?)",
                  'isssiis', [$u['id'], $v['judul'], $v['deskripsi'], $v['kategori'], $bmin, $bmax, $v['batas']]);
                flash('Proyek berhasil dipasang.'); go('?page=dashboard');
            }
        }
        $h = '<div class="card"><h1>Pasang Proyek</h1>' . ($err ? '<div class="flash err">' . e($err) . '</div>' : '')
           . '<form method="post" action="?page=post">' . csrf_field()
           . '<label>Judul</label><input name="judul" required value="' . e($v['judul']) . '">'
           . '<label>Deskripsi</label><textarea name="deskripsi" required>' . e($v['deskripsi']) . '</textarea>'
           . '<label>Kategori</label><input name="kategori" placeholder="Web, Mobile, Desain, Data, AI..." required value="' . e($v['kategori']) . '">'
           . '<div class="row"><div><label>Budget minimum (Rp)</label><input type="number" name="bmin" min="0" required value="' . e($v['bmin']) . '"></div>'
           . '<div><label>Budget maksimum (Rp)</label><input type="number" name="bmax" min="0" required value="' . e($v['bmax']) . '"></div>'
           . '<div><label>Batas lelang</label><input type="date" name="batas" required value="' . e($v['batas']) . '"></div></div>'
           . '<p><button>Pasang proyek</button></p></form></div>';
        render('Pasang Proyek', $h);

    case 'bid': // F4 (POST)
        $u = need_role('developer');
        if (!$isPost) go('?page=home');
        $pid = (int)($_POST['project_id'] ?? 0); $harga = (int)($_POST['harga'] ?? 0); $durasi = (int)($_POST['durasi'] ?? 0);
        $prop = trim((string)($_POST['proposal'] ?? ''));
        $r = q("SELECT status, batas_lelang FROM projects WHERE id=?", 'i', [$pid]);
        if (!$r) { flash('Proyek tidak ditemukan.', 'err'); go('?page=home'); }
        if ($r[0]['status'] !== 'terbuka' || $r[0]['batas_lelang'] < date('Y-m-d')) flash('Lelang sudah ditutup.', 'err');
        elseif ($harga < 1 || $durasi < 1 || $prop === '') flash('Harga, durasi, dan proposal wajib diisi dengan benar.', 'err');
        elseif (q("SELECT id FROM bids WHERE project_id=? AND developer_id=?", 'ii', [$pid, $u['id']])) flash('Anda sudah mengajukan penawaran untuk proyek ini.', 'err');
        else {
            q("INSERT INTO bids (project_id,developer_id,harga,durasi,proposal) VALUES (?,?,?,?,?)", 'iiiis', [$pid, $u['id'], $harga, $durasi, $prop]);
            flash('Penawaran berhasil dikirim.');
        }
        go('?page=project&id=' . $pid);

    case 'pick': // F5 (POST)
        $u = need_role('klien');
        if (!$isPost) go('?page=home');
        $pid = (int)($_POST['project_id'] ?? 0); $bid = (int)($_POST['bid_id'] ?? 0);
        $r = q("SELECT status FROM projects WHERE id=? AND client_id=?", 'ii', [$pid, $u['id']]); // cek kepemilikan
        if (!$r) { http_response_code(403); render('Akses ditolak', '<div class="card"><p>Anda bukan pemilik proyek ini.</p></div>'); }
        if ($r[0]['status'] !== 'terbuka') flash('Proyek sudah ditutup.', 'err');
        elseif (!q("SELECT id FROM bids WHERE id=? AND project_id=?", 'ii', [$bid, $pid])) flash('Penawaran tidak valid.', 'err');
        else {
            $db = db(); $db->begin_transaction();
            q("UPDATE bids SET is_winner=1 WHERE id=? AND project_id=?", 'ii', [$bid, $pid]);
            q("UPDATE projects SET status='ditutup' WHERE id=?", 'i', [$pid]);
            $db->commit();
            flash('Pemenang dipilih; proyek ditutup.');
        }
        go('?page=project&id=' . $pid);

    case 'dashboard': // F6
        $u = need_role('klien', 'developer');
        if ($u['role'] === 'klien') {
            $rows = q("SELECT p.*, (SELECT COUNT(*) FROM bids b WHERE b.project_id=p.id) AS jml FROM projects p WHERE p.client_id=? ORDER BY p.id DESC", 'i', [$u['id']]);
            $h = '<div class="card"><h1>Proyek Saya</h1><p><a class="btn" href="?page=post">+ Pasang proyek</a></p>';
            if (!$rows) $h .= '<p>Anda belum memasang proyek.</p>';
            else {
                $h .= '<table><tr><th>Judul</th><th>Status</th><th>Batas</th><th>Penawaran</th></tr>';
                foreach ($rows as $p) $h .= '<tr><td><a href="?page=project&id=' . (int)$p['id'] . '">' . e($p['judul']) . '</a></td><td>' . status_badge($p['status']) . '</td><td>' . e($p['batas_lelang']) . '</td><td>' . (int)$p['jml'] . '</td></tr>';
                $h .= '</table>';
            }
        } else {
            $rows = q("SELECT b.*, p.judul, p.status FROM bids b JOIN projects p ON p.id=b.project_id WHERE b.developer_id=? ORDER BY b.id DESC", 'i', [$u['id']]);
            $h = '<div class="card"><h1>Penawaran Saya</h1>';
            if (!$rows) $h .= '<p>Anda belum mengajukan penawaran. <a href="?page=home">Cari proyek</a>.</p>';
            else {
                $h .= '<table><tr><th>Proyek</th><th>Harga</th><th>Durasi</th><th>Hasil</th></tr>';
                foreach ($rows as $b) {
                    $hasil = $b['is_winner'] ? '<span class="badge win">Menang</span>' : ($b['status'] === 'terbuka' ? 'Menunggu' : 'Tidak terpilih');
                    $h .= '<tr><td><a href="?page=project&id=' . (int)$b['project_id'] . '">' . e($b['judul']) . '</a></td><td>' . rp($b['harga']) . '</td><td>' . (int)$b['durasi'] . ' hari</td><td>' . $hasil . '</td></tr>';
                }
                $h .= '</table>';
            }
        }
        render('Dasbor', $h . '</div>');

    case 'admin': // F7
        $u = need_role('admin');
        if ($isPost) {
            $act = (string)($_POST['act'] ?? ''); $id = (int)($_POST['id'] ?? 0);
            if ($act === 'role' && in_array($_POST['role'] ?? '', ['klien', 'developer', 'admin'], true)) {
                if ($id === $u['id']) flash('Tidak dapat mengubah peran akun sendiri.', 'err');
                else { q("UPDATE users SET role=? WHERE id=?", 'si', [$_POST['role'], $id]); flash('Peran diperbarui.'); }
            } elseif ($act === 'del_user') {
                if ($id === $u['id']) flash('Tidak dapat menghapus akun sendiri.', 'err');
                else {
                    $db = db(); $db->begin_transaction();
                    q("DELETE FROM bids WHERE project_id IN (SELECT id FROM projects WHERE client_id=?)", 'i', [$id]);
                    q("DELETE FROM projects WHERE client_id=?", 'i', [$id]);
                    q("DELETE FROM bids WHERE developer_id=?", 'i', [$id]);
                    q("DELETE FROM users WHERE id=?", 'i', [$id]);
                    $db->commit(); flash('Pengguna dihapus.');
                }
            } elseif ($act === 'del_project') {
                $db = db(); $db->begin_transaction();
                q("DELETE FROM bids WHERE project_id=?", 'i', [$id]);
                q("DELETE FROM projects WHERE id=?", 'i', [$id]);
                $db->commit(); flash('Proyek dihapus.');
            }
            go('?page=admin');
        }
        $users = q("SELECT id,nama,email,role,created_at FROM users ORDER BY id");
        $projs = q("SELECT p.id,p.judul,p.status,u.nama AS klien FROM projects p JOIN users u ON u.id=p.client_id ORDER BY p.id");
        $h = '<div class="card"><h1>Admin: Pengguna</h1><table><tr><th>ID</th><th>Nama</th><th>Email</th><th>Peran</th><th>Aksi</th></tr>';
        foreach ($users as $x) {
            $opts = '';
            foreach (['klien', 'developer', 'admin'] as $ro) $opts .= '<option' . ($x['role'] === $ro ? ' selected' : '') . '>' . $ro . '</option>';
            $h .= '<tr><td>' . (int)$x['id'] . '</td><td>' . e($x['nama']) . '</td><td>' . e($x['email']) . '</td><td>' . e($x['role']) . '</td><td>'
                . '<form class="inline" method="post" action="?page=admin">' . csrf_field() . '<input type="hidden" name="act" value="role"><input type="hidden" name="id" value="' . (int)$x['id'] . '"><select name="role">' . $opts . '</select> <button class="sm sec">Ubah</button></form> '
                . '<form class="inline" method="post" action="?page=admin" onsubmit="return confirm(\'Hapus pengguna beserta proyek/penawarannya?\')">' . csrf_field() . '<input type="hidden" name="act" value="del_user"><input type="hidden" name="id" value="' . (int)$x['id'] . '"><button class="sm danger">Hapus</button></form></td></tr>';
        }
        $h .= '</table></div><div class="card"><h1>Admin: Proyek</h1><table><tr><th>ID</th><th>Judul</th><th>Klien</th><th>Status</th><th>Aksi</th></tr>';
        foreach ($projs as $x) {
            $h .= '<tr><td>' . (int)$x['id'] . '</td><td><a href="?page=project&id=' . (int)$x['id'] . '">' . e($x['judul']) . '</a></td><td>' . e($x['klien']) . '</td><td>' . status_badge($x['status']) . '</td><td>'
                . '<form class="inline" method="post" action="?page=admin" onsubmit="return confirm(\'Hapus proyek ini?\')">' . csrf_field() . '<input type="hidden" name="act" value="del_project"><input type="hidden" name="id" value="' . (int)$x['id'] . '"><button class="sm danger">Hapus</button></form></td></tr>';
        }
        render('Admin', $h . '</table></div>');

    default:
        http_response_code(404);
        render('Tidak ditemukan', '<div class="card"><h1>404</h1><p>Halaman tidak ditemukan. <a href="?page=home">Kembali ke beranda</a>.</p></div>');
    }
} catch (mysqli_sql_exception $ex) {
    error_log('Garuda-Hub DB error: ' . $ex->getMessage());
    http_response_code(500);
    render('Kesalahan', '<div class="card"><p>Terjadi kesalahan pada server. Silakan coba lagi nanti.</p></div>');
}
