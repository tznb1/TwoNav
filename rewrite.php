<?php  //负责接管和处理Nginx伪静态规则

define('URI',$_SERVER['REQUEST_URI']);

//登录/管理/注册页面(不带html)
if (URI === '/login' || URI === '/admin' || URI == '/register') {
    $_GET['c'] = substr(URI, 1);
//管理页面
}elseif (preg_match('/^\/admin-([A-Za-z0-9]+)\.html?$/', URI, $matches)) {
    $_GET['c'] = 'admin';
    $UUID = $matches[1];
//专属登录页面
}elseif (preg_match('/^\/login-([A-Za-z0-9]+)-([A-Za-z0-9]+)\.html?$/', URI, $matches)) {
    $UUID = $matches[1];
    $_GET['c'] = $matches[2];
//收录和留言
}elseif (preg_match('/^\/(apply|guestbook)-([A-Za-z0-9]+)\.html?$/', URI, $matches)) {
    $_GET['c'] = $matches[1];
    $UUID = $matches[2];
//本地图标
}elseif(preg_match('/^\/ico\/(.+)$/', URI, $matches)){
    $_GET['c'] = 'icon';
    $_GET['url'] = $matches[1];
//用户主页
}elseif (preg_match('/^\/([A-Za-z0-9]+)\.html?$/', URI, $matches)) {
    $UUID = $matches[1];
//过渡/文章
}elseif(preg_match('/^\/(click|article)-([A-Za-z0-9]+)-(\d+)\.html?$/', URI, $matches)) {
    $_GET['c'] = $matches[1];
    $UUID = $matches[2];
    $_GET['id'] = $matches[3];
//分类页面
}elseif(preg_match('/^\/category-([A-Za-z0-9]+)-(\d+)\.html?$/', URI, $matches)) {
    $_GET['c'] = 'index';
    $UUID = $matches[1];
    $_GET['oc'] = $matches[2];
//站点地图
}elseif(URI === '/sitemap.xml'){
    $_GET['c'] = 'sitemap';
//匹配失败
}else{
    header("HTTP/1.0 404 Not Found");
    exit("404 Not Found.<br>".URI);
}

include 'index.php';
exit;