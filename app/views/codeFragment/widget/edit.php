<?php echo $header; ?>
    <div class="">
        <?php echo \Utils\FormBuilderHelper::begin(); //注册表单JS ?>
        <form data-status="0" id="widget_form" class="form-horizontal" method="post"
        ">
        <fieldset>
            <legend>修改挂件</legend>
            <div class="form-group">
                <label for="table_name" class="control-label">表名称*:</label>

                <div class="controls">
                    <input name="table_name" value="<?php echo $widget->table_name; ?>" class="form-control" type="text"
                           placeholder="表名称" id="widget_table_name">
                </div>
            </div>
            <div class="form-group">
                <label for="name" class="control-label">挂件名称*:</label>

                <div class="controls">
                    <input class="form-control" name="name" value="<?php echo $widget->name; ?>" type="text"
                           placeholder="挂件名称" id="widget_name">
                </div>
            </div>
            <div class="form-group">
                <label for="action" class="control-label">执行动作:</label>

                <div class="controls">
                    <?php echo Form::select('action',
                        array('index' => 'index', 'update' => 'update', 'create' => 'create', 'edit' => 'edit', 'store' => 'store', 'page' => 'newPage'),
                        $widget->action,
                        array('class' => 'form-control')
                    )
                    ?>
                </div>
            </div>
            <div class="form-group">
                <label for="" class="control-label">是否启用:</label>

                <div class="controls">
                    <label class="radio inline">
                        <input class=""
                               type="radio" <?php echo (int)$widget->status === 1 ? 'checked="checked"' : ''; ?>
                               name="status" value="1" id=""/> 启用
                    </label>
                    <label class="radio inline">
                        <input class="" type="radio"
                               name="status" <?php echo (int)$widget->status === 0 ? 'checked="checked"' : ''; ?>
                               value="0" id=""/> 禁用
                    </label>
                </div>
            </div>
            <div class="form-group">
                <label for="code" class="control-label">代码:</label>

                <div class="controls">
                    <a title="全屏" href="javascript:void (0)"><i class="glyphicon glyphicon-fullscreen"></i></a>

                    <div id="code_edit_plugin"
                         class="code_edit_plugin"><?php echo htmlspecialchars($widget->code); ?></div>
                    <textarea class="hide" rows="3" name="code" id="widget_code"
                              style="width: 439px;"><?php echo htmlspecialchars($widget->code); ?></textarea>
                </div>
            </div>

            <div class="form-actions">
                <button class="btn btn-primary" id="JS_Sub" type="submit">更新挂件</button>
                <a class="btn btn-warning" onclick="history.back()">取消</a>
            </div>
        </fieldset>
        </form>
        <?php echo \Utils\FormBuilderHelper::staticEnd('widget_form',
            array( //表单规则
                'table_name' => array('required' => true),
                'name'       => array('required' => true),
                'action'     => array('required' => true),),
            URL::action('CodeFragmentController@updateWidget', array('widget' => $widget->id)),
            'PUT'
        );//注册表单JS
        ?>
    </div>
<?php echo $footer; ?>
<?php echo $code_common; ?>