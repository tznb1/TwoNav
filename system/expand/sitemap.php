<?php
if(!is_subscribe('bool')){exit;}

//设置协议头
header('Content-Type: application/xml');

//读取配置
$sitemap_config = unserialize( get_db("global_config", "v", ["k" => "sitemap_config"]));

//储存路径
$sitemap_path = DIR . "/data/user/{$u}/sitemap.php";

//载入生成脚本
require 'sitemap_create.php';

//是否为手动生成
if(!empty($_GET['mode'])){
    if($sitemap_config['switch'] != '1'){
        msg(-1,'请将功能开关设为开启并保存');
    }else{
        create_sitemap($sitemap_config,$sitemap_path,$u);
        msg(1,'生成完毕');
    }
}else{
    //未开启被动请求时,如果有缓存文件则返回
    if($sitemap_config['beidong'] != '1'){
        if(file_exists($sitemap_path)){
            exit(file_get_contents($sitemap_path) ?? '');
        }
        exit;
    }
}

//未开启功能时不输出任何数据
if($sitemap_config['switch'] != '1'){
    exit;
}

//判断是否需要更新
if(is_Update_Sitemap($sitemap_config,$sitemap_path)){
    exit (create_sitemap($sitemap_config,$sitemap_path,$u));
}else{
    exit(file_get_contents($sitemap_path) ?? '');
}

?>
