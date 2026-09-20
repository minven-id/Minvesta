<?php
require_once __DIR__.'/../config/config.php';
ensure_transactions_columns();
$type=$_GET['type']??'income';
if($_SERVER['REQUEST_METHOD']==='POST'){csrf_check();$type=$_POST['type'];$amount=(float)$_POST['amount'];$pdo=db();$pdo->beginTransaction();try{$s=$pdo->prepare("INSERT INTO transactions(company_id,wallet_id,category_id,type,amount,description,transaction_date) VALUES(?,?,?,?,?,?,?)");$s->execute([current_company_id(),$_POST['wallet_id'],$_POST['category_id'], $type,$amount,trim($_POST['description']),$_POST['transaction_date']]);$delta=$type==='income'?$amount:-$amount;$s=$pdo->prepare("UPDATE wallets SET balance=balance+? WHERE id=? AND company_id=?");$s->execute([$delta,$_POST['wallet_id'],current_company_id()]);$pdo->commit();flash('success','Transaksi berhasil disimpan.');redirect('transactions.php');}catch(Throwable $e){$pdo->rollBack();flash('error',$e->getMessage());}}
$w=db()->prepare("SELECT * FROM wallets WHERE company_id=? ORDER BY name");$w->execute([current_company_id()]);$wallets=$w->fetchAll();
$c=db()->prepare("SELECT * FROM categories WHERE company_id=? AND type=? ORDER BY name");$c->execute([current_company_id(),$type]);$cats=$c->fetchAll();
$active=$type==='expense'?'transaction_expense':'transactions';$title=$type==='income'?'Pemasukan':'Pengeluaran';require __DIR__.'/../includes/header.php';?>
<div class="section-head">
  <div><h1>Tambah <?=$title?></h1><p class="muted">Catat <?=$type==='income'?'pemasukan':'pengeluaran'?> non-setoran/non-penjualan (misal donasi, biaya operasional).</p></div>
  <a class="btn ghost sm" href="transactions.php">← Kembali ke Daftar</a>
</div>
<div class="card form"><form method="post"><input type="hidden" name="csrf" value="<?=csrf_token()?>"><input type="hidden" name="type" value="<?=e($type)?>">
<div class="form-grid"><div class="field"><label>Dompet</label><select name="wallet_id" required><?php foreach($wallets as $x):?><option value="<?=$x['id']?>"><?=e($x['name'])?> — <?=rupiah((float)$x['balance'])?></option><?php endforeach;?></select></div><div class="field"><label>Kategori</label><select name="category_id" required><?php foreach($cats as $x):?><option value="<?=$x['id']?>"><?=e($x['name'])?></option><?php endforeach;?></select></div><div class="field"><label>Nominal</label><input type="number" name="amount" min="0" step="0.01" required></div><div class="field"><label>Tanggal</label><input type="date" name="transaction_date" value="<?=date('Y-m-d')?>" required></div><div class="field full"><label>Keterangan</label><textarea name="description"></textarea></div></div><div class="actions"><button class="btn">Simpan <?=$title?></button><a class="btn ghost" href="transactions.php">Batal</a></div></form></div>
<?php require __DIR__.'/../includes/footer.php'; ?>
