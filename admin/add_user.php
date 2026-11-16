<?php
require_once '../include/db.php';
require_once '../include/admin_auth.php'; // 用后台专用auth
if (session_status() == PHP_SESSION_NONE) session_start();
if (empty($_SESSION['admin_user_id']) || !is_admin_user($pdo, $_SESSION['admin_user_id'])) {
    header('Location: login.php'); exit;
}
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $mobile = trim($_POST['mobile']);
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $status = intval($_POST['status']);
    if (!preg_match('/^1\d{10}$/', $mobile)) {
        $error = "手机号格式不正确";
    } elseif (!$name) {
        $error = "姓名不能为空";
    } elseif (strlen($password) < 6) {
        $error = "密码至少6位";
    } else {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE mobile=?");
        $stmt->execute([$mobile]);
        if ($stmt->fetch()) {
            $error = "手机号已存在";
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (mobile, password, name, email, status, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
            $stmt->execute([$mobile, $hash, $name, $email, $status]);
            header('Location: users.php');
            exit;
        }
    }
}
include 'header.php';
?>
<div class="card p-4 shadow" style="max-width:450px;margin:auto;">
    <h3 class="mb-4 text-center">添加用户</h3>
    <?php if ($error): ?><div class="alert alert-danger"><?=$error?></div><?php endif;?>
    <form method="post">
      <div class="mb-3">
        <label>手机号</label>
        <input type="text" name="mobile" class="form-control" required>
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
      <button class="btn btn-success w-100" type="submit">添加用户</button>
    </form>
    <div class="text-center mt-2"><a href="users.php">返回用户列表</a></div>
</div>
<?php include 'footer.php'; ?>