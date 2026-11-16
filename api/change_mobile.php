<?php
require_once __DIR__ . '/../include/db.php';
session_start();
header('Content-Type: application/json');

// 1. 鉴权
if (empty($_SESSION['user_id'])) {
    http_response_code(401);
    exit(json_encode(['code'=>401, 'msg'=>'未登录']));
}

$data = json_decode(file_get_contents('php://input'), true);
$new_mobile = trim($data['new_mobile'] ?? '');
$sms_code = trim($data['sms_code'] ?? '');

// 2. 检查手机号格式
if (!preg_match('/^1\d{10}$/', $new_mobile)) {
    http_response_code(400);
    exit(json_encode(['code'=>400, 'msg'=>'手机号格式不正确']));
}

// 3. 检查验证码
if (empty($sms_code) || $sms_code !== ($_SESSION['sms_code'] ?? '')) {
    http_response_code(400);
    exit(json_encode(['code'=>400, 'msg'=>'验证码错误']));
}

// 4. 检查手机号唯一性
$stmt = $pdo->prepare("SELECT id FROM users WHERE mobile=? AND id<>?");
$stmt->execute([$new_mobile, $_SESSION['user_id']]);
if ($stmt->fetch()) {
    http_response_code(400);
    exit(json_encode(['code'=>400, 'msg'=>'该手机号已被其他账号占用']));
}

// 5. 更新手机号
$stmt = $pdo->prepare("UPDATE users SET mobile=? WHERE id=?");
$stmt->execute([$new_mobile, $_SESSION['user_id']]);
unset($_SESSION['sms_code']);

echo json_encode(['code'=>0, 'msg'=>'手机号修改成功']);