<?php if(!defined('DIR')){header('HTTP/1.1 404 Not Found');header("status: 404 Not Found");exit;}AccessControl();

$page = trim($_GET['page']); //获取请求参数
$Ver = !Debug?SysVer:SysVer.'.'.time(); //版本
$LoginConfig = unserialize($USER_DB['LoginConfig']); //登录配置
define('offline',$global_config['offline'] == 1); //是否离线模式
//未登录,载入登录提示页
if(!is_login){
    require(DIR.'/templates/admin/page/LoginPrompt.php');
    exit;
}//已登录,检查是否需要验证二级密码
elseif(!empty($LoginConfig['Password2']) && !Check_Password2($LoginConfig)){
    $c = 'verify';$_GET['c'] = 'pwd2';
    require DIR."/system/templates.php";
    require $index_path;
    exit;
}

Check_Path('./data/user/'.U) or Amsg(-1,'创建用户目录失败,请检查权限!');

//如果是退出登录
if ($page == 'logout') {
    //删除记录
    delete_db("user_login_info",["uid"=>$USER_DB['ID'],"cookie_key"=>md5($_COOKIE[U.'_key'])]);
    //记录日志
    insert_db("user_log", ["uid" => $USER_DB['ID'],"user"=>$USER_DB['User'],"ip"=>Get_IP(),"time"=>time(),"type" => 'logout',"content"=>Get_Request_Content(),"description"=>"注销登录"]);
    //清除cookie
    setcookie(U."_key", '', time()-1,"/");
    if($_SERVER['REQUEST_METHOD'] === 'POST'){
        msg(1,'退出登录成功');
    }else{
        header("Location: ./index.php?u={$USER_DB['name']}");
        exit;
    }
    
}
//概况
if ($page == 'home') {
    $category_count = count_db('user_categorys',['uid'=>UID])??0;
    $link_count = count_db('user_links',['uid'=>UID])??0;
    $index_count = get_db('user_count','v',['uid'=>UID,'k'=>date('Ym'),'t'=>'index_Ym'])??0;
    $click_count = get_db('user_count','v',['uid'=>UID,'k'=>date('Ym'),'t'=>'click_Ym'])??0;
    $day = [];
    $day_data = [];
    array_push($day_data,['name'=>'访问量','type'=>'line','data'=>[]]);
    array_push($day_data,['name'=>'点击量','type'=>'line','data'=>[]]);
    for ($i=6; $i>=0; $i--){
        $date = date('Ymd',strtotime("-{$i} day"));
        array_push($day,$date);
        array_push($day_data[0]['data'],get_db('user_count','v',['uid'=>UID,'k'=>$date,'t'=>'index_Ymd'])??0);
        array_push($day_data[1]['data'],get_db('user_count','v',['uid'=>UID,'k'=>$date,'t'=>'click_Ymd'])??0);
    }
}

//载入主题配置
if($page == 'config_home'){
    $theme = $_GET['theme'];
    $config_path = DIR.'/templates/'.$_GET['fn'].'/'.$theme.'/config.php';
    $info_path = DIR.'/templates/'.$_GET['fn'].'/'.$theme.'/info.json';
    if (!is_file($config_path) || !is_file($info_path)){
        exit("<h3>主题不支持配置</h3>");
    }
    //载入主题初始配置
    $theme_config = json_decode(@file_get_contents($info_path),true);
    $theme_config = empty($theme_config['config']) ? []:$theme_config['config'];
    
    //读取用户主题配置
    if(!in_array($_GET['fn'],['home','login','register','transit','guide','article','verify','guestbook','apply'])){
        msg(-1,"参数错误");
    }
    if(in_array($_GET['fn'],['guide','register'])){
        $theme_config_db = get_db('user_config','v',['k'=>'theme_'.$theme,'uid'=>UID]);
    }else{
        $theme_config_db = get_db('user_config','v',['t'=>'theme_'.$_GET['fn'],'k'=>$theme,'uid'=>UID]);
    }
    
    $theme_config_db = unserialize($theme_config_db);
    
    //如果不为空则合并数据
    if(!empty($theme_config_db)){
        $theme_config = array_merge ($theme_config,$theme_config_db);
    }
    //配置为空
    if(empty($theme_config) || !check_purview('theme_in',1) || !check_purview('theme_set',1)){
        exit("<h3>获取主题配置失败</h3>");
    }
    require $config_path;
    exit;
}

//不带参数是载入框架
if(empty($page)){
    $site = unserialize(get_db('user_config','v',['uid'=>UID,'k'=>'s_site']));
    $favicon = ( !empty($site['site_icon_file'])) ? $site['site_icon'] : './favicon.ico';
    require DIR."/templates/admin/index.php";
    exit;
}

// 插件编辑链接跳转
if($page === 'edit_link' &&  !empty($_GET['id'])){
    header("HTTP/1.1 302 Moved Permanently");
    header("Location: ./index.php?c=admin&page=link_edit&u=".U."&id=".$_GET['id']);
    exit;
}

//页面文件不存在时载入404
if(!empty($page)){
    if(!is_file(DIR.'/templates/admin/page/'.$page.'.php')){
        require(DIR.'/templates/admin/page/404.php');
        exit;
    }else{
        require(DIR.'/templates/admin/page/'.$page.'.php');
        exit;
    }
}

//加载静态库
function load_static($type){
    if($type == 'css'){
        echo 
'<link rel="stylesheet" href="'.$GLOBALS['layui']['css'].'" media="all">
    <link rel="stylesheet" href="./templates/admin/css/public.css?v='.$GLOBALS['Ver'].'" media="all">
';
    }elseif($type == 'js'){
        echo 
'<script src="'.$GLOBALS['layui']['js'].'" charset="utf-8"></script>
<script src="./templates/admin/js/lay-config.js?v='.$GLOBALS['Ver'].'" charset="utf-8"></script>
<script>layui.config({version:"'.$GLOBALS['Ver'].'"})</script>
';
    }elseif($type == 'js.layui'){
        echo 
'<script src="'.$GLOBALS['layui']['js'].'" charset="utf-8"></script>
<script src="./templates/admin/js/lay-config.js?v='.$GLOBALS['Ver'].'" charset="utf-8"></script>
<script>layui.config({version:"'.$GLOBALS['Ver'].'"})</script>
';
    }
}

