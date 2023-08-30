<?php if(!defined('DIR')){Not_Found();}AccessControl();

//判断全局开关和用户权限
if($global_config['article'] < 1 || !check_purview('article',1)) Not_Found();

//IP数统计
count_ip();

//取GET参数中的id
$id = intval($_GET['id']);

//如果id为空,则显示404
if(empty($id)) Not_Found();

//通用数据初始化
require DIR."/system/templates.php";

//读取文章内容
$data = get_article_content($id);

//查找失败时显示404
if(empty($data)) Not_Found();

//统计点击数
update_db("user_article_list", ["browse_count[+]"=>1],['uid'=>UID,'id'=>$id]);

//载入模板
require $index_path;
