<?php

function get_maxid($v){
    $file = fopen(DIR.'/data/user/'.U.'/lock.'.UID,'w+');
    if(flock($file,LOCK_EX)){
        $id = get_db('user_config','v',['uid'=>UID,"k" =>$v]);
        update_db('user_config',['v[+]'=>1],['uid'=>UID,"k" =>$v]);
        flock($file,LOCK_UN);
        }
    fclose($file);
    return $id;
}

//取最大值
function max_db($table,$column,$where){
    global $db;
    try {
        return $db->max($table,$column,$where);
    }catch (Exception $e) {
        if(Debug){
            msgA(['code'=>-1,'msg'=>'查询数据库失败','Message'=>$e->getMessage(),'debug'=>debug_backtrace()]);
        }else{
            Amsg(-1,'查询数据库失败');
        }
    }
}
//统计条数
function count_db($table,$where){
    global $db;
    try {
        return $db->count($table,$where);
    }catch (Exception $e) {
        if(Debug){
            msgA(['code'=>-1,'msg'=>'查询数据库失败','Message'=>$e->getMessage(),'debug'=>debug_backtrace()]);
        }else{
            Amsg(-1,'查询数据库失败');
        }
    }
}
//查询表
function select_db($table,$columns,$where){
    global $db;
    try {
        $re = $db->select($table,$columns,$where);
        return $re;
    }catch (Exception $e) {
        if(Debug){
            msgA(['code'=>-1,'msg'=>'查询数据库失败','Message'=>$e->getMessage(),'debug'=>debug_backtrace()]);
        }else{
            Amsg(-1,'查询数据库失败');
        }
    }
}
//插入 $rp = [1,'成功'];
function insert_db($table,$values,$rp = []){
    global $db;
    try {
        $re = $db->insert($table,$values);
        if(empty($rp)){
            return $re;
        }else{
            msg($rp[0],$rp[1]);
        }
    }catch (Exception $e) {
        if(Debug){
            msgA(['code'=>-1,'msg'=>'插入数据失败','Message'=>$e->getMessage(),'debug'=>debug_backtrace()]);
        }else{
            Amsg(-1,'插入数据失败');
        }
    }
}
//取最后插入行的id
function get_id_db(){
    global $db;
    try {
        return $db->id();
    }catch (Exception $e) {
        if(Debug){
            msgA(['code'=>-1,'msg'=>'取最后插入行的id失败','Message'=>$e->getMessage(),'debug'=>debug_backtrace()]);
        }else{
            Amsg(-1,'取最后插入行的id失败');
        }
    }
}
//是否存在数据
function has_db($table,$where){
    global $db;
    try {
        return $db->has($table,$where);
    }catch (Exception $e) {
        if(Debug){
            msgA(['code'=>-1,'msg'=>'读取数据库失败','Message'=>$e->getMessage(),'debug'=>debug_backtrace()]);
        }else{
            Amsg(-1,'读取数据库失败');
        }
    }
}


//更新 $rp = [1,'成功'];
function update_db($table,$data,$where,$rp = []){
    global $db;
    try {
        $db->update($table,$data,$where);
        if(empty($rp)){
            return true;
        }else{
            msg($rp[0],$rp[1]);
        }
    }catch (Exception $e) {
        if(Debug){
            msgA(['code'=>-1,'msg'=>'更新数据失败','Message'=>$e->getMessage(),'debug'=>debug_backtrace()]);
        }else{
            Amsg(-1,'更新数据失败');
        }
    }
}
//删除 $rp = [1,'成功'];
function delete_db($table,$where,$rp = []){
    global $db;
    try {
        $db->delete($table,$where);
        if(empty($rp)){
            return true;
        }else{
            msg($rp[0],$rp[1]);
        }
    }catch (Exception $e) {
        if(Debug){
            msgA(['code'=>-1,'msg'=>'删除数据失败','Message'=>$e->getMessage(),'debug'=>debug_backtrace()]);
        }else{
            Amsg(-1,'删除数据失败');
        }
    }
}

//取一行 $rp = [1,'成功'];
function get_db($table,$columns,$where,$rp = []){
    global $db;
    try {
        $re = $db->get($table,$columns,$where);
        if(empty($rp)){
            return $re;
        }else{
            msg($rp[0],$rp[1]);
        }
    }catch (Exception $e) {
        if(Debug){
            msgA(['code'=>-1,'msg'=>'获取数据失败','Message'=>$e->getMessage(),'debug'=>debug_backtrace()]);
        }else{
            Amsg(-1,'获取数据失败');
        }
    }
}

//写全局配置(存在则更新,不存在则创建)
function write_global_config($key,$value,$d){
    if(!has_db('global_config',['k'=>$key])){
        insert_db("global_config", ["k" => $key,"v" => $value,"d" => $d]);  
    }else{
        update_db("global_config", ["v" => $value],['k'=>$key]); 
    }
}

//写用户配置(存在则更新,不存在则创建)
function write_user_config($key,$value,$t,$d){
    if(!has_db('user_config',['uid'=>UID,'k'=>$key,'t'=>$t])){
        insert_db("user_config", ['uid'=>UID,"k"=>$key,"v"=>$value,"t"=>$t,"d"=>$d]);  
    }else{
        update_db("user_config", ["v"=>$value],['uid'=>UID,'k'=>$key,'t'=>$t]); 
    }
}

//写用户统计
function write_user_count($key,$t){
    if(!has_db('user_count',['uid'=>UID,'t'=>$t,'k'=>$key])){
        insert_db("user_count", ['uid'=>UID,"k"=>$key,"v"=>1,'t'=>$t]);  
    }else{
        update_db("user_count", ["v[+]"=>1],['uid'=>UID,'t'=>$t,'k'=>$key]); 
    }
}

//生成专属登录入口(随机不可逆)
function Get_Exclusive_Login($user){
    return (substr(md5($user.'7EwRaBa2'.time().rand(5, 15)),0, 8));
}

//获取MD5密码
function Get_MD5_Password($Password,$RegTime){
    //安装前可修改规则,之后请勿修改,否则账号无法登录
    return ( md5($Password.$RegTime));
}

//取Get参数并过滤
function Get($str){
    return strip_tags(trim(@$_GET[$str]));
}


//xss检测
function check_xss($value){
    if(preg_match('/<(iframe|script|body|img|layer|div|meta|style|base|object|input)|">/i',$value)){
        return true;
    }else{
        return false;
    }
}
//输出分类
function echo_category($property = false){
    $categorys = [];
    $content = ['cid(id)','name'];
    $where['uid'] = UID; 
    $where['fid'] = 0;
    $where['status'] = 1;
    $where['ORDER'] = ['weight'=>'ASC'];
    if($property == false){
        $where['property'] = 0;
    }
    foreach (select_db('user_categorys',$content,$where) as $category) {
        echo "<option value=\"{$category['id']}\">{$category['name']}</option>";
        $where['fid'] = $category['id'];
        foreach (select_db('user_categorys',$content,$where) as $category_subitem) {
            echo "<option value=\"{$category_subitem['id']}\">└{$category_subitem['name']}</option>";
        }
    }
}
//输出加密组
function echo_pwds(){
    $where["uid"] = UID;
    $where['ORDER']['pid'] = 'ASC';
    foreach (select_db('user_pwd_group',['pid','name','password'],$where) as $data) {
        echo "<option value=\"{$data['pid']}\">{$data['name']} | 密码 [{$data['password']}]</option>";
    }
}
//检查链接
function check_link($fid,$title,$url,$url_standby=''){
    global $db;
    $pattern = "/^(http:\/\/|https:\/\/|ftp:\/\/|ftps:\/\/|sftp:\/\/|magnet:?|ed2k:\/\/|thunder:\/\/|tcp:\/\/|udp:\/\/|rtsp:\/\/).+/";
    
    if(empty($fid)) {msg(-1007,'分类id(fid)不能为空！');}
    if(!get_db('user_categorys','cid',['uid'=>UID ,"cid" => $fid])){msg(-1,'分类不存在');}
    if (empty($title)){msg(-1008,'标题不能为空！');}
    if (empty($url)){msg(-1009,'URL不能为空！');}
    if (check_xss($url)){msg(-1010,'URL存在非法字符！');}
    if (check_xss($url_standby)){msg(-1010,'备用URL存在非法字符！');}
    if (!preg_match($pattern,$url)){msg(-1010,'URL无效！');}
    if ( ( !empty($url_standby) ) && ( !preg_match($pattern,$url_standby) ) ) {msg(-1010,'备选URL无效！');}
    return true;
}
//获取版本号
function Get_Version(){
    $path = DIR.'/system/version.txt';
    return file_exists($path) ? @file_get_contents($path):'null';
}
//站长权限验证
function is_root(){
    global $USER_DB;
    if( $USER_DB['UserGroup'] != 'root'){ msg(-1,'您没有权限使用此功能');}else{return true;}
}
//返回Cookie的key
function Set_key($USER_DB){
    $LoginConfig = unserialize($USER_DB['LoginConfig']);
    $session  = $LoginConfig['Session']; //保持时间(单位天)
    $Expire = Get_ExpireTime($session); //计算到期时间戳
    $time = time(); //取当前时间
    $key = Getkey($USER_DB['User'],Get_MD5_Password($USER_DB["Password"],$USER_DB["RegTime"]),$Expire,$LoginConfig['KeySecurity'],$time);
    setcookie($USER_DB['User'].'_key', $key, $session == 0 ? 0 : $Expire,"/",'',false,$LoginConfig['HttpOnly']==1);
    insert_db("user_login_info", [
        "uid" => $USER_DB['ID'],
        "user"=>$USER_DB['User'],
        "ip"=>Get_IP(),
        "ua"=>$_SERVER['HTTP_USER_AGENT'],
        "login_time"=>$time,
        "last_time"=>$time,
        "expire_time"=>$Expire,
        "cookie_key"=>md5($key)]); //不记录用户真实key,同时防止Cookie攻击
    return $key;
}


//生成Cookie登录Key
function Getkey($User,$Password,$Expire,$keyLevel,$Time){
    $str = "<$User|$Password|$Time|$Expire|";
    if($keyLevel == 1){ //1.UA
        $str.= $_SERVER['HTTP_USER_AGENT'];
    }elseif($keyLevel == 2){ //2.UA+IP
        $str.= $_SERVER['HTTP_USER_AGENT'];
        $str.= '|'.Get_IP();
    }
    $str.= '|X6joqPCH>';
    $key = md5($str);
    return $key;
}

//验证二级密码
function Check_Password2($LoginConfig){
    global $USER_DB;
    //如果没设二级密码直接返回认证成功
    if(empty($LoginConfig['Password2'])){return true;}
    //验证二级密码的Cookie是否正确
    return $_COOKIE[U.'_Password2'] === md5($USER_DB['Password'].$_COOKIE[U.'_key'].$LoginConfig['Password2']);
}
//获取到期时间戳,默认30天
function Get_ExpireTime($day =30){
    return empty($day)?0:strtotime("+{$day} day");
}
//验证登录
function is_login(){
    global $USER_DB,$db;
    $time = time();
    $LoginConfig = unserialize($USER_DB['LoginConfig']);
    
    //清理间隔30分钟(1800秒)
    if( ($USER_DB['kct'] + 1800) < $time ){
        $lt = $time - ($LoginConfig['KeyClear'] * 24 * 60 * 60);
        $where =  ["AND" => 
            [
              "uid" => $USER_DB['ID'],
		      "OR" => ["expire_time[<]" => $time,"last_time[<]" => $lt]
	        ]
        ];
        delete_db("user_login_info", $where); //清理到期Key
        update_db("global_user",["kct"=>$time],["User" => $USER_DB['User']]); //记录清理时间
    }
    
    //查询登录信息
    $where = ["cookie_key"=>md5($_COOKIE[U.'_key']),"uid"=>$USER_DB['ID']];
    $info =  get_db("user_login_info", "*", $where);
    
    //没找到返回未登录
    if(empty($info)){return false;}
    

    
    //UA验证
    if($LoginConfig['KeySecurity'] > 0 && $_SERVER['HTTP_USER_AGENT'] != $info['ua']){return false;}
    //IP验证
    if($LoginConfig['KeySecurity'] > 1 && Get_IP() != $info['ip']){return false;}
    
    //到期验证(同时重新计算)
    if( $info['expire_time'] != 0 && ($time > $info['expire_time'] || $time > ($info['login_time'] + ($LoginConfig['Session'] * 24 * 60 * 60) )  )){
        delete_db("user_login_info", $where);
        return false;
    }
    //会话Key验证(没有到期时间时如果距上次访问时间大于24小时认为无效)
    if($info['expire_time'] == 0 &&  ($info['last_time'] + 86400) < $time){
        delete_db("user_login_info", $where);
        return false;
    }//有到期时间,且开启了Key清理
    elseif($LoginConfig['KeyClear'] != 0 && ($info['last_time'] + ($LoginConfig['KeyClear'] * 24 * 60 * 60)) < $time ){
        delete_db("user_login_info", $where);
        return false;
    }
    
    //Key验证
    $OkKey = Getkey(U,Get_MD5_Password($USER_DB["Password"],$USER_DB["RegTime"]),$info['expire_time'],$LoginConfig['KeySecurity'],$info['login_time']);
    if( $OkKey != $_COOKIE[U.'_key']){return false;}
    
    //写访问时间
    update_db("user_login_info",["last_time"=>time()], $where);
    return true;
}

//访问控制
function AccessControl(){
    global $USER_DB,$global_config;
    if( $global_config['Maintenance'] != 0 && $USER_DB['UserGroup'] != 'root'){
        Amsg(-1,'网站正在进行维护,请稍后再试!');
    }
}

//获取请求内容
function Get_Request_Content(){
    $str = '';
    foreach($_POST as $key =>$value){
        $str .= $key .'=' . $value.($value == end($_POST) ? '':'&');
    }
    return $str;
}

//获取首页地址
function Get_Index_URL(){
    $http_type = ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https')) ? 'https://' :'http://';
    $HOST = $http_type.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'];
    return($HOST);
}

//订阅验证
function is_subscribe($type = 'bool'){
    $data = unserialize(get_db('global_config','v',["k" => "s_subscribe"]));
    $data['host'] = $_SERVER['HTTP_HOST']; //当前域名
    if ( empty( $data['order_id']) ){ //单号为空
        $msg = $type == 'text' ? '未授权':'您未订阅,请先订阅在使用';
    }elseif($data['end_time'] < time()){
        $msg = $type == 'text' ? '已过期':'您的订阅已过期';
    }else{
        //判断是否为IP
        if(preg_match("/^(\d+\.\d+\.\d+\.\d+):*\d*$/",$data['host'],$host)) {
            $data['host'] = $host[1]; //取出IP(不含端口)
        }else{
            $host = explode(".", $data['host']);
            $count = count($host);
            if($count != 2){
                $data['host'] = $host[$count-2].'.'.$host[$count-1];
                //如果存在端口则去除
                if(preg_match("/(.+):\d+/",$data['host'],$host)) {
                    $data['host'] = $host[1];
                }
            }
        }
        if(!stristr($data['domain'],$data['host'])){
            $msg = $type == 'text' ? '域名不符':"您的订阅不支持当前域名 >> ".$_SERVER['HTTP_HOST'];
        }
    }

    if($type == 'bool'){
        return empty($msg);
    }elseif($type == 'text'){
        return empty($msg) ? '已授权':$msg;
    }elseif($type == 'msg'){
        if(empty($msg)){
            return true;
        }else{
            msg(-1,$msg);
        }
    }else{
        msg(-1,'调用参数错误');
    }
}

//检查目录是否存在,不存在则创建!失败返回假
function Check_Path($Path){
    if(!is_dir($Path)){
        return mkdir($Path,0755,true);
    }else{
        return true;
    }
}
//获取Base64的文件大小
function GetFileSize($Base64,$unit = 'kb'){
    $Base64 = str_replace('=', '', $Base64);
    $len = strlen($Base64);
    $file_size = $len - ($len/8)*2;
    if($unit == 'b'){
        $file_size = number_format(($file_size),2);
    }elseif($unit == 'kb'){
        $file_size = number_format(($file_size/1024),2);
    }elseif($unit == 'mb'){
        $file_size = number_format(($file_size/1024/1024),2);
    }elseif($unit == 'gb'){
        $file_size = number_format(($file_size/1024/1024/1024),2);
    }else{
        $file_size = number_format(($file_size/1024),2);
    }
    return $file_size;
}
//获取访问IP
function Get_IP() { 
    if (getenv('HTTP_CLIENT_IP')) { 
        $ip = getenv('HTTP_CLIENT_IP'); 
    }elseif(getenv('HTTP_X_FORWARDED_FOR')) { 
        $ip = getenv('HTTP_X_FORWARDED_FOR'); 
    } elseif (getenv('HTTP_X_FORWARDED')) { 
        $ip = getenv('HTTP_X_FORWARDED'); 
    } elseif (getenv('HTTP_FORWARDED_FOR')) { 
        $ip = getenv('HTTP_FORWARDED_FOR'); 
    } elseif (getenv('HTTP_FORWARDED')) { 
        $ip = getenv('HTTP_FORWARDED'); 
    }else{ 
        $ip = $_SERVER['REMOTE_ADDR']; 
    } 
    return $ip; 
}

//获取URL状态码
function get_http_code($url) { 
    $curl = curl_init(); 
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_HEADER, 1);
    curl_setopt($curl, CURLOPT_NOBODY, true);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_TIMEOUT, 10);
    $data = curl_exec($curl);
    $return = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    curl_close($curl);
    return $return;
}

function ccurl($url,$overtime = 3){
    try {
        $curl  =  curl_init ( $url ) ; //初始化
        curl_setopt($curl, CURLOPT_TIMEOUT, $overtime ); //超时
        curl_setopt($curl, CURLOPT_FAILONERROR, true);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        $Res["content"] = curl_exec   ( $curl ) ;
        $Res["code"] = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close  ( $curl ) ;
        
    } catch (\Throwable $th) {
        return false; 
    }
    return $Res;
}

function downFile($url, $file = '', $savePath = './data/temp/'){
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_TIMEOUT, 60); //超时/秒
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); //不直接输出
    curl_setopt($ch, CURLOPT_HEADER, FALSE);  //不需要response header
    curl_setopt($ch, CURLOPT_NOBODY, FALSE);  //需要response body
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1); //允许重定向(适应网盘下载)
    
    try{
        $res = curl_exec($ch);
    }finally{
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
    }
    
    if ($code == '200') { //状态码正常
        
        if(empty($file)){ //如果文件名为空
            $file = date('Ymd_His').'.tmp';
        }
        $fullName = rtrim($savePath, '/') . '/' . $file;
        return file_put_contents($fullName, $res);
    }else{
        return false;
    }
}
//获取目录列表
function get_dir_list($dir){
    $dirArray=[];
    if(false != ($handle = opendir($dir))){
        while(false !== ($file = readdir($handle))) {
            if($file != "." && $file != "..") {
                array_push($dirArray,$file);
            }
        }
        closedir($handle);
    }
    return $dirArray;
}
//删除目录
function deldir($dir) {
    //先删除目录下的文件：
    $dh=opendir($dir);
    while ($file=readdir($dh)) {
        if($file!="." && $file!="..") {
            $fullpath=$dir."/".$file;
            if(!is_dir($fullpath)) {
                unlink($fullpath);
            } else {
                deldir($fullpath);
            }
        }
    }
    closedir($dh);
    return rmdir($dir);
}
//取文本左边
function getSubstrRight($str, $rightStr){
    $right = strpos($str, $rightStr);
    return substr($str, 0, $right);
}
//取文本右边
function getSubstrLeft($str, $leftStr){
    $left = strpos($str, $leftStr);
    return substr($str, $left + strlen($leftStr));
}
//获取首页地址
function getindexurl(){
    $http_type = ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https')) ? 'https://' :'http://';
    $HOST = $http_type.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'];
    return($HOST);
}
//检查数组指定字段是否有重复值
function is_Duplicated($array, $field){
    $values = [];
    foreach($array as $item){
        if(in_array($item[$field], $values)){ 
            return true; 
        }else{
            $values[] = $item[$field];
        }
    }
    return false;
}
//检查权限(有权限返回true 没有权限时根传递参数1是返回false 2是直接返回错误信息)
function check_purview($name,$return_type){
    global $USER_DB;
    if($USER_DB['UserGroup'] == 'root' || $USER_DB['UserGroup'] == 'default'){
        return true;
    }
    $UserGroup = get_db('user_group','*',['code'=>$USER_DB['UserGroup']]);
    $codes = unserialize($UserGroup['codes']);
    if(empty($codes)){$codes = [];}
    if(in_array($name,$codes)){
        return true;
    }elseif($return_type == 2){
        msg(-1,'权限不足');
    }else{
        return false;
    }
    
}
//字节格式化
function byteFormat($bytes) {
    $sizetext = array(" B", " KB", " MB", " GB", " TB", " PB", " EB", " ZB", " YB");
    return round($bytes / pow(1024, ($i = floor(log($bytes, 1024)))), 2) . $sizetext[$i];
}
//取随机字符串
function Get_Rand_Str( $length = 8 ,$extend = false){
    $chars = array('a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 
    'i', 'j', 'k', 'l','m', 'n', 'o', 'p', 'q', 'r', 's', 
    't', 'u', 'v', 'w', 'x', 'y','z', 'A', 'B', 'C', 'D', 
    'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L','M', 'N', 'O', 
    'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y','Z', 
    '0', '1', '2', '3', '4', '5', '6', '7', '8', '9');
    if($extend){
        array_push($chars,[ '!', 
    '@','#', '$', '%', '^', '&', '*', '(', ')', '-', '_', 
    '[', ']', '{', '}', '<', '>', '~', '`', '+', '=', ',', 
    '.', ';', ':', '/', '?', '|']);
    }
    $keys = array_rand($chars, $length); 
    $str = '';
    for($i = 0; $i < $length; $i++){
        $str .= $chars[$keys[$i]];
    }
    return $str;
}
