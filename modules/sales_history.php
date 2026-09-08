<?php
require_once __DIR__.'/../config/config.php';
$pdo=db(); $cid=current_company_id();
ensure_transaction_documentation_columns();
$stockValue=0; $totalStock=0;
$wastes=$pdo->prepare("SELECT * FROM waste_types WHERE company_id=? ORDER BY name");
$wastes->execute([$cid]); $wasteRows=$wastes->fetchAll();
foreach($wasteRows as $w){$totalStock+=(float)$w['stock_kg']; $stockValue+=(float)$w['stock_kg']*(float)$w['sell_price'];}
$from=$_GET['from']??date('Y-m-01'); $to=$_GET['to']??date('Y-m-d');
$sales=$pdo->prepare("SELECT s.*,ct.name contact,w.name wallet FROM sales s LEFT JOIN contacts ct ON ct.id=s.contact_id LEFT JOIN wallets w ON w.id=s.wallet_id WHERE s.company_id=? AND s.sale_date BETWEEN ? AND ? ORDER BY s.sale_date DESC,s.id DESC");
$sales->execute([$cid,$from,$to]); $salesRows=$sales->fetchAll();
$salesTotal=0; foreach($salesRows as $s)$salesTotal+=(float)$s['total_amount'];
$active='sales_history';$title='Riwayat Penjualan';require __DIR__.'/../includes/header.php';?>
<div class="section-head">
  <div><h1>Riwayat Penjualan</h1><p class="muted">Daftar seluruh penjualan sampah ke pengepul / kontak.</p></div>
  <div class="toolbar"><a class="btn ghost sm" href="../index.php">← Kembali</a><a class="btn sm" href="sales.php">+ Penjualan Baru</a></div>
</div>
<div class="grid">
  <div class="card stat"><div class="label">Total Penjualan</div><div class="value income"><?=rupiah($salesTotal)?></div></div>
  <div class="card stat"><div class="label">Jumlah Transaksi</div><div class="value"><?=number_format(count($salesRows))?></div></div>
  <div class="card stat income-card"><div class="label">Nilai Stok Saat Ini</div><div class="value"><?=rupiah($stockValue)?></div></div>
  <div class="card stat"><div class="label">Total Berat Stok</div><div class="value"><?=number_format($totalStock,2,',','.')?> kg</div></div>
</div>
<div class="section card">
  <form class="toolbar" method="get">
    <label class="muted">Dari <input type="date" name="from" value="<?=e($from)?>" style="padding:7px 10px;border:1.5px solid #e2e8f0;border-radius:7px;font-size:13px"></label>
    <label class="muted">Sampai <input type="date" name="to" value="<?=e($to)?>" style="padding:7px 10px;border:1.5px solid #e2e8f0;border-radius:7px;font-size:13px"></label>
    <button class="btn sm">Filter</button>
    <a class="btn ghost sm" href="sales_history.php">Reset</a>
  </form>
</div>
<div class="section table-wrap"><table class="table">
  <tr><th>Tanggal</th><th>No. Invoice</th><th>Pembeli</th><th>Kas/Rekening</th><th class="right">Nilai</th><th>Keterangan</th><th>Dokumentasi</th><th>Status</th></tr>
  <?php foreach($salesRows as $s):?>
    <tr>
      <td><?=e($s['sale_date'])?></td>
      <td><b><?=e($s['invoice_no'])?></b></td>
      <td><?=e($s['contact'])?></td>
      <td><?=e($s['wallet'])?></td>
      <td class="right income"><?=rupiah((float)$s['total_amount'])?></td>
      <td class="muted"><?=e($s['notes'])?></td>
      <td><?php if(!empty($s['documentation'])):?><a class="doc-link" href="../<?=e($s['documentation'])?>" target="_blank" rel="noopener">Lihat Bukti</a><?php else:?><span class="muted">-</span><?php endif;?></td>
      <td><span class="badge <?=($s['status']=='paid'?'success':'warning')?>"><?=e($s['status'])?></span></td>
    </tr>
  <?php endforeach;?>
  <?php if(!$salesRows):?><tr><td colspan="8" class="empty">Belum ada transaksi penjualan. Lakukan penjualan melalui menu Penjualan Sampah.</td></tr><?php endif;?>
</table></div>
<div class="section"><div class="section-head"><h2>Stok Siap Jual</h2></div>
<div class="table-wrap"><table class="table">
  <tr><th>Jenis Sampah</th><th>Satuan</th><th class="right">Stok</th><th class="right">Harga Jual</th><th class="right">Estimasi Nilai</th></tr>
  <?php foreach($wasteRows as $w):$est=(float)$w['stock_kg']*(float)$w['sell_price'];?>
    <tr>
      <td><?=e($w['name'])?></td>
      <td><?=e($w['unit'])?></td>
      <td class="right"><?=number_format((float)$w['stock_kg'],2,',','.')?></td>
      <td class="right"><?=rupiah((float)$w['sell_price'])?></td>
      <td class="right income"><?=rupiah($est)?></td>
    </tr>
  <?php endforeach;?>
</table></div></div>
<?php require __DIR__.'/../includes/footer.php';?>