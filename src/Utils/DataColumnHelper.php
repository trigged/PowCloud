<?php namespace Utils;

class DataColumnHelper
{
    public static function registerFormNamespace($namespace = '')
    {
        FormBuilderHelper::$namespace = $namespace;
    }

    public static function select($format = '', $dataType, $value = '', $selectOptions = array())
    {
        if ($format === 'form')
            return FormBuilderHelper::select($dataType, $value, 'input-medium');

        $options = explode(',', $selectOptions);
        if (isset($options[$value])) {
            $pos = mb_strpos($options[$value], ':');
            return $pos === false ? $options[$value] : mb_substr($options[$value], 0, $pos);
        }
        return 'undefined';
    }

    public static function textQuick($format = '', $dataType, $value = '', $selectOptions = array())
    {
        if ($format === 'form')
            return FormBuilderHelper::textQuick($dataType, $value, 'input-medium');

        return $value;
    }

    public static function colorSelect($format = '', $dataType, $value = 0, $default_value)
    {
        if ($format === 'form')
            return FormBuilderHelper::colorSelect($dataType, $value);

        $colorSet = explode(',', $default_value);
        if (isset($colorSet[$value]) && (int)$value !== 0)
            return '<div data-data="' . $value . '" style="width: 20px;height: 20px;background: ' . $colorSet[$value] . ';">' . $value . '</div>';
        return $value;
    }

    public static function image($format = '', $dataType, $value = '')
    {
        if ($format === 'form')
            return FormBuilderHelper::image($dataType, $value, 'input-medium');

        $img = '';
        if (is_array($value)) {
            foreach ($value as $v) {
                $img .= '<img style="height: 20px" class="image-thumb" src="' . $v . '" />';
            }
        } else
            $img = '<img style="height: 20px" class="image-thumb" src="' . $value . '" />';

        return $img;
    }

    public static function upload2QN($format = '', $dataType, $value = '')
    {
        if ($format === 'form')
            return FormBuilderHelper::upload2QN($dataType, $value, '');

        return '';
    }

    public static function operation($opId)
    {

    }

    public static function rank($dataType, $value, $target = '', $data)
    {

        $str = '<a data-direction="top" data-url="' . \URL::action('ExtController@top') . '" data-rank="' . $data->rank . '" data-id="' . $value . '" data-row="' . $target . '" class="JS_top" title="置顶" href="javascript:void(0);"><i class="glyphicon glyphicon-fire"></i></a>';
        return $str;
    }

    public static function ajaxInput($format = '', $dataType, $value = '')
    {

        if ($format == 'form') {
            return FormBuilderHelper::ajaxInput($dataType, $value);
        }
        return self::text($format, $dataType, $value);
    }

    public static function text($format = '', $dataType, $value = '')
    {
        if ($format === 'form')
            return FormBuilderHelper::text($dataType, $value, 'input-medium');


        if (is_array($value))
            $value = json_encode($value);

        if(mb_strlen($value) > 50){
            return substr($value, 0, 50).'...';
        }
        return $value;
    }

    public static function areaFilter($format = '', $dataType, $value = '')
    {
        if ($format === 'form') {
            $flag = '无';
            if ($value)
                $value = http_build_query($value);
            $requestUrl = \URL::action('ExtController@areaList');
            if ($value && strpos($value, 'type=0') === false)
                $flag = '有';
            $filed = $flag . '<a data-namespace="' . FormBuilderHelper::$namespace . '" data-url="' . $requestUrl . '" data-value="' . $value . '" class="area_filter_tr" href="javascript:void(0)"><i class=" glyphicon glyphicon-th-list"></i></a>';
            return $filed;
        }


        if (is_array($value)) {
            return (int)$value['type'] !== 0 ? '有' : '无';
        }

        return $value ? '有' : '无';
    }

    public static function dateTimePicker($format = '', $dataType, $value = '')
    {

        return $value;
    }

    public static function timingState($format = '', $dataType, $value = '')
    {

        return $value;
    }

    public static function generateRandomNum($format = '', $dataType, $value = '')
    {

        return $value;
    }

    public static function formTip($format = '', $dataType, $value = '')
    {
        return $value;
    }
}