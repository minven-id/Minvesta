<?php
require_once __DIR__.'/../config/config.php';
ensure_transactions_columns();
if($_SERVER['REQUEST_METHOD']==='POST'&&($_POST['action']??'')==='delete'){csrf_check();$id=(int)($_POST['id']??0);$pdo=db();$pdo->beginTransaction();try{$s=$pdo->prepare("SELECT * FROM transactions WHERE id=? AND company_id=? FOR UPDATE");$s->execute([$id,current_company_id()]);$r=$s->fetch();if(!$r)throw new Exception('Transaksi tidak ditemukan.');$delta=$r['type']==='income'?-1:1;$walletAdjustment=$delta*(float)$r['amount'];$pdo->prepare("UPDATE wallets SET balance=balance+? WHERE id=? AND company_id=?")->execute([$walletAdjustment,$r['wallet_id'],current_company_id()]);$pdo->prepare("DELETE FROM transactions WHERE id=? AND company_id=?")->execute([$id,current_company_id()]);$pdo->commit();flash('success','Transaksi dihapus.');}catch(Throwable $e){if($pdo->inTransaction())$pdo->rollBack();flash('error',$e->getMessage());}redirect('transactions.php');}
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
<div class="table-wrap"><table class="table"><tr><th>Tanggal</th><th>Keterangan</th><th>Dompet</th><th>Kategori</th><th>Jenis</th><th class="right">Nominal</th><th></th></tr><?php foreach($rows as $r):?><tr><td><?=e($r['transaction_date'])?></td><td><?=e($r['description'])?></td><td><?=e($r['wallet'])?></td><td><?=e($r['category'])?></td><td class="<?=e($r['type'])?>"><?=e($r['type'])?></td><td class="right"><?=rupiah((float)$r['amount'])?></td><td><form method="post" onsubmit="return confirm('Hapus transaksi ini?')"><input type="hidden" name="csrf" value="<?=csrf_token()?>"><input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?=(int)$r['id']?>"><button class="btn danger sm" type="submit">Hapus</button></form></td></tr><?php endforeach;?><?php if(!$rows):?><tr><td colspan="7" class="empty">Belum ada catatan keuangan.</td></tr><?php endif;?></table></div>
<?php require __DIR__.'/../includes/footer.php'; ?>
