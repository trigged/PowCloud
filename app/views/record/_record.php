<?php if ($records): ?>
    <div class="recordsList">
        <fieldset>
            <legend>历史记录</legend>
        </fieldset>
        <table class="table table-striped">
            <thead>
            <tr>
                <th>#</th>
                <th>时间</th>
                <th>创建人</th>
                <th>变更人</th>
                <td>查看内容</td>
                <th>操作</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($records as $record): ?>
                <tr>
                    <td><?php echo $record->id; ?></td>
                    <td><?php echo $record->created_at; ?></td>
                    <td><?php
                        $content = json_decode($record->content, true);
                        if (!empty($content['user_name']))
                            echo $content['user_name'];
                        else
                            echo $record->user_name;
                        ?></td>
                    <td><?php echo $record->user_name; ?></td>
                    <td>
                        <a href="<?php echo URL::action('RecordController@detail', array('record' => $record->id)) ?>"
                           data-target="#recordHistory" data-toggle="modal"><span
                                class="label label-info">点击查看</span></a>
                    </td>
                    <td class="operation">
                        <a class="JS_recordOp" data-type="DELETE"
                           data-url="<?php echo URL::action('RecordController@destroy', array('record' => $record->id)) ?>"
                           title="删除此纪录" href="javascript:void (0)"><i class="glyphicon glyphicon-remove"></i></a>
                        <a class="JS_recordOp" data-type="POST"
                           data-url="<?php echo URL::action('RecordController@recover', array('record' => $record->id)) ?>"
                           title="恢复到此版本" href="javascript:void (0)"><i class="glyphicon glyphicon-repeat"></i></a>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <?php echo $records ? $records->links() : ''; ?>
    </div>

    <div id="recordHistory" style="height: 500px;" class="modal hide fade" tabindex="-1" role="dialog"
         aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            <h3 id="myModalLabel">历史数据</h3>
        </div>
        <div class="modal-body" style="height: 100%">
            <p></p>
        </div>
    </div>
    <script>
        $(function () {
            $('#recordHistory').on('hidden', function () {
                $('.modal-body', $(this)).html('');
                $(this).data('modal', null);
            });
            $('.JS_recordOp').click(function () {
                if (confirm('确认要执行操作吗?')) {
                    $.ajax({
                        url: $(this).attr('data-url'),
                        type: $(this).attr('data-type'),
                        success: function (re) {
                            re = $.parseJSON(re);
                            if (re.status == 'fail') {
                                alert(re.message);
                                return false;
                            }
                            alert(re.message);
                            location.reload();
                        }
                    });
                }
            })
        })
    </script>
<?php endif; ?>