<?php echo $header; ?>
    <div class="">
        <?php echo \Utils\FormBuilderHelper::begin(); //注册表单JS ?>
        <form data-status="0" id="user_form" class="form-horizontal" onsubmit="return check_form(this)">
            <div class="control-group">
                <label for="name" class="control-label">用户名*:</label>

                <div class="controls">
                    <input name="name" class="form-control" readonly value="<?php echo $user->name; ?>" type="text"
                           placeholder="用户名" id="name">
                </div>
            </div>



            <?php if (Limit::ROLE_SUPER === (int)$role): ?>
                <div class="control-group">
                    <label for="sa" class="control-label">启用超级管理员权限:</label>

                    <div class="controls">
                        <label class="radio inline">
                            <input class="" type="radio" name="sa"
                                   value="1"  <?php if ((int)$userRole === Limit::ROLE_SUPER) echo 'checked="checked"'; ?>
                                   id=""/> 开启
                        </label>
                        <label class="radio inline">
                            <input class="" type="radio" name="sa" id=""
                                   value="0"  <?php if ((int)$userRole !== Limit::ROLE_SUPER) echo 'checked="checked"'; ?> />
                            关闭
                        </label>
                    </div>
                </div>
            <?php endif; ?>

            <div class="form-actions">
                <button id="JS_Sub" class="btn btn-primary">更新</button>
                <a href="javascript:void (0)" class="btn" onclick="history.back();">取消</a>
            </div>

            <div class="control-group">
                <label for="groupName" class="control-label">选择用户组:</label>

                <div class="controls">
                    <?php echo Form::select('group_id', array('' => '') + Group::lists('groupName', 'id'), $group_id) ?>
                </div>
            </div>

        </form>
    </div>
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
                    label.html('<i class="icon-ok"></i>');
                }
            });
            var validate = $("#user_form").validate({
                rules: {
                    name: "required",
                    group_id: "required",
                    email: "required"
                }
            });
        });

        function check_form(form) {
            $.ajax({
                url: '<?php echo URL::action('LimitController@updateUser',array('limit'=>$user->id));?>',
                data: $(form).serialize(),
                type: 'PUT',
                beforeSend: function () {
                    $('#JS_Sub').attr('disabled', true);
                },
                success: function (re) {
                    re = $.parseJSON(re);
                    if (re.status == 'fail') {
                        alert(re.message);
                        $('#JS_Sub').attr('disabled', false);
                        return false;
                    }
                    alert('更新字段成功');
                    location.href = re.successRedirect;
                }
            });

            return false;
        }
    </script>


<?php echo $footer; ?>