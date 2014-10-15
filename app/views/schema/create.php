<?php echo $header; ?>
<div class="row">
<div class="span7">
    <div class="note note-success">
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
        <div class="control-group">
            <label for="table_name" class="control-label">表名*:</label>

            <div class="controls">
                <input name="table_name" class="input-medium" type="text" placeholder="表名" id="table_name">
            </div>
        </div>
        <div class="control-group">
            <label for="table_alias" class="control-label">表别名*:</label>

            <div class="controls">
                <input name="table_alias" class="input-medium" type="text" placeholder="表别名" id="table_alias">
            </div>
        </div>
        <div class="control-group">
            <label for="group_name" class="control-label">组名:</label>

            <div class="controls">
                <input name="group_name" class="input-medium" type="text" placeholder="组名" id="group_name">
            </div>
        </div>
        <div class="control-group">
            <label for="path" class="control-label">映射路径*:</label>

            <div class="controls">
                <?php echo Form::select('path_id', $pathTreeListOptions, '', array('id' => 'path_id')) ?>
            </div>
            <input type="hidden" id="path_name" name="path_name" value="">
        </div>

        <div class="control-group">
            <label for="name" class="control-label">属性*:</label>

            <div class="controls">
                <table class="table" style="width: 500px;">
                    <thead>
                    <tr>
                        <th>字段名</th>
                        <th>属性</th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php for ($tableIndex = 1; $tableIndex <= 1; $tableIndex++): ?>
                        <tr data-index="<?php echo $tableIndex ?>" id="row-<?php echo $tableIndex ?>"
                            class="propertyInput">
                            <td>
                                <input class="filedName filedNameLetter input-medium" type="text"
                                       name="property[<?php echo $tableIndex; ?>][name]" value=""/>
                            </td>
                            <td>
                                <input class="property input-large" type="text"
                                       name="property[<?php echo $tableIndex; ?>][attributes]" value=""/>
                            </td>
                            <td>
                                <i data-row="row-<?php echo $tableIndex; ?>" class="icon-remove"></i>
                            </td>
                        </tr>
                    <?php endfor; ?>
                    </tbody>
                    <tfoot>
                    <tr>
                        <td colspan="3">
                            <button class="btn btn-mini btn-primary" onclick="addProperty()" type="button">添加字段</button>
                        </td>
                    </tr>
                    </tfoot>
                </table>
            </div>
        </div>
        <div id="JRestCtrl">
            <div class="control-group">
                <label for="" class="control-label">API 访问:</label>

                <div class="controls">
                    <label class="radio inline">
                        <input class="" type="radio" name="restful" value="1"
                               onclick="$('#JRestful').removeClass('hide');" id=""/> 开启
                    </label>
                    <label class="radio inline">
                        <input class="" type="radio" onclick="$('#JRestful').addClass('hide');" name="restful"
                               checked="checked" value="0" id=""/> 关闭
                    </label>
                </div>
            </div>
            <div id="JRestful" class="control-group hide">
                <label for="" class="control-label">可执行操作:</label>

                <div class="controls">
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
        <div class="form-actions">
            <button class="btn btn-primary" id="JS_Sub" type="submit">创建表</button>
            <a href="javascript:void (0)" class="btn" onclick="history.back();">取消</a>
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
<div class="span5">
    <div id="accordion2" class="accordion">
        <div class="accordion-group">
            <div class="accordion-heading">
                <a href="#collapseOne" data-parent="#accordion2" data-toggle="collapse" class="accordion-toggle">
                    字符类型
                </a>
            </div>
            <div class="accordion-body in collapse" id="collapseOne" style="height: auto;">
                <div class="accordion-inner">
                    string
                </div>
            </div>
        </div>
        <div class="accordion-group">
            <div class="accordion-heading">
                <a href="#collapseTwo" data-parent="#accordion2" data-toggle="collapse" class="accordion-toggle">
                    时间类型
                </a>
            </div>
            <div class="accordion-body collapse" id="collapseTwo" style="height: 0px;">
                <div class="accordion-inner">
                    datetime
                </div>
            </div>
        </div>
        <div class="accordion-group">
            <div class="accordion-heading">
                <a href="#collapseThree" data-parent="#accordion2" data-toggle="collapse" class="accordion-toggle">
                    数字类型
                </a>
            </div>
            <div class="accordion-body collapse" id="collapseThree" style="height: 0px;">
                <div class="accordion-inner">
                    integer,double,decimal
                </div>
            </div>
        </div>
        <div class="accordion-group">
            <div class="accordion-heading">
                <a href="#collapseFour" data-parent="#accordion2" data-toggle="collapse" class="accordion-toggle">
                    长文本类型
                </a>
            </div>
            <div class="accordion-body collapse" id="collapseFour" style="height: 0px;">
                <div class="accordion-inner">
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


        $(".icon-remove").live('click', function () {
            if ($('tbody tr').length == 1) {
                alert('至少 添加一个字段 ');
                return false;
            }
            var target = $(this).attr('data-row');
            $('#' + target).remove();
        });
    });
    function addProperty() {
        var index = parseInt($(".propertyInput:last").attr('data-index')) + 1;
        var tpl = '<tr class="propertyInput" data-index="' + index + '" id="row-' + index + '"><td><input class="fieldName filedNameLetter input-mini" type="text" name="property[' + index + '][name]" value="" /></td><td><input class="property input-xlarge" type="text" name="property[' + index + '][attributes]" value="" /></td><td><i class="icon-remove" data-row="row-' + index + '"></i></td> </tr>';
        $('table tbody').append(tpl);
    }
</script>
</div>
<?php echo $footer; ?>
