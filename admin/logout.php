<?php
if (session_status() == PHP_SESSION_NONE) session_start();
session_unset();
session_destroy();

// 清理后台 SSO 相关cookie（补充你实际用到的）
$cookies_to_clear = [
    'user_center_token',
    'user_token',
    'remember_me',
    'username'
];
foreach ($cookies_to_clear as $cookie) {
    setcookie($cookie, '', time() - 3600, "/");
    setcookie($cookie, '', time() - 3600, "/", ".wsx.tax");
}

// 支持 redirect_uri 参数，兼容 SSO 业务平台回跳
$redirect = isset($_GET['redirect_uri']) ? $_GET['redirect_uri'] : 'login.php';
header('Location: ' . $redirect);
exit;