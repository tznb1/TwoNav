<?php if(!defined('DIR')){header('HTTP/1.1 404 Not Found');header("status: 404 Not Found");exit;}
$sql =<<<EOF
CREATE TABLE IF NOT EXISTS "user_config2" (
  "id" INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
  "uid" integer(10) NOT NULL,
  "k" text(32) NOT NULL DEFAULT "",
  "v" text NOT NULL DEFAULT "",
  "t" text(32) NOT NULL DEFAULT "",
  "d" TEXT(32) NOT NULL DEFAULT "",
  CONSTRAINT "id" UNIQUE ("id" ASC)
);
EOF;
if(exe_sql($sql)){
    insert_db('updatadb_logs',['file_name'=>$file_name,'update_time'=>time(),'status'=>'TRUE','extra'=>'']);
}else{
    msg(-1,'数据库更新失败');
}
