<div class="">
    <form id="pathForm" class="form-horizontal" method="post" onsubmit="return check_form(this)">
        <div class="form-group">
            <label for="name" class="control-label">path*:</label>

            <div class="controls">
                <input name="name" readonly class="form-control" value="<?php echo $path->name; ?>" type="text"
                       placeholder="主机名称" id="name">
            </div>
        </div>
        <?php if ((int)$path->id !== 0): ?>
            <div class="form-group">
                <label for="host" class="control-label">主机*:</label>

                <div class="controls">
                    <?php echo Form::select('host_id', Host::getHostList(), $path->host_id); ?>
                </div>
            </div>
        <?php endif; ?>
        <div class="form-group">
            <label for="expire" class="control-label">缓存时间:</label>

            <div class="controls">
                <input class="expire" name="expire" type="text" value="<?php echo $path->expire; ?>" placeholder="有效期"
                       id="expire">
            </div>
        </div>
        <div class="form-actions">
            <?php if ((int)$path->id !== 0): ?>
                <!--                <button class="btn btn-primary" type="submit">更新</button>-->
            <?php endif; ?>
            <a class="btn" onclick="addChild();">创建子路径</a>
            <a class='btn' onclick="update()">更新路径</a>
            <!--            --><?php //if ((int)$path->id !== 0): ?>
            <!--                <a id="remove" class="btn" data-url="-->
            <?php //echo URL::action('PathController@destroy',array('path'=>$path->id)); ?><!--" >删除路径</a>-->
            <!--            --><?php //endif; ?>
        </div>
    </form>
</div>

<div id="JPathChildInfo" class="hide">
    <fieldset>
        <legend>创建<?php echo $path->name; ?>子路径</legend>
        <div id="JPathChild"></div>
    </fieldset>
</div>
<script>
    function addChild() {
        $.ajax({
            url: '<?php echo URL::action('PathController@create'); ?>',
            type: 'GET',
            success: function (re) {
                $('#JPathChildInfo').show();
                $('#JPathChild').html(re);

            }
        });

        return false;
    }
    function update() {
        $.ajax({
            url: '<?php echo URL::action('PathController@update',array('path'=>$path->id)); ?>',
            type: 'PUT',
            data: $('#pathForm').serialize(),
            success: function (re) {
                var reObj = $.parseJSON(re);
                if (reObj.status == 'success')
                    alert('更新成功');
                else
                    alert(reObj.message)
            }
        });
    }

    function check_form(form) {

    }
</script>