<?php
require_once __DIR__.'/config/config.php';
require_login();
ensure_profile_columns();

$pdo = db();
$userId = (int)$_SESSION['auth_user_id'];
$uploadDir = __DIR__.'/assets/uploads/profiles';
$uploadUrl = 'assets/uploads/profiles/';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $name = trim($_POST['name'] ?? '');
    $username = trim($_POST['username'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $newPassword = $_POST['new_password'] ?? '';
    $photoPath = null;
    $oldPhoto = '';

    try {
        if ($name === '' || $username === '') throw new Exception('Nama dan username wajib diisi.');
        if (!preg_match('/^[A-Za-z0-9._-]{3,80}$/', $username)) {
            throw new Exception('Username hanya boleh berisi huruf, angka, titik, garis bawah, atau tanda hubung.');
        }
        if ($newPassword !== '' && strlen($newPassword) < 8) {
            throw new Exception('Password baru minimal 8 karakter.');
        }

        $current = $pdo->prepare('SELECT profile_photo FROM users WHERE id=? AND company_id=?');
        $current->execute([$userId, current_company_id()]);
        $currentUser = $current->fetch();
        if (!$currentUser) throw new Exception('Data user tidak ditemukan.');
        $oldPhoto = (string)($currentUser['profile_photo'] ?? '');

        $duplicate = $pdo->prepare('SELECT COUNT(*) FROM users WHERE company_id=? AND username=? AND id<>?');
        $duplicate->execute([current_company_id(), $username, $userId]);
        if ((int)$duplicate->fetchColumn() > 0) throw new Exception('Username sudah digunakan user lain.');

        if (!empty($_FILES['profile_photo']['name'])) {
            if ($_FILES['profile_photo']['error'] !== UPLOAD_ERR_OK) throw new Exception('Foto profil gagal diunggah.');
            if ((int)$_FILES['profile_photo']['size'] > 2 * 1024 * 1024) throw new Exception('Ukuran foto maksimal 2 MB.');
            $imageInfo = @getimagesize($_FILES['profile_photo']['tmp_name']);
            $extensions = [IMAGETYPE_JPEG=>'jpg', IMAGETYPE_PNG=>'png', IMAGETYPE_WEBP=>'webp'];
            if (!$imageInfo || !isset($extensions[$imageInfo[2]])) throw new Exception('Foto harus berformat JPG, PNG, atau WEBP.');
            if (!is_dir($uploadDir) && !mkdir($uploadDir, 0755, true) && !is_dir($uploadDir)) {
                throw new Exception('Folder foto profil tidak dapat dibuat.');
            }
            $filename = 'user_'.$userId.'_'.bin2hex(random_bytes(6)).'.'.$extensions[$imageInfo[2]];
            if (!move_uploaded_file($_FILES['profile_photo']['tmp_name'], $uploadDir.'/'.$filename)) {
                throw new Exception('Foto profil tidak dapat disimpan.');
            }
            $photoPath = $uploadUrl.$filename;
        }

        $updates = ['name=?', 'username=?', 'phone=?'];
        $args = [$name, $username, $phone];
        if ($newPassword !== '') {
            $updates[] = 'password=?';
            $args[] = password_hash($newPassword, PASSWORD_DEFAULT);
        }
        if ($photoPath !== null) {
            $updates[] = 'profile_photo=?';
            $args[] = $photoPath;
        }
        $args[] = $userId;
        $args[] = current_company_id();
        $statement = $pdo->prepare('UPDATE users SET '.implode(', ', $updates).' WHERE id=? AND company_id=?');
        $statement->execute($args);
        $_SESSION['auth_user_name'] = $name;
        $_SESSION['auth_username'] = $username;
        if ($photoPath !== null && $oldPhoto !== '' && str_starts_with($oldPhoto, $uploadUrl)) {
            $oldFile = __DIR__.'/'.$oldPhoto;
            if (is_file($oldFile)) @unlink($oldFile);
        }
        flash('success', 'Profil berhasil diperbarui.');
    } catch (Throwable $error) {
        flash('error', $error->getMessage());
    }
    redirect('profile.php');
}

$query = $pdo->prepare('SELECT id,name,username,phone,profile_photo,role,created_at FROM users WHERE id=? AND company_id=?');
$query->execute([$userId, current_company_id()]);
$user = $query->fetch();
if (!$user) redirect('logout.php');

$title = 'Profil Saya';
$active = 'profile';
require __DIR__.'/includes/header.php';
$profileImage = !empty($user['profile_photo']) ? $user['profile_photo'] : '';
$initial = strtoupper(substr($user['name'], 0, 1));
?>
<div class="section-head profile-page-head">
  <div><h1>Profil Saya</h1><p class="muted">Kelola identitas, akses login, dan foto profil Anda.</p></div>
  <a class="btn ghost sm" href="index.php">&larr; Kembali ke Dashboard</a>
</div>

<div class="profile-layout">
  <aside class="profile-summary card">
    <div class="profile-avatar-wrap">
      <?php if ($profileImage): ?><img class="profile-avatar" src="<?=e($profileImage)?>" alt="Foto profil <?=e($user['name'])?>"><?php else: ?><span class="profile-avatar profile-avatar-fallback"><?=e($initial)?></span><?php endif; ?>
    </div>
    <h2><?=e($user['name'])?></h2>
    <p>@<?=e($user['username'])?></p>
    <span class="badge success"><?=e(ucfirst($user['role']))?></span>
    <div class="profile-summary-note">Akun aktif untuk mengelola operasional Bank Sampah.</div>
  </aside>

  <section class="profile-card card">
    <div class="profile-card-heading"><span class="eyebrow">Informasi akun</span><h2>Identitas &amp; akses login</h2><p>Perubahan username dan password akan langsung digunakan saat login berikutnya.</p></div>
    <form method="post" enctype="multipart/form-data">
      <input type="hidden" name="csrf" value="<?=csrf_token()?>">
      <div class="form-grid">
        <div class="field"><label for="profile-name">Nama lengkap</label><input id="profile-name" name="name" value="<?=e($user['name'])?>" required autocomplete="name"></div>
        <div class="field"><label for="profile-username">Username</label><input id="profile-username" name="username" value="<?=e($user['username'])?>" required autocomplete="username"></div>
        <div class="field"><label for="profile-phone">Nomor telepon</label><input id="profile-phone" name="phone" value="<?=e($user['phone'] ?? '')?>" placeholder="Contoh: 0812-3456-7890" autocomplete="tel"></div>
        <div class="field"><label for="profile-password">Password baru</label><input id="profile-password" type="password" name="new_password" placeholder="Kosongkan jika tidak diubah" minlength="8" autocomplete="new-password"><small class="field-help">Minimal 8 karakter.</small></div>
        <div class="field full"><label for="profile-photo">Foto profil</label><input id="profile-photo" type="file" name="profile_photo" accept="image/jpeg,image/png,image/webp" data-photo-input><small class="field-help">JPG, PNG, atau WEBP. Maksimal 2 MB.</small></div>
      </div>
      <div class="profile-photo-preview" data-photo-preview hidden><img alt="Preview foto profil"><span>Preview foto baru</span></div>
      <div class="actions"><button class="btn" type="submit">Simpan Perubahan</button><a class="btn ghost" href="profile.php">Batal</a></div>
    </form>
  </section>
</div>
<script>
document.querySelector('[data-photo-input]')?.addEventListener('change',event=>{
  const file=event.target.files?.[0], preview=document.querySelector('[data-photo-preview]'), image=preview?.querySelector('img');
  if(!file||!preview||!image){if(preview)preview.hidden=true;return;}
  image.src=URL.createObjectURL(file); preview.hidden=false;
});
</script>
<?php require __DIR__.'/includes/footer.php'; ?>
