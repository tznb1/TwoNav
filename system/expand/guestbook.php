<?php 
if($_SERVER['REQUEST_METHOD'] === 'GET'){
    exit('当前为免费版,不支持此功能');
}
msg(-1,'当前为免费版,不支持此功能');
?>
