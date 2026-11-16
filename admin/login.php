<?php
require_once '../include/db.php';
require_once '../include/log.php';
if (session_status() == PHP_SESSION_NONE) session_start();
$error = '';

// 获取 redirect 参数，GET 优先，POST 其次
$redirect = isset($_GET['redirect']) ? $_GET['redirect'] : (isset($_POST['redirect']) ? $_POST['redirect'] : '');

// 已经登录则自动跳转
if (!empty($_SESSION['admin_user_id'])) {
    // 已有session，取admin信息
    $stmt = $pdo->prepare("SELECT au.*, GROUP_CONCAT(ar.key_name) AS role_keys
        FROM admin_users au
        LEFT JOIN admin_user_roles aur ON au.id=aur.user_id
        LEFT JOIN admin_roles ar ON aur.role_id=ar.id
        WHERE au.id=? AND au.status=1
        GROUP BY au.id
        LIMIT 1");
    $stmt->execute([$_SESSION['admin_user_id']]);
    $admin = $stmt->fetch();
    $role_keys = explode(',', $admin['role_keys'] ?? '');

    // 判断是否有 admin 角色
    if ($admin && in_array('admin', $role_keys)) {
        require_once '../include/jwt.php';
        $payload = [
            'uid' => $admin['id'],
            'roles' => $role_keys,
            'exp' => time() + 3600 // 1小时有效
        ];
        $jwt_token = jwt_encode($payload, JWT_SECRET);

        // 优先跳转到redirect参数，并带上token
        if ($redirect && preg_match('/^https?:\\/\\//i', $redirect)) {
            $redirect_with_token = $redirect . (strpos($redirect, '?') === false ? '?' : '&') . 'token=' . urlencode($jwt_token);
            header('Location: ' . $redirect_with_token);
        } else {
            header('Location: dashboard.php');
        }
        exit;
    } else {
        // 会话存在但权限被收回
        session_destroy();
    }
}

// 登录表单提交处理
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    // 只查 admin_users 表
    $stmt = $pdo->prepare("SELECT au.*, GROUP_CONCAT(ar.key_name) AS role_keys
        FROM admin_users au
        LEFT JOIN admin_user_roles aur ON au.id=aur.user_id
        LEFT JOIN admin_roles ar ON aur.role_id=ar.id
        WHERE au.username=? AND au.status=1
        GROUP BY au.id
        LIMIT 1");
    $stmt->execute([$username]);
    $admin = $stmt->fetch();
    $role_keys = explode(',', $admin['role_keys'] ?? '');
    // 判断是否有 admin 角色
    if ($admin && in_array('admin', $role_keys) && password_verify($password, $admin['password'])) {
        $_SESSION['admin_user_id'] = $admin['id'];
        write_log($pdo, $admin['id'], 'admin_login', "管理员登录成功");

        // 生成JWT（假设你有jwt_encode函数）
        require_once '../include/jwt.php';
        $payload = [
            'uid' => $admin['id'],
            'roles' => $role_keys,
            'exp' => time() + 3600 // 1小时有效
        ];
        $jwt_token = jwt_encode($payload, JWT_SECRET);

        // 优先跳转到redirect参数，并带上token
        if ($redirect && preg_match('/^https?:\\/\\//i', $redirect)) {
            // 判断redirect里是否已带参数
            $redirect_with_token = $redirect . (strpos($redirect, '?') === false ? '?' : '&') . 'token=' . urlencode($jwt_token);
            header('Location: ' . $redirect_with_token);
        } else {
            header('Location: dashboard.php');
        }
        exit;
    } else {
        $error = '账号或密码错误，或权限不足';
        write_log($pdo, null, 'admin_login', "管理员登录失败:$username");
    }
}
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <title>管理员登录</title>
    <link href="https://lib.wsx.tax/jsdelivr/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5" style="max-width:400px">
  <div class="card p-4 shadow">
    <h3 class="mb-4 text-center">管理员登录</h3>
    <?php if ($error): ?><div class="alert alert-danger"><?=$error?></div><?php endif;?>
    <form method="post">
      <input type="hidden" name="redirect" value="<?=htmlspecialchars($redirect)?>">
      <div class="mb-3"><input type="text" name="username" class="form-control" placeholder="管理员账号" required></div>
      <div class="mb-3"><input type="password" name="password" class="form-control" placeholder="密码" required></div>
      <button class="btn btn-primary w-100" type="submit">登录</button>
    </form>
  </div>
</div>
</body>
</html>