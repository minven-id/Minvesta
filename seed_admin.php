<?php
require_once __DIR__.'/config/config.php';

$password = 'password';
$hash = password_hash($password, PASSWORD_DEFAULT);

$companyName = 'Minvesta Utama';
$companyCode = 'BSU';

$db = db();

$db->beginTransaction();
try {
    $chk = $db->query("SELECT COUNT(*) FROM companies WHERE id=1")->fetchColumn();
    if (!$chk) {
        $db->prepare("INSERT INTO companies(id,name,code) VALUES(1,?,?)")->execute([$companyName, $companyCode]);
        echo "✔ Perusahaan #1 dibuat: $companyName<br>";
    } else {
        $db->prepare("UPDATE companies SET name=?, code=? WHERE id=1")->execute([$companyName, $companyCode]);
        echo "✔ Perusahaan #1 diperbarui: $companyName<br>";
    }

    $db->prepare("DELETE FROM users WHERE username='admin'")->execute();
     $db->prepare("INSERT INTO users(company_id,name,email,username,password,role) VALUES(1,?,?,?,?,?)")
         ->execute(['Administrator','admin@minvesta.local','admin',$hash,'admin']);
    echo "✔ User admin dibuat/direset<br>";
    echo "➡ Email: <b>admin@minvesta.local</b><br>";
    echo "➡ Password: <b>password</b><br>";

    $db->commit();
    echo "<hr>✅ Seed berhasil. <a href='login.php'>Ke Halaman Login →</a>";
} catch (Exception $e) {
    $db->rollBack();
    echo "❌ Error: " . $e->getMessage();
}
