<?php

define('ROOT_DIR', dirname(__FILE__));

require_once(ROOT_DIR.'/config.php');
require_once(ROOT_DIR.'/common/util.php');
require_once(ROOT_DIR.'/common/db.inc.php');
require_once(ROOT_DIR.'/common/loader.php');
require_once(ROOT_DIR.'/service.php');

//Error handle
function _ErrorHandler($severity, $message, $filepath, $line)
{
    if ($severity == E_STRICT)
    {
        return;
    }
    
    $error_reporting = error_reporting();
    if($error_reporting == 0)
    {
        return;
    }
    
    logx("PHP Error Severity: $severity --> $message $filepath $line");
}
set_error_handler('_ErrorHandler');

date_default_timezone_set('PRC');

// 加载API目录下的类文件
$loader = new Loader(ROOT_DIR,array('api'));

$service = new Service();

$service->api();