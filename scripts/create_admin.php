<?php
// Run only from the CLI after importing install.sql and configuring environment variables.
if (PHP_SAPI !== 'cli') { http_response_code(404); exit; }
require_once __DIR__ . '/../include/config.php';
$username = getenv('ADMIN_USERNAME') ?: 'admin';
$password = getenv('ADMIN_PASSWORD') ?: '';
if (strlen($password) < 16) { fwrite(STDERR, "Set ADMIN_PASSWORD to a unique password of at least 16 characters.\n"); exit(1); }
$pdo = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4', DB_USER, DB_PASS, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
$pdo->beginTransaction();
try {
    $role = $pdo->query("SELECT id FROM admin_roles WHERE key_name = 'admin'")->fetchColumn();
    if (!$role) { throw new RuntimeException('Import install.sql before creating an administrator.'); }
    $stmt = $pdo->prepare('INSERT INTO admin_users (username, password, name, status) VALUES (?, ?, ?, 1)');
    $stmt->execute([$username, password_hash($password, PASSWORD_DEFAULT), $username]);
    $id = $pdo->lastInsertId();
    $pdo->prepare('INSERT INTO admin_user_roles (user_id, role_id) VALUES (?, ?)')->execute([$id, $role]);
    $pdo->commit();
    echo "Administrator created. Clear ADMIN_PASSWORD from your environment.\n";
} catch (Throwable $e) {
    $pdo->rollBack();
    fwrite(STDERR, "Administrator creation failed; check database configuration and existing usernames.\n");
    exit(1);
}
