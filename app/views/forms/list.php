<?php echo $header; ?>
<?php if ($forms): ?>
    <div class="formsList">
        <fieldset>
            <legend><?php echo $table->table_name ?>'s 的表单</legend>
        </fieldset>

        <div id="JS_button_hook" class="" style="">
            <a href="<?php echo URL::action('FormsController@addField', array('table' => $table->id)) ?>"
               class="btn btn-primary" type="button">添加字段</a>

            <button type="button" id="loading-example-btn" data-loading-text="请求中..." class="btn btn-info"
                    value="<?php echo $timing_state ?>">
                <?php echo $timing_state ?>定时发布
            </button>
        </div>

        <table id="forms_table" class="table table-hover sortable">
            <thead>
            <tr>
                <th>#ID</th>
                <th>字段</th>
                <th>标签</th>
                <th>类型</th>
                <th style="width: 250px;">默认值</th>
                <th style="width: 200px;">验证规则</th>
                <th>状态</th>
                <th>操作</th>
            </tr>
            </thead>
            <tbody data-table="forms_table"
                   data-rank="<?php echo URL::action('FormsController@rank', array('form' => $table->id)); ?>">
            <?php
            $value = Config::get('params.formField');
            foreach ($forms as $index => $form): ?>
                <tr data-rank="<?php echo $index ?>" data-id="<?php echo $form->id ?>">
                    <td class="index"><?php echo $form->id; ?></td>
                    <td><?php echo $form->field; ?></td>
                    <td><?php echo $form->label; ?></td>
                    <td><?php echo $value[$form->type]; ?></td>
                    <td><?php echo $form->default_value; ?></td>
                    <td><?php echo $form->rules; ?></td>
                    <td>
                        <?php echo (int)$form->isVisible === 0 ? '<span class="label label-warning">列表可见</span>' : '<span class="label label-success">列表可见</span>'; ?>
                        <?php echo (int)$form->isEditable === 0 ? '<span class="label label-warning">输入</span>' : '<span class="label label-success">输入</span>'; ?>
                        <?php echo (int)$form->visibleByGroup === 0 ? '' : '<span class="label label-info">' . Group::getGroupName($form->visibleByGroup) . '-可见</span>'; ?>
                    </td>
                    <td class="operation">
                        <a title="编辑"
                           href="<?php echo URL::action('FormsController@edit', array('form' => $form->id)); ?>"><i
                                class="glyphicon glyphicon-edit"></i></a>
                        <a class="JS_hostOp"
                           data-url="<?php echo URL::action('FormsController@destroy', array('form' => $form->id)) ?>"
                           href="#" data-status="<?php echo $form->deleted_at ? 'restore' : 'delete'; ?>"
                           data-target="<?php echo $form->id; ?>" href="javascript:void(0)">
                            <?php if (!$form->deleted_at): ?>
                                <i title="删除" class="glyphicon glyphicon-remove"></i>
                            <?php else: ?>
                                <i title="恢复" class="glyphicon glyphicon-repeat"></i>
                            <?php endif; ?>
                        </a>

                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>

        </table>

        <?php echo $forms ? $forms->appends(array('table' => $table->id))->links() : ''; ?>
    </div>
    <script>
        $('#loading-example-btn').click(function () {
            var btn = $(this);
            btn.button('loading');
            if (this.value == '开启') {
                btn.val('关闭');
                $.ajax({
                    type: "get",
                    url: "<?php echo URL::action('FormsController@addTiming', array('table' => $table->id)); ?>",
                }).done(function () {
                        btn.button('reset');
                        btn.text('关闭定时发布')
                    });
            }
            else if (this.value == '关闭') {
                btn.val('开启');

                $.ajax({
                    type: "get",
                    url: "<?php echo URL::action('FormsController@delTiming', array('table' => $table->id)); ?>",
                }).done(function () {
                        btn.button('reset');
                        btn.text('开启定时发布')
                    });
            }
        });

        $(function () {
            table.init('.sortable tbody tr', '<tr class="sortable-holder" style="height: 50px;"></tr>');
            $('.comment').popover({trigger: 'hover'});
            $('.JS_hostOp').click(function () {
                var hid = $(this).attr('data-target');
                if (confirm('确认要删除这条表单项吗：' + hid)) {
                    $.ajax({
                        url: $(this).attr('data-url'),
                        data: {id: hid, status: $(this).attr('data-status')},
                        type: 'DELETE',
                        success: function (re) {
                            re = $.parseJSON(re);
                            if (re.status == 'fail') {
                                alert(re.message);
                                return false;
                            }
                            alert('删除成功');
                            location.reload();
                        }
                    });
                }
            })
        })
    </script>
<?php endif; ?>
<?php echo $footer; ?>