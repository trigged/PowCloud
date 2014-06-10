<?php namespace Library\DataSource\Data;

class UrlData implements DataInterface{

    private $_responseType = '';
    private $_content = '';

    public function __construct($target,$url = '',$dataSource=''){
        $target = str_replace('{target}',$target,$dataSource);
        $this->_content = @file_get_contents($target);

        if($this->_content && $this->determineContentTypeByRawOrHeader($this->_content,$http_response_header)){
            $method = 'parse'.$this->_responseType;
            if(method_exists($this,$method)){
                $this->$method();
            }
        }else
            $this->_responseType='';

    }

    private function parseJson(){
        $this->_content = json_decode($this->_content,true);
    }

    private function parseXml(){
        $this->_content = simplexml_load_string($this->_content);
    }

    public function getData(){

    }

    public function mapLocal($mapLocal = array()){
        if(!$this->_responseType) return array();

        $localData = array();
        if($mapLocal){
            foreach($mapLocal as $map){
                $remoteNeed = '';
                $method = 'retrieval'.$this->_responseType;
                list($local,$remote) = explode(':',$map);
                if(strpos($remote,'.')!==false){
                    $remote = explode('.',$remote);
                    foreach($remote as $r){
                        $remoteNeed = $this->$method($remoteNeed,$r);
                    }
                }elseif(preg_match('#^{(.*?)}$#is',$remote,$code)){
                    if(!empty($code[1]))
                        $remoteNeed = eval($code[1]);
                }else{
                    $remoteNeed = $this->$method(false,$remote);;
                }
                $localData [$local] =(string)$remoteNeed;
            }
        }
        return $localData;
    }

    private function retrievalJson($deep,$remoteField){
        if($deep===false){
            return empty($this->_content[$remoteField])?'':$this->_content[$remoteField];
        }else{
            return $deep?$deep[$remoteField]:$this->_content[$remoteField];
        }
    }

    private function retrievalXml($deep,$remoteField){
        $attribute = '';
        if(strpos($remoteField,'^')!==false){
            list($remoteField,$attribute) = explode('^',$remoteField);
        }
        if($deep===false){
            $v = !$this->_content->$remoteField?'':$this->_content->$remoteField;
        }else{
            $v = $deep?$this->$remoteField:$this->_content->$remoteField;
        }
        if($v && $attribute)
            return $v[$attribute];
        return $v;
    }

    protected function determineContentTypeByRawOrHeader($rawContent,$header)
    {
        if (preg_match('/^\\{.*\\}$/is', $rawContent)) {
            $this->_responseType = 'Json';
        }
        if (preg_match('/^[^=|^&]+=[^=|^&]+(&[^=|^&]+=[^=|^&]+)*$/is', $rawContent)) {
            $this->_responseType = 'urlencode';
        }
        if (preg_match('/^<.*>$/is', $rawContent)) {
            $this->_responseType =  'Xml';
        }

        if(!$this->_responseType){
            $header = implode("\r\n",$header);
            if(strpos($header,'Content-Type: text/xml;')!==false)
                $this->_responseType = 'Xml';
            elseif(strpos($header,'Content-Type: application/json;'))
                $this->_responseType = 'Json';
        }

        if($this->_responseType)
            return true;
        return false;
    }
}