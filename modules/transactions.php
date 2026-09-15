<?php
require_once __DIR__.'/../config/config.php';
if(isset($_GET['delete'])){ $id=(int)$_GET['delete']; $s=db()->prepare("SELECT * FROM transactions WHERE id=? AND company_id=?");$s->execute([$id,current_company_id()]);$r=$s->fetch();if($r){$delta=$r['type']==='income'?-1:1;db()->prepare("UPDATE wallets SET balance=balance+(?*amount) WHERE id=? AND company_id=?")->execute([$delta,$r['amount'],$r['wallet_id']]);db()->prepare("DELETE FROM transactions WHERE id=? AND company_id=?")->execute([$id,current_company_id()]);flash('success','Transaksi dihapus.');}redirect('transactions.php');}
$filter=$_GET['filter']??'';
$where="t.company_id=?"; $args=[current_company_id()];
if(in_array($filter,['income','expense'],true)){ $where.=" AND t.type=?"; $args[]=$filter; }
$q=db()->prepare("SELECT t.*,w.name wallet,c.name category FROM transactions t LEFT JOIN wallets w ON w.id=t.wallet_id LEFT JOIN categories c ON c.id=t.category_id WHERE $where ORDER BY t.transaction_date DESC,t.id DESC");$q->execute($args);$rows=$q->fetchAll();$active='transactions';$title='Transaksi';require __DIR__.'/../includes/header.php';?>
<div class="section-head">
  <div><h1>Catatan Keuangan</h1><p class="muted">Semua pemasukan dan pengeluaran non-setoran/non-penjualan.</p></div>
  <div style="display:flex;gap:8px;align-items:center;flex-wrap:wrap">
    <a class="btn ghost sm" href="../index.php">← Kembali</a>
    <a class="btn sm" href="transaction.php?type=income">+ Pemasukan</a>
    <a class="btn danger sm" href="transaction.php?type=expense">+ Pengeluaran</a>
  </div>
</div>
<div class="table-wrap"><table class="table"><tr><th>Tanggal</th><th>Keterangan</th><th>Dompet</th><th>Kategori</th><th>Jenis</th><th class="right">Nominal</th><th></th></tr><?php foreach($rows as $r):?><tr><td><?=e($r['transaction_date'])?></td><td><?=e($r['description'])?></td><td><?=e($r['wallet'])?></td><td><?=e($r['category'])?></td><td class="<?=e($r['type'])?>"><?=e($r['type'])?></td><td class="right"><?=rupiah((float)$r['amount'])?></td><td><a data-confirm="Hapus transaksi ini?" class="btn danger" href="?delete=<?=$r['id']?>">Hapus</a></td></tr><?php endforeach;?></table></div>
<?php require __DIR__.'/../includes/footer.php'; ?>
