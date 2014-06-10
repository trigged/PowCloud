<?php
use Illuminate\Routing\Controllers\Controller;
use Operator\CacheController;
use Operator\ReadApi;
use Utils\CMSLog;
use Utils\UseHelper;

/**
 * Created by JetBrains PhpStorm.
 * User: trigged
 * Date: 4/4/14
 * Time: 10:21 AM
 * To change this template use File | Settings | File Templates.
 */


class RecommendController extends Controller
{


    const resize = '500X667.jpg';

    public $source = null;

    public $format = null;

    public $page = null;

    public $count = null;

    public $token = '8ch86oMZN6p1N1/TcSr9Fw==';

    public $expire = 60;

    public $app_pool = null;

    public $epg_api = 'http://epg.api.pptv.com/detail.api?platform=androidphone&c=28&s=1&vid=%s&auth=1&virtual=1&series=1&fb=333';

    public $detail = 'pptv.atv://com.pplive.androidtv/detail?source=%s&channel_id=%s';

    public $play = 'pptv.atv://com.pplive.androidtv/player?type=ppvod&source=%s&channel_id=%s';

    public $result = array(
        'code'    => 1,
        'message' => 'success',
        'data'    => array()
    );

    public $num = 0;

    public $start = null;

    public $table_names = array('home_large', 'home_video', 'home_special');

    public function __construct()
    {
        $that = $this;
        $this->start = microtime(true);

        $this->beforeFilter(function () use ($that) {

            $that->token = Input::get('token', $that->token);
            $that->app_pool = (int)UseHelper::checkToken($that->token, UseHelper::$default_key);
            if ($that->app_pool == 0) {
                return $that->getResult(-1, 'token error', null);
            }
            \Utils\AppChose::updateConf($that->app_pool);
            $that->source = Input::get('source');

            $that->format = Input::get('format');

            //prepare for paging
            $that->page = (int)Input::get('page');
            $that->count = (int)Input::get('count');
            if ($that->count == null) {
                $that->count = 20;
            }
            if ($that->format == null) {
                $that->format = 'json';
            }
            if (empty($that->source)) {
                return $that->getResult(-1, 'missing params', null);
            }
            $that->expire = (int)Request::get('expire');
        });
    }

    public function getResult($code = 1, $message = 'success', $data = array())
    {
        $this->result['code'] = $code;
        $this->result['message'] = $message;
        if ($data != null) {
            $data = array_merge(array(), $data);
        }
        $this->result['data'] = $data;
        $http_code = $code > 0 ? 200 : 404;
        $value = round(microtime(true) - $this->start, 3);

        \Operator\WriteApi::addApiMonitor('recommend', 'index', $value * 1000);
        $header = array(
            'Expires'        => date(DATE_RFC822, time() + $this->expire) . ' GMT',
            'Pragma'         => 'public',
            'Last-Modified:' => 'Fri, 14 Feb 2014 04:03:53 GMT',
            'Cache-Control'  => 'public, max-age=' . $this->expire,
        );

        return Response::json($this->result, $http_code, $header);
    }

    public function index()
    {
        $today = date('Y-m-d', time());
        $result = array();
        $data = array();
        //region get all the content id
        foreach ($this->table_names as $table_name) {
            //->where('created_at', $today)  lists('content_id');
            $query = DB::connection('models')->table($table_name)->where('deleted_at', '0000-00-00 00:00:00')->where('geo', '')->where('updated_at', 'like', $today . '%')->where('content_type', '1')->lists('content_id');
            $data = array_merge($data, $query);
        }
        //endregion

        foreach ($data as $content_id) {
            $value = $this->getInfoByEpgId($content_id);
            if ($value) {
                $result[] = $value;
            }
        }
        return $this->getResult(1, 'success', $result);
    }

    private function getInfoByEpgId($id)
    {
        $url = vsprintf($this->epg_api, $id);
        $data = array();
        $epg_data = simplexml_load_file($url);
        if (empty($epg_data)) {
            return false;
        }
        if (isset($epg_data->title)) {
            $data['title'] = (string)$epg_data->title;
        }
        if (isset($epg_data->imgurl)) {
            $img = (string)$epg_data->imgurl;
            $data['img_img'] = $img;
            $img = str_replace('.jpg', self::resize, $img);
            $data['img'] = $img;
        }
        if (isset($epg_data->total_state)) {
            $data['total'] = (string)$epg_data->total_state;
        }
        if (count($data) == 0) {
            return false;
        }
        $data['detail'] = sprintf($this->detail, $this->source, $id);
        $data['play'] = sprintf($this->play, $this->source, $id);
        return $data;
    }

}