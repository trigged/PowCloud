<?php echo $header; ?>
<div class="row mt20">

<div class="col-md-7">
    <div class="note note-info">
        <h4 class="block">小技巧</h4>

        <p>
            当表创建完成后如果绑定了访问路径并且开启了restful 的Create 权限，你可以通过API来添加数据或者通过配置表单，然后在数据管理中通过界面添加
            如没有绑定路径则可以通过配置表单然后通过内容管理添加数据

        </p>

        <p>
            <a href="http://doc.powapi.com/system_manage/table.html" target="_blank"> 相关文档地址</a>.
        </p>
    </div>
    <?php echo \Utils\FormBuilderHelper::begin(); //注册表单JS ?>
    <form id="schema_form" class="form-horizontal" method="post">
        <fieldset>
            <legend>修改表:<?php echo $schema->table_name; ?></legend>
            <div class="form-group">
                <label for="table_alias" class="control-label col-sm-3">表别名*:</label>

                <div class="controls col-sm-8">
                    <input name="table_alias" value="<?php echo $schema->table_alias; ?>" class="form-control"
                           type="text" placeholder="表别名" id="table_alias">
                </div>
            </div>
            <div class="form-group">
                <label for="group_name" class="control-label col-sm-3">组名:</label>

                <div class="controls col-sm-8">
                    <input name="group_name" value="<?php echo $schema->group_name; ?>" class="form-control"
                           type="text" placeholder="组名" id="group_name">
                </div>
            </div>
            <div class="form-group">
                <label for="path" class="control-label col-sm-3">映射路径*:</label>

                <div class="controls col-sm-8">
                    <?php echo Form::select('path_id', $pathTreeListOptions, $schema->path_id, array('id' => 'path_id')) ?>
                </div>
                <input type="hidden" id="path_name" name="path_name" value="">
            </div>

            <div class="form-group">
                <label for="name" class="control-label col-sm-3">属性*:</label>

                <div class="controls col-sm-8">
                    <table class="table">
                        <thead>
                        <tr>
                            <th>字段名</th>
                            <th>属性</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr data-index="0" id="row-0" class="propertyInput">
                            <td>
                                <input class="filedName filedNameLetter form-control" type="text"
                                       name="property[0][name]" value="id" disabled/>
                            </td>
                            <td>
                                <input class="property form-control" type="text"
                                       name="property[0][attributes]" value="integer" disabled/>
                            </td>
                        </tr>
                        <tr data-index="0" id="row-0" class="propertyInput">
                            <td>
                                <input class="filedName filedNameLetter form-control" type="text"
                                       name="property[0][name]" value="created_at" disabled/>
                            </td>
                            <td>
                                <input class="property form-control" type="text"
                                       name="property[0][attributes]" value="dateTime" disabled/>
                            </td>
                        </tr>
                        <tr data-index="0" id="row-0" class="propertyInput">
                            <td>
                                <input class="filedName filedNameLetter form-control" type="text"
                                       name="property[0][name]" value="updated_at" disabled/>
                            </td>
                            <td>
                                <input class="property form-control" type="text"
                                       name="property[0][attributes]" value="dateTime" disabled/>
                            </td>
                        </tr>
                        <?php foreach ($schema->property as $propertyName => $propertyValue): ?>
                            <tr class="propertyInput">
                                <td>
                                        <span><input class="form-control " placeholder="<?php echo $propertyName ?>"
                                                     value="<?php echo $propertyName ?>"
                                                     name="<?php echo $propertyName ?>"/></span>
                                </td>
                                <td>
                                    <span class="form-control uneditable-input"><?php
                                        echo implode(' ', array_except($propertyValue, array('default')));
                                        echo !empty($propertyValue['default']) ? '|' . $propertyValue['default'] : '';
                                        ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                        <tfoot>
                        </tfoot>
                    </table>
                </div>
            </div>
            <div id="JRestCtrl" class="pow_btn_horiz">
                <div class="form-group">
                    <label for="" class="control-label col-sm-4">restful:</label>

                    <div class="controls col-sm-8">
                        <label class="radio inline">
                            <input class="" onclick="$('#JRestful').removeClass('hide');" type="radio"
                                   name="restful" <?php if ((int)$schema->restful === 1) echo 'checked="checked"'; ?>
                                   value="1" id=""/> 开启
                        </label>
                        <label class="radio inline">
                            <input class="" onclick="$('#JRestful').addClass('hide');" type="radio"
                                   name="restful" <?php if (!$schema->restful && (int)$schema->restful === 0) echo 'checked="checked"'; ?>
                                   value="0" id=""/> 关闭
                        </label>
                    </div>
                </div>
                <div id="JRestful" class="form-group">
                    <label for="" class="control-label col-sm-4">可执行操作:</label>

                    <div class="controls col-sm-8">
                        <label class="checkbox inline">
                            <input type="checkbox" name="index"
                                   id="" <?php if ((int)$schema->index === 1) echo 'checked="checked"'; ?>
                                   value="1"> 读取
                        </label>
                        <label class="checkbox inline">
                            <input type="checkbox" name="update"
                                   id="" <?php if ((int)$schema->update === 1) echo 'checked="checked"'; ?>
                                   value="1"> 编辑
                        </label>
                        <label class="checkbox inline">
                            <input type="checkbox" name="create"
                                   id="" <?php if ((int)$schema->create === 1) echo 'checked="checked"'; ?>
                                   value="1"> 创建
                        </label>
                        <label class="checkbox inline">
                            <input type="checkbox"
                                   name="delete" <?php if ((int)$schema->delete === 1) echo 'checked="checked"'; ?>
                                   id="" value="1"> 删除
                        </label>
                    </div>
                </div>
            </div>
            <div class="form-actions">
                <button class="btn btn-primary" id="JS_Sub" type="submit">更新表</button>
                <a href="javascript:void (0)" class="btn  btn-warning" onclick="history.back();">取消</a>
            </div>
        </fieldset>
    </form>
    <?php echo \Utils\FormBuilderHelper::staticEnd('schema_form',
        array( //表单规则
            'table_alias' => array('required' => true),
        ),
        URL::action('SchemaBuilderController@update', array('table' => $schema->id)),
        'PUT'
    );//注册表单JS
    ?>
</div>
<div class="col-md-5">
    <div class="panel-group" id="accordion">
        <div class="panel panel-default">
            <div class="panel-heading active">
                <h4 class="panel-title">
                    <a data-toggle="collapse" data-parent="#accordion" href="#collapseString">
                        字符类型
                    </a>
                </h4>
            </div>
            <div id="collapseString" class="panel-collapse collapse in">
                <div class="panel-body">
                    string
                </div>
            </div>
        </div>
        <div class="panel panel-default">
            <div class="panel-heading">
                <h4 class="panel-title">
                    <a data-toggle="collapse" data-parent="#accordion" href="#collapseDate">
                        时间类型
                    </a>
                </h4>
            </div>
            <div id="collapseDate" class="panel-collapse collapse">
                <div class="panel-body">
                    datetime
                </div>
            </div>
        </div>
        <div class="panel panel-default">
            <div class="panel-heading">
                <h4 class="panel-title">
                    <a data-toggle="collapse" data-parent="#accordion" href="#collapseNum">
                        数字类型
                    </a>
                </h4>
            </div>
            <div id="collapseNum" class="panel-collapse collapse">
                <div class="panel-body">
                    integer,double,decimal
                </div>
            </div>
        </div>
        <div class="panel panel-default">
            <div class="panel-heading">
                <h4 class="panel-title">
                    <a data-toggle="collapse" data-parent="#accordion" href="#collapseText">
                        长文本类型
                    </a>
                </h4>
            </div>
            <div id="collapseText" class="panel-collapse collapse">
                <div class="panel-body">
                    text
                </div>
            </div>
        </div>
    </div>

</div>
</div>
<script>
    $(function () {
        var side_h = $(document.body).height();
        $(".sidebar-nav").height(side_h);
    })

</script>
<script>
    $(function () {
        $('#path_id').change(function () {
            var val = $(this).val();
            val == -1 ? $('#JRestCtrl').addClass('hide') : $('#JRestCtrl').removeClass('hide');
            $('#path_name').val($(this).find("option:selected").text());
        })

        if ($('#path_id').val() == -1) {
            $('#JRestCtrl').addClass('hide');
        }

    });
    function addProperty() {
        var index = $(".propertyInput").length;
        var tpl = '<tr><td><input class="form-control propertyInput" type="text" name="property[' + index + '][name]" value="" /></td><td><input class="form-control" type="text" name="property[' + index + '][attributes]" value="" /></td></tr>'
        $('table tbody').append(tpl);
    }
</script>
<?php echo $footer; ?>
