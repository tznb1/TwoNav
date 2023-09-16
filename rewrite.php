<?php  //负责接管和处理Nginx伪静态规则

define('URI',$_SERVER['REQUEST_URI']);

//登录页和管理员(默认)
if (URI === '/login' || URI === '/admin') {
    $_GET['c'] = substr(URI, 1);
//本地图标
}elseif(preg_match('/^\/ico\/(.+)$/', URI, $matches)){
    $_GET['c'] = 'icon';
    $_GET['url'] = $matches[1];
//用户主页
}elseif (preg_match('/^\/([A-Za-z0-9]+)(\.html)?$/', URI, $matches)) {
    $_GET['u'] = $matches[1];
//过渡/文章
}elseif(preg_match('/^\/([A-Za-z0-9]+)\/(click|article)\/([A-Za-z0-9]+)(\.html)?$/', URI, $matches)) {
    $_GET['u'] = $matches[1];
    $_GET['c'] = $matches[2];
    $_GET['id'] = $matches[3];
//匹配失败
}else{
    header("HTTP/1.0 404 Not Found");
    exit("404 Not Found.");
}

include 'index.php';
exit;