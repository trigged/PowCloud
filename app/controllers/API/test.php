<?php
/**
 * Created by JetBrains PhpStorm.
 * User: trigged
 * Date: 11/15/13
 * Time: 4:16 PM
 * To change this template use File | Settings | File Templates.
 */

/*
 * 多接口请求
 * 元素映射
 * 自动检测接口元素 (检索条件)
 * 拖动元素*/

$values = array(
    array(
        'type'   => 1, //has many req
        'method' => 'GET',
        'format' => 'json',
        'url'    => 'http://cms/test1',
        'fields' => array('type' => 'result.code'),
    ),
    array(
        'type'     => 2, //no req
        'method'   => 'GET',
        'format'   => 'json',
        'url'      => 'http://cms/test2',
        'params'   => array('format' => 'json', 'version' => 1),
        'dyparams' => array('type'),
        'map'      => '""',
    ),

);

class AjaxParser
{

    public function handler($values)
    {
        foreach ($values as $value) {
            if ($value['type'] === 1) {
                $result = $this->call_api($value['url'], $value['method']);
                if ($value['format'] === 'json') {
                    $result = json_decode($result, true);
                } else if ($value['format'] === 'xml') {
                    //xml handler
                }
                foreach ($value['fields'] as $key => $path) {
                    //generator field in dynamic
                    $$key = $this->get_value_in_path($path, $result);
                }
            } else if ($value['type'] === 2) {
                //todo make sure query can be used in post method!
                $query = null;
                foreach ($value['params'] as $key => $kv) {
                    $query .= sprintf('%s=%s&', $key, $kv);
                }
                foreach ($value['dyparams'] as $kv) {
                    $query .= sprintf('%s=%s&', $kv, $$kv);
                }
                var_dump($this->call_api($value['url'] . '?' . $query));
            }
        }
        //todo then map the filed to the result!

    }

    function call_api($url, $method = 'GET', $data = false)
    {
        $curl = curl_init();

        switch ($method) {
            case 'GET':
                break;
            case "POST":
                curl_setopt($curl, CURLOPT_POST, 1);

                if ($data)
                    curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
                break;
            case "PUT":
                curl_setopt($curl, CURLOPT_PUT, 1);
                break;
            default:
                if ($data)
                    $url = sprintf("%s?%s", $url, http_build_query($data));
        }

        // Optional Authentication:
        curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($curl, CURLOPT_USERPWD, "username:password");

        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

        return curl_exec($curl);
    }

    private function get_value_in_path($path, $data, $split = '.')
    {
        $result = $data;
        foreach (explode($split, $path) as $value) {
            if (isset($result[$value])) {
                $result = $result[$value];
            }
        }
        return $result;
    }

//    private function get_value_in_path($path, $data, $split = '.')
//    {
//        $result = $data;
//        foreach (explode($split, $path) as $value) {
//            if (isset($result[$value])) {
//                $result = $result[$value];
//            }
//        }
//        return $result;
//    }
}

//$aj = new AjaxParser();
//$aj->handler($values);
//var_dump(json_encode($values), true);


//$url = 'http://cms/special?version=1&format=json';
////$url = 'http://cms/home?version=1&format=json';
//$url = 'http://cms/special/16?version=1&format=json';
//$a = array(1.2121,23232.434,4343.32323,4.434,0.4);
//foreach($a as $t){
//    var_dump(round($t,2));
//}

//
//$result = call_api($url);
//$result = json_decode($result, true);
//$iterator = new RecursiveIteratorIterator(new RecursiveArrayIterator($result));
//$keys = array();
//foreach ($iterator as $key => $value) {
//    // Build long key name based on parent keys
//    for ($i = $iterator->getDepth() - 1; $i >= 0; $i--) {
//        $kv = $iterator->getSubIterator($i)->key();
//        if (is_string($kv)) {
//            $key = $kv . '.' . $key;
//        } else {
//            echo $key . " has number\r\n";
//        }
//    }
//    $keys[$key] = $key;
//}

$value = array(
    'special'       => array(
        'title'      => 'test123',
        'bgimg'      => 'xxx.jpg',
        'cover_img'  => 'xxx.jpg',
        'assist_img' => 'xx.jpg',
    ),
    'special_video' => array(
        array(
            'epg_id' => '12443',
            'title'  => 'xxx',
            'img'    => 'scc.jpg',
            'vip'    => '1',
        ),
        array(
            'epg_id' => '12443',
            'title'  => 'xxx',
            'img'    => 'scc.jpg',
            'vip'    => '1',
        ),
        array(
            'epg_id' => '12443',
            'title'  => 'xxx',
            'img'    => 'scc.jpg',
            'vip'    => '1',
        ),
        array(
            'epg_id' => '12443',
            'title'  => 'xxx',
            'img'    => 'scc.jpg',
            'vip'    => '1',
        ),
    )
);
//last week 5.26-6.1


//echo json_encode($value);
$test = array('a');
var_dump(array_rand($test));
echo "today :";
var_dump(date('y-m-d', time()));
echo "last week :";
var_dump(date('y-m-d', strtotime("last week")));

echo "last end  week :";
var_dump(date('y-m-d', strtotime("last  week +6 day")));

echo "last month :";
var_dump(date('y-m-d', strtotime("last month")));

echo "-1 month :";
var_dump(date('y-m-t', strtotime("-1 month")));

$query = "insert into `forms` (`default_value`, `field`, `label`, `models_id`, `rank`, `rules`, `type`) values (?, ?, ?, ?, ?, ?, ?), (?, ?, ?, ?, ?, ?, ?), (?, ?, ?, ?, ?, ?, ?)) ";
$values = array(0  => '""',
                1  => 'name',
                2  => '分类',
                3  => '1',
                4  => '""',
                5  => '""',
                6  => 'text',
                7  => '""',
                8  => 'price',
                9  => '标题',
                10 => '1',
                11 => '""',
                12 => '""',
                13 => 'text',
                14 => '""',
                15 => 'img',
                16 => '内部产品代号',
                17 => '1',
                18 => '""',
                19 => '""',
                20 => 'text',
);

$sql = str_replace(array('%', '?'), array('%%', '%s'), $query);
//var_dump($sql);
$full_sql = vsprintf($sql, $values);
//printf($full_sql);
$launcher = '{"content_id":["integer"],"title":["string"],"bgimg":["string","200"],"relation_id":["integer"],"geo":["text"]}';
$special = '{"title":["string"],"bgimg":["string","200"],"cover_img":["string","200"],"geo":["text"],"videos":["text"],"foreign":{"0":"string","default":"videos:special_video"},"assist_bgimg":["string"]}';

$launcher_test = json_decode($launcher, true);
$special_test = json_decode($special, true);
$test1 = array('a' => 1, 'b' => 2, 'c' => array('m'));
$test2 = array('d' => 111, 'a' => 1, 'b' => 11, 'c' => array('m', 'n'));
//var_dump(array_intersect($test1, $test2));
var_dump(array_intersect_key($test2, $test1));
var_dump(array_intersect_key($test1, $test2));
echo "--------------\n";


$json_test1 = json_encode(array("title", "img"));
var_dump($json_test1);
var_dump(htmlspecialchars($json_test1));
var_dump(htmlspecialchars_decode(htmlspecialchars($json_test1)));
//var_dump(array_keys(array_intersect_key($launcher_test, $special_test)));
//var_dump(array_keys(array_intersect_key($special_test, $launcher_test)));
