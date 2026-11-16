<?php
require_once '../include/db.php';
require_once '../include/admin_auth.php';
if (session_status() == PHP_SESSION_NONE) session_start();
require_admin_login($pdo);

// 处理搜索
$search = isset($_GET['q']) ? trim($_GET['q']) : '';
$params = [];
$sql = "SELECT au.*, GROUP_CONCAT(ar.name) AS roles
        FROM admin_users au
        LEFT JOIN admin_user_roles aur ON au.id = aur.user_id
        LEFT JOIN admin_roles ar ON aur.role_id = ar.id";
$where = [];
if ($search !== '') {
    $where[] = "(au.username LIKE :q OR au.name LIKE :q OR au.email LIKE :q)";
    $params[':q'] = '%' . $search . '%';
}
if ($where) {
    $sql .= " WHERE " . implode(' AND ', $where);
}
$sql .= " GROUP BY au.id ORDER BY au.id DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$admins = $stmt->fetchAll();

include 'header.php';
?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0 fw-bold"><i class="bi bi-shield-lock"></i> 管理员列表</h4>
    <div>
        <form class="d-inline-block me-2" method="get" action="">
            <input type="text" name="q" class="form-control form-control-sm d-inline" style="width:160px;display:inline;" value="<?=htmlspecialchars($search)?>" placeholder="账号/姓名/邮箱">
            <button type="submit" class="btn btn-main btn-sm">搜索</button>
        </form>
        <a href="add_admin.php" class="btn btn-main btn-sm shadow-sm"><i class="bi bi-plus-lg"></i> 添加管理员</a>
    </div>
</div>
<div class="card main-card">
    <div class="card-header"><i class="bi bi-table"></i> 管理员列表</div>
    <div class="card-body p-0">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>ID</th>
                    <th>账号</th>
                    <th>姓名</th>
                    <th>邮箱</th>
                    <th>状态</th>
                    <th>角色</th>
                    <th>创建时间</th>
                    <th class="text-center">操作</th>
                </tr>
            </thead>
            <tbody>
            <?php if (empty($admins)): ?>
                <tr><td colspan="8" class="text-center text-muted">暂无管理员</td></tr>
            <?php else: foreach ($admins as $admin): ?>
                <tr>
                    <td><?=htmlspecialchars($admin['id'])?></td>
                    <td><?=htmlspecialchars($admin['username'])?></td>
                    <td><?=htmlspecialchars($admin['name'])?></td>
                    <td><?=htmlspecialchars($admin['email'])?></td>
                    <td>
                        <?php if ($admin['status']): ?>
                            <span class="badge bg-success">启用</span>
                        <?php else: ?>
                            <span class="badge bg-secondary">禁用</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php
                            if (!empty($admin['roles'])) {
                                echo htmlspecialchars($admin['roles']);
                            } else {
                                echo '<span class="text-muted">无</span>';
                            }
                        ?>
                    </td>
                    <td><?=htmlspecialchars($admin['created_at'])?></td>
                    <td class="text-center">
                        <a href="edit_admin.php?id=<?=$admin['id']?>" class="btn btn-sm btn-outline-secondary btn-action me-1"><i class="bi bi-pencil"></i> 编辑</a>
                        <?php if ($admin['id'] != $_SESSION['admin_user_id']): ?>
                            <a href="delete_admin.php?id=<?=$admin['id']?>" class="btn btn-sm btn-danger btn-action" onclick="return confirm('确定要删除该管理员吗？');"><i class="bi bi-trash"></i> 删除</a>
                        <?php else: ?>
                            <span class="text-muted">当前登录</span>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?php include 'footer.php'; ?>