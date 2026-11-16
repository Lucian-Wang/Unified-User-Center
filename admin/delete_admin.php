<?php
require_once '../include/db.php';
require_once '../include/admin_auth.php';
if (session_status() == PHP_SESSION_NONE) session_start();
require_admin_login($pdo);

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// 不允许删除自己
if ($id == $_SESSION['admin_user_id']) {
    header('Location: admins.php'); exit;
}

// 检查是否存在
$stmt = $pdo->prepare("SELECT * FROM admin_users WHERE id=?");
$stmt->execute([$id]);
$admin = $stmt->fetch();
if (!$admin) {
    header('Location: admins.php'); exit;
}

// 删除关联角色
$stmt = $pdo->prepare("DELETE FROM admin_user_roles WHERE user_id=?");
$stmt->execute([$id]);
// 删除管理员账号
$stmt = $pdo->prepare("DELETE FROM admin_users WHERE id=?");
$stmt->execute([$id]);

header('Location: admins.php');
exit;