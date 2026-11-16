<?php
require_once '../include/db.php';
require_once '../include/admin_auth.php'; // 改为后台权限认证
require_once '../include/log.php';
if (session_status() == PHP_SESSION_NONE) session_start();
if (empty($_SESSION['admin_user_id']) || !is_admin_user($pdo, $_SESSION['admin_user_id'])) header('Location: login.php');
$id = intval($_GET['id']);
$stmt = $pdo->prepare("SELECT * FROM users WHERE id=?");
$stmt->execute([$id]);
$user = $stmt->fetch();
if (!$user) die('用户不存在');
$newpwd = substr(md5(uniqid()),0,8);
$hash = password_hash($newpwd, PASSWORD_DEFAULT);
$stmt = $pdo->prepare("UPDATE users SET password=? WHERE id=?");
$stmt->execute([$hash, $id]);
write_log($pdo, $_SESSION['admin_user_id'], 'reset_pwd', "管理员重置用户{$id}密码");
include 'header.php';
?>
<div class="alert alert-success mt-5" style="max-width:400px;margin:auto;">
    <h5>新密码: <b><?=$newpwd?></b></h5>
    <a href="users.php" class="btn btn-primary mt-3">返回用户列表</a>
</div>
<?php include 'footer.php'; ?>