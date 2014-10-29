<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="utf-8">
    <title>Powserver</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <meta name="description" content=""/>
    <meta http-equiv="Content-Type" content="text/html;charset=utf-8">
    <!-- css -->
    <link href="<?php echo URL::asset('css/register_login.css'); ?>" rel="stylesheet">
</head>
<body>

<div class="register_login_bg">
    <div class="rl_title"></div>
    <div class="bg_login form_bg">
        <form action="<?php echo URL::action('LoginController@loginStore'); ?>" method="post">
            <div class="item">
                <input type="text" name="username" placeholder="请输入账号"/>
            </div>
            <div class="item">
                <input type="password" name="password" placeholder="请输入密码"/>
            </div>
            <a href="#" class="fotget_password">忘记密码？</a>
            <input type="submit" value="立即登录" class="submit_button"/>
        </form>
    </div>
    <div class="login_footer clearfix">
        <span><em>其他登录方式</em><a href="#" class="icon_sina"><img src="images/icon_sina.png"></a><a href="#"
                                                                                                  class="icon_qq"><img
                    src="images/icon_qq.png"></a></span>
        <a href="#" class="free_reigster">免费注册</a>
    </div>
</div>
</body>
</html>