<?php
require_once __DIR__.'/../config/config.php';
$pdo=db(); $cid=current_company_id();
if($_SERVER['REQUEST_METHOD']==='POST'){csrf_check();
  $wasteId=(int)$_POST['waste_type_id']; $actual=(float)$_POST['actual_kg']; $reason=trim($_POST['reason']);
  if($wasteId>0 && $reason){try{
    $pdo->beginTransaction();
    $w=$pdo->prepare("SELECT stock_kg,name FROM waste_types WHERE id=? AND company_id=?");
    $w->execute([$wasteId,$cid]); $wt=$w->fetch();
    if(!$wt)throw new Exception('Jenis sampah tidak valid.');
    $diff=$actual-(float)$wt['stock_kg'];
    $pdo->prepare("UPDATE waste_types SET stock_kg=? WHERE id=? AND company_id=?")->execute([$actual,$wasteId,$cid]);
    $pdo->commit();flash('success',"Penyesuaian stok berhasil. {$wt['name']}: " . number_format((float)$wt['stock_kg'],2,',','.') . " → " . number_format($actual,2,',','.') . " kg (Selisih: " . number_format($diff,2,',','.') . "kg).");
  }catch(Throwable $e){$pdo->rollBack();flash('error',$e->getMessage());}
  redirect('stock_adjustment.php');}}
$wastes=$pdo->prepare("SELECT * FROM waste_types WHERE company_id=? ORDER BY name");
$wastes->execute([$cid]); $wasteRows=$wastes->fetchAll();
$active='stock_adjustment';$title='Penyesuaian Stok';require __DIR__.'/../includes/header.php';?>
<div class="section-head">
  <div><h1>Penyesuaian Stok Sampah</h1><p class="muted">Koreksi stok sistem sesuai hasil timbang fisik aktual.</p></div>
  <a class="btn ghost sm" href="../index.php">← Kembali</a>
</div>
<div class="card form">
  <form method="post"><input type="hidden" name="csrf" value="<?=csrf_token()?>">
  <h2 style="margin:0 0 14px">Form Koreksi Stok Fisik</h2>
  <div class="form-grid">
    <div class="field"><label>Jenis Sampah</label>
      <select name="waste_type_id" id="wasteSel" required onchange="document.getElementById('currStock').value=this.options[this.selectedIndex].dataset.stock">
        <option value="" data-stock="">-- Pilih Jenis Sampah --</option>
        <?php foreach($wasteRows as $wr):?>
          <option value="<?=(int)$wr['id']?>" data-stock="<?=e(number_format((float)$wr['stock_kg'],2,',','.'))?> kg">
            <?=e($wr['name'])?> (Stok: <?=number_format((float)$wr['stock_kg'],2,',','.')?> kg)
          </option>
        <?php endforeach;?>
      </select>
    </div>
    <div class="field"><label>Stok di Sistem (Saat Ini)</label><input id="currStock" disabled placeholder="Pilih jenis sampah"></div>
    <div class="field"><label>Stok Fisik Hasil Timbang (kg)</label><input type="number" step="0.001" min="0" name="actual_kg" required placeholder="Masukkan hasil timbang"></div>
    <div class="field"><label>Tanggal</label><input type="date" name="adj_date" value="<?=date('Y-m-d')?>"></div>
    <div class="field full"><label>Alasan Penyesuaian</label><textarea name="reason" required placeholder="Contoh: Stok fisik berbeda setelah opname bulanan, selisih timbangan, dll."></textarea></div>
  </div>
  <div class="actions"><button class="btn">Simpan Penyesuaian Stok</button><a class="btn ghost" href="report_stock.php">Lihat Laporan Stok</a></div>
  </form>
</div>
<div class="section"><div class="section-head"><h2>Kondisi Stok Saat Ini</h2></div>
<div class="table-wrap"><table class="table">
  <tr><th>Jenis Sampah</th><th>Satuan</th><th class="right">Stok Sistem (kg)</th><th class="right">Harga Beli</th><th class="right">Harga Jual</th></tr>
  <?php foreach($wasteRows as $w):?>
    <tr>
      <td><b><?=e($w['name'])?></b></td>
      <td><?=e($w['unit'])?></td>
      <td class="right"><?=number_format((float)$w['stock_kg'],2,',','.')?></td>
      <td class="right"><?=rupiah((float)$w['buy_price'])?></td>
      <td class="right"><?=rupiah((float)$w['sell_price'])?></td>
    </tr>
  <?php endforeach;?>
</table></div></div>
<?php require __DIR__.'/../includes/footer.php';?>