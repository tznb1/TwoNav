<?php 
$apply = $global_config['apply'];
// 如果管理了收录功能则返回404
if ($apply != 1 ){
    load_tip();
    header('HTTP/1.1 404 Not Found');
    header("status: 404 Not Found");
    exit;
}
$apply = unserialize( get_db("user_config", "v", ["k" => "apply","uid"=>UID]));
// 用户关闭收录申请
if ( $apply['apply'] == 0 ){
    if($_SERVER['REQUEST_METHOD'] === 'GET'){
        load_tip();
    }else{
        msg(-1,"用户已关闭收录申请");
    }
}
//get请求载入页面
if($_SERVER['REQUEST_METHOD'] === 'GET'){
    require DIR."/system/templates.php";
    require($index_path);
    exit;
}
//载入提示页
function load_tip() {
    $content = '站长或用户未开启申请收录功能';
    require DIR.'/templates/admin/page/404.php';
    exit;
}

//强制加载防火墙来过滤相关攻击!
$global_config['XSS_WAF'] = 1; $global_config['SQL_WAF'] = 1; 
require DIR.'/system/firewall.php';

// 遍历请求表单,拦截可疑内容!
foreach($_POST as $key =>$value){
    if( htmlspecialchars($value,ENT_QUOTES) != $value ){
        msg(-1,$key.' > 请避免使用<\'&">单引号,双引号等特殊字符!');
    }elseif( strlen($value) >= 256 ){
        msg(-1,$key.' > 字符串长度不允许超过256');
    }
}


$title = $_POST['title'];
$url =  $_POST['url'];
$iconurl = $_POST['iconurl'] ?? '';
$description = $_POST['description'] ?? '';
$category_id = intval ($_POST['category_id']);
$email = $_POST['email'] ?? '';
$user_ip = Get_IP();
if( !filter_var($url, FILTER_VALIDATE_URL) ) {
    msg(-1,'URL无效!');
}elseif(!empty($apply['iconurl'])  && !filter_var($iconurl, FILTER_VALIDATE_URL) ){
    msg(-1,'网站图标无效!');
}elseif(!empty($apply['email']) && !preg_match('/^([a-zA-Z]|[0-9])(\w|\-)+@[a-zA-Z0-9]+\.([a-zA-Z]{2,4})$/',$email)){
    msg(-1,'联系邮箱无效!');
}elseif(!isset($_POST['category_id'])){
    msg(-1,'分类ID不能为空!');
}elseif(!isset($_POST['title'])){
    msg(-1,'网站标题不能为空!');
}elseif(!empty($apply['description']) && empty($_POST['description'])){
    msg(-1,'网站描述不能为空!');
}
//获取和检查分类信息
$where['cid'] = $category_id;
$where['uid'] = UID;
$category_info = get_db('user_categorys',['cid','fid','property','name','font_icon','description'],$where);
if(empty($category_info) || $category_info['property'] != 0){
    msgA(['code'=>-1,'msg'=>'没有找到分类信息']);
}

//检查是否重复
$url_data  = get_db("user_apply","*",["url"=> $url,'uid'=>UID ]);
if(isset($url_data['id'])){
    if ($url_data['state'] == 0){
        msg(-1,'审核中,请勿重复提交!');
    }elseif ($url_data['state'] == 1 || $url_data['state'] == 3 ){
        msg(-1,'已通过,请勿重复提交!');
    }elseif ($url_data['state'] == 2){
        msg(-1,'已拒绝,请勿重复提交!');
    }
}

// 统计IP 24小时内提交的数量!,超限则拦截!
$count = count_db("user_apply", ["uid"=>UID , "ip" => $user_ip ,"time[>]" => time() - 60*60*24]);
if ($count >= $apply['submit_limit'] ?? 5){
    msg(-1,'您提交的申请数量已达到上限!请明天再试!');
}


$data = [
    'uid'           =>  UID,
    'iconurl'       =>  $iconurl,
    'title'         =>  $title,
    'url'           =>  $url,
    'email'         =>  $email,
    'ip'            =>  $user_ip,
    'ua'            =>  $_SERVER['HTTP_USER_AGENT'],
    'time'          =>  time(),
    'state'         =>  0, // 0.待审核 1.手动通过 2.已拒绝 3.自动通过
    'category_id'   =>  $category_id,
    'category_name' =>  $category_info['name'],
    'description'   =>  $description
];

//0.关闭 1.开启 2.无需审核
if($apply['apply'] == 1){
    $data['state'] = 0 ;
}elseif($apply['apply'] == 2){
    $data['state'] = 3 ;
    if(!empty(get_db("user_links","*",["url"=> $url,'uid'=>UID ]))){
        msg(-1,'URL已经存在！'); //存在于链接列表中!
    }
    $lid = get_maxid('link_id');
    $url_data = [
        'lid'           =>  $lid,
        'uid'           =>  UID,
        'fid'           =>  $category_id,
        'title'         =>  $title,
        'url'           =>  $url,
        'description'   =>  $description,
        'add_time'      =>  time(),
        'up_time'       =>  time(),
        'weight'        =>  0,
        'property'      =>  0,
        'icon'       =>  $iconurl
    ];
    insert_db('user_links',$url_data);
}
insert_db('user_apply',$data,[1,'提交成功!']);
?>
