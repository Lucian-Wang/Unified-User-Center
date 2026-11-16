<?php
session_start();
header('Content-Type: application/json');

// 清理所有会话数据
session_unset();
session_destroy();

echo json_encode(['code'=>0, 'msg'=>'登出成功']);