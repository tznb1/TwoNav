<?php if(!defined('DIR')){header('HTTP/1.1 404 Not Found');header("status: 404 Not Found");exit;}

if ( in_array($method,['link_list','get_a_link','q_category_link','category_list','get_a_category','check_login','add_link']) && function_exists($method) ) {
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
//查询链接列表
function link_list(){
    $page   = empty(intval($_REQUEST['page'])) ? 1 : intval($_REQUEST['page']);
    $limit  = empty(intval($_REQUEST['limit'])) ? 50 : intval($_REQUEST['limit']);
    $offset = ($page - 1) * $limit; //起始行号
    $where['uid'] = UID;
    $where['AND']['status'] = 1;
    if(Access_Type != 'all'){
        $where['property'] = 0;
    }

    $count = count_db('user_links',$where); //统计条数
    //权重排序(数字小的排前面)
    $where['ORDER']['weight'] = 'ASC';
    $where['ORDER']['lid'] = 'ASC';
    //分页
    $where['LIMIT'] = [$offset,$limit];
    //查询
    $datas = select_db('user_links',['lid(id)','fid','property','title','url','url_standby','weight','description','icon','click','add_time','up_time'],$where);
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
        if(Access_Type == 'all' || $link_info['property'] == 0){
            msgA(['code'=>0,'data'=>$link_info]);
        }else{
            msgA(['code'=>-1,'msg'=>'私有链接,无权查看','data'=>[]]);
        }
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
    if(Access_Type != 'all'){
        $where['property'] = 0;
    }
    
    $count = count_db('user_links',$where); //统计条数
    //权重排序(数字小的排前面)
    $where['ORDER']['weight'] = 'ASC';
    $where['ORDER']['lid'] = 'ASC';
    //分页
    $where['LIMIT'] = [$offset,$limit];
    //查询
    $datas = select_db('user_links',['lid(id)','fid','property','title','url','url_standby','weight','description','icon','click','add_time','up_time'],$where);
    msgA(['code'=>0,'msg'=>'获取成功','count'=>$count,'data'=>$datas]);
}
//查询分类列表
function category_list(){
    $where = ['uid'=>UID,'status'=>1,'ORDER' => ['weight'=>'ASC']];
    if(Access_Type != 'all'){
        $where['property'] = 0;
    }
    $datas = select_db('user_categorys',['cid(id)','fid','property','name','add_time','up_time','weight','description','font_icon'],$where);
    msgA(['code'=>0,'msg'=>'获取成功','count'=>count($datas),'data'=>$datas ]);
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
        if(Access_Type == 'all' || $category_info['property'] == 0){
            msgA(['code'=>0,'data'=>$category_info]);
        }else{
            msgA(['code'=>-1,'msg'=>'私有分类,无权查看','data'=>[]]);
        }
    }
}

//是否已登录
function check_login(){
    if(Access_Type == 'open'){
        msgA(['code'=>-1002,'data'=>'false','err_msg'=>'Authorization failure!']);
    }else{
        msgA(['code'=>200,'data'=>'true','msg'=>'success']);
    }
}