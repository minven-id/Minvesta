<?php
require_once __DIR__.'/../config/config.php';
$s=db()->prepare("SELECT * FROM waste_types WHERE company_id=? ORDER BY name");
$s->execute([current_company_id()]);
$rows=$s->fetchAll();
$totalKg=0;
$totalValue=0;
foreach($rows as $r){
  $totalKg+=(float)$r['stock_kg'];
  $totalValue+=(float)$r['stock_kg']*(float)$r['sell_price'];
}
$active='report_stock';
$title='Stok Sampah';
require __DIR__.'/../includes/header.php';
?>
<div class="section-head">
  <div>
    <h1>Laporan Stok Sampah</h1>
    <p class="muted">Pantau kuantitas, harga, dan estimasi nilai persediaan siap jual.</p>
  </div>
  <div class="toolbar">
    <div style="display:flex;gap:8px">
      <button class="btn ghost sm" onclick="exportToExcel('stock-table', 'laporan-stok-<?=date('Y-m-d')?>')">📊 Excel</button>
      <button class="btn ghost sm" onclick="exportToPDF('stock-table', 'laporan-stok-<?=date('Y-m-d')?>', 'Laporan Stok Sampah')">📄 PDF</button>
      <button class="btn ghost sm" onclick="printReport()">🖨️ Print</button>
      <a class="btn ghost sm" href="../index.php">← Kembali</a>
    </div>
    <a class="btn sm" href="stock_adjustment.php">Koreksi Stok</a>
  </div>
</div>
<div class="grid">
  <div class="card stat">
    <div class="label">Jenis Aktif</div>
    <div class="value"><?=number_format(count($rows))?></div>
  </div>
  <div class="card stat income-card">
    <div class="label">Total Stok</div>
    <div class="value"><?=number_format($totalKg,2,',','.')?> kg</div>
  </div>
  <div class="card stat income-card">
    <div class="label">Estimasi Nilai Jual</div>
    <div class="value income"><?=rupiah($totalValue)?></div>
  </div>
</div>
<div class="section table-wrap">
  <table class="table" id="stock-table">
    <tr>
      <th>Jenis</th>
      <th>Satuan</th>
      <th class="right">Stok</th>
      <th class="right">Harga Beli</th>
      <th class="right">Harga Jual</th>
      <th class="right">Estimasi Nilai</th>
    </tr>
    <?php foreach($rows as $r):
      $value=(float)$r['stock_kg']*(float)$r['sell_price'];
    ?>
      <tr>
        <td><b><?=e($r['name'])?></b></td>
        <td><?=e($r['unit'])?></td>
        <td class="right"><?=number_format((float)$r['stock_kg'],2,',','.')?> kg</td>
        <td class="right"><?=rupiah((float)$r['buy_price'])?></td>
        <td class="right"><?=rupiah((float)$r['sell_price'])?></td>
        <td class="right income"><?=rupiah($value)?></td>
      </tr>
    <?php endforeach;?>
    <?php if(!$rows):?>
      <tr><td colspan="6" class="empty">Belum ada data stok.</td></tr>
    <?php endif;?>
  </table>
</div>
<?php require __DIR__.'/../includes/footer.php';?>