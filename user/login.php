<?php
session_start();
require_once '../include/db.php';
require_once '../include/sms.php';

// 获取是否启用短信验证码登录
$stmt = $pdo->prepare("SELECT value FROM settings WHERE key_name='use_sms_code'");
$stmt->execute();
$row = $stmt->fetch();
$use_sms_code = $row ? $row['value'] : '1';

$error = '';
$mobile = $_POST['mobile'] ?? '';
$password = $_POST['password'] ?? '';
$code = $_POST['code'] ?? '';
$login_type = $_POST['login_type'] ?? 'pwd';
$captcha = $_POST['captcha'] ?? '';
$redirect_uri = $_GET['redirect_uri'] ?? $_POST['redirect_uri'] ?? '';

// ========= SSO已登录自动跳转逻辑 =========
if (isset($_SESSION['user_id']) && $_SESSION['user_id']) {
    if ($redirect_uri) {
        $token = bin2hex(random_bytes(16));
        $stmt2 = $pdo->prepare("UPDATE users SET api_token=?, api_token_expire=DATE_ADD(NOW(), INTERVAL 2 HOUR) WHERE id=?");
        $stmt2->execute([$token, $_SESSION['user_id']]);
        header('Location: ' . $redirect_uri . (strpos($redirect_uri, '?')!==false ? '&' : '?') . 'token=' . $token);
        exit;
    } else {
        header('Location: profile.php');
        exit;
    }
}
// =========================================

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 图形验证码优先校验
    if (!isset($_SESSION['captcha_code']) || strtolower(trim($captcha)) !== strtolower($_SESSION['captcha_code'])) {
        $error = "验证码错误";
        unset($_SESSION['captcha_code']);
    } elseif (!preg_match('/^1\d{10}$/', $mobile)) {
        $error = "手机号格式不正确";
    } elseif ($login_type=='pwd') {
        if (!$password) {
            $error = "请输入密码";
        } else {
            $stmt = $pdo->prepare("SELECT * FROM users WHERE mobile=? AND status=1 LIMIT 1");
            $stmt->execute([$mobile]);
            $u = $stmt->fetch();
            if ($u && password_verify($password, $u['password'])) {
                $_SESSION['user_id'] = $u['id'];
                // 写入登录日志
                $stmt_log = $pdo->prepare("INSERT INTO logs (user_id, type, message, ip, created_at) VALUES (?, 'login', ?, ?, NOW())");
                $stmt_log->execute([$u['id'], '登录成功', $_SERVER['REMOTE_ADDR']]);
                if ($redirect_uri) {
                    $token = bin2hex(random_bytes(16));
                    $stmt2 = $pdo->prepare("UPDATE users SET api_token=?, api_token_expire=DATE_ADD(NOW(), INTERVAL 2 HOUR) WHERE id=?");
                    $stmt2->execute([$token, $u['id']]);
                    header('Location: ' . $redirect_uri . (strpos($redirect_uri, '?')!==false ? '&' : '?') . 'token=' . $token);
                    exit;
                } else {
                    header('Location: profile.php');
                    exit;
                }
            } else {
                $error = "账号或密码错误";
            }
        }
    } elseif ($login_type=='sms' && $use_sms_code=='1') {
        if (
            !isset($_SESSION['sms_verify_phone'], $_SESSION['sms_verify_code'], $_SESSION['sms_verify_time']) ||
            $_SESSION['sms_verify_phone'] !== $mobile ||
            $_SESSION['sms_verify_code'] != $code ||
            time() - $_SESSION['sms_verify_time'] > 300
        ) {
            $error = "短信验证码错误或已过期";
        } else {
            $stmt = $pdo->prepare("SELECT * FROM users WHERE mobile=? AND status=1 LIMIT 1");
            $stmt->execute([$mobile]);
            $u = $stmt->fetch();
            if ($u) {
                $_SESSION['user_id'] = $u['id'];
                unset($_SESSION['sms_verify_code'], $_SESSION['sms_verify_phone'], $_SESSION['sms_verify_time']);
                // 写入登录日志
                $stmt_log = $pdo->prepare("INSERT INTO logs (user_id, type, message, ip, created_at) VALUES (?, 'login', ?, ?, NOW())");
                $stmt_log->execute([$u['id'], '登录成功', $_SERVER['REMOTE_ADDR']]);
                if ($redirect_uri) {
                    $token = bin2hex(random_bytes(16));
                    $stmt2 = $pdo->prepare("UPDATE users SET api_token=?, api_token_expire=DATE_ADD(NOW(), INTERVAL 2 HOUR) WHERE id=?");
                    $stmt2->execute([$token, $u['id']]);
                    header('Location: ' . $redirect_uri . (strpos($redirect_uri, '?')!==false ? '&' : '?') . 'token=' . $token);
                    exit;
                } else {
                    header('Location: profile.php');
                    exit;
                }
            } else {
                $error = "手机号未注册";
            }
        }
    }
    unset($_SESSION['captcha_code']);
}
include 'header.php';
?>
<style>
.auth-form-header {
    text-align:center;
}
.auth-form-header h1 {
    font-size: 24px;
    font-weight: 400;
    margin: 24px 0 0 0;
}
.auth-form-body {
    background: #f6f8fa;
    border: 1px solid #d0d7de;
    border-radius: 6px;
    box-shadow: 0 1px 8px 0 rgba(0,0,0,0.04);
    padding: 32px 24px 16px 24px;
    margin-top: 16px;
}
.auth-form-body label {
    font-weight: 600;
    font-size: 14px;
    margin-bottom: 6px;
    color: #24292f;
    display: block;
}
.auth-form-body .form-control {
    border-radius: 6px;
    border: 1px solid #d0d7de;
    background: #fff;
    font-size: 15px;
    padding: 6px 12px;
    width: 100%;
    margin-bottom: 16px;
    box-sizing: border-box;
}
.auth-form-body .form-control:focus {
    border-color: #0969da;
    box-shadow: 0 0 0 2px rgba(9,105,218,0.1);
}
.btn-primary {
    width: 100%;
    background: #238636;
    color: #fff;
    border: none;
    border-radius: 6px;
    padding: 10px 0;
    font-size: 16px;
    font-weight: 600;
    margin-top: 8px;
    transition: background .18s;
    cursor: pointer;
}
.btn-primary:hover {
    background: #2ea043;
}
.label-link {
    color: #0969da;
    font-size: 13px;
    float: right;
    margin-top: 2px;
    text-decoration: none;
}
.label-link:hover {
    text-decoration: underline;
}
.get-sms-btn {
    display: block;
    width: 100%;
    margin-bottom: 16px;
    background: #fff;
    border: 1px solid #24292f;
    border-radius: 6px;
    color: #24292f;
    font-size: 16px;
    font-weight: 600;
    padding: 7px 0;
    cursor: pointer;
    transition: border 0.2s, color 0.2s;
    outline: none;
}
.get-sms-btn:disabled {
    color: #8c959f;
    border-color: #d0d7de;
    background: #f6f8fa;
    cursor: not-allowed;
}
.get-sms-btn:hover:enabled {
    border-color: #0969da;
    color: #0969da;
    background: #f6f8fa;
}
.auth-form-footer {
    background: #fff;
    border: 1px solid #d0d7de;
    border-radius: 6px;
    box-sizing: border-box;
    padding: 20px 24px 10px 24px;
    margin: 16px auto 0 auto;
    max-width: 340px;
    font-size: 15px;
    text-align: center;
}
.btn-link-passkey {
    color: #0969da;
    background: none;
    border: none;
    padding: 0;
    font-size: 16px;
    font-weight: 600;
    cursor: pointer;
    text-align: center;
    display: inline-block;
    margin-bottom: 2px;
}
.btn-link-passkey:hover {
    text-decoration: underline;
    color: #218bff;
}
@media (max-width:500px){
    .auth-form-body, .auth-form-footer {
        max-width: 99vw;
        padding-left: 2vw;
        padding-right: 2vw;
    }
}
</style>
<div class="auth-form-header p-0">
    <h1>用户登录</h1>
</div>
<?php if($error): ?>
<div class="alert alert-danger" style="background:#ffebe9;border:1px solid #d0d7de;color:#cf222e;padding:12px 16px;border-radius:6px;margin-top:16px;"><?=htmlspecialchars($error)?></div>
<?php endif;?>
<div class="auth-form-body mt-3">
    <form method="post" autocomplete="off" id="loginForm">
        <input type="hidden" name="redirect_uri" value="<?=htmlspecialchars($redirect_uri)?>">
        <input type="hidden" name="login_type" id="login_type" value="<?=htmlspecialchars($login_type)?>">
        <label for="login_mobile">手机号</label>
        <input type="text" name="mobile" id="login_mobile" value="<?=htmlspecialchars($mobile)?>" class="form-control" required autocomplete="username">

        <div id="pwd_form"<?=($login_type=='pwd'?'':' style="display:none;"')?>>
            <label for="login_password">
                密码
                <a href="forgot.php" class="label-link">忘记密码？</a>
            </label>
            <input type="password" name="password" id="login_password" value="<?=htmlspecialchars($password)?>" class="form-control" autocomplete="current-password">
        </div>

        <?php if($use_sms_code=='1'): ?>
        <div id="sms_form"<?=($login_type=='sms'?'':' style="display:none;"')?>>
            <label for="login_code">短信验证码</label>
            <input type="text" name="code" id="login_code" value="<?=htmlspecialchars($code)?>" class="form-control" autocomplete="one-time-code">
            <button type="button" class="get-sms-btn" id="sendCodeBtn">获取验证码</button>
        </div>
        <?php endif;?>

        <label for="login_captcha">
            图形验证码
            <a href="javascript:;" class="label-link" onclick="document.getElementById('captchaImg').src='/include/captcha.php?rand='+Math.random()">换一张</a>
        </label>
        <div style="display:flex;align-items:center;gap:8px;margin-bottom:16px;">
            <input type="text" name="captcha" id="login_captcha" class="form-control" required maxlength="4" autocomplete="off" style="width:80px;margin-bottom:0;">
            <img src="/include/captcha.php?rand=<?=time()?><?=mt_rand(1000,9999)?>" id="captchaImg" style="cursor:pointer;height:34px;border-radius:4px;" alt="验证码" title="点击刷新" onclick="this.src='/include/captcha.php?rand='+Math.random()">
        </div>
        <button class="btn-primary" type="submit">登录</button>
    </form>
</div>
<div class="auth-form-footer">
    <button type="button"
        id="toggleLoginTypeBtn"
        class="btn-link-passkey">
        <?=($login_type=='pwd'?'使用短信验证码登录':'使用密码登录')?>
    </button>
    <div style="margin-top:8px;font-size:15px;">
        没有账号？<a href="register.php">立即注册</a>
    </div>
</div>
<script>
<?php if($use_sms_code=='1'): ?>
document.getElementById('sendCodeBtn').onclick = function() {
  let mobile = document.querySelector('input[name="mobile"]').value.trim();
  let captcha = document.querySelector('input[name="captcha"]').value.trim();
  if(!/^1\d{10}$/.test(mobile)){
      alert('请先填写正确的手机号');
      return;
  }
  if(!captcha){
      alert('请先填写图片验证码');
      return;
  }
  this.disabled = true;
  this.innerText = "发送中...";
  fetch('send_code.php', {
    method: 'POST',
    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
    body: 'mobile=' + encodeURIComponent(mobile) + '&captcha=' + encodeURIComponent(captcha)
  })
    .then(res=>res.json())
    .then(d=>{
      if(d.status==='ok'){
        alert('验证码已发送');
      }else{
        alert(d.msg||'发送失败');
        document.getElementById('captchaImg').src = '/include/captcha.php?rand=' + Math.random();
      }
      this.disabled = false;
      this.innerText = "获取验证码";
    }).catch(()=>{
      alert('网络异常');
      this.disabled = false;
      this.innerText = "获取验证码";
    });
};
<?php endif; ?>
document.getElementById('toggleLoginTypeBtn').onclick = function(){
    var type = document.getElementById('login_type').value;
    if(type==='pwd'){
        document.getElementById('pwd_form').style.display='none';
        <?php if($use_sms_code=='1'): ?>document.getElementById('sms_form').style.display='block';<?php endif; ?>
        document.getElementById('login_type').value='sms';
        this.innerText='使用密码登录';
    }else{
        document.getElementById('pwd_form').style.display='block';
        <?php if($use_sms_code=='1'): ?>document.getElementById('sms_form').style.display='none';<?php endif; ?>
        document.getElementById('login_type').value='pwd';
        this.innerText='使用短信验证码登录';
    }
};
<?php if($error): ?>
window.onload = function(){
  document.getElementById('captchaImg').src = '/include/captcha.php?rand=' + Math.random();
};
<?php endif; ?>
</script>
<?php include 'footer.php'; ?>