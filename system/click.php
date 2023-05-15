<?php if(!defined('DIR')){Not_Found();}AccessControl();
//负责过渡页/跳转/隐私保护/密码访问
$id = intval($_GET['id']);

//如果id为空,则显示404
if(empty($id)){Not_Found();}

//查询链接信息
$where['lid'] = $id;
$where['uid'] = UID;
$where['status'] = 1;
$link = get_db('user_links','*',$where);

//查找失败时显示404
if(empty($link)){Not_Found();}

//站点设置和站点图标
$site = unserialize(get_db('user_config','v',['uid'=>UID,'k'=>'s_site']));
$site['Title']  =  $site['title'].(empty($site['subtitle'])?'':' - '.$site['subtitle']);
//免费用户请保留版权,谢谢!
$copyright = empty($global_config['copyright'])?'<a target="_blank" href="https://gitee.com/tznb/TwoNav">Copyright © TwoNav</a>':$global_config['copyright'];
$ICP = empty($global_config['ICP'])?'':'<a target="_blank" href="https://beian.miit.gov.cn">'.$global_config['ICP'].'</a>';
$favicon = ( !empty($site['site_icon_file'])) ? $site['site_icon'] : './favicon.ico';

//取登录状态
$is_login = is_login();

//取父分类和祖分类信息
$info_c = ['cid','fid','property','status','pid'];
$category_parent  = get_db('user_categorys',$info_c,['uid'=>UID,'cid'=>$link['fid']]);
$category_ancestor  = empty($category_parent['fid']) ? [] : get_db('user_categorys',$info_c,['uid'=>UID,'cid'=>$category_parent['fid']]);

//未登录时判断各种状态
if(!$is_login){
    //初始化session
    session_start();
    
    //从来路中匹配书签分享的SID
    if(preg_match('/share=(.{8})/',$_SERVER['HTTP_REFERER'],$match) ) { 
        $share = get_db('user_share','*',['uid'=>UID,'sid'=>$match[1]]);
        if(isset($share['pv']) && $share['pv'] == 1){
            $pv = empty($share['pwd']) || $_SESSION['verify']['share'][$share['id']] == $share['pwd'];
        }
    }
    
    //判断链接是否停用/私有
    if($link['status'] == 0){
        exit('很抱歉,链接已停用!您无权限查看,如果您是管理员,请先登录!');
    }elseif($link['property'] == 1 && !$pv){
        exit('很抱歉,链接是私有的!您无权限查看,如果您是管理员,请先登录!');
    }
    
    //判断父分类状态
    if($category_parent['status'] == 0 ){
        exit('很抱歉,页面所属的分类已停用!您无权限查看,如果您是管理员,请先登录!');
    }
    if($category_parent['property'] == 1 && !$pv){
        exit('很抱歉,页面所属的分类是私有的!您无权限查看,如果您是管理员,请先登录!');
    }
    
    //判断祖分类状态
    if($category_ancestor['status'] === 0 ){
        exit('很抱歉,页面所属的祖分类已停用!您无权限查看,如果您是管理员,请先登录!');
    }
    if($category_ancestor['property'] == 1 && !$pv){
        exit('很抱歉,页面所属的祖分类是私有的!您无权限查看,如果您是管理员,请先登录!');
    }
    
    //判断链接是否加密
    if(!empty($link['pid'])){
        $verify_type = 'link_pwd';
        $password = get_db('user_pwd_group','password',['uid'=>UID,'pid'=>$link['pid']]);
        if($_SESSION['verify']['link'][$link['lid']] != $password){
            require DIR.'/templates/admin/other/verify_link_pwd.php';
            exit();
        }
    }
    //判断父分类是否加密
    if(empty($link['pid']) && !empty($category_parent['pid'])){
        $verify_type = 'category_pwd';
        $password = get_db('user_pwd_group','password',['uid'=>UID,'pid'=>$category_parent['pid']]);
        if($_SESSION['verify']['category'][$category_parent['cid']] != $password){
            require DIR.'/templates/admin/other/verify_link_pwd.php';
            exit();
        }
    }
    //判断祖分类是否加密
    if(empty($link['pid']) && empty($category_parent['pid']) && !empty($category_ancestor['pid'])){
        $verify_type = 'category_pwd';
        $password = get_db('user_pwd_group','password',['uid'=>UID,'pid'=>$category_ancestor['pid']]);
        if($_SESSION['verify']['category'][$category_ancestor['cid']] != $password){
            require DIR.'/templates/admin/other/verify_link_pwd.php';
            exit();
        }
    }
}

//取模板信息
require DIR ."/system/templates.php";
$dir_path = DIR.'/templates/transit/'.$s_templates['transit'];
$theme_dir = str_replace(DIR.'/templates/transit',"./templates/transit",$dir_path);
$transit_path = $dir_path.'/index.php';
//检查是否存在,不存在则使用默认
if(!is_file($transit_path)){
    $transit_path= DIR.'/templates/transit/default/index.php';
}

//统计点击数
write_user_count(date('Ym'),'click_Ym');
write_user_count(date('Ymd'),'click_Ymd');
update_db("user_links", ["click[+]"=>1],['uid'=>UID,'lid'=>$id]);



//读取用户主题配置
$theme_config_db = unserialize(get_db('user_config','v',['t'=>'theme_transit','k'=>$s_templates['transit'],'uid'=>UID]));

//读取默认主题配置
$theme_info = json_decode(@file_get_contents($dir_path.'/info.json'),true);
$theme_config = empty($theme_info['config']) ? []:$theme_info['config'];
$theme_ver = !Debug?$theme_info['version']:$theme_info['version'].'.'.time();

//合并配置数据
$theme_config = empty($theme_config_db) ? $theme_config : array_merge ($theme_config??[],$theme_config_db??[]);

//如果主题信息声明支持扩展字段
if($global_config['link_extend'] == 1 && check_purview('link_extend',1) && in_array($theme_info['support']['link_extend'],["true","1"])){
    $extend = empty($link['extend']) ? [] : unserialize($link['extend']);
}

//如果存在备用链接,则强制载入过渡页
if(!empty($link['url_standby'])) {
    $link['url_standby'] = unserialize($link['url_standby']);
    //主链优先模式
    if(!empty($site['main_link_priority']) && $site['link_model'] != 'Transition'){
        $code = get_http_code($link['url'],3,($site['main_link_priority'] == 1)); 
        if(in_array(intval($code),[200,301,302,401]) ){ 
            $site['link_model'] =  $site['link_model'] == 'direct' ? '302' : $site['link_model'];
        }else{
            require $transit_path;
            exit;
        }
    }else{
        require $transit_path;
        exit;
    }
}

if ($site['link_model'] == '302'){ //302重定向(临时)
    header("HTTP/1.1 302 Moved Permanently");
    header("Location: ".$link['url']);
    exit;
}elseif($site['link_model'] == '301'){  //301重定向(永久)
    header("HTTP/1.1 301 Moved Permanently");
    header("Location: ".$link['url']);
    exit;
}elseif($site['link_model'] == 'Privacy'){ //隐私保护_header
    header("Content-type: text/html; charset=utf-8"); 
    header("Refresh:0;url=".$link['url']);
    echo '<html lang="zh-ch"><head><title>正在保护您的隐私..</title><meta name="referrer" content="same-origin"></head>';
    exit;
}elseif($site['link_model'] == 'Privacy_js'){ //隐私保护_js
    header("Content-type: text/html; charset=utf-8");
    echo '<html lang="zh-ch"><head><title>正在保护您的隐私..</title><meta name="referrer" content="same-origin"><script>window.location.href="'.$link['url'].'"</script></head>';
    exit;
}elseif($site['link_model'] == 'Privacy_meta'){ //隐私保护_meta
    header("Content-type: text/html; charset=utf-8");
    echo '<html lang="zh-ch"><head><title>正在保护您的隐私..</title><meta name="referrer" content="same-origin"><meta http-equiv="refresh" content="0;url='.$link['url'].'"></head>';
    exit;
}else{ //Transition 过渡页
    require $transit_path;
    exit;
}

//返回404
function Not_Found() {
    header('HTTP/1.1 404 Not Found');header("status: 404 Not Found");exit;
}
