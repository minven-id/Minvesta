<?php
require_once __DIR__.'/../config/config.php';
$pdo=db(); $cid=current_company_id();
ensure_transaction_documentation_columns();
$from=$_GET['from']??date('Y-m-01'); $to=$_GET['to']??date('Y-m-d');
$where="d.company_id=? AND d.deposit_date BETWEEN ? AND ?"; $args=[$cid,$from,$to];
$totalWeight=0; $totalAmount=0;
$q=$pdo->prepare("SELECT d.*,c.name customer FROM deposits d LEFT JOIN customers c ON c.id=d.customer_id WHERE $where ORDER BY d.deposit_date DESC,d.id DESC");
$q->execute($args); $rows=$q->fetchAll();
foreach($rows as $r){$totalWeight+=(float)$r['total_weight']; $totalAmount+=(float)$r['total_amount'];}
$active='deposit_history';$title='Riwayat Setoran';require __DIR__.'/../includes/header.php';?>
<div class="section-head">
  <div><h1>Riwayat Setoran</h1><p class="muted">Daftar seluruh penerimaan sampah dan nilai tabungan nasabah.</p></div>
  <div class="toolbar"><a class="btn ghost sm" href="../index.php">← Kembali</a><a class="btn sm" href="deposit.php">+ Setoran Baru</a></div>
</div>
<div class="grid">
  <div class="card stat income-card"><div class="label">Total Berat</div><div class="value"><?=number_format($totalWeight,2,',','.')?> kg</div></div>
  <div class="card stat income-card"><div class="label">Total Nilai</div><div class="value income"><?=rupiah($totalAmount)?></div></div>
  <div class="card stat"><div class="label">Jumlah Transaksi</div><div class="value"><?=number_format(count($rows))?></div></div>
</div>
<div class="section card">
  <form class="toolbar" method="get">
    <label class="muted">Dari <input type="date" name="from" value="<?=e($from)?>" style="padding:7px 10px;border:1.5px solid #e2e8f0;border-radius:7px;font-size:13px"></label>
    <label class="muted">Sampai <input type="date" name="to" value="<?=e($to)?>" style="padding:7px 10px;border:1.5px solid #e2e8f0;border-radius:7px;font-size:13px"></label>
    <button class="btn sm">Filter</button>
    <a class="btn ghost sm" href="deposit_history.php">Reset</a>
  </form>
</div>
<div class="section table-wrap"><table class="table">
  <tr><th>Tanggal</th><th>No. Setoran</th><th>Nasabah</th><th class="right">Berat (kg)</th><th class="right">Nilai (Rp)</th><th>Catatan</th><th>Dokumentasi</th><th>Status</th></tr>
  <?php foreach($rows as $r):?>
    <tr>
      <td><?=e($r['deposit_date'])?></td>
      <td><b><?=e($r['receipt_no'])?></b></td>
      <td><?=e($r['customer'])?></td>
      <td class="right"><?=number_format((float)$r['total_weight'],2,',','.')?></td>
      <td class="right income"><?=rupiah((float)$r['total_amount'])?></td>
      <td class="muted"><?=e($r['notes'])?></td>
      <td><?php if(!empty($r['documentation'])):?><a class="doc-link" href="../<?=e($r['documentation'])?>" target="_blank" rel="noopener">Lihat Bukti</a><?php else:?><span class="muted">-</span><?php endif;?></td>
      <td><span class="badge success"><?=e($r['status'])?></span></td>
    </tr>
  <?php endforeach;?>
  <?php if(!$rows):?><tr><td colspan="8" class="empty">Belum ada data setoran pada periode ini.</td></tr><?php endif;?>
</table></div>
<?php require __DIR__.'/../includes/footer.php';?>