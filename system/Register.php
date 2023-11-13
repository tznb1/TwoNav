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
}elseif(!empty(get_db('global_user','ID',['User'=>$user ]))){
    msg(-1,'该账号已被注册!');
}elseif(!empty(get_db('global_user','ID',['Email'=>$Email ]))){
    msg(-1,'该邮箱已被使用!');
}elseif(!preg_match("/\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*/i",$Email)){
    msg(-1,'邮箱错误!');
}elseif(username_retain_verify($user)){
    msg(-1,'该账号已被站长保留!');
}

//插入用户表和创建初始数据库
$RegTime = time();
$PassMD5 = Get_MD5_Password($pass,$RegTime);
$Elogin = Get_Exclusive_Login($user);

//用户组
if(!empty($regcode_info['u_group'])){
    $UserGroup = $regcode_info['u_group'];
}elseif(!empty($global_config['default_UserGroup'])){
    $UserGroup = $global_config['default_UserGroup'];
}else{
    $UserGroup = 'default';
}

//读取用户组信息,如果用户组不存在则设为默认用户组
if(!in_array($UserGroup,['default','root','visitor'])){
    $Group = get_db('user_group','*',['code' => $UserGroup]);
    if(empty( $Group )){
        $UserGroup = 'default';
    }
}

$blueprint = !empty(get_db('global_user','ID',['ID'=>$Group['uid']]));

if($blueprint){
    $LoginConfig = unserialize(get_db('global_user','LoginConfig',['ID'=>$Group['uid']]));
    $LoginConfig['Password2'] = '';
}else{
    //不需要修改内容,无需反序化
    $LoginConfig = get_db('global_config','v',['k'=>'LoginConfig']);
}
//父ID
if(!empty($regcode_info['user'])){
    $FID = get_db('global_user','ID',['User'=>$regcode_info['user']]);
}else{
    $FID = 0;
}


insert_db("global_user", [
    "FID"=>$FID,
    "User"=>$user,
    "Password"=>$PassMD5,
    "UserGroup"=>$UserGroup,
    "Email"=>$Email,
    "SecretKey"=>'',
    "Token"=>'',
    "RegIP"=>$IP,
    "RegTime"=>$RegTime,
    "Login"=>$Elogin,
    "LoginConfig"=>$LoginConfig
]);

//读取用户信息
$USER_DB = get_db("global_user", "*", ["User"=>$user]);
//记录日志
insert_db("user_log", ["uid" => $USER_DB['ID'],"user"=>$USER_DB['User'],"ip"=>$IP,"time"=>time(),"type" => 'register',"content"=>Get_Request_Content(),"description"=>"注册账号"]);
//生成Cookie
Set_key($USER_DB);

//注册码注册时回写数据
if(!empty($regcode_info)){
    update_db('regcode_list',['use_time'=>time(),'use_state'=>'已使用,用户名:'.$user],['id'=>$regcode_info['id']]);
}

//写默认站点配置
if($blueprint){
    $s_site = get_db('user_config','v',['k'=>'s_site','uid'=>$Group['uid']]);
}else{
    $s_site = get_db('global_config','v',['k'=>'s_site']);
}

insert_db("user_config", ["uid"=>$USER_DB['ID'], "k" => "s_site","v" => $s_site,"d" => '站点配置','t'=>'config']);

//写默认模板
if($blueprint){
    $global_templates = unserialize(get_db('user_config','v',['k'=>'s_templates','uid'=>$Group['uid']]));
}else{
    $global_templates = unserialize(get_db('global_config','v',['k'=>'s_templates']));
}

insert_db("user_config", ["uid" => $USER_DB['ID'],"k"=>"s_templates","v"=>$global_templates,"t"=>"config","d" => '默认模板']);

//写初始分类和链接
$time = time();
if($blueprint){
    $categorys = select_db('user_categorys','*',['uid'=>$Group['uid']]);
    $links = select_db('user_links','*',['uid'=>$Group['uid']]);
}else{
    $categorys = select_db('user_categorys','*',['uid'=>0]);
    $links = select_db('user_links','*',['uid'=>0]);
}

foreach ($categorys as $key => $data){
    $data['uid'] = $USER_DB['ID'];
    $data['add_time'] = $time;
    $data['up_time'] = $time;
    unset($data['id']);
    insert_db('user_categorys',$data);
}

foreach ($links as $key => $data){
    $data['uid'] = $USER_DB['ID'];
    $data['add_time'] = $time;
    $data['up_time'] = $time;
    unset($data['id']);
    insert_db('user_links',$data);
}

//写初始ID
$link_id = intval(max_db('user_links','lid',['uid'=>$USER_DB['ID']])) +1;
insert_db("user_config", ["uid"=>$USER_DB['ID'],"k"=>"link_id","v"=>$link_id,"t"=>"max_id","d"=>'链接ID']);
$category_id = intval(max_db('user_categorys','cid',['uid'=>$USER_DB['ID']])) +1;
insert_db("user_config", ["uid"=>$USER_DB['ID'],"k"=>"category_id","v"=>$category_id,"t"=>"max_id","d"=>'分类ID']);
insert_db("user_config", ["uid"=>$USER_DB['ID'],"k"=>"pwd_group_id","v"=>1,"t"=>"max_id","d"=>'加密组ID']);


//账号保留
function username_retain_verify($username){
    $list = get_db("global_config", "v", ["k" => "username_retain"]);
    if(empty($list)){
        return false;
    }
    $patterns = explode("\n", $list);
    foreach($patterns as $pattern){
        if (preg_match($pattern, $username)) {
            return true;
        }
    }
    return false;
}

//返回注册成功
msg(1,'注册成功');


