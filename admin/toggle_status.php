<?php
require_once '../include/db.php';
require_once '../include/admin_auth.php'; // 改为后台权限认证
require_once '../include/log.php';
if (session_status() == PHP_SESSION_NONE) session_start();
if (empty($_SESSION['admin_user_id']) || !is_admin_user($pdo, $_SESSION['admin_user_id'])) header('Location: login.php');
$id = intval($_GET['id']);
$stmt = $pdo->prepare("SELECT status FROM users WHERE id=?");
$stmt->execute([$id]);
$user = $stmt->fetch();
if (!$user) die('用户不存在');
$new_status = $user['status'] ? 0 : 1;
$stmt = $pdo->prepare("UPDATE users SET status=? WHERE id=?");
$stmt->execute([$new_status, $id]);
write_log($pdo, $_SESSION['admin_user_id'], 'toggle_status', "管理员切换用户{$id}状态为{$new_status}");
header('Location: users.php');
exit;