<?php
require_once '../include/db.php';
require_once '../include/admin_auth.php'; // 改为后台认证
if (session_status() == PHP_SESSION_NONE) session_start();
if (empty($_SESSION['admin_user_id']) || !is_admin_user($pdo, $_SESSION['admin_user_id'])) header('Location: login.php');
$id = intval($_GET['id']);
$error = '';
$stmt = $pdo->prepare("SELECT * FROM users WHERE id=?");
$stmt->execute([$id]);
$user = $stmt->fetch();
if (!$user) die('用户不存在');
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $status = intval($_POST['status']);
    if (!$name) {
        $error = '姓名不能为空';
    } else {
        $stmt = $pdo->prepare("UPDATE users SET name=?, email=?, status=? WHERE id=?");
        $stmt->execute([$name, $email, $status, $id]);
        header('Location: users.php');
        exit;
    }
}
include 'header.php';
?>
<div class="card p-4 shadow" style="max-width:400px;margin:auto;">
    <h3 class="mb-4 text-center">编辑用户</h3>
    <?php if ($error): ?><div class="alert alert-danger"><?=$error?></div><?php endif;?>
    <form method="post">
      <div class="mb-3">
        <input type="text" name="name" value="<?=htmlspecialchars($user['name'])?>" class="form-control" placeholder="姓名" required>
      </div>
      <div class="mb-3">
        <input type="email" name="email" value="<?=htmlspecialchars($user['email'])?>" class="form-control" placeholder="邮箱">
      </div>
      <div class="mb-3">
        <select name="status" class="form-select">
          <option value="1" <?=$user['status']?'selected':''?>>启用</option>
          <option value="0" <?=$user['status']?'':'selected'?>>禁用</option>
        </select>
      </div>
      <button class="btn btn-primary w-100" type="submit">保存</button>
    </form>
    <div class="text-center mt-2"><a href="users.php">返回用户列表</a></div>
</div>
<?php include 'footer.php'; ?>