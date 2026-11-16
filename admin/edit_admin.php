<?php
require_once '../include/db.php';
require_once '../include/admin_auth.php';
if (session_status() == PHP_SESSION_NONE) session_start();
require_admin_login($pdo);

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$stmt = $pdo->prepare("SELECT * FROM admin_users WHERE id=?");
$stmt->execute([$id]);
$admin = $stmt->fetch();
if (!$admin) {
    header('Location: admins.php'); exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $status = intval($_POST['status']);
    $password = $_POST['password'];
    if (!$name) {
        $error = "姓名不能为空";
    } elseif ($password && strlen($password) < 6) {
        $error = "新密码至少6位";
    } else {
        if ($password) {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE admin_users SET name=?,email=?,status=?,password=? WHERE id=?");
            $stmt->execute([$name, $email, $status, $hash, $id]);
        } else {
            $stmt = $pdo->prepare("UPDATE admin_users SET name=?,email=?,status=? WHERE id=?");
            $stmt->execute([$name, $email, $status, $id]);
        }
        header('Location: admins.php');
        exit;
    }
}
include 'header.php';
?>
<div class="card p-4 shadow" style="max-width:450px;margin:auto;">
    <h3 class="mb-4 text-center">编辑管理员</h3>
    <?php if ($error): ?><div class="alert alert-danger"><?=$error?></div><?php endif;?>
    <form method="post">
      <div class="mb-3">
        <label>账号</label>
        <input type="text" class="form-control" value="<?=htmlspecialchars($admin['username'])?>" disabled>
      </div>
      <div class="mb-3">
        <label>姓名</label>
        <input type="text" name="name" class="form-control" value="<?=htmlspecialchars($admin['name'])?>" required>
      </div>
      <div class="mb-3">
        <label>邮箱</label>
        <input type="email" name="email" class="form-control" value="<?=htmlspecialchars($admin['email'])?>">
      </div>
      <div class="mb-3">
        <label>新密码（不修改请留空）</label>
        <input type="password" name="password" class="form-control">
      </div>
      <div class="mb-3">
        <label>状态</label>
        <select name="status" class="form-select">
          <option value="1"<?=$admin['status']==1?' selected':''?>>启用</option>
          <option value="0"<?=$admin['status']==0?' selected':''?>>禁用</option>
        </select>
      </div>
      <button class="btn btn-main w-100" type="submit">保存修改</button>
    </form>
    <div class="text-center mt-2"><a href="admins.php">返回管理员列表</a></div>
</div>
<?php include 'footer.php'; ?>