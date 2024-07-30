<?php if(!defined('DIR')){header('HTTP/1.1 404 Not Found');header("status: 404 Not Found");exit;}
$sql ='
CREATE INDEX "category_idx_1"
ON "user_categorys" ("fid","uid","status","property","pid","weight");

CREATE INDEX "link_idx_1"
ON "user_links" ("uid","fid","status","property","pid","add_time","click");
';
//创建索引用于优化效率
if(exe_sql($sql)){
    insert_db('updatadb_logs',['file_name'=>$file_name,'update_time'=>time(),'status'=>'TRUE','extra'=>'']);
}else{
    msg(-1,'数据库更新失败');
}


