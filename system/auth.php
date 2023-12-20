<?php if(!defined('DIR')){header('HTTP/1.1 404 Not Found');header("status: 404 Not Found");exit;}
// 鉴权接口: 账号登录

//忽略GET/POST以外的请求
if(!in_array($_SERVER['REQUEST_METHOD'],['GET','POST'])){
    exit;
}
if(!isset($auth_mode)){
    $auth_mode = $_GET['mode'];
}

//账号登录
if($auth_mode == 'uname'){
    $username = $_POST['username'];
    $password = $_POST['password'];
    $log = ["uid" => '',"user"=>$username,"ip"=>Get_IP(),"time"=>time(),"type" => 'login',"content"=>Get_Request_Content(),"description"=>""];
    //密码长度
    if(strlen($password)!==32){
        $log['description'] = '请求登录>密码错误(长度应该是32位的MD5)';
        insert_db("user_log",$log);
        msg(-1,'账号或密码错误');
    }
    //浏览器UA
    if(strlen($_SERVER['HTTP_USER_AGENT']) > 1024){
        $log['description'] = '请求登录>浏览器UA长度>1024';
        insert_db("user_log",$log);
        msg(-1,"浏览器UA长度异常,请更换浏览器!");
    }
    //读取资料
    $USER_DB = get_db("global_user", "*", ["OR"=>['User'=>$username,'Email'=>$username,'phone'=>$username]]);
    if(empty($USER_DB)){
        $log['description'] = '请求登录>账号不存在';
        insert_db("user_log",$log);
        msg(-1,'账号不存在');
    }
    $log['uid'] = $USER_DB['ID'];
    //登录入口
    session_start();
    if($_SESSION['login'] != $global_config["Login"] && $_SESSION['login'] != $USER_DB['Login'] ){
        $log['description'] = '请求登录>登录入口错误';
        insert_db("user_log",$log);
        msg(-1,"请求失败,请刷新登录页面再试");
    }
    //双重验证
    $LoginConfig = unserialize( $USER_DB['LoginConfig'] );
    if(!empty($LoginConfig['totp_key'])){
        if(empty($_POST['otp_code'])){
            msgA(['code'=>2]);
        }
        require DIR . '/system/Authenticator.php';
        $totp = new PHPGangsta_GoogleAuthenticator();
        $checkResult = $totp->verifyCode($LoginConfig['totp_key'], $_POST['otp_code'], 2);
        if(!$checkResult){
            $log['description'] = '请求登录>动态口令错误';
            insert_db("user_log",$log);
            msgA(['code'=>-1,'msg'=>'动态口令错误']);
        }
    }
    //验证密码
    if(Get_MD5_Password($password,$USER_DB["RegTime"]) === $USER_DB["Password"]){
        $log['description'] = '请求登录>登录成功';
        insert_db("user_log",$log);
        //保持登录
        $keep_login = isset($_POST['keep']) && $_POST['keep'] == 'on';
        if($keep_login == true){
            $LoginConfig['Session'] = ($LoginConfig['Session'] > 0 ? $LoginConfig['Session'] : 7 );
        }else{
            $LoginConfig['Session'] = 0;
        }
        $USER_DB['LoginConfig'] = serialize($LoginConfig);
        //设置Cookie
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
            setcookie('Default_User', $USER_DB['User'], strtotime("+360 day"),"/",'',false,false);
        }
        msgA(['code'=>1,'msg'=>'登录成功','url'=>$url]);
    }else{
        $log['description'] = '请求登录>账户或密码错误';
        insert_db("user_log",$log);
        msg(-1,"账户或密码错误");
    }
}
