<?php
session_start();
session_unset();
session_destroy();

// 清理常用用户相关cookie（补充你实际使用的所有身份cookie名）
$cookies_to_clear = [
    'user_center_token', // SSO token
    'user_token',        // 普通登录token
    'remember_me',
    'username'
];
foreach ($cookies_to_clear as $cookie) {
    // 清当前域
    setcookie($cookie, '', time() - 3600, "/");
    // 清主域
    setcookie($cookie, '', time() - 3600, "/", ".wsx.tax");
}

// 支持 redirect_uri 参数，登出后自动跳回业务系统（如books）
$redirect = isset($_GET['redirect_uri']) ? $_GET['redirect_uri'] : 'login.php';
header('Location: ' . $redirect);
exit;