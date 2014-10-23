<?php echo $header; ?>
<?php
if (!$isAdmin) {
    echo "<style>
.limit {
display:none;
}
</style>";
}
?>
<?php if ($groupModels): ?>
    <div class="groupModelsList">
        <table class="table table-striped">
            <thead>
            <tr>
                <th>#</th>
                <th>用户组名</th>
                <th class="limit">操作</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($groupModels as $groupModel): ?>
                <tr>
                    <td><?php echo $groupModel->id; ?></td>
                    <td><?php echo $groupModel->groupName; ?></td>
                    <td class="operation limit">
                        <a href="<?php echo URL::action('LimitController@edit', array('limit' => $groupModel->id)); ?>"><i
                                class="glyphicon glyphicon-edit"></i></a>
                        <a class="JS_groupModelOp"
                           data-url="<?php echo URL::action('LimitController@destroy', array('limit' => $groupModel->id)) ?>"
                           href="#" data-target="<?php echo $groupModel->id; ?>" href="javascript:void(0)"><i title="删除"
                                                                                                              class="glyphicon glyphicon-remove"></i>
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <div class="limit" style="">
        <a href="<?php echo URL::action('LimitController@create', array()) ?>" class="btn btn-primary" type="button">添加数据</a>
    </div>

    <script>
        $(function () {
            $('.comment').popover({trigger: 'hover'});
            $('.JS_groupModelOp').click(function () {
                var hid = $(this).attr('data-target');
                if (confirm('确认要删除：' + hid)) {
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