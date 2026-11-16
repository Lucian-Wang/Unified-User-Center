<?php
require_once __DIR__ . '/../include/db.php';
session_start();
header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);
$name = trim($data['name'] ?? '');
$email = trim($data['email'] ?? '');
$bio = trim($data['bio'] ?? '');

// 检查登录
if (empty($_SESSION['user_id'])) {
    http_response_code(401);
    exit(json_encode(['code'=>401, 'msg'=>'未登录']));
}

$user_id = $_SESSION['user_id'];

// 校验
if ($name === '' && $email === '' && $bio === '') {
    http_response_code(400);
    exit(json_encode(['code'=>400, 'msg'=>'参数不能为空']));
}
if ($email && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    exit(json_encode(['code'=>400, 'msg'=>'邮箱格式不正确']));
}
if ($bio && mb_strlen($bio) > 200) {
    http_response_code(400);
    exit(json_encode(['code'=>400, 'msg'=>'简介最多200字']));
}

// 构造SQL
$fields = [];
$params = [];
if ($name !== '') {
    $fields[] = "name=?";
    $params[] = $name;
}
if ($email !== '') {
    $fields[] = "email=?";
    $params[] = $email;
}
if ($bio !== '') {
    $fields[] = "bio=?";
    $params[] = $bio;
}
$params[] = $user_id;

$sql = "UPDATE users SET " . implode(', ', $fields) . " WHERE id=?";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);

echo json_encode(['code'=>0, 'msg'=>'资料修改成功']);