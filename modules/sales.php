<?php
require_once __DIR__.'/../config/config.php';
$pdo=db(); $cid=current_company_id();
ensure_transaction_documentation_columns();
$contacts=$pdo->prepare("SELECT id,name FROM contacts WHERE company_id=? ORDER BY name");$contacts->execute([$cid]);$contacts=$contacts->fetchAll();
$wallets=$pdo->prepare("SELECT id,name,balance FROM wallets WHERE company_id=? ORDER BY name");$wallets->execute([$cid]);$wallets=$wallets->fetchAll();
$wastes=$pdo->prepare("SELECT * FROM waste_types WHERE company_id=? AND status=1 ORDER BY name");$wastes->execute([$cid]);$wastes=$wastes->fetchAll();
if($_SERVER['REQUEST_METHOD']==='POST'){csrf_check();$pdo->beginTransaction();try{
$inv='INV-'.date('YmdHis').'-'.random_int(100,999);
$weights=$_POST['weight']??[];$ids=$_POST['waste_type_id']??[];$totalW=0;$total=0;$details=[];
foreach($ids as $i=>$wid){$wt=(float)($weights[$i]??0);if($wt<=0)continue;
$s=$pdo->prepare("SELECT sell_price,stock_kg,name FROM waste_types WHERE id=? AND company_id=?");$s->execute([$wid,$cid]);$rw=$s->fetch();
if(!$rw)throw new Exception("Jenis sampah tidak valid.");
if($wt>(float)$rw['stock_kg'])throw new Exception("Stok {$rw['name']} tidak mencukupi: sisa ".number_format((float)$rw['stock_kg'],2,',','.')." kg, diminta ".number_format($wt,2,',','.')." kg.");
$price=(float)$rw['sell_price'];$sub=$wt*$price;$totalW+=$wt;$total+=$sub;$details[]=[$wid,$wt,$price,$sub];}
if(!$details)throw new Exception('Minimal satu jenis sampah harus diisi.');
$walletId=(int)$_POST['wallet_id'];
if($walletId<=0)throw new Exception('Pilih kas/rekening penerima.');
$documentation=store_transaction_documentation('documentation','sale');
$s=$pdo->prepare("INSERT INTO sales(company_id,contact_id,wallet_id,invoice_no,sale_date,total_weight,total_amount,status,notes,documentation) VALUES(?,?,?,?,?,?,?,?,?,?)");
$s->execute([$cid,$_POST['contact_id']?:null,$walletId,$inv,$_POST['sale_date'],$totalW,$total,$_POST['status'],$_POST['notes'],$documentation]);
$sid=$pdo->lastInsertId();
foreach($details as $d){$pdo->prepare("INSERT INTO sale_details(sale_id,waste_type_id,weight_kg,price_per_kg,subtotal) VALUES(?,?,?,?,?)")->execute([$sid,...$d]);
$pdo->prepare("UPDATE waste_types SET stock_kg=stock_kg-? WHERE id=? AND company_id=?")->execute([$d[1],$d[0],$cid]);}
if($_POST['status']==='paid'){
  $pdo->prepare("UPDATE wallets SET balance=balance+? WHERE id=? AND company_id=?")->execute([$total,$walletId,$cid]);
  $cat=$pdo->prepare("SELECT id FROM categories WHERE company_id=? AND type='income' ORDER BY id LIMIT 1");$cat->execute([$cid]);$catId=(int)$cat->fetchColumn();
  $pdo->prepare("INSERT INTO transactions(company_id,wallet_id,category_id,type,amount,description,transaction_date) VALUES(?,?,?,?,?,?,?)")
    ->execute([$cid,$walletId,$catId?:0,'income',$total,"Penjualan $inv ($totalW kg)",$_POST['sale_date']]);
}
$pdo->commit();flash('success',"Penjualan tersimpan. Invoice: $inv, nilai: ".rupiah($total).($totalW>0?", berat: ".number_format($totalW,2,',','.')." kg":""));redirect('sales.php');
}catch(Throwable $e){$pdo->rollBack();flash('error',$e->getMessage());redirect('sales.php');}}
$active='sales';$title='Penjualan Sampah';require __DIR__.'/../includes/header.php';?>
<div class="section-head">
  <div><h1>Penjualan Sampah ke Pengepul</h1><p class="muted">Catat penjualan sampah ke pengepul, otomatis kurangi stok dan tambahkan kas (jika lunas).</p></div>
  <a class="btn ghost sm" href="../index.php">← Kembali</a>
</div>
<div class="card form"><form method="post" enctype="multipart/form-data"><input type="hidden" name="csrf" value="<?=csrf_token()?>">
<div class="form-grid">
  <div class="field"><label>Pembeli (Kontak)</label>
    <select name="contact_id"><option value="">-- Pilih / Belum ada --</option>
      <?php foreach($contacts as $c):?><option value="<?=(int)$c['id']?>"><?=e($c['name'])?></option><?php endforeach;?>
    </select>
  </div>
  <div class="field"><label>Kas/Rekening Penerima</label>
    <select name="wallet_id" required><?php foreach($wallets as $w):?><option value="<?=(int)$w['id']?>"><?=e($w['name'])?> — <?=rupiah((float)$w['balance'])?></option><?php endforeach;?></select>
  </div>
  <div class="field"><label>Tanggal</label><input type="date" name="sale_date" value="<?=date('Y-m-d')?>"></div>
  <div class="field"><label>Status</label>
    <select name="status"><option value="paid">Lunas (Langsung Masuk Kas)</option><option value="pending">Hutang / Belum Bayar</option></select>
  </div>
</div>
<div class="section"><h2>Detail Barang (Sampah yang Dijual)</h2>
<div id="rows"><?php for($i=0;$i<4;$i++):?>
  <div class="form-grid row" style="margin-bottom:8px">
    <div class="field"><select name="waste_type_id[]"><option value="">-- Pilih jenis --</option>
      <?php foreach($wastes as $w):?><option value="<?=$w['id']?>"><?=e($w['name'])?> (Stok: <?=number_format((float)$w['stock_kg'],2,',','.')." kg | ".rupiah((float)$w['sell_price'])?>/kg)</option><?php endforeach;?></select>
    </div>
    <div class="field"><input type="number" step="0.001" min="0" name="weight[]" placeholder="Berat dijual (kg)"></div>
  </div>
<?php endfor;?></div></div>
<div class="form-grid transaction-documentation"><div class="field"><label>Catatan</label><textarea name="notes" placeholder="Nomor polisi, sopir, dll."></textarea></div><div class="field"><label>Dokumentasi Transaksi</label><input type="file" name="documentation" accept="image/jpeg,image/png,image/webp,application/pdf"><small class="field-help">Foto bukti serah terima atau dokumen pendukung. Maksimal 5 MB.</small></div></div>
<div class="actions"><button class="btn">Simpan Penjualan</button><a class="btn ghost" href="sales_history.php">Riwayat Penjualan</a></div>
</form></div>
<?php require __DIR__.'/../includes/footer.php';?>