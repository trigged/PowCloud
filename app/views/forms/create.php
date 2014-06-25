<?php echo $header; ?>
    <h3>创建表:<?php echo $table->table_name; ?>表单</h3>
    <form class="form-horizontal child_form" action="<?php echo URL::action('FormsController@store') ?>" method="post"
          onsubmit="return check_form(this);">
        <fieldset>
            <legend style="margin-bottom: 0px;">表单选项</legend>
            <div class="control-group">
                <input type="hidden" name="tableId" value="<?php echo $table->id; ?>">
                <label for="" class="control-label" style="width: 60px;">定时发布</label>

                <div class="controls" style="margin-left: 0px;">
                    <label class="radio inline">
                        <input type="radio" name="timing_time" id="" value="1"> 开启
                    </label>
                    <label class="radio inline">
                        <input type="radio" name="timing_time" checked="checked" id="" value="2"> 关闭
                    </label>
                </div>
            </div>
        </fieldset>
        <fieldset>
            <legend>表单字段</legend>
            <table class="table table-striped">
                <thead>
                <tr>
                    <th>字段</th>
                    <th>标签</th>
                    <th>类型</th>
                    <th>验证规则</th>
                    <th>默认值</th>
                    <th>排序</th>
                </tr>
                </thead>
                <tbody>
                <?php $index = 0;
                foreach ($formFields as $formField): ?>
                    <tr>
                        <td class="">
                            <?php echo $formField['name'] ?>(<?php echo $formField['type']->getName(); ?>)
                            <input type="hidden" name="field[<?php echo $index; ?>][field]"
                                   value="<?php echo $formField['name'] ?>"/>
                            <input type="hidden" name="field[<?php echo $index; ?>][models_id]"
                                   value="<?php echo $table->id; ?>"/>
                        </td>
                        <td>
                            <input data-tip="标签(<?php echo $formField['name']; ?>)" type="text"
                                   class="input-mini required" name="field[<?php echo $index; ?>][label]" value=""/>
                        </td>
                        <td>
                            <?php echo Form::select('field[' . $index . '][type]', Config::get('params.formField'), '', array('class' => 'input-small')); ?>
                        </td>
                        <td>
                            <?php echo Form::textarea('field[' . $index . '][rules]'); ?>

                        </td>
                        <td>
                            <input type="text" name="field[<?php echo $index; ?>][default_value]" value=""/>
                        </td>
                        <td>
                            <input type="text" class="input-mini" name="field[<?php echo $index; ?>][rank]" value=""/>
                        </td>
                    </tr>
                    <?php $index++;endforeach; ?>
                </tbody>
            </table>
        </fieldset>
        <div class="form-actions">
            <button class="btn btn-primary" id="JS_Sub" type="submit">创建表单</button>
            <a href="javascript:void (0);" onclick="history.back();" class="btn">取消</a>
        </div>
    </form>
    <script>
        function check_form(form) {
            var flag = true;
            $('input.required', $(form)).each(function () {
                if (!$(this).val()) {
                    var tip = $(this).attr('data-tip');
                    alert(tip);
                    this.focus();
                    flag = false;
                }
            });
            return flag;
        }
    </script>
<?php echo $footer; ?>