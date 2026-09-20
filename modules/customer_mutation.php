<?php
require_once __DIR__.'/../config/config.php';
$pdo=db(); $cid=current_company_id();
$customers=$pdo->prepare("SELECT id,customer_no,name,balance FROM customers WHERE company_id=? ORDER BY name");
$customers->execute([$cid]); $custRows=$customers->fetchAll();
$custId=isset($_GET['customer_id'])?(int)$_GET['customer_id']:($custRows[0]['id']??0);
$cust=[]; $mutations=[]; $totalIn=0; $totalOut=0;
if($custId>0){
  $s=$pdo->prepare("SELECT * FROM customers WHERE id=? AND company_id=?");
  $s->execute([$custId,$cid]); $cust=$s->fetch();
  // Setoran
  $q=$pdo->prepare("SELECT deposit_date tgl,receipt_no ref,total_amount amt,'Kredit' jenis,CONCAT('Setoran: ',notes) ket FROM deposits WHERE company_id=? AND customer_id=? UNION ALL SELECT withdrawal_date tgl,CONCAT('WD-',id) ref,amount amt,'Debit' jenis,CONCAT('Penarikan: ',description) ket FROM withdrawals WHERE company_id=? AND customer_id=? ORDER BY tgl DESC");
  $q->execute([$cid,$custId,$cid,$custId]); $mutations=$q->fetchAll();
  foreach($mutations as $m){if($m['jenis']==='Kredit')$totalIn+=(float)$m['amt'];else $totalOut+=(float)$m['amt'];}
}
$active='customer_mutation';$title='Mutasi Nasabah';require __DIR__.'/../includes/header.php';?>
<div class="section-head">
  <div><h1>Mutasi Nasabah</h1><p class="muted">Riwayat kredit setoran dan debit penarikan per nasabah.</p></div>
  <div class="toolbar"><a class="btn ghost sm" href="../index.php">← Kembali</a></div>
</div>
<div class="card">
  <form class="toolbar" method="get">
    <label class="muted">Pilih Nasabah
      <select name="customer_id" style="padding:7px 10px;border:1.5px solid #e2e8f0;border-radius:7px;font-size:13px;min-width:260px">
        <?php foreach($custRows as $cr):?>
          <option value="<?=(int)$cr['id']?>" <?=($cr['id']==$custId?'selected':'')?>><?=e($cr['customer_no'].' — '.$cr['name'])?> (<?=rupiah((float)$cr['balance'])?>)</option>
        <?php endforeach;?>
      </select>
    </label>
    <button class="btn sm">Tampilkan</button>
  </form>
</div>
<?php if($cust):?>
<div class="grid section">
  <div class="card stat"><div class="label">Nasabah</div><div class="value" style="font-size:19px"><?=e($cust['name'])?></div><div class="muted" style="font-size:12px;margin-top:6px">No: <?=e($cust['customer_no'])?></div></div>
  <div class="card stat income-card"><div class="label">Total Kredit (Setoran)</div><div class="value income"><?=rupiah($totalIn)?></div></div>
  <div class="card stat expense-card"><div class="label">Total Debit (Penarikan)</div><div class="value expense"><?=rupiah($totalOut)?></div></div>
  <div class="card stat income-card"><div class="label">Saldo Akhir</div><div class="value"><?=rupiah((float)$cust['balance'])?></div></div>
</div>
<div class="section table-wrap"><div class="section-head"><h2>Riwayat mutasi</h2></div><table class="table">
  <tr><th>Tanggal</th><th>No. Referensi</th><th>Keterangan</th><th>Jenis</th><th class="right">Kredit</th><th class="right">Debit</th></tr>
  <?php foreach($mutations as $m):?>
    <tr>
      <td><?=e($m['tgl'])?></td>
      <td><b><?=e($m['ref'])?></b></td>
      <td><?=e($m['ket'])?></td>
      <td><span class="badge <?=($m['jenis']==='Kredit'?'success':'warning')?>"><?=e($m['jenis'])?></span></td>
      <td class="right income"><?=($m['jenis']==='Kredit')?rupiah((float)$m['amt']):'-'?></td>
      <td class="right expense"><?=($m['jenis']==='Debit')?rupiah((float)$m['amt']):'-'?></td>
    </tr>
  <?php endforeach;?>
  <?php if(!$mutations):?><tr><td colspan="6" class="empty">Belum ada transaksi untuk nasabah ini.</td></tr><?php endif;?>
</table></div>
<?php else:?><div class="card empty section">Pilih nasabah untuk melihat mutasi.</div><?php endif;?>
<?php require __DIR__.'/../includes/footer.php';?>