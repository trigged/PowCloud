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
    <script src="<?php echo URL::asset('/bower_components/jquery/dist/jquery.min.js'); ?>"></script>
</head>
<body>

<div class="register_login_bg">
    <div class="rl_title"></div>
    <div class="bg_login form_bg">
        <?php echo \Utils\FormBuilderHelper::begin(); //注册表单JS ?>
        <form method="post" id="login">
            <div class="item">
                <i class="icon_user"></i>
                <input class="normal_input" type="text" name="username" placeholder="请输入账号"/>
            </div>
            <div class="item">
                <i class="icon_password"></i>
                <input class="normal_input" type="password" name="password" placeholder="请输入密码"/>
            </div>
            <a href="<?php echo URL::action('UserMessageController@viewForget') ?>" class="fotget_password">忘记密码？</a>
            <?php echo \Utils\FormBuilderHelper::staticEnd('login',
                array(),
                URL::action('LoginController@loginStore'),
                'POST'
            );//注册表单JS
            ?>
            <div class="input_alert">请输入正确的密码</div>
            <input id="JS_Sub" type="submit" data-loading-text="登录中..." value="立即登录" class="submit_button"/>
        </form>
    </div>
    <div class="login_footer clearfix">
        <!--        <span><em>其他登录方式</em><a href="#" class="icon_sina"><img src="images/icon_sina.png"></a><a href="#"-->
        <!--                                                                                                  class="icon_qq"><img-->
        <!--                    src="images/icon_qq.png"></a></span>-->
        <a href="<?php echo URL::action('LoginController@register') ?>" class="free_reigster">免费注册</a>
    </div>
</div>
<script>
    $(function () {


        function check_username() {
            var user_name = $('input[name="username"]').val();
            if (user_name.length <= 0) {
                $(".input_alert").show().html("请输入正确的用户名");
                return false
            }
            $(".input_alert").hide();
            return true;

        }

        function check_password() {
            var pwd = $('input[name="password"]').val();
            if (pwd.length < 6) {
                $(".input_alert").show().html("请输入正确的密码 长度必须大于6为");
                return false
            }
            $(".input_alert").hide();
            return true;

        }

        $('.submit_button').click(function () {

            if (check_username() && check_password()) {
                $('form').submit();
            } else {

                $(".input_alert").show().html("请输入正确的用户名和密码");
                return false;
            }
        });

    });
</script>
</body>
</html>