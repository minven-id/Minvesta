<?php require_once __DIR__.'/../config/config.php';
require_login();
$c = company();
$u = current_user();
$livePages = ['dashboard','wallets','deposit_history','withdrawal_history','sales_history','customer_mutation','reports','report_cashflow','report_customers','report_deposits','report_sales','report_stock','report_wallet','report_daily'];
$isLivePage = in_array($active ?? '', $livePages, true);
// Tentukan base path relatif: jika di modules/, naik 1 folder
$_rp = (strpos(basename($_SERVER['PHP_SELF'] ?? ''),'.php')!==false && strpos(($_SERVER['SCRIPT_NAME'] ?? ''),'modules/')!==false) ? '../' : '';
?>
<!doctype html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1,user-scalable=0">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="default">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<title><?= e($title ?? 'Dashboard') ?> - <?= e($c['name']) ?></title>
<style>
/* === CSS VARS FALLBACK (Pastel Sage Palette) === */
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
  --r-xs:6px;--r-sm:9px;--r-md:13px;--r-lg:18px;--r-xl:22px;
}
svg{width:17px;height:17px;flex:0 0 auto}
.sidebar svg{width:17px;height:17px}
.nav-footer-settings svg{width:15px;height:15px}
.sidebar{width:268px;
  background:
    radial-gradient(600px 300px at 100% 0%,rgba(167,243,199,.28),transparent 60%),
    radial-gradient(500px 400px at 0% 100%,rgba(134,239,172,.30),transparent 60%),
    linear-gradient(155deg,#14532d 0%,#166534 20%,#15803d 45%,#16a34a 72%,#22c55e 100%);
  color:#fff;padding:20px 13px;position:fixed;inset:0 auto 0 0;overflow:auto;
  box-shadow:4px 0 30px rgba(20,83,45,.16),1px 0 0 rgba(255,255,255,.08) inset,0 0 0 1px rgba(74,222,128,.10);z-index:40}
.brand{display:flex;gap:12px;align-items:center;padding:8px 10px 18px;border-bottom:1px solid rgba(187,247,208,.14);margin-bottom:16px}
.brand-mark{width:44px;height:44px;border-radius:14px;
  background:radial-gradient(circle at 30% 30%,#fff,#f0fdf4 50%,#bbf7d0 100%);
  color:var(--c-sage-800);display:grid;place-items:center;font-weight:900;font-size:21px;
  box-shadow:0 8px 22px rgba(0,0,0,.20),0 0 0 1px rgba(255,255,255,.55) inset,0 2px 0 rgba(255,255,255,.6) inset;position:relative;overflow:hidden}
.brand-mark img{width:100%;height:100%;object-fit:contain;border-radius:14px}
.brand-mark::after{content:'';position:absolute;inset:2px;border-radius:12px;background:linear-gradient(135deg,rgba(255,255,255,.6),transparent 45%);pointer-events:none}
.app{display:flex;min-height:100vh}
body{margin:0;
  background:
    radial-gradient(1200px 600px at 95% -10%,rgba(134,239,172,.08),transparent 60%),
    radial-gradient(900px 500px at -5% 110%,rgba(74,222,128,.06),transparent 55%),
    linear-gradient(180deg,#fbfaf7 0%,#f7f5ef 100%);
  color:var(--c-ink-800);font-family:'Manrope','Plus Jakarta Sans','Segoe UI',Arial,sans-serif;line-height:1.6;-webkit-font-smoothing:antialiased}
.company-pill{background:linear-gradient(180deg,rgba(255,255,255,.14),rgba(255,255,255,.07));
  padding:12px 13px;border-radius:var(--r-md);margin-bottom:16px;font-size:12.5px;
  backdrop-filter:blur(10px);-webkit-backdrop-filter:blur(10px);
  border:1px solid rgba(187,247,208,.18);line-height:1.5;
  box-shadow:0 4px 14px rgba(0,0,0,.07),0 0 0 1px rgba(255,255,255,.04) inset}
.company-pill b{font-size:10px;opacity:.86;letter-spacing:.8px;text-transform:uppercase;font-weight:800;color:var(--c-gold-300)}
.company-pill small{display:block;margin-top:4px;font-size:13px;opacity:.98;font-weight:700;color:#fff}
nav a{display:flex;gap:11px;align-items:center;color:rgba(220,252,231,.85);text-decoration:none;
  padding:10px 12px;border-radius:var(--r-sm);margin:2px 0;font-size:13px;border:1px solid transparent;
  transition:all .2s cubic-bezier(.2,.7,.2,1);font-weight:520;position:relative}
nav a:hover{background:linear-gradient(180deg,rgba(255,255,255,.14),rgba(255,255,255,.07));
  color:#fff;transform:translateX(2px);border-color:rgba(187,247,208,.16);box-shadow:0 4px 12px rgba(0,0,0,.09)}
nav a.active{background:linear-gradient(180deg,rgba(134,239,172,.32),rgba(74,222,128,.20));
  color:#fff;font-weight:650;border-color:rgba(167,243,199,.38);
  box-shadow:0 5px 16px rgba(0,0,0,.11),0 0 0 1px rgba(187,247,208,.14) inset}
nav a.active::before{content:'';position:absolute;left:-13px;top:8px;bottom:8px;width:3.5px;
  background:linear-gradient(180deg,#fbbf24,#f59e0b);border-radius:0 4px 4px 0;box-shadow:0 0 10px rgba(251,191,36,.50)}
nav a.active::after{content:'';position:absolute;right:10px;top:50%;transform:translateY(-50%);
  width:6px;height:6px;border-radius:50%;background:var(--c-gold-300);box-shadow:0 0 8px var(--c-gold-300)}
.nav-label{font-size:9.5px;letter-spacing:1.5px;opacity:.62;margin:18px 12px 6px;color:#bbf7d0;
  font-weight:800;text-transform:uppercase;padding-top:14px;border-top:1px solid rgba(187,247,208,.10);
  display:flex;align-items:center;gap:8px}
.nav-label::before{content:'';width:12px;height:1.5px;background:linear-gradient(90deg,var(--c-gold-400),transparent);border-radius:2px}
.nav-label:first-of-type{margin-top:8px;padding-top:0;border-top:0}
.nav-label:first-of-type::before{display:none}
.main{margin-left:268px;width:calc(100% - 268px);padding:0 15px 56px;min-width:0}
.topbar{height:70px;
  background:linear-gradient(180deg,rgba(255,255,255,.92),rgba(247,245,239,.85));
  backdrop-filter:saturate(160%) blur(14px);-webkit-backdrop-filter:saturate(160%) blur(14px);
  border-bottom:1px solid rgba(226,232,240,.7);margin:0 -15px 28px;padding:0 15px;
  display:flex;align-items:center;justify-content:space-between;gap:16px;
  box-shadow:0 1px 0 rgba(255,255,255,.85) inset,0 2px 10px rgba(22,101,52,.04);
  position:sticky;top:0;z-index:30}
.topbar::after{content:'';position:absolute;left:15px;right:15px;bottom:0;height:1px;
  background:linear-gradient(90deg,transparent,rgba(34,197,94,.35),transparent);opacity:.7}
.btn{display:inline-flex;align-items:center;gap:7px;border:0;border-radius:var(--r-sm);padding:11px 19px;
  background:linear-gradient(180deg,#86efac 0%,#4ade80 40%,#22c55e 75%,#16a34a 100%);color:#fff;
  text-decoration:none;cursor:pointer;font-size:13px;font-weight:650;white-space:nowrap;
  box-shadow:0 2px 8px rgba(34,197,94,.15),0 0 0 1px rgba(167,243,199,.55) inset,0 -2px 0 rgba(22,163,74,.22) inset;
  transition:all .2s cubic-bezier(.2,.7,.2,1);text-shadow:0 1px 2px rgba(22,101,52,.18);position:relative}
.btn:hover{background:linear-gradient(180deg,#4ade80 0%,#22c55e 40%,#16a34a 75%,#15803d 100%);
  transform:translateY(-1.8px);
  box-shadow:0 10px 28px rgba(34,197,94,.20),0 0 0 1px rgba(167,243,199,.65) inset,0 -2px 0 rgba(21,128,61,.26) inset}
.btn.ghost{background:rgba(255,255,255,.95);color:var(--c-ink-700);border:1.5px solid var(--c-line);
  box-shadow:0 1px 2px rgba(15,23,42,.04),0 1px 0 rgba(255,255,255,.9) inset;text-shadow:none}
.btn.ghost:hover{background:var(--c-sage-50);border-color:var(--c-sage-300);color:var(--c-sage-800);
  transform:translateY(-1px);box-shadow:0 4px 14px rgba(34,197,94,.10),0 1px 0 rgba(255,255,255,.9) inset}
.btn.sm{padding:7.5px 13px;font-size:12px;border-radius:var(--r-xs)}
.btn.danger{background:linear-gradient(180deg,#fda4af 0%,#fb7185 45%,#f43f5e 78%,#e11d48 100%);color:#fff;box-shadow:0 2px 8px rgba(244,63,94,.22),0 0 0 1px rgba(254,205,211,.55) inset,0 -2px 0 rgba(190,18,60,.20) inset;text-shadow:0 1px 2px rgba(159,18,57,.18)}
.btn.danger:hover{background:linear-gradient(180deg,#fb7185 0%,#f43f5e 45%,#e11d48 78%,#be123c 100%);transform:translateY(-1.8px);box-shadow:0 10px 26px rgba(244,63,94,.30),0 0 0 1px rgba(254,205,211,.6) inset}
.toolbar{display:flex;gap:11px;flex-wrap:wrap;align-items:center;padding:18px 20px}
nav a.wallet-link{font-size:12.5px;padding:8px 12px;
  background:linear-gradient(180deg,rgba(255,255,255,.08),rgba(255,255,255,.04));
  border:1px solid rgba(187,247,208,.14);border-radius:var(--r-sm)}
nav a.wallet-link:hover{background:linear-gradient(180deg,rgba(255,255,255,.16),rgba(255,255,255,.08));border-color:rgba(187,247,208,.25)}
nav a.wallet-link.active{background:linear-gradient(180deg,rgba(134,239,172,.26),rgba(74,222,128,.16));border-color:rgba(167,243,199,.32)}
.nav-footer-settings{margin-top:4px;padding:6px 4px 14px;display:flex;flex-direction:column;gap:5px}
.nav-footer-settings a{font-size:12px;padding:8px 10px;
  background:linear-gradient(180deg,rgba(255,255,255,.07),rgba(255,255,255,.03));
  border:1px solid rgba(187,247,208,.11);border-radius:var(--r-sm);opacity:.90}
.nav-footer-settings a:hover{opacity:1;
  background:linear-gradient(180deg,rgba(255,255,255,.15),rgba(255,255,255,.07));
  border-color:rgba(187,247,208,.22);transform:translateX(2px)}
.topbar .muted{display:block;font-size:10.5px;color:var(--c-ink-500);letter-spacing:.6px;margin-bottom:3px;font-weight:700;text-transform:uppercase}
.topbar b{font-size:15px;color:var(--c-ink-900);font-weight:750;letter-spacing:-.15px}
.topbar-title{flex:1;min-width:0}
.topbar-title b{overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
h1{font-size:26px;font-weight:800;margin:0 0 8px;letter-spacing:-.55px;line-height:1.2;
  background:linear-gradient(180deg,var(--c-ink-900),var(--c-sage-800));
  -webkit-background-clip:text;background-clip:text;-webkit-text-fill-color:transparent}
.grid{display:grid;grid-template-columns:repeat(4,1fr);gap:22px}
.card{background:linear-gradient(180deg,#fff,#fbfaf7);border:1px solid rgba(226,232,240,.8);
  border-radius:var(--r-lg);padding:22px;
  box-shadow:0 1px 3px rgba(15,23,42,.05),0 1px 2px rgba(15,23,42,.03),0 1px 0 rgba(255,255,255,.9) inset;
  transition:all .28s cubic-bezier(.2,.7,.2,1);position:relative;overflow:hidden}
.card:hover{transform:translateY(-3px);
  box-shadow:0 22px 52px -14px rgba(22,101,52,.16),0 10px 24px -8px rgba(15,23,42,.10),0 1px 0 rgba(255,255,255,.9) inset;
  border-color:var(--c-line-mint)}
.stat .label{font-size:11px;color:var(--c-ink-500);letter-spacing:.8px;font-weight:800;text-transform:uppercase;display:flex;align-items:center;gap:7px}
.stat .label::before{content:'';width:3px;height:14px;border-radius:2px;background:var(--c-sage-400)}
.stat .value{font-size:28px;font-weight:850;margin-top:12px;letter-spacing:-.65px;line-height:1.12;color:var(--c-ink-900);font-variant-numeric:tabular-nums}
.income{color:var(--c-sage-700);font-weight:700}
.expense{color:#e11d48;font-weight:700}
.table-wrap{overflow:hidden;background:linear-gradient(180deg,#fff,#fbfaf7);
  border:1px solid rgba(226,232,240,.85);border-radius:var(--r-lg);
  box-shadow:0 2px 8px rgba(22,101,52,.06),0 1px 3px rgba(15,23,42,.04),0 1px 0 rgba(255,255,255,.9) inset;position:relative}
.table{width:100%;border-collapse:separate;border-spacing:0;font-size:13.5px}
.table th,.table td{padding:14px 18px;border-bottom:1px solid var(--c-ink-100);text-align:left}
.table th{font-size:10.5px;text-transform:uppercase;color:var(--c-sage-900);
  background:linear-gradient(180deg,#f0fdf4,#dcfce7);
  font-weight:800;letter-spacing:.85px;border-bottom:1.5px solid var(--c-line-mint)}
.table tr:last-child td{border-bottom:0}
.table tbody tr:nth-child(even) td{background:rgba(240,253,244,.35)}
.right{text-align:right!important}
.field label{display:block;font-size:12.5px;font-weight:700;margin-bottom:7.5px;color:var(--c-sage-900);letter-spacing:.2px;display:flex;align-items:center;gap:6px}
.field label::before{content:'';width:2.5px;height:12px;border-radius:2px;background:linear-gradient(180deg,var(--c-sage-400),var(--c-gold-300));opacity:.9}
.field input,.field select,.field textarea{width:100%;padding:11.5px 14px;border:1.5px solid var(--c-line);border-radius:var(--r-sm);
  background:linear-gradient(180deg,#fff,#fdfcf8);font-size:13.5px;transition:all .2s;
  font-family:inherit;color:var(--c-ink-900);box-shadow:0 1px 2px rgba(34,197,94,.03) inset,0 1px 0 rgba(255,255,255,.9)}
.field input:focus,.field select:focus,.field textarea:focus{outline:none;border-color:var(--c-sage-500);
  box-shadow:0 0 0 4px rgba(74,222,128,.18),0 1px 3px rgba(34,197,94,.07);background:#fff}
.form-grid{display:grid;grid-template-columns:1fr 1fr;gap:19px}
.full{grid-column:1/-1}
.actions{display:flex;gap:11px;margin-top:28px;flex-wrap:wrap;padding-top:20px;border-top:1px dashed var(--c-line)}
.badge{padding:5.5px 13px;border-radius:99px;background:var(--c-ink-100);font-size:11.5px;font-weight:750;color:var(--c-ink-700);display:inline-block;letter-spacing:.2px;
  box-shadow:0 0 0 1px rgba(148,163,184,.18) inset,0 1px 0 rgba(255,255,255,.8) inset}
.badge.success{background:linear-gradient(135deg,#dcfce7,#bbf7d0);color:#166534;box-shadow:0 0 0 1px rgba(34,197,94,.28) inset,0 1px 0 rgba(255,255,255,.6) inset}
.badge.warning{background:linear-gradient(135deg,#fef3c7,#fde68a);color:#92400e;box-shadow:0 0 0 1px rgba(245,158,11,.28) inset,0 1px 0 rgba(255,255,255,.6) inset}
.badge.danger{background:linear-gradient(135deg,#fee2e2,#fecaca);color:#991b1b;box-shadow:0 0 0 1px rgba(239,68,68,.28) inset,0 1px 0 rgba(255,255,255,.6) inset}
.alert{padding:15px 20px 15px 22px;border-radius:var(--r-md);margin-bottom:24px;font-size:13.5px;font-weight:600;
  border:1px solid transparent;box-shadow:0 2px 8px rgba(22,101,52,.06);position:relative;overflow:hidden;display:flex;align-items:flex-start;gap:11px}
.alert::before{content:'';position:absolute;left:0;top:0;bottom:0;width:5px;background:currentColor;opacity:.55}
.alert.success{background:linear-gradient(135deg,#f0fdf4,#dcfce7);color:var(--c-sage-900);border-color:var(--c-line-mint)}
.alert.error{background:linear-gradient(135deg,#fff1f2,#ffe4e6);color:#9f1239;border-color:#fecdd3}
.wallet-grid{display:grid;grid-template-columns:repeat(5,1fr);gap:19px}
.wallet{min-height:150px;border-radius:var(--r-xl);
  background:radial-gradient(400px 200px at 100% 0%,rgba(255,255,255,.24),transparent 55%),
    linear-gradient(145deg,#166534 0%,#15803d 22%,#16a34a 48%,#22c55e 74%,#4ade80 100%);
  color:#fff;padding:21px;
  box-shadow:0 10px 28px rgba(34,197,94,.20),0 0 0 1px rgba(167,243,199,.4) inset,0 2px 0 rgba(255,255,255,.14) inset;
  position:relative;overflow:hidden;transition:all .3s cubic-bezier(.2,.7,.2,1)}
.wallet::before{content:'';position:absolute;top:-45%;right:-30%;width:180px;height:180px;border-radius:50%;
  background:radial-gradient(circle,rgba(187,247,208,.42),transparent 68%)}
.wallet::after{content:'';position:absolute;bottom:-55%;left:-20%;width:140px;height:140px;border-radius:50%;
  background:radial-gradient(circle,rgba(251,191,36,.20),transparent 68%)}
.wallet>*{position:relative;z-index:1}
.wallet:hover{transform:translateY(-4px) scale(1.018);
  box-shadow:0 22px 48px rgba(34,197,94,.26),0 0 0 1px rgba(187,247,208,.55) inset,0 2px 0 rgba(255,255,255,.16) inset}
.wallet .name{font-weight:800;font-size:14.5px;color:#fff;letter-spacing:.2px;position:relative;text-shadow:0 1px 3px rgba(0,0,0,.18)}
.wallet .balance{font-size:25px;margin-top:28px;font-weight:850;letter-spacing:-.65px;line-height:1.1;text-shadow:0 2px 8px rgba(0,0,0,.22);font-variant-numeric:tabular-nums}
.wallet .muted{color:rgba(255,255,255,.84)!important;font-size:12px;margin-top:4px;font-weight:500}
.muted{color:var(--c-ink-500)}
.page-subtitle{color:var(--c-ink-500);font-size:14px;margin:0 0 26px;line-height:1.65;max-width:760px}
.section{margin-top:36px}
.section-head{display:flex;justify-content:space-between;align-items:center;margin-bottom:18px;padding-bottom:4px}
.section-head h2{font-size:18px;margin:0;font-weight:750;color:var(--c-ink-800);letter-spacing:-.25px;display:flex;align-items:center;gap:10px}
.section-head h2::before{content:'';width:4px;height:20px;border-radius:3px;background:linear-gradient(180deg,var(--c-sage-500),var(--c-gold-400));box-shadow:0 0 0 2px rgba(34,197,94,.08)}
@media(max-width:1200px){.wallet-grid{grid-template-columns:repeat(3,1fr)}.grid{grid-template-columns:repeat(2,1fr)}}
@media(max-width:900px){.sidebar{width:232px}.main{margin-left:232px;width:calc(100% - 232px);padding:0 15px 48px}.topbar{margin:0 -15px 26px;padding:0 15px;height:68px}.grid{grid-template-columns:1fr 1fr}.wallet-grid{grid-template-columns:1fr 1fr}.form-grid{grid-template-columns:1fr}h1{font-size:22px}.stat .value{font-size:24px}}
@media(max-width:640px){.sidebar{position:static;width:100%;height:auto}.main{margin-left:0;width:100%;padding:0 18px 42px}.topbar{margin:0 -18px 22px;padding:0 18px;height:64px}.grid{grid-template-columns:1fr;gap:18px}.wallet-grid{grid-template-columns:1fr;gap:16px}h1{font-size:20px}.stat .value{font-size:22px}.topbar-title{display:none}.topbar-actions{margin-left:auto}.menu-toggle{display:grid;place-items:center;width:40px;height:40px;padding:0;border:1px solid var(--c-line);border-radius:var(--r-sm);background:#fff;color:var(--c-sage-800);cursor:pointer}.menu-toggle svg{width:19px;height:19px}}
@media(max-width:480px){.sidebar{padding:16px 10px}.brand{padding:6px 8px 14px}.brand-mark{width:40px;height:40px;border-radius:12px;font-size:18px}.brand b{font-size:14px}.brand small{font-size:9px}.company-pill{padding:10px 12px;font-size:11.5px}.main{padding:0 14px 36px}.topbar{margin:0 -14px 20px;padding:0 14px;height:60px}.grid{gap:16px}.card{padding:18px}.wallet{padding:16px}.wallet .balance{font-size:22px}.h1{font-size:18px}.table th,.table td{padding:10px 12px;font-size:12px}}
@media(min-width:761px) and (max-width:1024px){.sidebar{width:240px}.main{margin-left:240px;width:calc(100% - 240px);padding:0 15px 48px}.topbar{margin:0 -15px 28px;padding:0 15px;height:68px}.grid{gap:20px}.wallet-grid{gap:18px}}
/* === FALLBACK: Extra komponen pelengkap === */
.badge.sage{background:linear-gradient(135deg,#dcfce7,#bbf7d0);color:#15803d;box-shadow:0 0 0 1px rgba(34,197,94,.26) inset,0 1px 0 rgba(255,255,255,.6) inset}
.toolbar label{font-size:12px;font-weight:700;color:var(--c-sage-900);letter-spacing:.2px;display:inline-flex;align-items:center;gap:8px}
.toolbar label input,.toolbar label select{padding:7.5px 12px;border:1.5px solid #e2e8f0;border-radius:6px;font-size:13px;background:linear-gradient(180deg,#fff,#fdfcf8);font-family:inherit;color:#0f172a;transition:all .2s;box-shadow:0 1px 0 rgba(255,255,255,.9) inset}
.toolbar label input:focus,.toolbar label select:focus{outline:none;border-color:#4ade80;box-shadow:0 0 0 4px rgba(74,222,128,.18);background:#fff}
.card.income-card{background:linear-gradient(135deg,#fff7ed,#ffedd5);border-color:#fed7aa;box-shadow:0 8px 24px rgba(217,119,6,.10),0 1px 0 rgba(255,255,255,.9) inset}
.section-head h1{font-size:21px;margin:0;font-weight:800;color:#0f172a;letter-spacing:-.4px;display:flex;align-items:center;gap:10px}
.section-head h1::before{content:'';width:4.5px;height:24px;border-radius:3px;background:linear-gradient(180deg,#22c55e,#d97706);box-shadow:0 0 0 2px rgba(34,197,94,.10)}
.section-head > div > .muted{margin:6px 0 0;line-height:1.55;padding-left:14.5px}
.section-head .toolbar{padding:0}
.empty{text-align:center;color:#94a3b8;padding:60px 24px;font-size:14px;font-weight:600}
</style>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Poppins:wght@400;500;600;700&family=Sora:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="<?=$_rp?>assets/app.css?v=<?=time()?>">
<!-- Export Libraries -->
<script src="https://cdn.sheetjs.com/xlsx-0.20.0/package/dist/xlsx.full.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.31/jspdf.plugin.autotable.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script>
// Export XLSX
function exportToExcel(tableId, filename) {
  const table = document.getElementById(tableId);
  const wb = XLSX.utils.table_to_book(table, {sheet: "Sheet1"});
  XLSX.writeFile(wb, filename + '.xlsx');
}

// Export PDF
function exportToPDF(tableId, filename, title) {
  const { jsPDF } = window.jspdf;
  const doc = new jsPDF();
  const table = document.getElementById(tableId);

  doc.setFontSize(18);
  doc.text(title, 14, 22);
  doc.setFontSize(10);
  doc.text('Minvesta - Bank Sampah Management System', 14, 30);

  const autoTable = (doc.autoTable || jsPDF.autoTable);
  autoTable.call(doc, {
    html: table,
    startY: 40,
    theme: 'grid',
    headStyles: { fillColor: [22, 101, 52] },
    styles: { fontSize: 8 },
    margin: { top: 40, right: 10, bottom: 10, left: 10 }
  });

  doc.save(filename + '.pdf');
}

// Print
function printReport() {
  window.print();
}
</script>
<link rel="icon" type="image/png" href="<?=$_rp?>assets/favicon.png">
</head>
<body data-live-refresh="<?=$isLivePage?'true':'false'?>">
<div class="app">
<div class="sidebar-overlay" id="sidebar-overlay"></div>
<aside class="sidebar" id="app-sidebar">
  <div class="brand">
    <div class="brand-mark">
      <img src="<?=$_rp?>assets/logo/ChatGPT Image 12 Sep 2026, 22.24.30.png" alt="Minvesta Logo">
    </div>
    <div>
      <b>Minvesta</b>
      <small>Waste Management Suite</small>
    </div>
  </div>
  <div class="company-pill">
    <span class="user-meta"><b><?=e($u['name'])?></b><small><?=e($u['email'] ?? '')?> · <?=e($u['role'])?></small></span>
  </div>
  <nav>
    <!-- 🏠 DASHBOARD -->
    <a class="<?=($active??'')==='dashboard'?'active':''?>" href="<?=$_rp?>index.php">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 12l9-9 9 9M5 10v10h14V10"/></svg>
      <span>Dashboard</span>
    </a>

    <!-- ▸ DATA UTAMA (4 menu — Kategori dipindah ke Pengaturan) -->
    <button class="nav-label" type="button" aria-expanded="false">Data Utama</button>
    <a href="<?=$_rp?>modules/customers.php" class="<?=($active??'')==='customers'?'active':''?>">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
      <span>Nasabah</span>
    </a>
    <a href="<?=$_rp?>modules/waste_types.php" class="<?=($active??'')==='waste_types'?'active':''?>">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 6h18M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6M10 11v6M14 11v6"/></svg>
      <span>Jenis Sampah &amp; Harga</span>
    </a>
    <a href="<?=$_rp?>modules/contacts.php" class="<?=($active??'')==='contacts'?'active':''?>">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
      <span>Pengepul / Kontak</span>
    </a>
    <a href="<?=$_rp?>modules/wallets.php" class="<?=($active??'')==='wallets'?'active':''?>">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 12V8H6a2 2 0 0 1 0-4h12v4"/><path d="M4 6v12a2 2 0 0 0 2 2h14v-4"/><path d="M18 12a2 2 0 0 0 0 4h4v-4z"/></svg>
      <span>Dompet / Rekening</span>
    </a>

    <!-- 💸 OPERASIONAL UTAMA -->
    <button class="nav-label" type="button" aria-expanded="false">Operasional</button>
    <a href="<?=$_rp?>modules/deposit.php" class="<?=($active??'')==='deposit'?'active':''?>">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 3v12"/><polyline points="7 10 12 15 17 10"/><path d="M5 21h14"/></svg>
      <span>Setoran Sampah</span>
    </a>
    <a href="<?=$_rp?>modules/withdrawal.php" class="<?=($active??'')==='withdrawal'?'active':''?>">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 21V9"/><polyline points="17 14 12 9 7 14"/><path d="M5 3h14"/></svg>
      <span>Penarikan Saldo</span>
    </a>
    <a href="<?=$_rp?>modules/sales.php" class="<?=($active??'')==='sales'?'active':''?>">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/></svg>
      <span>Penjualan Sampah</span>
    </a>
    <a href="<?=$_rp?>modules/transfer.php" class="<?=($active??'')==='transfer'?'active':''?>">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M8 7h12l-3-3"/><path d="M16 17H4l3 3"/><path d="M8 7v10"/><path d="M16 17V7"/></svg>
      <span>Transfer Antar Dompet</span>
    </a>
    <a href="<?=$_rp?>modules/customer_mutation.php" class="<?=($active??'')==='customer_mutation'?'active':''?>">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 12h18"/><path d="M12 3v18"/><path d="M4 7l8-4 8 4"/><path d="M4 17l8 4 8-4"/></svg>
      <span>Mutasi Nasabah</span>
    </a>
    <a href="<?=$_rp?>modules/transactions.php" class="<?=($active??'')==='transactions'?'active':''?>">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 19h16"/><path d="M7 16V5"/><path d="M12 16V9"/><path d="M17 16v-4"/></svg>
      <span>Catatan Keuangan</span>
    </a>
    <a href="<?=$_rp?>modules/transaction.php?type=expense" class="<?=($active??'')==='transaction_expense'?'active':''?>">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 3v18"/><path d="M5 12h14"/><path d="M5 5h14"/><path d="M5 19h14"/></svg>
      <span>Pembiayaan / Biaya</span>
    </a>

    <!-- 📊 RIWAYAT OPERASIONAL -->
    <button class="nav-label" type="button" aria-expanded="false">Riwayat</button>
    <a href="<?=$_rp?>modules/deposit_history.php" class="<?=($active??'')==='deposit_history'?'active':''?>">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
      <span>Riwayat Setoran</span>
    </a>
    <a href="<?=$_rp?>modules/withdrawal_history.php" class="<?=($active??'')==='withdrawal_history'?'active':''?>">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="16 18 12 12 16 6"/></svg>
      <span>Riwayat Penarikan</span>
    </a>
    <a href="<?=$_rp?>modules/sales_history.php" class="<?=($active??'')==='sales_history'?'active':''?>">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M8 7h8M8 12h8M8 17h5"/></svg>
      <span>Riwayat Penjualan</span>
    </a>

    <!-- 📈 LAPORAN INTI -->
    <button class="nav-label" type="button" aria-expanded="false">Laporan</button>
    <a href="<?=$_rp?>modules/report_cashflow.php" class="<?=($active??'')==='reports'||($active??'')==='report_cashflow'?'active':''?>">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 3v18h18"/><path d="M7 15l4-4 4 4 5-6"/></svg>
      <span>Ringkasan Keuangan</span>
    </a>
    <a href="<?=$_rp?>modules/report_customers.php" class="<?=($active??'')==='report_customers'?'active':''?>">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/></svg>
      <span>Nasabah</span>
    </a>
    <a href="<?=$_rp?>modules/report_deposits.php" class="<?=($active??'')==='report_deposits'?'active':''?>">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 3v18h18"/><rect x="7" y="12" width="3" height="6"/><rect x="12" y="9" width="3" height="9"/></svg>
      <span>Setoran</span>
    </a>
    <a href="<?=$_rp?>modules/report_sales.php" class="<?=($active??'')==='report_sales'?'active':''?>">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 3v18h18"/><rect x="7" y="12" width="3" height="6"/><rect x="12" y="8" width="3" height="10"/><rect x="17" y="5" width="3" height="13"/></svg>
      <span>Penjualan</span>
    </a>
    <a href="<?=$_rp?>modules/report_wallet.php" class="<?=($active??'')==='report_wallet'?'active':''?>">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 12V8H6a2 2 0 0 1 0-4h12v4"/><path d="M4 6v12a2 2 0 0 0 2 2h14v-4"/><rect x="3" y="3" width="18" height="18" rx="2" fill="none"/></svg>
      <span>Dompet</span>
    </a>
    <a href="<?=$_rp?>modules/report_stock.php" class="<?=($active??'')==='report_stock'?'active':''?>">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/><polyline points="3.27 6.96 12 12.01 20.73 6.96"/></svg>
      <span>Stok</span>
    </a>
    <a href="<?=$_rp?>modules/report_daily.php" class="<?=($active??'')==='report_daily'?'active':''?>">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="17" rx="2"/><path d="M8 2v4M16 2v4M3 10h18"/><path d="M8 14h3M8 18h6"/></svg>
      <span>Laporan Harian</span>
    </a>

    <!-- ⚙ PENGATURAN SISTEM (4 menu — tanpa Perusahaan & Hak Akses) -->
    <button class="nav-label" type="button" aria-expanded="false">Pengaturan Sistem</button>
    <div class="nav-footer-settings">
      <a href="<?=$_rp?>modules/setup.php" class="<?=($active??'')==='setup'?'active':''?>">
        <svg viewBox="0 0 24 24" width="15" height="15" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 2.83-2.83l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>
        <span>Setup Utama</span>
      </a>
      <a href="<?=$_rp?>profile.php" class="<?=($active??'')==='profile'?'active':''?>">
        <svg viewBox="0 0 24 24" width="15" height="15" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="8" r="4"/><path d="M4 21a8 8 0 0 1 16 0"/></svg>
        <span>Profil Saya</span>
      </a>
      <?php if(($u['role'] ?? '')==='admin'): ?><a href="<?=$_rp?>modules/users.php" class="<?=($active??'')==='users'?'active':''?>">
        <svg viewBox="0 0 24 24" width="15" height="15" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M19 8v6M22 11h-6"/></svg>
        <span>Manajemen User</span>
      </a><?php endif; ?>
      <a href="<?=$_rp?>modules/categories.php" class="<?=($active??'')==='categories'?'active':''?>">
        <svg viewBox="0 0 24 24" width="15" height="15" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"/></svg>
        <span>Kategori Keuangan</span>
      </a>
      <a href="<?=$_rp?>modules/adjustment.php" class="<?=($active??'')==='adjustment'?'active':''?>">
        <svg viewBox="0 0 24 24" width="15" height="15" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 14h16M4 14l4-4M4 14l4 4M20 10H4M20 10l-4-4M20 10l-4 4"/></svg>
        <span>Penyesuaian Saldo</span>
      </a>
      <a href="<?=$_rp?>modules/stock_adjustment.php" class="<?=($active??'')==='stock_adjustment'?'active':''?>">
        <svg viewBox="0 0 24 24" width="15" height="15" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M3 9h18M9 3v18"/></svg>
        <span>Penyesuaian Stok</span>
      </a>
    </div>
</nav>
<div class="sidebar-footer">
  <a href="<?=$_rp?>logout.php" class="sidebar-logout">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
    <span>Keluar</span>
  </a>
</div>
</aside>
<main class="main">
<header class="topbar">
  <button class="menu-toggle" type="button" aria-label="Buka menu navigasi" aria-expanded="false" aria-controls="app-sidebar" id="menu-toggle">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 6h16M4 12h16M4 18h16"/></svg>
  </button>
  <div class="topbar-title">
    <span class="muted">Dashboard</span>
    <b><?=e($title ?? 'Dashboard')?></b>
  </div>
  <div class="topbar-actions">
    <?php if($isLivePage): ?><span class="live-status" title="Data diperbarui otomatis"><i></i>Live</span><?php endif; ?>
    <span class="date-pill"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg><?=date('d M Y')?></span>
    <a class="btn" href="<?=$_rp?>modules/deposit.php">
      <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M12 3v12"/><polyline points="7 10 12 15 17 10"/><path d="M5 21h14"/></svg>
      Setoran Baru
    </a>
    <a class="btn ghost" href="<?=$_rp?>modules/sales.php">
      <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.2"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/></svg>
      Penjualan
    </a>
  </div>
</header>
<?php if($f=flash()): ?><div class="alert <?=$f['type']?>"><?=e($f['message'])?></div><?php endif; ?>
