<?php
require_once __DIR__.'/../config/config.php';
$pdo = db();
$cid = current_company_id();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $action = $_POST['action'] ?? '';

    try {
        if ($action === 'add') {
            $name = trim($_POST['name'] ?? '');
            $type = trim($_POST['type'] ?? 'Cash');
            $balance = (float)($_POST['balance'] ?? 0);
            if ($name === '') throw new Exception('Nama dompet wajib diisi.');
            $s = $pdo->prepare("INSERT INTO wallets(company_id,name,type,balance) VALUES(?,?,?,?)");
            $s->execute([$cid,$name,$type,$balance]);
            flash('success','Dompet baru berhasil dibuat.');
        } elseif ($action === 'edit') {
            $id = (int)$_POST['id'];
            $name = trim($_POST['name'] ?? '');
            $type = trim($_POST['type'] ?? 'Cash');
            if ($name === '') throw new Exception('Nama dompet wajib diisi.');
            $s = $pdo->prepare("UPDATE wallets SET name=?,type=? WHERE id=? AND company_id=?");
            $s->execute([$name,$type,$id,$cid]);
            flash('success','Dompet berhasil diperbarui.');
        } elseif ($action === 'delete') {
            $id = (int)$_POST['id'];
            $checks = [
                ["SELECT COUNT(*) FROM transactions WHERE wallet_id=? AND company_id=?", [$id,$cid]],
                ["SELECT COUNT(*) FROM withdrawals WHERE wallet_id=? AND company_id=?", [$id,$cid]],
                ["SELECT COUNT(*) FROM sales WHERE wallet_id=? AND company_id=?", [$id,$cid]],
                ["SELECT COUNT(*) FROM transfers WHERE (from_wallet=? OR to_wallet=?) AND company_id=?", [$id,$id,$cid]],
            ];
            foreach ($checks as [$sql,$args]) {
                $q=$pdo->prepare($sql); $q->execute($args);
                if ((int)$q->fetchColumn() > 0) throw new Exception('Dompet tidak dapat dihapus karena sudah digunakan dalam transaksi.');
            }
            $s=$pdo->prepare("DELETE FROM wallets WHERE id=? AND company_id=?");
            $s->execute([$id,$cid]);
            flash('success','Dompet berhasil dihapus.');
        }
    } catch (Throwable $e) {
        flash('error',$e->getMessage());
    }
    redirect('wallets.php');
}

$q=$pdo->prepare("SELECT * FROM wallets WHERE company_id=? ORDER BY id");
$q->execute([$cid]);
$rows=$q->fetchAll();
$walletCount=count($rows);
$walletTotal=array_sum(array_map(static fn($wallet)=>(float)$wallet['balance'],$rows));
$active='wallets';
$title='Dompet / Rekening';
require __DIR__.'/../includes/header.php';
?>
<div class="section-head">
  <div><h1>Dompet / Rekening</h1><p class="muted">Jumlah dompet bebas. Anda dapat membuat nama dan tipe dompet sendiri.</p></div>
  <div style="display:flex;gap:8px;align-items:center;flex-wrap:wrap">
    <a class="btn ghost sm" href="../index.php">← Kembali</a>
    <a class="btn sm" href="#tambah">+ Tambah Dompet</a>
  </div>
</div>

<div class="wallet-summary">
  <div class="wallet-summary-card"><span class="summary-label">Total dompet</span><strong><?=number_format($walletCount)?></strong><small>Akun aktif</small></div>
  <div class="wallet-summary-card"><span class="summary-label">Total saldo</span><strong><?=rupiah($walletTotal)?></strong><small>Gabungan seluruh rekening</small></div>
  <div class="wallet-summary-card summary-accent"><span class="summary-label">Akses cepat</span><strong>24/7</strong><small>Mutasi dan detail transaksi</small></div>
</div>

<div class="wallet-grid">
<?php foreach($rows as $r): ?>
  <div class="card wallet wallet-type-<?=e(strtolower(str_replace([' ','-'],'-',(string)$r['type'])))?>">
    <div class="name"><?=e($r['name'])?></div>
    <div class="muted"><?=e($r['type'])?></div>
    <div class="balance"><?=rupiah((float)$r['balance'])?></div>
    <div class="actions">
      <a class="btn secondary" href="wallet_detail.php?id=<?=(int)$r['id']?>">Detail</a>
      <button class="btn secondary" type="button" data-modal-target="edit-<?=(int)$r['id']?>">Edit</button>
      <form method="post" style="display:inline" onsubmit="return confirm('Hapus dompet ini?')">
        <input type="hidden" name="csrf" value="<?=csrf_token()?>">
        <input type="hidden" name="action" value="delete">
        <input type="hidden" name="id" value="<?=(int)$r['id']?>">
        <button class="btn danger" type="submit">Hapus</button>
      </form>
    </div>
  </div>
<?php endforeach; ?>
<?php if (!$rows): ?><div class="card empty">Belum ada dompet. Buat dompet pertama Anda.</div><?php endif; ?>
</div>

<div class="section card wallet-form wallet-create" id="tambah">
<div class="form-header">
  <h2>Tambah Dompet Custom</h2>
  <span class="badge sage">Rekening baru</span>
</div>
<form method="post">
<input type="hidden" name="csrf" value="<?=csrf_token()?>">
<input type="hidden" name="action" value="add">
<div class="form-grid">
  <div class="field"><label>Nama Dompet</label><input name="name" placeholder="Contoh: Kas Operasional, BCA, Mandiri, QRIS, Dana" required></div>
  <div class="field"><label>Tipe</label><select name="type"><option>Cash</option><option>Bank</option><option>E-Wallet</option><option>Kas Kecil</option><option>Lainnya</option></select></div>
  <div class="field"><label>Saldo Awal</label><input type="number" step="0.01" name="balance" value="0" min="0"></div>
</div>
<div class="actions">
  <button class="btn">Simpan Dompet</button>
  <a class="btn ghost" href="wallets.php">Batal</a>
</div>
</form>
</div>

<?php foreach($rows as $r): ?>
<dialog class="wallet-modal wallet-form wallet-edit" id="edit-<?=(int)$r['id']?>" aria-labelledby="edit-title-<?=(int)$r['id']?>">
<div class="wallet-modal-head">
  <div><span class="eyebrow">Pengaturan rekening</span><h2 id="edit-title-<?=(int)$r['id']?>">Edit: <?=e($r['name'])?></h2></div>
  <button class="modal-close" type="button" data-modal-close aria-label="Tutup dialog">&times;</button>
</div>
<form method="post">
<input type="hidden" name="csrf" value="<?=csrf_token()?>">
<input type="hidden" name="action" value="edit">
<input type="hidden" name="id" value="<?=(int)$r['id']?>">
<div class="form-grid">
  <div class="field"><label>Nama Dompet</label><input name="name" value="<?=e($r['name'])?>" required></div>
  <div class="field"><label>Tipe</label><select name="type"><?php foreach(['Cash','Bank','E-Wallet','Kas Kecil','Lainnya'] as $t):?><option <?=($r['type']===$t?'selected':'')?>><?=e($t)?></option><?php endforeach;?></select></div>
  <div class="field"><label>Saldo Saat Ini</label><input value="<?=rupiah((float)$r['balance'])?>" disabled></div>
</div>
<div class="actions">
  <button class="btn">Simpan Perubahan</button>
  <a class="btn ghost" href="wallets.php">Batal</a>
</div>
</form>
</dialog>
<?php endforeach; ?>

<?php require __DIR__.'/../includes/footer.php'; ?>