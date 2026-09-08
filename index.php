<?php
require_once __DIR__.'/config/config.php';
$title='Dashboard'; $active='dashboard'; $pdo=db(); $cid=current_company_id();
function scalar($sql,$args){$s=db()->prepare($sql);$s->execute($args);return $s->fetchColumn();}

$nasabah=(int)scalar("SELECT COUNT(*) FROM customers WHERE company_id=? AND status='active'",[$cid]);
$saldoKas=(float)scalar("SELECT COALESCE(SUM(balance),0) FROM wallets WHERE company_id=?",[$cid]);
$setoranBulan=(float)scalar("SELECT COALESCE(SUM(total_amount),0) FROM deposits WHERE company_id=? AND MONTH(deposit_date)=MONTH(CURDATE()) AND YEAR(deposit_date)=YEAR(CURDATE())",[$cid]);
$pencairanBulan=(float)scalar("SELECT COALESCE(SUM(amount),0) FROM withdrawals WHERE company_id=? AND MONTH(withdrawal_date)=MONTH(CURDATE()) AND YEAR(withdrawal_date)=YEAR(CURDATE())",[$cid]);
$penjualanBulan=(float)scalar("SELECT COALESCE(SUM(total_amount),0) FROM sales WHERE company_id=? AND MONTH(sale_date)=MONTH(CURDATE()) AND YEAR(sale_date)=YEAR(CURDATE())",[$cid]);
$beratBulan=(float)scalar("SELECT COALESCE(SUM(d.weight_kg),0) FROM deposit_details d JOIN deposits x ON x.id=d.deposit_id WHERE x.company_id=? AND MONTH(x.deposit_date)=MONTH(CURDATE()) AND YEAR(x.deposit_date)=YEAR(CURDATE())",[$cid]);
$saldoNasabah=(float)scalar("SELECT COALESCE(SUM(balance),0) FROM customers WHERE company_id=?",[$cid]);
$jumlahWallet=(int)scalar("SELECT COUNT(*) FROM wallets WHERE company_id=?",[$cid]);
$chartTotal=max(1,$setoranBulan+$penjualanBulan+$pencairanBulan);
$depositPct=round($setoranBulan/$chartTotal*100);
$salesPct=round($penjualanBulan/$chartTotal*100);
$withdrawalPct=max(0,100-$depositPct-$salesPct);

$q=$pdo->prepare("SELECT d.*,c.name customer FROM deposits d JOIN customers c ON c.id=d.customer_id WHERE d.company_id=? ORDER BY d.deposit_date DESC,d.id DESC LIMIT 8");$q->execute([$cid]);$recentDeposit=$q->fetchAll();

$qw=$pdo->prepare("SELECT id,name,balance FROM wallets WHERE company_id=? ORDER BY id LIMIT 5");$qw->execute([$cid]);$wallets=$qw->fetchAll();

require __DIR__.'/includes/header.php';?>

<div class="page-intro">
  <div>
    <span class="eyebrow">Ringkasan operasional</span>
    <h1>Dashboard Bank Sampah</h1>
    <p class="page-subtitle">Pantau aktivitas, saldo, dan transaksi bulan berjalan dari satu tempat.</p>
  </div>
  <div class="intro-actions">
    <a class="btn ghost" href="modules/customers.php">Kelola Nasabah</a>
    <a class="btn" href="modules/deposit.php">+ Terima Setoran</a>
  </div>
</div>

<!-- Statistik Operasional -->
<div class="section-head"><h2>Statistik Operasional Bulan Ini</h2><a class="btn secondary sm" href="modules/report_cashflow.php">Lihat Laporan</a></div>
<div class="grid">
  <div class="card stat">
    <div class="label">👥 Nasabah Aktif</div>
    <div class="value"><?=number_format($nasabah)?></div>
  </div>
  <div class="card stat">
    <div class="label">🏦 Saldo Kas Total</div>
    <div class="value"><?=rupiah($saldoKas)?></div>
  </div>
  <div class="card stat income-card">
    <div class="label">📥 Setoran Bulan Ini</div>
    <div class="value income"><?=rupiah($setoranBulan)?></div>
  </div>
  <div class="card stat income-card">
    <div class="label">📤 Penjualan Bulan Ini</div>
    <div class="value income"><?=rupiah($penjualanBulan)?></div>
  </div>
</div>

<div class="grid section" style="margin-top:22px">
  <div class="card stat expense-card">
    <div class="label">💸 Pencairan Bulan Ini</div>
    <div class="value expense"><?=rupiah($pencairanBulan)?></div>
  </div>
  <div class="card stat income-card">
    <div class="label">👛 Estimasi Saldo Nasabah</div>
    <div class="value"><?=rupiah($saldoNasabah)?></div>
  </div>
  <div class="card stat">
    <div class="label">⚖ Berat Terkumpul</div>
    <div class="value"><?=number_format($beratBulan,2,',','.')?> kg</div>
  </div>
  <div class="card stat">
    <div class="label">💳 Jumlah Dompet</div>
    <div class="value"><?=number_format($jumlahWallet)?></div>
  </div>
</div>

<!-- Kartu Dompet -->
<?php if($wallets): ?>
<div class="section">
  <div class="section-head">
    <h2>Dompet &amp; Rekening</h2>
    <a class="btn ghost sm" href="modules/wallets.php">Kelola Dompet</a>
  </div>
  <div class="wallet-grid">
    <?php $wv=0;foreach($wallets as $w):$wv++;?>
      <a href="modules/wallet_detail.php?id=<?=(int)$w['id']?>" style="text-decoration:none" class="wallet <?=($wv===2)?'alt':(($wv===3)?'v3':(($wv===4)?'v4':(($wv===5)?'v5':'')))?>">
        <div class="name"><?=e($w['name'])?></div>
        <div class="muted">Saldo terkini</div>
        <div class="balance"><?=rupiah((float)$w['balance'])?></div>
      </a>
    <?php endforeach;?>
  </div>
</div>
<?php endif;?>

<!-- Setoran Terbaru -->
<div class="section dashboard-lower">
  <div class="section-head">
    <h2>Setoran Terbaru</h2>
    <div style="display:flex;gap:8px">
      <a class="btn ghost sm" href="modules/deposit_history.php">Riwayat Setoran</a>
      <a class="btn sm" href="modules/deposit.php">+ Terima Setoran</a>
    </div>
  </div>
  <div class="table-wrap dashboard-table">
    <table class="table">
      <tr>
        <th>Tanggal</th>
        <th>No. Setoran</th>
        <th>Nasabah</th>
        <th>Berat</th>
        <th class="right">Nilai</th>
        <th>Status</th>
      </tr>
      <?php foreach($recentDeposit as $r):?>
        <tr>
          <td><?=e($r['deposit_date'])?></td>
          <td><?=e($r['receipt_no'])?></td>
          <td><?=e($r['customer'])?></td>
          <td><?=number_format((float)$r['total_weight'],2,',','.')?> kg</td>
          <td class="right"><?=rupiah((float)$r['total_amount'])?></td>
          <td><span class="badge <?=($r['status']==='completed'||$r['status']==='success')?'success':(($r['status']==='pending')?'warning':'sage')?>"><?=ucfirst(e($r['status']))?></span></td>
        </tr>
      <?php endforeach;?>
      <?php if(!$recentDeposit):?>
        <tr><td colspan="6" class="empty">Belum ada setoran bulan ini.</td></tr>
      <?php endif;?>
    </table>
  </div>
  <aside class="dashboard-chart panel">
    <div class="chart-heading">
      <div>
        <span class="eyebrow">Komposisi transaksi</span>
        <h2>Ringkasan Bulan Ini</h2>
      </div>
      <span class="chart-month"><?=date('F Y')?></span>
    </div>
    <div class="donut" style="--deposit:<?=$depositPct?>%;--sales:<?=$salesPct?>%;--withdrawal:<?=$withdrawalPct?>%" aria-label="Ringkasan transaksi bulan ini"></div>
    <div class="chart-legend">
      <span><i class="dot deposit"></i>Setoran <b><?=$depositPct?>%</b></span>
      <span><i class="dot sales"></i>Penjualan <b><?=$salesPct?>%</b></span>
      <span><i class="dot withdrawal"></i>Pencairan <b><?=$withdrawalPct?>%</b></span>
    </div>
  </aside>
</div>

<?php require __DIR__.'/includes/footer.php'; ?>
