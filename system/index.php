<?php if(!defined('DIR')){header('HTTP/1.1 404 Not Found');header("status: 404 Not Found");exit;}
//主页入口
define('is_login',is_login());
//var_dump($global_config['offline']);

//判断用户组,是否允许未登录时访问主页
if(!is_login && !check_purview('Common_home',1)){
    header("HTTP/1.1 302 Moved Permanently");
    header("Location: ./?c=admin");
    exit;
}
//载入站点设置
$site = unserialize(get_db('user_config','v',['uid'=>UID,'k'=>'s_site']));
$site['Title']  =  $site['title'].(empty($site['subtitle'])?'':' - '.$site['subtitle']);
//免费用户请保留版权,谢谢!
$copyright = empty($global_config['copyright'])?'<a target="_blank" href="https://gitee.com/tznb/twonav">Copyright © TwoNav</a>':$global_config['copyright'];
$ICP = empty($global_config['ICP'])?'':'<a target="_blank" href="https://beian.miit.gov.cn">'.$global_config['ICP'].'</a>';
$favicon = ( !empty($site['site_icon_file'])) ? $site['site_icon'] : './favicon.ico';
//读取默认模板信息
require DIR ."/system/templates.php";
//参数指定主题优先
$theme = trim(@$_GET['theme']);
if ( !empty ($theme) ){
    $dir_path = DIR.'/templates/home/'.$theme;
    $index_path = $dir_path.'/index.php';
}else{
    $is_Pad = preg_match('/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i',$_SERVER['HTTP_USER_AGENT']);
    $theme = $is_Pad?$s_templates['home_pad']:$s_templates['home_pc'];
    $dir_path = DIR.'/templates/home/'.$theme;
    $index_path = $dir_path.'/index.php';
}
//检查是否存在,不存在则使用默认
if(!is_file($index_path)){
    $dir_path= DIR.'/templates/home/default';
    $index_path = $dir_path.'/index.php';
}
//相对路径
$theme_dir = str_replace(DIR.'/templates/home',"./templates/home",$dir_path);
//主题信息
$theme_info = json_decode(@file_get_contents($dir_path.'/info.json'),true);
//支持属性
$support_subitem = $theme_info['support']['subitem']??0; //0.不支持子分类 1.分类栏支持 2.链接栏支持 3.都支持
$support_category_svg = $theme_info['support']['category_svg']??0; //0.不支持 1.支持
//主题配置(默认)
$theme_config = empty($theme_info['config']) ? []:$theme_info['config'];
//主题配置(用户)
$theme_config_db = get_db('user_config','v',['t'=>'theme','k'=>$theme,'uid'=>UID]);
$theme_config_db = unserialize($theme_config_db);
//合并配置数据
$theme_config = empty($theme_config_db) ? $theme_config : array_merge ($theme_config,$theme_config_db);
//主题版本(调试时追加时间戳)
$theme_ver = !Debug?$theme_info['version']:$theme_info['version'].'.'.time();
$site['ex_theme'] = in_array($theme,['snail-nav','heimdall']); //例外主题,不支持热门网址/最新网址/输出上限
//分类查找条件
$categorys = []; //声明一个空数组
$content = ['cid(id)','name','property','font_icon','icon','description'];//需要的内容
$where['uid'] = UID; 
$where['fid'] = 0;
$where['status'] = 1;
$where['ORDER'] = ['weight'=>'ASC'];
//未登录仅查找公开分类
if(!is_login){
    $where['property'] = 0;
}

//建立索引
$fid_s = select_db('user_categorys',['cid','fid','pid'],['uid'=>UID,'status'=>1]);
$fid_s = array_column($fid_s,null,'cid');

//根据分类ID查询二级分类
function get_category_sub($id) {
    global $share,$data;
    //禁止搜索非数字
    if(intval($id) == 0){
        return;
    }
    //书签分享>限定范围内的分类ID
    if(!empty($share)){
        $where['cid'] = $data;
    }
    $content = ['cid(id)','name','property','font_icon','icon','description'];
    $where['uid'] = UID;
    $where['fid'] = intval($id);
    $where['status'] = 1;
    $where['ORDER'] = ['weight'=>'ASC'];
    if(!is_login){
        $where['property'] = 0;
    }
    //书签分享>私有可见
    if(isset($share['pv']) && $share['pv'] == 1){
        unset($where['property']);
    }
    $category_sub = select_db('user_categorys',$content,$where);
    return $category_sub;
}

//根据分类id查找链接
function get_links($fid) {
    global $site,$fid_s,$share,$data,$u;
    $where = [];
    $where = ["uid"=> UID];
    $where['fid'] = intval($fid);
    $where['status'] = 1;
    $where['ORDER']['weight'] = 'ASC';
    $where['ORDER']['lid'] = 'ASC';
    if(!is_login){
        $where['property'] = 0;

    }
    //书签分享>私有可见
    if(isset($share['pv']) && $share['pv'] == 1){
        unset($where['property']);
    }
    //书签分享>链接分享
    if(isset($share['type']) && $share['type'] == 2){
        $where['lid'] = $data;
        unset($where['fid']);
    }
    
    //虚拟分类,根据特定条件查找
    if($fid == 'top_link' || $fid == 'new_link' ){
        unset($where['ORDER']);
        if(!is_login) {
            $where['fid'] = get_open_category();
        }else{
            unset($where['fid']);
        }
        if($fid == 'top_link'){
            $where['ORDER']['click'] = 'DESC';
            $where['LIMIT'] = $site['top_link'];
        }elseif($fid == 'new_link'){
            $where['ORDER']['add_time'] = 'DESC';
            $where['LIMIT'] = $site['new_link'];
        }
        $where['ORDER']['lid'] = 'DESC';
    //输出上限&不在子页面&例外主题
    }elseif($site['max_link'] > 0 && empty(Get('oc')) && !$site['ex_theme']){
        $count = count_db('user_links',$where);
        $where['LIMIT'] = $site['max_link'];
    }
    $links = select_db('user_links',['lid(id)','fid','property','title','url(real_url)','url_standby','description','icon','click','pid'],$where);
    foreach ($links as $key => $link) {
        $click = false; $lock = false;
        
        //直连模式,但存在备用链接
        if ($site['link_model'] == 'direct' && !empty($link['url_standby'])){
            $click = true;
        }
        
        //未登录,判断是否加密
        if(!is_login){
            //链接加密了
            if(!empty($link['pid'])){
                $click = true; $lock = true;
            //父分类加密了 或 祖分类加密了
            }elseif(!empty($fid_s[$link['fid']]['pid']) || (!empty($fid_s[$link['fid']]['fid']) && !empty($fid_s[$fid_s[$link['fid']]['fid']]['pid'])) ){
                $click = true; $lock = true;
            }
        }
        
        if($click || $site['link_model'] != 'direct'){
            $links[$key]['url'] = "./index.php?c=click&id={$link['id']}&u=".U;
            if($lock){
                $links[$key]['real_url'] = $links[$key]['url']; //篡改真实URL,防止泄密
                if(isset($share['sid'])){
                    $links[$key]['url'] .='&share='.$share['sid'];
                }
            }
        }else{
            $links[$key]['url'] = $link['real_url'];
        }

        //获取图标链接
        $links[$key]['ico'] = $lock ? $GLOBALS['libs'].'/Other/lock.svg' : geticourl($site['link_icon'],$link);
    }
    if($site['max_link'] > 0 && $count > $site['max_link'] && empty(Get('oc')) && !$site['ex_theme']){
        $oc_url = "./index.php?u={$u}&oc={$fid}" . (empty($_GET['theme']) ? '':"&theme={$_GET['theme']}");
        array_push($links,['id'=>0,'title'=>'查看全部','url'=>$oc_url,'real_url'=>$oc_url,'description'=>'该分类共有'.$count.'条数据','ico'=>'./favicon.ico']);
    }
   
    return $links;
}

//书签分享
$share = Get('share');
if(!empty($share)){
    $share = get_db('user_share','*',['uid'=>UID,'sid'=>$share]);
    if(empty($share)){
        $content = '分享已被删除,请联系作者!';
        require DIR.'/templates/admin/page/404.php';
        exit;
    }
    //判断是否过期
    if(time() > $share['expire_time']){
        $content = '分享已过期,请联系作者!';
        require DIR.'/templates/admin/page/404.php';;
        exit;
    }
    //判断是否加密
    if(!empty($share['pwd']) && !is_login){
        session_start();
        if($_SESSION['verify']['share'][$share['id']] != $share['pwd']){
            require DIR.'/templates/admin/other/verify_share_pwd.php';
            exit;
        }
    }
    
    $data = json_decode($share['data']);
    //判断分享类型(1.分类 2.链接)
    if($share['type'] == 1){
        $where['cid'] = $data;
        if($share['pv'] == 1){
            unset($where['property']);
        }
    }else if($share['type'] == 2){
        $category_parent = [['name' => $share['name'] ,"font_icon" =>"fa fa-bookmark-o" , "id" => 'share' ,"description" => "书签分享"]];
        $categorys = $category_parent;
    }
    
    //浏览计次
    update_db("user_share", ["views[+]"=>1],['uid'=>UID,'id'=>$share['id']]);
}

//如果为空则查找分类
if($category_parent == []){
    //查找一级分类
    $category_parent = select_db('user_categorys',$content,$where);
    //查找二级分类
    foreach ($category_parent as $key => $category) {
        $where['fid'] = $category['id'];
        $category_subitem = select_db('user_categorys',$content,$where);
        $category['subitem_count'] = count($category_subitem);
        $category_parent[$key]['subitem_count'] = $category['subitem_count'];
        array_push($categorys,$category);
        $categorys = array_merge ($categorys,$category_subitem);
    }
}
//书签分享/例外主题禁止热门和最新
if(empty($_GET['share']) && !$site['ex_theme']){
    //非指定分类页面
    if(empty(Get('oc'))){
        //热门链接
        if($site['top_link'] > 0){
            $top_link = ['name' => "热门网址","font_icon" =>"fa fa-bookmark-o" , "id" => 'top_link' ,"description" => ""];
            array_unshift($category_parent,$top_link);
            array_unshift($categorys,$top_link);
        }
        //最新链接
        if($site['new_link'] > 0){
            $new_link = ['name' => "最新网址","font_icon" =>"fa fa-bookmark-o" , "id" => 'new_link' ,"description" => ""];
            array_unshift($category_parent,$new_link);
            array_unshift($categorys,$new_link);
        }
    }else{
        unset($where['fid']);
        $where['cid'] = Get('oc');
        $categorys = select_db('user_categorys',$content,$where);
        $category_parent = $categorys;
    }
}


//访问统计
write_user_count(date('Ym'),'index_Ym');
write_user_count(date('Ymd'),'index_Ymd');
//载入模板
require($index_path);