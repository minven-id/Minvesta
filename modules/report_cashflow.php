<?php
require_once __DIR__.'/../config/config.php';
$pdo=db(); $cid=current_company_id();
$from=$_GET['from']??date('Y-m-01'); $to=$_GET['to']??date('Y-m-d');
$whereDate="BETWEEN ? AND ?"; $args=[$from,$to];
$cashflow=[];
// Deposits (kas masuk potensial -> saldo nasabah)
$qd=$pdo->prepare("SELECT deposit_date tgl,receipt_no ref,CONCAT('Setoran: ',c.name) ket,total_amount amt,'in' jenis,'deposit' src FROM deposits d LEFT JOIN customers c ON c.id=d.customer_id WHERE d.company_id=? AND d.deposit_date $whereDate");
$qd->execute(array_merge([$cid],$args));
foreach($qd->fetchAll() as $r)$cashflow[]=['tgl'=>$r['tgl'],'ref'=>$r['ref'],'ket'=>$r['ket'],'in'=>$r['amt'],'out'=>0,'jenis'=>'Setoran Nasabah'];
// Withdrawals (kas keluar)
$qw=$pdo->prepare("SELECT withdrawal_date tgl,CONCAT('WD-',w.id) ref,CONCAT('Penarikan: ',c.name) ket,amount amt,'out' jenis FROM withdrawals w LEFT JOIN customers c ON c.id=w.customer_id WHERE w.company_id=? AND w.withdrawal_date $whereDate");
$qw->execute(array_merge([$cid],$args));
foreach($qw->fetchAll() as $r)$cashflow[]=['tgl'=>$r['tgl'],'ref'=>$r['ref'],'ket'=>$r['ket'],'in'=>0,'out'=>$r['amt'],'jenis'=>'Penarikan'];
// Sales (kas masuk)
$qs=$pdo->prepare("SELECT sale_date tgl,invoice_no ref,CONCAT('Penjualan: ',COALESCE(ct.name,'-')) ket,total_amount amt,'in' jenis FROM sales s LEFT JOIN contacts ct ON ct.id=s.contact_id WHERE s.company_id=? AND s.sale_date $whereDate");
$qs->execute(array_merge([$cid],$args));
foreach($qs->fetchAll() as $r)$cashflow[]=['tgl'=>$r['tgl'],'ref'=>$r['ref'],'ket'=>$r['ket'],'in'=>$r['amt'],'out'=>0,'jenis'=>'Penjualan'];
// Transaksi umum (income/expense)
$qt=$pdo->prepare("SELECT transaction_date tgl,CONCAT(UPPER(type),'-',t.id) ref,description ket,amount amt,type jenis FROM transactions t WHERE t.company_id=? AND t.transaction_date $whereDate");
$qt->execute(array_merge([$cid],$args));
foreach($qt->fetchAll() as $r){
  if($r['jenis']==='income')$cashflow[]=['tgl'=>$r['tgl'],'ref'=>$r['ref'],'ket'=>$r['ket'],'in'=>$r['amt'],'out'=>0,'jenis'=>'Pemasukan Lain'];
  else $cashflow[]=['tgl'=>$r['tgl'],'ref'=>$r['ref'],'ket'=>$r['ket'],'in'=>0,'out'=>$r['amt'],'jenis'=>'Pengeluaran'];
}
usort($cashflow,fn($a,$b)=>$b['tgl']<=>$a['tgl']);
$totalIn=0; $totalOut=0; $running=$pdo->prepare("SELECT COALESCE(SUM(balance),0) FROM wallets WHERE company_id=?");
$running->execute([$cid]); $saldoAwal=(float)$running->fetchColumn();
foreach($cashflow as $c){$totalIn+=$c['in']; $totalOut+=$c['out'];}
$active='report_cashflow';$title='Laporan Arus Kas';require __DIR__.'/../includes/header.php';?>
<div class="section-head"><div><h1>Laporan Arus Kas</h1><p class="muted">Rekap kas masuk, kas keluar, dan ringkasan keuangan Bank Sampah.</p></div><div><a class="btn ghost sm" href="../index.php">← Kembali</a></div></div>
<div class="card">
  <form class="toolbar" method="get">
    <label class="muted">Dari <input type="date" name="from" value="<?=e($from)?>" style="padding:7px 10px;border:1.5px solid #e2e8f0;border-radius:7px;font-size:13px"></label>
    <label class="muted">Sampai <input type="date" name="to" value="<?=e($to)?>" style="padding:7px 10px;border:1.5px solid #e2e8f0;border-radius:7px;font-size:13px"></label>
    <button class="btn sm">Tampilkan</button>
    <a class="btn ghost sm" href="report_cashflow.php">Reset</a>
  </form>
</div>
<div class="grid section">
  <div class="card stat"><div class="label">Total Kas Masuk</div><div class="value income"><?=rupiah($totalIn)?></div></div>
  <div class="card stat"><div class="label">Total Kas Keluar</div><div class="value expense"><?=rupiah($totalOut)?></div></div>
  <div class="card stat income-card"><div class="label">Surplus/Defisit</div><div class="value <?=($totalIn-$totalOut>=0?'income':'expense')?>"><?=rupiah($totalIn-$totalOut)?></div></div>
  <div class="card stat"><div class="label">Saldo Dompet Saat Ini</div><div class="value"><?=rupiah($saldoAwal)?></div></div>
</div>
<div class="section table-wrap"><table class="table">
  <tr><th>Tanggal</th><th>No. Referensi</th><th>Jenis</th><th>Keterangan</th><th class="right">Kas Masuk</th><th class="right">Kas Keluar</th></tr>
  <?php foreach($cashflow as $r):?>
    <tr>
      <td><?=e($r['tgl'])?></td>
      <td><b><?=e($r['ref'])?></b></td>
      <td><span class="badge <?=($r['in']>0?'success':'warning')?>"><?=e($r['jenis'])?></span></td>
      <td><?=e($r['ket'])?></td>
      <td class="right income"><?=($r['in']>0)?rupiah($r['in']):'-'?></td>
      <td class="right expense"><?=($r['out']>0)?rupiah($r['out']):'-'?></td>
    </tr>
  <?php endforeach;?>
  <?php if(!$cashflow):?><tr><td colspan="6" class="empty">Belum ada transaksi arus kas pada periode ini.</td></tr><?php endif;?>
</table></div>
<?php require __DIR__.'/../includes/footer.php';?>