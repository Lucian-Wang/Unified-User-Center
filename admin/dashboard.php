<?php
require_once '../include/db.php';
require_once '../include/admin_auth.php';
require_admin_login($pdo);
include 'header.php';
?>
<div class="row mb-4">
  <div class="col-lg-4 col-md-6 mb-4">
    <div class="card main-card text-center">
      <div class="card-body py-5">
        <i class="bi bi-people display-4 text-info mb-3"></i>
        <h5 class="card-title mb-3">用户管理</h5>
        <p class="card-text text-muted">管理平台所有用户信息与状态。</p>
        <a href="users.php" class="btn btn-main px-4">进入</a>
      </div>
    </div>
  </div>
  <div class="col-lg-4 col-md-6 mb-4">
    <div class="card main-card text-center">
      <div class="card-body py-5">
        <i class="bi bi-person-plus display-4 text-info mb-3"></i>
        <h5 class="card-title mb-3">添加用户</h5>
        <p class="card-text text-muted">直接添加新的前台用户账号。</p>
        <a href="add_user.php" class="btn btn-main px-4">添加</a>
      </div>
    </div>
  </div>
  <div class="col-lg-4 col-md-6 mb-4">
    <div class="card main-card text-center">
      <div class="card-body py-5">
        <i class="bi bi-person-gear display-4 text-success mb-3"></i>
        <h5 class="card-title mb-3">管理员管理</h5>
        <p class="card-text text-muted">平台后台管理员账号、角色配置。</p>
        <a href="admins.php" class="btn btn-main px-4">进入</a>
      </div>
    </div>
  </div>
  <div class="col-lg-4 col-md-6 mb-4">
    <div class="card main-card text-center">
      <div class="card-body py-5">
        <i class="bi bi-person-add display-4 text-success mb-3"></i>
        <h5 class="card-title mb-3">添加管理员</h5>
        <p class="card-text text-muted">创建新的后台管理员账号。</p>
        <a href="add_admin.php" class="btn btn-main px-4">添加</a>
      </div>
    </div>
  </div>
  <div class="col-lg-4 col-md-6 mb-4">
    <div class="card main-card text-center">
      <div class="card-body py-5">
        <i class="bi bi-clock-history display-4 text-warning mb-3"></i>
        <h5 class="card-title mb-3">操作日志</h5>
        <p class="card-text text-muted">审计所有管理及用户操作行为。</p>
        <a href="logs.php" class="btn btn-main px-4">进入</a>
      </div>
    </div>
  </div>
  <div class="col-lg-4 col-md-6 mb-4">
    <div class="card main-card text-center">
      <div class="card-body py-5">
        <i class="bi bi-gear display-4 text-primary mb-3"></i>
        <h5 class="card-title mb-3">系统设置</h5>
        <p class="card-text text-muted">平台参数、短信等配置。</p>
        <a href="settings.php" class="btn btn-main px-4">进入</a>
      </div>
    </div>
  </div>
</div>
<?php include 'footer.php'; ?>