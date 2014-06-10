<div class="area">
    <div class="area_filter_type" style="margin-bottom:5px; ">
        <label class="radio inline">
            <input class="area_list_hide" type="radio"
                   name="<?php echo $name; ?>[type]" <?php echo (int)$type === 0 ? 'checked="checked"' : ''; ?>
                   value="0"> 无
        </label>
        <label class="radio inline">
            <input class="area_list_show" type="radio"
                   name="<?php echo $name; ?>[type]" <?php echo (int)$type === 1 ? 'checked="checked"' : ''; ?>
                   value="1"> 地域屏蔽
        </label>
        <label class="radio inline">
            <input class="area_list_show" type="radio"
                   name="<?php echo $name; ?>[type]" <?php echo (int)$type === 2 ? 'checked="checked"' : ''; ?>
                   value="2"> 地域定向
        </label>

        <input type="hidden" name="<?php echo $name; ?>[force]" value="<?php echo implode(',', $force); ?>">
    </div>
    <div class="area_list well <?php echo $name; ?>" id=""
         style="width: 800px;<?php if (!$data) echo 'display: none;'; ?>">
        <ul class="area_city clearfix" style="margin:0 0 10px 0;">
            <?php foreach (Config::get('params.areaFilterList') as $areaCode => $areaName): ?>
                <li style="float: left;list-style: none;width: 160px;">
                    <label class="checkbox inline">
                        <input type="checkbox" id=""
                               name="<?php echo $name; ?>[data][]" <?php echo in_array($areaCode, $data) ? 'checked="checked"' : ''; ?>
                               class="JGeoCheck" data-space="<?php echo $name; ?>"
                               value="<?php echo $areaCode; ?>"> <?php echo $areaName; ?>
                        <img width="15px;" data-space="<?php echo $name; ?>"
                             class="JGeoForce"
                             data-geo="<?php echo $areaCode; ?>"
                             data-select="<?php echo in_array($areaCode, $force) ? 1 : 0; ?>"
                             data-strong-select="<?php echo URL::asset('css/img/strongselect.png'); ?>"
                             data-strong="<?php echo URL::asset('css/img/strong.png'); ?>"
                             src="<?php echo in_array($areaCode, $force) ? URL::asset('css/img/strongselect.png') : URL::asset('css/img/strong.png'); ?>"
                            />
                    </label>
                </li>
            <?php endforeach; ?>
        </ul>
        <?php if ($shortCut): ?>
            <div class="area_quick" style="border-top: 4px solid #EEEEEE; ">
                <?php foreach ($shortCut as $sc): ?>
                    <label class="checkbox inline">
                        <span data-type="<?php echo $sc->type; ?>" data-check="<?php echo $sc->shortcut ?>"
                              class="area_quick_label label"><?php echo $sc->name; ?>
                            <?php if ((int)$sc->type === 1): ?>
                                <img data-strong-select="<?php echo URL::asset('css/img/strongselect.png'); ?>" data-strong="<?php echo URL::asset('css/img/strong.png'); ?>" width="17px;" class="JForce" src="<?php echo URL::asset('css/img/strong.png'); ?>"/>
                            <?php endif; ?>
                        </span>
                    </label>
                <?php endforeach; ?>
            </div>
            <script>
                $('.JForce').click(function(){
                    var dataStrongSelect = $(this).attr('data-strong-select');
                    var dataStrong = $(this).attr('data-strong');
                    var current = $(this).attr('src');
                    if(current == dataStrong){
                        $(this).attr('src',dataStrongSelect);
                        $(this).attr('data-select',1);
                    }else if(current == dataStrongSelect){
                        $(this).attr('src',dataStrong);
                        $(this).attr('data-select',0);
                    }
                });
                $('.area_list_hide').click(function () {
                    $(this).parent().parent().next('.area_list').hide();
                });

                $('.area_list_show').click(function () {
                    $(this).parent().parent().next('.area_list').show();
                });

                $('.area_quick_label').click(function (event) {
                    var checkData = $(this).attr('data-check');
                    var data = $(this).attr('data-check').split(',');
                    var force = $(this).attr('data-type') == 1 ? true : false;
                    var forceData = [];
                    if(!$(event.target).hasClass('label-info')){
                        $('input[name="<?php echo $name; ?>[data][]"]', $(this).parent().parent().prev()).each(function () {
                            if ($.inArray($(this).val(), data) != -1) {
                                $(this).attr('checked', 'true');
                                if (force && $(event.target).hasClass('JForce')) {
                                    $('span[data-check="'+checkData+'"]').addClass('label-info');
                                    if($(event.target).hasClass('JForce')){
                                        if($(event.target).attr('data-select')==1)
                                            $(this).next().attr('src', $(this).next().attr('data-strong-select')).attr('data-select', 1);
                                        else{
                                            $(this).next().attr('src', $(this).next().attr('data-strong')).attr('data-select', 0);
                                        }
                                    }
                                }
                                $(event.currentTarget).addClass('label-info');
                            }
                            if ($(this).next().attr('data-select') == 1) {
                                forceData.push($(this).next().attr('data-geo'));
                            }
                        });
                        if (forceData.length > 0)
                            $('input[name="<?php echo $name; ?>[force]"]').val(forceData.join(','));
                        else
                            $('input[name="<?php echo $name; ?>[force]"]').val('');
                        return false;
                    }else if($(event.target).hasClass('label-info')){
                        $('input[name="<?php echo $name; ?>[data][]"]', $(this).parent().parent().prev()).each(function () {
                            if ($.inArray($(this).val(), data) != -1) {
                                $(this).next().attr('src', $(this).next().attr('data-strong')).attr('data-select', 0);
                                $('span[data-check="'+checkData+'"]').removeClass('label-info');
                                $(this).attr('checked', false);
                                if(force)
                                    $('img',$(event.target)).attr('src',$('img',$(event.target)).attr('data-strong'));
                                $(event.target).removeClass('label-info');
                            }
                            if ($(this).next().attr('data-select') == 1) {
                                forceData.push($(this).next().attr('data-geo'));
                            }
                        });
                        if (forceData.length > 0)
                            $('input[name="<?php echo $name; ?>[force]"]').val(forceData.join(','));
                        else
                            $('input[name="<?php echo $name; ?>[force]"]').val('');
                    }

                });
            </script>
        <?php endif; ?>
    </div>
</div>

