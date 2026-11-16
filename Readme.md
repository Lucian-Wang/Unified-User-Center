# Unified-User-Center 统一用户中心

## 项目简介

`Unified-User-Center` 是 wsx.tax 系列 SaaS 系统的统一用户中心模块，基于 PHP 实现，提供注册、登录、认证、用户管理、权限管理及 API 支持。适合需要多系统统一身份认证、用户管理的应用场景。

---

## 功能特性

- 用户注册、登录、找回密码（短信验证码）
- 用户信息与权限管理
- Token 鉴权 API（支持 JWT）
- 管理后台（多角色，权限分配）
- 操作日志
- 丰富可扩展的接口，便于与其它系统对接

---

## 安装与部署

1. **克隆代码**
   ```bash
   git clone https://github.com/Lucian-Wang/Unified-User-Center.git
   cd Unified-User-Center
   ```

2. **初始化数据库**
   - 创建数据库（如 user_wsx_tax）
   - 导入 [`install.sql`](https://github.com/Lucian-Wang/Unified-User-Center/blob/main/install.sql)
   ```bash
   mysql -u root -p user_wsx_tax < install.sql
   ```

3. **配置数据库连接**
   - 在运行环境中设置 `DB_HOST`、`DB_NAME`、`DB_USER`、`DB_PASS` 和至少 32 字符的随机 `JWT_SECRET`。短信使用 `YUNPIAN_API_KEY`、`YUNPIAN_SMS_SIGN`。
   - SQL 仅保留结构与通用权限设置，不含用户及日志。导入后设置 `ADMIN_USERNAME`、至少 16 字符的 `ADMIN_PASSWORD`，运行 `php scripts/create_admin.php` 创建管理员，完成后清除 `ADMIN_PASSWORD` 环境变量。

4. **Web服务器部署**
   - 推荐 PHP 7.2+、MySQL 5.7+
   - 如需省略 `.php` 后缀，按 [API 文档](api/README.md#路由规则nginx) 配置单层接口 rewrite；仓库没有 nginx.txt。
   - 网站根目录指向项目根目录；静态资源位于 `static/`，仓库没有 `public/` 目录

5. **访问测试**
   - 打开首页（如 `index.php`），测试注册和登录流程

---

## 目录结构说明

- `admin/`   — 管理后台前端与控制器
- `api/`     — 对外 API 接口（注册、登录、用户信息等）
- `include/` — 公用库、配置、数据库操作等
- `static/`  — 静态资源（如头像）
- `user/`    — 用户业务逻辑模块
- `install.sql` — 数据库结构和初始化数据

---

## 主要数据库表结构

- `users`：用户基本信息、加密密码、token 等
- `roles`：角色（如超级管理员、普通管理员、普通用户）
- `permissions`：权限点
- `user_roles`：用户与角色关系
- `role_permissions`：角色与权限关系
- `admin_users`：后台管理员登录账号
- `logs`：操作日志

---

## API 文档

### 1. 注册

- **接口**：`POST /api/register`
- **参数**：`mobile`（手机号），`password`（密码），`name`（姓名），`sms_code`（短信验证码）
- **返回**：
  ```json
  { "code": 0, "msg": "注册成功" }
  ```

### 2. 登录

- **接口**：`POST /api/login`
- **参数**：`mobile`（手机号），`password`（密码）
- **返回**：
  ```json
  { "code": 0, "token": "JWT_TOKEN", "user": { "id":1, "mobile":"139****", "name":"张三" } }
  ```
- **实现细节**：登录成功后返回 JWT Token，JWT 有效期 24 小时；`user_list`、`log_list` 使用 Bearer JWT。`user_info` 使用数据库中的 API Token，两者不能互换。

### 3. 发送短信验证码

- **接口**：`POST /api/send_sms`
- **参数**：`phone`
- **返回**：
  ```json
  { "ok": true, "msg": "发送成功" }
  ```

### 4. 找回密码

- **接口**：`POST /api/forgot_password`
- **参数**：`mobile`，`sms_code`，`new_password`
- **返回**：
  ```json
  { "code": 0, "msg": "密码已重置" }
  ```

### 5. 获取当前用户信息

- **接口**：`GET /api/user_info`
- **查询参数**：`?token={api_token}`，需匹配数据库的 `api_token`、`api_token_expire` 和启用状态；不是登录接口返回的 JWT。
- **返回**：
  ```json
  { "code": 0, "user": { "id":1, ... } }
  ```

### 6. 用户管理（仅管理员）

- **接口**：`GET /api/user_list`
- **Header**：`Authorization: Bearer {token}`
- **返回**：
  ```json
  { "code": 0, "list": [...] }
  ```

### 7. 日志查询（仅管理员）

- **接口**：`GET /api/log_list`
- **Header**：`Authorization: Bearer {token}`
- **返回**：
  ```json
  { "code": 0, "list": [...] }
  ```

---

## 权限与角色说明

- 支持多角色（如超级管理员、普通管理员、普通用户）
- 通过 `roles`、`permissions`、`role_permissions`、`user_roles` 表灵活分配
- 可在后台管理中分配权限

---

## 二次开发建议

- 各接口认证方式不同：管理员列表接口使用 Bearer JWT，用户详情使用查询参数 API Token，其他资料修改接口请以具体源码和登录会话要求为准
- 新增功能建议参考现有 API 风格
- 业务逻辑拆分在 `user/`、`api/`、`include/` 目录，便于维护

---

## 常见问题

- 登录失败：请确认手机号/密码正确、用户状态有效
- Token 失效：Token 有效期 24 小时，过期需重新登录
- 权限不足：请检查用户角色与权限配置
- API 403：Token 不正确或无访问权限

---

## 参与贡献

1. Fork 本仓库
2. 新建分支开发
3. 提交 Pull Request
4. Issue 区反馈建议

---

## 许可协议

MIT License

---

> 技术支持与更多资料请参考各源码文件及注释，或联系维护者。

## 维护与反馈

维护者：[Lucian](https://lucian.wang/) · 邮箱：im@lucian.wang · [问题反馈](https://github.com/Lucian-Wang/Unified-User-Center/issues)
