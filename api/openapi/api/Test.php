<?php
class Test{
    
    public $_request = array();
    
    public function __construct($request){
        $this->_request = $request;
    }
    
    public function run(){
        returnInfo(0,'ok',array(
            'data'=>'123'
        ));
    }
}