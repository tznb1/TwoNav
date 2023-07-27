<?php if(!defined('DIR')){header('HTTP/1.1 404 Not Found');header("status: 404 Not Found");exit;}
$sql =<<<EOF
CREATE TABLE IF NOT EXISTS "user_article_categorys" (
  "id" INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
  "uid" integer(10) NOT NULL,
  "name" text NOT NULL DEFAULT "",
  "weight" integer NOT NULL,
  "add_time" integer(10) NOT NULL,
  CONSTRAINT "id" UNIQUE ("id" ASC)
);

CREATE TABLE "user_article_list" (
  "id" integer PRIMARY KEY AUTOINCREMENT,
  "uid" integer(10) NOT NULL,
  "title" TEXT NOT NULL DEFAULT "",
  "category" integer NOT NULL,
  "state" integer(1) DEFAULT 0,
  "password" TEXT NOT NULL DEFAULT "",
  "top" integer(10),
  "add_time" integer(10),
  "up_time" integer(10),
  "browse_count" integer DEFAULT 0,
  "summary" TEXT,
  "content" TEXT,
  "cover" TEXT,
  "extend" TEXT,
  CONSTRAINT "id" UNIQUE ("id" ASC)
);

ALTER TABLE user_count ADD e TEXT NOT NULL DEFAULT "";

INSERT INTO `purview_list` (`code`, `name`, `description`) VALUES
('article', '文章管理', '允许使用文章管理功能'),
('article_image', '文章图片', '允许在文章编辑器上传图片');
EOF;
if(exe_sql($sql)){
    insert_db('updatadb_logs',['file_name'=>$file_name,'update_time'=>time(),'status'=>'TRUE','extra'=>'']);
}else{
    msg(-1,'数据库更新失败');
}