<?php echo $header; ?>
    <div class="">
        <?php echo \Utils\FormBuilderHelper::begin(); //注册表单JS ?>
        <form id="host_form" class="form-horizontal" method="post">
            <fieldset>
                <legend>更新主机:<?php echo $host->name; ?></legend>
                <div class="form-group">
                    <label for="name" class="control-label">主机名称*:</label>

                    <div class="controls">
                        <input name="name" class="form-control" value="<?php echo $host->name; ?>" type="text"
                               placeholder="主机名称" id="name">
                    </div>
                </div>
                <div class="form-group">
                    <label for="host" class="control-label">主机地址*:</label>

                    <div class="controls">
                        <span class="form-control uneditable-input" disabled><?php echo $host->host; ?></span>
                    </div>
                </div>
                <div class="form-group">
                    <label for="comment" class="control-label">备注:</label>

                    <div class="controls">
                        <textarea rows="3" name="comment" value="<?php echo $host->comment; ?>" id="comment"
                                  style="width: 439px;"></textarea>
                    </div>
                </div>
                <div class="form-group">
                    <label for="expire" class="control-label">页面缓存时间:</label>

                    <div class="controls">
                        <input class="expire" name="expire" type="text" value="<?php echo $host->expire; ?>"
                               placeholder="有效期" id="expire">
                    </div>
                </div>
                <div class="form-group">
                    <label for="" class="control-label">是否开启CDN:</label>

                    <div class="controls">
                        <label class="radio inline">
                            <input class="" type="radio" <?php echo (int)$host->cdn === 1 ? 'checked=""' : ''; ?>
                                   name="cdn" value="1" id=""/> 开启
                        </label>
                        <label class="radio inline">
                            <input class="" type="radio"
                                   name="cdn" <?php echo (int)$host->cdn === 0 ? 'checked=""' : ''; ?>" value="0" id=""
                            /> 关闭
                        </label>
                    </div>
                </div>
                <div class="form-actions">
                    <button class="btn btn-primary" type="submit">更新主机</button>
                    <a class="btn btn-warning" onclick="history.back();">取消</a>
                </div>
            </fieldset>
        </form>
        <?php echo \Utils\FormBuilderHelper::staticEnd('host_form',
            array( //表单规则
                'name'   => array('required' => true),
                'host'   => array('required' => true),
                'expire' => array('digits' => true)
            ),
            URL::action('HostController@update', array('host' => $host->id)),
            'PUT'
        );//注册表单JS
        ?>
    </div>
<?php echo $footer; ?>