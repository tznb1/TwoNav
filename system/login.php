<?php if(!defined('DIR')){header('HTTP/1.1 404 Not Found');header("status: 404 Not Found");exit;}

//如果是Get请求则载入登录模板
if($_SERVER['REQUEST_METHOD'] === 'GET'){
    require DIR ."/system/templates.php";
    $t_path = DIR ."/templates/login/{$s_templates['login']}/index.php"; //模板路径
    $copyright = empty($global_config['copyright'])?'<a target="_blank" href="https://gitee.com/tznb/TwoNav">Copyright © TwoNav</a>':$global_config['copyright'];
    $ICP = empty($global_config['ICP'])?'':'<a target="_blank" href="https://beian.miit.gov.cn">'.$global_config['ICP'].'</a>';
    //检查是否存在,不存在则使用默认
    if(!is_file($t_path)){
        $t_path = DIR.'/templates/login/default/index.php';
    }
    require $t_path;
    exit;
}

AccessControl(); //访问控制
$User = $_POST["User"];$Password = $_POST["Password"]; //获取请求数据

if(empty($User)){
    insert_db("user_log", ["uid" => '',"user"=>'',"ip"=>Get_IP(),"time"=>time(),"type" => 'login',"content"=>Get_Request_Content(),"description"=>"请求登录>账号为空"]);
    msg(-1,'账号不能为空!');
}elseif($User != $USER_DB['User']){ 
    insert_db("user_log", ["uid" => '',"user"=>$User,"ip"=>Get_IP(),"time"=>time(),"type" => 'login',"content"=>Get_Request_Content(),"description"=>"请求登录>账号不存在"]);
    msg(-1,'账号不存在!');
}

//记录请求日志
insert_db("user_log", ["uid" => $USER_DB['ID'],"user"=>$USER_DB['User'],"ip"=>Get_IP(),"time"=>time(),"type" => 'login',"content"=>Get_Request_Content(),"description"=>"请求登录"]);
$log_id = $db->id();
//基础判断
if(strlen($Password)!==32){
    update_db("user_log", ["description" => "请求登录>密码错误(长度应该是32位的MD5)"], ["id"=>$log_id]);
    msg(-1,'密码错误!');
}elseif($c != $global_config["Login"] && $c != $USER_DB['Login'] ){
    update_db("user_log", ["description" => "请求登录>登录入口错误"], ["id"=>$log_id]);
    msg(-1,"登录入口错误");
}elseif(strlen($_SERVER['HTTP_USER_AGENT'])>1024){
    update_db("user_log", ["description" => "请求登录>浏览器UA长度异常"], ["id"=>$log_id]);
    msg(-1,"浏览器UA长度异常,请更换浏览器!");
}

$LoginConfig = unserialize( $USER_DB['LoginConfig'] );
//开启双重验证时验证OTP验证码
if(!empty($LoginConfig['totp_key'])){
    if(empty($_POST['otp_code'])){
        msgA(['code'=>-1,'msg'=>'您已开启双重验证,请输入OTP验证码']);
    }
    require DIR . '/system/Authenticator.php';
    $totp = new PHPGangsta_GoogleAuthenticator();
    $checkResult = $totp->verifyCode($LoginConfig['totp_key'], $_POST['otp_code'], 2);
    if(!$checkResult){
        msgA(['code'=>-1,'msg'=>'OTP验证码错误,请重试!']);
    }
}

//计算请求密码和数据库的对比
if(Get_MD5_Password($Password,$USER_DB["RegTime"]) === $USER_DB["Password"]){
    update_db("user_log", ["description" => "请求登录>登录成功"], ["id"=>$log_id]);
    Set_key($USER_DB);
    
    if(empty($LoginConfig['login_page']) || $LoginConfig['login_page'] == 'admin'){
        $url = "./?c=admin&u={$USER_DB['User']}";
    }elseif($LoginConfig['login_page'] == 'index'){
        $url = "./?c=index&u={$USER_DB['User']}";
    }else{
        $url = preg_match('/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i',$_SERVER['HTTP_USER_AGENT']) ? "./?c=index&u={$USER_DB['User']}" : "./?c=admin&u={$USER_DB['User']}";
    }
    //默认页面
    if(!empty($global_config['default_page'])){
        setcookie('Default_User', $User, strtotime("+360 day"),"/",'',false,false);
    }
    msgA(['code'=>1,'msg'=>'登录成功','url'=>$url]);
}else{
    update_db("user_log", ["description" => "请求登录>账户或密码错误"], ["id"=>$log_id]);
    msg(-1,"账户或密码错误");
}
