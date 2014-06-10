<div class="area">
    <div class="area_filter_type" style="margin-bottom:5px; ">
        <label class="radio inline">
            <input class="area_list_hide"  type="radio" name="<?php echo $name; ?>[type]" <?php echo (int)$type===0?'checked="checked"':''; ?> value="0"> 无
        </label>
        <label class="radio inline">
            <input class="area_list_show" type="radio" name="<?php echo $name; ?>[type]" <?php echo (int)$type===1?'checked="checked"':''; ?> value="1"> 地域屏蔽
        </label>
        <label class="radio inline">
            <input class="area_list_show" type="radio" name="<?php echo $name; ?>[type]" <?php echo (int)$type===2?'checked="checked"':''; ?> value="2"> 地域定向
        </label>
    </div>
    <div  class="area_list well <?php echo $name; ?>" id=""  <?php if(!$data) echo 'style="display: none;"'; ?>>
        <ul class="area_city clearfix" style="margin:0 0 10px 0;">
            <?php foreach(Config::get('params.areaFilterList') as $areaCode=>$areaName):?>
                <li style="float: left;list-style: none;width: 120px;">
                    <label class="checkbox inline">
                        <input type="radio" id="" name="<?php echo $name; ?>[data]" <?php echo ($areaCode==$data)?'checked="checked"':''; ?> value="<?php echo $areaCode;?>"> <?php echo $areaName;?>
                    </label>
                </li>
            <?php endforeach; ?>
        </ul><?php ;?>
        <script>

            $(function () {
                $("input[type=radio][name='geo[type]']").click(function () {
                    if ($(this).val() != 0) {
                        $(this).parent().parent().next('.area_list').show();
                    }
                });
                $('.area_list_hide').click(function(){
                    $(this).parent().parent().next('.area_list').hide();
                });
                $("input[type=radio][name='geo[data]']").click(function() {
                    $(this).closest("form").submit();
                });
            });
        </script>
    </div>
</div>

