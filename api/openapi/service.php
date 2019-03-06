<?php

class Service {
    
    private $_request = '';
    private $_appsecret = '';
    
    // 调用接口执行
    public function api(){
        debug('开始请求接口...');
        $this->_request = $_REQUEST;
        
        // 检查时间戳
        //$this->checkTimestamp();
        
        // 检查授权
        //$this->checkAuth();
        
        // 检查签名
        //$this->checkSign();
        
        // 检查方法
        $method = $this->_request['method'];
        if (!class_exists($method)) {
            returnInfo(400,'方法不存在！');
        }
        
        // 执行接口
        $api = new $method($this->_request);
        $api->run();
    }
    
    public function checkAuth(){
        
        global $g_app_auth;
        if(!isset($g_app_auth['app']['appkey'])){
            returnInfo(400,'未授权的应用！');
        }
        
        $this->_appsecret = $g_app_auth['app']['appkey'];
        if(empty($this->_appsecret)){
            returnInfo(400,'应用授权信息错误！');
        }
    }
    
    protected function checkSign(){
        $req_sign = $this->_request['sign'];
        $cur_sign = $this->makeSign($this->_request,$this->_appsecret);
        if($cur_sign != $req_sign)
        {
            returnInfo(400,'无效的签名！');
        }
    }
    
    protected function checkTimestamp(){
        
        $timestamp = $this->_request['timestamp'];
        if(empty($timestamp))
        {
            returnInfo(400,'时间戳字段不能为空！');
        }
        
        if(abs(strtotime($timestamp)-time())>300)
        {
            returnInfo(400,'时间戳不正确请检查服务器时间！');
        }
    }
    
    protected function packData(&$req)
    {
        ksort($req);
    
        $arr = array();
        foreach($req as $key => $val)
        {
            if($key == 'sign') continue;
        
            if(count($arr))
                $arr[] = ';';
        
            $arr[] = sprintf("%02d", iconv_strlen($key, 'UTF-8'));
            $arr[] = '-';
            $arr[] = $key;
            $arr[] = ':';
            
            $arr[] = sprintf("%04d", iconv_strlen($val, 'UTF-8'));
            $arr[] = '-';
            $arr[] = $val;
        }
        return implode('', $arr);
    }

    protected function makeSign(&$req, $appsecret)
    {
        $sign = md5(packData($req) . $appsecret);
        $req['sign'] = $sign;
    }
}