<?php
require_once '../include/db.php';
require_once __DIR__ . '/ip_whitelist.php';
header('Content-Type: application/json');

// 1. 获取token参数
$token = $_GET['token'] ?? '';
if (!$token) {
    http_response_code(401);
    die(json_encode(['code' => 401, 'msg' => '未授权']));
}

// 2. 查询用户（token+有效期+激活状态），补充 avatar, bio 字段
$stmt = $pdo->prepare("SELECT id, mobile, name, email, avatar, bio, status, created_at FROM users WHERE api_token=? AND api_token_expire > NOW() AND status=1 LIMIT 1");
$stmt->execute([$token]);
$user = $stmt->fetch();
if (!$user) {
    http_response_code(401);
    die(json_encode(['code' => 401, 'msg' => '未授权']));
}

// 3. 查询角色
$stmt2 = $pdo->prepare("SELECT r.key_name FROM user_roles ur JOIN roles r ON ur.role_id=r.id WHERE ur.user_id=?");
$stmt2->execute([$user['id']]);
$user['roles'] = $stmt2->fetchAll(PDO::FETCH_COLUMN);

// 4. 返回用户信息，含 avatar, bio
echo json_encode(['code' => 0, 'user' => $user]);