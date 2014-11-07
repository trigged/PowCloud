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
            <div class="form-group">
                <label for="name" class="col-sm-2 control-label">请输入邮箱 :</label>

                <div class="col-sm-6">
                    <input name="email" class="form-control" type="text" placeholder="请输入邮箱" id="filed">
                </div>
            </div>

            <div class="form-actions" style="margin-left:10px;">
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
    URL::action('UserMessageController@forget'),
    'POST'
);//注册表单JS
?>

<?php echo $footer ?>