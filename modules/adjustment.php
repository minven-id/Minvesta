<?php
require_once __DIR__.'/../config/config.php';
$pdo=db(); $cid=current_company_id();
if($_SERVER['REQUEST_METHOD']==='POST'){csrf_check();
  $custId=(int)$_POST['customer_id']; $type=$_POST['type']; $amt=(float)$_POST['amount']; $reason=trim($_POST['reason']);
  if($custId>0 && $amt>0 && $reason){try{
    $pdo->beginTransaction();
    $delta=($type=='plus')?$amt:-$amt;
    $pdo->prepare("UPDATE customers SET balance=balance+? WHERE id=? AND company_id=?")->execute([$delta,$custId,$cid]);
    $ref='ADJ-'.date('YmdHis');
    $desc="Penyesuaian Saldo: $reason ($ref)";
    $pdo->prepare("INSERT INTO transactions(company_id,wallet_id,category_id,type,amount,description,transaction_date) SELECT ?,COALESCE(MIN(id),0),(SELECT id FROM categories WHERE company_id=? ORDER BY id LIMIT 1),?,?,?,? FROM wallets WHERE company_id=?")
      ->execute([$cid,$cid,($type=='plus'?'income':'expense'),$amt,$desc,$_POST['adj_date'],$cid]);
    $pdo->prepare("INSERT INTO customer_mutations(company_id,customer_id,mutation_date,type,amount,reference_type,description,created_at) VALUES(?,?,?,?,?,?,?, NOW())")
      ->execute([$cid,$custId,$_POST['adj_date'],($type=='plus'?'credit':'debit'),$amt,'adjustment',$desc]);
    $pdo->commit();flash('success','Penyesuaian saldo berhasil disimpan.');
  }catch(Throwable $e){$pdo->rollBack();flash('error',$e->getMessage());}
  redirect('adjustment.php');}}
$customers=$pdo->prepare("SELECT id,customer_no,name,balance FROM customers WHERE company_id=? ORDER BY name");
$customers->execute([$cid]); $custRows=$customers->fetchAll();
$q=$pdo->prepare("SELECT cm.*,c.name AS cust,c.customer_no FROM customer_mutations cm LEFT JOIN customers c ON c.id=cm.customer_id WHERE cm.company_id=? AND cm.description LIKE 'Penyesuaian%' ORDER BY cm.id DESC LIMIT 20");
$q->execute([$cid]); $rows=$q->fetchAll();
$active='adjustment';$title='Penyesuaian Saldo';require __DIR__.'/../includes/header.php';?>
<div class="section-head">
  <div><h1>Penyesuaian Saldo Nasabah</h1><p class="muted">Koreksi saldo dengan alasan jelas dan jejak audit otomatis.</p></div>
  <a class="btn ghost sm" href="../index.php">← Kembali</a>
</div>
<div class="card form">
  <form method="post"><input type="hidden" name="csrf" value="<?=csrf_token()?>">
  <h2 style="margin:0 0 14px">Form Penyesuaian</h2>
  <div class="form-grid">
    <div class="field"><label>Nasabah</label>
      <select name="customer_id" required>
        <option value="">-- Pilih Nasabah --</option>
        <?php foreach($custRows as $cr):?><option value="<?=(int)$cr['id']?>"><?=e($cr['customer_no'].' — '.$cr['name'])?> (<?=rupiah((float)$cr['balance'])?>)</option><?php endforeach;?>
      </select>
    </div>
    <div class="field"><label>Jenis Penyesuaian</label>
      <select name="type" required>
        <option value="plus">Tambah Saldo (Plus) - Bonus/Koreksi Kurang</option>
        <option value="minus">Kurangi Saldo (Minus) - Koreksi Lebih</option>
      </select>
    </div>
    <div class="field"><label>Tanggal</label><input type="date" name="adj_date" value="<?=date('Y-m-d')?>"></div>
    <div class="field"><label>Nominal (Rp)</label><input type="number" min="0" step="100" name="amount" required placeholder="Contoh: 50000"></div>
    <div class="field full"><label>Alasan (wajib diisi untuk audit)</label><textarea name="reason" required placeholder="Contoh: Koreksi kesalahan input berat setoran tgl xx-xx-xxxx"></textarea></div>
  </div>
  <div class="actions"><button class="btn">Simpan Penyesuaian</button><a class="btn ghost" href="customer_mutation.php">Cek Mutasi Nasabah</a></div>
  </form>
</div>
<div class="section"><div class="section-head"><h2>Riwayat Penyesuaian (20 Terakhir)</h2></div>
<div class="table-wrap"><table class="table">
  <tr><th>Tanggal</th><th>Nasabah</th><th>Deskripsi</th><th>Jenis</th><th class="right">Nominal</th></tr>
  <?php foreach($rows as $r):
    $isPlus = ($r['type']==='credit');
  ?>
    <tr>
      <td><?=e($r['mutation_date'])?></td>
      <td>
        <?php if(!empty($r['cust'])):?>
          <b><?=e($r['cust'])?></b>
          <?php if(!empty($r['customer_no'])):?><br><small class="muted"><?=e($r['customer_no'])?></small><?php endif;?>
        <?php else:?>
          <span class="muted">-</span>
        <?php endif;?>
      </td>
      <td><?=e($r['description'])?></td>
      <td><span class="badge <?=$isPlus?'success':'danger'?>"><?=$isPlus?'Penambahan':'Pengurangan'?></span></td>
      <td class="right <?=$isPlus?'income':'expense'?>"><?=rupiah((float)$r['amount'])?></td>
    </tr>
  <?php endforeach;?>
  <?php if(!$rows):?><tr><td colspan="5" class="empty">Belum ada penyesuaian saldo.</td></tr><?php endif;?>
</table></div></div>
<?php require __DIR__.'/../includes/footer.php';?>