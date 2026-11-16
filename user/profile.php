<?php
session_start();
require_once '../include/db.php';
if(empty($_SESSION['user_id'])) {
    header('Location: login.php'); exit;
}
$stmt = $pdo->prepare("SELECT * FROM users WHERE id=?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();
$avatar_url = !empty($user['avatar']) ? htmlspecialchars($user['avatar']) : '/static/user.svg';
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <title>灯火通明-个人中心</title>
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
        .gh-profile-info-row {
            margin-bottom: 22px;
            display: flex;
            align-items: flex-start;
        }
        .gh-profile-info-label {
            width: 84px;
            color: #656d76;
            font-weight: 600;
            flex-shrink: 0;
        }
        .gh-profile-info-value {
            flex: 1 1 auto;
            color: #24292f;
            word-break: break-all;
        }
        .gh-profile-info-value .gh-btn {
            margin-left: 12px;
            padding: 3px 11px;
            font-size: 13px;
            border-radius: 5px;
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
        .gh-btn-danger {
            color: #cf222e;
            border-color: #cf222e;
            background: #fff;
        }
        .gh-btn-danger:hover, .gh-btn-danger:focus {
            background: #fae1e2;
            color: #fff;
            border-color: #cf222e;
        }
        @media (max-width: 900px) {
            .gh-main-wrap { flex-direction: column; max-width: 99vw; }
            .gh-sidebar { width: 100%; border-right: none; border-bottom: 1px solid #d8dee4; padding-bottom: 16px; }
            .gh-main { padding: 0 2vw; }
            .gh-profile-card { padding: 16px 4vw; }
            .gh-profile-header { flex-direction: column; align-items: flex-start; gap: 10px;}
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
    </style>
</head>
<body>
<div class="header-logo">
<img src="https://user.wsx.tax/logo.svg" alt="LOGO" width="60" height="60" style="display:block;">
</div>
<div class="gh-main-wrap">
<?php include 'sidebar_nav.php'; ?>
    <main class="gh-main">
        <div class="gh-profile-header">
            <div class="gh-profile-picture-box" title="点击更换头像">
                <img id="avatar-img" src="<?=$avatar_url?>" alt="头像" class="gh-user-avatar">
                <a href="edit_avatar.php" class="gh-profile-avatar-edit-link" tabindex="0">
                    <svg width="22" height="22" fill="none" style="vertical-align:middle;">
                        <circle cx="11" cy="11" r="11" fill="#218bff" opacity="0.92"/>
                        <path d="M11 7a2 2 0 1 1 0 4 2 2 0 0 1 0-4zm0-2a4 4 0 1 0 0 8 4 4 0 0 0 0-8zm-6 14v-1a4 4 0 0 1 4-4h4a4 4 0 0 1 4 4v1H5z" fill="#fff"/>
                    </svg>
                    <span class="gh-profile-avatar-edit-text">更换头像</span>
                </a>
            </div>
            <div>
                <div class="gh-user-name"><?=htmlspecialchars($user['name'])?> <span class="gh-user-id">(ID:<?=htmlspecialchars($user['id'])?>)</span></div>
                <div class="gh-user-login">统一用户中心</div>
            </div>
        </div>
        <div class="gh-section-title">个人资料</div>
        <div class="gh-profile-card">
            <div class="gh-profile-info-row">
                <div class="gh-profile-info-label">姓名</div>
                <div class="gh-profile-info-value"><?=htmlspecialchars($user['name'])?></div>
            </div>
            <div class="gh-profile-info-row">
                <div class="gh-profile-info-label">邮箱</div>
                <div class="gh-profile-info-value"><?=htmlspecialchars($user['email'])?></div>
            </div>
            <div class="gh-profile-info-row">
                <div class="gh-profile-info-label">手机号</div>
                <div class="gh-profile-info-value">
                    <?=htmlspecialchars($user['mobile'])?>
                    <a href="edit_mobile.php" class="gh-btn" style="margin-left:14px;padding:4px 13px;font-size:13px;">修改</a>
                </div>
            </div>
            <div class="gh-profile-info-row">
                <div class="gh-profile-info-label">简介</div>
                <div class="gh-profile-info-value"><?=htmlspecialchars($user['bio'] ?? '')?></div>
            </div>
            <div class="gh-profile-info-row">
                <div class="gh-profile-info-label">账号状态</div>
                <div class="gh-profile-info-value">
                    <?=$user['status']?'<span style="color:#1a7f37;font-weight:600;">启用</span>':'<span style="color:#8c959f;font-weight:600;">禁用</span>'?>
                </div>
            </div>
            <div class="gh-profile-info-row">
                <div class="gh-profile-info-label">注册时间</div>
                <div class="gh-profile-info-value"><?=htmlspecialchars($user['created_at'])?></div>
            </div>
            <div class="gh-profile-btn-row">
                <a href="edit_profile.php" class="gh-btn">修改资料</a>
                <a href="edit_pwd.php" class="gh-btn">修改密码</a>
                <a href="logout.php" class="gh-btn gh-btn-danger">退出登录</a>
            </div>
        </div>
    </main>
</div>
<?php include 'foot_nav.php'; ?>
</body>
</html>