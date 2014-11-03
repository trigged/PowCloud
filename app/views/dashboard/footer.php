</div>
</div>
</div>
<!--todo set resourcce to qiniu-->
<!-- Load JS here for greater good =============================-->
<script src="<?php echo URL::asset('js/jquery-ui-1.10.3.custom.min.js'); ?>"></script>
<script src="<?php echo URL::asset('js/jquery.ui.touch-punch.min.js'); ?>"></script>
<script src="<?php echo URL::asset('js/bootstrap.min.js'); ?> "></script>
<script src="<?php echo URL::asset('js/bootstrap-select.js'); ?>"></script>
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
        //$('.JAvatar').identicon5({size: 45});
        <?php if(Session::has('messageTip')):?>
        <?php echo Session::get('messageTip');Session::remove('messageTip');?>
        <?php endif;?>
    });
</script>
<script>
    $(function () {
        var side_h = $(window).height();
        var or_height = $(".sidebar-nav").height();
        if (or_height < side_h) {
            $(".sidebar-nav").height(side_h);
        }
        console.log("##init or_height: ", or_height, " side_h: ", side_h);
        $(window).on('resize', function () {
            var side_h = $(window).height();
            var or_height = $(".sidebar-nav").height();
            console.log("##resize  or_height: ", or_height, " side_h: ", side_h);
            if (or_height < side_h) {
                $(".sidebar-nav").height(side_h);
            }
        });
    });

</script>
</body>
</html>