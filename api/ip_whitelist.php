<?php
// 配置允许访问的IP列表
$ip_whitelist = [
    '127.0.0.1',
    '::1', // IPv6本地回环
    '47.99.88.91'
];

// 获取客户端真实IP（支持代理场景）
function get_client_ip() {
    if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ip = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR'])[0];
    } elseif (!empty($_SERVER['REMOTE_ADDR'])) {
        $ip = $_SERVER['REMOTE_ADDR'];
    } else {
        $ip = '';
    }
    return trim($ip);
}

$client_ip = get_client_ip();
if (!in_array($client_ip, $ip_whitelist)) {
    http_response_code(403);
    header('Content-Type: application/json');
    echo json_encode([
        'code' => 403,
        'msg'  => '禁止访问，IP未在白名单'
    ]);
    exit;
}