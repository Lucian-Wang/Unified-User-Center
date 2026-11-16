<?php
session_start();
$user_id = $_SESSION['user_id'] ?? 0;
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <title>帮助中心 - 灯火通明用户中心</title>
    <meta name="viewport" content="width=device-width">
    <style>
        body { background: #f6f8fa; color: #24292f; font-family: -apple-system,BlinkMacSystemFont,"Segoe UI",Helvetica,Arial,sans-serif,"Apple Color Emoji","Segoe UI Emoji"; font-size: 15px; margin: 0; }
        .main { max-width: 900px; margin: 48px auto 24px auto; background: #fff; border-radius: 8px; border: 1px solid #d0d7de; box-shadow: 0 4px 24px 0 rgba(149,157,165,0.10); padding: 44px 36px 38px 36px; }
        h1 { font-size: 2em; margin-bottom: 22px; }
        h2 { font-size: 1.32em; color: #1f2328; margin-top: 32px; margin-bottom: 10px; }
        h3 { font-size: 1em; color: #656d76; margin-top: 24px; }
        ul { margin: 0 0 18px 24px; }
        li { margin-bottom: 6px; }
        .q { color: #1a7f37; font-weight: bold; }
        .a { margin-bottom: 16px; }
        a { color: #0969da; text-decoration: none; }
        a:hover { text-decoration: underline; color: #218bff; }
        .toc { margin-bottom: 36px; background: #f6f8fa; border: 1px solid #d8dee4; border-radius: 5px; padding: 18px; }
        .toc a { margin-right: 18px; }
        @media (max-width: 700px) {
            .main { padding: 18px 2vw; }
        }
    </style>
</head>
<body>
<div class="main">
    <h1>帮助中心</h1>
    <div class="toc">
        <b>常见问题导航：</b>
        <a href="#register">注册与登录</a>
        <a href="#profile">个人资料</a>
        <a href="#mobile">绑定/更换手机</a>
        <a href="#avatar">更换头像</a>
        <a href="#password">密码相关</a>
        <a href="#security">账号安全</a>
        <a href="#contact">联系客服</a>
    </div>

    <h2 id="register">注册与登录</h2>
    <div class="q">Q：如何注册/登录账号？</div>
    <div class="a">
        在登录页输入您的手机号、验证码（如有）及密码即可注册或登录。<br>
        若已注册可直接登录，忘记密码可通过“找回密码”功能重置。
    </div>
    <div class="q">Q：收不到短信验证码怎么办？</div>
    <div class="a">
        请确认手机号输入无误，且手机信号正常。若多次未收到，可尝试稍后重试或联系微信客服。
    </div>

    <h2 id="profile">个人资料</h2>
    <div class="q">Q：如何修改个人信息？</div>
    <div class="a">
        登录后，进入“个人资料”页面，可修改姓名、头像、手机号等信息。
    </div>

    <h2 id="mobile">绑定/更换手机</h2>
    <div class="q">Q：如何更换绑定的手机号？</div>
    <div class="a">
        在“个人资料-修改手机号”页面，输入新手机号并完成短信验证即可更换。
    </div>
    <div class="q">Q：手机号已被占用怎么办？</div>
    <div class="a">
        一个手机号只能绑定一个账号。如遇特殊情况，请联系客服协助处理。
    </div>

    <h2 id="avatar">更换头像</h2>
    <div class="q">Q：如何上传或更换头像？</div>
    <div class="a">
        在“个人资料-更换头像”页面，选择图片文件（支持jpg、png、gif、webp格式，最大2M），上传后即可更换头像。
    </div>

    <h2 id="password">密码相关</h2>
    <div class="q">Q：如何重置/修改密码？</div>
    <div class="a">
        可在登录页面通过“忘记密码”找回，也可在登录后“修改密码”。
    </div>
    <div class="q">Q：密码安全有哪些建议？</div>
    <div class="a">
        建议使用<strong>较复杂的密码</strong>（字母+数字+符号），并定期更换，避免与其他网站相同。
    </div>

    <h2 id="security">账号安全</h2>
    <div class="q">Q：如何确保账号安全？</div>
    <div class="a">
        <ul>
            <li>定期更换密码，勿与他人共享账号。</li>
            <li>建议绑定常用手机号，便于找回密码。</li>
            <li>如发现异常登录及时修改密码。</li>
        </ul>
    </div>
    <div class="q">Q：是否支持二步验证？</div>
    <div class="a">
        暂未开放二步验证功能，将在后续版本支持。
    </div>

    <h2 id="contact">联系客服</h2>
    <div class="q">Q：遇到问题如何联系客服？</div>
    <div class="a">
        可通过“联系客服”页面扫码添加微信客服或发送邮件至 <a href="mailto:im@lucian.wang">im@lucian.wang</a> 寻求帮助。
    </div>

    <h2>其他说明</h2>
    <ul>
        <li>如有更多疑问，请优先查看本帮助中心或联系客服。</li>
        <li>本页面内容会根据系统功能不断更新。</li>
    </ul>
</div>
</body>
</html>