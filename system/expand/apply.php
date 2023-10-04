<?php 

//get请求载入页面
if($_SERVER['REQUEST_METHOD'] === 'GET'){
    require DIR."/system/templates.php";
    require($index_path);
    exit;
}

msg(-1,'免费版不支持此功能');
?>
