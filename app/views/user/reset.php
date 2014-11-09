<?php echo $header ?>


    <h4>修改密码</h4>
<?php echo \Utils\FormBuilderHelper::begin(); //注册表单JS ?>
    <form data-status="0" id="forget" class="form-horizontal" method="post">
        <fieldset>
            <?php if (!$action && $action !== UserMessage::ACTION_FORGET_PASSWORD): ?>
                <div class="form-group">
                    <label for="name" class="control-label col-sm-2">旧密码 :</label>

                    <div class="controls col-sm-10">
                        <input name="old_pwd" class="form-control" type="password" placeholder="旧密码" id="filed">
                    </div>
                </div>
            <?php endif ?>
            <div class="form-group">
                <label for="name" class="control-label col-sm-2">新密码 :</label>

                <div class="controls col-sm-10">
                    <input name="new_pwd" class="form-control" type="password" placeholder="新密码" id="filed">
                </div>
            </div>


            <div class="form-group">
                <label for="name" class="control-label col-sm-2">新密码确认 :</label>

                <div class="controls col-sm-10">
                    <input name="new_pwd_check" class="form-control" type="password" placeholder="新密码确认" id="filed">
                </div>
            </div>

            <div class="form-actions" style="margin-left:10px;">
                <button class="btn btn-primary" id="JS_Sub" data-loading-text="Loading..." type="submit">确定</button>
                <a class="btn  btn-warning" onclick="history.back()">取消</a>
            </div>
        </fieldset>
    </form>

<?php echo \Utils\FormBuilderHelper::staticEnd('forget',
    array( //表单规则
        'old_pwd'       => array('required' => true),
        'new_pwd'       => array('required' => true),
        'new_pwd_check' => array('required' => true),
    ),
    URL::action('UserMessageController@resetPassword', array('msg_id' => $msg_id)),
    'POST'
);//注册表单JS
?>

<?php if ($status && $status == User::DISABLE ): ?>
    <div style="height:20px; "></div>
<?php echo \Utils\FormBuilderHelper::begin(); //注册表单JS ?>
<form data-status="0" id="resend" class="form-horizontal" method="post">
    <h4>重发激活邮件</h4>
    <div class="col-sm-4">
        <button type="submit" id="JS_Sub" data-loading-text="请求中..." class="btn btn-primary" autocomplete="off" >重发激活邮件</button>
    </div>

    <?php echo \Utils\FormBuilderHelper::staticEnd('resend',
        array(),
        URL::action('UserMessageController@reSendActiveMail'),
        'POST'
    );
?>
<?php endif; ?>


<?php echo $footer ?>