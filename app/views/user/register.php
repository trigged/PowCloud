<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="utf-8">
    <title>PowCloud</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <meta name="description" content=""/>
    <meta http-equiv="Content-Type" content="text/html;charset=utf-8">

    <link href="<?php echo URL::asset('css/register_login.css'); ?>" rel="stylesheet">
    <script src="<?php echo URL::asset('/bower_components/jquery/dist/jquery.min.js'); ?>"></script>
</head>
<body>

<div class="register_login_bg">
    <div class="rl_title"></div>
    <div class="reigster_bg form_bg">
        <?php echo \Utils\FormBuilderHelper::begin(); //注册表单JS ?>
        <form method="post" id="register">
            <div class="item">
                <label>用户名</label>
                <input class="normal_input" type="text" name="username" placeholder="请输入您的用户名"/>
            </div>
            <div class="item">
                <label>密码</label>
                <input class="normal_input" type="password" placeholder="请输入您的密码" name="password"/>
            </div>
            <div class="item">
                <label>邮箱</label>
                <input class="normal_input" name="email" type="email"   <?php if (!empty($email)) {
                    echo 'readonly';
                } ?> class="feedback-input" id="email" placeholder="您的邮箱地址" value="<?php echo $email ?>"/>
            </div>

            <input name="msg_id" type="hidden" value="<?php echo $msg_id ?>" "/>
            <?php echo \Utils\FormBuilderHelper::staticEnd('register',
                array(),
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