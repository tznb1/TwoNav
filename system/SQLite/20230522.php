<?php if(!defined('DIR')){header('HTTP/1.1 404 Not Found');header("status: 404 Not Found");exit;}
$sql =<<<EOF
CREATE TABLE IF NOT EXISTS "global_icon" (
  "id" INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
  "url_md5" text(32) NOT NULL DEFAULT "",
  "url" text NOT NULL DEFAULT "",
  "ico_url" text NOT NULL DEFAULT "",
  "add_time" integer(10) NOT NULL,
  "update_time" integer(10) NOT NULL,
  "file_name" text NOT NULL DEFAULT "",
  "file_mime" text NOT NULL DEFAULT "",
  "extend" text NOT NULL DEFAULT "",
  CONSTRAINT "id" UNIQUE ("id" ASC)
);
INSERT INTO `purview_list` (`code`, `name`, `description`) VALUES
('icon_pull', '图标拉取', '允许用户拉取链接图标');
EOF;
if(exe_sql($sql)){
    insert_db('updatadb_logs',['file_name'=>$file_name,'update_time'=>time(),'status'=>'TRUE','extra'=>'']);
}else{
    msg(-1,'数据库更新失败');
}
