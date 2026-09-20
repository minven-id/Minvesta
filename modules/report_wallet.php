<?php
require_once __DIR__.'/../config/config.php';
$q=db()->prepare("SELECT w.*,COALESCE(SUM(CASE WHEN t.type='income' THEN t.amount ELSE 0 END),0) income,COALESCE(SUM(CASE WHEN t.type='expense' THEN t.amount ELSE 0 END),0) expense FROM wallets w LEFT JOIN transactions t ON t.wallet_id=w.id WHERE w.company_id=? GROUP BY w.id ORDER BY w.name");
$q->execute([current_company_id()]);
$rows=$q->fetchAll();
$active='report_wallet';
$title='Laporan Dompet';
require __DIR__.'/../includes/header.php';
?>
<div class="section-head">
  <div>
    <h1>Laporan Dompet</h1>
    <p class="muted">Daftar dompet beserta saldo, pemasukan, dan pengeluaran.</p>
  </div>
  <div>
    <div style="display:flex;gap:8px">
      <button class="btn ghost sm" onclick="exportToExcel('wallet-table', 'laporan-dompet-<?=date('Y-m-d')?>')">📊 Excel</button>
      <button class="btn ghost sm" onclick="exportToPDF('wallet-table', 'laporan-dompet-<?=date('Y-m-d')?>', 'Laporan Dompet')">📄 PDF</button>
      <button class="btn ghost sm" onclick="printReport()">🖨️ Print</button>
      <a class="btn ghost sm" href="../index.php">← Kembali</a>
    </div>
  </div>
</div>
<div class="table-wrap">
  <table class="table" id="wallet-table">
    <tr>
      <th>Dompet</th>
      <th>Tipe</th>
      <th class="right">Saldo</th>
      <th class="right">Pemasukan</th>
      <th class="right">Pengeluaran</th>
    </tr>
    <?php foreach($rows as $r):?>
      <tr>
        <td><?=e($r['name'])?></td>
        <td><?=e($r['type'])?></td>
        <td class="right"><?=rupiah((float)$r['balance'])?></td>
        <td class="right income"><?=rupiah((float)$r['income'])?></td>
        <td class="right expense"><?=rupiah((float)$r['expense'])?></td>
      </tr>
    <?php endforeach;?>
  </table>
</div>
<?php require __DIR__.'/../includes/footer.php'; ?>
