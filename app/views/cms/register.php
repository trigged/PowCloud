<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="utf-8">
    <title>Powserver</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <meta name="description" content=""/>
    <meta http-equiv="Content-Type" content="text/html;charset=utf-8">
    <!-- css -->
    <link href="css/base.css" rel="stylesheet"/>
</head>
<body>
<style type="text/css">
        /*登录和注册*/
    .register_login_bg {
        background: url(images/register_login_bg.jpg) no-repeat;
        overflow: hidden;
    }

    .rl_title {
        width: 161px;
        height: 29px;
        background: url(images/rl_logo.png) no-repeat;
        margin: 50px auto 10px;
    }

    .reigster_bg {
        width: 640px;
        height: 741px;
        background: url(images/bg_register.png) no-repeat;
        margin: 70px auto 0;
        overflow: hidden;
    }

        /*注册表格的文字label*/
    .reigster_bg form {
        margin: 123px 0 0 0;
    }

    .reigster_bg .item {
        margin: 0 0 19px 74px;
    }

    .reigster_bg .item label {
        font-size: 18px;
        color: #fff;
        text-align: justify;
        width: 60px;
        display: inline-block;
        background: url(images/snow.png) no-repeat 10px 10px;
        padding: 0 0 0 40px;
    }

    .reigster_bg .item input[type="text"], input[type="password"] {
        height: 45px;
        line-height: 45px;
        margin: 0 0 0 60px;
        width: 330px;
        background: none;
        border: none;
        outline: none;
        color: #fff;
        font-size: 18px;
        vertical-align: middle;
    }

    .reigster_bg input[type="submit"] {
        color: #fff;
        font-size: 24px;
        background: #2da9ff;
        width: 356px;
        height: 58px;
        -moz-border-radius: 5px;
        -webkit-border-radius: 5px;
        border-radius: 5px;
        border: none;
        margin: 30px 0 0 188px;
    }

    .bg_login {
        width: 451px;
        height: 423px;
        background: url(images/bg_login.png) no-repeat;
        margin: 30px auto 0;
        overflow: hidden;
    }

    .bg_login form {
        margin: 129px 0 0 0;
    }

    .bg_login .item input[type="text"], input[type="password"] {
        height: 45px;
        line-height: 45px;
        margin: 0 0 0 60px;
        width: 330px;
        background: none;
        border: none;
        outline: none;
        color: #fff;
        font-size: 18px;
        vertical-align: middle;
    }

    .bg_login .item {
        margin: 0 0 9px 0;
    }

    .fotget_password {
        color: #e2e2e2;
        font-size: 18px;
        margin: 10px 0 0 306px;
        display: block;
    }

    .bg_login input[type="submit"] {
        color: #fff;
        font-size: 24px;
        background: #2da9ff;
        width: 356px;
        height: 58px;
        -moz-border-radius: 5px;
        -webkit-border-radius: 5px;
        border-radius: 5px;
        border: none;
        margin: 50px auto 0;
        display: block;
    }

    .login_footer {
        width: 445px;
        height: 67px;
        border-top: 2px solid #6f6c74;
        margin: 20px auto 100px;
        overflow: hidden;
    }

    .login_footer span {
        color: #dcdcdc;
        font-size: 18px;
        display: block;
        float: left;
        line-height: 40px;
        height: 40px;
        position: relative;
        margin-top: 5px;
    }

    .login_footer span em {
        display: inline-block;

    }

    .login_footer span a {
        position: absolute;
    }

    .login_footer span a.icon_sina {
        top: 5px;
        left: 115px;
    }

    .login_footer span a.icon_qq {
        top: 5px;
        left: 150px;
    }

    .free_reigster {
        float: right;
        color: #4bb5ff;
        font-size: 18px;
        display: block;
        margin: 10px 0 0 0;
    }
</style>
<div class="register_login_bg">
    <div class="rl_title"></div>
    <div class="reigster_bg form_bg">
        <?php echo \Utils\FormBuilderHelper::begin(); //注册表单JS ?>
        <form action="" method="post">
            <div class="item">
                <label>用户名</label>
                <input type="text" name="username" placeholder="请输入您的用户名"/>
            </div>
            <div class="item">
                <label>密码</label>
                <input type="password" placeholder="请输入您的密码"/>
            </div>
            <div class="item">
                <label>邮箱</label>
                <input name="email" type="text"   <?php if (!empty($email)) {
                    echo 'readonly';
                } ?> class="feedback-input" id="email" placeholder="您的邮箱地址" value="<?php echo $email ?>"/>
            </div>

            <input name="msg_id" type="hidden" value="<?php echo $msg_id ?>" "/>
            <?php echo \Utils\FormBuilderHelper::staticEnd('register',
                array( //                                'username' => array('required' => true),
//                                'email'    => array('required' => true),
//                                'password' => array('required' => true),
                ),
                URL::action('LoginController@registerUser'),
                'POST'
            );//注册表单JS
            ?>

            <input id="JS_Sub" type="submit" value="立即注册" class="submit_button"/>

        </form>
    </div>
</div>

</body>
</html>