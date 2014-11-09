<?php echo $header; ?>
<?php if (count($forms)): ?>
    <div class="form_field">
        <?php echo \Utils\FormBuilderHelper::begin($table->table_name); ?>

        <form id="cms_form" class="form-horizontal"
              action="<?php echo URL::action('CmsController@store', array('id' => $table_id)); ?>" method="post">
            <input type="hidden" name='create_flag' id="JStatus" value="">
            <fieldset>
                <legend>添加数据</legend>
                <?php foreach ($forms as $form): ?>

                <?php if ((int)$form->visibleByGroup !== 0 && (int)$form->visibleByGroup !== (int)Auth::user()->group_id) {
                    echo \Utils\FormBuilderHelper::hidden($form);
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
                        echo \Utils\FormBuilderHelper::timingState($form);
                        echo "</div>";
                        echo "</div>";
                        continue;
                        } ?>


                        <?php if ($form->type == 'image' && $form->default_value): ?>
                            <?php
                            $label_val = array_filter(explode(',', $form->default_value));
                            ?>
                            <?php foreach ($label_val as $sub): ?>
                                <div class="form-group">
                                    <label for="name" class="control-label col-sm-3"><?php echo $sub ?>:</label>

                                    <div class="controls col-sm-5">
                                        <?php
                                        if ($form->rules)
                                            \Utils\FormBuilderHelper::registerValidateRules($form->field, $form->rules);
                                        //注册验证规则 以便JS可以验证
                                        $namespace = $table->table_name ? $table->table_name . '[' . $form->field . ']' : $form->field;
                                        $class = 'form-control';
                                        $input = '<input type="text" name="' . $namespace . '[]" placeholder="单击上传" value=""  class="' . $class . ' image-uploader"  />';
                                        echo $input;
                                        ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php elseif ($form->field && $form->type !== 'formTip') : ?>
                            <div class="form-group">
                                <label for="name" class="control-label col-sm-3"><?php echo $form->label ?>:</label>

                                <div class="controls col-md-6 form-inline">
                                    <?php
                                    if ($form->rules)
                                        Utils\FormBuilderHelper::registerValidateRules($form->field, $form->rules);
                                    echo call_user_func_array(array('\Utils\FormBuilderHelper', $form->type), array($form));
                                    ?>
                                </div>
                            </div>
                        <?php endif; ?>
                        <?php endforeach; ?>
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
                            foreach ($children_relation['data'] as $data): \Utils\DataColumnHelper::registerFormNamespace($children_relation['table']->table_name . '[' . $index . ']') ?>
                                <tr class="data">
                                    <td><span class="data-index"><?php echo $index++; ?></span></td>
                                    <?php foreach ($children_relation['forms'] as $form): ?>
                                        <?php if ((int)$form->visibleByGroup !== 0 && (int)$form->visibleByGroup !== (int)Auth::user()->group_id) {
                                            echo \Utils\FormBuilderHelper::hidden($form);
                                            continue;
                                        } ?>
                                        <td>
                                            <?php
                                            if ($form->rules)
                                                Utils\FormBuilderHelper::registerValidateRules($form->field, $form->rules);
                                            echo call_user_func_array(array('\Utils\DataColumnHelper', $form->type), array('form', $form));
                                            ?>
                                        </td>
                                    <?php endforeach; ?>
                                    <td>
                                        <a href="javascript:void(0);"
                                           data-table="<?php echo $children_relation['table']->table_name; ?>"
                                           title="上升"
                                           class="tr_rank" data-direction="up"><i
                                                class="glyphicon glyphicon-chevron-up"></i></a>
                                        <a href="javascript:void(0);"
                                           data-table="<?php echo $children_relation['table']->table_name; ?>"
                                           title="下降"
                                           class="tr_rank" data-direction="down"><i
                                                class="glyphicon glyphicon-chevron-down"></i></a>
                                        <a href="javascript:void (0);"
                                           data-table="<?php echo $children_relation['table']->table_name; ?>"
                                           class="tr_remove"><i class="glyphicon glyphicon-remove"></i></a>
                                        <a href="javascript:void (0);"
                                           data-table="<?php echo $children_relation['table']->table_name; ?>"
                                           class="tr_add"><i class="glyphicon glyphicon-plus"></i></a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </fieldset>
                <?php endforeach; ?>
            <?php endif; ?>
            <div class="form-actions">
                <button class="btn btn-info" onclick="document.getElementById('JStatus').value='create';" type="submit">

                    添加到待发布
                </button>
                <button class="btn btn-primary" onclick="document.getElementById('JStatus').value='save';"
                        type="submit">保存
                </button>
                <a href="<?php echo URL::action('CmsController@index', array('id' => $table->id)); ?>"
                   class="btn btn-warning">取消</a>
            </div>
        </form>
        <?php echo \Utils\FormBuilderHelper::end('cms_form', 'beforeSubmit'); ?>
        <script>
            function beforeSubmit(form, formValidate) {
                if (!$(form).attr('data-submit') && formValidate.form()) {
                    $(form).attr('data-submit', 1);
                    $(formValidate.submitButton).attr('disabled', 'disabled').html('正在提交中.....');
                    form.submit();
                } else
                    return false;
            }
        </script>
    </div>
<?php else: ?>
    <div class="alert alert-danger">
        <strong>哎出现问题!</strong> 请去表列表中创建表单,以便添加数据.
    </div>
<?php endif; ?>
<?php echo $footer; ?>
