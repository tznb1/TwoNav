<?php if(!defined('DIR')){Not_Found();}AccessControl();
//负责过渡页/跳转/隐私保护/密码访问
$id = intval($_GET['id']);
//IP数统计
count_ip();
//如果id为空,则显示404
if(empty($id)) Not_Found();

//查询链接信息
$where['lid'] = $id;
$where['uid'] = UID;
$where['status'] = 1;
$link = get_db('user_links','*',$where);

//查找失败时显示404
if(empty($link)) Not_Found();

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
            $c = 'verify';
            require DIR."/system/templates.php";
            require $index_path;
            exit();
        }
    }
    //判断父分类是否加密
    if(empty($link['pid']) && !empty($category_parent['pid'])){
        $verify_type = 'category_pwd';
        $password = get_db('user_pwd_group','password',['uid'=>UID,'pid'=>$category_parent['pid']]);
        if($_SESSION['verify']['category'][$category_parent['cid']] != $password){
            $c = 'verify';
            require DIR."/system/templates.php";
            require $index_path;
            exit();
        }
    }
    //判断祖分类是否加密
    if(empty($link['pid']) && empty($category_parent['pid']) && !empty($category_ancestor['pid'])){
        $verify_type = 'category_pwd';
        $password = get_db('user_pwd_group','password',['uid'=>UID,'pid'=>$category_ancestor['pid']]);
        if($_SESSION['verify']['category'][$category_ancestor['cid']] != $password){
            $c = 'verify';
            require DIR."/system/templates.php";
            require $index_path;
            exit();
        }
    }
}


//统计点击数
write_user_count(date('Ym'),'click_Ym');
write_user_count(date('Ymd'),'click_Ymd');
update_db("user_links", ["click[+]"=>1],['uid'=>UID,'lid'=>$id]);

//通用数据初始化
require DIR."/system/templates.php";

//如果主题信息声明支持扩展字段
if($global_config['link_extend'] == 1 && check_purview('link_extend',1) && in_array($theme_info['support']['link_extend'],["true","1"])){
    $extend = empty($link['extend']) ? [] : unserialize($link['extend']);
}

//载入过渡页设置
$transition_page = unserialize(get_db("user_config", "v", ["uid"=>UID,"k"=>"s_transition_page"]));

//关键字处理
if(!empty($link['url_standby']) || $site['link_model'] == 'Transition'){
    if(empty($link['keywords'])){
       if($transition_page['default_keywords'] == '0'){
           $link['keywords'] = $link['title'];
       }else if($transition_page['default_keywords'] == '1'){
           $link['keywords'] = $site['keywords'];
       }else{
           $link['keywords'] = $link['title'];
       }
    }
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
            require $index_path;
            exit;
        }
    }else{
        require $index_path;
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
    require $index_path;
    exit;
}
