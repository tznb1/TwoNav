<?php if(!defined('DIR')){header('HTTP/1.1 404 Not Found');header("status: 404 Not Found");exit;}

$type = htmlspecialchars(trim($_GET['type']),ENT_QUOTES); 

if (function_exists($type) ) {
    if($GLOBALS['global_config']['article'] < 1 || !check_purview('article',1)){
        msg_tip();
    }
    $type();
}else{
    Amsg(-1,'请求类型错误 >> '.$type);
}

//获取文章列表
function article_list(){
    msg_tip();
}



