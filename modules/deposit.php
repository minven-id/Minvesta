<?php
require_once __DIR__.'/../config/config.php'; $pdo=db(); $cid=current_company_id();
ensure_transaction_documentation_columns();
$customers=$pdo->prepare("SELECT id,customer_no,name FROM customers WHERE company_id=? AND status='active' ORDER BY name");$customers->execute([$cid]);$customers=$customers->fetchAll();
$wastes=$pdo->prepare("SELECT * FROM waste_types WHERE company_id=? AND status=1 ORDER BY name");$wastes->execute([$cid]);$wastes=$wastes->fetchAll();
if($_SERVER['REQUEST_METHOD']==='POST'){csrf_check();$pdo->beginTransaction();try{
$receipt='SET-'.date('YmdHis').'-'.random_int(100,999);$weights=$_POST['weight']??[];$ids=$_POST['waste_type_id']??[];$totalW=0;$total=0;$details=[];
foreach($ids as $i=>$wid){$wt=(float)($weights[$i]??0);if($wt<=0)continue;$s=$pdo->prepare("SELECT buy_price FROM waste_types WHERE id=? AND company_id=?");$s->execute([$wid,$cid]);$price=(float)$s->fetchColumn();$sub=$wt*$price;$totalW+=$wt;$total+=$sub;$details[]=[$wid,$wt,$price,$sub];}
if(!$details)throw new Exception('Minimal satu jenis sampah harus diisi.');
$documentation=store_transaction_documentation('documentation','deposit');
$s=$pdo->prepare("INSERT INTO deposits(company_id,customer_id,receipt_no,deposit_date,total_weight,total_amount,notes,documentation) VALUES(?,?,?,?,?,?,?,?)");$s->execute([$cid,$_POST['customer_id'],$receipt,$_POST['deposit_date'],$totalW,$total,$_POST['notes'],$documentation]);$did=$pdo->lastInsertId();
foreach($details as $d){$pdo->prepare("INSERT INTO deposit_details(deposit_id,waste_type_id,weight_kg,price_per_kg,subtotal) VALUES(?,?,?,?,?)")->execute([$did,...$d]);$pdo->prepare("UPDATE waste_types SET stock_kg=stock_kg+? WHERE id=? AND company_id=?")->execute([$d[1],$d[0],$cid]);}
$pdo->prepare("UPDATE customers SET balance=balance+? WHERE id=? AND company_id=?")->execute([$total,$_POST['customer_id'],$cid]);$pdo->commit();flash('success',"Setoran tersimpan. No: $receipt, nilai: ".rupiah($total));redirect('deposit.php');
}catch(Throwable $e){$pdo->rollBack();flash('error',$e->getMessage());redirect('deposit.php');}}
$active='deposit';$title='Setoran Sampah';require __DIR__.'/../includes/header.php';?>
<div class="section-head">
  <div><h1>Terima Setoran Sampah</h1><p class="muted">Catat setoran sampah dari nasabah dan tambahkan otomatis ke saldo.</p></div>
  <a class="btn ghost sm" href="../index.php">← Kembali</a>
</div>
<div class="card form"><form method="post" enctype="multipart/form-data"><input type="hidden" name="csrf" value="<?=csrf_token()?>"><div class="form-grid"><div class="field"><label>Nasabah</label><select name="customer_id" required><?php foreach($customers as $c):?><option value="<?=$c['id']?>"><?=e($c['customer_no'].' — '.$c['name'])?></option><?php endforeach;?></select></div><div class="field"><label>Tanggal</label><input type="date" name="deposit_date" value="<?=date('Y-m-d')?>"></div></div>
<div class="section"><h2>Detail Sampah</h2><div id="rows"><?php for($i=0;$i<4;$i++):?><div class="form-grid row" style="margin-bottom:8px"><div class="field"><select name="waste_type_id[]"><option value="">-- Pilih jenis --</option><?php foreach($wastes as $w):?><option value="<?=$w['id']?>"><?=e($w['name'])?> — <?=rupiah((float)$w['buy_price'])?>/kg</option><?php endforeach;?></select></div><div class="field"><input type="number" step="0.001" min="0" name="weight[]" placeholder="Berat (kg)"></div></div><?php endfor;?></div></div>
<div class="form-grid transaction-documentation"><div class="field"><label>Catatan</label><textarea name="notes"></textarea></div><div class="field"><label>Dokumentasi Transaksi</label><input type="file" name="documentation" accept="image/jpeg,image/png,image/webp,application/pdf"><small class="field-help">Foto bukti timbang atau dokumen pendukung. Maksimal 5 MB.</small></div></div><div class="actions"><button class="btn">Simpan Setoran</button><a class="btn ghost" href="deposit_history.php">Riwayat Setoran</a></div></form></div><?php require __DIR__.'/../includes/footer.php'; ?>