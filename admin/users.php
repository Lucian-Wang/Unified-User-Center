<?php
require_once '../include/db.php';
require_once '../include/admin_auth.php'; // 使用后台专用权限认证
if (session_status() == PHP_SESSION_NONE) session_start();
if (empty($_SESSION['admin_user_id']) || !is_admin_user($pdo, $_SESSION['admin_user_id'])) {
    header('Location: login.php'); exit;
}
$page = max(1, intval($_GET['page'] ?? 1));
$pageSize = 20;
$offset = ($page - 1) * $pageSize;

// 查询用户及角色
$stmt = $pdo->query("SELECT COUNT(*) FROM users");
$total = $stmt->fetchColumn();
$stmt = $pdo->prepare("SELECT u.*, GROUP_CONCAT(r.key_name) as roles FROM users u 
LEFT JOIN user_roles ur ON u.id=ur.user_id 
LEFT JOIN roles r ON ur.role_id=r.id 
GROUP BY u.id
ORDER BY u.id DESC LIMIT ?,?");
$stmt->bindValue(1, $offset, PDO::PARAM_INT);
$stmt->bindValue(2, $pageSize, PDO::PARAM_INT);
$stmt->execute();
$users = $stmt->fetchAll();

include 'header.php';
?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0 fw-bold"><i class="bi bi-people"></i> 用户列表</h4>
    <a href="add_user.php" class="btn btn-main btn-lg shadow-sm"><i class="bi bi-plus-lg"></i> 添加用户</a>
</div>
<div class="card main-card">
    <div class="card-header"><i class="bi bi-table"></i> 用户列表</div>
    <div class="card-body p-0">
      <table class="table table-hover align-middle mb-0">
        <thead class="table-light">
          <tr>
            <th>ID</th><th>手机号</th><th>姓名</th><th>邮箱</th><th>状态</th><th>用户类型</th><th class="text-center">操作</th>
          </tr>
        </thead>
        <tbody>
        <?php foreach($users as $u): ?>
          <tr>
            <td><?=$u['id']?></td>
            <td><?=htmlspecialchars($u['mobile'])?></td>
            <td><?=htmlspecialchars($u['name'])?></td>
            <td><?=htmlspecialchars($u['email'])?></td>
            <td>
                <?php if($u['status']): ?>
                  <span class="badge bg-success">启用</span>
                <?php else: ?>
                  <span class="badge bg-secondary">禁用</span>
                <?php endif;?>
            </td>
            <td>
              <?php
                if (strpos($u['roles'], 'admin') !== false)
                    echo '<span class="user-type-badge admin">超级管理员</span>';
                elseif (strpos($u['roles'], 'manager') !== false)
                    echo '<span class="user-type-badge manager">管理员</span>';
                else
                    echo '<span class="user-type-badge user">普通用户</span>';
              ?>
            </td>
            <td class="text-center">
              <a href="edit_user.php?id=<?=$u['id']?>" class="btn btn-sm btn-outline-secondary btn-action me-1"><i class="bi bi-pencil"></i> 编辑</a>
              <a href="reset_pwd.php?id=<?=$u['id']?>" class="btn btn-sm btn-warning btn-action me-1"><i class="bi bi-key-fill"></i> 重置密码</a>
              <a href="toggle_status.php?id=<?=$u['id']?>" class="btn btn-sm <?=$u['status']?'btn-danger':'btn-success'?> btn-action me-1">
                  <i class="bi <?=$u['status']?'bi-slash-circle':'bi-check-circle'?>"></i> <?=$u['status']?'禁用':'启用'?>
              </a>
              <!-- <a href="assign_role.php?id=<?=$u['id']?>" class="btn btn-sm btn-info btn-action"><i class="bi bi-person-badge"></i> 分配角色</a> -->
            </td>
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