<?php if(!defined('DIR')){header('HTTP/1.1 404 Not Found');header("status: 404 Not Found");exit;}AccessControl();

//是否载入引导页
if(@$global_config['default_page'] == 2){
    if(empty(Get('u')) && empty($_COOKIE['Default_User'])){
        $c = 'guide';
        require DIR."/system/templates.php";
        require $index_path;
        exit;
    }
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
    if(!empty($share['pwd']) && !is_login()){
        session_start();
        if($_SESSION['verify']['share'][$share['id']] != $share['pwd']){
            $c = 'verify';$_GET['c'] = 'share';
            require DIR."/system/templates.php";
            require $index_path;
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


//通用数据初始化
require DIR."/system/templates.php";

//判断用户组,是否允许未登录时访问主页
if(!is_login && ($global_config['Privacy'] == 1 || !check_purview('Common_home',1))){
    header("HTTP/1.1 302 Moved Permanently");
    header("Location: ./?c=admin&u=".U);
    exit;
}

//例外主题,不支持热门网址/最新网址/输出上限
$site['ex_theme'] = in_array($theme,['snail-nav','heimdall']); 

//分类查找条件
$categorys = []; //声明一个空数组
$content = ['cid(id)','fid','name','property','font_icon','icon','description'];//需要的内容
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
    $content = ['cid(id)','name','fid','property','font_icon','icon','description'];
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
    //输出上限&不在子页面&例外主题&书签分享
    }elseif($site['max_link'] > 0 && empty(Get('oc')) && !$site['ex_theme'] && empty($_GET['share'])){
        $count = count_db('user_links',$where);
        $where['LIMIT'] = $site['max_link'];
        $max_link = true;
    }
    $links = select_db('user_links',['lid(id)','fid','property','title','url(real_url)','url_standby','description','icon','click','pid','extend'],$where);
    foreach ($links as $key => $link) {
        $click = false; $lock = false;
        
        //直连模式,但存在备用链接
        if ($site['link_model'] == 'direct' && $site['main_link_priority'] != '3' && !empty($link['url_standby'])){
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
        $links[$key]['type'] = 'link';
    }
    //处理扩展信息
    if($GLOBALS['global_config']['link_extend'] == 1 && check_purview('link_extend',1) && in_array($GLOBALS['theme_info']['support']['link_extend'],["true","1"])){
        foreach ($links as &$link) {
            if(!empty($link['extend'])){
                $link = array_merge ($link,unserialize($link['extend']));
            }
        }

    }
    //生成文章链接, 条件:非隐藏,且主题未声明不显示文章
    if( intval($site['article_visual'] ?? '1') > 0 && $GLOBALS['theme_info']['support']['article'] != 'notdisplay'){
        $articles = get_article_list($fid);
        foreach ($articles['data'] as $article) {
            $url = "./index.php?c=article&id={$article['id']}&u={$u}";
            if($site['article_icon'] == '1'){ //站点图标
                $icon = $GLOBALS['favicon'];
            }elseif($site['article_icon'] == '2' && !empty($article['cover'])){ //封面
                $icon = $article['cover'];
            }else{ //首字
                $icon = './system/ico.php?text='.mb_strtoupper(mb_substr($article['title'], 0, 1));
            }
            $article_link = ['type'=>'article','id'=>0,'title'=>htmlspecialchars($article['title'],ENT_QUOTES),'url'=>$url,'real_url'=>$url,'description'=> htmlspecialchars($article['summary'],ENT_QUOTES),'ico'=>$icon,'icon'=>$icon];
            //判断靠前还是靠后
            if($site['article_visual'] == '1'){
                array_unshift($links,$article_link);
            }else{
                array_push($links,$article_link);
            }
            
        }
    }
    
    
    if($max_link && $count > $site['max_link']){
        $oc_url = "./index.php?u={$u}&oc={$fid}" . (empty($_GET['theme']) ? '':"&theme={$_GET['theme']}");
        array_push($links,['id'=>0,'title'=>'查看全部','url'=>$oc_url,'real_url'=>$oc_url,'description'=>'该分类共有'.$count.'条数据','ico'=>'./favicon.ico']);
    }
   
    return $links;
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
count_ip();
//载入模板
require($index_path);