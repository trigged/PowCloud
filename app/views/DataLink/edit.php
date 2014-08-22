<?php echo $header; ?>
    <div class="">
        <?php echo \Utils\FormBuilderHelper::begin(); //注册表单JS ?>
        <form data-status="0" id="shortcut_form" class="form-horizontal" method="post">
            <fieldset>
                <div class="note note-success">
                    <h4 class="block">:)</h4>

                    <p>主数据变更会影响到所有链接数据,但是链接数据的变化不会影响到主数据</p>

                    <p>一个主数据可以有多个链接数据,但是一个链接数据不能有多个主数据</p>

                    <p>
                        链接数据的字段名和属性值( <a href="<?php echo URL::action('SchemaBuilderController@index') ?>"
                                         target="_blank"> 系统管理->表列表 查看</a>)与主数据一致时才会产生级联变更
                        (主数据变更,相关的级联数据更着变更)
                    </p>
                </div>
                <legend>变更链接设置</legend>
                <div class="control-group">
                    <label for="name" class="control-label">主数据:</label>

                    <div class="controls">
                        <input name="data_info" class="input-medium" type="text" placeholder="数据变更" id="filed" readonly
                               value="<?php echo $data_link->table_name . '.' . $data_link->data_id ?>">
                    </div>
                </div>
                <div class="control-group">
                    <label for="name" class="control-label">添加链接数据:</label>

                    <div class="controls">
                        <input class="input-large" id="add_itmes" type="text" placeholder="table_name:data_id">
                        <a class="btn btn-info" onclick="addItmes()">add </a>
                    </div>
                </div>

                <div class="control-group" id='items'>
                    <?php if (count($items) > 0): ?>
                        <a id='choose_all' href="javascript:void(0);" class="btn btn-danger" onclick="allChose()">all
                            star</a>
                    <?php endif; ?>

                    <?php foreach ($items as $item): ?>
                        <label class="checkbox inline">
                            <input type="checkbox" id="inlineCheckbox1" class="checkbox_set"
                                   name="<?php echo $item->data_id . '-' . $item->table_name ?>">
                            <a href='<?php echo URL::action('CmsController@edit', array('table' => $item->table_id, 'cms' => $item->data_id)) ?>'
                               target="_blank"><?php echo $item->table_alias . '-' . $item->data_id ?></a>
                        </label>
                    <?php endforeach ?>
                </div>

                <div class="form-actions">
                    <button class="btn btn-primary" id="JS_Sub" type="submit">保存</button>
                    <a class="btn" onclick="history.back()">取消</a>
                </div>
            </fieldset>
        </form>
        <script>
            var items = [];
            function addItmes() {
                var value = $('#add_itmes').val();
                if (!(value.indexOf(":") > -1)) {
                    return alert('请输入合法链接数据 表名:数据ID');
                }
                if (items.indexOf(value) > -1) {
                    return alert('请不要重复输入数据');
                }

                items.push(value);
                var table_name = value.substring(0, value.indexOf(":"));
                var data_id = value.substring(value.indexOf(":") + 1);
                var check_item = '<label class="checkbox inline"> <input type="checkbox" id="inlineCheckbox1" class="checkbox_set" checked value="' + value + ' " name="' + "link_items[]" + '"> <a href="#" target="_blank"></a> ' + value + ' </label>';
                if (!$('#choose_all').length > 0) {
                    $('#items').html(' <a id="choose_all" href="javascript:void(0);" class="btn btn-danger" onclick="allChose()">全选</a>');
                }

                $('#items').append(check_item);
                return false;
            }
            function allChose() {
                $(".checkbox_set").attr("checked", !$(".checkbox_set").attr("checked"));
            }
        </script>

        <?php echo \Utils\FormBuilderHelper::staticEnd('shortcut_form',
            array( //表单规则
                'filed' => array('required' => true),
            ),
            URL::action('DataLinkController@update', array($data_link->id)),
            'PUT'
        );//注册表单JS
        ?>
    </div>
<?php echo $footer; ?>