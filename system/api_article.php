<?php if(!defined('DIR')){header('HTTP/1.1 404 Not Found');header("status: 404 Not Found");exit;}

$type = htmlspecialchars(trim($_GET['type']),ENT_QUOTES); 

if (function_exists($type) ) {
    if($GLOBALS['global_config']['article'] < 1 || !check_purview('article',1)){
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
    if(!preg_match('/^(jpg|png|gif|bmp|jpeg|svg|webp)$/',$suffix)){
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
//删除图片
function deleteImage(){
    global $u;
    if(empty($_POST['path'])){
        msg(-1,'请求参数错误');
    }
    $path = $_POST['path'];
    $pattern = "/^\.\/data\/user\/{$u}\/upload\/\d{6}\/AI_[A-Za-z0-9_]+\.(jpg|png|gif|bmp|jpeg|svg|webp)$/i";
    if(preg_match($pattern,$path) && is_file($path)){
        @unlink($path);
    }else{
        msg(-1,'请求参数错误');
    }
    //需考虑编辑文章删除封面时未点击保存的情况
    if(is_file($path)){
        msg(-1,'删除失败');
    }else{
        msg(1,'删除成功');
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

    $datas = select_db('user_article_list',['id','title','category','state','password','top','add_time','up_time','browse_count','summary','cover'],$where);

    //查询分类
    $categorys = select_db('user_categorys',['cid(id)','name'],['uid'=>UID]);
    $categorys = array_column($categorys,'name','id');
    //为文章添加分类名称
    foreach ($datas as &$data) {
        $data['category_name'] = $categorys[$data['category']] ?? 'Null';
    }
    msgA(['code'=>1,'count'=>$count,'data'=>$datas]);
}

//保存文章
function save_article(){
    if(empty($_POST['category']) || !has_db('user_categorys',['uid'=>UID,'cid'=>$_POST['category']])){
        msg(-1,'分类不存在');
    }
    $time = time();
    //id为空,添加文章
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
            'cover'=>$_POST['cover_url'],
            'extend'=>''
        ],[1,'保存成功']);
    //存在id,更新文章数据
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
            'cover'=>$_POST['cover_url']
        ],['uid'=>UID,'id'=>$_POST['id']],[1,'保存成功']);
    }
    

}
//删除文章
function del_article(){
    $id = json_decode($_POST['id']);
    if(empty($id)) msg(-1,'参数错误');
    delete_db('user_article_list',['uid'=>UID,'id'=>$id],[1,'操作成功']);
}
//修改分类
function up_category(){
    $id = json_decode($_POST['id']);
    if(empty($id)) msg(-1,'参数错误');
    if(empty($_POST['category_id']) || !has_db('user_categorys',['uid'=>UID,'cid'=>$_POST['category_id']])){
        msg(-1,'分类不存在');
    }
    update_db('user_article_list',['category'=>$_POST['category_id']],['uid'=>UID,'id'=>$id],[1,'操作成功']);
}
//修改状态
function up_state(){
    $id = json_decode($_POST['id']);
    if(empty($id)) msg(-1,'参数错误');
    if(!in_array($_POST['state_id'],['1','2','3','4'])){
        msg(-1,'状态参数错误');
    }
    update_db('user_article_list',['state'=>$_POST['state_id']],['uid'=>UID,'id'=>$id],[1,'操作成功']);
}


//保存设置 (与站点配置共享)
function save_article_set(){
    //检查配置参数
    if(!in_array($_POST['visual'],['0','1','2']) || !in_array($_POST['icon'],['0','1','2'])){
        msg(-1,'参数错误');
    }
    //读取站点配置
    $s_site = unserialize(get_db('user_config','v',['uid'=>UID,'k'=>'s_site']));
    $s_site['article_visual'] = $_POST['visual'];
    $s_site['article_icon'] = $_POST['icon'];
    update_db("user_config",["v"=>$s_site],["k"=>'s_site',"uid"=>UID],[1,'保存成功']);
}


