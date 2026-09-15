<?php
require_once __DIR__.'/../config/config.php';
$pdo=db(); $cid=current_company_id();
$from=$_GET['from']??date('Y-m-01'); $to=$_GET['to']??date('Y-m-d');
$where="company_id=? AND deposit_date BETWEEN ? AND ?"; $args=[$cid,$from,$to];
$sum=$pdo->prepare("SELECT COALESCE(SUM(total_weight),0) w,COALESCE(SUM(total_amount),0) a,COUNT(*) c FROM deposits WHERE $where");
$sum->execute($args); $tot=$sum->fetch();
$perNasabah=$pdo->prepare("SELECT c.id,c.name,c.customer_no,COALESCE(SUM(d.total_weight),0) w,COALESCE(SUM(d.total_amount),0) a,COUNT(d.id) cnt FROM customers c LEFT JOIN deposits d ON d.customer_id=c.id AND d.company_id=? AND d.deposit_date BETWEEN ? AND ? WHERE c.company_id=? GROUP BY c.id ORDER BY a DESC LIMIT 15");
$perNasabah->execute([$cid,$from,$to,$cid]); $nasabahRows=$perNasabah->fetchAll();
$perTanggal=$pdo->prepare("SELECT deposit_date tgl,COALESCE(SUM(total_weight),0) w,COALESCE(SUM(total_amount),0) a,COUNT(*) c FROM deposits WHERE $where GROUP BY deposit_date ORDER BY deposit_date DESC");
$perTanggal->execute($args); $tglRows=$perTanggal->fetchAll();
$active='report_deposits';$title='Laporan Setoran';require __DIR__.'/../includes/header.php';?>
<div class="section-head"><div><h1>Laporan Setoran</h1><p class="muted">Rekap berat, nilai, dan frekuensi setoran per periode.</p></div><div><a class="btn ghost sm" href="../index.php">← Kembali</a></div></div>
<div class="card">
  <form class="toolbar" method="get">
    <label class="muted">Dari <input type="date" name="from" value="<?=e($from)?>" style="padding:7px 10px;border:1.5px solid #e2e8f0;border-radius:7px;font-size:13px"></label>
    <label class="muted">Sampai <input type="date" name="to" value="<?=e($to)?>" style="padding:7px 10px;border:1.5px solid #e2e8f0;border-radius:7px;font-size:13px"></label>
    <button class="btn sm">Tampilkan</button>
    <a class="btn ghost sm" href="report_deposits.php">Reset</a>
  </form>
</div>
<div class="grid section">
  <div class="card stat income-card"><div class="label">Total Berat</div><div class="value"><?=number_format((float)$tot['w'],2,',','.')?> kg</div></div>
  <div class="card stat income-card"><div class="label">Total Nilai</div><div class="value income"><?=rupiah((float)$tot['a'])?></div></div>
  <div class="card stat"><div class="label">Frekuensi</div><div class="value"><?=number_format((int)$tot['c'])?>x</div></div>
  <div class="card stat"><div class="label">Rata-Rata/Transaksi</div><div class="value"><?=($tot['c']>0)?rupiah((float)$tot['a']/$tot['c']):'-'?></div></div>
</div>
<div class="section"><div class="section-head"><h2>Rekap per Nasabah (Top 15)</h2></div>
<div class="table-wrap"><table class="table">
  <tr><th>No. Nasabah</th><th>Nama</th><th class="right">Setoran (x)</th><th class="right">Berat (kg)</th><th class="right">Total Nilai</th><th class="right">Rata-Rata</th></tr>
  <?php foreach($nasabahRows as $i=>$r):?>
    <tr>
      <td><?=e($r['customer_no'])?></td>
      <td><b><?=e($r['name'])?></b></td>
      <td class="right"><?=number_format((int)$r['cnt'])?></td>
      <td class="right"><?=number_format((float)$r['w'],2,',','.')?></td>
      <td class="right income"><?=rupiah((float)$r['a'])?></td>
      <td class="right"><?=($r['cnt']>0)?rupiah((float)$r['a']/$r['cnt']):'-'?></td>
    </tr>
  <?php endforeach;?>
  <?php if(!$nasabahRows):?><tr><td colspan="6" class="empty">Belum ada data.</td></tr><?php endif;?>
</table></div></div>
<div class="section"><div class="section-head"><h2>Rekap per Tanggal</h2></div>
<div class="table-wrap"><table class="table">
  <tr><th>Tanggal</th><th class="right">Jumlah</th><th class="right">Berat (kg)</th><th class="right">Nilai (Rp)</th></tr>
  <?php foreach($tglRows as $r):?>
    <tr>
      <td><?=e($r['tgl'])?></td>
      <td class="right"><?=number_format((int)$r['c'])?></td>
      <td class="right"><?=number_format((float)$r['w'],2,',','.')?></td>
      <td class="right income"><?=rupiah((float)$r['a'])?></td>
    </tr>
  <?php endforeach;?>
  <?php if(!$tglRows):?><tr><td colspan="4" class="empty">Belum ada data pada periode ini.</td></tr><?php endif;?>
</table></div></div>
<?php require __DIR__.'/../includes/footer.php';?>