</div>
</div>
</div>
<!--todo set resourcce to qiniu-->
<!-- Load JS here for greater good =============================-->
<script src="<?php echo URL::asset('js/jquery-ui-1.10.3.custom.min.js'); ?>"></script>
<script src="<?php echo URL::asset('js/jquery.ui.touch-punch.min.js'); ?>"></script>
<script src="<?php echo URL::asset('js/bootstrap.min.js'); ?> "></script>
<script src="<?php echo URL::asset('js/bootstrap-select.js'); ?>"></script>
<script src="<?php echo URL::asset('js/bootstrap-switch.js'); ?>"></script>
<script src="<?php echo URL::asset('js/jquery.tagsinput.js'); ?>"></script>
<script src="<?php echo URL::asset('js/jquery.placeholder.js'); ?>"></script>
<script src="<?php echo URL::asset('js/jquery.identicon5.js'); ?>"></script>
<script src="<?php echo URL::asset('js/jquery.stacktable.js'); ?>"></script>
<script src="<?php echo URL::asset('js/application.js'); ?>"></script>
<script src="<?php echo URL::asset('js/x-cms.js'); ?>"></script>
<script src="<?php echo URL::asset('js/cms.js'); ?>"></script>
<script>
    $(function () {
        CMS.init();
        $('.JAvatar').identicon5({size: 45});
        <?php if(Session::has('messageTip')):?>
        <?php echo Session::get('messageTip');Session::remove('messageTip');?>
        <?php endif;?>
    });
</script>
<script>
    $(function () {
        var side_h = $(document.body).height();
        $(".sidebar").height(side_h);
    })

</script>
</body>
</html>