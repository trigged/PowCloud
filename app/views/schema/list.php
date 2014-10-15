<?php echo $header; ?>
    <div class="tablesList">
        <table class="table table-striped">
            <thead>
            <tr>
                <th>#</th>
                <th>表别名</th>
                <th>组名</th>
                <th>创建人</th>
                <th>文档</th>
                <th>路径</th>
                <th>API 访问</th>
                <th>权限</th>
                <th>更新时间</th>
                <th>操作</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($tables as $table): ?>
                <tr>
                    <td><?php echo $table->id; ?></td>
                    <td>
                        <?php echo $table->table_alias; ?>
                        <?php if ($table->property): ?>
                            <a class="comment" rel="popover" href="javascript:void(0)" data-original-title="表结构">
                                <i class="icon-comment"></i>
                            </a>
                            <p class="hide"><?php echo $table->property; ?></p>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php echo $table->group_name; ?>
                        <?php if ($table->property): ?>
                            <a class="comment" rel="popover" href="javascript:void(0)" data-original-title="表结构"></a>
                        <?php endif; ?>
                    </td>
                    <td><?php echo $table->user_id && $table->user ? $table->user->name : 'unknow'; ?></td>
                    <td>
                        <a href="<?php echo URL::action('SchemaBuilderController@docsHtml', array('table' => $table->table_name)) ?>"
                           data-target="#docs" data-toggle="modal"><span class="label label-info">文档</span></a></td>
                    <td><?php echo $table->path_id ?></td>
                    <td>
                        <!--                        <a href="--><?php
//                        if ($table->restful == 1) {
//                            echo URL::action('SchemaBuilderController@apiInfo', array('table' => $table->table_name));
//                        }
//                        ?><!--" target="_blank">-->
                            <span class="label label-<?php echo $table->restful == 1 ? 'info' : 'disable'; ?>">
                        <?php echo $table->restful == 1 ? '支持' : '不支持(未绑定路由)'; ?>
                                <!--                        </a>-->
                    </td>
                    <td>
                        <span class="label label-<?php echo $table->index === null ? 'disable' : 'info'; ?>">list</span>
                        <span
                            class="label label-<?php echo $table->update === null ? 'disable' : 'info'; ?>">edit</span>
                        <span
                            class="label label-<?php echo $table->create === null ? 'disable' : 'info'; ?>">create</span>
                        <span
                            class="label label-<?php echo $table->delete === null ? 'disable' : 'info'; ?>">delete</span>
                    </td>
                    <td><?php echo $table->updated_at; ?></td>
                    <td class="operation">
                        <a href="<?php echo URL::action('SchemaBuilderController@tableSchema', array('table' => $table->id)); ?>"
                           data-toggle="tooltip" title="表结构" style="container:'body'"><i class="icon-th"></i></a>
                        <a href="<?php echo URL::action('SchemaBuilderController@edit', array('table' => $table->id)) ?>"
                           data-toggle="tooltip" title="修改"><i class="icon-pencil"></i></a>
                        <a class="JS_tableOp" href="javascript:void(0) "
                           data-url="<?php echo URL::action('SchemaBuilderController@destroy', array('table' => $table->id)) ?>"
                           data-target=" <?php echo $table->id; ?>"><i title="删除" class="icon-remove"></i></a>
                        <a title="配置选项"
                           href="<?php echo URL::action('SchemaBuilderController@tableOptions', array('table' => $table->id)) ?>"
                           class=""><i class="icon-cog"></i></a>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>

        <?php echo $tables ? $tables->links() : ''; ?>

        <div id="docs" style="height: 500px;;" class="modal hide fade" tabindex="-1" role="dialog"
             aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h3 id="myModalLabel">文档</h3>
            </div>
            <div class="modal-body" style="height: 100%">
                <p></p>
            </div>
        </div>
    </div>

    <script>
        $(function () {
            $('#docs').on('hidden', function () {
                $('.modal-body', $(this)).html('');
                $(this).data('modal', null);
            });
            $('.comment').popover({trigger: 'hover', content: function () {
                text = $(this).next().text();
                return text;
            }});
            $('.JS_tableOp').click(function () {
                var tid = $(this).attr('data-target');
                if (confirm('确认要删除主机：' + tid)) {
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
            })
        })
    </script>
<?php echo $footer; ?>