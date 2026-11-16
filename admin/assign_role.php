<?php
require_once '../include/db.php';
require_once '../include/admin_auth.php'; // 改为后台权限认证
if (session_status() == PHP_SESSION_NONE) session_start();
if (empty($_SESSION['admin_user_id']) || !is_admin_user($pdo, $_SESSION['admin_user_id'])) header('Location: login.php');
$user_id = intval($_GET['id']);
$stmt = $pdo->prepare("SELECT * FROM users WHERE id=?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();
if (!$user) die('用户不存在');
$stmt = $pdo->query("SELECT * FROM roles");
$roles = $stmt->fetchAll();
$stmt = $pdo->prepare("SELECT role_id FROM user_roles WHERE user_id=?");
$stmt->execute([$user_id]);
$current_roles = array_column($stmt->fetchAll(), 'role_id');
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $sel_roles = isset($_POST['roles']) ? $_POST['roles'] : [];
    $pdo->prepare("DELETE FROM user_roles WHERE user_id=?")->execute([$user_id]);
    foreach ($sel_roles as $rid) {
        $pdo->prepare("INSERT INTO user_roles (user_id, role_id) VALUES (?,?)")->execute([$user_id, $rid]);
    }
    header("Location: users.php");
    exit;
}
include 'header.php';
?>
<div class="card main-card" style="max-width:400px;margin:32px auto;">
  <div class="card-header text-center"><i class="bi bi-person-badge"></i> 分配角色</div>
  <div class="card-body">
    <h5 class="mb-4 text-center"><?=htmlspecialchars($user['name'])?>（ID:<?=$user_id?>）</h5>
    <form method="post">
      <div class="mb-3">
        <?php foreach($roles as $role): ?>
          <div class="form-check mb-2">
            <input class="form-check-input" type="checkbox" name="roles[]" value="<?=$role['id']?>" id="role<?=$role['id']?>" <?=in_array($role['id'],$current_roles)?'checked':''?>>
            <label class="form-check-label" for="role<?=$role['id']?>"><?=htmlspecialchars($role['name'])?>(<?=htmlspecialchars($role['key_name'])?>)</label>
          </div>
        <?php endforeach;?>
      </div>
      <button class="btn btn-main w-100" type="submit"><i class="bi bi-save"></i> 保存</button>
    </form>
    <div class="text-center mt-2"><a href="users.php" class="text-decoration-underline">返回用户列表</a></div>
  </div>
</div>
<?php include 'footer.php'; ?>