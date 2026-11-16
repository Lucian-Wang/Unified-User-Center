# 用户中心 API 简要文档

> 注意：实际 API 路径为 `/api/xxx`，比如 `/api/user_info`，而不是 `/api/user/info`。服务端 Nginx 只对 `/api/xxx` 这种单层路径做 rewrite，详见下方说明。

---

## 主要接口列表

### /api/login

- POST
- 参数：mobile, password
- 返回：{ code, token, user }

### /api/register

- POST
- 参数：mobile, password, name, sms_code
- 返回：{ code, msg }

### /api/send_sms

- POST
- 参数：phone
- 返回：{ ok, msg }

### /api/forgot_password

- POST
- 参数：mobile, sms_code, new_password
- 返回：{ code, msg }

### /api/user_info

- GET
- 参数：token（URL参数），如 `?token=xxx`。这里使用数据库 `users.api_token`，还需 `api_token_expire` 未过期；不能直接使用 `/api/login` 返回的 JWT。
- 返回示例：
  ```json
  {
    "code": 0,
    "user": {
      "id": 1,
      "mobile": "13800000000",
      "name": "张三",
      "email": "im@lucian.wang",
      "avatar": "/static/avatar/u1_1717648800.jpg",
      "bio": "一句简介",
      "roles": ["user"],
      "status": 1,
      "created_at": "2024-06-06 10:00:00"
    }
  }
  ```
- 字段说明：含头像 URL(`avatar`)、简介(`bio`)、角色(`roles`)等

### /api/update_profile

- POST
- 参数：name, email, bio（可单独或全部提交）
- 返回：{ code, msg }
- 用于修改昵称、邮箱、简介

### /api/change_avatar

- POST
- 参数：avatar（form-data 上传图片，jpg/png/webp，2M以内）
- 返回：{ code, url, msg }
- 用于更换头像

### /api/change_mobile

- POST
- 参数：new_mobile, sms_code
- 返回：{ code, msg }
- 用于更换绑定手机号

### /api/change_password

- POST
- 参数：old_password, new_password
- 返回：{ code, msg }
- 已登录状态下修改密码

### /api/user_log_list

- GET
- 返回：{ code, list }
- 获取当前用户操作日志

### /api/user_list

- GET (管理员)
- Header: Authorization: Bearer {token}
- 返回：{ code, list }
- 获取所有用户列表

### /api/log_list

- GET (管理员)
- Header: Authorization: Bearer {token}
- 返回：{ code, list }
- 获取所有操作日志

---

## 路由规则（Nginx）

```nginx
rewrite ^/api/([a-zA-Z0-9_]+)$ /api/$1.php last;
```
即，所有接口实际路径为 `/api/接口名`，如 `/api/user_info`，而不是 `/api/user/info`。

---

> 更多细节请参见源码注释和数据库结构说明。如有新功能接口请同步补充本文档。