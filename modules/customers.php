<?php
require_once __DIR__.'/../config/config.php';
$pdo=db(); $cid=current_company_id();

if($_SERVER['REQUEST_METHOD']==='POST'){
    csrf_check();
    $action = $_POST['action'] ?? 'add';
    try {
        if($action==='add'){
            $s=$pdo->prepare("INSERT INTO customers(company_id,customer_no,name,phone,address,join_date,status) VALUES(?,?,?,?,?,?,?)");
            $s->execute([$cid,trim($_POST['customer_no']),trim($_POST['name']),trim($_POST['phone']),trim($_POST['address']),$_POST['join_date'],$_POST['status']]);
            flash('success','Nasabah berhasil ditambahkan.');
        } elseif($action==='edit'){
            $id=(int)$_POST['id'];
            $s=$pdo->prepare("UPDATE customers SET customer_no=?,name=?,phone=?,address=?,join_date=?,status=? WHERE id=? AND company_id=?");
            $s->execute([trim($_POST['customer_no']),trim($_POST['name']),trim($_POST['phone']),trim($_POST['address']),$_POST['join_date'],$_POST['status'],$id,$cid]);
            flash('success','Nasabah berhasil diperbarui.');
        } elseif($action==='delete'){
            $id=(int)$_POST['id'];
            $chk=$pdo->prepare("SELECT COUNT(*) FROM deposits WHERE customer_id=? AND company_id=?"); $chk->execute([$id,$cid]);
            if((int)$chk->fetchColumn()>0) throw new Exception('Nasabah tidak dapat dihapus karena memiliki riwayat setoran.');
            $chk=$pdo->prepare("SELECT COUNT(*) FROM withdrawals WHERE customer_id=? AND company_id=?"); $chk->execute([$id,$cid]);
            if((int)$chk->fetchColumn()>0) throw new Exception('Nasabah tidak dapat dihapus karena memiliki riwayat penarikan.');
            $pdo->prepare("DELETE FROM customer_mutations WHERE customer_id=? AND company_id=?")->execute([$id,$cid]);
            $s=$pdo->prepare("DELETE FROM customers WHERE id=? AND company_id=?");
            $s->execute([$id,$cid]);
            flash('success','Nasabah berhasil dihapus.');
        }
    } catch(Throwable $e){ flash('error',$e->getMessage()); }
    redirect('customers.php');
}

$editRow = null;
if(isset($_GET['edit'])){
    $eid=(int)$_GET['edit'];
    $es=$pdo->prepare("SELECT * FROM customers WHERE id=? AND company_id=?"); $es->execute([$eid,$cid]); $editRow=$es->fetch();
}

$s=$pdo->prepare("SELECT * FROM customers WHERE company_id=? ORDER BY name");
$s->execute([$cid]);$rows=$s->fetchAll();
$active='customers';$title='Nasabah';require __DIR__.'/../includes/header.php';?>
<div class="section-head">
  <div><h1>Nasabah</h1><p class="muted">Kelola data nasabah: tambah, ubah, atau hapus sesuai kebutuhan.</p></div>
  <div style="display:flex;gap:8px;align-items:center;flex-wrap:wrap">
    <a class="btn ghost sm" href="../index.php">← Kembali</a>
    <a class="btn sm" href="#tambah">+ Tambah Nasabah</a>
  </div>
</div>

<div class="card form" id="tambah">
  <h2 style="margin:0 0 14px"><?=$editRow?'Edit Nasabah':'Tambah Nasabah Baru'?></h2>
  <form method="post">
  <input type="hidden" name="csrf" value="<?=csrf_token()?>">
  <input type="hidden" name="action" value="<?=$editRow?'edit':'add'?>">
  <?php if($editRow):?><input type="hidden" name="id" value="<?=(int)$editRow['id']?>"><?php endif;?>
  <div class="form-grid">
    <div class="field"><label>No. Nasabah</label><input name="customer_no" placeholder="NSB-0002" required value="<?=$editRow?e($editRow['customer_no']):''?>"></div>
    <div class="field"><label>Nama</label><input name="name" required value="<?=$editRow?e($editRow['name']):''?>"></div>
    <div class="field"><label>No. HP</label><input name="phone" value="<?=$editRow?e($editRow['phone']):''?>"></div>
    <div class="field"><label>Tanggal Gabung</label><input type="date" name="join_date" value="<?=$editRow?e($editRow['join_date']):date('Y-m-d')?>"></div>
    <div class="field full"><label>Alamat</label><textarea name="address"><?=$editRow?e($editRow['address']):''?></textarea></div>
    <div class="field">
      <label>Status</label>
      <select name="status">
        <?php foreach(['active','inactive','blacklist'] as $st):?>
          <option <?=$editRow && $editRow['status']===$st?'selected':''?>><?=e($st)?></option>
        <?php endforeach;?>
      </select>
    </div>
    <?php if($editRow):?>
    <div class="field"><label>Saldo Saat Ini</label><input value="<?=rupiah((float)$editRow['balance'])?>" disabled></div>
    <?php endif;?>
  </div>
  <div class="actions">
    <button class="btn"><?=$editRow?'Simpan Perubahan':'Tambah Nasabah'?></button>
    <?php if($editRow):?><a class="btn ghost" href="customers.php">Batal Edit</a><?php endif;?>
  </div>
  </form>
</div>

<div class="section">
  <div class="section-head"><h2>Daftar Nasabah (<?=count($rows)?>)</h2></div>
  <div class="table-wrap"><table class="table">
    <tr>
      <th>No</th><th>No. Nasabah</th><th>Nama</th><th>HP</th><th>Status</th>
      <th class="right">Saldo</th><th class="right">Aksi</th>
    </tr>
    <?php foreach($rows as $i=>$r):?>
      <tr>
        <td><?=$i+1?></td>
        <td><?=e($r['customer_no'])?></td>
        <td><b><?=e($r['name'])?></b><?php if($r['address']):?><br><small class="muted"><?=e($r['address'])?></small><?php endif;?></td>
        <td><?=e($r['phone'])?></td>
        <td><span class="badge <?=($r['status']==='active'?'success':($r['status']==='blacklist'?'danger':'warning'))?>"><?=ucfirst(e($r['status']))?></span></td>
        <td class="right"><?=rupiah((float)$r['balance'])?></td>
        <td class="right">
          <div style="display:flex;gap:6px;justify-content:flex-end;flex-wrap:wrap">
            <a class="btn ghost sm" href="?edit=<?=(int)$r['id']?>">Edit</a>
            <form method="post" style="display:inline" onsubmit="return confirm('Hapus nasabah <?=e($r['name'])?>? Saldo tidak akan dikembalikan.')">
              <input type="hidden" name="csrf" value="<?=csrf_token()?>">
              <input type="hidden" name="action" value="delete">
              <input type="hidden" name="id" value="<?=(int)$r['id']?>">
              <button class="btn danger sm" type="submit">Hapus</button>
            </form>
          </div>
        </td>
      </tr>
    <?php endforeach;?>
    <?php if(!$rows):?><tr><td colspan="7" class="empty">Belum ada nasabah. Silakan tambah di atas.</td></tr><?php endif;?>
  </table></div>
</div>
<?php require __DIR__.'/../includes/footer.php'; ?>
