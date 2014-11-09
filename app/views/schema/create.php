<?php echo $header; ?>
<script src="<?php echo URL::asset('bower_components/bootstrap3-typeahead/bootstrap3-typeahead.min.js'); ?>"></script>
<div class="row">

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
        <h4>创建表结构</h4>
        <hr/>
        <div class="form-group">
            <label for="table_name" class="control-label col-sm-3">表名*:</label>

            <div class="controls col-sm-8 form-inline">
                <input name="table_name" class="form-control" type="text" placeholder="表名" id="table_name">
            </div>
        </div>
        <div class="form-group">
            <label for="table_alias" class="control-label col-sm-3">表别名*:</label>

            <div class="controls col-sm-8 form-inline">
                <input name="table_alias" class="form-control" type="text" placeholder="表别名" id="table_alias">
            </div>
        </div>
        <div class="form-group">
            <label for="group_name" class="control-label col-sm-3">组名:</label>

            <div class="controls col-sm-8 form-inline">
                <input name="group_name" class="form-control" type="text" placeholder="组名" id="group_name">
            </div>
        </div>
        <div class="form-group">
            <label for="path" class="control-label col-sm-3">API 地址绑定*:</label>

            <div class="controls col-sm-8 form-inline">
                <?php echo Form::select('path_id', $pathTreeListOptions, '', array('id' => 'path_id')) ?>
            </div>
            <input type="hidden" id="path_name" name="path_name" value="">
        </div>

        <div class="form-group">
            <label for="name" class="control-label col-sm-3">属性*:</label>

            <div class="controls col-sm-8 form-inline">
                <table class="table" style="width: 500px;">
                    <thead>
                    <tr>
                        <th>字段名</th>
                        <th>属性</th>
                        <th></th>
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

                    <?php for ($tableIndex = 1; $tableIndex <= 1; $tableIndex++): ?>

                        <tr data-index="<?php echo $tableIndex ?>" id="row-<?php echo $tableIndex ?>"
                            class="propertyInput">

                            <td>
                                <input class="filedName filedNameLetter form-control" type="text"
                                       name="property[<?php echo $tableIndex; ?>][name]" value=""/>
                            </td>
                            <td>
                                <input class="typeahead property form-control" type="text"
                                       name="property[<?php echo $tableIndex; ?>][attributes]" value=""
                                       data-provide="typeahead" data-source='["string","integer","double",
                                       "text","datetime","decimal"]'
                                    />
                            </td>
                            <td>
                                <i data-row="row-<?php echo $tableIndex; ?>" class="glyphicon glyphicon-remove"></i>
                            </td>
                        </tr>
                    <?php endfor; ?>
                    </tbody>
                    <tfoot>
                    <tr>
                        <td colspan="3">
                            <button class="btn btn-large btn-primary" onclick="addProperty()" type="button">添加字段
                            </button>
                        </td>
                    </tr>
                    </tfoot>
                </table>
            </div>
        </div>
        <div id="JRestCtrl">
            <div class="form-group">
                <label for="" class="control-label col-sm-3">API 访问:</label>

                <div class="controls col-sm-8">
                    <label class="radio inline pow_padding_left_20">
                        <input class="" type="radio" name="restful" value="1"
                               onclick="$('#JRestful').removeClass('hide');" id=""/> 开启
                    </label>
                    <label class="radio inline pow_padding_left_20">
                        <input class="" type="radio" onclick="$('#JRestful').addClass('hide');" name="restful"
                               checked="checked" value="0" id=""/> 关闭
                    </label>
                </div>
            </div>
            <div id="JRestful" class=" hide form-group">
                <label for="" class="control-label col-sm-3">可执行操作:</label>

                <div class="controls col-sm-8">
                    <label class="checkbox inline">
                        <input type="checkbox" name="index" id="" value="1"> 读取
                    </label>
                    <label class="checkbox inline">
                        <input type="checkbox" name="update" id="" value="1"> 编辑
                    </label>
                    <label class="checkbox inline">
                        <input type="checkbox" name="create" id="" value="1"> 创建
                    </label>
                    <label class="checkbox inline">
                        <input name="" type="checkbox" name="delete" id="" value="1"> 删除
                    </label>
                </div>
            </div>
        </div>
        <div style="margin-left:148px;">
            <button class="btn btn-primary" id="JS_Sub" type="submit">创建表</button>
            <a href="javascript:void (0)" class="btn  btn-warning" onclick="history.back();">取消</a>
        </div>

    </form>
    <?php echo \Utils\FormBuilderHelper::staticEnd('schema_form',
        array( //表单规则
            'table_name'  => array('required' => true),
            'table_alias' => array('required' => true),
        ),
        URL::action('SchemaBuilderController@store'),
        'POST',
        '',
        '$.validator.addMethod("filedName", function(value, element) {
            return !this.optional(element);
        }, "请填写字段名");

        $.validator.addMethod("filedNameLetter", function(value, element) {
              return parseInt(value.toLowerCase().charCodeAt())>=97 && parseInt(value.toLowerCase().charCodeAt())<=122;
        },"字段名只能是字母");

        $.validator.addMethod("property", function(value, element) {
            return !this.optional(element);
        }, "请填写属性");'
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
<script>


    $(function () {
        $('#path_id').change(function () {
            var val = $(this).val();
            val == -1 ? $('#JRestCtrl').addClass('hide') : $('#JRestCtrl').removeClass('hide');
            $('#path_name').val($(this).find("option:selected").text());
        })
        $("body").on("click", ".glyphicon-remove", function () {
            if ($('tbody tr').length == 4) {
                alert('至少 添加一个字段 ');
                return false;
            }
            var target = $(this).attr('data-row');
            $('#' + target).remove();
        });


    });
    function addProperty() {
        var index = parseInt($(".propertyInput:last").attr('data-index')) + 1;
        var tpl = '<tr class="propertyInput" data-index="' + index + '" id="row-' + index + '"><td><input class="fieldName filedNameLetter form-control" type="text" name="property[' + index + '][name]" value="" /></td><td><input class="property form-control" type="text" name="property[' + index + '][attributes]" value="" /></td><td><i class="glyphicon glyphicon-remove" data-row="row-' + index + '"></i></td> </tr>';
        //  var tpl = '<tr class="propertyInput" data-index="' + index + '" id="row-' + index + '"><td><input class="fieldName filedNameLetter input-mini" type="text" name="property[' + index + '][name]" value="" /></td><td><input class="property input-xlarge" type="text" name="property[' + index + '][attributes]" value="" /></td><td><i class="icon-remove" data-row="row-' + index + '"></i></td> </tr>';
        $('table tbody').append(tpl);
    }
</script>

</div>
<?php echo $footer; ?>
