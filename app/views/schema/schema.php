<?php echo $header; ?>
<div class="schema">
    <fieldset>
        <legend><?php echo $table->table_name ?>'s 表结构</legend>
    </fieldset>
    <a href="<?php echo URL::action('SchemaBuilderController@addField', array('table' => $table->id)) ?>"
       class="btn btn-primary" type="button">添加字段</a>
    <table class="table table-striped">
        <thead>
        <tr>
            <th>字段名称</th>
            <th>字段属性</th>
            <th>操作</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($schema as $schemaItemName => $schemaItemProperty): ?>
            <tr>
                <td><?php echo $schemaItemName; ?></td>
                <td><?php echo implode(' ', $schemaItemProperty); ?></td>
                <td>
                    <a class="JS_tableOp" href="javascript:void(0) " data-tip="<?php echo $schemaItemName; ?>"
                       data-url="<?php echo URL::action('SchemaBuilderController@destroyField', array('table' => $table->id, 'field' => $schemaItemName)) ?>"
                       data-target="<?php echo $table->id; ?>"><i title="删除" class="glyphicon glyphicon-remove"></i></a>
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
            var tip = $(this).attr('data-tip');
            if (confirm('确认要删这个字段：' + tip)) {
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

