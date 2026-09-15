<?php
require_once __DIR__.'/../config/config.php';
$pdo=db(); $cid=current_company_id();

if($_SERVER['REQUEST_METHOD']==='POST'){
    csrf_check();
    $action = $_POST['action'] ?? 'add';
    try {
        if($action==='add'){
            $pdo->prepare("INSERT INTO contacts(company_id,name,type,phone,email) VALUES(?,?,?,?,?)")
                ->execute([$cid,trim($_POST['name']),$_POST['type'],trim($_POST['phone']),trim($_POST['email'])]);
            flash('success','Kontak ditambahkan.');
        } elseif($action==='edit'){
            $id=(int)$_POST['id'];
            $pdo->prepare("UPDATE contacts SET name=?,type=?,phone=?,email=? WHERE id=? AND company_id=?")
                ->execute([trim($_POST['name']),$_POST['type'],trim($_POST['phone']),trim($_POST['email']),$id,$cid]);
            flash('success','Kontak diperbarui.');
        } elseif($action==='delete'){
            $id=(int)$_POST['id'];
            $chk=$pdo->prepare("SELECT COUNT(*) FROM sales WHERE contact_id=? AND company_id=?"); $chk->execute([$id,$cid]);
            if((int)$chk->fetchColumn()>0) throw new Exception('Kontak tidak dapat dihapus karena tercatat di penjualan.');
            $pdo->prepare("DELETE FROM contacts WHERE id=? AND company_id=?")->execute([$id,$cid]);
            flash('success','Kontak dihapus.');
        }
    } catch(Throwable $e){ flash('error',$e->getMessage()); }
    redirect('contacts.php');
}

$editRow = null;
if(isset($_GET['edit'])){
    $eid=(int)$_GET['edit'];
    $es=$pdo->prepare("SELECT * FROM contacts WHERE id=? AND company_id=?"); $es->execute([$eid,$cid]); $editRow=$es->fetch();
}

$q=$pdo->prepare("SELECT * FROM contacts WHERE company_id=? ORDER BY name");
$q->execute([$cid]);$rows=$q->fetchAll();
$active='contacts';$title='Kontak';require __DIR__.'/../includes/header.php';?>
<div class="section-head">
  <div><h1>Pengepul / Kontak</h1><p class="muted">Kelola data pengepul, pemasok, dan kontak kerja sama lainnya.</p></div>
  <div style="display:flex;gap:8px;align-items:center;flex-wrap:wrap">
    <a class="btn ghost sm" href="../index.php">← Kembali</a>
    <a class="btn sm" href="#tambah">+ Tambah Kontak</a>
  </div>
</div>

<div class="card form" id="tambah">
  <h2 style="margin:0 0 14px"><?=$editRow?'Edit Kontak':'Tambah Kontak Baru'?></h2>
  <form method="post">
  <input type="hidden" name="csrf" value="<?=csrf_token()?>">
  <input type="hidden" name="action" value="<?=$editRow?'edit':'add'?>">
  <?php if($editRow):?><input type="hidden" name="id" value="<?=(int)$editRow['id']?>"><?php endif;?>
  <div class="form-grid">
    <div class="field"><label>Nama</label><input name="name" required value="<?=$editRow?e($editRow['name']):''?>"></div>
    <div class="field"><label>Tipe</label>
      <select name="type">
        <?php foreach(['customer','supplier','Pengepul','other'] as $t):?>
          <option <?=$editRow && $editRow['type']===$t?'selected':''?>><?=e($t)?></option>
        <?php endforeach;?>
      </select>
    </div>
    <div class="field"><label>Telepon</label><input name="phone" value="<?=$editRow?e($editRow['phone']):''?>"></div>
    <div class="field"><label>Email</label><input name="email" value="<?=$editRow?e($editRow['email']):''?>"></div>
  </div>
  <div class="actions">
    <button class="btn"><?=$editRow?'Simpan Perubahan':'Tambah Kontak'?></button>
    <?php if($editRow):?><a class="btn ghost" href="contacts.php">Batal Edit</a><?php endif;?>
  </div>
  </form>
</div>

<div class="section">
  <div class="section-head"><h2>Daftar Kontak (<?=count($rows)?>)</h2></div>
  <div class="table-wrap"><table class="table">
    <tr><th>Nama</th><th>Tipe</th><th>Telepon</th><th>Email</th><th class="right">Aksi</th></tr>
    <?php foreach($rows as $r):?>
      <tr>
        <td><b><?=e($r['name'])?></b></td>
        <td><span class="badge sage"><?=e($r['type'])?></span></td>
        <td><?=e($r['phone'])?></td>
        <td><?=e($r['email'])?></td>
        <td class="right">
          <div style="display:flex;gap:6px;justify-content:flex-end;flex-wrap:wrap">
            <a class="btn ghost sm" href="?edit=<?=(int)$r['id']?>">Edit</a>
            <form method="post" style="display:inline" onsubmit="return confirm('Hapus kontak <?=e($r['name'])?>?')">
              <input type="hidden" name="csrf" value="<?=csrf_token()?>">
              <input type="hidden" name="action" value="delete">
              <input type="hidden" name="id" value="<?=(int)$r['id']?>">
              <button class="btn danger sm" type="submit">Hapus</button>
            </form>
          </div>
        </td>
      </tr>
    <?php endforeach;?>
    <?php if(!$rows):?><tr><td colspan="5" class="empty">Belum ada kontak. Silakan tambah di atas.</td></tr><?php endif;?>
  </table></div>
</div>
<?php require __DIR__.'/../includes/footer.php'; ?>
