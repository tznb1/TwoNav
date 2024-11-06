<?php if(!defined('DIR')){header('HTTP/1.1 404 Not Found');header("status: 404 Not Found");exit;}

//读取全局模板配置
$global_templates = unserialize(get_db("global_config",'v', ["k" => "s_templates"]));

if(defined('UID') && $c != 'guide'){
    //读取用户模板配置
    $s_templates = unserialize(get_db("user_config", "v", ["uid"=>UID,"k"=>"s_templates"]));
    //没找到用户模板配置
    if(empty($s_templates)){
        $s_templates = $global_templates;
        insert_db("user_config", ["uid" => UID,"k"=>"s_templates","v"=>$global_templates,"t"=>"config","d" => '默认模板']);
    }
}

//根据请求来读取模板名
if($c == 'index'){
    $theme = trim(@$_GET['theme']); //主题预览
    if (empty($theme)){
        $is_Pad = preg_match('/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i',$_SERVER['HTTP_USER_AGENT']);
        $theme = $is_Pad ? $s_templates['home_pad'] : $s_templates['home_pc'];
    }
    $dir_path = DIR.'/templates/home';
}elseif($c == 'click'){ //过渡
    $theme = $s_templates['transit'];
    $dir_path = DIR.'/templates/transit';
}elseif($c == 'verify'){ //验证
    if($_GET['c'] == 'click'){
        $data['title'] = $link['title'];
        $data['tip'] = '查看加密链接';
        $data['input_tip'] = '请输入密码';
        $data['post_url'] = "./index.php?c=verify&type=link_pwd&u={$u}&id={$_GET['id']}";
        $config = unserialize(get_db("user_config", "v", ["k" => "s_verify_page","uid"=>$USER_DB['ID']]));
        $data['get_tip'] = $config['link_tip'];
    }elseif($_GET['c'] == 'share'){
        $data['title'] = $share['name'];
        $data['tip'] = '查看分享书签';
        $data['input_tip'] = '请输入提取码';
        $data['post_url'] = "./index.php?c=verify&type=share_pwd&u={$u}&share={$_GET['share']}";
        $config = unserialize(get_db("user_config", "v", ["k" => "s_verify_page","uid"=>$USER_DB['ID']]));
        $data['get_tip'] = $config['share_tip'];
    }elseif($_GET['c'] == 'pwd2'){
        $data['title'] = '验证二级密码';
        $data['tip'] = '验证二级密码';
        $data['input_tip'] = '请输入二级密码';
        $data['post_url'] = "./index.php?c=verify&type=pwd2&u={$u}";
    }
    $theme = $s_templates['verify'];
    $dir_path = DIR.'/templates/verify';
}elseif($c == 'article'){ //文章
    $theme = $s_templates['article'];
    $dir_path = DIR.'/templates/article';
}elseif($c == 'guestbook'){ //留言
    $theme = $s_templates['guestbook'];
    $dir_path = DIR.'/templates/guestbook';
}elseif($c == 'apply'){ //收录
    $theme = $s_templates['apply'];
    $dir_path = DIR.'/templates/apply/';
}elseif($c == $global_config['Login']  || $c == $USER_DB['Login']){ //登录
    $theme = $s_templates['login'];
    $dir_path = DIR.'/templates/login';
}elseif($c == $global_config["Register"] ){ //注册
    $theme = $global_templates['register'];
    $dir_path = DIR.'/templates/register';
}elseif($c == 'guide'){ //引导页,由主页修改$c
    $theme = $global_templates['guide'];
    $dir_path = DIR.'/templates/guide';
    $s_site = unserialize( get_db("global_config", "v", ["k" => "s_site"]));//读入默认站点配置
}

//模板类型(用于读取配置)
$templates_type = substr($dir_path, strrpos($dir_path, "/") + 1) ;
$config_type = in_array($templates_type,['guide','register']) ? 'global' : 'user';
//无权限或不存在使用默认
if( !check_purview('theme_in',1) || !is_file("{$dir_path}/{$theme}/index.php")){
    $theme = 'default';
    $dir_path .= '/default';
    $index_path = $dir_path.'/index.php';
}else{
    $dir_path .= '/'.$theme;
    $index_path = $dir_path.'/index.php';
}

//相对路径
$theme_dir = str_replace(DIR,'.',$dir_path);
//主题信息
$theme_info = json_decode(@file_get_contents($dir_path.'/info.json'),true);
//主题配置(默认)
$theme_config = empty($theme_info['config']) ? []:$theme_info['config'];

//读取主题配置
if(defined('UID') && !in_array($templates_type,['guide','register'])){
    $theme_config_db = get_db('user_config','v',['t'=>"theme_{$templates_type}",'k'=>$theme,'uid'=>UID]);
}else{
    $theme_config_db = get_db('global_config','v',['k'=>"theme_{$templates_type}_{$theme}"]);
}
$theme_config_db = unserialize($theme_config_db);

//合并配置数据
$theme_config = empty($theme_config_db) ? $theme_config : array_merge ($theme_config,$theme_config_db);
//主题版本
$theme_ver = Debug ? "{$theme_info['version']}.".time() : $theme_info['version'];

//版权信息
$copyright = empty($global_config['copyright'])?'<a target="_blank" href="https://gitee.com/tznb/TwoNav">Copyright © TwoNav</a>':$global_config['copyright'];

//备案信息
$ICP = empty($global_config['ICP'])?'':'<a target="_blank" href="https://beian.miit.gov.cn">'.$global_config['ICP'].'</a>';
//访问域名(伪静态用)
$HOST = get_HOST();
$OEM = get_OEM();
//静态链接
define('static_link',$global_config['static_link'] > 0);

$urls['login'] = static_link ? "$HOST/login":"./?c=login";
$urls['register'] = static_link ? "$HOST/register":"./?c=register";
if($config_type == 'user'){
    //载入站点设置
    $site = unserialize(get_db('user_config','v',['uid'=>UID,'k'=>'s_site']));
    //如果没有权限则清除自定义代码
    if(!check_purview('header',1)){$site['custom_header'] = '';}
    if(!check_purview('footer',1)){$site['custom_footer'] = '';}
    
    //主页标题(分类页面显示分类名-描述)
    if(isset($_GET['cid']) && intval($_GET['cid']) > 0){
        $teml_cd = get_db('user_categorys',['name','description'],['uid'=>UID,'cid'=>intval($_GET['cid'])]);
        $site['title'] = $teml_cd['name']; $site['subtitle'] = $teml_cd['description']; 
    }
    $site['Title']  =  $site['title'].(empty($site['subtitle'])?'':' - '.$site['subtitle']);
    
    //站点图标
    $favicon = ( !empty($site['site_icon_file'])) ? $site['site_icon'] : './favicon.ico';
    //相关入口
    $UUID = get_UUID();
    $urls['home'] = static_link ? "$HOST/{$UUID}.html":"./?u={$u}";
    $urls['admin'] = static_link ? "$HOST/admin-{$UUID}.html":"./?c=admin&u={$u}";
    $urls['apply'] = static_link ? "$HOST/apply-{$UUID}.html":"./?c=apply&u={$u}";
    $urls['guestbook'] = static_link ? "$HOST/guestbook-{$UUID}.html":"./?c=guestbook&u={$u}";
}else{
    //站点图标
    $favicon = './favicon.ico';
}

//是否启用收录
function is_apply(){
    $apply_user = unserialize( get_db("user_config", "v", ["k" => "apply","uid"=>UID]));
    return ($GLOBALS['global_config']['apply'] == 1 && $apply_user['apply'] > 0);
}
//是否启用留言
function is_guestbook(){
    $guestbook_user = unserialize( get_db("user_config", "v", ["k" => "guestbook","uid"=>UID]) );
    return ($GLOBALS['global_config']['guestbook'] == 1 && $guestbook_user['allow'] == 1);
}

//取URL域名
function get_url_host($url, $get_scheme = false, $get_port = false){
    $urls = parse_url($url);
    $host = $urls['host']; //获取主机名
    $port = $get_port === true ? ( empty( $urls['port'] ) ? '' : ':'.$urls['port']) : '';
    $scheme = $get_port === true ? ( empty( $urls['scheme'] ) ? 'http://' : $urls['scheme'].'://') : ''; //获取请求协议
    return $scheme.$host.$port;
}
//获取图标URL
function geticourl($icon,$link){
    if( !empty( $link['icon']) ){
        if(substr($link['icon'], 0,4) == '<svg'){
            return('data:image/svg+xml;base64,'.base64_encode($link['icon']));
        }else{
            return($link['icon']);
        }
    }
    if ($site['link_icon'] == 'default'){
        return($GLOBALS['libs'].'/Other/default.ico');
    }elseif ($icon == 20){
        return('./index.php?c=icon&url='.base64_encode($link['real_url']));
    }elseif ($icon == 21){
        return('./ico/'.base64_encode($link['real_url']));
    }elseif($icon == 2){ 
        return('https://favicon.png.pub/v1/'.base64_encode(get_url_host($link['real_url'],true,true)));
    }elseif($icon == 6){
        return('https://t3.gstatic.cn/faviconV2?client=SOCIAL&type=FAVICON&fallback_opts=TYPE,SIZE,URL&size=128&url='.$link['real_url']);
    }else{
        return('./system/ico.php?text='.mb_strtoupper(mb_substr($link['title'], 0, 1)));
    }
}

//取分类图标
function get_category_icon($content = ''){
    return empty($content) ? '' : ( substr($content, 0,4) == '<svg' ? 'data:image/svg+xml;base64,'.base64_encode($content) : $content);
}

//获取公开分类(返回数组cid)
function get_open_category(){
    $where['uid'] = UID;
    $where['fid'] = 0;
    $where['status'] = 1;
    $where['property'] = 0;
    $categorys = select_db('user_categorys','cid',$where);
    $where['fid'] = $categorys;
    $categorys = array_merge ($categorys,select_db('user_categorys','cid',$where));
    return $categorys;
}

//获取文章列表
function get_article_list($category = 0,$limit = 0){
    if($GLOBALS['global_config']['article'] < 1){
        return ['data'=>[],'count'=>0];
    }
    $where['uid'] = UID; 
    if(!is_login()){
        $where['AND']['state'] = 1; //状态筛选
    }else{
        $where['AND']['OR']['state'] = [1,2]; //状态筛选
    }
    //分类筛选
    if($category > 0){
        $where['AND']['category'] = $category;
    }
    //统计条数
    $count = count_db('user_article_list',$where);
    //获取条数
    if($limit > 0){
        $where['LIMIT'] = [0,$limit];
    }
    //获取文章列表
    $datas = select_db('user_article_list','*',$where);
    $host = get_HOST();
    $uuid = get_UUID();
    //查询分类
    $categorys = select_db('user_categorys',['cid(id)','name'],['uid'=>UID]);
    $categorys = array_column($categorys,'name','id');
    //为文章添加分类名称
    foreach ($datas as &$data) {
        $data['category_name'] = $categorys[$data['category']] ?? 'Null';
        $data['title'] = htmlspecialchars($data['title'],ENT_QUOTES);
        $data['summary'] = htmlspecialchars($data['summary'],ENT_QUOTES);
        if(static_link){
            $data['url'] = "{$host}/article-{$uuid}-{$data['id']}.html";
        }else{
            $data['url'] = "./index.php?c=article&id={$data['id']}&u={$u}";
        }
    }
    return ['data'=>$datas,'count'=>$count];
}

//根据文章id获取内容
function get_article_content($id){
    if($GLOBALS['global_config']['article'] < 1){
        return ['data'=>[],'count'=>0];
    }
    $where['uid'] = UID;
    if(!is_login()){
        $where['state'] = 1; //状态筛选
    }
    $where['id'] = $id;
    $data = get_db('user_article_list','*',$where);
    $data['title'] = htmlspecialchars($data['title'],ENT_QUOTES);
    $data['summary'] = htmlspecialchars($data['summary'],ENT_QUOTES);
    $data['category_name'] = get_db('user_categorys','name',['uid'=>UID,'cid'=>$data['category']]);
    return $data;
}

//获取分类列表
function get_category_list($layer = false){
    //查询条件
    $where = [];
    $where['uid'] = UID; 
    $where['fid'] = 0;
    $where['status'] = 1;
    $where['ORDER'] = ['weight'=>'ASC'];
    if(!is_login()){
        $where['property'] = 0;
    }
    //查找一级分类
    $content = ['cid(id)','name','property','font_icon','icon','description'];
    $category_parent = select_db('user_categorys',$content,$where);
    //查找二级分类
    $categorys = [];
    if($layer === true){
        foreach ($category_parent as $key => $category) {
            $where['fid'] = $category['id'];
            $category_subitem = select_db('user_categorys',$content,$where);
            $category['subitem_count'] = count($category_subitem);
            $category['subitem'] = $category_subitem;
            array_push($categorys,$category);
        }
    }else{
        foreach ($category_parent as $key => $category) {
            $where['fid'] = $category['id'];
            $category_subitem = select_db('user_categorys',$content,$where);
            $category['subitem_count'] = count($category_subitem);
            array_push($categorys,$category);
            $categorys = array_merge ($categorys,$category_subitem);
        }
    }
    return $categorys;
}

function get_links2($fid,$limit = 0) {
    global $site,$u;
    $fid_s = select_db('user_categorys',['cid','fid','pid'],['uid'=>UID,'status'=>1]);
    $fid_s = array_column($fid_s,null,'cid');
    $where['uid'] = UID;
    $where['fid'] = intval($fid);
    $where['status'] = 1;
    $where['ORDER']['weight'] = 'ASC';
    $where['ORDER']['lid'] = 'ASC';
    if($limit > 0){
        $where['LIMIT'] = [0,$limit];
    }
    if(!is_login){
        $where['property'] = 0;
    }
    
    $links = select_db('user_links',['lid(id)','fid','property','title','url(real_url)','url_standby','description','icon','click','pid','extend'],$where);
    $UUID = ($GLOBALS['global_config']['static_link'] == 2 ? UID : U);
    foreach ($links as &$link) {
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
            $link['url'] = static_link ? "{$GLOBALS['HOST']}/click-{$UUID}-{$link['id']}.html" : "./index.php?c=click&id={$link['id']}&u={$u}";
            if($lock){
                $link['real_url'] = $link['url'];
            }
        }else{
            $link['url'] = $link['real_url'];
        }

        //获取图标链接
        $link['ico'] = $lock ? $GLOBALS['libs'].'/Other/lock.svg' : geticourl($site['link_icon'],$link);
    }
    return $links;
}

function admin_inlet() {
    global $USER_DB;
    $v = unserialize($USER_DB['LoginConfig'])['admin_inlet'];
    if($v == 'display'){
        return true;
    }elseif($v == 'hide'){
        return false;
    }elseif($v == 'condition1'){
        return is_login();
    }else{
        return true;
    }
}