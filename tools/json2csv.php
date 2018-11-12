<?php
//读取json数据
$myfile = fopen("data.json", "r") or die("Unable to open file!");
$data = fread($myfile,filesize("data.json"));
fclose($myfile);
echo "<br>开始生成<br>";
//转换成数组
$data = json_decode($data,true);
if(!is_array($data)){
    die("<br>数据格式转换失败<br>");
}
$first = true;
$count = 0;
$file = fopen("data.csv","w");
foreach($data as $k=>$v){
    $count++;
    //第一行是表头
    if($first){
        $first = false;
        // 写入表头和数据
        echo "<br>生成表头<br>";
        $header = array_keys($v);
        fputcsv($file,checkChildArray($header));
        echo "<br>生成第 $count 行<br>";
        fputcsv($file,checkChildArray($v));
    }
    //顺序写入数据
    echo "<br>生成第 $count 行<br>";
    fputcsv($file,checkChildArray($v));
}
fclose($file);
echo "<br>生成完毕<br>";

function checkChildArray($data){
    if(is_array($data)){
        foreach($data as &$it){
            if(is_array($it)){
                $it = json_encode($it);
            }
        }
    }
    return $data;
}