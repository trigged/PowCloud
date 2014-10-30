<div class="">
    <form id="pathCreate" class="form-horizontal child_form" method="post" onsubmit="return check_form(this)">
        <div class="form-group">
            <label for="name" class="control-label col-sm-3">path*:</label>

            <div class="controls col-sm-8">
                <input name="name" class="form-control" value="" type="text" placeholder="路径名称" id="name">
            </div>
        </div>
        <!--        <div class="control-group">-->
        <!--            <label for="host" class="control-label">主机*:</label>-->

        <!--            <div class="controls">-->
        <!--                --><?php //echo Form::select('host_id', Host::getHostList(), array('id' => 'host_id')); ?>
        <!--            </div>-->
        <!--        </div>-->
        <!--        <div class="control-group">-->
        <!--            <label for="type" class="control-label">类型*:</label>-->
        <!--            <div class="controls">-->
        <!--                --><?php //echo Form::select('type',Path::$pathType,array('id'=>'type')); ?>
        <!--            </div>-->
        <!--        </div>-->
        <div class="form-group">
            <label for="expire" class="control-label col-sm-3">缓存时间:</label>

            <div class="controls col-sm-8">
                <input class="expire" name="expire" type="text" value="" placeholder="有效期" id="expire">
            </div>
        </div>
        <div class="pow_btn_horiz">
            <a class="btn btn-primary" id="addLeaf">创建</a>
            <a class="btn btn-warning" onclick="$('#JPathChildInfo').hide();" href="javascript:void (0)">取消</a>
        </div>
    </form>
</div>
<script>
    function check_form(form) {
        with (form) {
            if (!name.value) {
                alert('请添写路径');
                name.focus();
                return false;
            }
            if (name.value == '/') {
                alert('请正确填写路径');
                name.focus();
                return false;
            }
            if (!host_id.value) {
                alert('请选择主机');
                return false;
            }
        }
    }
</script>