<?php

echo_link_type_icon();

function echo_icon($path,$config,$db = false){
    //文件不存在时输出固定图标(理论上执行到这里不会出现文件不存在)
    if(!is_file($path)){
        echo_icon(DIR . '/templates/admin/img/ie.svg',$config);
    }
    //如果存在mime类型则直接读取,否则根据文件类型声明(从缓存读取时才会有mime)
    if(empty($db['mime'])){
        $suffix = strtolower(end(explode('.',$path))); //文件类型
        $type = ['jpg'=>'jpeg','jpeg'=>'jpeg','svg'=>'svg+xml','ico'=>'x-icon']; //类型表
        $mime = $type[$suffix] ?? 'x-icon';
    }else{
        $mime = $db['mime'];
    }
    //MIME类型
    header("Content-Type: image/{$mime};text/html; charset=utf-8"); 
    //缓存时间
    $cache_time = intval($config['browse_cache_time']);
    if($cache_time > 0 ){  
        header ("Last-Modified: " .gmdate("D, d M Y H:i:s", empty($db['mime']) ? filemtime($path):$db['mime'] )." GMT"); //更新时间
        header("Expires: " .gmdate("D, d M Y H:i:s", time() + $cache_time)." GMT");  //过期时间 HTTP1.0
        header("Cache-Control: public, max-age={$cache_time}"); //存活时间 HTTP1.1
    }
    //输出文件
    exit(file_get_contents($path,true));
}


//根据链接类型输出图标
function echo_link_type_icon(){
    global $config;$config['browse_cache_time'] = 60;
    if(preg_match("/^(http:\/\/|https:\/\/)/",$GLOBALS['url'])){
        echo_icon(DIR . '/templates/admin/img/ie.svg',$config);
    }elseif(preg_match("/^(ftp:\/\/|ftps:\/\/|sftp:\/\/)/",$GLOBALS['url'])){
        echo_icon(DIR . '/templates/admin/img/ftp.svg',$config);
    }elseif(preg_match("/^magnet:?/",$GLOBALS['url'])){
        echo_icon(DIR . '/templates/admin/img/magnet.svg',$config);
    }elseif(preg_match("/^(tcp:\/\/|udp:\/\/|rtsp:\/\)/",$GLOBALS['url'])){
        echo_icon(DIR . '/templates/admin/img/tcpudp.svg',$config);
    }elseif(preg_match("/^thunder:\/\//",$GLOBALS['url'])){
        echo_icon(DIR . '/templates/admin/img/xunlei.png',$config);
    }else{
        echo_icon(DIR . '/templates/admin/img/ie.svg',$config);
    }
    exit;
}
