<?php
require_once __DIR__.'/../config/config.php';
$pdo=db(); $cid=current_company_id();
$from=$_GET['from']??date('Y-m-01'); $to=$_GET['to']??date('Y-m-d');
$where="w.company_id=? AND w.withdrawal_date BETWEEN ? AND ?"; $args=[$cid,$from,$to];
$totalAmount=0;
$q=$pdo->prepare("SELECT w.*,c.name customer,wa.name wallet FROM withdrawals w LEFT JOIN customers c ON c.id=w.customer_id LEFT JOIN wallets wa ON wa.id=w.wallet_id WHERE $where ORDER BY w.withdrawal_date DESC,w.id DESC");
$q->execute($args); $rows=$q->fetchAll();
foreach($rows as $r)$totalAmount+=(float)$r['amount'];
$active='withdrawal_history';$title='Riwayat Penarikan';require __DIR__.'/../includes/header.php';?>
<div class="section-head">
  <div><h1>Riwayat Penarikan</h1><p class="muted">Daftar seluruh pencairan saldo nasabah Minvesta.</p></div>
  <div class="toolbar"><a class="btn ghost sm" href="../index.php">← Kembali</a><a class="btn danger sm" href="withdrawal.php">+ Penarikan Baru</a></div>
</div>
<div class="grid">
  <div class="card stat expense-card"><div class="label">Total Penarikan</div><div class="value expense"><?=rupiah($totalAmount)?></div></div>
  <div class="card stat"><div class="label">Jumlah Transaksi</div><div class="value"><?=number_format(count($rows))?></div></div>
  <div class="card stat"><div class="label">Rata-Rata</div><div class="value"><?=count($rows)?rupiah($totalAmount/count($rows)):'-'?></div></div>
</div>
<div class="section card">
  <form class="report-filter" method="get">
    <label class="muted">Dari <input type="date" name="from" value="<?=e($from)?>" style="padding:7px 10px;border:1.5px solid #e2e8f0;border-radius:7px;font-size:13px"></label>
    <label class="muted">Sampai <input type="date" name="to" value="<?=e($to)?>" style="padding:7px 10px;border:1.5px solid #e2e8f0;border-radius:7px;font-size:13px"></label>
    <button class="btn sm">Filter</button>
    <a class="btn ghost sm" href="withdrawal_history.php">Reset</a>
  </form>
</div>
<div class="section table-wrap"><table class="table">
  <tr><th>Tanggal</th><th>Nasabah</th><th>Kas/Rekening</th><th class="right">Nominal</th><th>Keterangan</th><th>Status</th></tr>
  <?php foreach($rows as $r):?>
    <tr>
      <td><?=e($r['withdrawal_date'])?></td>
      <td><?=e($r['customer'])?></td>
      <td><?=e($r['wallet'])?></td>
      <td class="right expense"><?=rupiah((float)$r['amount'])?></td>
      <td class="muted"><?=e($r['description'])?></td>
      <td><span class="badge success">Selesai</span></td>
    </tr>
  <?php endforeach;?>
  <?php if(!$rows):?><tr><td colspan="6" class="empty">Belum ada data penarikan pada periode ini.</td></tr><?php endif;?>
</table></div>
<?php require __DIR__.'/../includes/footer.php';?>