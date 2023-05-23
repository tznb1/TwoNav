<?php if(!defined('DIR')){header('HTTP/1.1 404 Not Found');header("status: 404 Not Found");exit;}
$sql ="
CREATE TABLE IF NOT EXISTS `global_icon` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `url_md5` varchar(32) NOT NULL COMMENT 'url_md5',
  `url` text NOT NULL COMMENT 'url',
  `ico_url` text NOT NULL COMMENT 'url_ico',
  `add_time` int(10) UNSIGNED NOT NULL COMMENT '创建时间',
  `update_time` int(10) UNSIGNED NOT NULL COMMENT '更新时间',
  `file_name` text NOT NULL COMMENT '文件名',
  `file_mime` text NOT NULL COMMENT 'MIME类型',
  `extend` text NOT NULL COMMENT '预留扩展',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4;

INSERT INTO `purview_list` (`code`, `name`, `description`) VALUES
('icon_pull', '图标拉取', '允许用户拉取链接图标');
";
if(exe_sql($sql)){
    insert_db('updatadb_logs',['file_name'=>$file_name,'update_time'=>time(),'status'=>'TRUE','extra'=>'']);
}else{
    msg(-1,'数据库更新失败');
}
