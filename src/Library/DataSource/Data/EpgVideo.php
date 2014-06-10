<?php  namespace Library\DataSource\Data;

use Utils\CMSLog;
class EpgVideo implements DataInterface
{

    public $dataTyp = 'XML';

    public $url = 'http://epg.api.pptv.com/detail.api?auth=1&ver=2&vid=';

    public $geo = 'http://api.epg.synacast.com/data_query?type=db.query.status&pres=tvbox&ids=';

    public $xmlContent = '';

    protected $data = array();

    public $error = array();

    private $_map = array();

    public function __construct($target, $url = '',$dataSource='')
    {
        try {
            $videoInfo = @file_get_contents($this->url . $target);
            if ($videoInfo === false) {
                $this->error = 'time out';
                return null;
            }
            $this->xmlContent['videoInfo'] = $videoInfo;
            $geoInfo = @file_get_contents($this->geo . $target);
            if ($geoInfo === false) {
                $this->error = 'time out';
                return null;
            }
            $this->xmlContent['geoInfo'] = $geoInfo;
            $this->resolveVideo();
            $this->resolveGeo();
        } catch (\Exception $e) {
            CMSLog::debug('Epg:' . $this->url . $target . ' 解析出错');
            $this->error[] = 'Epg:' . $this->url . $target . ' 解析出错';
            $this->xml_content = '';
        }

    }

    public function resolveVideo()
    {

        if ($this->xmlContent['videoInfo']) {
            try {
                $xml = simplexml_load_string($this->xmlContent['videoInfo']);
                $this->data['title'] = (string)$xml->title;
                $this->data['imgurl'] = (string)$xml->imgurl;
                $this->data['vip'] = (int)$xml->vip;
                //http://img24.pplive.cn/2008/03/28/15432073224_230X306.jpg
                $this->data['imgurl'] = preg_replace('#(http://.*?)/(.*?)_.*?(\.\w*)#is', '$1/sp423/$2$3', $this->data['imgurl']);
            } catch (Exception $e) {
                CMSLog::debug('解析EpgVideo XML 解析出错');
                $this->error[] = '解析EpgVideo XML 解析出错';
            }
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
            } catch (Exception $e) {
                CMSLog::debug('解析EpgVideo XML 解析出错');
                $this->error[] = '解析EpgVideo XML 解析出错';
            }
        }
    }

    public function getData()
    {

        return $this->data ? $this->data : array();
    }

    public function mapLocal($mapLocal = array())
    {
        $local_data = array();
        if ($mapLocal) {
            foreach ($mapLocal as $map) {
                list($local, $remote) = explode(':', $map);
                $local_data[$local] = isset($this->data[$remote]) ? $this->data[$remote] : '';
            }
        }
        if (!$local_data)
            return array();

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
        return $local_data;
    }
}