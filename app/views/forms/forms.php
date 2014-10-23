<?php echo $header; ?>
    <div class="tablesList">
        <table class="table table-striped">
            <thead>
            <tr>
                <th>#</th>
                <th>对应表名</th>
                <th>创建人</th>
                <th>更新时间</th>
                <th>操作</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($tables as $table): ?>
                <tr>
                    <td><?php echo $table->id; ?></td>
                    <td><?php echo $table->table_name . '(' . $table->table_alias . ')'; ?></td>
                    <td><?php echo $table->user_id && $table->user ? $table->user->name : 'unknow'; ?></td>
                    <td><?php echo $table->updated_at; ?></td>
                    <td class="operation">
                        <?php if (!$table->forms->count()): ?>
                            <a title="创建表单"
                               href="<?php echo URL::action('FormsController@create', array('table' => $table->id)) ?>"><i
                                    class="glyphicon glyphicon-plus"></i></a>
                        <?php else: ?>
                            <a title="查看表单"
                               href="<?php echo URL::action('FormsController@index', array('table' => $table->id)) ?>"><i
                                    class="glyphicon glyphicon-th-list"></i></a>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <?php echo $tables ? $tables->links() : ''; ?>
    </div>
<?php echo $footer; ?>