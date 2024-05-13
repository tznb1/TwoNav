<?php if(!defined('DIR')||$global_config['RegOption']=='0'){header('HTTP/1.1 404 Not Found');header("status: 404 Not Found");exit;}
if($global_config['Maintenance'] != 0){Amsg(-1,'网站正在进行维护,请稍后再试!');}
//注册入口
$global_templates = unserialize(get_db("global_config",'v', ["k" => "s_templates"]));
//如果是Get请求则载入登录模板
if($_SERVER['REQUEST_METHOD'] === 'GET'){
    //通用数据初始化
    require DIR."/system/templates.php";
    $reg_tips = get_db('global_config','v',['k'=>'reg_tips']); //注册提示
    require $index_path;
    exit;
}

$user = $_POST['User'];
$pass = $_POST['Password'];
$Email = $_POST['Email'];
$regcode = $_POST['regcode'];
$IP = Get_IP();

//检查注册配置
if ($global_config['RegOption'] == 0){
    msg(-1,'管理员已禁止注册,请联系管理员!'); //不执行,1行已经返回404了
}elseif ($global_config['RegOption'] == 1){
    //开放注册
}elseif ($global_config['RegOption'] == 2){
    //邀请注册
}else{
    msg(-1,'配置错误,请联系站长检查相关配置!');
}

//如果注册码不为空
if(!empty($regcode)){
    $regcode_info = get_db('regcode_list','*',['regcode'=>$regcode,'use_time'=>0 ]);
    if(empty($regcode_info)){
        msg(-1,'注册码无效');
    }
}elseif($global_config['RegOption'] == 2){
    msg(-1,'注册码不能为空');
}

//检查账号和密码是否符合注册要求
if(!preg_match('/^[A-Za-z0-9]{4,13}$/', $user)){
    msg(-1,'账号只能是4到13位的数字和字母!');
}elseif(strlen($Email)>32){
    msg(-1,'邮箱长度超限');
}elseif(strlen($pass)!=32){
    msg(-1,'POST提交的密码异常≠32!');
}elseif(preg_match("/^(system|data|static|templates|index|root|admin)$/i",$user) ) {
    msg(-1,'改用户名已被系统保留!');
}elseif(!preg_match("/\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*/i",$Email)){
    msg(-1,'邮箱错误!');
}

msg(-1,'免费版不支持此功能<br /> <a href="https://gitee.com/tznb/TwoNav/wikis/pages?sort_id=7968669&doc_id=3767990" target="_blank" style="color: #1e9fff;">点击此处前往购买页面</a>');
