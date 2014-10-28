<?php echo $header; ?>
    <div class="">
        <form class="form-horizontal child_form"
              action="<?php echo URL::action('SchemaBuilderController@tableOptions', array('table' => $table->id)) ?>"
              method="post" onsubmit="">
            <fieldset>
                <legend>配置表:<?php echo $table->table_name; ?></legend>
                <?php if ($message): ?>
                    <div class="alert alert-success">
                        <button type="button" class="close" data-dismiss="alert">&times;</button>
                        修改成功
                    </div>
                <?php endif; ?>

                <div class="form-group">
                    <label for="" class="control-label">数据列表是否显示ID:</label>

                    <div class="controls">
                        <label class="radio inline">
                            <input type="radio"
                                   name="options[list.table.id_display]" <?php echo !empty($options['list.table.rank']) && $options['list.table.id_display'] == 1 ? 'checked="checked"' : ''; ?>
                                   id="" value="1"> 显示
                        </label>
                        <label class="radio inline">
                            <input type="radio"
                                   name="options[list.table.id_display]" <?php echo empty($options['list.table.rank']) || (!empty($options['list.table.rank']) && $options['list.table.id_display'] == 2) ? 'checked="checked"' : ''; ?>
                                   id="" value="2"> 隐藏
                        </label>
                        <label class="radio inline">
                            <input type="radio"
                                   name="options[list.table.id_display]" <?php echo !empty($options['list.table.rank']) && $options['list.table.id_display'] == 3 ? 'checked="checked"' : ''; ?>
                                   id="" value="3"> 自动排序
                        </label>
                        <label class="radio inline">
                            <input type="radio"
                                   name="options[list.table.id_display]" <?php echo !empty($options['list.table.rank']) && $options['list.table.id_display'] == 4 ? 'checked="checked"' : ''; ?>
                                   id="" value="4"> 自动排序＋数据ID
                        </label>
                    </div>
                </div>
                <div class="form-group">
                    <label for="" class="control-label">数据列表是可排序:</label>

                    <div class="controls">
                        <label class="radio inline">
                            <input type="radio"
                                   name="options[list.table.rank]" <?php echo empty($options['list.table.rank']) || (!empty($options['list.table.rank']) && $options['list.table.rank'] == 1) ? 'checked="checked"' : ''; ?>
                                   id="" value="1"> 开启
                        </label>
                        <label class="radio inline">
                            <input type="radio" name="options[list.table.rank]"
                                   <?php echo !empty($options['list.table.rank']) && $options['list.table.rank'] == 2 ? 'checked="checked"' : ''; ?>id=""
                                   value="2"> 禁止
                        </label>
                    </div>
                </div>

                <div class="form-group">
                    <label for="" class="control-label">显示修改人:</label>

                    <div class="controls">
                        <label class="radio inline">
                            <input type="radio"
                                   name="options[list.table.modifier]" <?php echo !empty($options['list.table.modifier']) && $options['list.table.modifier'] == 1 ? 'checked="checked"' : ''; ?>
                                   id="" value="1"> 显示
                        </label>
                        <label class="radio inline">
                            <input type="radio" name="options[list.table.modifier]"
                                   <?php echo empty($options['list.table.modifier']) || $options['list.table.modifier'] == 2 ? 'checked="checked"' : ''; ?>id=""
                                   value="2"> 隐藏
                        </label>
                    </div>
                </div>

                <div class="form-actions">
                    <button class="btn btn-primary" type="submit">设置</button>
                    <a class="btn  btn-warning" onclick="history.back();">取消</a>
                </div>
            </fieldset>
        </form>
    </div>
<?php echo $footer; ?>