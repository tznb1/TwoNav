<?php if(!defined('DIR')){header('HTTP/1.1 404 Not Found');header("status: 404 Not Found");exit;}

if (function_exists($method)) {
    $method();
}else{
    Amsg(-1,'方法未找到 >> '.$method);
}

//添加链接
function add_link(){
    $fid = intval(@$_POST['fid']);
    $title = $_POST['title'];
    $url = $_POST['url'];
    $description = empty($_POST['description']) ? '' : $_POST['description'];
    $property = empty($_POST['property']) ? 0 : 1;
    //检测链接是否合法
    check_link($fid,$title,$url); 
    //检查链接是否已存在
    if(get_db('user_links','lid',['uid'=>UID ,"url" => $url])){
        msgA(['code'=>-1,'err_msg'=>"链接已存在"]);
    }
    //取最大链接ID
    $lid = get_maxid('link_id');
    $data = [
        'uid'           =>  UID,
        'lid'           =>  $lid,
        'fid'           =>  $fid,
        'pid'           =>  0,
        'title'         =>  htmlspecialchars($title,ENT_QUOTES),
        'url'           =>  $url,
        'url_standby'   =>  '',
        'description'   =>  htmlspecialchars($description,ENT_QUOTES),
        'add_time'      =>  time(),
        'up_time'       =>  time(),
        'click'         =>  0,
        'weight'        =>  $lid,
        'status'        =>  1,
        'property'      =>  $property,
        'icon'          =>  ''
        ];
        //插入数据库
        insert_db('user_links',$data); 
        msgA(['code'=>0,'id'=>$lid]);
}

//编辑链接
function edit_link(){
    $lid = intval(@$_POST['id']);
    $fid = intval(@$_POST['fid']);
    $title = $_POST['title'];
    $url = $_POST['url'];
    $description = empty($_POST['description']) ? '' : $_POST['description'];
    $property = empty($_POST['property']) ? 0 : 1;
    //检测链接是否合法
    check_link($fid,$title,$url,''); 
    //描述长度检测
    $length_limit = unserialize(get_db("global_config","v",["k"=>"length_limit"]));
    if($length_limit['l_desc'] > 0 && strlen($description) > $length_limit['l_desc'] ){
        msg(-1,'描述长度不能大于'.$length_limit['l_desc'].'个字节');
    }
    //关键字长度检测
    if($length_limit['l_key'] > 0 && strlen($keywords) > $length_limit['l_key'] ){
        msg(-1,'关键字长度不能大于'.$length_limit['l_key'].'个字节');
    }
    //检查链接是否已存在
    if(has_db('user_links',['uid'=>UID ,'lid[!]'=>$lid, "url" => $url])){msg(-1011,'链接已存在!');}
    //检查链接ID是否存在
    if(!has_db('user_links',['uid'=>UID ,'lid'=>$lid])){msg(-1012,'链接ID不存在!');}
    $data = [
        'fid'           =>  $fid,
        'title'         =>  htmlspecialchars($title,ENT_QUOTES),
        'url'           =>  $url,
        'description'   =>  htmlspecialchars($description,ENT_QUOTES),
        'up_time'       =>  time(),
        'property'      =>  $property
        ];
    
    //更新数据
    update_db('user_links',$data,['uid'=>UID,'lid'=>$lid ]);
    msgA(['code'=>0,'msg'=>'successful']);
}


//删除链接
function del_link(){
    $lid = intval(trim($_REQUEST['id']));
    if(empty($lid)){
        msg(-1010,'链接ID不能为空');
    }
    $where['lid'] = $lid;
    $where['uid'] = UID;
    if(!has_db('user_links',$where)){
        msg(-1010,'链接id不存在');
    }
    delete_db('user_links',$where,[0,'删除成功']);
}

//搜索链接
function global_search(){
    $keyword = htmlspecialchars($_REQUEST['keyword']);
    if( strlen($keyword) < 2 ) {
        msg(-2000,'关键字的长度太短');
    }elseif( strlen($keyword) > 32 ) {
        msg(-2000,'关键字长度过长');
    }
    $where['uid'] = UID;
    $where['status'] = 1;
    $where['AND']['OR'] = ["title[~]" => $keyword,"url[~]" => $keyword, "url_standby[~]" => $keyword,"description[~]" => $keyword];
    $where['ORDER'] = ['weight'=>'DESC'];
    $field = ['lid(id)','fid','status','property','title','url','url_standby','weight','description','click','add_time','up_time'];
    $datas = select_db('user_links',$field,$where);
    links_add_category_field($datas); //添加分类信息
    msgA(['code'=>0,'msg'=>'获取成功','count'=>count($datas),'data'=>$datas]);
}
//查询链接列表
function link_list(){
    $page   = empty(intval($_REQUEST['page'])) ? 1 : intval($_REQUEST['page']);
    $limit  = empty(intval($_REQUEST['limit'])) ? 50 : intval($_REQUEST['limit']);
    $offset = ($page - 1) * $limit; //起始行号
    $where['uid'] = UID;
    $where['status'] = 1;
    $count = count_db('user_links',$where); //统计条数
    //权重排序(数字小的排前面)
    $where['ORDER']['weight'] = 'ASC';
    $where['ORDER']['lid'] = 'ASC';
    //分页
    $where['LIMIT'] = [$offset,$limit];
    //查询
    $datas = select_db('user_links',['lid(id)','fid','property','title','url','url_standby','weight','description','icon','click','add_time','up_time'],$where);
    links_add_category_field($datas); //添加分类信息
    msgA(['code'=>0,'msg'=>'获取成功','count'=>$count,'data'=>$datas]);
}
//查询单个链接
function get_a_link(){
    $lid = intval(trim($_REQUEST['id']));
    if(empty($lid)){
        msg(-1,'id不能为空');
    }
    $where['lid'] = $lid;
    $where['uid'] = UID;
    $link_info = get_db('user_links',['lid','fid','property','title','url','description'],$where);
    if(empty($link_info)){
        msgA(['code'=>-1,'msg'=>'没有找到链接信息','data'=>[]]);
    }else{
        msgA(['code'=>0,'data'=>$link_info]);
    }
}
//查询指定分类的链接
function q_category_link(){
    $category_id = empty(intval($_REQUEST['category_id'])) ? 1 : intval($_REQUEST['category_id']);
    $page   = empty(intval($_REQUEST['page'])) ? 1 : intval($_REQUEST['page']);
    $limit  = empty(intval($_REQUEST['limit'])) ? 50 : intval($_REQUEST['limit']);
    $offset = ($page - 1) * $limit; //起始行号
    $where['uid'] = UID;
    $where['AND']['status'] = 1;
    $where['AND']['fid'] = $category_id;
    
    $count = count_db('user_links',$where); //统计条数
    //权重排序(数字小的排前面)
    $where['ORDER']['weight'] = 'ASC';
    $where['ORDER']['lid'] = 'ASC';
    //分页
    $where['LIMIT'] = [$offset,$limit];
    //查询
    $datas = select_db('user_links',['lid(id)','fid','property','title','url','url_standby','weight','description','icon','click','add_time','up_time'],$where);
    links_add_category_field($datas); //添加分类信息

    
    msgA(['code'=>0,'msg'=>'获取成功','count'=>$count,'data'=>$datas]);
}
//查询分类列表
function category_list(){
    $where = ['uid'=>UID,'status'=>1,'ORDER' => ['weight'=>'ASC']];
    $datas = select_db('user_categorys',['cid(id)','fid','property','name','add_time','up_time','weight','description','font_icon'],$where);
    msgA(['code'=>0,'msg'=>'获取成功','count'=>count($datas),'data'=>$datas ]);
}

//添加分类
function add_category(){
    if(empty($_POST['name'])){
        msg(-1,'分类名称不能为空');
    }elseif(!preg_match('/^(fa fa-|layui-icon layui-icon-)([A-Za-z0-9]|-)+$/',$_POST['font_icon'])){
        $_POST['font_icon'] = 'fa fa-star-o';
    }
    //分类名查重
    if(get_db('user_categorys','cid',['uid'=>UID ,"name" => $_POST['name']])){
        msg(-1,'分类名称已存在');
    }
    //父分类不能是二级分类
    if(intval($_POST['fid']) !=0 && get_db('user_categorys','fid',['uid'=>UID ,"cid" => intval($_POST['fid']) ]) !=0  ){
        msg(-1,'父分类不能是二级分类');
    }

    //长度检测
    $length_limit = unserialize(get_db("global_config","v",["k"=>"length_limit"]));
    if($length_limit['c_name'] > 0 && strlen($_POST['name']) > $length_limit['c_name'] ){
        msg(-1,'名称长度不能大于'.$length_limit['c_name'].'个字节');
    }
    if($length_limit['c_desc'] > 0 && strlen($_POST['description']) > $length_limit['c_desc'] ){
        msg(-1,'名称长度不能大于'.$length_limit['c_desc'].'个字节');
    }
    //取最大CID
    $cid = get_maxid('category_id');
    //插入数据库
    insert_db('user_categorys',[
        'uid'=>UID,
        'cid'=>$cid,
        'fid'=>intval($_POST['fid']??'0'),
        'pid'=>0,
        'status'=>1,
        'property'=>intval($_POST['property']??'0'),
        'name'=>htmlspecialchars($_POST['name'],ENT_QUOTES),
        'add_time'=>time(),
        'up_time'=>time(),
        'weight'=>$cid,
        'description'=>htmlspecialchars($_POST['description'],ENT_QUOTES),
        'font_icon'=>$_POST['font_icon'],
        'icon'=>''
        ],[0,'添加成功']
    );
}
//编辑分类
function edit_category(){
    if(empty($_POST['name'])){
        msg(-1,'分类名称不能为空');
    }elseif(!preg_match('/^(fa fa-|layui-icon layui-icon-)([A-Za-z0-9]|-)+$/',$_POST['font_icon'])){
        $_POST['font_icon'] = 'fa fa-star-o';
    }
    //父分类不能是自己
    if($_POST['id'] == $_POST['fid']){
        msg(-1,'父分类不能是自己');
    }
    //查CID是否存在
    if(!get_db('user_categorys','cid',['uid'=>UID ,"cid" => intval($_POST['id'])])){
        msg(-1,'分类不存在');
    }
    //分类名查重(排除自身)
    if(get_db('user_categorys','cid',['uid'=>UID,'cid[!]'=>intval($_POST['id']),"name" => $_POST['name']])){
        msg(-1,'分类名称已存在');
    }
    //父分类不能是二级分类
    if(intval($_POST['fid']) !=0 && get_db('user_categorys','fid',['uid'=>UID ,"cid" => intval($_POST['fid']) ]) !=0  ){
        msg(-1,'父分类不能是二级分类');
    }
    //分类下存在子分类,禁止修改父分类
    if( $_POST['fid']!=0  && count_db('user_categorys',['uid'=>UID,'fid'=>$_POST['id']])>0){
        msg(-1,'该分类下已存在子分类！');
    }
    //查父分类是否存在
    if( $_POST['fid'] !=0  && !get_db('user_categorys','cid',['uid'=>UID ,"cid" => intval($_POST['fid'])])){
        msg(-1,'父分类不存在');
    }
    //长度检测
    $length_limit = unserialize(get_db("global_config","v",["k"=>"length_limit"]));
    if($length_limit['c_name'] > 0 && strlen($_POST['name']) > $length_limit['c_name'] ){
        msg(-1,'名称长度不能大于'.$length_limit['c_name'].'个字节');
    }
    if($length_limit['c_desc'] > 0 && strlen($_POST['description']) > $length_limit['c_desc'] ){
        msg(-1,'名称长度不能大于'.$length_limit['c_desc'].'个字节');
    }
    
    //更新数据
    $data = [
        'fid'=>$_POST['fid'],
        'property'=>intval($_POST['property']??'0'),
        'name'=>$_POST['name'],
        'up_time'=>time(),
        'description'=>$_POST['description']??'',
        'font_icon'=>$_POST['font_icon'],
        ];
    if(!isset($_POST['fid'])){ //为空时不修改父id,避免二级变一级
        unset($data['fid']);
    }
    if(!isset($_POST['font_icon'])){
        unset($data['font_icon']);
    }
    update_db('user_categorys',$data,['uid'=>UID ,"cid"=>intval($_POST['id'])],[0,'successful']);
}

//查询单个分类信息
function get_a_category(){
    $cid = intval(trim($_REQUEST['id']));
    if(empty($cid)){
        msg(-1,'id不能为空');
    }
    $where['cid'] = $cid;
    $where['uid'] = UID;
    $category_info = get_db('user_categorys',['cid','fid','property','name','font_icon','description','icon'],$where);
    if(empty($category_info)){
        msgA(['code'=>-1,'msg'=>'没有找到分类信息','data'=>[]]);
    }else{
        msgA(['code'=>0,'data'=>$category_info]);
    }
}

//获取TwoNav信息
function app_info(){
    $data['php_version'] = floatval(PHP_VERSION);
    $data['onenav_version'] = 'v0.9.35-20240318'; //模拟版本号用于解决新版插件检测版本>1提示发生异常
    $data['twonav_version'] = SysVer;
    $data['cat_num'] = count_db('user_categorys',['uid'=>UID])??0;
    $data['link_num'] = count_db('user_links',['uid'=>UID])??0;
    $data['username'] = U;
    msgA(['code'=>200,'msg'=>'success','data'=>$data]);
}

//是否已登录,由于上游已经拦截未登录状态,所以这里固定返回已登录
function check_login(){
    msgA(['code'=>200,'data'=>'true','msg'=>'success']);
}
//给链接数组添加分类字段
function links_add_category_field(&$arr){
    $where['uid'] = UID;
    $where['status'] = 1;
    $categorys = select_db('user_categorys',['cid(id)','name'],$where);
    $newCategorys = array_column($categorys,'name','id');
    foreach ($arr as &$data) {
        $data['category_name'] = $newCategorys[$data['fid']];
    }
    return $arr;
}

