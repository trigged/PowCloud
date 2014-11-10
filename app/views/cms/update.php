<?php echo $header; ?>
<?php echo \Utils\FormBuilderHelper::begin($table->table_name); ?>
    <form id="cms_form" class="form-horizontal" onsubmit="">
        <fieldset class="">

            <legend>更新数据:#<?php echo $tableData->id ?></legend>
            <?php foreach ($forms as $form): ?>
            <?php if ((int)$form->visibleByGroup !== 0 && (int)$form->visibleByGroup !== (int)Auth::user()->group_id) {
                echo \Utils\FormBuilderHelper::hidden($form, $tableData->{$form->field});
                continue;
            }?>
            <?php if ($form->type == 'formTip') {
                echo \Utils\FormBuilderHelper::formTip($form);
                continue;
            } ?>
            <?php if ($form->type == 'timingState') { ?>
            <div class="form-group timing-radio">
                <label for="name" class="control-label" style="display:none"><?php echo $form->label ?>:</label>

                <div class="controls pow_gap">
                    <?php
                    echo \Utils\FormBuilderHelper::timingState($form, $tableData->timing_state, $tableData);
                    echo "</div>";
                    echo "</div>";
                    continue;
                    } ?>
                    <?php if ($form->type == 'image' && $form->default_value): ?>
                        <?php $del_val_array = array_filter(explode(',', $form->default_value)) ?>
                        <?php foreach ($del_val_array as $key => $val): ?>
                            <div class="form-group">
                                <label for="name" class="control-label col-sm-3"><?php echo $val ?>:</label>

                                <div class="controls col-md-5">
                                    <?php
                                    \Utils\FormBuilderHelper::registerValidateRules($form->field, $form->rules); //注册验证规则 以便JS可以验证
                                    $namespace = $table->table_name ? $table->table_name . '[' . $form->field . ']' : $form->field;
                                    $class = 'form-control';
                                    $input = '<input type="text" name="' . $namespace . '[]" placeholder="单击上传" value="' . (isset($tableData->{$form->field}[$key]) ? $tableData->{$form->field}[$key] : '') . '"  class="' . $class . ' image-uploader"  />';
                                    echo $input;
                                    ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php elseif ($form->field === 'timing_time'): ?>
                        <div class="form-group">
                            <label for="name" class="control-label col-sm-3"><?php echo $form->label ?>:</label>

                            <div class="controls col-md-6 form-inline">
                                <?php
                                \Utils\FormBuilderHelper::registerValidateRules($form->field, $form->rules); //注册验证规则 以便JS可以验证
                                $time = '';
                                if ($tableData->hasTiming()) {
                                    $time = $tableData->{$form->field};
                                }
                                echo call_user_func_array(array('\Utils\FormBuilderHelper', $form->type), array($form, $time));
                                ?>
                            </div>
                        </div>
                    <?php elseif (!in_array($form->field, $hide) && $form->type !== 'formTip'): ?>
                        <div class="form-group">
                            <label for="name" class="control-label col-sm-3"><?php echo $form->label ?>:</label>

                            <div class="controls col-md-5">
                                <?php
                                \Utils\FormBuilderHelper::registerValidateRules($form->field, $form->rules); //注册验证规则 以便JS可以验证
                                echo call_user_func_array(array('\Utils\FormBuilderHelper', $form->type), array($form, $tableData->{$form->field}));
                                ?>
                            </div>
                        </div>
                    <?php endif; ?>
                    <?php endforeach; ?>


                    <?php if (!empty($data_link_info)) {
                        echo '<div class="alert alert-info">此数据设置了级联更新,数据变化会导致从属数据同步,会影响的数据如下: </div>';
                        echo '<ul  class="nav nav-pills">';
                        foreach ($data_link_info as $info) {
                            $url = URL::action('CmsController@edit', array('cms' => $info['data_id'], 'table' => $info['table_id']));
                            echo sprintf('<input type="hidden" name="link_items[%s][%s]" value="%s" />', $info['table_name'], $info['data_id'], htmlspecialchars($info['options']));
                            echo sprintf('<li><a href="%s" target="_blank"  > %s</a></li>', $url, $info['table_alias'] . '.' . $info['data_id']);
                        }
                        echo '</ul>';
                    }?>
        </fieldset>
        <?php if ($children_relations): ?>
            <?php foreach ($children_relations as $children_relation): ?>
                <fieldset>
                    <legend><?php echo $children_relation['table']->table_alias; ?></legend>
                    <table id="table-<?php echo $children_relation['table']->table_name; ?>"
                           data-count="<?php echo count($children_relation['data']); ?>"
                           class="table  dynamic-child-table">
                        <thead>
                        <tr>
                            <td>#序号</td>
                            <?php foreach ($children_relation['forms'] as $form): ?>
                                <?php if ((int)$form->visibleByGroup !== 0 && (int)$form->visibleByGroup !== (int)Auth::user()->group_id) continue; ?>
                                <td><?php echo $form->label ?></td>
                            <?php endforeach; ?>
                            <td>操作</td>
                        </tr>
                        </thead>
                        <tbody>
                        <?php $index = 1;
                        foreach ($children_relation['data'] as $fid => $data): \Utils\DataColumnHelper::registerFormNamespace($children_relation['table']->table_name . '[' . $index . ']') ?>
                            <tr class="data">
                                <td><span class="data-index"><?php echo $index; ?></span><input type="hidden"
                                                                                                name="<?php echo $children_relation['table']->table_name ?>[<?php echo $index ?>][id]"
                                                                                                value="<?php echo $fid; ?>"/>
                                </td>
                                <?php foreach ($children_relation['forms'] as $form): ?>
                                    <?php if ((int)$form->visibleByGroup !== 0 && (int)$form->visibleByGroup !== (int)Auth::user()->group_id) {
                                        echo \Utils\FormBuilderHelper::hidden($form, $data ? $data->{$form->field} : '');
                                        continue;
                                    } ?>
                                    <td>
                                        <?php
                                        echo call_user_func_array(array('\Utils\DataColumnHelper', $form->type), array('form', $form, $data ? $data->{$form->field} : ''));
                                        ?>
                                    </td>
                                <?php endforeach;
                                $index++; ?>
                                <td>
                                    <a href="javascript:void(0);"
                                       data-table="<?php echo $children_relation['table']->table_name; ?>" title="上升"
                                       class="tr_rank" data-direction="up"><i
                                            class="glyphicon glyphicon-chevron-up"></i></a>
                                    <a href="javascript:void(0);"
                                       data-table="<?php echo $children_relation['table']->table_name; ?>" title="下降"
                                       class="tr_rank" data-direction="down"><i
                                            class="glyphicon glyphicon-chevron-down"></i></a>
                                    <a href="javascript:void (0);"
                                       data-table="<?php echo $children_relation['table']->table_name; ?>"
                                       class="tr_remove"><i class="glyphicon glyphicon-remove"></i></a>
                                    <a href="javascript:void (0);"
                                       data-table="<?php echo $children_relation['table']->table_name; ?>"
                                       class="tr_add"><i
                                            class="glyphicon glyphicon-plus"></i></a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </fieldset>
            <?php endforeach; ?>
        <?php endif; ?>


        <div class="form-actions pow_ml100">
            <?php if (isset($options[$table->id]) && $options[$table->id]['edit'] == 2): ?>
                <button class="btn btn-primary" type="submit" id="JS_Sub">更新</button>
            <?php endif ?>
            <a href="<?php echo URL::action('CmsController@index', array('id' => $table->id)) ?>"
               class="btn btn-warning"
               onclick="">取消</a>
        </div>
    </form>
<?php echo \Utils\FormBuilderHelper::end('cms_form', 'success_ajax_post'); ?>
    <script>
        function success_ajax_post(form) {
            $.ajax({
                url: '<?php echo URL::action('CmsController@update',array('cms'=>$tableData->id,'table'=>$table->id));?>',
                data: $(form).serialize(),
                type: 'PUT',
                beforeSend: function () {
                    $('#JS_Sub').attr('disabled', true);
                },
                success: function (re) {
                    console.log("re", re)
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
            }).complete(function () {
                    $('#JS_Sub').attr('disabled', false);
                });

            return false;
        }
    </script>
<?php echo $footer; ?>