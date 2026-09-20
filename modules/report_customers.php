<?php require_once __DIR__.'/../config/config.php';
$s=db()->prepare("SELECT * FROM customers WHERE company_id=? ORDER BY name");
$s->execute([current_company_id()]);
$rows=$s->fetchAll();
$active='report_customers';
$title='Laporan Nasabah';
require __DIR__.'/../includes/header.php';
?>
<div class="section-head">
  <div>
    <h1>Laporan Nasabah</h1>
    <p class="muted">Daftar seluruh nasabah beserta status dan saldo tabungan.</p>
  </div>
  <div>
    <div style="display:flex;gap:8px">
      <button class="btn ghost sm" onclick="exportToExcel('customers-table', 'laporan-nasabah-<?=date('Y-m-d')?>')">📊 Excel</button>
      <button class="btn ghost sm" onclick="exportToPDF('customers-table', 'laporan-nasabah-<?=date('Y-m-d')?>', 'Laporan Nasabah')">📄 PDF</button>
      <button class="btn ghost sm" onclick="printReport()">🖨️ Print</button>
      <a class="btn ghost sm" href="../index.php">← Kembali</a>
    </div>
  </div>
</div>
<div class="table-wrap">
  <table class="table" id="customers-table">
    <tr>
      <th>No</th>
      <th>Nasabah</th>
      <th>HP</th>
      <th>Status</th>
      <th class="right">Saldo</th>
    </tr>
    <?php foreach($rows as $r):?>
      <tr>
        <td><?=e($r['customer_no'])?></td>
        <td><?=e($r['name'])?></td>
        <td><?=e($r['phone'])?></td>
        <td><?=e($r['status'])?></td>
        <td class="right"><?=rupiah((float)$r['balance'])?></td>
      </tr>
    <?php endforeach;?>
  </table>
</div>
<?php require __DIR__.'/../includes/footer.php';?>