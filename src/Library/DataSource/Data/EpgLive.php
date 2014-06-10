<?php namespace Library\DataSource\Data;

use Utils\CMSLog;

class EpgLive implements  DataInterface{

    public $url = 'http://epg.api.pptv.com/detail.api?auth=1&ver=2&vid={vid}&nowplay=1&platform=atv';
    public $geo = 'http://api.epg.synacast.com/data_query?type=db.query.status&pres=tvbox&ids=';
    public $xmlContent = array();

    protected $data = array();

    public $error = array();

    private $_map = array();

    public function __construct($target,$url = '',$dataSource=''){

        //init geo data
        $this->data['geo'] = array('data'  => array(),'force' => array(),'type'  => 0);

        try{
            $liveInfo = @file_get_contents(str_replace('{vid}',$target,$this->url));
            if(!$liveInfo){
                $liveInfoBool = !!$liveInfo;
                throw new \Exception('无法解析:liveInfo:'.$liveInfoBool);
            }
            $this->xmlContent['geoInfo'] = @file_get_contents($this->geo . $target);
            $this->xmlContent['liveInfo'] = $liveInfo;
            $this->resolveLive();
            $this->resolveGeo();
        }catch (\Exception $e){
            CMSLog::debug('Epg:' . $this->url . $target . ' 解析出错');
            $this->error[] = 'Epg:' . $this->url . $target . ' 解析出错';
            $this->xml_content = '';
        }
    }

    public function resolveLive(){
        try{
            $xml = simplexml_load_string($this->xmlContent['liveInfo']);
            $this->data['tv_name'] = (string)$xml->title;
            $this->data['tv_icon'] = (string)$xml->imgurl;
            $this->data['tv_img'] = (string)$xml->sloturl;
            $this->data['atv_img']='';

            if($this->data['tv_img']){
                $img = str_replace('sp120','sp480',$this->data['tv_img']);
                $this->data['atv_img'] = $img;
                $this->data['tv_img'] = $img."?ver=".time();
            }
            $this->data['current_begin']=(string)$xml->nowplay['begin_time'];
            $this->data['current_end'] = (string)$xml->willplay['begin_time'];
            $this->data['current_play'] = (string)$xml->nowplay;
            $this->data['expired'] = strtotime((string)$xml->willplay['begin_time']);
        }catch (Exception $e){
            CMSLog::debug('解析EpgLive XML 解析出错');
            $this->error[] = '解析EpgLive XML 解析出错';
        }
    }

    public function resolveGeo()
    {
        if ($this->xmlContent['geoInfo']) {
            try {
                $xml = simplexml_load_string($this->xmlContent['geoInfo']);
                $bppStatus = (int)$xml->bpp_channel->row[0]['status'];
                $geoStatus = (int)$xml->tvbox_channel->row[0]['status'];
                $bppItems = $xml->bpp_item_value->row;
                $localGeo = \Config::get('params.areaChar2Number');
                $this->data['geo'] = '';
                $this->data['force'] = array_values($localGeo);
                if ($bppStatus === 1 && $geoStatus === 1)
                    $this->data['geo'] = (string)$xml->tvbox_channel->row[0]['forbidden'];

                if ($bppItems) {
                    foreach ($bppItems as $bppItem) {
                        if ((int)$bppItem['itemid'] === 9) {
                            $force = explode(',', $bppItem['strvalue']);
                            $this->data['force'] = array_values(array_except($localGeo, $force));
                        }
                    }
                }
                if (!empty($this->data['geo'])) {
                    $geo = explode(',', $this->data['geo']);
                    $this->data['geo'] = array_values(array_except($localGeo, $geo));
                    if (empty($this->data['geo']))
                        $this->data['geo'] = true;
                }
                $this->data['geo'] = $this->processGeo();
                unset($this->data['force']);
            } catch (\Exception $e) {
                CMSLog::debug('解析EpgVideo XML 解析出错');
                $this->error[] = '解析EpgVideo XML 解析出错';
            }
        }
    }

    public function getData()
    {
        return $this->data ? $this->data : array();
    }

    public function mapLocal($mapLocal = array()){
        $local_data = array();
        if ($mapLocal) {
            foreach ($mapLocal as $map) {
                list($local, $remote) = explode(':', $map);
                $local_data[$local] = isset($this->data[$remote]) ? $this->data[$remote] : '';
            }
        }
        return $local_data?$local_data:array();
    }

    private function processGeo(){
        if (!empty($this->data['geo'])) {
            $local_data ['geo'] = array(
                'data'  => $this->data['geo'] === true ? array() : $this->data['geo'],
                'force' => $this->data['force'],
                'type'  => 1
            );
        } else
            $local_data ['geo'] = array(
                'data'  => array(),
                'force' => array(),
                'type'  => 0
            );

        return $local_data['geo'];
    }
}