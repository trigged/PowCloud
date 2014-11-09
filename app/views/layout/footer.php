</div>
</div>
</div>
<!--todo set resourcce to qiniu-->
<!-- Load JS here for greater good =============================-->
<script src="<?php echo URL::asset('js/jquery-ui-1.10.3.custom.min.js'); ?>"></script>
<script src="<?php echo URL::asset('js/jquery.ui.touch-punch.min.js'); ?>"></script>
<script src="<?php echo URL::asset('js/bootstrap.min.js'); ?> "></script>
<script src="<?php echo URL::asset('bower_components/bootstrap-select/dist/js/bootstrap-select.min.js'); ?>"></script>
<script src="<?php echo URL::asset('js/bootstrap-switch.js'); ?>"></script>
<script src="<?php echo URL::asset('js/jquery.tagsinput.js'); ?>"></script>
<script src="<?php echo URL::asset('js/jquery.placeholder.js'); ?>"></script>
<script src="<?php echo URL::asset('js/jquery.stacktable.js'); ?>"></script>
<script src="<?php echo URL::asset('js/application.js'); ?>"></script>
<!--<script src="--><?php //echo URL::asset('js/x-cms.js'); ?><!--"></script>-->
<script src="<?php echo URL::asset('js/cms.js'); ?>"></script>
<script>
    $(function () {
        CMS.init();

        <?php if(Session::has('messageTip')):?>
        <?php echo Session::get('messageTip');Session::remove('messageTip');?>
        <?php endif;?>

    });
</script>
<script>
    $(function () {
        var body_h = $("body").height();
        var window_h = $(window).height();
        var or_height = $(".sidebar-nav").height();
        var max_h = Math.max(body_h, window_h, or_height);
        if (or_height < max_h) {
            $(".sidebar-nav").height(max_h);
        }

        $(window).on('resize', function () {
            var body_h = $("body").height();
            var window_h = $(window).height();
            var or_height = $(".sidebar-nav").height();
            var max_h = Math.max(body_h, window_h, or_height);
            if (or_height < max_h) {
                $(".sidebar-nav").height(max_h);
            }
        });
    });

</script>
<script type="text/javascript">
    var _bdhmProtocol = (("https:" == document.location.protocol) ? " https://" : " http://");
    document.write(unescape("%3Cscript src='" + _bdhmProtocol + "hm.baidu.com/h.js%3Fb872713567b65c6d1ec9f3a3c8e9d490' type='text/javascript'%3E%3C/script%3E"));
</script>

</body>
</html>