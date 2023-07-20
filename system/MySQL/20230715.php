<?php if(!defined('DIR')){header('HTTP/1.1 404 Not Found');header("status: 404 Not Found");exit;}
$sql ="
ALTER TABLE `user_links` ADD `keywords` TEXT NOT NULL DEFAULT '' COMMENT '关键字' AFTER `weight` ;
";
if(exe_sql($sql)){
    insert_db('updatadb_logs',['file_name'=>$file_name,'update_time'=>time(),'status'=>'TRUE','extra'=>'']);
}else{
    msg(-1,'数据库更新失败');
}
