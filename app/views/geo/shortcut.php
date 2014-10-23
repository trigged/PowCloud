<?php echo $header; ?>
    <div class="shortCutsList">
        <fieldset>
            <legend>区域快捷键</legend>
        </fieldset>
        <div class="" style="">
            <a href="<?php echo URL::action('GeoController@shortcut_create') ?>" class="btn btn-primary" type="button">添加数据</a>
        </div>
        <table class="table table-striped">
            <thead>
            <tr>
                <th>#</th>
                <th>快捷键名称</th>
                <th>更新时间</th>
                <th>类型</th>
                <th>操作</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($shortCuts as $shortCut): ?>
                <tr>
                    <td><?php echo $shortCut->id; ?></td>
                    <td><?php echo $shortCut->name; ?></td>
                    <td><?php echo $shortCut->updated_at; ?></td>
                    <td>
                        <?php echo (int)$shortCut->type === 1 ? '<img width="20px;" src="' . URL::asset('css/img/strongselect.png') . '" />' : '<img width="20px;" src="' . URL::asset('css/img/strong.png') . '" />'; ?>
                    </td>
                    <td class="operation">
                        <a data-target="<?php echo $shortCut->id; ?>" class="JS_tableOp"
                           data-url="<?php echo URL::action('GeoController@shortcut_destroy', array('id' => $shortCut->id)) ?>"
                           href="javascript:void(0)"><i class="glyphicon glyphicon-remove" title="删除"></i></a>
                        <a href="<?php echo URL::action('GeoController@shortcut_edit', array('id' => $shortCut->id)) ?>"><i
                                class="glyphicon glyphicon-edit"></i></a>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <?php echo $shortCuts ? $shortCuts->links() : ''; ?>
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