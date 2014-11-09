<?php namespace Utils;


class FormBuilderHelper
{

    static $validateRules = array();

    static $namespace = '';

    public static function begin($namespace = '', $scene = '')
    {
        //注册表单命名空间
        self::$namespace = $namespace;
        return '<script src="' . \URL::asset('js/validate/jquery.validate.min.js') . '"></script>' . '<script src="' . \URL::asset('js/validate/messages_zh.js') . '"></script>';
    }

    /**
     * 为了加载validate脚本 用于动态生成的表单
     * @param $formId
     * @param $success
     * @return string
     */
    public static function  end($formId = '', $success = '')
    {
        $ruleConfig = '';
        $messageConfig = '';
        $js = <<<EOT
        <script>
        $().ready(function() {
            $.validator.setDefaults({
                errorClass:'error help-inline',
                errorElement : 'span',
                errorPlacement:function(error,element){
                    element.parent().parent().addClass('error');
                    error.appendTo(element.parent());
                },
                success:function(label){
                    label.parent().parent().removeClass('error').addClass('success');
                    label.html('<i class="glyphicon glyphicon-ok"></i>');
                }
            });
            var {$formId}Validate = $("#{$formId}").validate({
EOT;
        if ($success) {
            $js .= "\n submitHandler:function(form){ {$success}(form,{$formId}Validate); },";
        }
        if (self::$validateRules) {
            foreach (self::$validateRules as $name => $rule) {
                $ruleConfig .= "\n" . '"' . $name . '":{' . $rule['rule'] . '},';
                if (isset($rule['message'])) {
                    $messageConfig .= "\n" . '"' . $name . '":{' . $rule['message'] . '},';
                }
            }

            $ruleConfig = "\n rules:{" . $ruleConfig . '}';
            if ($messageConfig)
                $messageConfig = ",\n messages: { " . $messageConfig . "} \n";
        }
        $js_tail = <<<EOT
            });
        });
        </script>
EOT;
        return $js . $ruleConfig . $messageConfig . $js_tail . "\n";
    }

    public static function registerValidateRules($field, $value)
    {

        // $flag = false;
        $rule = explode("\n##message##", $value);
        $field = self::getFieldName($field);

        self::$validateRules[$field]['rule'] = str_replace("\n", ',', trim(trim($rule[0]), "\n"));
        if (!empty($rule[1]))
            self::$validateRules[$field]['message'] = str_replace("\n", ',', trim(trim($rule[1]), "\n"));
    }

    /**
     * 获取命名空间
     * @param $field
     * @return string
     */
    protected static function getFieldName($field)
    {

        return self::$namespace ? self::$namespace . '[' . $field . ']' : $field;
    }

    public static function text($form, $value = '', $class = "form-control")
    {
        $value = is_array($value) ? json_encode($value) : $value;

//        $class = $form->dataType == 'Integer' ? ' input-mini' : '';

        $name = self::getFieldName($form->field);

        $htmlOption = array('id' => self::getFiledId($form->field), 'class' => $class);

        if ((int)$form->isEditable === 0)
            $htmlOption['readonly'] = 'readonly';

        return \Form::text($name,
            $value === 0 || $value ? $value : $form->default_value,
            $htmlOption
        );
    }

    protected static function getFiledId($field)
    {
        return self::$namespace ? preg_replace('#\[|\]#', '_', trim(self::$namespace, ']')) . '_' . $field : $field;
    }

    public static function select($form, $selected = null)
    {
        $optionsArray = explode(',', $form->default_value);
        $list = array();
        $options = array();
        if ($optionsArray) {
            foreach ($optionsArray as $index => $option) {
                if (($pos = strpos($option, ':')) !== false) {
                    $display = substr($option, 0, $pos);
                    $o = substr($option, $pos + 1);
                    $list[] = $display;
                    $options['data-' . $index] = $o;
                } else
                    $list [] = $option;
            }
        }

        return \Form::select(self::getFieldName($form->field), $list, $selected, array('id' => self::$namespace . '_' . $form->field, 'class' => 'required') + $options);
    }

    public static function radio($form)
    {

    }

    public static function checkbox($form)
    {

    }

    public static function textQuick($form, $value = '')
    {

        $js = <<<EOT
        <script>
        $(function(){
            $("#textQuick").tagsInput({width:'auto',unique:false});
        })
        function addTag(val){
            $("#textQuick").addTag(val,{unique:false});
        }
        </script>
EOT;
        $input = '<input name="' . self::getFieldName($form->field) . '" id="textQuick" type="text" class="tags" value="' . $value . '" />';
        $label = '';
        if ($form->default_value) {
            $valueArray = array_filter(explode(',', $form->default_value));
            foreach ($valueArray as $val) {
                $label .= '<label onClick="addTag(\'' . $val . '\')" class="large-checkbox"><span class="large-label" data="' . $val . '">' . $val . '</span></label>';
            }

        }
        return $js . $input . $label;
    }

    public static function textArea($form, $value = '', $class = "form-control")
    {
        $value = is_array($value) ? json_encode($value) : $value;
        $name = self::getFieldName($form->field);

        $htmlOption = array('id' => self::getFiledId($form->field), 'class' => $class);

        if ((int)$form->isEditable === 0)
            $htmlOption['readonly'] = 'readonly';

        return \Form::textarea($name,
            $value === 0 || $value ? $value : $form->default_value,
            $htmlOption
        );
    }

    public static function image($form, $value = '', $class = "form-control")
    {
        $value = $value ? $value : $form->default_value;
        $input = '';
        if (stripos($form->dataType, 'Array') !== false) {
            return self::imageArray($form, $value, $class);
        } else {

            return '<input class="form-control"  type="text" name="' . self::getFieldName($form->field) . '" placeholder="有效图片地址" value="' . $value . '"  class="' . $class . ' image-uploader" data-validate="' . $form->field . '" />';
        }
    }

    public static function imageArray($form, $value, $class = "form-control")
    {
        $input = '';
        if ($value && is_array($value)) {
            foreach ($value as $v) {
                $input .= '<input  type="text" name="' . self::getFieldName($form->field) . '[]" placeholder="有效图片地址" value="' . $v . '"  class="' . $class . ' image-uploader" />';
            }
        } else
            $input = '<input type="text" name="' . self::getFieldName($form->field) . '[]" placeholder="有效图片地址" value="' . $value . '"  class="' . $class . ' image-uploader"  />';

        $js_link = '<a class="JS_repeat" title="继续添加" href="javascript:void(0) "><i class="glyphicon glyphicon-plus"></i></a>';
        $js_remove = '<a class="JS_remove" title="继续添加" href="javascript:void(0) "><i class="glyphicon glyphicon-remove"></i></a>';
        $js = " <script>
        $(function(){
            $('.JS_repeat').click(function(){
                var html = $(this).prev().clone();
                html.attr('value','');
                $(this).before(html);
            });
            $('.JS_remove').click('click' , function() {
                 $(this).prev().prev().remove();
//                 $(this).remove();

                return false;
            });
        })
    </script>";
        return $input . $js_link . $js_remove . $js;
    }

    public static function upload2QN($form, $value, $class = '')
    {

        $input = '<input type="file" name="' . self::getFieldName($form->field) . '[]" />';

        return $input;
    }

    public static function generateRandomNum($form = '', $value = '')
    {
        if (!empty($value)) {
            list($begin, $end, $random) = explode(":", $value);
        }

        $input = '<span>开始</span><input class="input-mini" type="text" id="begin" value="' . (isset($begin) ? $begin : '') . '"/><span>结束</span><input class="input-mini" type="text" id="end" value="' . (isset($end) ? $end : '') . '"/><input id="show-random" class="input-mini" disabled type="text" value="' . (isset($random) ? $random : '') . '" /><button id="generate" class="btn randomBtn btn-primary">产生</button>';
        $input .= '<input type="hidden" value="' . $value . '" name="' . self::getFieldName($form->field) . '" />';
        $js = '<script>$(function () {$("#generate").click(function (event) {event.preventDefault(); minNum = $("#begin").val();if (!$.isNumeric(minNum)) {alert("请输入数字");$("#begin").val("");return;} maxNum=$("#end").val();if (!$.isNumeric(maxNum)) {alert("请输入数字！");$("#end").val("");return;} if (minNum >= maxNum) {alert("最小值大于最大值，请重新输入！"); $("#begin, #end").val("");return;} minNum = parseInt(minNum);maxNum = parseInt(maxNum); randomNum = Math.floor(Math.random() * (maxNum - minNum +1)) + minNum;$("#show-random").val(randomNum);$("input[name=\'' . self::getFieldName($form->field) . '\']").prop("disabled",false).val(minNum+":"+maxNum+":"+randomNum)}); });</script>';

        return $input . $js;

    }

    /**
     * @param $formId
     * @param array $rules
     * @param string $url
     * @param string $requestMethod
     * @param string $beforeSend  发送之前的事件
     * @param string $method
     * @return string
     */
    public static function staticEnd($formId, $rules = array(), $url = '', $requestMethod = 'POST', $beforeSend = 'beforeSend', $method = '')
    {

        $rules = json_encode($rules);
        $beforeSend = $beforeSend ? $beforeSend : 'beforeSend'; //设置默认回调
        return $javascript = <<<EOT
    <script>
        $().ready(function(){
            $.validator.setDefaults({
                errorClass:'error help-inline',
                errorElement : 'span',
                errorPlacement:function(error,element){
                    element.parent().parent().addClass('error');
                    error.appendTo(element.parent());
                },
                success:function(label){
                    label.parent().parent().removeClass('error').addClass('success');
                    label.html('<i class="glyphicon glyphicon-ok"></i>');
                }
            });
            {$method}
            var validate = $("#{$formId}").validate({
                rules:{$rules},
                submitHandler:function(form){
                    $.ajax({
                        url  : '{$url}',
                        data : $(form).serialize(),
                        type : '{$requestMethod}',
                        beforeSend : function(){
                            var bs = '{$beforeSend}'?(typeof({$beforeSend})=='function'?true:false):false;
                            if(bs){
                               return {$beforeSend};
                            }
                            $('#JS_Sub').attr('disabled',true);
                        },
                        success: function(re){
                            console.log("re",re)
                            re = $.parseJSON(re);
                            if(re.status){
                                 var errors = re.data;
                                 if(!errors){
                                     errors = re.message
                                 }
                                 if(errors){
                                try
                                    {
                                       errors = JSON.parse(this.responseText);
                                       console.log("json")
                                    }
                                    catch(e)
                                    {
                                        console.log("not json")
                                    }
                                     $('#alerts').on('closed.bs.alert', function () {
                                         if(re.redirect){
                                            location.href = re.redirect;
                                         }

                                     })
                                    if($('#alerts').length >0 ){
                                         setTimeout(function(){
                                           if(re.redirect){
                                                location.href = re.redirect;
                                            }
                                         }, 3000);

                                        if(re.status == 1){
                                            $("#alerts").attr('class',"alert alert-dismissible  alert-success");
                                        }
                                        else{
                                             $("#alerts").attr('class',"alert alert-dismissible  alert-danger");
                                        }


                                        $("#alert_title").html(re.message);
                                        $("#alert_content").html(re.data);
                                        $("#alerts").fadeTo(4000, 500).slideUp(500, function(){
                                                $("#alerts").alert();
                                            });
                                    }
                                    else{
                                        alert(errors);
                                    }
                                }
                                $('#JS_Sub').attr('disabled',false);
                            }
                            if(re.redirect){
                              if($('#alerts').length <=0 ){
                                 if(re.redirect){
                                    location.href = re.redirect;
                                 }
                              }

                            }

                        }
                    });
                    return false;
                }
            });
        });
    </script>
EOT;
    }

    /**
     * 加载地域屏蔽或者地域定向
     * @param string $forms
     * @param string $value
     * @return string
     */
    public static function areaFilter($form, $value = '')
    {

        //兼容老老数剧
        if (!isset($value['force']) && !empty($value['type']) && (int)$value['type'] === 1)
            $value['force'] = array_keys(\Config::get('params.areaFilterList'));
        $query = 'namespace=' . self::$namespace;
        if ($value)
            $query .= '&' . http_build_query($value);
        $filed = '<div class="area_filter"></div>';
        $requestUrl = \URL::action('ExtController@areaList');
        $filed .= <<<EOT
<script>
    $(function(){
        /**
         *加载城市过滤列表
         */
        $('.area_filter').bind("area_filter_list",function(event,url,method,data){
            $.ajax({
                url  : url,
                data : data,
                type : method,
                success: function(re){
                    $('.area_filter').html(re);
                }
            });
        });

        $('.area_filter').trigger('area_filter_list',["{$requestUrl}","GET","{$query}"])
    });
</script>
EOT;
        return $filed;

    }

    public static function colorSelect($form, $value = 0)
    {

        $selectColorJs = <<<EOT
        <script>
            $(function(){
                $(".selectColor li").click(function(){
                    var value = $(this).attr('data-value');
                    var target = $(this).attr('data-target');
                    $(".selectColor li .glyphicon glyphicon-ok").addClass('hide');
                    $('.glyphicon glyphicon-ok',$(this)).removeClass('hide');
                    $("#"+target).val(value);
                });
            });
        </script>
EOT;

        $colorSelect = '';
        $name = self::getFieldName($form->field);
        $id = preg_replace('#\[|\]#is', '_', $name);
        $id = trim($id, '_');
        if ($form->default_value) {
            if ($colors = explode(',', $form->default_value)) {
                foreach ($colors as $index => $color) {
                    $colorSelect .= '<li data-target="' . $id . '" data-value="' . $index . '" style="' . ($color == '无' ? '' : 'background:' . $color . ';') . ';"><i class="glyphicon glyphicon-ok ' . ((int)$value === (int)$index ? '' : ' hide') . '"></i>' . ($color == '无' ? '无' : '') . '</li>' . "\n";
                }
            }
        }
        $hiddenField = '<input id="' . $id . '" type="hidden" name="' . $name . '" value="' . $value . '" />';
        return '<ul class="selectColor">' . "\n" . $colorSelect . "\n</ul>\n" . $hiddenField . "\n" . $selectColorJs . "\n";
    }

    public static function formTip($form)
    {
        $content = '';
        $html = '<div class="note note-success"><h4 class="block">注意</h4>{content}</div>';
        if ($form->default_value && strpos($form->default_value, ';') !== false) {
            $valueArray = explode(';', $form->default_value);
            foreach ($valueArray as $value) {
                $content .= '<p>' . $value . '</p>';
            }
        } elseif ($form->default_value && strpos($form->default_value, ';') === false) {
            $content = '<p>' . $form->default_value . '</p>';
        }
        if ($content)
            return str_replace('{content}', $content, $html);

        return '';
    }

    public static function dateTimePicker($form, $value = '')
    {
        $startTime = date('Y-m-d H:i', time());
        $timestamp = $value ? strtotime($value) : '';

        $start = $value ? date('Y-m-d', $timestamp) : '';
        $hour = $value ? date('H', $timestamp) : '00';
        $minute = $value ? date('i', $timestamp) : '00';

        $css = '<link href="' . \URL::asset('css/datetimepicker.css') . '" rel="stylesheet">';
        $js = '<script src="' . \URL::asset('js/bootstrap-datetimepicker.min.js') . '"></script>';

        $timingTime = self::getFieldName('timing_time');
        $timingStatus = $timing_state = self::getFieldName('timing_state');
        $checkScript = '';
        if ($value) {
            $checkScript = <<<CHECKSCRIPT
<script>
    $('#JSCheckEnable').attr('checked','checked');
    $("#JSCheckEnable").trigger('click');
    $('#JSCheckEnable').attr('checked','checked');
</script>
CHECKSCRIPT;
        }
        $script = <<<EOT
<script type="text/javascript">
    $('#JSCheckEnable').click(function(){
       console.log("checked: ",this.checked);
        if(this.checked){
            $('input[name="{$timingTime}[start]"],#start_hour,#start_minute').attr("disabled",false);
            $('.timing-radio').css("display",'block');
            $("#start_date").datetimepicker({
                format: 'yyyy-mm-dd',
                linkField: "start_hour",
                linkFormat: "hh",
                minuteStep:1,
                autoclose:true,
                minView:1,
                todayBtn: true,
                startDate: "{$startTime}",
            }).on('change', function () {
                $('input[name="{$timingTime}[start]"],#start_hour,#start_minute').attr("disabled",false);
                $('input[name="{$timingStatus}"]').attr('checked','checked');

                $('#JSCheckEnable').prop('checked','true');
                var dt = new Date();
                var month = ('0'+(dt.getMonth() + 1)).slice(-2);
                var day = dt.getDate();
                var year = dt.getFullYear();
                var minute = dt.getMinutes();
                var hour = ('0'+dt.getHours()).slice(-2);

                if ((year+'-'+month+'-'+day == $('#start_date').find('input').val()) && (hour == $('#start_hour').val()) && minute > 0) {
                    alert('创建定时时间大于当前时间，请重新设置');
                    $('input[name="{$timingTime}[start]"],#start_hour').val("");
                }
            });
        }
        else{
            $('input[name="{$timingTime}[start]"],#start_hour,#start_minute').attr("disabled","disabled");
            $('.timing-radio').css("display",'none');

        }

    });

</script>
EOT;


        $dataPicker = sprintf('<div class="input-group date form_datetime  col-md-5" id="start_date" >
        <input size="16" class="form-control" type="text" name="%s[start]" value="%s" disabled="disabled">
        <span class="input-group-addon"><span class="glyphicon glyphicon-th"></span></span>
        </div>
        <input  class="form-control" size="2" id="start_hour" type="text" name="%s[hour]" value="%s" disabled="disabled">时

        <input  class="form-control"  style="width: 50px;"   size="2" id="start_minute" type="text" name="%s[minute]" value="%s" disabled="disabled">
        分<input type="checkbox" id="JSCheckEnable"  value=1> <span class="help-inline"> 启用</span>&nbsp;&nbsp;选填
        ', $timingTime, $start, $timingTime, $hour, $timingTime, $minute);

        return $css . "\n" . $js . "\n" . $dataPicker . "\n" . $script . $checkScript;

    }

    public static function timingState($form, $value = '', $model = '')
    {
        $timingState = '<div style="display:none" class="timing-radio">
        <label class="inline radio"><input type="radio" name="' . self::getFieldName($form->field) . '" id="optionsRadios1" value="4" data-toggle="radio" ' . (($value == 4) ? 'checked' : '') . '>上线</label>
        <label style="display:' . ($value == \Operator\RedisKey::READY_LINE && !empty($model) && !$model->hasNormalData() ? 'none' : '') . '" class="inline radio"><input  style="display:' . ($value == \Operator\RedisKey::READY_LINE && !empty($model) && !$model->hasNormalData() ? 'none' : '') . '" type="radio" name="' . self::getFieldName($form->field) . '" id="optionsRadios2" value="5" data-toggle="radio" ' . (($value == 5) ? 'checked' : '') . '>下线</label>
        <label class="inline radio"><input type="radio" name="' . self::getFieldName($form->field) . '" id="optionsRadios2" value="6" data-toggle="radio" ' . (($value == 6) ? 'checked' : '') . '>置顶</label>
        </div>';

        $js = '<script>$(function () {var checked_val;
        checked_val = $("div.timing-radio input[type=radio]:checked").val();
         if (checked_val) {$(".timing-radio").css("display","block");
         $("#JSCheckEnable").prop("disabled", true);
         $("div.timing-radio input[type=radio]").each(function () {$(this).prop("disabled", true)})
         ;}})</script>';

        return $timingState . $js;

    }

    public static function ajaxInput($form, $value = '')
    {

        $options = $form->default_value ? json_decode($form->default_value, true) : array('data' => '', 'target' => '',);

        return '<input class="input-mini"  value="' . $value . '" type="text" name="' . self::$namespace . '[' . $form->field . ']" ><button data-data="' . (isset($options['data']) ? $options['data'] : '') . '" data-target="' . (isset($options['target']) ? $options['target'] : '') . '" data-field="' . $form->id . '" data-url="' . \URL::action('ExtController@ajax_analyse') . '" data-namespace="' . self::$namespace . '" data-table="' . $form->models_id . '" class="btn  btn-primary ajaxBtn ajaxBtn_analyse" type="button">分析</button>';
    }

    public static function hidden($form, $value = '')
    {
        $value = $value !== '' ? $value : $form->default_value;
        return '<input type="hidden" value="' . $value . '" name="' . self::getFieldName($form->field) . '" />';
    }
}