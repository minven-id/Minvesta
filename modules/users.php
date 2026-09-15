<?php
require_once __DIR__.'/../config/config.php';
require_login();
ensure_profile_columns();

$current = current_user();
if (($current['role'] ?? '') !== 'admin') {
    http_response_code(403);
    exit('Akses hanya untuk administrator.');
}

$pdo = db();
$cid = current_company_id();
$roles = ['admin', 'staff'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $action = $_POST['action'] ?? '';
    try {
        $name = trim($_POST['name'] ?? '');
        $username = trim($_POST['username'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $role = in_array($_POST['role'] ?? '', $roles, true) ? $_POST['role'] : 'staff';
        $password = $_POST['password'] ?? '';
        $id = (int)($_POST['id'] ?? 0);

        if ($name === '' || $username === '') throw new Exception('Nama dan username wajib diisi.');
        if (!preg_match('/^[A-Za-z0-9._-]{3,80}$/', $username)) throw new Exception('Username hanya boleh berisi huruf, angka, titik, garis bawah, atau tanda hubung.');

        $duplicate = $pdo->prepare('SELECT COUNT(*) FROM users WHERE company_id=? AND username=? AND id<>?');
        $duplicate->execute([$cid, $username, $id]);
        if ((int)$duplicate->fetchColumn() > 0) throw new Exception('Username sudah digunakan.');

        if ($action === 'add') {
            if (strlen($password) < 8) throw new Exception('Password minimal 8 karakter.');
            $statement = $pdo->prepare('INSERT INTO users(company_id,name,username,phone,password,role) VALUES(?,?,?,?,?,?)');
            $statement->execute([$cid, $name, $username, $phone, password_hash($password, PASSWORD_DEFAULT), $role]);
            flash('success', 'User baru berhasil dibuat.');
        } elseif ($action === 'edit') {
            if ($id <= 0) throw new Exception('User tidak valid.');
            $updates = ['name=?', 'username=?', 'phone=?', 'role=?'];
            $args = [$name, $username, $phone, $role];
            if ($password !== '') {
                if (strlen($password) < 8) throw new Exception('Password minimal 8 karakter.');
                $updates[] = 'password=?';
                $args[] = password_hash($password, PASSWORD_DEFAULT);
            }
            $args[] = $id;
            $args[] = $cid;
            $statement = $pdo->prepare('UPDATE users SET '.implode(', ', $updates).' WHERE id=? AND company_id=?');
            $statement->execute($args);
            flash('success', 'Data user berhasil diperbarui.');
        } elseif ($action === 'delete') {
            if ($id <= 0 || $id === (int)$current['id']) throw new Exception('Akun yang sedang digunakan tidak dapat dihapus.');
            $statement = $pdo->prepare('DELETE FROM users WHERE id=? AND company_id=?');
            $statement->execute([$id, $cid]);
            flash('success', 'User berhasil dihapus.');
        } else {
            throw new Exception('Aksi tidak dikenali.');
        }
    } catch (Throwable $error) {
        flash('error', $error->getMessage());
    }
    redirect('users.php');
}

$query = $pdo->prepare('SELECT id,name,username,phone,role,created_at FROM users WHERE company_id=? ORDER BY name');
$query->execute([$cid]);
$users = $query->fetchAll();
$active = 'users';
$title = 'Manajemen User';
require __DIR__.'/../includes/header.php';
?>
<div class="section-head">
  <div><h1>Manajemen User</h1><p class="muted">Buat dan kelola akses staf yang menggunakan aplikasi Bank Sampah.</p></div>
  <div style="display:flex;gap:8px;align-items:center;flex-wrap:wrap">
    <a class="btn ghost sm" href="setup.php">&larr; Pengaturan</a>
    <a class="btn sm" href="#tambah-user">+ User Baru</a>
  </div>
</div>

<div class="grid user-summary-grid">
  <div class="card stat"><div class="label">Total User</div><div class="value"><?=number_format(count($users))?></div></div>
  <div class="card stat"><div class="label">Administrator</div><div class="value"><?=number_format(count(array_filter($users,static fn($user)=>$user['role']==='admin')))?></div></div>
  <div class="card stat income-card"><div class="label">Status Akses</div><div class="value">Aktif</div></div>
</div>

<div class="section card form" id="tambah-user">
  <div class="profile-card-heading"><span class="eyebrow">Akses aplikasi</span><h2>Tambah User Baru</h2><p>User baru dapat langsung login setelah dibuat.</p></div>
  <form method="post">
    <input type="hidden" name="csrf" value="<?=csrf_token()?>">
    <input type="hidden" name="action" value="add">
    <div class="form-grid">
      <div class="field"><label>Nama Lengkap</label><input name="name" required placeholder="Contoh: Siti Rahma"></div>
      <div class="field"><label>Username</label><input name="username" required placeholder="Contoh: siti.rahma" autocomplete="off"></div>
      <div class="field"><label>Nomor Telepon</label><input name="phone" placeholder="Opsional"></div>
      <div class="field"><label>Role</label><select name="role"><option value="staff">Staff</option><option value="admin">Administrator</option></select></div>
      <div class="field full"><label>Password</label><input type="password" name="password" required minlength="8" placeholder="Minimal 8 karakter" autocomplete="new-password"><small class="field-help">Gunakan password yang kuat dan tidak mudah ditebak.</small></div>
    </div>
    <div class="actions"><button class="btn" type="submit">Simpan User</button><a class="btn ghost" href="users.php">Batal</a></div>
  </form>
</div>

<div class="section table-wrap user-table-wrap">
  <table class="table">
    <tr><th>Nama</th><th>Username</th><th>Telepon</th><th>Role</th><th>Dibuat</th><th class="right">Aksi</th></tr>
    <?php foreach($users as $user): ?>
      <tr>
        <td><b><?=e($user['name'])?></b><?php if((int)$user['id']===(int)$current['id']):?><br><small class="muted">Akun Anda</small><?php endif;?></td>
        <td>@<?=e($user['username'])?></td>
        <td><?=e($user['phone'] ?? '-')?></td>
        <td><span class="badge <?=$user['role']==='admin'?'info':'success'?>"><?=e(ucfirst($user['role']))?></span></td>
        <td><?=e(date('d M Y',strtotime($user['created_at'])))?></td>
        <td class="right"><div class="user-actions"><button class="btn ghost sm" type="button" data-modal-target="edit-user-<?=(int)$user['id']?>">Edit</button><?php if((int)$user['id']!==(int)$current['id']):?><form method="post" onsubmit="return confirm('Hapus user <?=e($user['name'])?>?')"><input type="hidden" name="csrf" value="<?=csrf_token()?>"><input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?=(int)$user['id']?>"><button class="btn danger sm" type="submit">Hapus</button></form><?php endif;?></div></td>
      </tr>
      <dialog class="wallet-modal user-modal" id="edit-user-<?=(int)$user['id']?>">
        <div class="wallet-modal-head"><div><span class="eyebrow">Manajemen akses</span><h2>Edit User</h2></div><button class="modal-close" type="button" data-modal-close aria-label="Tutup dialog">&times;</button></div>
        <form method="post"><input type="hidden" name="csrf" value="<?=csrf_token()?>"><input type="hidden" name="action" value="edit"><input type="hidden" name="id" value="<?=(int)$user['id']?>"><div class="form-grid"><div class="field"><label>Nama Lengkap</label><input name="name" value="<?=e($user['name'])?>" required></div><div class="field"><label>Username</label><input name="username" value="<?=e($user['username'])?>" required></div><div class="field"><label>Nomor Telepon</label><input name="phone" value="<?=e($user['phone'] ?? '')?>"></div><div class="field"><label>Role</label><select name="role"><option value="staff" <?=$user['role']==='staff'?'selected':''?>>Staff</option><option value="admin" <?=$user['role']==='admin'?'selected':''?>>Administrator</option></select></div><div class="field full"><label>Password Baru</label><input type="password" name="password" minlength="8" placeholder="Kosongkan jika tidak diubah" autocomplete="new-password"></div></div><div class="actions"><button class="btn" type="submit">Simpan Perubahan</button><button class="btn ghost" type="button" data-modal-close>Batal</button></div></form>
      </dialog>
    <?php endforeach; ?>
    <?php if(!$users): ?><tr><td colspan="6" class="empty">Belum ada user.</td></tr><?php endif; ?>
  </table>
</div>
<?php require __DIR__.'/../includes/footer.php'; ?>
