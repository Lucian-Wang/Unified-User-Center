<?php
session_start();
require_once '../include/db.php';
if (empty($_SESSION['user_id'])) {
    header('Location: login.php'); exit;
}
// 分页参数
$page = max(1, intval($_GET['page'] ?? 1));
$page_size = 20;
$offset = ($page - 1) * $page_size;

// 总条数
$stmt = $pdo->prepare("SELECT COUNT(*) FROM logs WHERE user_id = ? AND (type = 'login' OR type = 'reset_pwd')");
$stmt->execute([$_SESSION['user_id']]);
$total = $stmt->fetchColumn();
$total_pages = max(1, ceil($total / $page_size));

// 当前页数据
$stmt = $pdo->prepare("SELECT * FROM logs WHERE user_id = ? AND (type = 'login' OR type = 'reset_pwd') ORDER BY created_at DESC LIMIT $page_size OFFSET $offset");
$stmt->execute([$_SESSION['user_id']]);
$logs = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <title>灯火通明-登录历史/安全日志</title>
    <meta name="viewport" content="width=device-width">
     <style>
        body {
            background: #f6f8fa;
            color: #24292f;
            font-family: -apple-system,BlinkMacSystemFont,"Segoe UI",Helvetica,Arial,sans-serif,"Apple Color Emoji","Segoe UI Emoji";
            font-size: 15px;
            margin: 0;
        }
        .header-logo {
            display: flex;
            justify-content: center;
            margin-top: 44px;
            margin-bottom: 18px;
        }
        .header-logo svg {
            width: 48px;
            height: 48px;
            fill: #24292f;
        }
        .gh-main-wrap {
            display: flex;
            max-width: 1050px;
            min-height: 600px;
            margin: 40px auto 0 auto;
        }
        .gh-sidebar {
            width: 270px;
            flex-shrink: 0;
            background: none;
            border-right: 1px solid #d8dee4;
            padding-right: 24px;
            padding-top: 10px;
        }
        .gh-sidebar h2 {
            font-size: 16px;
            font-weight: 600;
            color: #1f2328;
            margin: 0 0 20px 0;
        }
        .gh-sidebar ul {
            list-style: none;
            margin: 0; padding: 0;
        }
        .gh-sidebar li {
            margin-bottom: 2px;
        }
        .gh-sidebar .gh-nav-link {
            display: flex;
            align-items: center;
            padding: 7px 14px;
            border-radius: 6px;
            color: #24292f;
            text-decoration: none;
            font-weight: 500;
            font-size: 15px;
            transition: background .14s,color .14s;
            margin-bottom: 2px;
        }
        .gh-sidebar .gh-nav-link.active,
        .gh-sidebar .gh-nav-link:hover {
            background: #eaeef2;
            color: #1f2328;
        }
        .gh-main {
            flex: 1 1 auto;
            min-width: 0;
            padding-left: 48px;
            padding-right: 18px;
            padding-top: 4px;
        }
        .gh-profile-header {
            display: flex;
            align-items: center;
            gap: 18px;
            margin-bottom: 24px;
        }
        .gh-profile-picture-box {
            position: relative;
            width: 72px;
            height: 72px;
            margin-right: 0;
        }
        .gh-user-avatar {
            width: 72px;
            height: 72px;
            border-radius: 50%;
            border: 1.5px solid #d0d7de;
            background: #fff;
            object-fit: cover;
            box-shadow: 0 4px 16px 0 rgba(149,157,165,0.10);
            display: block;
        }
        .gh-profile-avatar-edit-link {
            display: flex;
            align-items: center;
            justify-content: center;
            position: absolute;
            right: -8px;
            bottom: -8px;
            background: transparent;
            border: none;
            text-decoration: none;
            cursor: pointer;
            opacity: 0;
            pointer-events: none;
            transition: opacity .14s;
        }
        .gh-profile-picture-box:hover .gh-profile-avatar-edit-link,
        .gh-profile-avatar-edit-link:focus {
            opacity: 1;
            pointer-events: auto;
        }
        .gh-profile-avatar-edit-link svg {
            display: block;
            background: #218bff;
            border-radius: 50%;
            box-shadow: 0 2px 12px 0 rgba(33,139,255,.07);
        }
        .gh-profile-avatar-edit-text {
            display: none;
        }
        @media (pointer: coarse) {
            .gh-profile-avatar-edit-link {
                opacity: 1 !important;
                pointer-events: auto;
            }
        }
        .gh-user-name {
            font-size: 20px;
            font-weight: 700;
            color: #24292f;
        }
        .gh-user-id {
            color: #656d76;
            font-weight: 400;
            font-size: 15px;
        }
        .gh-user-login {
            font-size: 15px;
            color: #656d76;
            margin-top: 2px;
        }
        .gh-section-title {
            font-size: 22px;
            font-weight: 600;
            margin: 18px 0 18px 0;
            color: #1f2328;
        }
        .gh-profile-card {
            background: #fff;
            border: 1px solid #d0d7de;
            border-radius: 8px;
            box-shadow: 0 4px 24px 0 rgba(149,157,165,0.10);
            padding: 28px 36px 24px 36px;
            margin-bottom: 40px;
            max-width: 600px;
        }
        .gh-form-row {
            margin-bottom: 22px;
            display: flex;
            align-items: center;
        }
        .gh-form-label {
            width: 84px;
            color: #656d76;
            font-weight: 600;
            flex-shrink: 0;
        }
        .gh-form-input {
            flex: 1 1 auto;
            padding: 7px 11px;
            font-size: 15px;
            border-radius: 5px;
            border: 1px solid #d0d7de;
            background: #f6f8fa;
            color: #24292f;
            outline: none;
            transition: border .14s;
        }
        .gh-form-input:focus, .gh-form-input:active {
            border-color: #218bff;
        }
        .gh-alert {
            padding: 10px 14px;
            margin-bottom: 18px;
            border-radius: 7px;
            font-size: 15px;
        }
        .gh-alert-danger {
            background: #fae1e2;
            color: #cf222e;
            border: 1px solid #cf222e;
        }
        .gh-alert-success {
            background: #e6f7ea;
            color: #1a7f37;
            border: 1px solid #1a7f37;
        }
        .gh-profile-btn-row {
            display: flex;
            gap: 12px;
            margin-top: 16px;
        }
        .gh-btn {
            border-radius: 6px;
            padding: 8px 18px;
            font-size: 15px;
            font-weight: 600;
            border: 1px solid #d0d7de;
            background: #f6f8fa;
            color: #24292f;
            transition: background .12s, color .12s, border .12s;
            cursor: pointer;
            text-decoration: none;
        }
        .gh-btn:hover, .gh-btn:focus {
            background: #eaeef2;
            color: #0969da;
            border-color: #218bff;
        }
        .gh-btn-main {
            border: 1px solid #218bff;
            background: #218bff;
            color: #fff;
        }
        .gh-btn-main:hover, .gh-btn-main:focus {
            background: #0969da;
            color: #fff;
            border-color: #218bff;
        }
        @media (max-width: 900px) {
            .gh-main-wrap { flex-direction: column; max-width: 99vw; }
            .gh-sidebar { width: 100%; border-right: none; border-bottom: 1px solid #d8dee4; padding-bottom: 16px; }
            .gh-main { padding: 0 2vw; }
            .gh-profile-card { padding: 16px 4vw; }
            .gh-profile-header { flex-direction: column; align-items: flex-start; gap: 10px;}
        }
        table {
            width: 100%;
            border-collapse: collapse;
            background: #fff;
            font-size: 15px;
        }
        th, td {
            padding: 10px 8px;
            border-bottom: 1px solid #eaecef;
            text-align: left;
        }
        th {
            background: #f6f8fa;
            color: #656d76;
            font-weight: 600;
        }
        tr:last-child td {
            border-bottom: none;
        }
        a,
        a:visited,
        a:active,
        a:focus {
            color: #0969da !important;
            text-decoration: none;
            transition: color .15s;
        }
        a:hover {
            color: #218bff !important;
            text-decoration: underline;
        }
        .footer-links {
            color: #656d76;
            font-size: 13px;
            text-align: center;
            width: 100%;
            margin: 40px 0 20px 0;
        }
        .footer-links a, .footer-links button {
            color: #0969da !important;
            text-decoration: none;
            margin: 0 6px;
            background: none;
            border: none;
            cursor: pointer;
            font-size: 13px;
            padding: 0;
            font-family: inherit;
            transition: color .15s;
        }
        .footer-links a:hover, .footer-links button:hover {
            text-decoration: underline;
            color: #218bff !important;
        }
        .gh-pagination {
            display: flex;
            justify-content: flex-end;
            align-items: center;
            gap: 6px;
            margin-top: 18px;
            font-size: 15px;
            color: #656d76;
            user-select: none;
        }
        .gh-pagination a, .gh-pagination span {
            display: inline-block;
            min-width: 28px;
            padding: 4px 9px;
            border-radius: 5px;
            border: 1px solid #d0d7de;
            background: #fff;
            color: #24292f;
            text-align: center;
            text-decoration: none;
            margin: 0 1px;
            transition: background .12s, color .12s;
        }
        .gh-pagination .current, .gh-pagination a:hover {
            background: #218bff;
            color: #fff !important;
            border-color: #218bff;
        }
        .gh-pagination .disabled {
            background: #f6f8fa;
            color: #bbb !important;
            border-color: #eee;
            pointer-events: none;
    </style>
</head>
<body>
<div class="header-logo">
<img src="https://user.wsx.tax/logo.svg" alt="LOGO" width="60" height="60" style="display:block;">
</div>
<div class="gh-main-wrap">
<?php include 'sidebar_nav.php'; ?>
    <main class="gh-main">
        <div class="gh-section-title">登录历史 / 安全日志</div>
        <div class="gh-profile-card">
            <table>
                <thead>
                    <tr>
                        <th style="width:170px;">时间</th>
                        <th style="width:80px;">类型</th>
                        <th>内容</th>
                        <th style="width:138px;">IP</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!$logs): ?>
                    <tr><td colspan="4" style="text-align:center;color:#999;">暂无日志</td></tr>
                    <?php else: foreach($logs as $log): ?>
                    <tr>
                        <td><?=htmlspecialchars($log['created_at'])?></td>
                        <td><?=($log['type'] === 'login' ? '登录' : ($log['type'] === 'reset_pwd' ? '修改密码' : htmlspecialchars($log['type'])))?></td>
                        <td><?=htmlspecialchars($log['message'])?></td>
                        <td><?=htmlspecialchars($log['ip'])?></td>
                    </tr>
                    <?php endforeach; endif; ?>
                </tbody>
            </table>            <?php if ($total_pages > 1): ?>
            <div class="gh-pagination">
                <?php if ($page > 1): ?>
                    <a href="?page=1">&laquo; 首页</a>
                    <a href="?page=<?=($page-1)?>">&lt; 上一页</a>
                <?php else: ?>
                    <span class="disabled">&laquo; 首页</span>
                    <span class="disabled">&lt; 上一页</span>
                <?php endif; ?>
                <?php
                    $page_window = 2;
                    $start = max(1, $page - $page_window);
                    $end = min($total_pages, $page + $page_window);
                    for($i = $start; $i <= $end; $i++):
                ?>
                    <?php if($i == $page): ?>
                        <span class="current"><?=$i?></span>
                    <?php else: ?>
                        <a href="?page=<?=$i?>"><?=$i?></a>
                    <?php endif; ?>
                <?php endfor; ?>
                <?php if ($page < $total_pages): ?>
                    <a href="?page=<?=($page+1)?>">下一页 &gt;</a>
                    <a href="?page=<?=$total_pages?>">末页 &raquo;</a>
                <?php else: ?>
                    <span class="disabled">下一页 &gt;</span>
                    <span class="disabled">末页 &raquo;</span>
                <?php endif; ?>
                <span style="margin-left:8px;">共<?=$total?>条</span>
            </div>
            <?php endif; ?>
        </div>
    </main>
</div>
<?php include 'foot_nav.php'; ?>
</body>
</html>