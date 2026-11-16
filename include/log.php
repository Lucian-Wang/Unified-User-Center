<?php
function write_log($pdo, $user_id, $type, $msg) {
    $stmt = $pdo->prepare("INSERT INTO logs (user_id, type, message, ip) VALUES (?,?,?,?)");
    $stmt->execute([$user_id, $type, $msg, $_SERVER['REMOTE_ADDR']]);
}