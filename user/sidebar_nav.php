<aside class="gh-sidebar">
    <h2>用户中心</h2>
    <ul>
        <li><a class="gh-nav-link<?=basename($_SERVER['SCRIPT_NAME']) == 'profile.php' ? ' active' : ''?>" href="profile.php">个人资料</a></li>
        <li><a class="gh-nav-link<?=basename($_SERVER['SCRIPT_NAME']) == 'edit_profile.php' ? ' active' : ''?>" href="edit_profile.php">修改资料</a></li>
         <li><a class="gh-nav-link<?=basename($_SERVER['SCRIPT_NAME']) == 'edit_pwd.php' ? ' active' : ''?>" href="edit_pwd.php">修改密码</a></li>
        <li><a class="gh-nav-link<?=basename($_SERVER['SCRIPT_NAME']) == 'edit_mobile.php' ? ' active' : ''?>" href="edit_mobile.php">更换手机</a></li>
        <li><a class="gh-nav-link<?=basename($_SERVER['SCRIPT_NAME']) == 'edit_avatar.php' ? ' active' : ''?>" href="edit_avatar.php">更换头像</a></li>
    </ul>
    <h2 style="margin-top:36px;">其他</h2>
    <ul>
        <li><a class="gh-nav-link<?=basename($_SERVER['SCRIPT_NAME']) == 'log_history.php' ? ' active' : ''?>" href="log_history.php">安全日志</a></li>
         <li><a class="gh-nav-link" href="help.php" target="_blank">帮助中心</a></li>
       <li><a class="gh-nav-link" href="mailto:im@lucian.wang">联系客服</a></li>
    </ul>
    <h2 style="margin-top:36px;">业务系统</h2>
    <ul>
        <li><a class="gh-nav-link" href="https://saas.wsx.tax/index.php" target="_blank">代理记账CRM</a></li>
        <li><a class="gh-nav-link" href="https://b.wsx.tax/dashboard.php" target="_blank">小本创业记账</a></li>
    </ul>
</aside>