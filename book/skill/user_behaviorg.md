# 用户体系

用户体系是系统内置封装的用户模板 ,实现了

- 用户的登录注册
- 用户发布数据 ,可以简单的理解成用户xx,这个 xx 可以是用户喜欢,用户收藏,用户视频等
- 好友时间线(timeline)  : 就像微博中我关注了一个用户,那么这个用户发布的数据都会出现在自己的页面中(正在做)
- 用户结构定义如下,用户的密码处于安全考虑不会输出,而且服务会自动对密码加密

    {
        "id": //唯一 ID ,
        "name": //用户名称不可重复,
        "nick_name": //用户昵称,
        "sex": //性别,
        "age": //岁数,
        "email": //邮箱,
        "phone": //电话,
        "address": //地址,
        "status": //状态
        "password" //安全考虑此字段不会输出
    }


## 如何调用

### 获取所有用户 [get]

http://domain/api_user?&token=xxx

返回

```
{
    "code": 1,
    "message": "success",
    "data": [
        {
            "id": 8,
            "name": "test-24-1",
            "nick_name": "test-24-1",
            "sex": "",
            "age": "",
            "email": "",
            "phone": "",
            "address": "",
            "status": ""
        },
      ...
    ]
}
```


### 获取单个用户 [get]
http://domain/api_user/ `用户ID`?&token=xxx

例如
http://domain/api_user/1?&token=xxx
获取用户 ID 是1的用户信息


### 用户名重复检测 [post]

http://pow/api_user/name_check?version=1&token=8ch86oMZN6p1N1/TcSr9Fw==

`[post data]`

name:xxxx

返回

```
未注册
{
    "code": 1,
    "message": "用户名尚未被注册",
    "data": []
}
已经注册
{
    "code": -1,
    "message": "用户名已经被注册",
    "data": []
}
```


### 用户登录 [post]

http://pow/api_user/login?version=1&token=8ch86oMZN6p1N1/TcSr9Fw==


`[post data]`

name:xxxx

password:xxx

返回

```
登录成功
{
    "code": 1,
    "message": "登陆成功",
    "data": {
        "id": 7,
        "name": "test5",
        "nick_name": "test5",
        "sex": null,
        "age": null,
        "email": null,
        "phone": null,
        "address": null,
        "status": null
    }
}
登录失败
{
    "code": -1,
    "message": "登陆失败,用户名或者密码错误",
    "data": []
}
```


### 用户发布数据相关

#### 创建用户发布数据


