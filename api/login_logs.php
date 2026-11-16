<?php
require_once __DIR__ . '/../include/db.php';
session_start();
header('Content-Type: application/json');

// 检查是否登录
if (empty($_SESSION['user_id'])) {
    http_response_code(401);
    exit(json_encode(['code'=>401, 'msg'=>'未登录']));
}
$user_id = $_SESSION['user_id'];

// 只查当前用户的登录日志
$stmt = $pdo->prepare("SELECT id, type, message, ip, created_at FROM logs WHERE user_id=? AND type='login' ORDER BY created_at DESC LIMIT 20");
$stmt->execute([$user_id]);
$list = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode(['code'=>0, 'list'=>$list]);