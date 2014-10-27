<?php echo $header; ?>
<div class="row">
    <div class="col-md-7">
        <?php echo \Utils\FormBuilderHelper::begin(); //注册表单JS ?>
        <form id="schema_form" class="form-horizontal" method="post">
            <h4>添加表(<?php echo $table->table_name; ?>)字段</h4>
            <hr/>
            <div class="form-group">
                <label for="field" class="control-label">字段名*:</label>

                <div class="controls">
                    <input name="field" class="filedNameLetter col-md-4" type="text" placeholder="字段名" id="field">
                </div>
            </div>
            <div class="form-group">
                <label for="property" class="control-label">属性*:</label>

                <div class="controls">
                    <input name="property" class="form-control" type="text" placeholder="属性" id="property"
                           data-provide="typeahead" autocomplete="off" data-source='["string","integer","double",
                                       "text","datetime","decimal"]'>
                </div>
            </div>

            <div class="form-actions">
                <button class="btn btn-primary" id="JS_Sub" type="submit">添加字段</button>
                <a href="javascript:void (0)" class="btn" onclick="history.back();">取消</a>
            </div>

        </form>
        <?php echo \Utils\FormBuilderHelper::staticEnd('schema_form',
            array( //表单规则
                'field'    => array('required' => true),
                'property' => array('required' => true),
            ),
            URL::action('SchemaBuilderController@addField', array('table' => $table->id)),
            'POST',
            '',
            '
            $.validator.addMethod("filedNameLetter", function(value, element) {
                  return parseInt(value.toLowerCase().charCodeAt())>=97 && parseInt(value.toLowerCase().charCodeAt())<=122;
            },"字段名只能是字母");

            '
        );//注册表单JS
        ?>
    </div>
</div>
<?php echo $footer; ?>
