<?php
require_once __DIR__ . '/../include/db.php';
session_start();
header('Content-Type: application/json');

if (empty($_SESSION['user_id'])) {
    http_response_code(401);
    exit(json_encode(['code'=>401, 'msg'=>'未登录']));
}

$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT * FROM logs WHERE user_id=? ORDER BY id DESC LIMIT 100");
$stmt->execute([$user_id]);
$logs = $stmt->fetchAll();

echo json_encode(['code'=>0, 'list'=>$logs]);