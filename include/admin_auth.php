<?php
// include/admin_auth.php

// 判断是否为后台admin用户（拥有admin角色）
function is_admin_user($pdo, $admin_user_id) {
    if (empty($admin_user_id)) return false;
    $stmt = $pdo->prepare("SELECT COUNT(*) 
        FROM admin_user_roles aur
        JOIN admin_roles ar ON aur.role_id = ar.id
        WHERE aur.user_id = ? AND ar.key_name = 'admin'");
    $stmt->execute([$admin_user_id]);
    return $stmt->fetchColumn() > 0;
}

// 获取当前登录后台管理员信息
function get_admin_user($pdo, $admin_user_id) {
    $stmt = $pdo->prepare("SELECT au.*, GROUP_CONCAT(ar.key_name) AS role_keys
        FROM admin_users au
        LEFT JOIN admin_user_roles aur ON au.id = aur.user_id
        LEFT JOIN admin_roles ar ON aur.role_id = ar.id
        WHERE au.id = ?
        GROUP BY au.id
        LIMIT 1");
    $stmt->execute([$admin_user_id]);
    $admin = $stmt->fetch();
    if ($admin) {
        $admin['role_keys'] = explode(',', $admin['role_keys'] ?? '');
    }
    return $admin;
}

// 检查是否登录（如未登录重定向到后台登录页）
function require_admin_login($pdo) {
    if (session_status() == PHP_SESSION_NONE) session_start();
    if (empty($_SESSION['admin_user_id']) || !is_admin_user($pdo, $_SESSION['admin_user_id'])) {
        header('Location: login.php');
        exit;
    }
}
?>