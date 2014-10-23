<?php echo $header; ?>
<?php if ($hooks): ?>
    <div class="hooksList">
        <fieldset>
            <legend>hook列表</legend>
        </fieldset>
        <div class="" style="">
            <a href="<?php echo URL::action('CodeFragmentController@createHook') ?>" class="btn btn-primary"
               type="button">添加数据</a>
        </div>
        <table class="table table-striped">
            <thead>
            <tr>
                <th>#</th>
                <th>模型名称</th>
                <th>Hook名称</th>
                <th>创建人</th>
                <th>更新时间</th>
                <th>状态</th>
                <th>操作</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($hooks as $hook): ?>
                <tr>
                    <td><?php echo $hook->id; ?></td>
                    <td><?php echo $hook->table_name; ?></td>
                    <td><?php echo Hook::getHooksName($hook->name); ?></td>
                    <td><?php echo $hook->user_name; ?></td>
                    <td><?php echo $hook->updated_at; ?></td>
                    <td>
                <span class="label label-<?php echo $hook->deleted_at ? 'disable' : 'info'; ?>">
                    <?php echo (int)$hook->status === 0 ? '禁用' : '启用'; ?>
                </span>
                    </td>
                    <td class="operation">
                        <a href="<?php echo URL::action('CodeFragmentController@editHook', array('hook' => $hook->id)); ?>"><i
                                class="glyphicon glyphicon-edit"></i></a>
                        <a title="查看历史版本"
                           href="<?php echo URL::action('CodeFragmentController@hookDetail', array('hook' => $hook->id)); ?>"><i
                                class="glyphicon glyphicon-file"></i></a>
                        <!--                        <a class="JS_hookOp" data-url="-->
                        <?php //echo URL::action('CodeFragmentController@disableHook',array('hook'=>$hook->id)) ?><!--" href="#" data-status="-->
                        <?php //echo $hook->deleted_at?'restore':'delete'; ?><!--" data-target="-->
                        <?php //echo $hook->id;?><!--" href="javascript:void(0)">-->
                        <!--                            --><?php //if(!$hook->deleted_at):?>
                        <!--                                <i title="删除" class="glyphicon glyphicon-remove"></i>-->
                        <!--                            --><?php //else:?>
                        <!--                                <i title="恢复" class="glyphicon glyphicon-repeat"></i>-->
                        <!--                            --><?php //endif;?>
                        <!--                        </a>-->
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>

        <?php echo $hooks ? $hooks->links() : ''; ?>
    </div>
    <script>
        $(function () {
            $('.comment').popover({trigger: 'hover'});
            $('.JS_hookOp').click(function () {
                var hid = $(this).attr('data-target');
                if (confirm('确认要删除的HOOK：' + hid)) {
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