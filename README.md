# 域名管理系统 (Domain Management System)

<p align="center">
<a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a>
</p>

<p align="center">
<a href="#项目介绍">项目介绍</a> •
<a href="#功能特性">功能特性</a> •
<a href="#技术栈">技术栈</a> •
<a href="#安装说明">安装说明</a> •
<a href="#使用说明">使用说明</a> •
<a href="#服务配置">服务配置</a> •
<a href="#定时任务">定时任务</a>
</p>

## 项目介绍

域名管理系统是一个基于 Laravel 和 Filament 构建的专业工具，用于高效管理、监控和自动化处理大量域名。系统设计用于解决域名批量管理、状态监控和自动化处理的复杂需求，特别适合需要管理大量域名资源的团队和企业。

## 功能特性

-   ✅ **域名批量管理** - 支持批量添加、编辑和删除域名
-   🔍 **域名分组管理** - 灵活组织和分类域名资源
-   🔄 **状态监控** - 实时监控域名状态（正常/拦截）
-   🤖 **自动检测** - 自动检测域名在微信环境中的状态
-   🔀 **智能重定向** - 高效的域名重定向服务
-   📱 **微信环境适配** - 智能检测微信环境并提供引导页面

## 技术栈

-   🐘 **PHP 8.2+** - 强大的服务端脚本语言
-   🚀 **Laravel 11** - 优雅的 PHP Web 应用框架
-   🎛️ **Filament 3.x** - 现代化管理面板
-   🗄️ **Redis** - 高性能队列和缓存管理
-   💾 **MySQL/MariaDB** - 可靠的关系型数据库
-   🎨 **Tailwind CSS** - 实用优先的 CSS 框架
-   ⚡ **Vite** - 下一代前端构建工具

## 安装说明

### 系统要求

| 组件          | 最低版本/要求                                       |
| ------------- | --------------------------------------------------- |
| PHP           | 8.2+                                                |
| Composer      | 最新版本                                            |
| Redis         | 服务器                                              |
| MySQL/MariaDB | 5.7+/10.3+                                          |
| PHP 扩展      | OpenSSL, PDO, Mbstring, Tokenizer, XML, Ctype, JSON |

### 安装步骤

1. **克隆代码库**

    ```bash
    git clone https://github.com/yourusername/domain-management.git
    cd domain-management
    ```

2. **安装依赖**

    ```bash
    composer install
    ```

3. **环境配置**

    ```bash
    cp .env.example .env
    php artisan key:generate
    ```

4. **配置数据库**

    编辑 `.env` 文件，设置数据库连接信息：

    ```ini
    DB_CONNECTION=mysql
    DB_HOST=127.0.0.1
    DB_PORT=3306
    DB_DATABASE=domain_management
    DB_USERNAME=root
    DB_PASSWORD=
    ```

5. **配置 Redis**

    ```ini
    REDIS_HOST=127.0.0.1
    REDIS_PASSWORD=null
    REDIS_PORT=6379
    ```

6. **配置第三方服务**

    | 服务     | 配置项                                                                 | 说明                 | 官网                                     |
    | -------- | ---------------------------------------------------------------------- | -------------------- | ---------------------------------------- |
    | 爱发布   | `AIFABU_TOKEN=your_token_here`                                         | 爱发布 API 服务      | [爱短链](https://openapi.aifabu.com)     |
    | 短链接   | `SHORT_URL_ACCOUNT=your_account`<br>`SHORT_URL_PASSWORD=your_password` | 短链接服务           | [短链接平台](https://hm.dw.googlefb.sbs) |
    | Forage   | `FORAGE_ACCOUNT=your_account`<br>`FORAGE_PASSWORD=your_password`       | 二维码服务           | [草料二维码](https://user.cli.im)        |
    | 微信检测 | `WX_CHECK_KEY=your_api_key`                                            | 微信域名检测         | [微信检测](http://wx.rrbay.com)          |
    | 微信环境 | `DETECT_WECHAT_ENVIRONMENT=true`                                       | 是否开启微信环境检测 | -                                        |

7. **完成安装**

    ```bash
    # 运行数据库迁移
    php artisan migrate

    # 创建管理员用户
    php artisan make:filament-user

    # 启动服务
    php artisan serve
    ```

    现在可以访问 http://localhost:8000/admin 登录管理面板。

### Docker 快速部署

项目支持使用 Laravel Sail 进行 Docker 部署：

```bash
# 启动容器
./vendor/bin/sail up -d

# 运行迁移
./vendor/bin/sail artisan migrate

# 创建管理员
./vendor/bin/sail artisan make:filament-user
```

访问 http://localhost 即可使用系统。

## 使用说明

### 管理面板

![管理面板](https://raw.githubusercontent.com/filamentphp/filament/3.x/docs/images/dashboard.png)

访问 `/admin` 路径登录管理面板，系统提供以下主要功能：

| 功能       | 说明                               | 路径                |
| ---------- | ---------------------------------- | ------------------- |
| 域名管理   | 添加、编辑、删除域名，支持批量导入 | `/admin/domains`    |
| 域名分组   | 创建和管理域名分组                 | `/admin/groups`     |
| 短链接管理 | 创建和管理短链接                   | `/admin/short-urls` |
| 链接管理   | 管理爱发布链接                     | `/admin/chains`     |

### 域名管理功能

-   **批量导入**：支持多行文本导入域名，每行一个
-   **分组管理**：可为域名分配分组，便于批量操作
-   **状态监控**：自动监控域名状态（正常/拦截）
-   **智能排序**：按分组和 ID 自动排序域名

### 域名状态检测

系统提供自动检测域名状态的命令：

```bash
php artisan domains:check
```

此命令会检测所有活跃域名在微信环境中的状态，并自动更新被拦截的域名。

### 域名重定向服务

访问网站根路径时，系统会自动从活跃域名池中选择一个域名进行重定向：

1. 系统从 Redis 队列中获取一个活跃域名
2. 生成随机前缀，构建重定向 URL
3. 将用户重定向到目标域名

如需手动刷新域名队列，可以访问 `/refresh-domains`（需要登录认证）。

## 开发指南

### 开发环境启动

使用以下命令启动完整的开发环境：

```bash
composer dev
```

这将同时启动 Laravel 服务器、队列处理器、日志监控和 Vite 开发服务器。

### 测试

运行测试套件：

```bash
php artisan test
```

## 许可证

本项目基于 [MIT 许可证](https://opensource.org/licenses/MIT) 开源。

## 贡献指南

欢迎提交 Pull Request 或提出 Issue。在提交代码前，请确保：

1. 代码符合 PSR-12 编码规范
2. 添加了适当的测试
3. 所有测试都能通过

## 联系方式

如有问题或建议，请通过 Issue 系统提交。

## 项目服务说明

本项目包含以下核心服务：

### 1. 域名管理服务 (DomainRedirectService)

负责管理域名重定向队列，提供以下功能：

-   从 Redis 队列中获取可用域名
-   当队列为空时自动重新填充队列
-   按分组和 ID 排序域名
-   仅使用状态为 active 的域名

配置：无需特殊配置，使用 Redis 存储域名队列

### 2. 爱发布服务 (AifabuService)

与爱发布 API 集成，提供以下功能：

-   同步分组信息
-   同步链接信息
-   更新链接目标域名

配置：

```
AIFABU_TOKEN=your_token_here
```

### 3. 短链接服务 (ShortUrlService)

与短链接平台集成，提供以下功能：

-   创建短链接
-   更新短链接
-   删除短链接
-   同步短链接数据

配置：

```
SHORT_URL_ACCOUNT=your_account
SHORT_URL_PASSWORD=your_password
```

### 4. Forage 服务 (ForageService)

用于更新被屏蔽域名的跳转链接，提供以下功能：

-   获取活跃域名列表
-   更新被屏蔽域名的跳转链接

配置：

```
FORAGE_ACCOUNT=your_account
FORAGE_PASSWORD=your_password
```

### 5. 微信检测服务

用于检测域名在微信环境中是否被屏蔽，提供以下功能：

-   批量检测域名状态
-   自动更新被屏蔽域名的状态

配置：

```
WX_CHECK_KEY=your_api_key
```

### 6. 微信环境检测

用于检测用户是否在微信浏览器中访问，提供以下功能：

-   检测微信浏览器环境
-   在微信环境中显示引导页面

配置：

```
DETECT_WECHAT_ENVIRONMENT=true
```

## 定时任务

建议配置以下定时任务：

```bash
# 检查域名状态
* * * * * cd /path-to-your-project && php artisan domains:check >> /dev/null 2>&1

# 更新爱发布链接
0 */2 * * * cd /path-to-your-project && php artisan app:update-aifabu >> /dev/null 2>&1

# 更新 Forage 链接
0 */3 * * * cd /path-to-your-project && php artisan app:forage >> /dev/null 2>&1
```
