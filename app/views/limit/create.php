<?php echo $header; ?>
<div class="">
    <?php echo \Utils\FormBuilderHelper::begin(); //注册表单JS ?>
    <form data-status="0" id="group_form" class="form-horizontal" method="post"
          action="<?php echo URL::action('LimitController@store'); ?>" onsubmit="return check_form(this)">
        <div class="form-group">
            <label for="GroupName" class="control-label col-md-2">用户组名*:</label>

            <div class="controls col-md-5">
            <input name="group[groupName]" class="form-control" value="" type="text" placeholder="用户组名称"
                       id="groupName">
            </div>
        </div>
        <?php foreach ($models as $model): ?>
            <div class="form-group">
                <label class="control-label col-md-2"
                       for="<?php echo $model->table_name ?>"><?php echo $model->table_alias; ?></label>

                <div class="controls col-md-5">
                    <?php echo Form::radio('limit[' . $model->table_name . ']', 1, true, array('class' => 'radio')); ?>
                    <span class="">无权限</span>
                    <?php echo Form::radio('limit[' . $model->table_name . ']', 2, '', array('class' => 'radio')); ?>
                    <span>只看权限</span>
                    <?php echo Form::radio('limit[' . $model->table_name . ']', 3, '', array('class' => 'radio')); ?>
                    <span>修改权限</span>
                </div>
            </div>
        <?php endforeach; ?>
        <div class="form-actions">
            <button class="btn btn-primary" id="">创建</button>
            <a href="javascript:void (0)" class="btn btn-warning" onclick="history.back();">取消</a>
        </div>
    </form>

    <script>
        $().ready(function () {
            $.validator.setDefaults({
                errorClass: 'error help-inline',
                errorElement: 'span',
                errorPlacement: function (error, element) {
                    element.parent().parent().addClass('error');
                    error.appendTo(element.parent());
                },
                success: function (label) {
                    label.parent().parent().removeClass('error').addClass('success');
                    label.html('<i class="glyphicon glyphicon-ok"></i>');
                }
            });
            var validate = $("#group_form").validate({
                rules: {"group[groupName]": "required"}
            });
        });
    </script>
</div>


<?php echo $footer; ?>

