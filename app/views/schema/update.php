<?php echo $header; ?>
<div class="row">
    <div class="span7">
        <?php echo \Utils\FormBuilderHelper::begin(); //注册表单JS ?>
        <form id="schema_form" class="form-horizontal" method="post">
            <fieldset>
                <legend>修改表:<?php echo $schema->table_name; ?></legend>
                <div class="control-group">
                    <label for="table_alias" class="control-label">表别名*:</label>

                    <div class="controls">
                        <input name="table_alias" value="<?php echo $schema->table_alias; ?>" class="input-medium"
                               type="text" placeholder="表别名" id="table_alias">
                    </div>
                </div>
                <div class="control-group">
                    <label for="group_name" class="control-label">组名:</label>

                    <div class="controls">
                        <input name="group_name" value="<?php echo $schema->group_name; ?>" class="input-medium"
                               type="text" placeholder="组名" id="group_name">
                    </div>
                </div>
                <div class="control-group">
                    <label for="path" class="control-label">映射路径*:</label>

                    <div class="controls">
                        <?php echo Form::select('path_id', $pathTreeListOptions, $schema->path_id, array('id' => 'path_id')) ?>
                    </div>
                    <input type="hidden" id="path_name" name="path_name" value="">
                </div>

                <div class="control-group">
                    <label for="name" class="control-label">属性*:</label>

                    <div class="controls">
                        <table class="table">
                            <thead>
                            <tr>
                                <th>字段名</th>
                                <th>属性</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($schema->property as $propertyName => $propertyValue): ?>
                                <tr class="propertyInput">
                                    <td>
                                        <span><input class="input-medium " placeholder="<?php echo $propertyName ?>"
                                                     value="<?php echo $propertyName ?>"
                                                     name="<?php echo $propertyName ?>"/></span>
                                    </td>
                                    <td>
                                    <span class="input-medium uneditable-input"><?php
                                        echo implode(' ', array_except($propertyValue, array('default')));
                                        echo !empty($propertyValue['default']) ? '|' . $propertyValue['default'] : '';
                                        ?>
                                    </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                            <tfoot>
                            </tfoot>
                        </table>
                    </div>
                </div>
                <div id="JRestCtrl">
                    <div class="control-group">
                        <label for="" class="control-label">restful:</label>

                        <div class="controls">
                            <label class="radio inline">
                                <input class="" onclick="$('#JRestful').removeClass('hide');" type="radio"
                                       name="restful" <?php if ((int)$schema->restful === 1) echo 'checked="checked"'; ?>
                                       value="1" id=""/> 开启
                            </label>
                            <label class="radio inline">
                                <input class="" onclick="$('#JRestful').addClass('hide');" type="radio"
                                       name="restful" <?php if (!$schema->restful && (int)$schema->restful === 0) echo 'checked="checked"'; ?>
                                       value="0" id=""/> 关闭
                            </label>
                        </div>
                    </div>
                    <div id="JRestful" class="control-group">
                        <label for="" class="control-label">可执行操作:</label>

                        <div class="controls">
                            <label class="checkbox inline">
                                <input type="checkbox" name="index"
                                       id="" <?php if ((int)$schema->index === 1) echo 'checked="checked"'; ?>
                                       value="1"> 读取
                            </label>
                            <label class="checkbox inline">
                                <input type="checkbox" name="update"
                                       id="" <?php if ((int)$schema->update === 1) echo 'checked="checked"'; ?>
                                       value="1"> 编辑
                            </label>
                            <label class="checkbox inline">
                                <input type="checkbox" name="create"
                                       id="" <?php if ((int)$schema->create === 1) echo 'checked="checked"'; ?>
                                       value="1"> 创建
                            </label>
                            <label class="checkbox inline">
                                <input type="checkbox"
                                       name="delete" <?php if ((int)$schema->delete === 1) echo 'checked="checked"'; ?>
                                       id="" value="1"> 删除
                            </label>
                        </div>
                    </div>
                </div>
                <div class="form-actions">
                    <button class="btn btn-primary" id="JS_Sub" type="submit">更新表</button>
                    <a href="javascript:void (0)" class="btn" onclick="history.back();">取消</a>
                </div>
            </fieldset>
        </form>
        <?php echo \Utils\FormBuilderHelper::staticEnd('schema_form',
            array( //表单规则
                'table_alias' => array('required' => true),
            ),
            URL::action('SchemaBuilderController@update', array('table' => $schema->id)),
            'PUT'
        );//注册表单JS
        ?>
    </div>
    <div class="span5">
        <dl>
            <dt>属性格式</dt>
            <dd>类型名 长度</dd>
            <dt>示例</dt>
            <dd>integer 11</dd>
            <dd>string 100</dd>
        </dl>
    </div>
</div>
<script>
    $(function () {
        $('#path_id').change(function () {
            var val = $(this).val();
            val == -1 ? $('#JRestCtrl').addClass('hide') : $('#JRestCtrl').removeClass('hide');
            $('#path_name').val($(this).find("option:selected").text());
        })

        if ($('#path_id').val() == -1) {
            $('#JRestCtrl').addClass('hide');
        }

    });
    function addProperty() {
        var index = $(".propertyInput").length;
        var tpl = '<tr><td><input class="input-mini propertyInput" type="text" name="property[' + index + '][name]" value="" /></td><td><input class="input-xlarge" type="text" name="property[' + index + '][attributes]" value="" /></td></tr>'
        $('table tbody').append(tpl);
    }
</script>
<?php echo $footer; ?>
