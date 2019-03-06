<?php
date_default_timezone_set('PRC');

// 日志
function logx($msg,$level='error')
{
    global $g_log_dir;
    
    $filename = $level;
    
    $tm = time();
    
    $dt = date('Y-m-d', $tm);
    
    $pid = getmypid();
    
    $file_dir = "{$g_log_dir}{$dt}".'/';
    
    if(!is_dir($file_dir))
    {
        @mkdir($file_dir,0777,true);
    }
    
    $log_file = "{$file_dir}/{$filename}.log";
    
    file_put_contents($log_file, date('H:i:s', $tm) . "\t{$pid}\t{$msg}\n", FILE_APPEND);
}

function rc4($key, $str) {
    $s = array();
    for ($i = 0; $i < 256; $i++) {
        $s[$i] = $i;
    }
    $j = 0;
    for ($i = 0; $i < 256; $i++) {
        $j = ($j + $s[$i] + ord($key[$i % strlen($key)])) % 256;
        $x = $s[$i];
        $s[$i] = $s[$j];
        $s[$j] = $x;
    }
    $i = 0;
    $j = 0;
    $res = '';
    for ($y = 0; $y < strlen($str); $y++) {
        $i = ($i + 1) % 256;
        $j = ($j + $s[$i]) % 256;
        $x = $s[$i];
        $s[$i] = $s[$j];
        $s[$j] = $x;
        $res .= $str[$y] ^ chr($s[($s[$i] + $s[$j]) % 256]);
    }
    return $res;
}

function decodeDbPwd($pwd)
{
    return rc4('jhY^&54$HJ4##HJ**&%', base64_decode($pwd));
}

//获取数据库
function getMysqlDb()
{
    global $g_db_info;
    $host = $g_db_info['host'];
    $instance = '';
    $db_name = $g_db_info['name'];
    $db_user = $g_db_info['user'];
    $db_pwd = $g_db_info['pwd'];

    if(empty($db_name))
    {
        logx("Invalid Database config");
        return NULL;
    }

    $mysql = new MySQLdb($host, $db_user, $db_pwd, false, $db_name);
    if(!$mysql->connect())
    {
        logx("Mysql Connect Fail: {$host} {$instance} {$db_user} {$db_name}");
        return NULL;
    }

    if($mysql->execute('SET NAMES UTF8') === false)
    {
        $mysql->close();
        logx("Mysql set encodeing Fail");
        return NULL;
    }
    return $mysql;
}

function requestAll($name, $def='')
{
    if(isset($_REQUEST[$name]))
        return $_REQUEST[$name];
    return $def;
}

function requestPost($name, $def='')
{
    if(isset($_POST[$name]))
        return $_POST[$name];
    return $def;
}

function requestGet($name, $def='')
{
    if(isset($_GET[$name]))
        return $_GET[$name];
    return $def;
}

function returnInfo(int $code,string $msg,array $info = array()){
    $result = array(
        'code'=>$code,
        'msg'=>$msg,
        'info'=>$info
    );
    die(json_encode($result));
}

function debug($msg){
    if(is_array($msg)){
        $msg = json_encode($msg);
    }
    logx($msg,'debug');
}

function error($msg){
    if(is_array($msg)){
        $msg = json_encode($msg);
    }
    logx($msg,'error');
}

function httpRequestByPost($url,$req=array()){
    $post_data = http_build_query($req);
    $length    = strlen($post_data);
    $cl        = curl_init($url);
    curl_setopt($cl, CURLOPT_POST, true);
    curl_setopt($cl,CURLOPT_HTTP_VERSION,CURL_HTTP_VERSION_1_1);
    curl_setopt($cl,CURLOPT_HTTPHEADER,array("Content-Type: application/x-www-form-urlencoded","Content-length: ".$length));
    curl_setopt($cl,CURLOPT_TIMEOUT_MS,30000);
    curl_setopt($cl,CURLOPT_POSTFIELDS,$post_data);
    curl_setopt($cl,CURLOPT_RETURNTRANSFER,true);
    $content = curl_exec($cl);
    if (curl_errno($cl)){
        error("Request_Error:".curl_error($cl));
    }
    else{
        $httpStatusCode = curl_getinfo($cl, CURLINFO_HTTP_CODE);
        if (200 !== $httpStatusCode){
            error("Request_Error:".$httpStatusCode);
        }
    }
    curl_close($cl);
    return $content;
}

function httpRequestByGet($url,$req=array()){
    $service_url = $url . '?' . http_build_query($req);
	$content = file_get_contents($service_url);
    return $content;
}
?>