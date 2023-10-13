<?php
if(!defined('DIR')){header('HTTP/1.1 404 Not Found');header("status: 404 Not Found");exit;}

//初始化
session_name('TwoNav_initial');
session_start();
$layui['js']  = './static/Layui/v2.8.17/layui.js';
$layui['css'] = './static/Layui/v2.8.17/css/layui.css';

//判断请求类型
if($_SERVER['REQUEST_METHOD'] === 'POST'){
    if(empty($_SESSION['initial'])){ msg(-1,'当前环境无法满足程序运行条件!'); }
    define('Debug',TRUE);
    $db = null;
    $USER_DB =null;
    require DIR.'/system/public.php';
    install();
}else{
    clearstatcache();//清除缓存
    check_env();
    $libs = './static'; //使用本地静态库
}


// 环境检查
function check_env() {
    if(!empty($_GET['diagnosis'])){
        diagnosis();
    }elseif(!empty($_GET['phpinfo'])){
        phpinfo();
        exit;
    }
    $ext = get_loaded_extensions(); //获取组件信息
    $php_version = floatval(PHP_VERSION); //获取PHP版本
    
    if( ( $php_version < 7.3 ) || ( $php_version > 8.2 ) ) {
        exit("当前PHP版本{$php_version}不满足要求,支持范围7.3 - 8.2");
    }
    
    //检查是否支持pdo_sqlite
    if ( !array_search('pdo_sqlite',$ext) ) {
        exit("不支持PDO_SQLite组件(即使您使用MySQL数据库,本程序其他功能也需要它,例如导出数据/本地备份/导入数据等)!");
    }
    $ha = '<br /><a href="./?diagnosis=1">一键诊断</a> <a href="./?phpinfo=1">查看phpinfo</a></font>';
    if (!is_dir('./data')) mkdir('./data',0755,true) or exit('<h3><font color=red>创建data目录失败,请检查权限!</font></h3>'.$ha);
    if (!is_dir('./data/temp')) mkdir('./data/temp',0755,true) or exit('<h3><font color=red>创建temp目录失败,请检查权限!</font></h3>'.$ha);
    if (!is_dir('./data/user')) mkdir('./data/user',0755,true) or exit('<h3><font color=red>创建user目录失败,请检查权限!</font></h3>'.$ha);
    $_SESSION['initial'] = TRUE; //标记满足安装条件
}

//安转前诊断(get参数diagnosis不为空时)
function diagnosis() {
        $log='';
        $log .= "服务器时间：" . date("Y-m-d H:i:s") ."<br />"; 
        $log .= "系统信息：" . php_uname('s').','.php_uname('r') ."<br />";
        $log .= "当前版本：" . file_get_contents('./system/version.txt') . "<br />";
        
        //检查PHP版本，需要大于5.6小于8.0
        $php_version = floatval(PHP_VERSION);
        $log .= "PHP版本：{$php_version}<br />";
        $log .= "Web版本：{$_SERVER['SERVER_SOFTWARE']}<br />";
        if( ( $php_version < 7.3 ) || ( $php_version > 8.1 ) ) {
            $log .= "PHP版本：不满足要求,需要7.3 <= PHP <= 8.1 )<br />";
        }
        //获取加载的模块
        $ext = get_loaded_extensions(); 

        $path = './data/test_'.time().'.txt';
        if(file_put_contents($path, '测试文本,可以删除!由一键诊断生成!')){
            if(unlink($path)){
                $log = $log ."data目录：正常<br />";
            }else{
                $log = $log ."data目录：创建文件成功,删除文件失败<br />";
            }
        }else{
            $log = $log ."data目录：异常,请检查权限!<br />";
        }
        if(function_exists("opcache_reset")){
            $log = $log ."opcache: 已安装<br />";
        }
        $log .= "脚本权限:" . get_current_user()."/".substr(sprintf("%o",fileperms("index.php")),-4)."<br />";
        $log .= in_array("pdo_sqlite",$ext) ? "PDO_Sqlite：支持<br />" : "PDO_Sqlite：不支持 (导入db3)<br />";
        $log .= in_array("curl",$ext) ? "curl：支持<br />" : "curl：不支持 (链接识别/在线更新/主题下载/订阅等)<br />";
        $log .= in_array("mbstring",$ext) ? "mbstring：支持<br />" : "mbstring：不支持 (链接识别)<br />";
        $log .= in_array("Phar",$ext) ? "Phar：支持<br />" : "Phar：不支持 (在线更新/主题下载)<br />";
        $log .= in_array("hash",$ext) ? "hash：支持<br />" : "hash：不支持 (书签分享/生成注册码)<br />";
        $log .= in_array("session",$ext) ? "session：支持<br />" : "session：不支持 (影响较大)<br />";
        $log .= "可用模块：".implode("&#12288;",$ext)."<br />";
        exit($log);
}

//执行安装
function install(){
    global $db;
    if(!preg_match("/^[a-zA-Z0-9]+$/",$_POST['User'])) msg(-1,'账号只能是4到13位的数字和字母!');
    // sqlite
    if($_POST['db_type'] === 'sqlite'){
        if(!preg_match("/^data.*\.db3$/",$_POST['db_file'])){
            msg(-1,'SQLite文件名错误,必须以data开头.db3结尾!');
        }
        if(!class_exists('SQLite3')){
			msg(-1,'不支持SQLite3');
		}
        $path = DIR.'/data/'.$_POST['db_file'];
        //强制重装(清空数据库)
        if($_GET['f'] =='yes'){
            try {
                if( !unlink($path)) msg(-1,'删除数据库失败');
            }catch (Exception $e) {
                msg(-1,'删除数据库失败:'.$e->getMessage());
            } 
        }else if(file_exists($path)){
            msg(-2,'检测到您已安装过,如需重装点击是(将清空所有数据)');
        }
        
        //创建数据表(由于Medoo不支持执行多条SQLite语句,故使用php自带的扩展)
        try {
            $sql = file_get_contents(DIR."/system/SQLite/install.sql");
            class MyDB extends SQLite3 {function __construct() {} } 
            $db2 = new MyDB();
            $db2 -> open($path);
            if(!$db2) msg(-1,'打开SQLite3数据库失败:'.$db2->lastErrorMsg());
            $result = $db2->exec($sql);
            if(!$result) msg(-1,'安装失败:'.$db2->lastErrorMsg());
            $db2->close();
        } catch(Exception $e){
            msg(-1,'初始化SQLite3失败:'.$e->getMessage());
        }
        $config = '<?php
//数据库配置
$db_config = array(
    "type" => "sqlite", //类型
    "file" => "'.$_POST['db_file'].'" //文件
);
?>';
        if(!file_put_contents(DIR.'/data/config.php',$config)) msg(-1,'保存配置失败!');
        
        //sqlite初始化代码
        require (DIR.'/system/Medoo.php'); //载入框架
        try {
            $db = new Medoo\Medoo(['type'=>'sqlite','database'=>$path]);
        }catch (Exception $e) {
            msg(-1,'载入数据库失败:'.$e->getMessage()); //无法载入数据库
        }
        
        
        Write_Config();
    }
    
    // mysql
    if($_POST['db_type'] === 'mysql' || $_POST['db_type'] === 'mariadb'){
        if( !isset($_POST['db_host']) || !isset($_POST['db_port']) || !isset($_POST['db_name']) || !isset($_POST['db_user']) || !isset($_POST['db_password']) ){
            msg(-1,'MySQL配置错误,请检查..');
        }
        
        require (DIR.'/system/Medoo.php'); //载入框架
        try {
            $db = new Medoo\Medoo([
                'type' => $_POST['db_type'],
                'host' => $_POST['db_host'],
                'port' => $_POST['db_port'],
                'database' => $_POST['db_name'],
                'username' => $_POST['db_user'],
                'password' => $_POST['db_password'],
                'charset' => 'utf8mb4'
            ]);
            if($_POST['db_type'] === 'mysql'){
                if(version_compare($db->info ()['version'],'5.6.0','<')){
                    msg(-1,'MySQL数据库版本不能低于5.6,当前版本:'.$db->info ()['version']);
                }
            }else{
                if(version_compare($db->info ()['version'],'10.1.0','<')){
                    msg(-1,'MariaDB数据库版本不能低于10.1,当前版本:'.$db->info ()['version']);
                }
            }
        }catch (Exception $e) {
            $E = $e->getMessage();
            if(strstr($E,'[1044]') || strstr($E,'[1049]')){
                msg(-1,'数据库链接失败,请检查库名!');
            }elseif(strstr($E,'[2002]')){
                msg(-1,'数据库链接失败,请检查地址和网络!');
            }elseif(strstr($E,'[1045]')){
                msg(-1,'数据库链接失败,请检查账号密码!');
            }else{
                msg(-1,'数据库链接失败:'.$E);
            }
        }
        //检查是否存在表
        try {
            $re = $db->query("SHOW TABLES LIKE 'global_config'")->fetchAll();
        }catch (Exception $e) {
            msg(-1,'查询数据失败:'.$e->getMessage());
        }

        //强制重装(会清空数据库)
        if(!empty($re) && $_GET['f'] != 'yes'){
            msg(-2,'您已安装过,如需重装点击确定(将清空所有数据)');
        }
        
        $config = '<?php
//数据库配置
$db_config = array(
    "type" => "'.$_POST['db_type'].'", //类型
	"host" => "'.$_POST['db_host'].'", //地址
	"port" => '.$_POST['db_port'].', //端口
	"name" => "'.$_POST['db_name'].'", //库名
	"user" => "'.$_POST['db_user'].'", //账号
	"password" => "'.$_POST['db_password'].'" //密码
);
?>';
        if(!file_put_contents(DIR.'/data/config.php',$config)) msg(-1,'保存配置失败!');
        //创建数据表
        try {
            $sql = file_get_contents(DIR."/system/MySQL/install.sql"); 
            $re = $db->query($sql)->fetchAll();
        }catch (Exception $e) {
            msg(-1,'install.sql执行失败-1:'.$e->getMessage());
        }
        
        //写到配置文件
        Write_Config(); //写初始配置
    }
    
    //不支持的数据库类型
    msg(-1,'请求错误');
}

//写入配置
function Write_Config(){
    global $USER_DB,$db;
        
    //记录建站时间
    insert_db("global_config", ["k" => "build","v" => ['date'=>date("Y-m-d"),'time'=>date("H:i:s"),'int'=>time()],"d" => '建站时间']);
    
    //默认安全配置
    $LoginConfig['Password2'] = '';//二级密码
    $LoginConfig['api_model'] = 'security'; //API模式
    $LoginConfig['KeySecurity'] = '0'; //key安全
    $LoginConfig['KeyClear'] = '7'; //key清理
    $LoginConfig['HttpOnly'] = '1'; //HttpOnly
    $LoginConfig['Session'] = '360'; //登录保持
    $LoginConfig['Login'] = '0'; //登录入口
    
    //写入管理员账户
    $RegTime = time();
    $re = insert_db("global_user", [
            "FID"=>0,
            "User"=>$_POST['User'],
            "Password"=>Get_MD5_Password(md5($_POST['Password']),$RegTime),
            "UserGroup"=>'root',
            "Email"=>$_POST['Email'],
            "Token"=>'',
            "RegIP"=>Get_IP(),
            "RegTime"=>$RegTime,
            "Login"=>Get_Exclusive_Login($_POST['User']),
            "LoginConfig"=>$LoginConfig
            ]);
    $uid = $db->id(); //取管理员账号id
    
    //写默认安全配置
    insert_db("global_config", ["k" => "LoginConfig","v" => $LoginConfig,"d" => '默认安全配置']); 
    
    //默认站点配置
    $s_site['title'] = '我的书签'; //站点标题
    $s_site['subtitle'] = 'TwoNav'; //副标题
    $s_site['logo'] = '我的书签'; //站点logo
    $s_site['keywords'] = 'TwoNav,开源导航,开源书签,简洁导航,云链接,个人导航,个人书签,扩展,多用户,落幕'; //关键字
    $s_site['description'] = 'TwoNav 是一款使用PHP + SQLite3/MySQL 开发的简约导航/书签管理器。'; //描述
    $s_site['link_model'] = '302'; //链接模式
    $s_site['link_icon'] = '0'; //链接图标
    $s_site['custom_header'] = ''; //头部代码
    $s_site['custom_footer'] = ''; //底部代码
    
    //写入默认站点配置
    insert_db("global_config", ["k" => "s_site","v" => $s_site,"d" => '默认站点配置']);
    
    //写入用户站点配置
    insert_db("user_config", ["uid"=>$uid, "k" => "s_site","v" => $s_site,"d" => '站点配置','t'=>'config']);
    
    //默认模板
    $templates['home_pc'] = 'default';
    $templates['home_pad'] = 'default';
    $templates['login'] = 'default';
    $templates['transit'] = 'default';
    //写到用户模板配置
    insert_db("user_config", ["uid"=>$uid, "k" => "s_templates","v" => $templates,"d" => '默认模板','t'=>'config']);
    //写入全局
    insert_db("global_config", ["k" => "s_templates","v" => $templates,"d" => '默认模板']);

    //写站点配置
    $o_config['Default_User'] = $_POST['User'];
    $o_config['default_page'] = 0;
    $o_config['default_UserGroup'] = '';
    $o_config['RegOption'] = 0;
    $o_config['Register'] = 'register';
    $o_config['Login'] = 'login';
    $o_config['Libs'] = './static';
    $o_config['ICP'] = ''; 
    $o_config['XSS_WAF'] = 0;
    $o_config['SQL_WAF'] = 0;
    $o_config['offline'] = 0;
    $o_config['Update_Source'] = 0;
    $o_config['Update_Overtime'] = 3;
    $o_config['Debug'] = 0;
    $o_config['Maintenance'] = 0;
    $o_config['static_link'] = 0;
    $o_config['Privacy'] = 0;
    $o_config['Sub_domain'] = 0;
    $o_config['copyright'] = '';
    $o_config['global_header'] = '';
    $o_config['global_footer'] = '';
    $o_config['api_extend'] = 0;
    $o_config['apply'] = 0;
    $o_config['guestbook'] = 0;
    $o_config['link_extend'] = 0;
    $o_config['article'] = 0;
    $o_config['c_name'] = 0;
    $o_config['c_desc'] = 0;
    $o_config['l_name'] = 0;
    $o_config['l_url'] = 0;
    $o_config['l_key'] = 0;
    $o_config['l_desc'] = 0;
    $o_config['c_code'] = 0;
    
    
    insert_db("global_config", ["k" => "o_config","v" => $o_config,"d" => '网站配置']);  
    
    //读取信息(注册后默认已经登录)
    $USER_DB = get_db("global_user", "*", ["User"=>$_POST['User']]);
    Set_key($USER_DB);
    
    //复制默认分类和链接
    $time = time();
    $categorys = select_db('user_categorys','*',['uid'=>0]);
    foreach ($categorys as $key => $data){
        $categorys[$key]['uid'] = $USER_DB['ID'];
        $categorys[$key]['add_time'] = $time;
        $categorys[$key]['up_time'] = $time;
        unset($categorys[$key]['id']);
    }
    insert_db('user_categorys',$categorys);
    $category_id = intval(max_db('user_categorys','cid',['uid'=>$USER_DB['ID']])) +1;
    insert_db("user_config", ["uid"=>$USER_DB['ID'],"k"=>"category_id","v"=>$category_id,"t"=>"max_id","d"=>'分类ID']);
    
    $inks = select_db('user_links','*',['uid'=>0]);
    foreach ($inks as $key => $data){
        $inks[$key]['uid'] = $USER_DB['ID'];
        $inks[$key]['add_time'] = $time;
        $inks[$key]['up_time'] = $time;
        unset($inks[$key]['id']);
    }
    insert_db('user_links',$inks);
    $link_id = intval(max_db('user_links','lid',['uid'=>$USER_DB['ID']])) +1;
    insert_db("user_config", ["uid"=>$USER_DB['ID'],"k"=>"link_id","v"=>$link_id,"t"=>"max_id","d"=>'链接ID']);

    //初始ID
    insert_db("user_config", ["uid"=>$USER_DB['ID'],"k"=>"pwd_group_id","v"=>1,"t"=>"max_id","d"=>'加密组ID']);
    
    header('Content-Type:application/json; charset=utf-8');
    exit(json_encode(['code'=>1,'User'=>$_POST['User'],'Password'=>$_POST['Password'],'msg'=>'安装成功'  ])); 
}
 


?>
<!DOCTYPE html>
<html lang="zh-cn">
<head>
	<meta charset="utf-8" />
	<title>TwoNav 安装引导</title>
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
	<link rel='stylesheet' href='<?php echo $layui['css']; ?>'>
	<style>
	    body{ background-color:rgba(0, 0, 51, 0.8); }
	    .login-logo h1 { color:#FFFFFF; text-align: center; }
	    .login-logo { max-width: 400px; height: auto; margin-left: auto; margin-right: auto; margin-top:5em; }
	</style>
</head>
<body>
<div class="layui-container">
<div class="layui-row">
<div class="login-logo"><h1>TwoNav 安装引导</h1></div>
<div class="layui-col-lg6 layui-col-md-offset3" style ="margin-top:4em;">
<form class="layui-form layui-form-pane" action="">
  <div class="layui-form-item">
    <label class="layui-form-label">管理员账号</i></label>
    <div class="layui-input-block">
      <input type="text" name="User" required  lay-verify="required" placeholder="请输入账号" autocomplete="off" class="layui-input">
    </div>
  </div>
  <div class="layui-form-item">
    <label class="layui-form-label">管理员密码</label>
    <div class="layui-input-block">
      <input type="text" name="Password" required  lay-verify="required" placeholder="请输入密码" autocomplete="off" class="layui-input">
    </div>
  </div>
  <div class="layui-form-item">
    <label class="layui-form-label">电子邮箱</label>
    <div class="layui-input-block">
      <input type="text" name="Email" required  lay-verify="required|email" placeholder="请输入邮箱"  autocomplete="off" class="layui-input">
    </div>
  </div>

  <div class="layui-form-item">
    <label class="layui-form-label">数据库类型</label>
    <div class="layui-input-block">
      <select id="db_type" name="db_type" lay-filter="db_type" >
        <option value="sqlite" selected="">SQLite ( 推荐 )</option>
        <option value="mysql" >MySQL ≥ 5.6.0 </option>
        <option value="mariadb" >MariaDB ≥ 10.1 </option>
      </select>
    </div>
  </div>
  
<!--SQLite配置-->
  <div class="layui-form-item" id='db_sqlite'>
    <label class="layui-form-label">SQLite文件</label>
    <div class="layui-input-block">
      <input type="text" name="db_file" id="db_file" placeholder="SQLite数据库的文件名" autocomplete="off" class="layui-input">
    </div>
  </div>
<!--SQLite配置-->

<!--MySQL/MariaDB 配置-->
 <div id='db_mysql' style = "display:none;">
  <div class="layui-form-item">
    <label class="layui-form-label">地址</label>
    <div class="layui-input-block">
      <input type="text" name="db_host" value="localhost" placeholder="请输入服务器地址" autocomplete="off" class="layui-input">
    </div>
  </div>
  <div class="layui-form-item">
    <label class="layui-form-label">端口</label>
    <div class="layui-input-block">
      <input type="number" name="db_port" value="3306" placeholder="请输入服务器端口" autocomplete="off" class="layui-input">
    </div>
  </div>
  <div class="layui-form-item">
    <label class="layui-form-label">库名</label>
    <div class="layui-input-block">
      <input type="text" name="db_name" placeholder="请输入数据库库名" autocomplete="off" class="layui-input">
    </div>
  </div>
  <div class="layui-form-item">
    <label class="layui-form-label">账号</label>
    <div class="layui-input-block">
      <input type="text" name="db_user" placeholder="请输入数据库账号" autocomplete="off" class="layui-input">
    </div>
  </div>
  <div class="layui-form-item">
    <label class="layui-form-label">密码</label>
    <div class="layui-input-block">
      <input type="text" name="db_password" placeholder="请输入数据库密码" autocomplete="off" class="layui-input">
    </div>
  </div>
 </div>
<!--MySQL/MariaDB 配置 End-->
 <div class="layui-form-mid layui-word-aux">安装方式：全新安装 &ensp;&ensp;&ensp;&ensp;&ensp;</div>
 <div class="layui-form-mid layui-word-aux">推荐配置：Nginx-1.20 +&ensp;PHP-8.1 </div>
 <button class="layui-btn" lay-submit lay-filter="register" style = "width:100%;">开始安装</button>
</form>

</div>
</div>
</div>
<script src = '<?php echo $libs?>/jquery/jquery-3.6.0.min.js'></script>
<script src = '<?php echo $layui['js']; ?>'></script>
<script>

var file = "data_" + Date.now() + '_' + getRandomString(20) + ".db3" //生成文件名
var db_type = getCookie('db_type'); 
$('#db_file').val(file); //赋值
$('#db_type').val(db_type.length == 0 ?"sqlite":db_type);
set_db_type(db_type);

layui.use(['form'], function(){
    var form = layui.form;
    
    //伪静态检测
    var request = new XMLHttpRequest();
    request.open('GET', './static/Other/login.css?t=' + new Date().getTime(), true);
    request.onload = function() {
      if (request.status >= 200 && request.status < 400) {
        var fileContent = request.responseText;
        if (fileContent.startsWith('<!DOCTYPE html>')) {
            layer.alert(
                "系统检测到您的站点可能配置了不属于TwoNav的伪静态规则<br />通常是因为之前使用过其他程序,例如:OneNav Extend 或 OneNav <br />您需要将它清除,否则会影响到程序的正常使用 ( 如登录页异常 )<br />并在安装完成后在站长工具>生成伪静态>重新配置到站点中"
                ,{title:'环境异常提示',anim: 2,closeBtn: 0,btn: ['刷新页面']},function () {
                    location.reload();
                }
            );
        }
      }
    };
    request.send();
    
    
    //开始安装
    form.on('submit(register)', function(data){
        var d = data.field;
        if(!/^[A-Za-z0-9]{3,13}$/.test(d.User)){
            layer.msg('账号只能是3到13位的数字和字母!', {icon: 5});
            return false;
        }else if(d.Password.length<3){
            layer.msg('密码长度不能小于3个字符!', {icon: 5});
            return false;
        }else if(d.db_type == 'sqlite' && d.db_file.length == 0){ 
            layer.msg('SQLite文件名不能为空!', {icon: 5});
            return false;
        }else if(d.db_type == 'mysql'){
            if(d.db_host.length == 0 || d.db_port.length == 0 || d.db_name.length == 0 || d.db_user.length == 0 || d.db_password.length == 0){
                layer.msg('数据库配置有误,请检查.', {icon: 5});
                return false;
            }
        }
        $.post('./index.php?c=install',d,function(Re,status){
            if(Re.code == 1){
                open_msg(d.User,d.Password);
            }else if(Re.code == -2){ //强制安装
                layer.confirm(Re.msg,{icon: 3, title:'确定继续 ?'}, function(index){
                    $.post('./index.php?c=install&f=yes',d,function(Re,status){
                        if(Re.code == 1){
                            open_msg(d.User,d.Password);
                        }else{
                            layer.msg(Re.msg, {icon: 5,time: 60*1000});
                        }
                    });
                });
            }else{
                layer.msg(Re.msg, {icon: 5,time: 60*1000});
            }
        });
        return false;
    });
    
    //数据库类型选择事件
    form.on('select(db_type)', function(data){
        set_db_type(data.value);
    });
    

});

//数据库类型切换
function set_db_type(v){
    document.cookie="db_type="+v;
    if(v == 'mysql' || v == 'mariadb'){
        $("#db_mysql").show();
        $("#db_sqlite").hide();
    }else if(v == 'sqlite'){
        $("#db_mysql").hide();
        $("#db_sqlite").show();
    }
}
//信息框
function open_msg(u,p){
    layer.closeAll();
    layer.open({ //弹出结果
    type: 1
    ,title: '安装成功'
    ,area: ['230px', '260px']
    ,maxmin: false
    ,shadeClose: false
    ,resize: false
    ,closeBtn: 0
    ,content: '<div style="padding: 15px;">管理员账号: '+u+'<br>管理员密码: '+p+'<br><h3><a href="?c=admin&u='+u+'" style="color: #0000FF;" class="fl">  <br> >>点我进入后台</a></h3><h3><a href="?u='+u+'" style="color: #0000FF;" class="fl">  <br> >>点我进入首页</a></h3>  <h3><a href="https://gitee.com/tznb/TwoNav/wikis/%E5%AE%89%E8%A3%85%E6%95%99%E7%A8%8B/%E5%AE%89%E5%85%A8%E9%85%8D%E7%BD%AE" style="color: #0000FF;" class="fl" target="_blank">  <br> >>安全配置说明</a></h3> </div>'
    });
}

//取随机字符串
function getRandomString(len) {
    len = len || (Math.floor(Math.random() * (40 - 32)) + 32);
    var $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    var maxPos = $chars.length;
    var str = '';
    for (i = 0; i < len; i++) {
        str += $chars.charAt(Math.floor(Math.random() * maxPos));
    }
    return str;
}

//获取Cookie
function getCookie(cname){
	var name = cname + "=";
	var ca = document.cookie.split(';');
	for(var i=0; i<ca.length; i++) {
		var c = ca[i].trim();
		if (c.indexOf(name)==0) { return c.substring(name.length,c.length); }
	}
	return "";
}

</script>
</body>
</html>