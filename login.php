<?php
require_once __DIR__.'/config/config.php';

$appName = company()['name'] ?? 'Minvesta';
if (is_logged_in()) { redirect('index.php'); }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $csrf = $_POST['csrf'] ?? '';
    if (!hash_equals($_SESSION['csrf'] ?? '', $csrf)) {
        flash('error', 'Sesi login tidak valid. Silakan coba lagi.');
    } else {
        $username = trim((string)($_POST['username'] ?? ''));
        $password = (string)($_POST['password'] ?? '');

        if ($username === '' || $password === '') {
            flash('error', 'Username dan password wajib diisi.');
        } elseif (attempt_login($username, $password)) {
            $redirectTo = $_SESSION['auth_redirect'] ?? 'index.php';
            unset($_SESSION['auth_redirect']);
            redirect($redirectTo);
        } else {
            flash('error', 'Username atau password salah.');
        }
    }
}

$csrf = csrf_token();
?>
<!doctype html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1,user-scalable=0">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="default">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<title>Login - <?= e($appName) ?></title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Sora:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<!-- Bootstrap 5 CSS CDN -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="icon" type="image/png" href="assets/favicon.png">
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
  --r-xs:6px;--r-sm:9px;--r-md:13px;--r-lg:18px;--r-xl:24px;
}
*{box-sizing:border-box}
body{
  margin:0;min-height:100vh;
  font-family:'Manrope','Plus Jakarta Sans','Segoe UI',Arial,sans-serif;
  color:var(--c-ink-800);
  background: linear-gradient(rgba(10, 30, 20, 0.55), rgba(15, 23, 42, 0.65)), url('https://images.unsplash.com/photo-1542601906990-b4d3fb778b09?q=80&w=1920&auto=format&fit=crop') no-repeat center center fixed;
  background-size: cover;
  display:flex;align-items:center;justify-content:center;padding:24px;
}
.wrap{
  width:100%;max-width:920px;
  display:grid;grid-template-columns:minmax(260px,.82fr) minmax(380px,1fr);
  gap:58px;align-items:center;position:relative;
}
.brand{
  display:flex;flex-direction:column;align-items:flex-start;
  gap:18px;justify-content:center;margin:0;padding:24px 0;
}
/* Diperbesar agar logo lebih terlihat jelas dan menonjol */
.brand-mark{
  width:110px;height:110px;border-radius:26px;
  background:radial-gradient(circle at 30% 30%,#fff,#f0fdf4 50%,#bbf7d0 100%);
  color:var(--c-sage-800);display:grid;place-items:center;
  font-weight:900;font-size:34px;
  box-shadow:0 16px 36px rgba(20,83,45,.25),0 0 0 1px rgba(255,255,255,.8) inset,0 2px 0 rgba(255,255,255,.9) inset;
  position:relative;overflow:hidden;padding:10px;
}
.brand-mark img{width:100%;height:100%;object-fit:contain;border-radius:18px}
.brand-mark::after{
  content:'';position:absolute;inset:4px;border-radius:22px;
  background:linear-gradient(135deg,rgba(255,255,255,.6),transparent 48%);
  pointer-events:none;
}
.brand-text b{
  display:block;font-size:30px;color:#ffffff;
  letter-spacing:-.9px;font-weight:800;line-height:1.12;
  animation:brandNeon 2s ease-in-out infinite alternate;
}
@keyframes brandNeon{
  0%{text-shadow:0 0 5px rgba(34,197,94,.6),0 0 10px rgba(34,197,94,.4),0 0 15px rgba(34,197,94,.2);color:#ffffff}
  100%{text-shadow:0 0 10px rgba(34,197,94,1),0 0 20px rgba(34,197,94,.8),0 0 30px rgba(34,197,94,.6),0 0 40px rgba(34,197,94,.4);color:var(--c-sage-300)}
}
.brand-text small{font-size:11px;color:var(--c-sage-200);font-weight:700;letter-spacing:1.8px;text-transform:uppercase}
.brand::after{
  content:'Kelola operasional secara lebih tertata dan aman.';
  display:block;max-width:270px;color:var(--c-sage-100);font-size:13px;line-height:1.7;
  text-shadow:0 1px 3px rgba(0,0,0,.6);
}
.card{
  background:linear-gradient(180deg,rgba(255,255,255,.96),rgba(251,250,247,.92));
  backdrop-filter:saturate(160%) blur(18px);-webkit-backdrop-filter:saturate(160%) blur(18px);
  border:1px solid rgba(134,239,172,.35);border-radius:22px;padding:38px 36px;
  box-shadow:0 28px 70px -24px rgba(22,101,52,.4),0 10px 24px -10px rgba(15,23,42,.2),0 1px 0 rgba(255,255,255,.9) inset,0 0 0 1px rgba(255,255,255,.4) inset;
  position:relative;overflow:hidden;
}
.card::before{
  content:'';position:absolute;top:-80px;right:-80px;width:220px;height:220px;border-radius:50%;
  background:radial-gradient(circle,rgba(134,239,172,.35),transparent 60%);
}
.card::after{
  content:'';position:absolute;bottom:-100px;left:-60px;width:200px;height:200px;border-radius:50%;
  background:radial-gradient(circle,rgba(251,191,36,.18),transparent 60%);
}
.card>*{position:relative;z-index:1}
h1{margin:0 0 7px;font-family:'Sora','Manrope','Plus Jakarta Sans',sans-serif;font-size:27px;font-weight:700;color:var(--c-ink-900)}
.subtitle{margin:0 0 28px;font-size:13.5px;color:var(--c-ink-500);line-height:1.6}
.field{margin-bottom:18px}
.field label{
  display:flex;align-items:center;gap:6px;
  font-size:12px;font-weight:750;margin-bottom:8px;color:var(--c-sage-900);letter-spacing:.25px;
}
.field label::before{
  content:'';width:3px;height:12px;border-radius:2px;
  background:linear-gradient(180deg,var(--c-sage-400),var(--c-gold-300));opacity:.9;
}
.field input{
  width:100%;padding:13.5px 16px;border:1.5px solid var(--c-line);border-radius:var(--r-md);
  background:linear-gradient(180deg,#fff,#fdfcf8);font-size:14px;font-family:inherit;color:var(--c-ink-900);
  transition:all .2s;box-shadow:0 1px 2px rgba(34,197,94,.03) inset,0 1px 0 rgba(255,255,255,.9);
}
.field input:focus{
  outline:none;border-color:var(--c-sage-500);
  box-shadow:0 0 0 4px rgba(74,222,128,.18),0 1px 3px rgba(34,197,94,.07);background:#fff;
}
.btn{
  width:100%;margin-top:10px;display:inline-flex;align-items:center;justify-content:center;gap:8px;
  border:0;border-radius:var(--r-md);padding:14px 20px;
  background:linear-gradient(135deg,#00b894 0%,#008f78 100%);color:#fff;cursor:pointer;
  font-size:14px;font-weight:700;letter-spacing:.2px;
  box-shadow:0 3px 10px rgba(34,197,94,.22),0 0 0 1px rgba(167,243,199,.55) inset,0 -2px 0 rgba(22,163,74,.22) inset;
  transition:all .2s cubic-bezier(.2,.7,.2,1);text-shadow:0 1px 2px rgba(22,101,52,.18);font-family:inherit;
}
.btn:hover{
  transform:translateY(-1.8px);background:linear-gradient(135deg,#08c9a3 0%,#007a67 100%);
  box-shadow:0 14px 32px rgba(0,143,120,.28),0 0 0 1px rgba(167,243,199,.65) inset,0 -2px 0 rgba(21,128,61,.26) inset;
}
.btn:active{transform:translateY(0)}
.alert{
  padding:13px 16px 13px 18px;border-radius:var(--r-md);margin-bottom:20px;
  font-size:13px;font-weight:600;border:1px solid transparent;position:relative;overflow:hidden;
  display:flex;align-items:flex-start;gap:10px;
}
.alert::before{content:'';position:absolute;left:0;top:0;bottom:0;width:4px;background:currentColor;opacity:.55}
.alert.error{background:linear-gradient(135deg,#fff1f2,#ffe4e6);color:#9f1239;border-color:#fecdd3}
.alert.success{background:linear-gradient(135deg,#f0fdf4,#dcfce7);color:var(--c-sage-900);border-color:var(--c-line-mint)}
.hint{margin-top:20px;text-align:center;font-size:12px;color:var(--c-ink-500);line-height:1.6}
.footer{
  grid-column:1/-1;margin-top:10px;text-align:center;font-size:11.5px;
  color:#ffffff;font-weight:600;letter-spacing:.3px;
  text-shadow:0 1px 3px rgba(0,0,0,.8);
  animation:neonGlow 2s ease-in-out infinite alternate;
}
@keyframes neonGlow{
  0%{text-shadow:0 0 5px rgba(34,197,94,.6),0 0 10px rgba(34,197,94,.4);color:#ffffff}
  100%{text-shadow:0 0 10px rgba(34,197,94,1),0 0 20px rgba(34,197,94,.8),0 0 30px rgba(34,197,94,.6);color:var(--c-sage-300)}
}
@media(max-width:760px){
  body{padding:20px 16px}
  .wrap{max-width:440px;display:block}
  .brand{align-items:center;gap:12px;padding:0;margin-bottom:22px}
  .brand-mark{width:88px;height:88px;border-radius:22px}
  .brand-text{text-align:center}
  .brand-text b{font-size:22px}
  .brand::after{display:none}
  .card{padding:30px 24px;border-radius:20px}
  h1{font-size:24px}
}
</style>
</head>
<body>
<div class="wrap">
  <div class="brand">
    <div class="brand-mark">
      <img src="assets/logo/ChatGPT Image 12 Sep 2026, 22.24.30.png" alt="Minvesta Logo">
    </div>
    <div class="brand-text">
      <b>Minvesta</b>
      <small>Waste Management Suite</small>
    </div>
  </div>
  <div class="card shadow-lg">
    <h1>Masuk ke Aplikasi</h1>
    <p class="subtitle">Gunakan username dan password akun <?= e($appName) ?> untuk masuk.</p>
    
    <?php if ($f = flash()): ?>
      <div class="alert <?= $f['type'] === 'success' ? 'success' : 'error' ?> shadow-sm">• <?= e($f['message']) ?></div>
    <?php endif; ?>

    <form method="post" action="login.php" autocomplete="on" class="needs-validation" novalidate>
      <input type="hidden" name="csrf" value="<?= e($csrf) ?>">
      <div class="field mb-3">
        <label for="username" class="form-label">Username</label>
        <input id="username" type="text" name="username" value="" class="form-control" placeholder="Masukkan username" required autofocus>
      </div>
      <div class="field mb-3">
        <label for="password" class="form-label">Password</label>
        <input id="password" type="password" name="password" class="form-control" placeholder="Masukkan password" required autocomplete="current-password">
      </div>
      <button class="btn btn-success w-100 py-2" type="submit">Masuk</button>
    </form>

    <div class="hint">Akses aplikasi hanya tersedia untuk akun yang sudah terdaftar di Minvesta.</div>
  </div>
  <div class="footer">© <?= date('Y') ?> Minven.Id</div>
</div>

<!-- Bootstrap 5 JS Bundle CDN -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>