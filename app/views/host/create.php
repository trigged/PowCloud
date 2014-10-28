<?php echo $header; ?>
<div class="col-md-8" style="margin-left: 10px;">
    <?php echo \Utils\FormBuilderHelper::begin(); //注册表单JS ?>
    <form data-status="0" id="host_form" class="form-horizontal" method="post"
    ">
    <fieldset>
        <legend>新建主机</legend>
        <div class="form-group">
            <label for="name" class="control-label">主机名称*:</label>

            <div class="controls">
                <input name="name" class="form-control" type="text" placeholder="主机名称" id="name">
            </div>
        </div>
        <div class="form-group">
            <label for="host" class="control-label">主机地址*:</label>

            <div class="controls">
                <input class="form-control" name="host" type="text" placeholder="xxx.xxx.xx.com" id="host">
            </div>
        </div>
        <div class="form-group">
            <label for="comment" class="control-label">备注:</label>

            <div class="controls">
                <textarea rows="3" name="comment" id="comment" style="width: 439px;"></textarea>
            </div>
        </div>
        <div class="form-group">
            <label for="expire" class="control-label">页面缓存时间:</label>

            <div class="controls">
                <input class="expire" value="3600" name="expire" type="text" placeholder="有效期" id="expire">
            </div>
        </div>
        <div class="form-group">
            <label for="" class="control-label">是否开启CDN:</label>

            <div class="controls">
                <label class="radio inline">
                    <input class="" type="radio" name="cdn" value="1" id=""/> 开启
                </label>
                <label class="radio inline">
                    <input class="" type="radio" name="cdn" checked="checked" value="0" id=""/> 关闭
                </label>
            </div>
        </div>
        <div class="form-actions">
            <button class="btn btn-primary" id="JS_Sub" type="submit">创建主机</button>
            <a class="btn btn-warning" onclick="history.back()">取消</a>
        </div>
    </fieldset>
    </form>
    <?php echo \Utils\FormBuilderHelper::staticEnd('host_form',
        array( //表单规则
            'name'   => array('required' => true),
            'host'   => array('required' => true),
            'expire' => array('digits' => true)),
        URL::action('HostController@store')
    );//注册表单JS
    ?>
</div>
<?php echo $footer; ?>
