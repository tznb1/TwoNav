<?php if(!defined('DIR')){header('HTTP/1.1 404 Not Found');header("status: 404 Not Found");exit;}

$type = htmlspecialchars(trim($_GET['type']),ENT_QUOTES); 

if (function_exists($type) ) {
    if($GLOBALS['global_config']['article'] != 1 || !check_purview('article',1)){
        msg(-1,'无权限');
    }
    $type();
}else{
    Amsg(-1,'请求类型错误 >> '.$type);
}

//上传图片
function uploadImage(){
    global $u;
    //权限检测
    if(!check_purview('article_image',1)){
        msgA(['errno'=>-1,'message'=>'您的用户组无权限上传图片']);
    }elseif(empty($_FILES["file"]) || $_FILES["file"]["error"] > 0){
        msgA(['errno'=>-1,'message'=>'文件上传失败']);
    }
    
    //取后缀并判断是否支持
    $suffix = strtolower(end(explode('.',$_FILES["file"]["name"])));
    if(!preg_match('/^(jpg|png|gif|bmp|jpeg|svg)$/',$suffix)){
        @unlink($_FILES["file"]["tmp_name"]);
        msgA(['errno'=>-1,'message'=>'文件格式不被支持']);
    }
    //限制文件大小
    if(filesize($_FILES["file"]["tmp_name"]) > 5 * 1024 * 1024){
        msgA(['errno'=>-1,'message'=>'文件大小超限']);
    }
    //文件临时路径
    $ym = date("Ym");
    $path = DIR . "/data/user/{$u}/upload/{$ym}/";
    //检测目录,不存在则创建!
    if(!Check_Path($path)){
        msgA(['errno'=>-1,'message'=>'创建upload目录失败,请检查权限']);
    }
    $tmp_name = 'AI_'.uniqid().'.'.$suffix;
    //移动文件
    if(!move_uploaded_file($_FILES["file"]["tmp_name"],"{$path}/{$tmp_name}")) {
        msgA(['errno'=>-1,'message'=>'上传失败,请检查目录权限']);
    }else{
        msgA(['errno'=>0,'data'=>['url'=>"./data/user/{$u}/upload/{$ym}/$tmp_name",'alt'=>$_FILES["file"]["name"],'href'=>''],'message'=>'上传成功']);
    }
}

//上传视频
function uploadVideo(){
    msgA(['errno'=>-1,'message'=>'未开放']);
    global $u;
    //权限检测
    if(!check_purview('article_image',1)){
        msgA(['errno'=>-1,'message'=>'您的用户组无权限上传视频']);
    }elseif(empty($_FILES["file"]) || $_FILES["file"]["error"] > 0){
        msgA(['errno'=>-1,'message'=>'文件上传失败']);
    }
    
    //取后缀并判断是否支持
    $suffix = strtolower(end(explode('.',$_FILES["file"]["name"])));
    if(!preg_match('/^(avi|mp4|wma|rmvb|rm|flash|3gp|flv)$/',$suffix)){
        @unlink($_FILES["file"]["tmp_name"]);
        msgA(['errno'=>-1,'message'=>'文件格式不被支持']);
    }
    //限制文件大小
    if(filesize($_FILES["file"]["tmp_name"]) > 20 * 1024 * 1024){
        msgA(['errno'=>-1,'message'=>'文件大小超限']);
    }
    //文件临时路径
    $ym = date("Ym");
    $path = DIR . "/data/user/{$u}/upload/{$ym}/";
    //检测目录,不存在则创建!
    if(!Check_Path($path)){
        msgA(['errno'=>-1,'message'=>'创建upload目录失败,请检查权限']);
    }
    $tmp_name = 'AV_'.uniqid().'.'.$suffix;
    //移动文件
    if(!move_uploaded_file($_FILES["file"]["tmp_name"],"{$path}/{$tmp_name}")) {
        msgA(['errno'=>-1,'message'=>'上传失败,请检查目录权限']);
    }else{
        msgA(['errno'=>0,'data'=>['url'=>"./data/user/{$u}/upload/{$ym}/$tmp_name",'alt'=>$_FILES["file"]["name"],'href'=>''],'message'=>'上传成功']);
    }
}

//获取文章列表
function article_list(){
    $where['uid'] = UID;
    //分类筛选
    if(intval(@$_POST['category']) > 0){
        $where['AND']['category'] = intval(@$_POST['category']);
    }
    //状态筛选
    if(intval(@$_POST['state']) > 0){
        $where['AND']['state'] = intval(@$_POST['state']);
    }
    //关键字筛选
    $query  = $_POST['keyword'];
    if(!empty($query)){
        $where['AND']['OR'] = ["title[~]" => $query,"summary[~]" => $query,"content[~]" => $query];
    }
    //统计条数
    $count = count_db('user_article_list',$where);
    //分页
    $page   = empty(intval($_REQUEST['page'])) ? 1 : intval($_REQUEST['page']);
    $limit  = empty(intval($_REQUEST['limit'])) ? 50 : intval($_REQUEST['limit']);
    $offset = ($page - 1) * $limit; //起始行号
    $where['LIMIT'] = [$offset,$limit];
    $where['ORDER']['weight'] = 'ASC';
    
    $datas = select_db('user_article_list',['id','title','category','category_name','state','password','top','add_time','up_time','browse_count','summary'],$where);

    $categorys = select_db('user_article_categorys',['id','name'],['uid'=>UID]);
    
    foreach (select_db('user_article_categorys',['id','name'],['uid'=>UID]) as $data) {
        $categorys[$data['id']] = $data['name'];
    }
    
    foreach ($datas as &$data) {
        $data['category_name'] = $categorys[$data['category']];
    }
    msgA(['code'=>1,'count'=>$count,'data'=>$datas]);
}

//保存文章
function save_article(){
    check_category($_POST['category']);$time = time();
    if(empty($_POST['id'])){
        insert_db('user_article_list',[
            'uid'=>UID,
            'title'=>$_POST['title'],
            'category'=>$_POST['category'],
            'state'=>$_POST['state'],
            'password'=>'',
            'top'=>0,
            'add_time'=>$time,
            'up_time'=>$time,
            'browse_count'=>0,
            'summary'=>$_POST['summary'],
            'content'=>$_POST['content'],
            'cover'=>'',
            'extend'=>''
            ],[1,'保存成功']);
    }else{
        if(!has_db('user_article_list',['uid'=>UID,'id'=>$_POST['id']])){
            msg(-1,'文章id错误');
        }
        update_db('user_article_list',[
            'title'=>$_POST['title'],
            'category'=>$_POST['category'],
            'state'=>$_POST['state'],
            'up_time'=>$time,
            'summary'=>$_POST['summary'],
            'content'=>$_POST['content'],
            ],['uid'=>UID,'id'=>$_POST['id']],[1,'保存成功']);
    }
    

}
//删除文章
function del_article(){
    $id = json_decode($_POST['id']);
    delete_db('user_article_list',['uid'=>UID,'id'=>$id],[1,'删除成功']);
}
//分类列表
function category_list(){
    $where['uid'] = UID;
    $where['ORDER']['weight'] = 'ASC';
    $data = select_db('user_article_categorys',['id','name','weight','add_time'],$where);
    msgA(['code'=>1,'count'=>count($data),'data'=>$data]);
}
//添加分类
function add_category(){
    $name = trim($_POST['name']);
    $time = time();
    if(empty($name)){
        msg(-1,'分类名称不能为空');
    }
    if(has_db('user_article_categorys',['uid'=>UID,'name'=>$name])){
        msg(-1,'分类名称已存在');
    }
    insert_db('user_article_categorys',[
        'uid'=>UID,
        'name'=>$name,
        'weight'=>0,
        'add_time'=>$time
        ],[1,'添加成功']);
    msg(-1,'添加失败');
}
//删除分类
function del_category(){
    check_category($_POST['id']);
    delete_db('user_article_categorys',['uid'=>UID,'id'=>$_POST['id']],[1,'删除成功']);
}
//保存分类
function save_category(){
    check_category($_POST['id']);
    update_db('user_article_categorys',['name'=>$_POST['name'],'weight'=>$_POST['weight']],['uid'=>UID,'id'=>$_POST['id']],[1,'更新成功']);
}
//检查分类
function check_category($id){
    if(empty($id)){
        msg(-1,'分类ID不能为空');
    }
    if(!has_db('user_article_categorys',['uid'=>UID,'id'=>$id])){
        msg(-1,'分类不存在');
    }
}

