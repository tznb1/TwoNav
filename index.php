<?php
error_reporting(E_ALL^E_NOTICE^E_WARNING^E_DEPRECATED);
define('DIR',__DIR__); //常量: 运行目录
require DIR."/system/Msg.php";
if(!is_file(DIR.'/data/config.php')){require DIR.'/system/install.php';exit;} //无配置时进入安装引导
require DIR."/data/config.php";
require DIR.'/system/Medoo.php';
require DIR.'/system/public.php';

//载入或链接数据库
if($db_config['type'] == 'sqlite'){
    try {
        $db_config['path'] = DIR."/data/".$db_config['file'];
        $db = new Medoo\Medoo(['type'=>'sqlite','database'=>$db_config['path']]);
    }catch (Exception $e) {
        Amsg(-1,'载入数据库失败'.$db_config['path']); 
    }
}elseif($db_config['type'] == 'mysql' || $db_config['type'] == 'mariadb' ){
    try {
        $db = new Medoo\Medoo([
            'type' => $db_config['type'],
            'host' => $db_config['host'],
            'port' => $db_config['port'],
            'database' => $db_config['name'],
            'username' => $db_config['user'],
            'password' => $db_config['password'],
            'charset' => 'utf8mb4'
        ]);
    }catch (Exception $e) {
        Amsg(-1,'链接数据库失败!'); 
    }
}


$global_config = unserialize( get_db("global_config", "v", ["k" => "o_config"]) ); //全局配置
$c = Get('c');
$libs = $global_config['Libs'];
$layui['js']  = $libs.'/Layui/v2.9.10/layui.js';
$layui['css'] = $libs.'/Layui/v2.9.10/css/layui.css';
$global_config['static_link'] = isset($global_config['static_link']) ? $global_config['static_link'] : 0;
define('libs',$global_config['Libs']);
define('SysVer',Get_Version());
define('Debug',$global_config['Debug'] == 1);
define('static_link',$global_config['static_link'] > 0);

if(!in_array($c,[$global_config["Register"],'ico','icon','auth'])){
    if($global_config['static_link'] > 0 && !empty($UUID)){
        $_GET['u'] = $global_config['static_link'] == 2 ? get_db("global_user", "User", ["ID"=>$UUID]) : $UUID;
    }
    $u = Get('u');
    $u = !empty($u)?$u:(!empty($_COOKIE['Default_User'])?$_COOKIE['Default_User']:(!empty($global_config['Default_User'])?$global_config['Default_User']:'admin'));//优先级:Get>Host>Cookie>默认用户>admin
    $USER_DB = get_db("global_user", "*", ["User"=>$u]);
    //没找到账号显示404
    if(empty($USER_DB)) {
        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            msg(-1,'账号不存在!'); 
        }else{
            require(DIR.'/templates/admin/page/404.php');
            exit;
        }
    }
    define('U',$u);define('UID',$USER_DB['ID']);
}

session_name('TwoNavSID');
if(defined('UID')){
    define('is_login',is_login()); $is_login = is_login;
}

if(empty($c) || $c == 'index'){
    $c = 'index';
    require "./system/index.php";//主页
}elseif($c == $global_config["Register"]){
    require "./system/Register.php";//注册
}elseif($c == $global_config['Login']  || $c == $USER_DB['Login']){
    require "./system/login.php";//登陆
}elseif(in_array($c,['admin','click','api','ico','icon','verify','auth'])){
    require "./system/{$c}.php";
}elseif(in_array($c,['apply','guestbook','article','sitemap'])){
    if($global_config['Maintenance'] != 0){Amsg(-1,'网站正在进行维护,请稍后再试!');}
    require "./system/expand/{$c}.php";
}else{
    Amsg(-1,'接口错误'.$c); 
}
