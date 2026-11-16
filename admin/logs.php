<?php
require_once '../include/db.php';
require_once '../include/admin_auth.php'; // 改为后台权限认证
if (session_status() == PHP_SESSION_NONE) session_start();
if (empty($_SESSION['admin_user_id']) || !is_admin_user($pdo, $_SESSION['admin_user_id'])) header('Location: login.php');
$page = max(1, intval($_GET['page'] ?? 1));
$pageSize = 30;
$offset = ($page - 1) * $pageSize;
$stmt = $pdo->query("SELECT COUNT(*) FROM logs");
$total = $stmt->fetchColumn();
$stmt = $pdo->prepare("SELECT * FROM logs ORDER BY id DESC LIMIT ?,?");
$stmt->bindValue(1, $offset, PDO::PARAM_INT);
$stmt->bindValue(2, $pageSize, PDO::PARAM_INT);
$stmt->execute();
$logs = $stmt->fetchAll();
include 'header.php';
?>
<div class="card main-card">
  <div class="card-header"><i class="bi bi-clock-history"></i> 操作日志</div>
  <div class="card-body p-0">
    <table class="table table-hover align-middle mb-0">
      <thead class="table-light">
        <tr>
          <th>ID</th><th>用户ID</th><th>类型</th><th>内容</th><th>IP</th><th>时间</th>
        </tr>
      </thead>
      <tbody>
      <?php foreach($logs as $log): ?>
        <tr>
          <td><?=$log['id']?></td>
          <td><?=$log['user_id']?></td>
          <td>
            <span class="badge bg-<?= $log['type']=='admin_login'?'info':($log['type']=='reset_pwd'?'warning':'secondary') ?>">
              <?=htmlspecialchars($log['type'])?>
            </span>
          </td>
          <td><?=htmlspecialchars($log['message'])?></td>
          <td><?=$log['ip']?></td>
          <td><?=$log['created_at']?></td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  </div>
  <div class="card-footer bg-white">
    <?php for($i=1;$i<=ceil($total/$pageSize);$i++):?>
      <a href="?page=<?=$i?>" class="btn btn-link<?=$i==$page?' fw-bold':''?>"><?=$i?></a>
    <?php endfor;?>
  </div>
</div>
<?php include 'footer.php'; ?>