<?php

//读取配置
$config = unserialize( get_db("global_config", "v", ["k" => "icon_config"])) ?? [];
$config['analysis_timeout'] = (intval($config['analysis_timeout']) >= 3 && intval($config['analysis_timeout']) <= 20) ? intval($config['analysis_timeout']) : 6; //解析超时
$config['download_timeout'] = (intval($config['download_timeout']) >= 3 && intval($config['download_timeout']) <= 20) ? intval($config['download_timeout']) : 6; //下载超时
$config['icon_size'] = (intval($config['icon_size']) >= 5 && intval($config['icon_size']) <= 1024) ? intval($config['icon_size']) : 256; //大小限制
$favicon_url = '';
//防盗链
if($config['referer_test'] == 1){
    if(empty($_SERVER['HTTP_REFERER']) || !strstr($_SERVER['HTTP_REFERER'],$_SERVER['HTTP_HOST'])){
        header('HTTP/1.1 404 Not Found');header("status: 404 Not Found");exit('404 Not Found');
    }
}

//获取URL
$url = base64_decode($_GET['url']);
$url_md5 = md5($url);

//维护模式/离线模式/关闭服务 > 输出固定图标
if($global_config['Maintenance'] != 0 || $global_config['offline'] == '1' || $config['o_switch'] == '0' || !is_subscribe('bool')){
    echo_link_type_icon();
}

//如果不是http(s)则根据类型输出固定图标
if(!preg_match("/^(http:\/\/|https:\/\/)/",$url)){
    echo_link_type_icon();
}else{
    $uri_part = parse_url($url);
    $url_root = $uri_part['scheme'] . '://' . $uri_part['host'] . (isset($uri_part['port']) ? ':' . $uri_part['port'] : '');
}

//检查目录 > 不存在则自动创建 > 创建失败显示错误图标
if(!Check_Path(DIR.'/data/icon')){
    echo_icon(DIR . '/templates/admin/img/error.svg',$config);
}

//读取缓存 > 存在且可用则输出
$cache_data = get_db('global_icon','*',['url_md5'=>$url_md5]);
if(!empty($cache_data) && $cache_data['update_time'] > time() - intval($config['server_cache_time']) && is_file(DIR . '/data/icon/' . $cache_data['file_name'])){
    echo_icon(DIR . '/data/icon/' . $cache_data['file_name'],$config,$cache_data);
}

//缓存不可用
//获取URL的html内容
$html = get_html($url,$config['analysis_timeout']);

//获取html失败
if(empty($html)){
    backup_api($url,$config); //调用备选接口
}

//html获取成功>尝试解析
try {
    $doc = new DOMDocument();
    @$doc->loadHTML($html);
    $links = $doc->getElementsByTagName('link');
    //后续可以考虑将所有声明的图标加入数组,然后按特定规则排序,实现多图标时获取较大尺寸的图标
    foreach ($links as $link) {
        if (in_array($link->getAttribute('rel'),['shortcut icon','icon','alternate icon','apple-touch-icon'])) {
            $favicon_url = $link->getAttribute('href');
            break;
        }
    }
}catch (Exception $e) {
    //解析异常,不做处理!下面继续尝试其他方法获取!
}

//解析失败(可能是未设置图标)
if(empty($favicon_url)){
    //尝试获取根目录的favicon.ico
    $res = down_ico($url_root.'/favicon.ico','./data/icon/',$url,$config['download_timeout']);
    if($res){
        echo_icon(DIR . '/data/icon/'.$url_md5.".ico",$config);
    }
    //调用备选接口
    backup_api($url,$config);
}

//解析到图标
$favicon_url = url_patch($favicon_url,$url);

//if 如果图标类型是base64或者svg则不需要下载

//匹配图标类型>下载>输出
$suffix = strtolower(end(explode('.',$favicon_url)));
$suffix = strtolower(reset(explode('?',$suffix)));
$suffix = preg_match('/^(jpg|jpeg|png|ico|bmp|svg|webp)$/i',$suffix) ? $suffix : 'ico';

//下载图标 > 成功则输出
$res = down_ico($favicon_url,'./data/icon/',$url,$config['download_timeout']);
if($res){
    echo_icon(DIR . '/data/icon/'.$url_md5.".$suffix",$config);
}else{
    echo_link_type_icon();
}

//使用备用接口
function backup_api($url,$config){
    global $uri_part,$url_root;
    //未设置时直接输出ie图标
    $backup_api = intval($config['backup_api']);
    if($backup_api == 0){
        echo_icon(DIR . '/templates/admin/img/ie.svg',$config);
    }elseif($backup_api == 6){
        $res = down_ico('https://api.iowen.cn/favicon/'.parse_url($url)['host'].'.png','./data/icon/','',$config['download_timeout']);
        if($res){
            echo_icon(DIR . '/data/icon/'.$GLOBALS['url_md5'].".png",$config);
        }
    }elseif($backup_api == 2){
        $res = down_ico('https://favicon.png.pub/v1/'.base64_encode($url_root),'./data/icon/','',$config['download_timeout']);
        if($res){
            echo_icon(DIR . '/data/icon/'.$GLOBALS['url_md5'].".png",$config);
        }
    }
    
    //如果都失败,则输出默认图标
    echo_icon(DIR . '/templates/admin/img/ie.svg',$config);
}
//检测URL自动补全
function url_patch($favicon_url,$url){
    global $uri_part,$url_root;
    //包含协议表示URL完整,直接返回
    if(strpos($favicon_url, '://')){
        return $favicon_url;
    }
    
    //忽略协议的绝对路径
    if(strpos($favicon_url, '//') === 0 ) {
        return $uri_part['scheme'] . ':' . $favicon_url;
    }
    
    //位于根目录
    if(strpos($favicon_url, '/') === 0 ){
        return $url_root.$favicon_url;
    }
    //当前目录
    if(strpos($favicon_url, './') === 0){
        return $url_root . $uri_part['path'] . substr($favicon_url, 2);
    }
    //向上N级目录
    if(strpos($favicon_url, '../') === 0){
        $N = substr_count($favicon_url,'../');
        $url_temp = $uri_part['path'];
        for ($i = 0; $i < $N; $i++) {
             $url_temp = dirname($url_temp);
             $favicon_url = preg_replace('/^\.\.\//', '', $favicon_url);
        }
        return $url_root . $url_temp . $favicon_url;
    }
    
    //base64
    
    //SVG
    
    //默认路径
    return $url_root . $uri_part['path'] . $favicon_url;
}

//获取html
function get_html($url,$TIMEOUT = 5){
    try {
        $c = curl_init(); 
        curl_setopt($c, CURLOPT_URL, $url); 
        curl_setopt($c, CURLOPT_RETURNTRANSFER, 1); 
        curl_setopt($c, CURLOPT_FAILONERROR, 1);
        curl_setopt($c, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($c, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($c, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($c, CURLOPT_TIMEOUT, $TIMEOUT);
        curl_setopt($c, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/95.0.4638.69 Safari/537.36');
        $data = curl_exec($c); 
        //如果是gzip则解压
        $prefix  = dechex(ord($data[0])) . dechex(ord($data[1]));
        if(strtolower($prefix) == '1f8b'){
            $data = gzdecode($data);
        }
        curl_close($c);
        return $data;
    }catch (Exception $e) {
        return false;
    }
}

function down_ico($ico_url,  $savePath = './data/temp/',$referer = '',$TIMEOUT = 60){
    $suffix = strtolower(end(explode('.',$ico_url)));
    $suffix = strtolower(reset(explode('?',$suffix))); //截取?前面的
    if(!preg_match('/^(jpg|jpeg|png|ico|bmp|svg|webp)$/i',$suffix)){
        $suffix = 'ico'; //没匹配到后缀名则默认为ico
    }
    $file = "{$GLOBALS['url_md5']}.{$suffix}";
    $c = curl_init();
    curl_setopt($c, CURLOPT_URL, $ico_url);
    curl_setopt($c, CURLOPT_TIMEOUT, $TIMEOUT);
    curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($c, CURLOPT_HEADER, FALSE);
    curl_setopt($c, CURLOPT_NOBODY, FALSE);
    curl_setopt($c, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($c, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($c, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($c, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/95.0.4638.69 Safari/537.36');
    if(!empty($referer)){
        curl_setopt($c, CURLOPT_REFERER, $referer);
    }
    try{
        $res = curl_exec($c);
    }finally{
        $code = curl_getinfo($c, CURLINFO_HTTP_CODE);
        curl_close($c);
    }
    
    if ($code == '200') { //状态码正常
        //十六进制取文件头
        $prefix  = strtolower( dechex(ord($res[0])) . dechex(ord($res[1])) );
        //根据头判断类型
        if($prefix == '1f8b'){ //gzip解码
            $res = gzdecode($res);
        }elseif( $prefix != '3c73' && strpos($prefix, '3c') === 0){ // <开头视为文本 <?开头是svg除外 
            return false;
        }
        //3c73>svg 3c21>html 1f8b>gzip
        
        //文件大小限制
        if((strlen($res) / 1024)> $GLOBALS['config']['icon_size']){
            return false;
        }
        $fullName = rtrim($savePath, '/') . '/' . $file;
        $type = ['jpg'=>'jpeg','jpeg'=>'jpeg','svg'=>'svg+xml','ico'=>'x-icon']; //类型表
        $mime = $type[$suffix] ?? 'x-icon';
        
        //黑名单(后期考虑使用在线名单缓存到本地,以便以更好的维护)
        $_md5 = md5($res);
        if($_md5 == 'c531ffbdad1ba93bd84f2398052958dc') return false; //阿里云
        if($_md5 == '05231fb6b69aff47c3f35efe09c11ba0') return false; //一为默认
        if($_md5 == '3ca64f83fdcf25135d87e08af65e68c9') return false; //小z默认
        
        $data = ['update_time'=>time(),'file_name'=>$file,'file_mime'=>$mime,'ico_url'=>$ico_url,'extend'=>''];
        if(!has_db('global_icon',['url_md5'=>$GLOBALS['url_md5']])){
            $data['url_md5'] = $GLOBALS['url_md5'];
            $data['url'] = $GLOBALS['url'];
            $data['add_time'] = time();
            insert_db('global_icon',$data); 
        }else{
            update_db('global_icon',$data,['url_md5'=>$GLOBALS['url_md5']]); 
        }
        
        return file_put_contents($fullName, $res);
    }else{
        return false;
    }
}

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
