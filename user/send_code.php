<?php
require_once '../include/db.php';
require_once '../include/sms.php';

session_start();
header('Content-Type: application/json; charset=utf-8');

// ==== 配置 ====
$interval_seconds = 60; // 同手机号最小间隔
$ip_limit_hour = 20;    // 每个IP每小时最多几次

// 支持 POST/GET
$mobile = $_POST['mobile'] ?? $_GET['mobile'] ?? '';
$captcha = $_POST['captcha'] ?? $_GET['captcha'] ?? '';

// 先校验手机号
if (!preg_match('/^1\d{10}$/', $mobile)) {
    echo json_encode(['status'=>'fail','msg'=>'手机号格式不正确']); exit;
}

// 先校验图形验证码（如果是注册/找回/需要图片验证码的场景，强烈建议前端传递captcha参数）
if (!isset($_SESSION['captcha_code']) || strtolower(trim($captcha)) !== strtolower($_SESSION['captcha_code'])) {
    echo json_encode(['status'=>'fail','msg'=>'图片验证码错误']); exit;
}

// 校验通过后，图片验证码即失效，防止刷
unset($_SESSION['captcha_code']);

// 防刷1：同手机号60秒内只能发一次
if (isset($_SESSION['sms_last_send'][$mobile]) && time() - $_SESSION['sms_last_send'][$mobile] < $interval_seconds) {
    echo json_encode(['status'=> 'fail', 'msg' => '发送过于频繁，请稍后再试']); exit;
}

// 防刷2：同IP每小时最多20次
$ip = $_SERVER['REMOTE_ADDR'];
if (!isset($_SESSION['sms_ip_count'])) $_SESSION['sms_ip_count'] = [];
foreach ($_SESSION['sms_ip_count'] as $k => $v) {
    if ($v['time'] < time() - 3600) unset($_SESSION['sms_ip_count'][$k]);
}
if (!isset($_SESSION['sms_ip_count'][$ip])) {
    $_SESSION['sms_ip_count'][$ip] = ['count'=>0, 'time'=>time()];
}
if ($_SESSION['sms_ip_count'][$ip]['count'] >= $ip_limit_hour) {
    echo json_encode(['status'=>'fail','msg'=>'发送已达上限，请稍后再试']); exit;
}
$_SESSION['sms_ip_count'][$ip]['count']++;

// 5分钟内重复请求同手机号，返回原验证码
if (
    isset($_SESSION['sms_verify_phone']) && $_SESSION['sms_verify_phone']===$mobile
    && isset($_SESSION['sms_verify_time']) && time()-$_SESSION['sms_verify_time'] < 300
    && isset($_SESSION['sms_verify_code'])
) {
    $code = $_SESSION['sms_verify_code'];
} else {
    $code = rand(100000,999999);
    $_SESSION['sms_verify_code'] = $code;
    $_SESSION['sms_verify_phone'] = $mobile;
    $_SESSION['sms_verify_time'] = time();
}
if (!isset($_SESSION['sms_last_send'])) $_SESSION['sms_last_send'] = [];
$_SESSION['sms_last_send'][$mobile] = time();

// 调用云片短信发送
$ok = send_sms($mobile, $code);

if($ok){
    echo json_encode(['status'=>'ok']);
}else{
    echo json_encode(['status'=>'fail','msg'=>'短信发送失败，请联系管理员']);
}
?>