<?php if(!defined('DIR')){header('HTTP/1.1 404 Not Found');header("status: 404 Not Found");exit;}

//获取请求参数
$page = trim($_GET['page']);

//layui版本路径,方便后期切换版本
$layui['js']  = $libs.'/Layui/v2.6.8/layui.js';$layui['css'] = $libs.'/Layui/v2.6.8/css/layui.css';
$Ver = !Debug?SysVer:SysVer.'.'.time();
$LoginConfig = unserialize($USER_DB['LoginConfig']);
define('offline',$global_config['offline'] == 1);
define('is_login',is_login());
//未登录,载入登录提示页
if(!is_login){
    require(DIR.'/templates/admin/page/LoginPrompt.php');
    exit;
}//已登录,检查是否需要验证二级密码
elseif(!empty($LoginConfig['Password2']) && !Check_Password2($LoginConfig)){
    require DIR.'/templates/admin/other/verify_pwd2.php';
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
    //var_dump(json_encode($day),$day_data);
}

//调试
if( $page == 'test' ) {
    $dirs = get_dir_list(DIR.'/templates/home');
    //var_dump($dirs);
    foreach ($dirs as $dir) {
        $path = DIR.'/templates/home/'.$dir; //目录完整路径
        //没有信息文件则跳过
        if(!is_file($path.'/info.json') ) {continue;}
        //读取主题信息
        $themes[$dir]['info'] = json_decode(@file_get_contents($path.'/info.json'),true);
        //是否支持配置
        $themes[$dir]['info']['config'] = is_file($path.'/config.php') ? '1':'0';
        //预览图优先顺序:png>jpg>info>default
        if(is_file($dirs.'/screenshot.png')){
            $themes[$dir]['info']['screenshot'] = "./templates/home/".$dir."/screenshot.png";
        }elseif(is_file($dirs.'/screenshot.jpg')){
            $themes[$dir]['info']['screenshot'] = "./templates/home/".$dir."/screenshot.jpg";
        }elseif(empty($themes[$dir]['info']['screenshot'])){ 
            $themes[$dir]['info']['screenshot'] = "./templates/admin/static/42ed3ef2c4a50f6d.png";
        }
        //var_dump($themes);
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
    $theme_config_db = get_db('user_config','v',['t'=>'theme','k'=>$theme,'uid'=>UID]);
    $theme_config_db = unserialize($theme_config_db);
    
    //如果不为空则合并数据
    if(!empty($theme_config_db)){
        $theme_config = array_merge ($theme_config,$theme_config_db);
    }
    //配置为空
    if(empty($theme_config)){
        exit("<h3>获取主题配置失败</h3>");
    }
    //var_dump($theme_config);
    require $config_path;
    exit;
}

//主题设置页面
if( $page == 'theme_home' || $page == 'theme_login' || $page == 'theme_transit' || $page == 'theme_register') {
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
        //var_dump($themes);
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
            $urls = [ "https://update.lm21.top/TwoNav/{$fn}_template.json"];
        }else{
            $cache = true;
        }
        
        //远程获取
        foreach($urls as $url){ 
            $Res = ccurl($url,3);
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
    //var_dump($themes);exit;
}


//菜单接口
if ($page == 'menu') {
    $menu = array( 
        ['title'=>'站点设置','href'=>'SiteSetting','icon'=>'fa fa-cog'],
        ['title'=>'主题设置','href'=>'theme_home','icon'=>'fa fa-magic'],
        ['title'=>'分类管理','href'=>'category_list','icon'=>'fa fa-list-ul'],
        ['title'=>'加密管理','href'=>'pwd_group','icon'=>'fa fa-lock'],
        ['title'=>'链接管理','icon'=>'fa fa-folder-open-o','href'=>'','child'=>
          [
            ['title'=>'链接列表','href'=>'link_list','icon'=>'fa fa-link'],
            ['title'=>'添加链接','href'=>'link_add','icon'=>'fa fa-plus-square-o'],
            ['title'=>'书签分享','href'=>'share','icon'=>'fa fa-external-link'],
            ['title'=>'导出导入','href'=>'data_control','icon'=>'fa fa-retweet'],
          ]
        ]);
    
    //扩展功能
    $extend = [];
    if($global_config['apply'] == 1 && check_purview('apply',1)){
        array_push($extend,['title'=>'收录管理','href'=>'expand/apply-admin','icon'=>'fa fa-pencil']);
    }
    if($global_config['guestbook'] == 1 && check_purview('guestbook',1)){ 
        array_push($extend,['title'=>'留言管理','href'=>'expand/guestbook-admin','icon'=>'fa fa-commenting-o']);
    }

    if(!empty($extend)){
        $extend = ['title'=>'扩展功能','icon'=>'fa fa-folder-open-o','href'=>'','child'=> $extend];
        array_push($menu,$extend);
    }

    
    //如果是管理员则追加菜单
    if($USER_DB['UserGroup'] == 'root'){
        array_push($menu,
        ['title'=>'网站管理','icon'=>'fa fa-wrench','href'=>'','child'=>
          [
            ['title'=>'系统设置','href'=>'root/sys_setting','icon'=>'fa fa-gears'],
            ['title'=>'授权管理','href'=>'root/vip','icon'=>'fa fa-diamond'],
            ['title'=>'默认设置','href'=>'root/default_setting','icon'=>'fa fa-heart-o'],
            ['title'=>'用户管理','href'=>'root/user_control','icon'=>'fa fa-user'],
            ['title'=>'用户分组','href'=>'root/users_control','icon'=>'fa fa-users'],
            ['title'=>'注册管理','href'=>'root/reg_control','icon'=>'fa fa-user-plus'],
            ['title'=>'站长工具','href'=>'root/tool','icon'=>'fa fa-exclamation-triangle'],
          ]
        ]);
    }
    $init = array( 'homeInfo'=>['title'=>'概要','href'=>'home'],'logoInfo'=>['title'=>'TwoNav','image'=>'./templates/admin/img/logo.png','href'=>'./?u='.U],'menuInfo'=>$menu);
    header('Content-Type:application/json; charset=utf-8');
    exit(json_encode($init));
}


//不带参数是载入框架
if(empty($page)){
    $site = unserialize(get_db('user_config','v',['uid'=>UID,'k'=>'s_site']));
    $favicon = ( !empty($site['site_icon_file'])) ? $site['site_icon'] : './favicon.ico';
    require DIR."/templates/admin/index.php";
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
'<link rel="stylesheet" href="'.$GLOBALS['libs'].'/Layui/v2.6.8/css/layui.css" media="all">
    <link rel="stylesheet" href="./templates/admin/css/public.css?v='.$GLOBALS['Ver'].'" media="all">
';
    }elseif($type == 'js'){
        echo 
'<script src="'.$GLOBALS['libs'].'/Layui/v2.6.8/layui.js" charset="utf-8"></script>
<script src="./templates/admin/js/lay-config.js?v='.$GLOBALS['Ver'].'" charset="utf-8"></script>
<script>layui.config({version:"'.$GLOBALS['Ver'].'"})</script>
';
    }elseif($type == 'js.layui'){
        echo 
'<script src="'.$GLOBALS['libs'].'/Layui/v2.6.8/layui.js" charset="utf-8"></script>
<script>layui.config({version:"'.$GLOBALS['Ver'].'"})</script>
';
    }
}

