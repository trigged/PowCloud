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
                <div class="form-group">
                    <label for="name" class="control-label col-sm-2">主数据:</label>

                    <div class="controls col-sm-6">
                        <input name="data_info" class="form-control" type="text" placeholder="数据变更" id="filed" readonly
                               value="<?php echo $data_link->table_name . '.' . $data_link->data_id ?>">
                    </div>
                </div>
                <div class="form-group">
                    <label for="name" class="control-label col-sm-2">添加链接数据:</label>

                    <div class="controls col-sm-6">
                        <input class="form-control " id="add_itmes" type="text" placeholder="table_name:data_id">

                    </div>
                    <a class="btn btn-info" onclick="addItmes()">add </a>
                </div>

                <div class="form-group" id='items'>
                    <?php foreach ($items as $item): ?>
                        <div class="dropdown  ">
                            <ul class="dropdown-menu" role="menu" aria-labelledby="dropdownMenu"
                                style="display: block; position: static; margin-bottom: 5px; *width: 180px;">

                                <a class="close" style="color: red ; opacity: 0.6;"
                                   href="javascript:void(0)" data-value="<?php echo $item->id ?>"
                                   onclick="deleteItem(this)">&times;</a>

                                <li><a tabindex="-1" target="_blank"
                                       href="<?php echo URL::action('CmsController@edit', array('table' => $item->table_id, 'cms' => $item->data_id)) ?>"><?php echo $item->table_alias . '-' . $item->data_id ?></a>
                                </li>
                                <li class="divider"></li>
                                <?php
                                echo sprintf('<input type="hidden" name="link_items[%s][%s][id]" value="%s">', $item->table_name, $item->data_id, $item->id);
                                $item_options = json_decode($item->options, true);
                                foreach ($item_options as $options) {
                                    //$key, $mapping_table_name, $mapping_data_id, $key);
                                    echo sprintf('<li><a tabindex="-1"  href="javascript:void(0)">%s(点击删除同步此字段)</a>
    <input type="hidden" name="link_items[%s][%s][]" value="%s">
    </li>', $options, $item->table_name, $item->data_id, $options);
                                }
                                ?>
                            </ul>
                        </div>

                    <?php endforeach ?>
                </div>

                <div class="form-actions">
                    <button class="btn btn-primary" id="JS_Sub" type="submit">保存</button>
                    <a class="btn  btn-warning" onclick="history.back()">取消</a>
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
                if (table_name == "<?php echo $data_link->table_name?>" && data_id == "<?php echo  $data_link->data_id ?>") {
                    return alert("请不要输入主数据");
                }
                $.ajax({
                    type: "POST",
                    url: "<?php echo  URL::action('DataLinkController@checkMappingItem')  ?>",
                    data: {"master_table": "<?php echo $data_link->table_name  ?>",
                        "mapping_table": table_name,
                        "mapping_id": data_id
                    },
                    success: function (data) {
                        var result = jQuery.parseJSON(data);
                        if (result['status'] == 'success') {

                            $('#items').append(result['data']);
                        }
                        else {
                            alert(result['message']);
                        }
                    }
                }).done(function () {
                    }).fail(function (result) {
                        alert(result);
                    });
                return false;
            }

            $('.dropdown-menu li').click(function () {
                if ($(this).attr('data-value') !== 'table_name') {
                    $(this).remove();
                }
            });


            function deleteItem(item) {
                var delete_id = $(item).attr('data-value');
                $.ajax({
                    type: "POST",
                    url: "<?php echo  URL::action('DataLinkController@deleteItem')  ?>",
                    data: {"id": delete_id },
                    success: function (data) {
                        var result = jQuery.parseJSON(data);
                        alert(result['message']);
                        $(item).parent().remove();
                    }
                }).done(function () {

                    }).fail(function (result) {
                        alert(result);
                    });
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