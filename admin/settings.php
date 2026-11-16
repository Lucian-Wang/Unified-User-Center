<?php
require_once '../include/db.php';
require_once '../include/admin_auth.php'; // 改为后台权限认证
if (session_status() == PHP_SESSION_NONE) session_start();
if (empty($_SESSION['admin_user_id']) || !is_admin_user($pdo, $_SESSION['admin_user_id'])) header('Location: login.php');
$stmt = $pdo->prepare("SELECT * FROM settings WHERE key_name='use_sms_code'");
$stmt->execute();
$row = $stmt->fetch();
$use_sms_code = $row ? $row['value'] : '1';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $use_sms_code = $_POST['use_sms_code'] == '1' ? '1' : '0';
    $stmt = $pdo->prepare("INSERT INTO settings (key_name, value) VALUES ('use_sms_code', ?) ON DUPLICATE KEY UPDATE value=?");
    $stmt->execute([$use_sms_code, $use_sms_code]);
    $msg = "设置已保存";
}
include 'header.php';
?>
<div class="card main-card" style="max-width:500px;margin:auto;">
  <div class="card-header"><i class="bi bi-gear"></i> 系统设置</div>
  <div class="card-body">
    <?php if (!empty($msg)): ?><div class="alert alert-success"><?=$msg?></div><?php endif;?>
    <form method="post">
      <div class="mb-3">
        <label class="form-label fw-bold">注册/登录/找回密码启用短信验证码</label>
        <select name="use_sms_code" class="form-select">
          <option value="1" <?=$use_sms_code=='1'?'selected':''?>>启用</option>
          <option value="0" <?=$use_sms_code=='0'?'selected':''?>>关闭</option>
        </select>
      </div>
      <button class="btn btn-main px-4">保存设置</button>
    </form>
  </div>
</div>
<?php include 'footer.php'; ?>