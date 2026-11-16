<?php
require_once '../include/db.php';
require_once '../include/admin_auth.php';
if (session_status() == PHP_SESSION_NONE) session_start();
require_admin_login($pdo);

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $status = intval($_POST['status']);
    if (!$username || strlen($username) < 4) {
        $error = "管理员账号不能为空且至少4位";
    } elseif (!$name) {
        $error = "姓名不能为空";
    } elseif (strlen($password) < 6) {
        $error = "密码至少6位";
    } else {
        $stmt = $pdo->prepare("SELECT id FROM admin_users WHERE username=?");
        $stmt->execute([$username]);
        if ($stmt->fetch()) {
            $error = "管理员账号已存在";
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO admin_users (username, password, name, email, status, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
            $stmt->execute([$username, $hash, $name, $email, $status]);
            header('Location: admins.php');
            exit;
        }
    }
}
include 'header.php';
?>
<div class="card p-4 shadow" style="max-width:450px;margin:auto;">
    <h3 class="mb-4 text-center">添加管理员</h3>
    <?php if ($error): ?><div class="alert alert-danger"><?=$error?></div><?php endif;?>
    <form method="post">
      <div class="mb-3">
        <label>管理员账号</label>
        <input type="text" name="username" class="form-control" required>
      </div>
      <div class="mb-3">
        <label>姓名</label>
        <input type="text" name="name" class="form-control" required>
      </div>
      <div class="mb-3">
        <label>邮箱</label>
        <input type="email" name="email" class="form-control">
      </div>
      <div class="mb-3">
        <label>密码</label>
        <input type="password" name="password" class="form-control" required>
      </div>
      <div class="mb-3">
        <label>状态</label>
        <select name="status" class="form-select">
          <option value="1">启用</option>
          <option value="0">禁用</option>
        </select>
      </div>
      <button class="btn btn-success w-100" type="submit">添加管理员</button>
    </form>
    <div class="text-center mt-2"><a href="admins.php">返回管理员列表</a></div>
</div>
<?php include 'footer.php'; ?>