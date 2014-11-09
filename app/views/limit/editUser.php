<?php echo $header; ?>
    <div class="mt20">
        <?php echo \Utils\FormBuilderHelper::begin(); //注册表单JS ?>
        <form data-status="0" id="user_form" class="form-horizontal">
            <div class="form-group">
                <label for="name" class="control-label col-sm-3">用户名*:</label>

                <div class="controls col-sm-3">
                    <input name="name" class="form-control" readonly value="<?php echo $user->name; ?>" type="text"
                           placeholder="用户名" id="name">
                </div>
            </div>



            <?php if (Limit::ROLE_SUPER === (int)$role): ?>
                <div class="form-group">
                    <label for="sa" class="control-label col-sm-3" style="margin-top:12px;">启用超级管理员权限:</label>

                    <div class="controls inline col-sm-8">
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
            <div class="form-group">
                <label for="groupName" class="control-label col-sm-3">选择用户组:</label>

                <div class="controls col-sm-8" style="margin:10px 0 0 35px;">
                    <?php echo Form::select('group_id', array('' => '') + Group::lists('groupName', 'id'), $group_id) ?>
                </div>
            </div>

            <div style="height:30px;"></div>
            <div class="form-actions">
                <button id="JS_Sub" class="btn btn-primary">更新</button>
                <a href="javascript:void (0)" class="btn btn-warning" onclick="history.back();">取消</a>
            </div>
        </form>
        <?php echo \Utils\FormBuilderHelper::staticEnd('user_form',
            array(),
            URL::action('LimitController@updateUser', array('limit' => $user->id)),
            'PUT',
            ''
        );
        ?>
    </div>
<?php echo $footer; ?>