<?php echo $header ?>

    <div class="note note-success">
        <h4 class="block">密码找回</h4>

        <p>
            目前我们仅支持邮箱找回 ,如果少侠忘记了此信息,可与我们联系
        </p>
    </div>
<?php echo \Utils\FormBuilderHelper::begin(); //注册表单JS ?>
    <form data-status="0" id="forget" class="form-horizontal" method="post">
        <fieldset>
            <?php if (!$action && $action !== UserMessage::ACTION_FORGET_PASSWORD): ?>
                <div class="form-group">
                    <label for="name" class="control-label">旧密码 :</label>

                    <div class="controls">
                        <input name="old_pwd" class="form-control" type="text" placeholder="请输入邮箱" id="filed">
                    </div>
                </div>
            <?php endif ?>
            <div class="form-group">
                <label for="name" class="control-label">新密码 :</label>

                <div class="controls">
                    <input name="new_pwd" class="form-control" type="text" placeholder="请输入邮箱" id="filed">
                </div>
            </div>


            <div class="form-group">
                <label for="name" class="control-label">新密码确认 :</label>

                <div class="controls">
                    <input name="new_pwd" class="form-control" type="text" placeholder="请输入邮箱" id="filed">
                </div>
            </div>

            <div class="form-actions">
                <button class="btn btn-primary" id="JS_Sub" data-loading-text="Loading..." type="submit">确定</button>
                <a class="btn  btn-warning" onclick="history.back()">取消</a>
            </div>
        </fieldset>
    </form>
    <script>

    </script>
<?php echo \Utils\FormBuilderHelper::staticEnd('forget',
    array( //表单规则
        'filed' => array('required' => true),
    ),
    URL::action('UserMessageController@resetPassword', array('msg_id' => $msg_id)),
    'POST'
);//注册表单JS
?>

<?php echo $footer ?>