<?php
require_once __DIR__ . '/../include/sms.php'; // send_sms函数
require_once __DIR__ . '/ip_whitelist.php';
session_start();
header('Content-Type: application/json');

// ==== 参数校验 ====
$phone = $_POST['phone'] ?? '';
if (!preg_match('/^1\d{10}$/', $phone)) {
    exit(json_encode(['ok'=>0, 'msg'=>'手机号格式不正确']));
}

// ==== 防刷1：同手机号60秒内只能发一次 ====
$interval_seconds = 60;
if (!isset($_SESSION['sms_last_send'])) $_SESSION['sms_last_send'] = [];
if (isset($_SESSION['sms_last_send'][$phone]) && time() - $_SESSION['sms_last_send'][$phone] < $interval_seconds) {
    exit(json_encode(['ok'=>0, 'msg'=>'发送过于频繁，请稍后再试']));
}

// ==== 防刷2：同IP每小时最多20次 ====
$ip_limit_hour = 20;
$ip = $_SERVER['REMOTE_ADDR'];
if (!isset($_SESSION['sms_ip_count'])) $_SESSION['sms_ip_count'] = [];
foreach ($_SESSION['sms_ip_count'] as $k => $v) {
    if ($v['time'] < time() - 3600) unset($_SESSION['sms_ip_count'][$k]);
}
if (!isset($_SESSION['sms_ip_count'][$ip])) {
    $_SESSION['sms_ip_count'][$ip] = ['count'=>0, 'time'=>time()];
}
if ($_SESSION['sms_ip_count'][$ip]['count'] >= $ip_limit_hour) {
    exit(json_encode(['ok'=>0, 'msg'=>'发送已达上限，请稍后再试']));
}
$_SESSION['sms_ip_count'][$ip]['count']++;

// ==== 5分钟内重复请求同手机号，返回原验证码 ====
if (isset($_SESSION['sms_verify_phone']) && $_SESSION['sms_verify_phone']===$phone
    && isset($_SESSION['sms_verify_time']) && time()-$_SESSION['sms_verify_time'] < 300
    && isset($_SESSION['sms_verify_code'])
) {
    $code = $_SESSION['sms_verify_code'];
} else {
    $code = rand(100000, 999999);
    $_SESSION['sms_verify_code'] = $code;
    $_SESSION['sms_verify_phone'] = $phone;
    $_SESSION['sms_verify_time'] = time();
}
$_SESSION['sms_last_send'][$phone] = time();

// ==== 调用云片API ====
$send_ok = send_sms($phone, $code);

if ($send_ok) {
    exit(json_encode(['ok'=>1, 'msg'=>'短信发送成功']));
} else {
    exit(json_encode(['ok'=>0, 'msg'=>'短信发送失败，请联系管理员']));
}