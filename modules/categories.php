<?php
require_once __DIR__.'/../config/config.php';
$pdo=db(); $cid=current_company_id();

if($_SERVER['REQUEST_METHOD']==='POST'){
    csrf_check();
    $action = $_POST['action'] ?? 'add';
    try {
        if($action==='add'){
            $s=$pdo->prepare("INSERT INTO categories(company_id,name,type) VALUES(?,?,?)");
            $s->execute([$cid,trim($_POST['name']),$_POST['type']]);
            flash('success','Kategori ditambahkan.');
        } elseif($action==='edit'){
            $id=(int)$_POST['id'];
            $pdo->prepare("UPDATE categories SET name=?,type=? WHERE id=? AND company_id=?")
                ->execute([trim($_POST['name']),$_POST['type'],$id,$cid]);
            flash('success','Kategori diperbarui.');
        } elseif($action==='delete'){
            $id=(int)$_POST['id'];
            $chk=$pdo->prepare("SELECT COUNT(*) FROM transactions WHERE category_id=? AND company_id=?"); $chk->execute([$id,$cid]);
            if((int)$chk->fetchColumn()>0) throw new Exception('Kategori tidak dapat dihapus karena sudah digunakan dalam transaksi.');
            $pdo->prepare("DELETE FROM categories WHERE id=? AND company_id=?")->execute([$id,$cid]);
            flash('success','Kategori dihapus.');
        }
    } catch(Throwable $e){ flash('error',$e->getMessage()); }
    redirect('categories.php');
}

$editRow = null;
if(isset($_GET['edit'])){
    $eid=(int)$_GET['edit'];
    $es=$pdo->prepare("SELECT * FROM categories WHERE id=? AND company_id=?"); $es->execute([$eid,$cid]); $editRow=$es->fetch();
}

$q=$pdo->prepare("SELECT * FROM categories WHERE company_id=? ORDER BY type,name");
$q->execute([$cid]);$rows=$q->fetchAll();
$active='categories';$title='Kategori';require __DIR__.'/../includes/header.php';?>
<div class="section-head">
  <div><h1>Kategori Keuangan</h1><p class="muted">Kelompokkan pemasukan dan pengeluaran agar laporan keuangan rapi.</p></div>
  <div style="display:flex;gap:8px;align-items:center;flex-wrap:wrap">
    <a class="btn ghost sm" href="../index.php">← Kembali</a>
    <a class="btn sm" href="#tambah">+ Tambah Kategori</a>
  </div>
</div>

<div class="card form" id="tambah">
  <h2 style="margin:0 0 14px"><?=$editRow?'Edit Kategori':'Tambah Kategori Baru'?></h2>
  <form method="post">
  <input type="hidden" name="csrf" value="<?=csrf_token()?>">
  <input type="hidden" name="action" value="<?=$editRow?'edit':'add'?>">
  <?php if($editRow):?><input type="hidden" name="id" value="<?=(int)$editRow['id']?>"><?php endif;?>
  <div class="form-grid">
    <div class="field"><label>Nama Kategori</label><input name="name" required value="<?=$editRow?e($editRow['name']):''?>"></div>
    <div class="field"><label>Jenis</label>
      <select name="type">
        <option value="income" <?=$editRow && $editRow['type']==='income'?'selected':''?>>Pemasukan</option>
        <option value="expense" <?=$editRow && $editRow['type']==='expense'?'selected':''?>>Pengeluaran</option>
      </select>
    </div>
  </div>
  <div class="actions">
    <button class="btn"><?=$editRow?'Simpan Perubahan':'Tambah'?></button>
    <?php if($editRow):?><a class="btn ghost" href="categories.php">Batal Edit</a><?php endif;?>
  </div>
  </form>
</div>

<div class="section">
  <div class="section-head"><h2>Daftar Kategori (<?=count($rows)?>)</h2></div>
  <div class="table-wrap"><table class="table">
    <tr><th>Nama</th><th>Jenis</th><th class="right">Aksi</th></tr>
    <?php foreach($rows as $r):
      $isInc = $r['type']==='income';
    ?>
      <tr>
        <td><b><?=e($r['name'])?></b></td>
        <td><span class="badge <?=$isInc?'success':'danger'?>"><?=$isInc?'Pemasukan':'Pengeluaran'?></span></td>
        <td class="right">
          <div style="display:flex;gap:6px;justify-content:flex-end;flex-wrap:wrap">
            <a class="btn ghost sm" href="?edit=<?=(int)$r['id']?>">Edit</a>
            <form method="post" style="display:inline" onsubmit="return confirm('Hapus kategori <?=e($r['name'])?>?')">
              <input type="hidden" name="csrf" value="<?=csrf_token()?>">
              <input type="hidden" name="action" value="delete">
              <input type="hidden" name="id" value="<?=(int)$r['id']?>">
              <button class="btn danger sm" type="submit">Hapus</button>
            </form>
          </div>
        </td>
      </tr>
    <?php endforeach;?>
    <?php if(!$rows):?><tr><td colspan="3" class="empty">Belum ada kategori. Silakan tambah di atas.</td></tr><?php endif;?>
  </table></div>
</div>
<?php require __DIR__.'/../includes/footer.php'; ?>
