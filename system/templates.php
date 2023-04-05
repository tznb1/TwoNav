<?php if(!defined('DIR')){header('HTTP/1.1 404 Not Found');header("status: 404 Not Found");exit;}

//读取全局模板配置
$global_templates = unserialize(get_db("global_config",'v', ["k" => "s_templates"]));

//读取用户模板配置
$s_templates = unserialize(get_db("user_config", "v", ["uid"=>UID,"k"=>"s_templates"]));

//没找到用户模板配置
if(empty($s_templates)){
    //将全局默认模板配置写到用户配置
    $s_templates = $global_templates;
    insert_db("user_config", ["uid" => UID,"k"=>"s_templates","v"=>$global_templates,"t"=>"config","d" => '默认模板']);
}
