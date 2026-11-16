<?php
require_once __DIR__ . '/../include/db.php';
session_start();
header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);
$old_password = $data['old_password'] ?? '';
$new_password = $data['new_password'] ?? '';

// 检查登录
if (empty($_SESSION['user_id'])) {
    http_response_code(401);
    exit(json_encode(['code'=>401, 'msg'=>'未登录']));
}

$user_id = $_SESSION['user_id'];

// 获取用户信息
$stmt = $pdo->prepare("SELECT password FROM users WHERE id=?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

if (!$user || !password_verify($old_password, $user['password'])) {
    http_response_code(400);
    exit(json_encode(['code'=>400, 'msg'=>'原密码错误']));
}
if (strlen($new_password) < 6) {
    http_response_code(400);
    exit(json_encode(['code'=>400, 'msg'=>'新密码太短']));
}

// 更新密码
$new_hash = password_hash($new_password, PASSWORD_DEFAULT);
$stmt = $pdo->prepare("UPDATE users SET password=? WHERE id=?");
$stmt->execute([$new_hash, $user_id]);

echo json_encode(['code'=>0, 'msg'=>'密码修改成功']);