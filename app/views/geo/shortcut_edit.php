<?php echo $header; ?>
    <div class="">
        <?php echo \Utils\FormBuilderHelper::begin(); //注册表单JS ?>
        <form data-status="0" id="shortcut_form" class="form-horizontal" method="post">
            <fieldset>
                <legend>更新快捷键:#<?php echo $shortCut->name; ?></legend>
                <div class="form-group">
                    <label for="name" class="control-label">快捷键名称*:</label>

                    <div class="controls">
                        <input name="name" value="<?php echo $shortCut->name; ?>" class="form-control" type="text"
                               placeholder="快捷键名称" id="name">
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label" for="">屏蔽类型:</label>

                    <div class="controls">
                        <label class="radio inline">
                            <input type="radio"
                                   id="" <?php echo (int)$shortCut->type === 0 ? 'checked="checked"' : ''; ?> value="0"
                                   name="type" class=""> 普通屏蔽
                        </label>
                        <label class="radio inline">
                            <input type="radio" id=""
                                   value="1" <?php echo (int)$shortCut->type === 1 ? 'checked="checked"' : ''; ?>
                                   name="type" class=""> 强制屏蔽
                        </label>
                    </div>
                </div>
                <div class="form-group">
                    <label for="host" class="control-label">地域列表*:</label>

                    <div class="controls">
                        <ul class="area_city clearfix well" style="margin:0 0 10px 0;">
                            <?php foreach (Config::get('params.areaFilterList') as $areaCode => $areaName): ?>
                                <li style="float: left;list-style: none;width: 120px;">
                                    <label class="checkbox inline">
                                        <input <?php if (in_array($areaCode, $short)) echo 'checked="checked"' ?>
                                            type="checkbox" id="" name="geo[data][]"
                                            value="<?php echo $areaCode; ?>"> <?php echo $areaName; ?>
                                    </label>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                        <input type="hidden" id="shortcut" name="shortcut" value="<?php echo $shortCut->shortcut; ?>">
                    </div>
                </div>

                <div class="form-actions">
                    <button class="btn btn-primary" id="JS_Sub" type="submit">更新快捷键</button>
                    <a class="btn btn-warning" onclick="history.back()">取消</a>
                </div>
            </fieldset>
        </form>
        <script>
            function beforeSend() {
                if (!$('#shortcut').val()) {
                    alert('请选择地域列表');
                    $('#JS_Sub').attr('disabled', false);
                    return false;
                }
            }
        </script>
        <?php echo \Utils\FormBuilderHelper::staticEnd('shortcut_form',
            array( //表单规则
                'name'     => array('required' => true),
                'shortcut' => array('required' => true)
            ),
            URL::action('GeoController@shortcut_update', array('id' => $shortCut->id)),
            'PUT',
            'beforeSend()'
        );//注册表单JS
        ?>
    </div>
<?php echo $footer; ?>