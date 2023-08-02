<?php if(!defined('DIR')){header('HTTP/1.1 404 Not Found');header("status: 404 Not Found");exit;}
$sql =<<<EOF

CREATE TABLE IF NOT EXISTS `user_article_list` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `uid` varchar(32) NOT NULL COMMENT '用户id',
  `title` text NOT NULL COMMENT '标题',
  `category` int(10) UNSIGNED NOT NULL COMMENT '分类id',
  `state` int(10) UNSIGNED NOT NULL COMMENT '状态',
  `password` text NOT NULL COMMENT '访问密码',
  `top` int(10) UNSIGNED NOT NULL COMMENT '置顶',
  `add_time` int(10) UNSIGNED NOT NULL COMMENT '创建时间',
  `up_time` int(10) UNSIGNED NOT NULL COMMENT '修改时间',
  `browse_count` int(10) UNSIGNED NOT NULL COMMENT '浏览次数',
  `summary` text NOT NULL COMMENT '摘要',
  `content` text NOT NULL COMMENT '内容',
  `cover` text NOT NULL COMMENT '封面',
  `extend` text NOT NULL COMMENT '扩展',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4;

ALTER TABLE `user_count` ADD `e` TEXT NOT NULL DEFAULT '' COMMENT '扩展';

INSERT INTO `purview_list` (`code`, `name`, `description`) VALUES
('article', '文章管理', '允许使用文章管理功能'),
('article_image', '文章图片', '允许在文章编辑器上传图片');
EOF;
if(exe_sql($sql)){
    insert_db('updatadb_logs',['file_name'=>$file_name,'update_time'=>time(),'status'=>'TRUE','extra'=>'']);
}else{
    msg(-1,'数据库更新失败');
}
