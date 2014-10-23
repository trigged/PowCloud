<?php echo $header; ?>
    <div class="">
        <fieldset>
            <legend>区域快捷键</legend>
        </fieldset>
        <div class="" style="">
            <a href="<?php echo URL::action('DataLinkController@create') ?>" class="btn btn-primary"
               type="button">添加数据</a>
        </div>
        <table class="table table-striped">
            <thead>
            <tr>
                <th>#</th>
                <th>表名</th>
                <th>属性ID</th>
                <th>更新时间</th>
                <th>操作</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($data_link as $link): ?>
                <tr>
                    <td><?php echo $link->id; ?></td>
                    <td><?php echo $link->table_name; ?></td>
                    <td><?php echo $link->data_id; ?></td>
                    <td><?php echo $link->updated_at; ?></td>

                    <td class="operation">
                        <a data-target="<?php echo $link->id; ?>" class="JS_tableOp"
                           data-url="<?php echo URL::action('DataLinkController@destroy', array($link->id)) ?>"
                           href="javascript:void(0)"><i class="glyphicon glyphglyphicon glyphglyphicon glyphicon-remove"
                                                        title="删除"></i></a>
                        <a href="<?php echo URL::action('DataLinkController@edit', array($link->id)) ?>"><i
                                class="glyphicon glyphglyphicon glyphicon-edit"></i></a>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>

    </div>
    <script>
        $(function () {
            $('.JS_tableOp').click(function () {
                var tid = $(this).attr('data-target');
                if (confirm('确认要删除：' + tid)) {
                    $.ajax({
                        url: $(this).attr('data-url'),
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
                return false;
            })
        })
    </script>
<?php echo $footer; ?>