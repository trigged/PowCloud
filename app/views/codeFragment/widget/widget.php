<?php echo $header; ?>
<?php if ($widgets): ?>
    <div class="widgetsList">
        <fieldset>
            <legend>挂件列表</legend>
        </fieldset>
        <div class="" style="">
            <a href="<?php echo URL::action('CodeFragmentController@createWidget') ?>" class="btn btn-primary"
               type="button">添加数据</a>
        </div>
        <table class="table table-striped">
            <thead>
            <tr>
                <th>#</th>
                <th>模型名称</th>
                <th>插件名称</th>
                <th>创建人</th>
                <th>位置</th>
                <th>更新时间</th>
                <th>状态</th>
                <th>操作</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($widgets as $widget): ?>
                <tr>
                    <td><?php echo $widget->id; ?></td>
                    <td><?php echo $widget->table_name; ?></td>
                    <td>
                        <?php
                        if ($widget->action === 'page' && $widget->status == 1) {
                            echo '<a href="' . URL::action('CmsController@page', array('page' => $widget->name)) . '">' . $widget->name . '</a>';
                        } else
                            echo $widget->name;
                        ?>
                    </td>
                    <td><?php echo $widget->user_name; ?></td>
                    <td><?php echo $widget->action; ?></td>
                    <td><?php echo $widget->updated_at; ?></td>
                    <td>
                <span class="label label-<?php echo $widget->deleted_at ? 'disable' : 'info'; ?>">
                    <?php echo (int)$widget->status === 0 ? '禁用' : '启用'; ?>
                </span>
                    </td>
                    <td class="operation">
                        <a href="<?php echo URL::action('CodeFragmentController@editWidget', array('widget' => $widget->id)); ?>"><i
                                class="glyphicon glyphicon-edit"></i></a>
                        <a title="查看历史版本"
                           href="<?php echo URL::action('CodeFragmentController@widgetDetail', array('widget' => $widget->id)); ?>"><i
                                class="glyphicon glyphicon-file"></i></a>
                        <!--                        <a class="JS_widgetOp" data-url="-->
                        <?php //echo URL::action('CodeFragmentController@disableWidget',array('widget'=>$widget->id)) ?><!--" data-status="-->
                        <?php //echo $widget->deleted_at?'restore':'delete'; ?><!--" data-target="-->
                        <?php //echo $widget->id;?><!--" href="javascript:void(0)">-->
                        <!--                            --><?php //if(!$widget->deleted_at):?>
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

        <?php echo $widgets ? $widgets->links() : ''; ?>
    </div>
    <script>
        $(function () {
            $('.comment').popover({trigger: 'hover'});
            $('.JS_widgetOp').click(function () {
                var hid = $(this).attr('data-target');
                if (confirm('确认要禁用的挂件：' + hid)) {
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