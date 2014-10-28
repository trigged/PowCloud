<?php echo $header; ?>
    <div class="" xmlns="http://www.w3.org/1999/html">
        <?php echo \Utils\FormBuilderHelper::begin(); //注册表单JS ?>
        <form data-status="0" id="shortcut_form" class="form-horizontal" method="post">
            <fieldset>
                <div class="note note-success">
                    <h4 class="block">:)</h4>

                    <p>
                        在这里填写的是主数据源,创建完成后可以编辑此数据,然后就可以和其他数据建立链接关系了
                    </p>
                </div>
                <legend>数据链接</legend>
                <div class="form-group">
                    <label for="name" class="control-label">表名(不是表别名!):</label>

                    <div class="controls">
                        <input name="table_name" class="form-control" type="text" placeholder="数据变更" id="filed">
                    </div>
                </div>
                <div class="form-group">
                    <label for="name" class="control-label">数据ID:</label>

                    <div class="controls">
                        <input name="data_id" class="form-control" type="text" placeholder="数据变更" id="filed">
                    </div>
                </div>

                <div class="form-actions">
                    <button class="btn btn-primary" id="JS_Sub" type="submit">创建链接</button>
                    <a class="btn  btn-warning" onclick="history.back()">取消</a>
                </div>
            </fieldset>
        </form>
        <script>

        </script>
        <?php echo \Utils\FormBuilderHelper::staticEnd('shortcut_form',
            array( //表单规则
                'filed' => array('required' => true),
            ),
            URL::action('DataLinkController@store'),
            'POST'
        );//注册表单JS
        ?>
    </div>
<?php echo $footer; ?>