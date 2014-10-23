<?php echo $header; ?>
<?php if ($hosts): ?>
    <div class="hostsList">
        <table class="table table-striped">
            <thead>
            <tr>
                <th>#</th>
                <th>主机名称</th>
                <th>主机地址</th>
                <th>创建人</th>
                <th>CDN</th>
                <th>缓存时间</th>
                <th>更新时间</th>
                <th>状态</th>
                <th>操作</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($hosts as $host): ?>
                <tr>
                    <td><?php echo $host->id; ?></td>
                    <td><?php echo $host->name; ?></td>
                    <td>
                        <?php echo $host->host; ?>&nbsp;&nbsp;
                        <?php if ($host->comment): ?>
                            <a class="comment" rel="popover" href="javascript:void(0)" data-original-title="备注"
                               data-content="<?php echo $host->comment ?>">
                                <i class="glyphicon glyphicon-comment"></i>
                            </a>
                        <?php endif; ?>
                    </td>
                    <td><?php echo $host->user_id && $host->user ? $host->user->name : 'unknow'; ?></td>
                    <td>
                <span class="label label-<?php echo $host->cdn ? 'info' : 'disable'; ?>">
                    <?php echo $host->cdn ? '开启' : '未开启'; ?>
                </span>
                    </td>
                    <td><?php echo $host->expire; ?></td>
                    <td><?php echo $host->updated_at; ?></td>
                    <td>
                <span class="label label-<?php echo $host->deleted_at ? 'disable' : 'info'; ?>">
                    <?php echo $host->deleted_at ? '已删除:' . $host->deleted_at : '正常'; ?>
                </span>
                    </td>
                    <td class="operation">
                        <a href="<?php echo URL::action('HostController@edit', array('host' => $host->id)); ?>"><i
                                class="glyphicon glyphicon-edit"></i></a>
                        <a data-tip="<?php echo $host->name; ?>" class="JS_hostOp"
                           data-url="<?php echo URL::action('HostController@destroy', array('host' => $host->id)) ?>"
                           href="#" data-status="<?php echo $host->deleted_at ? 'restore' : 'delete'; ?>"
                           data-target="<?php echo $host->id; ?>" href="javascript:void(0)">
                            <?php if (!$host->deleted_at): ?>
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

        <?php echo $hosts ? $hosts->links() : ''; ?>
    </div>
    <script>
        $(function () {
            $('.comment').popover({trigger: 'hover'});
            $('.JS_hostOp').click(function () {
                var hid = $(this).attr('data-target');
                var tip = $(this).attr('data-tip');
                if (confirm('确认要删除主机：' + tip)) {
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