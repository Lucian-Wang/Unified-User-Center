<?php
if (session_status() == PHP_SESSION_NONE) session_start();
require_once '../include/db.php';
require_once '../include/admin_auth.php';

$admin_name = '管理员';
if (!empty($_SESSION['admin_user_id'])) {
    $admin = get_admin_user($pdo, $_SESSION['admin_user_id']);
    if ($admin && !empty($admin['name'])) {
        $admin_name = htmlspecialchars($admin['name']);
    } elseif ($admin && !empty($admin['username'])) {
        $admin_name = htmlspecialchars($admin['username']);
    }
}
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <title>后台管理</title>
    <meta name="viewport" content="width=device-width,initial-scale=1">
<link href="https://cdn.bootcdn.net/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
<!-- Bootstrap Icons 1.11.3 CSS -->
<link href="https://cdn.bootcdn.net/ajax/libs/bootstrap-icons/1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <style>
    body { background: #f6f8fa; }
    .navbar {
        background: linear-gradient(90deg,#232526 0,#414345 100%);
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    }
    .navbar-brand {
        font-weight: bold;
        color: #fff !important;
        font-size: 1.7rem;
        letter-spacing: 2px;
    }
    .navbar-nav .nav-link {
        color: #e0e0e0 !important;
        font-size: 1.08rem;
        margin-right: 6px;
        transition: color .2s;
    }
    .navbar-nav .nav-link.active, .navbar-nav .nav-link:hover {
        color: #31d2f2 !important;
    }
    .navbar-text, .btn-outline-danger {
        color: #fff !important;
    }
    .btn-outline-danger {
        border: 1px solid #fff !important;
    }
    .btn-outline-danger:hover {
        background: #dc3545 !important;
        color: #fff !important;
        border: 1px solid #dc3545 !important;
    }
    .main-card {
        background: #fff;
        border-radius: 18px;
        box-shadow: 0 4px 24px 2px rgba(80,110,200,.08);
        margin-top: 32px;
        margin-bottom: 32px;
        overflow: hidden;
        border: none;
    }
    .main-card .card-header {
        background: linear-gradient(90deg,#667eea,#764ba2 90%);
        color: #fff;
        font-size: 1.25rem;
        font-weight: 600;
        border-bottom: none;
    }
    .table th, .table td {
        vertical-align: middle !important;
        font-size: 1.08rem;
    }
    .table-hover tbody tr:hover {
        background: #f4f8ff;
        transition: background .2s;
    }
    .btn-main {
        background: linear-gradient(90deg,#31d2f2 0,#764ba2 100%);
        color: #fff;
        border: none;
        box-shadow: 0 2px 8px rgba(49,210,242,0.09);
        transition: background .2s;
    }
    .btn-main:hover {
        background: linear-gradient(90deg,#764ba2 0,#31d2f2 100%);
        color: #fff;
    }
    .btn-action {
        min-width: 80px;
        border-radius: 20px;
        font-weight: 500;
    }
    .user-type-badge {
        border-radius: 10px;
        padding: 4px 12px;
        font-size: .98rem;
        font-weight: 500;
        color: #fff;
        background: linear-gradient(90deg,#31d2f2 0,#764ba2 100%);
        letter-spacing: 1px;
    }
    .user-type-badge.admin { background: linear-gradient(90deg,#ff512f 0,#dd2476 100%) !important; }
    .user-type-badge.manager { background: linear-gradient(90deg,#36d1c4 0,#1e90ff 100%) !important; }
    .user-type-badge.user { background: linear-gradient(90deg,#888 0,#bbb 100%) !important; }
    .footer-bar {
        color: #aaa;
        font-size: .98rem;
        margin-top: 60px;
        margin-bottom: 20px;
    }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg mb-4">
  <div class="container-fluid">
    <a class="navbar-brand" href="dashboard.php"><i class="bi bi-shield-lock-fill me-2"></i>后台管理</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#adminNav" aria-controls="adminNav" aria-expanded="false" aria-label="菜单">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="adminNav">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        <li class="nav-item"><a class="nav-link<?=basename($_SERVER['PHP_SELF'])=='users.php'?' active':''?>" href="users.php"><i class="bi bi-people"></i> 用户管理</a></li>
        <li class="nav-item"><a class="nav-link<?=basename($_SERVER['PHP_SELF'])=='admins.php'?' active':''?>" href="admins.php"><i class="bi bi-person-lock"></i> 管理员管理</a></li>
        <li class="nav-item"><a class="nav-link<?=basename($_SERVER['PHP_SELF'])=='logs.php'?' active':''?>" href="logs.php"><i class="bi bi-clock-history"></i> 操作日志</a></li>
        <li class="nav-item"><a class="nav-link<?=basename($_SERVER['PHP_SELF'])=='settings.php'?' active':''?>" href="settings.php"><i class="bi bi-gear"></i> 系统设置</a></li>
        <li class="nav-item"><a class="nav-link" href="https://pay.wsx.tax/admin/index.php" target="_blank"><i class="bi bi-credit-card"></i> 支付中心后台</a></li>
        <li class="nav-item"><a class="nav-link" href="https://b.wsx.tax/admin/index.php" target="_blank"><i class="bi bi-journal-bookmark"></i> 账本记账Lite后台</a></li>
        <li class="nav-item"><a class="nav-link" href="https://user.wsx.tax/api/" target="_blank"><i class="bi bi-journal-bookmark"></i> API文档</a></li>
        <li class="nav-item"><a class="nav-link" href="https://saas.wsx.tax/platform_tenant_manage.php" target="_blank"><i class="bi bi-building"></i> 代理记账CRM后台</a></li>
      </ul>
      <span class="navbar-text me-3">
        <i class="bi bi-person-circle"></i> <?= $admin_name ?>
      </span>
      <a href="logout.php" class="btn btn-outline-danger btn-sm ms-2">退出登录</a>
    </div>
  </div>
</nav>
<div class="container">