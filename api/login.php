<?php
require_once '../include/db.php';
require_once '../include/jwt.php';
require_once __DIR__ . '/ip_whitelist.php';
header('Content-Type: application/json');
$data = json_decode(file_get_contents('php://input'), true);
$mobile = trim($data['mobile'] ?? '');
$password = $data['password'] ?? '';
$stmt = $pdo->prepare("SELECT * FROM users WHERE mobile=? AND status=1");
$stmt->execute([$mobile]);
$user = $stmt->fetch();
if ($user && password_verify($password, $user['password'])) {
    $token = jwt_encode(['uid' => $user['id']], JWT_SECRET, 86400);
    echo json_encode(['code'=>0,'token'=>$token,'user'=>['id'=>$user['id'],'mobile'=>$user['mobile'],'name'=>$user['name']]]);
} else {
    http_response_code(401); echo json_encode(['code'=>401,'msg'=>'手机号或密码错误']);
}