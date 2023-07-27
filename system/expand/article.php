<?php if(!defined('DIR')){Not_Found();}AccessControl();
$id = intval($_GET['id']);
//IP数统计
count_ip();
//如果id为空,则显示404
if(empty($id)){Not_Found();}

//查询文章
$where['uid'] = UID;
$where['state'] = 1; //1表示公开
$where['id'] = $id;
$data = get_db('user_article_list','*',$where);

//查找失败时显示404
if(empty($data)){Not_Found();}

//var_dump($data);
//exit;
//站点设置和站点图标
$site = unserialize(get_db('user_config','v',['uid'=>UID,'k'=>'s_site']));
$site['Title']  =  $site['title'].(empty($site['subtitle'])?'':' - '.$site['subtitle']);
//免费用户请保留版权,谢谢!
$copyright = empty($global_config['copyright'])?'<a target="_blank" href="https://gitee.com/tznb/TwoNav">Copyright © TwoNav</a>':$global_config['copyright'];
$ICP = empty($global_config['ICP'])?'':'<a target="_blank" href="https://beian.miit.gov.cn">'.$global_config['ICP'].'</a>';
$favicon = ( !empty($site['site_icon_file'])) ? $site['site_icon'] : './favicon.ico';


//取模板信息
require DIR ."/system/templates.php";
$dir_path = DIR.'/templates/article/'.$s_templates['article'];
$theme_dir = str_replace(DIR.'/templates/article',"./templates/article",$dir_path);
$path = $dir_path.'/index.php';
//检查是否存在,不存在则使用默认
if(!is_file($path)){
    $path= DIR.'/templates/article/default/index.php';
}

//统计点击数
update_db("user_article_list", ["browse_count[+]"=>1],['uid'=>UID,'id'=>$id]);

//读取用户主题配置
$theme_config_db = unserialize(get_db('user_config','v',['t'=>'theme_article','k'=>$s_templates['article'],'uid'=>UID]));

//读取默认主题配置
$theme_info = json_decode(@file_get_contents($dir_path.'/info.json'),true);
$theme_config = empty($theme_info['config']) ? []:$theme_info['config'];
$theme_ver = !Debug?$theme_info['version']:$theme_info['version'].'.'.time();

//合并配置数据
$theme_config = empty($theme_config_db) ? $theme_config : array_merge ($theme_config??[],$theme_config_db??[]);

require $path;
exit;

//返回404
function Not_Found() {
    header('HTTP/1.1 404 Not Found');header("status: 404 Not Found");exit;
}