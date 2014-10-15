<?php echo $header; ?>
    <div class="row">
        <div class="">
            <?php echo \Utils\FormBuilderHelper::begin(); //注册表单JS ?>
            <form data-status="0" id="addAppForm" class="form-horizontal"
                  action="<?php echo URL::action('DashBoardController@storeApp') ?>" method="post">
                <fieldset>
                    <legend>添加应用</legend>
                    <div class="control-group">
                        <label for="name" class="control-label">应用名称*:</label>

                        <div class="controls">
                            <input name="name" class="input-medium" value="" type="text" placeholder="应用名称" id="name">
                        </div>
                    </div>
                    <div class="control-group">
                        <label for="app_id" class="control-label">信息&nbsp;:</label>

                        <div class="controls">
                            <input name="info" class="input-xxlarge" value="" type="text" placeholder="信息" id="info">
                            <input name="info" class="form-control col-md-6" value="" type="text" placeholder="信息"
                                   id="info">
                        </div>
                    </div>

                    <div class="form-actions">
                        <button id="JS_Sub" class="btn btn-primary">提交</button>
                        <a href="javascript:void (0)" class="btn" onclick="history.back();">取消</a>
                    </div>
                </fieldset>
            </form>
            <?php echo \Utils\FormBuilderHelper::staticEnd('addAppForm',
                array( //表单规则
                    'name' => array('required' => true),
                ),
                URL::action('DashBoardController@storeApp'),
                'POST'
            );//注册表单JS
            ?>
        </div>
    </div>
<?php echo $footer; ?>