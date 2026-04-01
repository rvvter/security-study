# Decryptify 解密

Premium room 高级客房

Use your exploitation skills to uncover encrypted keys and get RCE.
利用你的漏洞利用技能来破解加密密钥并获取远程代码执行权限。

![image-20260401220825077](D:\security-study\writeups\images\image-20260401220825077.png)

![image-20260401223928185](D:\security-study\writeups\images\image-20260401223928185.png)

先连接vpn

![80e0be0a549932801b22d1028444fc53](D:\security-study\writeups\images\80e0be0a549932801b22d1028444fc53.png)

ping目标ip测试是否进入环境

![f84db9f047a198e6c3cfba08f2bba9cb](D:\security-study\writeups\images\f84db9f047a198e6c3cfba08f2bba9cb.png)

成功进入环境，先用nmap进行常规扫描

![b8265ddca26f824cc01d56e6f23cf28a](D:\security-study\writeups\images\b8265ddca26f824cc01d56e6f23cf28a.png)

发现有22端口的ssh服务，用-sV参数获取一下详细版本

![6ca23480f47449b7c117b7af4bc63a45](D:\security-study\writeups\images\6ca23480f47449b7c117b7af4bc63a45.png)

用searchsploit搜索一下是否有可利用漏洞

![8737a9339534d4683e1fcbb9d6a6d318](D:\security-study\writeups\images\8737a9339534d4683e1fcbb9d6a6d318.png)

在尝试一下连接ssh的方式

![a3ce9567b5e3192a3bfc8b0b31dfc6b3](D:\security-study\writeups\images\a3ce9567b5e3192a3bfc8b0b31dfc6b3.png)

![313ff541b4c83fccbf224181264f3c35](D:\security-study\writeups\images\313ff541b4c83fccbf224181264f3c35.png)

发现连接ssh需要私钥连接而不是账号和密码，不好进行爆破

重新扫描端口，用-p-参数进行全端口扫描，发现了1337下面有web服务（网络原因扫不出来，无奈查看题解）

![image-20260401221612479](D:\security-study\writeups\images\image-20260401221612479.png)

打开网页，发现一个登录页面

![image-20260401222057996](D:\security-study\writeups\images\image-20260401222057996.png)

先用dirsearch扫描一下目录

![52f329974dc14296b85e752c8013effe](D:\security-study\writeups\images\52f329974dc14296b85e752c8013effe.png)

在js下面发现了api.js

![f5b7ceebb13748397c13bd283acceed7](D:\security-study\writeups\images\f5b7ceebb13748397c13bd283acceed7.png)

这是经过混淆后的js，用反混淆的网站得到一个token值

![2cd6c826c69ad3836bf129b5ea162625](D:\security-study\writeups\images\2cd6c826c69ad3836bf129b5ea162625.png)

尝试使用这个登录/api.php

![image-20260401222533681](D:\security-study\writeups\images\image-20260401222533681.png)

![9cfd2751e97491cf4674abbefafb9689](D:\security-study\writeups\images\9cfd2751e97491cf4674abbefafb9689.png)

经过代码审计

```php
<?php
// 根据邮箱生成随机种子
function calculate_seed_value($email, $constant_value) {
    // 获取邮箱字符串长度
    $email_length = strlen($email);
    // 截取邮箱前8位，转成16进制数值
    $email_hex = hexdec(substr($email, 0, 8));
    // 拼接字符串后再转16进制（原代码这里有逻辑错误）
    $seed_value = hexdec($email_length + $constant_value + $email_hex);

    return $seed_value;
}

// 计算种子
$seed_value = calculate_seed_value($email, $constant_value);
// 设置随机数种子
mt_srand($seed_value);
// 生成随机整数
$random = mt_rand();
// 对随机数进行base64编码，作为邀请码
$invite_code = base64_encode($random);
?>
```

这是一个通过邮箱和constant_value计算出种子，用种子生成随机数再base64编码后作为邀请码

我们再访问/logs找到app.log

![da7d09f25bd84ab0f7379f53d35cdd13](D:\security-study\writeups\images\da7d09f25bd84ab0f7379f53d35cdd13.png)

发现一个alpha@fake.thm账户和邀请码 MTM0ODMzNzEyMg== 以及一个hello@fake.thm账户

那么我们现在可以使用alpha@fake.thm账户和邀请码 MTM0ODMzNzEyMg==再根据生成邀请码的逻辑，写出一个脚本来反推出constant_value的值

```php
<?php
/**
 * 根据邮箱和常量值计算种子值（用于随机数生成）
 * @param string $email 邮箱字符串
 * @param int $constant_value 常量值
 * @return int 计算后的种子值
 */
function calculate_seed_value($email, $constant_value) {
    // 获取邮箱字符串的长度
    $email_length = strlen($email);
    
    // 截取邮箱前8个字符，转换为16进制对应的十进制数值
    $email_hex = hexdec(substr($email, 0, 8));
    
    // 拼接邮箱长度、常量值、邮箱16进制值，再整体转换为16进制对应的十进制，得到种子值
    $seed_value = hexdec($email_length + $constant_value + $email_hex);
    
    // 返回最终种子值
    return $seed_value;
}

/**
 * 反向破解：通过邮箱和邀请码，反推出常量值
 * @param string $email 邮箱字符串
 * @param string $invite_code Base64编码的邀请码
 * @return mixed 成功返回常量值，失败返回提示信息
 */
function reverse_constant_value($email, $invite_code) {
    // 第一步：对邀请码进行Base64解码，并转换为整数，得到随机数值
    $random_value = intval(base64_decode($invite_code));

    // 第二步：获取邮箱相关计算参数
    // 获取邮箱长度
    $email_length = strlen($email);
    // 截取邮箱前8个字符，转换为十进制数值
    $email_hex = hexdec(substr($email, 0, 8));

    // 第三步：循环遍历可能的常量值（0 到 1000000）
    for ($constant_value = 0; $constant_value <= 1000000; $constant_value++) {
        // 用当前遍历的常量值计算种子值
        $seed_value = hexdec($email_length + $constant_value + $email_hex);

        // 使用计算出的种子值初始化随机数发生器
        mt_srand($seed_value);
        
        // 生成一个随机数，与解码后的随机值对比
        if (mt_rand() === $random_value) {
            // 匹配成功，返回找到的常量值
            return $constant_value;
        }
    }
    
    // 遍历完范围都没找到，返回失败提示
    return "Constant value not found in range.";
}

// ===================== 测试调用 =====================

// 给定的邮箱
$email = "alpha@fake.thm";
// 给定的邀请码（Base64编码格式）
$invite_code = "MTM0ODMzNzEyMg==";

// 调用函数，反向推导出常量值
$constant_value = reverse_constant_value($email, $invite_code);

// 输出结果
echo "Reversed Constant Value: " . $constant_value . PHP_EOL;
```

运行后结果为

![8dba19160aef7c2c48d7a72becd90e6d](D:\security-study\writeups\images\8dba19160aef7c2c48d7a72becd90e6d.png)

根据这个Constant Value的值，用hello@fake.thm账户生成一个邀请码

![1ff2cafc4d751c60f45f6a692f8ed193](D:\security-study\writeups\images\1ff2cafc4d751c60f45f6a692f8ed193.png)

使用该账户和邀请码登录，拿到第一个问题的flag

![8973a674380d4fb198aa7ab24ec157a1](D:\security-study\writeups\images\8973a674380d4fb198aa7ab24ec157a1.png)

发现admin@fake.thm账户，但是通过生成邀请码，不能登录admin账户

最后查看源码发现date参数

![7aa3634f38af038bbe01461da7e3ea35](D:\security-study\writeups\images\7aa3634f38af038bbe01461da7e3ea35.png)

用date参数传入这个value页面没有变化，但是改变value过后

![0caf134954c44a81163f508dafd3c5b6](D:\security-study\writeups\images\0caf134954c44a81163f508dafd3c5b6.png)

出现了填充报错，所以我们可以尝试padding oracle attack

接下来，我们需要确定这个 base64 编码的实际内容，并使用填充预言攻击来解密该数据。使用源文件中的值运行 padre 命令，我们发现执行的是 `date +%Y` 命令，从而在页脚中显示了当前年份。

![98499f8e965a50ff6b0e729911f0198b](D:\security-study\writeups\images\98499f8e965a50ff6b0e729911f0198b.png)

使用padre工具所需的cookie在当前页面抓包拿到

![495f3f2e9b2a9b0b7c08e964fabcde34](D:\security-study\writeups\images\495f3f2e9b2a9b0b7c08e964fabcde34.png)

接下来就可以使用padre加密自己的命令，我们需要cat /home/ubuntu/flag.txt来获取最后的flag

![1e458a4b7bd39b4569e4d8f9fe6b4a93](D:\security-study\writeups\images\1e458a4b7bd39b4569e4d8f9fe6b4a93.png)

传入参数

![51180e56b659d7ecdff1b9530b533fe0](D:\security-study\writeups\images\51180e56b659d7ecdff1b9530b533fe0.png)



