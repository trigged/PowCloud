<?php echo $header; ?>
    <div class="note note-<?php  if ($code && $code == 1) {
        echo 'success';
    } else {
        echo 'warning';
    } ?>">
        <h4 class="block">提示</h4>
        <p>
            <?php echo $message ?>
        </p>
        <label id='time'></label>
        <label> 秒后自动跳转</label>
        <a href="<?php echo $url ?>"> 立即跳转</a>.

    </div>

    <script>
        var value = 5
        function fomtime() {
            value -= 1;
            console.log(value);
            if (value < 0) {
               return location = '<?php echo $url?>';
            }
            $("#time").html(value);
            setTimeout("fomtime()", 1000);
        }
        fomtime();
    </script>
<?php echo $footer; ?>