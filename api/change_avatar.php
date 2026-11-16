<?php
require_once __DIR__ . '/../include/db.php';
session_start();
header('Content-Type: application/json');

if (empty($_SESSION['user_id'])) {
    http_response_code(401);
    exit(json_encode(['code'=>401, 'msg'=>'未登录']));
}

if (!isset($_FILES['avatar']) || $_FILES['avatar']['error'] !== UPLOAD_ERR_OK) {
    http_response_code(400);
    exit(json_encode(['code'=>400, 'msg'=>'未上传头像']));
}

$ext = strtolower(pathinfo($_FILES['avatar']['name'], PATHINFO_EXTENSION));
$allow_ext = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
if (!in_array($ext, $allow_ext)) {
    http_response_code(400);
    exit(json_encode(['code'=>400, 'msg'=>'头像格式不支持']));
}
if ($_FILES['avatar']['size'] > 2 * 1024 * 1024) {
    http_response_code(400);
    exit(json_encode(['code'=>400, 'msg'=>'头像文件太大']));
}

$save_dir = '/static/avatar/';
if (!is_dir($_SERVER['DOCUMENT_ROOT'].$save_dir)) {
    mkdir($_SERVER['DOCUMENT_ROOT'].$save_dir, 0755, true);
}
$filename = 'u'.$_SESSION['user_id'].'_'.time().'.'.$ext;
$dest = $_SERVER['DOCUMENT_ROOT'].$save_dir.$filename;
if (!move_uploaded_file($_FILES['avatar']['tmp_name'], $dest)) {
    http_response_code(500);
    exit(json_encode(['code'=>500, 'msg'=>'保存头像失败']));
}
// 保存头像路径到数据库
$url = $save_dir.$filename;
$stmt = $pdo->prepare("UPDATE users SET avatar=? WHERE id=?");
$stmt->execute([$url, $_SESSION['user_id']]);
echo json_encode(['code'=>0, 'url'=>$url, 'msg'=>'头像上传成功']);