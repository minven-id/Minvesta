<?php
require_once __DIR__.'/config/config.php';

$appName = company()['name'] ?? 'Minvesta';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    if (attempt_login($username, $password)) {
        flash('success', 'Login berhasil. Selamat datang, '.current_user()['name'].'!');
        $redirect = $_SESSION['auth_redirect'] ?? 'index.php';
        unset($_SESSION['auth_redirect']);
        redirect($redirect);
    } else {
        $error = 'Username atau password salah.';
    }
}
if (is_logged_in()) { redirect('index.php'); }
?>
<!doctype html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Login - <?= e($appName) ?></title>
<link rel="icon" type="image/png" href="assets/images/logo.png">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@500;600;700&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<style>
:root{
  --c-sage-950:#14532d;--c-sage-900:#166534;--c-sage-800:#15803d;
  --c-sage-700:#16a34a;--c-sage-600:#22c55e;--c-sage-500:#4ade80;
  --c-sage-400:#86efac;--c-sage-300:#a7f3c7;--c-sage-200:#bbf7d0;
  --c-sage-100:#dcfce7;--c-sage-50:#f0fdf4;
  --c-gold-500:#d97706;--c-gold-400:#f59e0b;--c-gold-300:#fbbf24;
  --c-ink-900:#0f172a;--c-ink-800:#1e293b;--c-ink-700:#334155;--c-ink-600:#475569;
  --c-ink-500:#64748b;--c-ink-400:#94a3b8;--c-ink-300:#cbd5e1;--c-ink-200:#e2e8f0;
  --c-ink-100:#f1f5f9;--c-ink-50:#f8fafc;
  --c-line:#e5e7eb;--c-line-2:#d1d5db;--c-line-mint:#bbf7d0;
  --c-cream-50:#fbfaf7;
  --r-xs:6px;--r-sm:9px;--r-md:13px;--r-lg:18px;--r-xl:24px;
}
*{box-sizing:border-box}
body{margin:0;min-height:100vh;font-family:'Plus Jakarta Sans','Segoe UI',Arial,sans-serif;color:var(--c-ink-800);
  background:
    radial-gradient(900px 600px at 90% 0%,rgba(0,166,125,.13),transparent 64%),
    radial-gradient(760px 620px at 0% 100%,rgba(134,239,172,.18),transparent 62%),
    linear-gradient(135deg,#f8fbfc 0%,#effaf6 52%,#e1f6ed 100%);
  display:flex;align-items:center;justify-content:center;padding:24px;
}
.wrap{width:100%;max-width:920px;display:grid;grid-template-columns:minmax(260px,.82fr) minmax(380px,1fr);gap:58px;align-items:center;position:relative}
.brand{display:flex;flex-direction:column;align-items:center;gap:24px;justify-content:center;margin:0;padding:32px 0}
.brand-mark{width:120px;height:120px;border-radius:22px;
  background:radial-gradient(circle at 30% 30%,#fff,#f0fdf4 50%,#bbf7d0 100%);
  color:var(--c-sage-800);display:grid;place-items:center;font-weight:900;font-size:34px;
  box-shadow:0 14px 32px rgba(20,83,45,.16),0 0 0 1px rgba(255,255,255,.72) inset,0 2px 0 rgba(255,255,255,.85) inset;position:relative}
.brand-mark::after{content:'';position:absolute;inset:4px;border-radius:18px;background:linear-gradient(135deg,rgba(255,255,255,.7),transparent 48%);pointer-events:none}
.brand-text b{display:block;font-size:30px;color:var(--c-sage-900);letter-spacing:-.9px;font-weight:800;line-height:1.12}
.brand-text small{font-size:11px;color:var(--c-ink-500);font-weight:700;letter-spacing:1.8px;text-transform:uppercase}
.brand::after{content:'Kelola operasional Bank Sampah dengan lebih tertata dan aman.';display:block;max-width:270px;color:var(--c-ink-500);font-size:13px;line-height:1.7}
.card{
  background:linear-gradient(180deg,rgba(255,255,255,.96),rgba(251,250,247,.92));
  backdrop-filter:saturate(160%) blur(18px);-webkit-backdrop-filter:saturate(160%) blur(18px);
  border:1px solid rgba(134,239,172,.35);
  border-radius:22px;padding:38px 36px;
  box-shadow:
    0 28px 70px -24px rgba(22,101,52,.28),
    0 10px 24px -10px rgba(15,23,42,.12),
    0 1px 0 rgba(255,255,255,.9) inset,
    0 0 0 1px rgba(255,255,255,.4) inset;
  position:relative;overflow:hidden;
}
.card::before{content:'';position:absolute;top:-80px;right:-80px;width:220px;height:220px;border-radius:50%;background:radial-gradient(circle,rgba(134,239,172,.35),transparent 60%)}
.card::after{content:'';position:absolute;bottom:-100px;left:-60px;width:200px;height:200px;border-radius:50%;background:radial-gradient(circle,rgba(251,191,36,.18),transparent 60%)}
.card>*{position:relative;z-index:1}
h1{margin:0 0 7px;font-size:27px;font-weight:800;letter-spacing:-.7px;color:var(--c-ink-900)}
.subtitle{margin:0 0 28px;font-size:13.5px;color:var(--c-ink-500);line-height:1.6}
.field{margin-bottom:18px}
.field label{display:block;font-size:12px;font-weight:750;margin-bottom:8px;color:var(--c-sage-900);letter-spacing:.25px;display:flex;align-items:center;gap:6px}
.field label::before{content:'';width:3px;height:12px;border-radius:2px;background:linear-gradient(180deg,var(--c-sage-400),var(--c-gold-300));opacity:.9}
.field input{width:100%;padding:13.5px 16px;border:1.5px solid var(--c-line);border-radius:var(--r-md);
  background:linear-gradient(180deg,#fff,#fdfcf8);font-size:14px;font-family:inherit;color:var(--c-ink-900);
  transition:all .2s;box-shadow:0 1px 2px rgba(34,197,94,.03) inset,0 1px 0 rgba(255,255,255,.9)}
.field input:focus{outline:none;border-color:var(--c-sage-500);box-shadow:0 0 0 4px rgba(74,222,128,.18),0 1px 3px rgba(34,197,94,.07);background:#fff}
.btn{width:100%;margin-top:10px;display:inline-flex;align-items:center;justify-content:center;gap:8px;border:0;border-radius:var(--r-md);padding:14px 20px;
  background:linear-gradient(135deg,#00b894 0%,#008f78 100%);color:#fff;
  cursor:pointer;font-size:14px;font-weight:700;letter-spacing:.2px;
  box-shadow:0 3px 10px rgba(34,197,94,.22),0 0 0 1px rgba(167,243,199,.55) inset,0 -2px 0 rgba(22,163,74,.22) inset;
  transition:all .2s cubic-bezier(.2,.7,.2,1);text-shadow:0 1px 2px rgba(22,101,52,.18);font-family:inherit}
.btn:hover{transform:translateY(-1.8px);background:linear-gradient(135deg,#08c9a3 0%,#007a67 100%);box-shadow:0 14px 32px rgba(0,143,120,.28),0 0 0 1px rgba(167,243,199,.65) inset,0 -2px 0 rgba(21,128,61,.26) inset}
.btn:active{transform:translateY(0)}
.btn svg{width:15px;height:15px}
.alert{padding:13px 16px 13px 18px;border-radius:var(--r-md);margin-bottom:20px;font-size:13px;font-weight:600;border:1px solid transparent;position:relative;overflow:hidden;display:flex;align-items:flex-start;gap:10px}
.alert::before{content:'';position:absolute;left:0;top:0;bottom:0;width:4px;background:currentColor;opacity:.55}
.alert.error{background:linear-gradient(135deg,#fff1f2,#ffe4e6);color:#9f1239;border-color:#fecdd3}
.hint{margin-top:20px;text-align:center;font-size:12px;color:var(--c-ink-500);line-height:1.6}
.hint b{color:var(--c-sage-700)}
.divider{height:1px;background:linear-gradient(90deg,transparent,rgba(34,197,94,.35),transparent);margin:22px 0 0;opacity:.7}
.footer{grid-column:1/-1;margin-top:0;text-align:center;font-size:11.5px;color:var(--c-ink-400);font-weight:600;letter-spacing:.3px}
@media(max-width:760px){
  body{padding:20px 16px}
  .wrap{max-width:440px;display:block}
  .brand{align-items:center;gap:12px;padding:0;margin-bottom:22px}
  .brand-mark{width:90px;height:90px;border-radius:18px;font-size:28px}
  .brand-mark::after{border-radius:15px}
  .brand-text{text-align:center}
  .brand-text b{font-size:22px;letter-spacing:-.3px}
  .brand-text small{font-size:10px;letter-spacing:1.2px}
  .brand::after{display:none}
  .card{padding:30px 24px;border-radius:20px}
  h1{font-size:24px}
  .footer{margin-top:20px}
}
</style>
</head>
<body>
<div class="wrap">
  <div class="brand">
    <div class="brand-mark">
      <img src="assets/images/logo.png" alt="Minvesta Logo" style="width:100%;height:100%;object-fit:contain;border-radius:22px;">
    </div>
    <div class="brand-text">
      <b>Minvesta</b>
      <small>BANK SAMPAH</small>
    </div>
  </div>
  <div class="card">
    <h1>Masuk ke Aplikasi</h1>
    <p class="subtitle">Silakan masukkan kredensial Anda untuk mengelola data <?= e($appName) ?>.</p>
    <?php if($error): ?><div class="alert error">⚠ <?= e($error) ?></div><?php endif; ?>
    <?php if($f=flash()): ?><div class="alert <?= $f['type']==='success'?'':'error' ?>" style="<?= $f['type']==='success'?'background:linear-gradient(135deg,#f0fdf4,#dcfce7);color:var(--c-sage-900);border-color:var(--c-line-mint);':'' ?>">• <?= e($f['message']) ?></div><?php endif; ?>
    <form method="post">
      <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
      <div class="field">
        <label>Username</label>
        <input type="text" name="username" placeholder="Masukkan username" required autofocus autocomplete="username">
      </div>
      <div class="field">
        <label>Password</label>
        <input type="password" name="password" placeholder="Masukkan password" required autocomplete="current-password">
      </div>
      <button class="btn" type="submit">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/><polyline points="10 17 15 12 10 7"/><line x1="15" y1="12" x2="3" y2="12"/></svg>
        Masuk
      </button>
    </form>
    <div class="divider"></div>
    <div class="hint">
      Default: <b>admin</b> / <b>password</b><br>
      Hubungi administrator jika lupa kredensial.
    </div>
  </div>
  <div class="footer">© <?= date('Y') ?> Minvesta — Dibuat dengan Minven.id</div>
</div>
</body>
</html>
