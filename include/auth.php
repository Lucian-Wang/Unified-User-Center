<?php
function check_permission($pdo, $user_id, $permission_key) {
    $sql = "SELECT COUNT(*) FROM user_roles ur
            JOIN role_permissions rp ON ur.role_id=rp.role_id
            JOIN permissions p ON rp.permission_id=p.id
            WHERE ur.user_id=? AND p.key_name=?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$user_id, $permission_key]);
    return $stmt->fetchColumn() > 0;
}
function is_admin($pdo, $user_id) {
    $sql = "SELECT r.key_name FROM user_roles ur JOIN roles r ON ur.role_id=r.id WHERE ur.user_id=?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$user_id]);
    while ($row = $stmt->fetch()) {
        if ($row['key_name'] === 'admin') return true;
    }
    return false;
}