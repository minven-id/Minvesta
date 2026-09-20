<?php
declare(strict_types=1);

ini_set('session.gc_maxlifetime', '86400');
session_set_cookie_params(['lifetime'=>0,'path'=>'/','httponly'=>true,'samesite'=>'Lax']);
session_start();

const DB_HOST = '127.0.0.1';
const DB_NAME = 'minvesta';
const DB_USER = 'root';
const DB_PASS = '';
function db(): PDO {
    static $pdo = null;
    if ($pdo === null) {
        $dsn = 'mysql:host='.DB_HOST.';dbname='.DB_NAME.';charset=utf8mb4';
        $pdo = new PDO($dsn, DB_USER, DB_PASS, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);
    }
    return $pdo;
}

function e(?string $v): string { return htmlspecialchars($v ?? '', ENT_QUOTES, 'UTF-8'); }
function redirect(string $url): never { header('Location: '.$url); exit; }
function flash(?string $type=null, ?string $message=null): ?array {
    if ($message !== null) $_SESSION['flash'] = ['type'=>$type, 'message'=>$message];
    $f = $_SESSION['flash'] ?? null; unset($_SESSION['flash']); return $f;
}
function csrf_token(): string {
    if (empty($_SESSION['csrf'])) $_SESSION['csrf'] = bin2hex(random_bytes(16));
    return $_SESSION['csrf'];
}
function csrf_check(): void {
    if (!hash_equals($_SESSION['csrf'] ?? '', $_POST['csrf'] ?? '')) {
        http_response_code(419); exit('CSRF token tidak valid.');
    }
}
function rupiah(float $n): string { return 'Rp '.number_format($n, 0, ',', '.'); }

function current_company_id(): int { return (int)($_SESSION['auth_company_id'] ?? 1); }
function company(): array {
    static $c = null;
    if ($c === null) {
        $s = db()->prepare("SELECT * FROM companies WHERE id=?");
        $s->execute([current_company_id()]);
        $c = $s->fetch() ?: ['name'=>'Minvesta'];
    }
    return $c;
}

function is_logged_in(): bool { return !empty($_SESSION['auth_user_id']); }
function ensure_profile_columns(): void {
    static $checked = false;
    if ($checked) return;
    $checked = true;
    $columns = db()->query("SHOW COLUMNS FROM users")->fetchAll(PDO::FETCH_COLUMN);
    $alter = [];
    if (!in_array('phone', $columns, true)) $alter[] = "ADD COLUMN phone VARCHAR(50) NULL AFTER username";
    if (!in_array('profile_photo', $columns, true)) $alter[] = "ADD COLUMN profile_photo VARCHAR(255) NULL AFTER phone";
    if ($alter) db()->exec('ALTER TABLE users '.implode(', ', $alter));
}
function ensure_email_column(): void {
    static $checked = false;
    if ($checked) return;
    $checked = true;
    $columns = db()->query('SHOW COLUMNS FROM users')->fetchAll(PDO::FETCH_COLUMN);
    if (!in_array('email', $columns, true)) db()->exec('ALTER TABLE users ADD COLUMN email VARCHAR(190) NULL AFTER name');
}
function ensure_transaction_documentation_columns(): void {
    static $checked = false;
    if ($checked) return;
    $checked = true;
    ensure_sales_columns();
    foreach (['deposits', 'sales'] as $table) {
        $columns = db()->query("SHOW COLUMNS FROM `$table`")->fetchAll(PDO::FETCH_COLUMN);
        if ($table === 'sales' && !in_array('notes', $columns, true)) {
            db()->exec("ALTER TABLE `sales` ADD COLUMN notes VARCHAR(255) NULL AFTER total_amount");
            $columns[] = 'notes';
        }
        if (!in_array('documentation', $columns, true)) {
            db()->exec("ALTER TABLE `$table` ADD COLUMN documentation VARCHAR(255) NULL AFTER notes");
        }
    }
}
function ensure_sales_columns(): void {
    static $checked = false;
    if ($checked) return;
    $checked = true;
    $columns = db()->query('SHOW COLUMNS FROM sales')->fetchAll(PDO::FETCH_COLUMN);
    $alter = [];
    if (!in_array('contact_id', $columns, true)) $alter[] = 'ADD COLUMN contact_id INT NULL AFTER company_id';
    if (!in_array('status', $columns, true)) $alter[] = "ADD COLUMN status VARCHAR(20) NOT NULL DEFAULT 'paid' AFTER total_amount";
    if ($alter) db()->exec('ALTER TABLE sales '.implode(', ', $alter));
}
function ensure_transactions_columns(): void {
    static $checked = false;
    if ($checked) return;
    $checked = true;
    $columns = db()->query('SHOW COLUMNS FROM transactions')->fetchAll(PDO::FETCH_COLUMN);
    if (!in_array('amount', $columns, true)) {
        db()->exec('ALTER TABLE transactions ADD COLUMN amount DECIMAL(18,2) NOT NULL DEFAULT 0 AFTER type');
    }
}
function store_transaction_documentation(string $field, string $prefix): ?string {
    if (empty($_FILES[$field]['name'])) return null;
    $file = $_FILES[$field];
    if (($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) throw new RuntimeException('Dokumentasi transaksi gagal diunggah.');
    if ((int)$file['size'] > 5 * 1024 * 1024) throw new RuntimeException('Ukuran dokumentasi maksimal 5 MB.');
    $mime = (new finfo(FILEINFO_MIME_TYPE))->file($file['tmp_name']);
    $extensions = ['image/jpeg'=>'jpg','image/png'=>'png','image/webp'=>'webp','application/pdf'=>'pdf'];
    if (!isset($extensions[$mime])) throw new RuntimeException('Dokumentasi harus berformat JPG, PNG, WEBP, atau PDF.');
    $directory = __DIR__.'/../assets/uploads/transactions';
    if (!is_dir($directory) && !mkdir($directory, 0755, true) && !is_dir($directory)) {
        throw new RuntimeException('Folder dokumentasi transaksi tidak dapat dibuat.');
    }
    $filename = $prefix.'_'.date('YmdHis').'_'.bin2hex(random_bytes(5)).'.'.$extensions[$mime];
    if (!move_uploaded_file($file['tmp_name'], $directory.'/'.$filename)) throw new RuntimeException('Dokumentasi transaksi tidak dapat disimpan.');
    return 'assets/uploads/transactions/'.$filename;
}
function current_user(): array {
    static $u = null;
    if ($u === null) {
        if (empty($_SESSION['auth_user_id'])) {
            $u = ['id'=>0,'name'=>'Guest','email'=>'','username'=>'guest','phone'=>'','profile_photo'=>'','role'=>'guest'];
        } else {
            ensure_email_column();
            ensure_profile_columns();
            $s = db()->prepare("SELECT id,company_id,name,email,username,phone,profile_photo,role FROM users WHERE id=?");
            $s->execute([(int)$_SESSION['auth_user_id']]);
            $u = $s->fetch() ?: ['id'=>0,'name'=>'Guest','email'=>'','username'=>'guest','phone'=>'','profile_photo'=>'','role'=>'guest'];
        }
    }
    return $u;
}
function require_login(): void {
    if (!is_logged_in()) {
        $prefix = strpos($_SERVER['SCRIPT_NAME'] ?? '', '/modules/') !== false ? '../' : '';
        redirect($prefix.'login.php');
    }
}
function attempt_login(string $username, string $password): bool {
    $username = trim($username);
    if ($username === '') return false;

    ensure_email_column();
    $s = db()->prepare("SELECT * FROM users WHERE username=? LIMIT 1");
    $s->execute([trim($username)]);
    $user = $s->fetch();

    if ($user && password_verify($password, $user['password'] ?? '')) {
        $_SESSION['auth_user_id'] = (int)$user['id'];
        $_SESSION['auth_company_id'] = (int)$user['company_id'];
        $_SESSION['auth_user_name'] = $user['name'];
        $_SESSION['auth_username'] = $user['username'] ?: $user['email'];
        $_SESSION['auth_role'] = $user['role'];
        return true;
    }

    return false;
}
function logout(): void {
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $p = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $p['path'], $p['domain'], $p['secure'], $p['httponly']);
    }
    session_destroy();
}
