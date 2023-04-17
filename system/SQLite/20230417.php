<?php if(!defined('DIR')){header('HTTP/1.1 404 Not Found');header("status: 404 Not Found");exit;}
$sql =<<<EOF
INSERT INTO `purview_list` (`code`, `name`, `description`) VALUES
('link_extend', '链接扩展', '允许使用链接扩展字段'),
('theme_in', '主题设置', '后台显示主题设置菜单'),
('theme_set', '主题配置', '允许自定义主题配置');
EOF;
if(exe_sql($sql)){
    insert_db('updatadb_logs',['file_name'=>$file_name,'update_time'=>time(),'status'=>'TRUE','extra'=>'']);
}else{
    msg(-1,'数据库更新失败');
}
