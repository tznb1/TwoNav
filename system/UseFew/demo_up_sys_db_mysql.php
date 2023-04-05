<?php if(!defined('DIR')){header('HTTP/1.1 404 Not Found');header("status: 404 Not Found");exit;}
$sql =<<<EOF
DROP TABLE IF EXISTS `user_config2`;
CREATE TABLE IF NOT EXISTS `user_config2` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `uid` int(10) UNSIGNED NOT NULL COMMENT '用户ID',
  `k` varchar(32) NOT NULL COMMENT '键',
  `v` text NOT NULL COMMENT '值',
  `t` varchar(32) NOT NULL COMMENT '类型',
  `d` varchar(32) DEFAULT '' COMMENT '描述',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
EOF;
if(exe_sql($sql)){
    insert_db('updatadb_logs',['file_name'=>$file_name,'update_time'=>time(),'status'=>'TRUE','extra'=>'']);
}else{
    msg(-1,'数据库更新失败');
}
