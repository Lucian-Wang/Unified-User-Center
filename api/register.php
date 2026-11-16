<?php
require_once '../include/db.php';
require_once '../include/log.php';
require_once __DIR__ . '/ip_whitelist.php';
header('Content-Type: application/json');
$data = json_decode(file_get_contents('php://input'), true);
$mobile = trim($data['mobile'] ?? '');
$password = $data['password'] ?? '';
$name = trim($data['name'] ?? '');
$sms_code = $data['sms_code'] ?? '';
session_start();
if ($sms_code != ($_SESSION['sms_verify_code'] ?? '') || $mobile != ($_SESSION['sms_verify_phone'] ?? '')) {
    http_response_code(400); die(json_encode(['code'=>400,'msg'=>'短信验证码错误']));
}
unset($_SESSION['sms_verify_code'], $_SESSION['sms_verify_phone']);
if (!preg_match('/^1\d{10}$/', $mobile)) {
    http_response_code(400); die(json_encode(['code'=>400, 'msg'=>'手机号不合法']));
}
if (strlen($password) < 6) {
    http_response_code(400); die(json_encode(['code'=>400, 'msg'=>'密码太短']));
}
$stmt = $pdo->prepare("SELECT id FROM users WHERE mobile=?");
$stmt->execute([$mobile]);
if ($stmt->fetch()) {
    http_response_code(409); die(json_encode(['code'=>409, 'msg'=>'手机号已注册']));
}
$hash = password_hash($password, PASSWORD_DEFAULT);
$stmt = $pdo->prepare("INSERT INTO users (mobile, password, name) VALUES (?, ?, ?)");
$stmt->execute([$mobile, $hash, $name]);
write_log($pdo, null, 'api_register', $mobile.'注册');
echo json_encode(['code'=>0, 'msg'=>'注册成功']);