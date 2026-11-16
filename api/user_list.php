<?php
require_once '../include/db.php';
require_once '../include/jwt.php';
require_once __DIR__ . '/ip_whitelist.php';
header('Content-Type: application/json');
$auth = $_SERVER['HTTP_AUTHORIZATION'] ?? '';
if (!preg_match('/Bearer\s(\S+)/', $auth, $m)) {
    http_response_code(401); die(json_encode(['code' => 401, 'msg' => '未授权']));
}
$payload = jwt_decode($m[1], JWT_SECRET);
if (!$payload) {
    http_response_code(401); die(json_encode(['code' => 401, 'msg' => 'Token无效或已过期']));
}
// 检查管理员权限
$stmt = $pdo->prepare("SELECT r.key_name FROM user_roles ur JOIN roles r ON ur.role_id=r.id WHERE ur.user_id=?");
$stmt->execute([$payload['uid']]);
$is_admin = false;
while ($row = $stmt->fetch()) {
    if ($row['key_name'] === 'admin') $is_admin = true;
}
if (!$is_admin) {
    http_response_code(403); die(json_encode(['code'=>403,'msg'=>'无权限']));
}
$stmt = $pdo->query("SELECT id, mobile, name, email, status, created_at FROM users ORDER BY id DESC LIMIT 100");
$list = $stmt->fetchAll();
echo json_encode(['code'=>0,'list'=>$list]);