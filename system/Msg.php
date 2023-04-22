<?php 

//返回JSON信息(状态码)
function code($code){
    header('Content-Type:application/json; charset=utf-8');
    exit(json_encode(['code'=>$code]));
}
//返回JSON信息(常用型)
function msg($code,$msg){
    $data = ['code'=>$code,'msg'=>$msg];
    header('Content-Type:application/json; charset=utf-8');
    exit(json_encode($data));
}
//返回JSON信息(自定义信息)
function msgA($data){
    header('Content-Type:application/json; charset=utf-8');
    exit(json_encode($data));
}

//如果是POST则返回Json信息,否则返回HTML文本
function Amsg($code,$msg){
    if($_SERVER['REQUEST_METHOD'] === 'POST'){
        msg($code,$msg);
    }else{
        header("content-Type: text/html; charset=utf-8");
        exit('<title>错误</title><font color="red">代码:'.$code.'<br />信息:'.$msg.'</font>');
    }
}