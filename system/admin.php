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
    $theme_config_db = get_db('user_config','v',['t'=>'theme_'.$_GET['fn'],'k'=>$theme,'uid'=>UID]);
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

//主题设置页面
if( $page == 'theme_home' || $page == 'theme_login' || $page == 'theme_transit' || $page == 'theme_register' || $page == 'theme_guide' || $page == 'theme_article') {
    if(!check_purview('theme_in',1)){
        require(DIR.'/templates/admin/page/404.php');
        exit;
    }
    $fn = str_replace('theme_','',$page);
    $dirs = get_dir_list(DIR.'/templates/'.$fn);
    
    foreach ($dirs as $dir) {
        $path = DIR.'/templates/'.$fn.'/'.$dir; //目录完整路径
        //没有信息文件则跳过
        if(!is_file($path.'/info.json') ) {continue;}
        //读取主题信息
        $themes[$dir]['info'] = json_decode(@file_get_contents($path.'/info.json'),true);
        //是否支持配置
        $themes[$dir]['info']['config'] = is_file($path.'/config.php') ? '1':'0';
        //预览图优先顺序:png>jpg>info>default
        if(is_file($path.'/screenshot.jpg')){
            $themes[$dir]['info']['screenshot'] = "./templates/$fn/$dir/screenshot.jpg";
        }elseif(is_file($path.'/screenshot.png')){
            $themes[$dir]['info']['screenshot'] = "./templates/$fn/$dir/screenshot.png";
        }elseif(empty($themes[$dir]['info']['screenshot'])){ 
            $themes[$dir]['info']['screenshot'] = "./templates/admin/static/42ed3ef2c4a50f6d.png";
        }
    }
    
    //获取当前主题
    require "./system/templates.php";

    //在线主题处理
    if ( !$global_config['offline'] && $USER_DB['UserGroup'] === 'root'){ 
        
        if(preg_match('/^v.+-(\d{8})$/i',SysVer,$matches)){
            $sysver = intval( $matches[1] );//取版本中的日期
        }else{
            exit("获取程序版本异常");
        }
        
        //读取缓存
        $template = get_db('global_config','v',['k'=>$page.'_cache']);
        if(!empty($template)){
            $data = json_decode($template, true);
        }
        
        //没有缓存 或 禁止缓存 或 缓存过时
        if(empty($template) ||   $_GET['cache'] === 'no'  || time() -  $data["time"] > 1800 ){ 
            $urls = [
                "lm21" => "https://update.lm21.top/TwoNav/{$fn}_template.json",
                "gitee" => "https://gitee.com/tznb/twonav_updata/raw/master/{$fn}_template.json"
            ];
            $Source = $global_config['Update_Source'] ?? '';
            if (!empty($Source) && isset($urls[$Source])) {
                $urls = [$Source => $urls[$Source]];
            }
        }else{
            $cache = true;
        }
        //读取超时参数
        $overtime = !isset($global_config['Update_Overtime']) ? 3 : ($global_config['Update_Overtime'] < 3 || $global_config['Update_Overtime'] > 60 ? 3 : $global_config['Update_Overtime']);
        //远程获取
        foreach($urls as $key => $url){ 
            $Res = ccurl($url,$overtime);
            $data = json_decode($Res["content"], true);
            if($data["code"] == 200 ){ //如果获取成功
                $data["time"] = time(); //记录当前时间
                write_global_config($page.'_cache',json_encode($data),$fn.'_模板缓存');
                break; //跳出循环.
            } 
        }
        //解析
        foreach($data["data"] as $key){
            $path = DIR.'/templates/'.$fn.'/'.$key["dir"];
            if( is_dir($path) ) {  //本地存在
                $value = $key["dir"];
                //检查是否可以更新
                $update = str_replace('/','',$themes[$value]['info']['update']); //本地主题版本
                $update_new = str_replace('/','',$key["update"]); //远程主题版本
                if( $sysver >= intval($key["low"])  && $sysver <= intval($key["high"]) &&  $update < $update_new ){
                    $themes[$value]['info']['up'] = '1';
                }
            }else{
                //判断是否适配当前系统版本
                if( $sysver >= intval($key["low"])  && $sysver <= intval($key["high"]) ){
                    $value = $key["dir"];
                    $themes[$value]['info'] = json_decode(json_encode($key),true);
                }
            }
        }
        //来源策略 (用于Gitee作为图床反防盗链)
        if(!empty($data['referrer'])){
            define('referrer',$data['referrer']);
        }
    }
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

