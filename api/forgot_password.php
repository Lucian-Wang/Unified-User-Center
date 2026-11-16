<?php
require_once __DIR__ . '/../include/db.php';
require_once __DIR__ . '/ip_whitelist.php';
session_start();
header('Content-Type: application/json');

// 1. 获取参数
$data = json_decode(file_get_contents('php://input'), true);
$mobile = trim($data['mobile'] ?? '');
$sms_code = $data['sms_code'] ?? '';
$new_password = $data['new_password'] ?? '';

// 2. 参数校验
if (!preg_match('/^1\d{10}$/', $mobile)) {
    http_response_code(400);
    exit(json_encode(['code'=>400, 'msg'=>'手机号格式不正确']));
}
if (strlen($new_password) < 6) {
    http_response_code(400);
    exit(json_encode(['code'=>400, 'msg'=>'新密码太短']));
}

// 3. 校验验证码
if (
    $sms_code !== ($_SESSION['sms_verify_code'] ?? '') ||
    $mobile !== ($_SESSION['sms_verify_phone'] ?? '') ||
    !isset($_SESSION['sms_verify_time']) ||
    time() - $_SESSION['sms_verify_time'] > 300
) {
    http_response_code(400);
    exit(json_encode(['code'=>400, 'msg'=>'短信验证码错误或已过期']));
}
// 验证成功后，清理验证码
unset($_SESSION['sms_verify_code'], $_SESSION['sms_verify_phone'], $_SESSION['sms_verify_time']);

// 4. 查找用户
$stmt = $pdo->prepare("SELECT id FROM users WHERE mobile=? LIMIT 1");
$stmt->execute([$mobile]);
$user = $stmt->fetch();
if (!$user) {
    http_response_code(404);
    exit(json_encode(['code'=>404, 'msg'=>'未找到该用户']));
}

// 5. 更新密码
$hash = password_hash($new_password, PASSWORD_DEFAULT);
$stmt = $pdo->prepare("UPDATE users SET password=? WHERE id=?");
$stmt->execute([$hash, $user['id']]);

echo json_encode(['code'=>0, 'msg'=>'密码重置成功']);