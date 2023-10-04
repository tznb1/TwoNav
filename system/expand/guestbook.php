<?php 

//POST提交留言
if($_SERVER['REQUEST_METHOD'] === 'POST'){
     msg(-1,'免费版不支持此功能');
 } 

//通用数据初始化
require DIR."/system/templates.php";
require $index_path;
exit;