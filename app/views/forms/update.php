<?php echo $header; ?>
<div class="">
    <form class="form-horizontal child_form" method="post" onsubmit="return check_form(this)">
        <fieldset>
            <legend>修改字段:<?php echo $field->field ?></legend>
            <div class="form-group">
                <label for="label" class="control-label col-sm-2">标签*:</label>

                <div class="controls col-sm-6">
                    <input name="label" class="form-control" value="<?php echo $field->label; ?>" type="text"
                           placeholder="标签" id="label">
                </div>
            </div>

            <div class="form-group">
                <label for="type" class="control-label col-sm-2">类型*:</label>

                <div class="controls col-sm-6">
                    <?php echo Form::select('type', Config::get('params.formField'), $field->type, array('class' => 'form-control JFieldType')); ?>
                </div>
            </div>
            <div class="form-group">
                <label for="default_value" class="control-label col-sm-2">默认值:</label>

                <div class="controls JDefaultValue col-sm-6">
                    <?php if ($field->type == 'ajaxInput' && $field->default_value): ?>
                        <table class="table">
                            <thead>
                            <tr>
                                <th>绑定对象</th>
                                <th>数组源或链接</th>
                                <th>字段映射关系(本地字段,远程字段)</th>
                            </tr>
                            </thead>
                            <?php $mapCount = count($field->default_value['map']); ?>
                            <tbody id="ajaxInput">
                            <?php $tableIndex = 1;
                            foreach ($field->default_value['map'] as $dataSource => $map): ?>
                                <tr class="<?php if ($tableIndex == 1) echo 'JDisableDelete'; ?>">
                                    <?php if ($tableIndex == 1): ?>
                                        <td id="ajaxInputRowspan" rowspan="<?php echo $mapCount; ?>">
                                            <input type="text" name="default_value[target]"
                                                   value="<?php echo $field->default_value['target']; ?>"/>
                                            &nbsp;&nbsp;<a href="javascript:;"
                                                           data-tableIndex="<?php echo $mapCount + 1; ?>"
                                                           class="JAjaxInput" data-type="tr" data-target="ajaxInput"
                                                           style="cursor:pointer;"><i
                                                    class="glyphicon glyphicon-plus"></i></a>
                                        </td>
                                    <?php endif; ?>
                                    <td>
                                        <input type="text" name="default_value[data][<?php echo $tableIndex; ?>][data]"
                                               value="<?php echo $dataSource; ?>"/>
                                        &nbsp;&nbsp;<a href="javascript:;" data-tableIndex="<?php echo $tableIndex; ?>"
                                                       data-mapIndex="<?php echo count($map) + 1; ?>" class="JAjaxInput"
                                                       data-type="td" data-target="ajaxInput" style="cursor:pointer;"><i
                                                class="glyphicon glyphicon-plus"></i></a>
                                        <a href="javascript:;" class="JAjaxInput" data-type="delete-tr"
                                           style="cursor:pointer;"><i class="glyphicon glyphicon-remove"></i></a>
                                    </td>
                                    <td>
                                        <?php $mapIndex = 1;
                                        foreach ($map as $m): ?>
                                            <div style="margin-bottom: 5px;">
                                                <?php list($localField, $remoteField) = explode(':', $m); ?>
                                                <input type="text"
                                                       name="default_value[data][<?php echo $tableIndex; ?>][map][<?php echo $mapIndex; ?>][localField]"
                                                       class="form-control" value="<?php echo $localField; ?>"/>&nbsp;&nbsp;&nbsp;
                                                <input type="text"
                                                       name="default_value[data][<?php echo $tableIndex; ?>][map][<?php echo $mapIndex; ?>][remoteField]"
                                                       class="form-control" value="<?php echo $remoteField; ?>"/>
                                                <a href="javascript:;" class="JAjaxInput" data-type="delete-td"
                                                   style="cursor:pointer;"><i
                                                        class="glyphicon glyphicon-remove"></i></a>
                                            </div>
                                            <?php $mapIndex++;endforeach; ?>
                                    </td>
                                </tr>
                                <?php $tableIndex++; endforeach; ?>
                            </tbody>
                        </table>
                    <?php elseif ($field->type == 'ajaxInput'): ?>
                        <table class="table">
                            <thead>
                            <tr>
                                <th>绑定对象</th>
                                <th>数组源或链接</th>
                                <th>字段映射关系(本地字段,远程字段)</th>
                            </tr>
                            </thead>
                            <tbody id="ajaxInput">
                            <tr class="JDisableDelete">
                                <td rowspan="1" id="ajaxInputRowspan">
                                    <input type="text" value="" name="default_value[target]">
                                    &nbsp;&nbsp;<a style="cursor:pointer;" data-target="ajaxInput" data-type="tr"
                                                   class="JAjaxInput" data-tableindex="2" href="javascript:;"><i
                                            class="glyphicon glyphicon-plus"></i></a>
                                </td>
                                <td>
                                    <input type="text" value="" name="default_value[data][1][data]">
                                    &nbsp;&nbsp;<a style="cursor:pointer;" data-target="ajaxInput" data-type="td"
                                                   class="JAjaxInput" data-mapindex="2" data-tableindex="1"
                                                   href="javascript:;"><i class="glyphicon glyphicon-plus"></i></a>
                                    <a style="cursor:pointer;" data-type="delete-tr" class="JAjaxInput"
                                       href="javascript:;"><i class="glyphicon glyphicon-remove"></i></a>
                                </td>
                                <td>
                                    <div style="margin-bottom: 5px;">
                                        <input type="text" value="" class="form-control"
                                               name="default_value[data][1][map][1][localField]">&nbsp;&nbsp;&nbsp;
                                        <input type="text" value="" class="form-control"
                                               name="default_value[data][1][map][1][remoteField]">
                                        <a style="cursor:pointer;" data-type="delete-td" class="JAjaxInput"
                                           href="javascript:;"><i class="glyphicon glyphicon-remove"></i></a>
                                    </div>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <input name="default_value" class="form-control" type="text"
                               value="<?php echo htmlspecialchars($field->default_value, ENT_QUOTES); ?>"
                               placeholder="默认值" id="default_value">
                    <?php endif; ?>
                </div>
            </div>

            <div class="form-group">
                <label for="rules" class="control-label col-sm-2">验证规则:</label>

                <div class="controls col-sm-6">
                    <?php echo Form::textarea('rules', $field->rules, array('class' => 'form-control')) ?>
                </div>
            </div>
            <div class="form-group">
                <label for="rank" class="control-label col-sm-2">排序:</label>

                <div class="controls col-sm-6">
                    <input name="rank" class="form-control" type="text" value="<?php echo $field->rank ?>"
                           placeholder="默认值" id="rank">
                </div>
            </div>
            <div class="form-group">
                <label for="isVisible" class="control-label col-sm-2">列表是否可见:</label>

                <div class="controls col-sm-6">
                    <?php echo Form::select('isVisible', array(1 => '可见', 0 => '不可见'), (int)$field->isVisible, array('class' => 'form-control')); ?>
                </div>
            </div>
            <div class="form-group">
                <label for="isEditable" class="control-label col-sm-2">是否可输入:</label>

                <div class="controls col-sm-6">
                    <?php echo Form::select('isEditable', array(1 => '可输入', 0 => '不可输入'), (int)$field->isEditable, array('class' => 'form-control')); ?>
                </div>
            </div>

            <div class="form-group">
                <label for="isEditable" class="control-label col-sm-2">角色可见:</label>

                <div class="controls col-sm-6">
                    <?php echo Form::select('visibleByGroup', array('0' => '无限制') + Group::getGroups(), (int)$field->visibleByGroup, array('class' => 'form-control')); ?>
                </div>
            </div>

            <div class="form-actions">
                <button id="JS_Sub" class="btn btn-primary" type="submit">修改</button>
                <a href="javascript:void (0)" class="btn  btn-warning" onclick="history.back();">取消</a>
            </div>
        </fieldset>
    </form>
</div>
<?php echo $footer; ?>
<?php echo $ajaxInput; ?>
<script>
    function check_form(form) {
        $.ajax({
            url: '<?php echo URL::action('FormsController@update',array('form'=>$field->id));?>',
            data: $(form).serialize(),
            type: 'PUT',
            beforeSend: function () {
                $('#JS_Sub').attr('disabled', true);
            },
            success: function (re) {
                re = $.parseJSON(re);
                if (re.status) {
                    var errors = re.data;
                    if (!errors) {
                        errors = re.message
                    }
                    if (errors) {
                        try {
                            errors = JSON.parse(this.responseText);
                            console.log("json")
                        }
                        catch (e) {
                            console.log("not json")
                        }
                        $('#alerts').on('closed.bs.alert', function () {
                            if (re.redirect) {
                                location.href = re.redirect;
                            }

                        })
                        if ($('#alerts').length > 0) {
                            setTimeout(function () {
                                if (re.redirect) {
                                    location.href = re.redirect;
                                }
                            }, 3000);
                            if (re.status == 1) {
                                $("#alerts").attr('class', "alert alert-dismissible  alert-success");
                            }
                            else {
                                $("#alerts").attr('class', "alert alert-dismissible  alert-danger");
                            }
                            $("#alert_title").html(re.message);
                            $("#alert_content").html(re.data);
                            $("#alerts").fadeTo(4000, 500).slideUp(500, function () {
                                $("#alerts").alert();
                            });
                        }
                        else {
                            alert(errors);
                        }
                    }
                    $('#JS_Sub').attr('disabled', false);
                }
                if (re.redirect) {
                    if ($('#alerts').length <= 0) {
                        if (re.redirect) {
                            location.href = re.redirect;
                        }
                    }

                }
            }
        });

        return false;
    }
</script>