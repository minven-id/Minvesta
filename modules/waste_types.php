<?php
require_once __DIR__.'/../config/config.php';
$pdo=db(); $cid=current_company_id();

if($_SERVER['REQUEST_METHOD']==='POST'){
    csrf_check();
    $action = $_POST['action'] ?? 'add';
    try {
        if($action==='add'){
            $pdo->prepare("INSERT INTO waste_types(company_id,name,unit,buy_price,sell_price,status) VALUES(?,?,?,?,?,1)")
                ->execute([$cid,trim($_POST['name']),$_POST['unit'],(float)$_POST['buy_price'],(float)$_POST['sell_price']]);
            flash('success','Jenis sampah ditambahkan.');
        } elseif($action==='edit'){
            $id=(int)$_POST['id'];
            $pdo->prepare("UPDATE waste_types SET name=?,unit=?,buy_price=?,sell_price=? WHERE id=? AND company_id=?")
                ->execute([trim($_POST['name']),$_POST['unit'],(float)$_POST['buy_price'],(float)$_POST['sell_price'],$id,$cid]);
            flash('success','Jenis sampah diperbarui.');
        } elseif($action==='delete'){
            $id=(int)$_POST['id'];
            $chk=$pdo->prepare("SELECT COUNT(*) FROM deposit_details d JOIN deposits x ON x.id=d.deposit_id WHERE d.waste_type_id=? AND x.company_id=?"); $chk->execute([$id,$cid]);
            if((int)$chk->fetchColumn()>0) throw new Exception('Jenis sampah tidak dapat dihapus karena sudah terpakai di setoran.');
            $chk=$pdo->prepare("SELECT COUNT(*) FROM sale_details d JOIN sales x ON x.id=d.sale_id WHERE d.waste_type_id=? AND x.company_id=?"); $chk->execute([$id,$cid]);
            if((int)$chk->fetchColumn()>0) throw new Exception('Jenis sampah tidak dapat dihapus karena sudah terpakai di penjualan.');
            $pdo->prepare("DELETE FROM waste_types WHERE id=? AND company_id=?")->execute([$id,$cid]);
            flash('success','Jenis sampah dihapus.');
        }
    } catch(Throwable $e){ flash('error',$e->getMessage()); }
    redirect('waste_types.php');
}

$editRow = null;
if(isset($_GET['edit'])){
    $eid=(int)$_GET['edit'];
    $es=$pdo->prepare("SELECT * FROM waste_types WHERE id=? AND company_id=?"); $es->execute([$eid,$cid]); $editRow=$es->fetch();
}

$s=$pdo->prepare("SELECT * FROM waste_types WHERE company_id=? ORDER BY name");
$s->execute([$cid]);$rows=$s->fetchAll();
$active='waste_types';$title='Jenis Sampah';require __DIR__.'/../includes/header.php';?>
<div class="section-head">
  <div><h1>Jenis Sampah &amp; Harga</h1><p class="muted">Kelola master jenis sampah beserta harga beli dan harga jual.</p></div>
  <div style="display:flex;gap:8px;align-items:center;flex-wrap:wrap">
    <a class="btn ghost sm" href="../index.php">← Kembali</a>
    <a class="btn sm" href="#tambah">+ Tambah Jenis</a>
  </div>
</div>

<div class="card form" id="tambah">
  <h2 style="margin:0 0 14px"><?=$editRow?'Edit Jenis Sampah':'Tambah Jenis Sampah Baru'?></h2>
  <form method="post">
  <input type="hidden" name="csrf" value="<?=csrf_token()?>">
  <input type="hidden" name="action" value="<?=$editRow?'edit':'add'?>">
  <?php if($editRow):?><input type="hidden" name="id" value="<?=(int)$editRow['id']?>"><?php endif;?>
  <div class="form-grid">
    <div class="field"><label>Jenis Sampah</label><input name="name" required value="<?=$editRow?e($editRow['name']):''?>"></div>
    <div class="field"><label>Satuan</label><input name="unit" value="<?=$editRow?e($editRow['unit']):'kg'?>"></div>
    <div class="field"><label>Harga Beli Nasabah / kg</label><input type="number" name="buy_price" min="0" required value="<?=$editRow?(float)$editRow['buy_price']:''?>"></div>
    <div class="field"><label>Harga Jual / kg</label><input type="number" name="sell_price" min="0" required value="<?=$editRow?(float)$editRow['sell_price']:''?>"></div>
    <?php if($editRow):?>
    <div class="field"><label>Stok Saat Ini</label><input value="<?=number_format((float)$editRow['stock_kg'],2,',','.')?> kg" disabled></div>
    <?php endif;?>
  </div>
  <div class="actions">
    <button class="btn"><?=$editRow?'Simpan Perubahan':'Tambah Jenis'?></button>
    <?php if($editRow):?><a class="btn ghost" href="waste_types.php">Batal Edit</a><?php endif;?>
  </div>
  </form>
</div>

<div class="section">
  <div class="section-head"><h2>Daftar Jenis Sampah (<?=count($rows)?>)</h2></div>
  <div class="table-wrap"><table class="table">
    <tr>
      <th>Jenis</th><th>Satuan</th><th class="right">Harga Beli</th><th class="right">Harga Jual</th>
      <th class="right">Stok (kg)</th><th class="right">Aksi</th>
    </tr>
    <?php foreach($rows as $r):?>
      <tr>
        <td><b><?=e($r['name'])?></b></td>
        <td><?=e($r['unit'])?></td>
        <td class="right"><?=rupiah((float)$r['buy_price'])?></td>
        <td class="right"><?=rupiah((float)$r['sell_price'])?></td>
        <td class="right"><?=number_format((float)$r['stock_kg'],2,',','.')?></td>
        <td class="right">
          <div style="display:flex;gap:6px;justify-content:flex-end;flex-wrap:wrap">
            <a class="btn ghost sm" href="?edit=<?=(int)$r['id']?>">Edit</a>
            <form method="post" style="display:inline" onsubmit="return confirm('Hapus jenis sampah <?=e($r['name'])?>?')">
              <input type="hidden" name="csrf" value="<?=csrf_token()?>">
              <input type="hidden" name="action" value="delete">
              <input type="hidden" name="id" value="<?=(int)$r['id']?>">
              <button class="btn danger sm" type="submit">Hapus</button>
            </form>
          </div>
        </td>
      </tr>
    <?php endforeach;?>
    <?php if(!$rows):?><tr><td colspan="6" class="empty">Belum ada jenis sampah. Silakan tambah di atas.</td></tr><?php endif;?>
  </table></div>
</div>
<?php require __DIR__.'/../includes/footer.php'; ?>
