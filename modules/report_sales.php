<?php
require_once __DIR__.'/../config/config.php';
ensure_sales_columns();
$pdo=db(); $cid=current_company_id();
$from=$_GET['from']??date('Y-m-01'); $to=$_GET['to']??date('Y-m-d');
$where="s.company_id=? AND s.sale_date BETWEEN ? AND ?"; $args=[$cid,$from,$to];
$sum=$pdo->prepare("SELECT COALESCE(SUM(total_weight),0) w,COALESCE(SUM(total_amount),0) a,COUNT(*) c FROM sales s WHERE $where");
$sum->execute($args); $tot=$sum->fetch();
$perContact=$pdo->prepare("SELECT ct.id,ct.name,COALESCE(SUM(s.total_weight),0) w,COALESCE(SUM(s.total_amount),0) a,COUNT(s.id) cnt FROM contacts ct LEFT JOIN sales s ON s.contact_id=ct.id AND s.company_id=? AND s.sale_date BETWEEN ? AND ? WHERE ct.company_id=? GROUP BY ct.id ORDER BY a DESC");
$perContact->execute([$cid,$from,$to,$cid]); $contactRows=$perContact->fetchAll();
$perTanggal=$pdo->prepare("SELECT sale_date tgl,COALESCE(SUM(total_weight),0) w,COALESCE(SUM(total_amount),0) a,COUNT(*) c FROM sales s WHERE $where GROUP BY sale_date ORDER BY tgl DESC");
$perTanggal->execute($args); $tglRows=$perTanggal->fetchAll();
$active='report_sales';$title='Laporan Penjualan';require __DIR__.'/../includes/header.php';?>
<div class="section-head"><div><h1>Laporan Penjualan</h1><p class="muted">Rekap penjualan sampah berdasarkan periode, pembeli, dan tanggal.</p></div><div><div style="display:flex;gap:8px"><button class="btn ghost sm" onclick="exportToExcel('sales-contact-table', 'laporan-penjualan-pembeli-<?=date('Y-m-d')?>')">📊 Excel Pembeli</button><button class="btn ghost sm" onclick="exportToPDF('sales-contact-table', 'laporan-penjualan-pembeli-<?=date('Y-m-d')?>', 'Laporan Penjualan per Pembeli')">📄 PDF Pembeli</button><button class="btn ghost sm" onclick="exportToExcel('sales-tanggal-table', 'laporan-penjualan-tanggal-<?=date('Y-m-d')?>')">📊 Excel Tanggal</button><button class="btn ghost sm" onclick="exportToPDF('sales-tanggal-table', 'laporan-penjualan-tanggal-<?=date('Y-m-d')?>', 'Laporan Penjualan per Tanggal')">📄 PDF Tanggal</button><button class="btn ghost sm" onclick="printReport()">🖨️ Print</button><a class="btn ghost sm" href="../index.php">← Kembali</a></div></div></div>
<div class="card">
  <form class="report-filter" method="get">
    <label class="muted">Dari <input type="date" name="from" value="<?=e($from)?>" style="padding:7px 10px;border:1.5px solid #e2e8f0;border-radius:7px;font-size:13px"></label>
    <label class="muted">Sampai <input type="date" name="to" value="<?=e($to)?>" style="padding:7px 10px;border:1.5px solid #e2e8f0;border-radius:7px;font-size:13px"></label>
    <button class="btn sm">Tampilkan</button>
    <a class="btn ghost sm" href="report_sales.php">Reset</a>
  </form>
</div>
<div class="grid section">
  <div class="card stat income-card"><div class="label">Total Berat Terjual</div><div class="value"><?=number_format((float)$tot['w'],2,',','.')?> kg</div></div>
  <div class="card stat income-card"><div class="label">Total Pendapatan</div><div class="value income"><?=rupiah((float)$tot['a'])?></div></div>
  <div class="card stat"><div class="label">Jumlah Transaksi</div><div class="value"><?=number_format((int)$tot['c'])?>x</div></div>
  <div class="card stat"><div class="label">Rata-Rata/Transaksi</div><div class="value"><?=($tot['c']>0)?rupiah((float)$tot['a']/$tot['c']):'-'?></div></div>
</div>
<div class="section"><div class="section-head"><h2>Rekap per Pembeli</h2></div>
<div class="table-wrap"><table class="table" id="sales-contact-table">
  <tr><th>Pembeli</th><th class="right">Transaksi</th><th class="right">Berat (kg)</th><th class="right">Total Nilai</th><th class="right">Rata-Rata</th></tr>
  <?php foreach($contactRows as $r):?>
    <tr>
      <td><b><?=e($r['name'])?></b></td>
      <td class="right"><?=number_format((int)$r['cnt'])?></td>
      <td class="right"><?=number_format((float)$r['w'],2,',','.')?></td>
      <td class="right income"><?=rupiah((float)$r['a'])?></td>
      <td class="right"><?=($r['cnt']>0)?rupiah((float)$r['a']/$r['cnt']):'-'?></td>
    </tr>
  <?php endforeach;?>
  <?php if(!$contactRows):?><tr><td colspan="5" class="empty">Belum ada data penjualan.</td></tr><?php endif;?>
</table></div></div>
<div class="section"><div class="section-head"><h2>Rekap per Tanggal</h2></div>
<div class="table-wrap"><table class="table" id="sales-tanggal-table">
  <tr><th>Tanggal</th><th class="right">Jumlah</th><th class="right">Berat (kg)</th><th class="right">Nilai (Rp)</th></tr>
  <?php foreach($tglRows as $r):?>
    <tr>
      <td><?=e($r['tgl'])?></td>
      <td class="right"><?=number_format((int)$r['c'])?></td>
      <td class="right"><?=number_format((float)$r['w'],2,',','.')?></td>
      <td class="right income"><?=rupiah((float)$r['a'])?></td>
    </tr>
  <?php endforeach;?>
  <?php if(!$tglRows):?><tr><td colspan="4" class="empty">Belum ada penjualan pada periode ini.</td></tr><?php endif;?>
</table></div></div>
<?php require __DIR__.'/../includes/footer.php';?>