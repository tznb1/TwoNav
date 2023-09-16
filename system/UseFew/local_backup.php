<?php 
if(!defined('DIR')){
    header('HTTP/1.1 404 Not Found');
    header("status: 404 Not Found");
    exit;
}else{
    if(!is_subscribe('bool')){
        msg(-1,"未检测到有效授权,无法使用该功能!");
    }
    msg(1,'请更新系统后再试');
}
