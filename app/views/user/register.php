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
                <input class="normal_input" name="email" type="text"   <?php if (!empty($email)) {
                    echo 'readonly';
                } ?> class="feedback-input" id="email" placeholder="您的邮箱地址" value="<?php echo $email ?>"/>
            </div>

            <div class="input_alert">请输入正确的密码</div>


            <input name="msg_id" type="hidden" value="<?php echo $msg_id ?>" "/>
            <?php echo \Utils\FormBuilderHelper::staticEnd('register',
                array(),
                URL::action('LoginController@registerUser'),
                'POST'
            );//注册表单JS
            ?>
            <input id="JS_Sub" type="submit" value="立即注册" data-loading-text="正在注册..." class="submit_button"/>
        </form>
    </div>
</div>
<!--<script type="text/javascript">-->
<!--    $(function () {-->
<!--        var state = false;-->
<!--        $('#txtEmail').focus(function () {-->
<!--            if (state == false) {-->
<!--                $(this).val('');-->
<!--            }-->
<!--        })-->
<!--        $('#txtEmail').blur(function () {-->
<!--            if ($(this).val() == '') {-->
<!--                $('#spinfo').text('邮箱不能为空');-->
<!--                $(this).focus();-->
<!--            }-->
<!--            else {-->
<!--                if (/^\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*$/.test($(this).val()) == false) {-->
<!--                    $('#spinfo').text('邮箱格式不正确，请重新输入');-->
<!--                    $(this).focus();-->
<!--                }-->
<!--                else {-->
<!--                    $('#spinfo').text('');-->
<!--                    $('#spinfo').append('<img src=images/onSuccess.gif/>');-->
<!--                    state = true;-->
<!--                }-->
<!--            }-->
<!--        })-->
<!---->
<!---->
<!--    })-->
<!--</script>-->
<script>
    $(function () {

        var ok1 = false;
        var ok2 = false;
        var ok3 = false;
        var ok4 = false;
        // 验证用户名
        $('input[name="username"]').blur(function () {
            if ($(this).val().length >= 3 && $(this).val().length <= 12 && $(this).val() != '') {
                $(".input_alert").hide();
                ok1 = true;
            } else {
                $(".input_alert").show().html("请输入用户名");
            }

        });

        //验证密码
        $('input[name="password"]').focus(function () {
            $(".input_alert").show().html("请输入密码");
        }).blur(function () {
                if ($(this).val().length >= 6 && $(this).val().length <= 20 && $(this).val() != '') {
                    $(".input_alert").hide();
                    ok2 = true;
                } else {
                    $(".input_alert").show().html("密码应该为6-20位之");
                }

            });

        //验证确认密码
//        $('input[name="repass"]').focus(function(){
//            $(this).next().text('输入的确认密码要和上面的密码一致,规则也要相同').removeClass('state1').addClass('state2');
//        }).blur(function(){
//                if($(this).val().length >= 6 && $(this).val().length <=20 && $(this).val()!='' && $(this).val() == $('input[name="password"]').val()){
//                    $(this).next().text('输入成功').removeClass('state1').addClass('state4');
//                    ok3=true;
//                }else{
//                    $(this).next().text('输入的确认密码要和上面的密码一致,规则也要相同').removeClass('state1').addClass('state3');
//                }
//
//            });

        //验证邮箱
        $('input[name="email"]').focus(function () {
            $(".input_alert").show().html("请输入正确的EMAIL格式");
        }).blur(function () {
                if ($(this).val().search(/\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*/) == -1) {
                    $(".input_alert").show().html("请输入正确的EMAIL格式");
                } else {
                    $(".input_alert").hide()
                    ok4 = true;
                }

            });

        //提交按钮,所有验证通过方可提交

        $('.submit_button').click(function () {
            $('form').submit();
//            if(ok1 && ok2 && ok4){
//            }else{
//                $(".input_alert").show().html("请完整填写信息");
//                return false;
//            }
        });

    });
</script>
</body>
</html>