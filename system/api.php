<?php if(!defined('DIR')){header('HTTP/1.1 404 Not Found');header("status: 404 Not Found");exit;}
//允许跨域访问
header("Access-Control-Allow-Origin: *"); 
header("Access-Control-Allow-Headers: Access-Control-Allow-Private-Network,Content-Type, AccessToken, X-CSRF-Token, Authorization, Token,X-Token,X-Cid");
AccessControl();
//鉴权验证 Cookie验证通过,验证二级密码,Cookie验证失败时尝试验证token
if(!empty(trim($_REQUEST['token']))){ $_COOKIE = []; } //兼容浏览器插件,避免干扰

//获取请求方法
$method = htmlspecialchars(trim($_GET['method']),ENT_QUOTES); 
$LoginConfig = unserialize($USER_DB['LoginConfig']);
$api_model = $LoginConfig['api_model']; //API模式

if(!is_login()){
    //没登录,尝试验证token
    if( empty($USER_DB['Token']) ){
        msg(-1,'鉴权失败:未登录且未设置token'); 
    }
    //获取请求token
    $token = trim($_REQUEST['token']);
    if(empty($token)){
        msg(-1,'鉴权失败:未登录且请求未携带token'); 
    }else{
        if($token === $USER_DB['Token']){
            //验证通过
        }else{
            msg(-1,'鉴权失败:未登录且token错误'); 
        }
    }
//Cookie登录验证OK,验证二级密码
}elseif(Check_Password2($LoginConfig)){
    // Cookie 二级密码验证成功(未设置时也认为成功)
}else{
    msg(-1,'请先验证二级密码!');
}

//扩展API
if($global_config['api_extend'] == 1 && is_file('./system/api_extend.php')){
    require './system/api_extend.php';
}

//兼容API
$compatible_list = ['add_link','edit_link','del_link','link_list','get_a_link','q_category_link','category_list','get_a_category','add_category','edit_category','app_info','check_login','global_search'];
if(in_array($api_model,['compatible','compatible+open']) && in_array($method,$compatible_list)){
    require 'api_compatible.php';
    exit;
}

//站长相关方法名
$root = ['write_subscribe','write_sys_settings','write_default_settings','read_user_list','write_user_info','read_purview_list','read_users_list','write_users','read_regcode_list','write_regcode','other_upsys','read_log','other_root','other_services'];
if(in_array($method,$root)){
    require('api_root.php');
//非站长接口则判断是否加载防火墙
}elseif($global_config['XSS_WAF'] == 1 || $global_config['SQL_WAF'] == 1){
    require DIR.'/system/firewall.php';
}

//函数名过滤和检测是否存在,存在则执行,否则报错
if ( preg_match("/^read_|^write_|^other_/",$method) && function_exists($method) ) {
    $method();
}else{
    Amsg(-1,'方法未找到 >> '.$method);
}

//读分类列表
function read_category_list(){
    if($_GET['type'] == 'onlyf'){
        $where = ['uid'=>UID,'fid'=>0,'ORDER' => ['weight'=>'ASC']];
    }else{
        $where = ['uid'=>UID,'ORDER' => ['weight'=>'ASC']];
    }
    //
    if($_GET['type'] == 'share'){
        $categorys = [];
        //获取父分类
        $category_parent = select_db('user_categorys',['cid(id)','fid','name','font_icon'],['uid'=>UID,'fid'=>0]);
        //遍历父分类下的二级分类
        foreach ($category_parent as $category) {
            array_push($categorys,$category);
            $category_subs = select_db('user_categorys',['cid(id)','fid','name','font_icon'],['uid'=>UID,'fid'=>$category['id']]);
            $categorys = array_merge ($categorys,$category_subs);
        }
        msgA(['code'=>1,'msg'=>'获取成功','count'=>count($categorys),'data'=>$categorys ]);
    }
    
    //精简数据(链接列表调用)
    if($_GET['type'] === 'Simplify'){
        $categorys = [];
        //获取父分类
        $category_parent = select_db('user_categorys',['cid','fid','name','font_icon'],['uid'=>UID,'fid'=>0]);
        //遍历父分类下的二级分类
        foreach ($category_parent as $category) {
            array_push($categorys,$category);
            $category_subs = select_db('user_categorys',['cid','fid','name','font_icon'],['uid'=>UID,'fid'=>$category['cid']]);
            $categorys = array_merge ($categorys,$category_subs);
        }
        //数据处理
        $new_categorys=[];
        foreach ($categorys as $data){
            $new_categorys[$data['cid']]['name']=$data['name'];
            $new_categorys[$data['cid']]['font_icon']=$data['font_icon'];
            $new_categorys[$data['cid']]['fid'] = $data['fid'];
        }
        msgA(['code'=>1,'msg'=>'获取成功','count'=>count($new_categorys),'data'=>$new_categorys ]);
    }
    
    $datas = select_db('user_categorys',['cid','fid','pid(pwd_id)','status','property','name','add_time','up_time','weight','description','font_icon','icon'],$where);
    foreach ($datas as $key => $data){
        $datas[$key]['count'] = count_db('user_links',['uid'=>UID,'fid'=>$data['cid']]);
        if(!empty($datas[$key]['pwd_id'])){
            $datas[$key]['pwd'] = get_db('user_pwd_group','password',['uid'=>UID,'pid'=>$datas[$key]['pwd_id']]);
        }
    }
    msgA(['code'=>1,'msg'=>'获取成功','count'=>count($datas),'data'=>$datas ]);
}
//读一个分类信息
function read_one_category(){
    $cid = intval(trim($_REQUEST['cid']));
    //虚拟分类是指通过书签分享时产生的分类,主要配合前端编辑
    if(empty($cid)){msg(-1,$_REQUEST['cid'] == 'share'?'虚拟分类不能编辑':'id不能为空！');}
    $where['cid'] = $cid;
    $where['uid'] = UID;
    $category_info = get_db('user_categorys',['cid','fid','property','name','font_icon','description','icon'],$where);
    if(empty($category_info)){
        msgA(['code'=>-1,'msg'=>'没有找到分类信息','data'=>[]]);
    }else{
        msgA(['code'=>1,'data'=>$category_info]);
    }
}
//写分类列表(新增/修改/删除/排序) 
function write_category(){
    check_purview('category',2);
    //新增/修改时名称和图标不能为空
    if(in_array($_GET['type'],['add','edit'])){
        if(empty($_POST['name'])){
            msg(-1,'分类名称不能为空');
        }elseif(!preg_match('/^(fa fa-|layui-icon layui-icon-)([A-Za-z0-9]|-)+$/',$_POST['font_icon'])){
            $_POST['font_icon'] = 'fa fa-star-o';
            //msg(-1,'无效的分类图标');
        }
    }
    //新增
    if($_GET['type'] === 'add'){
        //分类名查重
        if(get_db('user_categorys','cid',['uid'=>UID ,"name" => $_POST['name']])){
            msg(-1,'分类名称已存在');
        }
        //父分类不能是二级分类
        if(intval($_POST['fid']) !=0 && get_db('user_categorys','fid',['uid'=>UID ,"cid" => intval($_POST['fid']) ]) !=0  ){
            msg(-1,'父分类不能是二级分类');
        }
        //加密组pid是否存在
        if(intval($_POST['pwd_id']) !=0  && empty(get_db('user_pwd_group','pid',['uid'=>UID ,"pid" => intval($_POST['pwd_id'])]))){
            msg(-1,'加密组不存在');
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
            'pid'=>intval($_POST['pwd_id']??'0'),
            'status'=>1,
            'property'=>intval($_POST['property']??'0'),
            'name'=>htmlspecialchars($_POST['name'],ENT_QUOTES),
            'add_time'=>time(),
            'up_time'=>time(),
            'weight'=>$cid,
            'description'=>htmlspecialchars($_POST['description'],ENT_QUOTES),
            'font_icon'=>$_POST['font_icon'],
            'icon'=>$_POST['icon']??''
            ],[1,'添加成功']
        );
        
    //修改
    }elseif($_GET['type'] === 'edit'){
        //父分类不能是自己
        if($_POST['cid'] == $_POST['fid']){
            msg(-1,'父分类不能是自己');
        }
        //查CID是否存在
        if(!get_db('user_categorys','cid',['uid'=>UID ,"cid" => intval($_POST['cid'])])){
            msg(-1,'分类不存在');
        }
        //分类名查重(排除自身)
        if(get_db('user_categorys','cid',['uid'=>UID,'cid[!]'=>intval($_POST['cid']),"name" => $_POST['name']])){
            msg(-1,'分类名称已存在');
        }
        //父分类不能是二级分类
        if(intval($_POST['fid']) !=0 && get_db('user_categorys','fid',['uid'=>UID ,"cid" => intval($_POST['fid']) ]) !=0  ){
            msg(-1,'父分类不能是二级分类');
        }
        //分类下存在子分类,禁止修改父分类
        if( $_POST['fid']!=0  && count_db('user_categorys',['uid'=>UID,'fid'=>$_POST['cid']])>0){
            msg(-1,'该分类下已存在子分类！');
        }
        //查父分类是否存在
        if( $_POST['fid'] !=0  && !get_db('user_categorys','cid',['uid'=>UID ,"cid" => intval($_POST['fid'])])){
            msg(-1,'父分类不存在');
        }
        //加密组pid是否存在
        if(intval($_POST['pwd_id']) !=0  && empty(get_db('user_pwd_group','pid',['uid'=>UID ,"pid" => intval($_POST['pwd_id'])]))){
            msg(-1,'加密组不存在');
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
            'pid'=>intval($_POST['pwd_id']??'0'),
            'property'=>intval($_POST['property']??'0'),
            'name'=>$_POST['name'],
            'up_time'=>time(),
            'description'=>$_POST['description'],
            'font_icon'=>$_POST['font_icon'],
            'icon'=>$_POST['icon']
            ];
        if(!isset($_POST['fid'])){ //为空时不修改父id,避免二级变一级
            unset($data['fid']);
        }    
        update_db('user_categorys',$data,['uid'=>UID ,"cid"=>intval($_POST['cid'])],[1,'更新成功']);
        
        
    //删除 判断有没链接和子分类
    }elseif($_GET['type'] === 'del'){
       //查CID是否存在
        if(empty($_POST['cid']) || !get_db('user_categorys','cid',['uid'=>UID ,"cid" => intval($_POST['cid'])])){
            msg(-1,'分类不存在');
        }

        //判断分类下是否存在子分类
        if(!empty(get_db('user_categorys','cid',['uid'=>UID ,"fid" => intval($_POST['cid'])]))){
            msg(-1,'该分类下已存在子分类,请先删除子分类!');
        }
        
        //判断分类下是否存在链接
        if(!empty(get_db('user_links','id',['uid'=>UID ,"fid" => intval($_POST['cid'])]))){
            msg(-1,'分类下存在链接,请先删除链接!');
        }
        
        //删除数据
        delete_db('user_categorys',['uid'=>UID ,"cid" => intval($_POST['cid'])],[1,'删除成功']);
        
    //排序
    }elseif($_GET['type'] === 'order' ){
        foreach ($_POST['data'] as $key ){
            update_db('user_categorys',['weight'=>$key[1]],['uid'=>UID,'cid'=>$key[0]]);
        }
        msg(1,'保存成功');
        
    //私有切换
    }elseif($_GET['type'] === 'property_sw' ){
        //查CID是否存在
        if(empty($_POST['cid']) || !get_db('user_categorys','cid',['uid'=>UID ,"cid" => intval($_POST['cid'])])){
            msg(-1,'分类不存在');
        }
        //更新数据
        update_db('user_categorys',['property'=>intval($_POST['property']) ],['uid'=>UID,'cid'=>intval($_POST['cid']) ],[1,'保存成功']);
        
    //状态切换
    }elseif($_GET['type'] === 'status_sw' ){
        //查CID是否存在
        if(empty($_POST['cid']) || !get_db('user_categorys','cid',['uid'=>UID ,"cid" => intval($_POST['cid'])])){
            msg(-1,'分类不存在');
        }
        //更新数据
        update_db('user_categorys',['status'=>intval($_POST['status']) ],['uid'=>UID,'cid'=>intval($_POST['cid']) ],[1,'保存成功']);
    }
    
    
    msg(-1,'操作类型错误');
}

//读链接列表
function read_link_list(){
    if($_GET['type'] == 'extend_list'){
        if($GLOBALS['global_config']['link_extend'] != 1 || !check_purview('link_extend',1)){
            msgA(['code'=>1,'msg'=>'无权限','count'=>0,'data'=>[]]);
        }
        $list = get_db("user_config","v",["k"=>"s_extend_list","uid"=>UID]);
        if(empty($list)){
            msgA(['code'=>1,'msg'=>'无数据','count'=>0,'data'=>[]]);
        }
        $list = unserialize($list);
        msgA(['code'=>1,'msg'=>'获取成功','count'=>count($list),'data'=>$list]);
    }
    $field = ['lid','fid','pid(pwd_id)','status','property','title','url','url_standby','weight','description','icon','click','add_time','up_time'];
    $query  = $_POST['query'];
    $fid    = intval(@$_POST['fid']); //获取分类ID
    $page   = empty(intval($_REQUEST['page'])) ? 1 : intval($_REQUEST['page']);
    $limit  = empty(intval($_REQUEST['limit'])) ? 50 : intval($_REQUEST['limit']);
    $offset = ($page - 1) * $limit; //起始行号
    $where = ["uid"=> UID];
    //分类筛选
    if(!empty($fid)){
        $where['AND']['fid'] = $fid;
        //查询分类下的子分类
        //$category = select_db('user_categorys','cid',['uid'=>UID,'fid'=>$fid]);
        // if(!empty($category)){
        //     array_push($category,$fid);
        //     $where['AND']['fid'] = $category;
        // }
    }
    //属性筛选
    if($_POST['property']==='0' || $_POST['property'] ==='1'){$where['AND']['property'] = ($_POST['property'] == 1?1:0);}
    if($_POST['status']==='0' || $_POST['status']==='1'){$where['AND']['status'] = ($_POST['status'] == 1?1:0);}
    
    //关键字筛选
    if(!empty($query)){
        $where['AND']['OR'] = ["title[~]" => $query,"url[~]" => $query,"description[~]" => $query];
    }
    
    //统计条数
    $count = count_db('user_links',$where);
    
    //前端指定排序方式,过滤字段名和方式
    if(!empty($_POST['order']) && !empty($_POST['field']) && in_array($_POST['field'],$field) && in_array($_POST['order'],['ASC','DESC'])){
        $where['ORDER'][$_POST['field']] = $_POST['order'];
    }else{
        //默认排序方式 权重排序(数字小的排前面)
        $where['ORDER']['weight'] = 'ASC';
        $where['ORDER']['lid'] = 'ASC';
    }
    
    //分页
    $where['LIMIT'] = [$offset,$limit];
    //查询
    $datas = select_db('user_links',$field,$where);

    msgA(['code'=>1,'msg'=>'获取成功','count'=>$count,'data'=>$datas]);
}
//读一条链接信息(根据链接id)
function read_one_link(){
    $lid = intval(trim($_REQUEST['lid']));
    if(empty($lid)){msg(-1,'id不能为空！');}
    $where['lid'] = $lid;
    $where['uid'] = UID;
    $link_info = get_db('user_links',['lid','fid','property','title','url','description'],$where);
    if(empty($link_info)){
        msgA(['code'=>-1,'msg'=>'没有找到链接信息','data'=>[]]);
    }else{
        msgA(['code'=>1,'data'=>$link_info]);
    }
    
}


//写链接列表 权重后期可以在页面添加个插入头部还是尾部(选项记录到浏览器)
function write_link(){
    check_purview('link',2);
    global $u;
    //添加链接
    if($_GET['type'] === 'add'){
        $fid = intval(@$_POST['fid']);
        $title = $_POST['title'];
        $url = $_POST['url'];
        $icon = empty($_POST['icon']) ? '' : $_POST['icon'];
        $description = empty($_POST['description']) ? '' : $_POST['description'];
        $keywords = empty($_POST['keywords']) ? '' : $_POST['keywords'];
        $property = empty($_POST['property']) ? 0 : 1;
        //检测链接是否合法
        check_link($fid,$title,$url,$_POST['url_standby']); 
        //检查链接是否已存在
        if(empty(get_db('user_config','v',['uid'=>UID ,'t'=>'config','k'=>'repeat_url'])) && has_db('user_links',['uid'=>UID ,"url" => $url])){
            msg(-1,'链接已存在!');
        }
        //描述长度检测
        $length_limit = unserialize(get_db("global_config","v",["k"=>"length_limit"]));
        if($length_limit['l_desc'] > 0 && strlen($description) > $length_limit['l_desc'] ){
            msg(-1,'描述长度不能大于'.$length_limit['l_desc'].'个字节');
        }
        //关键字长度检测
        if($length_limit['l_key'] > 0 && strlen($keywords) > $length_limit['l_key'] ){
            msg(-1,'关键字长度不能大于'.$length_limit['l_key'].'个字节');
        }
        //取最大链接ID
        $lid = get_maxid('link_id');
        //图标处理
        if(!empty($_POST['file'])){
            session_start();
            $tmp_path = $_SESSION['upload_images'][UID][$_POST['file']];
            if(!empty($tmp_path) && is_file($tmp_path)){
                $suffix = strtolower(end(explode('.',$tmp_path)));
                $path =  "./data/user/{$u}/favicon/{$lid}.{$suffix}";
                if(rename($tmp_path,$path)) { //移动文件到用户目录
                    $icon = $path;
                }else{
                    msg(-1,'保存图标失败,请检查权限');
                }
            }
        }

        $data = [
            'uid'           =>  UID,
            'lid'           =>  $lid,
            'fid'           =>  $fid,
            'pid'           =>  intval($_POST['pwd_id']??'0'),
            'title'         =>  htmlspecialchars($title,ENT_QUOTES),
            'url'           =>  $url,
            'url_standby'   =>  $_POST['url_standby']??'',
            'keywords'      =>  htmlspecialchars($keywords,ENT_QUOTES),
            'description'   =>  htmlspecialchars($description,ENT_QUOTES),
            'add_time'      =>  time(),
            'up_time'       =>  time(),
            'click'         =>  0,
            'weight'        =>  $lid,
            'status'        =>  1,
            'property'      =>  $property,
            'icon'          =>  $icon
            ];
        //扩展字段
        if($GLOBALS['global_config']['link_extend'] == 1 && check_purview('link_extend',1)){
            $list = get_db("user_config","v",["k"=>"s_extend_list","uid"=>UID]);
            if(!empty($list)){
                $list = unserialize($list); 
                $extend = [];
                foreach($list as $field){
                    $name = "_{$field['name']}";
                    if(isset($_POST[$name])){
                        $data['extend'][$name] = $_POST[$name];
                    }
                }
            }
        }
        //插入数据库
        insert_db('user_links',$data);
        //读取站点地图配置并判断是否需要更新
        $sitemap_config = unserialize( get_db("global_config", "v", ["k" => "sitemap_config"]));
        if(isset($sitemap_config['zhudong']) && $sitemap_config['zhudong'] == '1'){
            $sitemap_path = DIR . "/data/user/{$u}/sitemap.php";
            require DIR .'/system/expand/sitemap_create.php';
            if(is_Update_Sitemap($sitemap_config,$sitemap_path)){
                create_sitemap($sitemap_config,$sitemap_path,$u);
            }
        }
        
        msgA(['code'=>1,'msg'=>'添加成功','id'=>$lid]);
    //上传图标
    }elseif($_GET['type'] === 'upload_images'){
        //权限检测
        if(!check_purview('Upload_icon',1)){
            msg(-1,'您的用户组无权限上传图标');
        }elseif(empty($_FILES["file"]) || $_FILES["file"]["error"] > 0){
            msg(-1,'文件上传失败');
        }
        
        //取后缀并判断是否支持
        $suffix = strtolower(end(explode('.',$_FILES["file"]["name"])));
        if(!preg_match('/^(jpg|jpeg|png|ico|bmp|svg)$/',$suffix)){
            @unlink($_FILES["file"]["tmp_name"]);
            msg(-1,'文件格式不被支持!');
        }
        //限制文件大小
        if(filesize($_FILES["file"]["tmp_name"]) > 1 * 1024 * 1024){
            msg(-1,'文件大小超限');
        }
        session_start();
        $sid = $_POST['page_sid'];
        //添加链接
        if( !empty($sid) && $sid != 'undefined'){
            //适用在添加页上传图标未点击添加前又重新上传图标时删除老图标
            if(!empty($sid) && !empty($_SESSION['upload_images'][UID][$sid]) && is_file($_SESSION['upload_images'][UID][$sid])){
                @unlink($_SESSION['upload_images'][UID][$sid]);
            }

            //文件临时路径
            $temp_path = DIR . "/data/temp";
            //检测目录,不存在则创建!
            if(!Check_Path($temp_path)){
                msg(-1,'创建临时目录失败,请检查权限');
            }
            //移动文件到临时目录
            $tmp_name = UID . "_link_ico_" . uniqid() . ".{$suffix}"; //临时文件名
            if(!move_uploaded_file($_FILES["file"]["tmp_name"],"{$temp_path}/{$tmp_name}")) {
                msg(-1,'上传失败,请检查目录权限');
            }else{
                $_SESSION['upload_images'][UID][$sid] = "{$temp_path}/{$tmp_name}";
                msg(1,'上传成功');
            }
        //编辑链接
        }elseif(!empty($_POST['link_id'])){
            $link = get_db('user_links',['lid','icon'],['uid'=>UID ,'lid'=>$_POST['link_id'] ]);
            if(empty($link)){
                msg(-1,'链接ID不存在!');
            }
            
            //如果存在本地图标,则先删除
            if(!empty($link['icon']) && preg_match("/^\.\/data\/user\/{$u}\/favicon\//",$link['icon']) && is_file($link['icon'])){
                @unlink($link['icon']);
            }
            
            //文件路径
            $path = DIR . "/data/user/{$u}/favicon";
            //检测目录,不存在则创建!
            if(!Check_Path($path)){
                msg(-1,'创建目录失败,请检查权限');
            }
            //移动文件
            if(!move_uploaded_file($_FILES["file"]["tmp_name"],"{$path}/{$link['lid']}.{$suffix}")) {
                msg(-1,'上传失败,请检查目录权限');
            }else{
                //更新记录
                $icon = "./data/user/{$u}/favicon/{$link['lid']}.{$suffix}";
                update_db('user_links',['icon'=>$icon],['uid'=>UID,'lid'=>$_POST['link_id']]);
                msgA(['code'=>1,'msg'=>'上传成功','icon'=>$icon]);
            }
        }else{
            msg(-1,'参数错误');
        }
    //扩展上传图片
    }elseif($_GET['type'] == 'extend_up_img'){
        msg_tip();
            
    //删除图标
    }elseif($_GET['type'] === 'del_images'){
        session_start();
        $sid = $_POST['page_sid'];
        if(!empty($sid)){
            //适用在添加页上传图标未点击添加前又删除图标
            if(!empty($sid) && !empty($_SESSION['upload_images'][UID][$sid]) && is_file($_SESSION['upload_images'][UID][$sid])){
                @unlink($_SESSION['upload_images'][UID][$sid]);
                msg(1,'删除成功');
            }else{
                msg(-1,'您未上传图标');
            }
        }elseif(!empty($_POST['link_id'])){
            $link = get_db('user_links',['lid','icon'],['uid'=>UID ,'lid'=>$_POST['link_id'] ]);
            if(empty($link)){
                msg(-1,'链接ID不存在');
            }elseif(empty($link['icon'])){
                msg(-1,'链接未设置图标');
            }
            //删除图标(如果是本地图标则同时删除文件)
            if(preg_match("/^\.\/data\/user\/{$u}\/favicon\//",$link['icon']) && is_file($link['icon'])){
                if(!has_db('user_links',['uid'=>UID,'lid[!]'=>$link['lid'],'icon'=>$link['icon'] ])){ //判断是否共用
                    @unlink($link['icon']);
                }
            }
            //更新记录
            update_db('user_links',['icon'=>''],['uid'=>UID,'lid'=>$_POST['link_id']],[1,'删除成功']);
        }else{
            msg(-1,'参数错误');
        }
        
    //编辑
    }elseif($_GET['type'] === 'edit' || $_GET['type'] === 'edit2'){
        $lid = intval(@$_POST['lid']);
        $fid = intval(@$_POST['fid']);
        $title = $_POST['title'];
        $url = $_POST['url'];
        $icon =  $_POST['icon'];
        $keywords = empty($_POST['keywords']) ? '' : $_POST['keywords'];
        $description = empty($_POST['description']) ? '' : $_POST['description'];
        $property = empty($_POST['property']) ? 0 : 1;
        //检测链接是否合法
        check_link($fid,$title,$url,$_POST['url_standby']); 
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
        if(empty(get_db('user_config','v',['uid'=>UID ,'t'=>'config','k'=>'repeat_url'])) && has_db('user_links',['uid'=>UID ,'lid[!]'=>$lid, "url" => $url])){
             msg(-1,'链接已存在!');
        }
        //检查链接ID是否存在
        if(!has_db('user_links',['uid'=>UID ,'lid'=>$lid])){msg(-1,'链接ID不存在!');}

        $data = [
            'fid'           =>  $fid,
            'pid'           =>  intval($_POST['pwd_id']??'0'),
            'title'         =>  htmlspecialchars($title,ENT_QUOTES),
            'url'           =>  $url,
            'url_standby'   =>  $_POST['url_standby']??'',
            'keywords'      =>  htmlspecialchars($keywords,ENT_QUOTES),
            'description'   =>  htmlspecialchars($description,ENT_QUOTES),
            'up_time'       =>  time(),
            'property'      =>  $property,
            'icon'          =>  $icon
            ];
        
        //扩展字段
        if($GLOBALS['global_config']['link_extend'] == 1 && check_purview('link_extend',1)){
            $list = get_db("user_config","v",["k"=>"s_extend_list","uid"=>UID]);
            if(!empty($list)){
                $list = unserialize($list); 
                $extend = [];
                foreach($list as $field){
                    $name = "_{$field['name']}";
                    if(isset($_POST[$name])){
                        $data['extend'][$name] = $_POST[$name];
                    }
                }
            }
        }
        
        //非必须参数,未传递参数时
        if(isset($_POST['icon'])){
            //指定本地图标时检测是否存在
            if(preg_match("/^\.\/data\/user\/{$u}\/favicon\//",$icon)  && !is_file($icon)){
                msg(-1,'指定的本地图标不存在');
            }
            
            //适用原本地图标改成非本地图标时删除本地图标
            $link = get_db('user_links','icon',['uid'=>UID ,'lid'=>$lid]);
            if($icon != $link && !empty($link) && preg_match("/^\.\/data\/user\/{$u}\/favicon\//",$link) && is_file($link)){
                @unlink($link);
            }
        }else{
            unset($data['icon']);
        }

        if(!isset($_POST['pwd_id'])){
            unset($data['pid']);
        }
        if(!isset($_POST['keywords'])){
            unset($data['keywords']);
        }
        //更新数据
        update_db('user_links',$data,['uid'=>UID,'lid'=>$lid ]);
        msgA(['code'=>1,'msg'=>'修改成功','icon' => $icon]);
    //删除
    }elseif($_GET['type'] === 'del'){
        //查链接是否存在
        $link = get_db('user_links',['lid','icon'],['uid'=>UID ,'lid'=>intval($_POST['lid']) ]);
        if(empty($link)){
            msg(-1,'链接ID不存在!');
        }
            
        //如果存在本地图标,则先删除
        if(!empty($link['icon']) && preg_match("/^\.\/data\/user\/{$u}\/favicon\//",$link['icon']) && is_file($link['icon'])){
            if(!has_db('user_links',['uid'=>UID,'lid[!]'=>$link['lid'],'icon'=>$link['icon'] ])){ //判断是否共用
                @unlink($link['icon']);
            }
        }
        //删除数据
        delete_db('user_links',['uid'=>UID ,"lid" => intval($_POST['lid'])],[1,'删除成功']);
    
    //排序
    }elseif($_GET['type'] === 'order'){
        foreach ($_POST['data'] as $key ){
            update_db('user_links',['weight'=>$key[1]],['uid'=>UID,'lid'=>$key[0]]);
        }
        msg(1,'保存成功');
    //私有切换
    }elseif($_GET['type'] === 'property_sw' ){
        update_db('user_links',['property'=>intval($_POST['property']) ],['uid'=>UID,'lid'=>intval($_POST['lid']) ],[1,'保存成功']);
    
    //状态切换
    }elseif($_GET['type'] === 'status_sw' ){
        update_db('user_links',['status'=>intval($_POST['status']) ],['uid'=>UID,'lid'=>intval($_POST['lid']) ],[1,'保存成功']);
    
    
    //快速编辑
    }elseif($_GET['type'] === 'fast_edit' ){
        $lid = intval($_POST['lid']);
        $field = $_POST['field'];
        $value = $_POST['value'];
        if(empty($lid)){
            msg(-1,'链接不能为空');
        }elseif( $field ==='name' || $field ==='description' || $field ==='title' ){
            $t = time();
            update_db('user_links',[$field => $value,'up_time' =>$t],['uid'=>UID,'lid'=>intval($_POST['lid'])]);
            msgA(['code'=>1,'msg'=>'successful','t'=>$t]);
        }
        msg(-1,'参数错误,请尝试清理浏览器缓存!若不能解决请联系管理员!');
    
    
    //批量删除
    }elseif($_GET['type'] === 'batch_del'){
        //取链接图标,并遍历检测若未本地图标则一起删除
        $datas = select_db('user_links','icon',['uid'=>UID,'lid'=>json_decode($_POST['lid'])]);
        foreach ($datas as $icon ){
            if(!empty($icon) && preg_match("/^\.\/data\/user\/{$u}\/favicon\//",$icon) && is_file($icon)){
                @unlink($icon);
            }
        }
        delete_db('user_links',['uid'=>UID ,"lid" => json_decode($_POST['lid']) ],[1,'删除成功']);
    //批量设为私有
    }elseif($_GET['type'] === 'batch_private'){
        update_db('user_links',['property'=>1],['uid'=>UID ,"lid" => json_decode($_POST['lid']) ],[1,'设置成功']);
    //批量设为公开
    }elseif($_GET['type'] === 'batch_public'){
        update_db('user_links',['property'=>0],['uid'=>UID ,"lid" => json_decode($_POST['lid']) ],[1,'设置成功']);
    //批量设为启用
    }elseif($_GET['type'] === 'batch_start'){
        update_db('user_links',['status'=>1],['uid'=>UID ,"lid" => json_decode($_POST['lid']) ],[1,'设置成功']);
    //批量设为禁用
    }elseif($_GET['type'] === 'batch_disable'){
        update_db('user_links',['status'=>0],['uid'=>UID ,"lid" => json_decode($_POST['lid']) ],[1,'设置成功']);
    //批量修改分类
    }elseif($_GET['type'] === 'batch_category'){
        $fid = intval(@$_POST['fid']);
        if(empty($fid)){msg(-1,'分类ID错误');}
        //加一个查找分类是否存在
        update_db('user_links',['fid'=>$fid],['uid'=>UID ,"lid" => json_decode($_POST['lid']) ],[1,'设置成功']);
    //检测是否满足要求
    }elseif($_GET['type'] === 'msg_pull_check'){
        msg_tip();
    }elseif($_GET['type'] === 'msg_pull'){
        msg_tip();
    //图标拉取
    }elseif($_GET['type'] === 'icon_pull'){
        msg_tip();

    }elseif($_GET['type'] == 'extend_list'){
        msg_tip();
    }

    msg(-1,'操作类型错误');
}


//写安全设置
function write_security_setting(){ 
    global $USER_DB;
    if($USER_DB['Password'] !== Get_MD5_Password($_POST['Password'],$USER_DB['RegTime'])){
        msg(-1,'密码错误,请核对后再试！');
    }elseif( intval($_POST['Session']) > 0 && intval($_POST['KeyClear']) > intval($_POST['Session'])){
        msg(-1,'Key清理时间不能大于登录保持时间');
    }
    
    $datas = [
        'admin_inlet'=>['v'=>['display','hide','condition1'],'msg'=>'管理入口参数错误'],
        'Session'=>['int'=>true,'min'=>0,'max'=>360,'msg'=>'登录保持参数错误'],
        'HttpOnly'=>['int'=>true,'min'=>0,'max'=>1,'msg'=>'HttpOnly参数错误'],
        'KeySecurity'=>['int'=>true,'min'=>0,'max'=>2,'msg'=>'Key安全参数错误'],
        'KeyClear'=>['int'=>true,'min'=>1,'max'=>60,'msg'=>'Key清理参数错误'],
        'api_model'=>['v'=>['security','compatible'],'msg'=>'API模式参数错误'],
        'login_page'=>['v'=>['admin','index','auto'],'msg'=>'登录成功参数错误'],
        'Password2'=>['empty'=>true]
        ];
    $LoginConfig = unserialize($USER_DB['LoginConfig']);
    foreach ($datas as $key => $data){
        if($data['int']){
            $LoginConfig[$key] = ($_POST[$key] >= $data['min'] && $_POST[$key] <= $data['max'])?intval($_POST[$key]):msg(-1,$data['msg']);
        }elseif(isset($data['v'])){
            $LoginConfig[$key] = in_array($_POST[$key],$data['v']) ? $_POST[$key]:msg(-1,$data['msg']);
        }else{
            $LoginConfig[$key] = $data['empty']?$_POST[$key]:(!empty($_POST[$key])?$_POST[$key]:msg(-1,$data['msg']));
        }
    }
    $LoginConfig['Login'] = '0';

    if($_POST['Login'] == '1'){ //更换入口和保存安全配置
        $Login = Get_Exclusive_Login($USER_DB['User']);
        $USER_DB['Login'] = $Login;
        update_db("global_user", ["LoginConfig"=>$LoginConfig,"Login"=> $Login],["ID"=>UID]); 
    }else{ //不更换入口只保存安全配置
        update_db("global_user", ["LoginConfig" =>$LoginConfig], ["ID"=>UID]); 
    }
    $USER_DB['LoginConfig'] = serialize($LoginConfig);

    //删除所有登录信息并生成新的Key
    delete_db("user_login_info", ["uid"=>UID]);
    $Key = Set_key($USER_DB);
    
    //如果设置了二级密码
    if(!empty($LoginConfig['Password2'])){
        setcookie($USER_DB['User'].'_Password2', md5($USER_DB['Password'].$Key.$LoginConfig['Password2']), 0,'','',false,true);
    }
    msg(1,'保存成功');
}

//写收录配置
function write_apply(){ 
    msg_tip();
}
//读收录列表
function read_apply_list(){
    msg_tip();
}

//写站点设置
function write_site_setting(){ 
    //图标上传
    if(!empty($_FILES["file"])){
        msg_tip();
    }
    check_purview('site_info',2);
    if(!empty($_POST['custom_header']) && !check_purview('header',1)){
        msg(-1,'您所在的用户组无法使用自定义头部代码!');
    }
    if(!empty($_POST['custom_footer']) && !check_purview('footer',1)){
        msg(-1,'您所在的用户组无法使用自定义底部代码!');
    }
    $datas = [
        'title'=>['empty'=>false,'msg'=>'主标题不能为空'],
        'subtitle'=>['empty'=>true],
        'logo'=>['empty'=>true],
        'keywords'=>['empty'=>true],
        'description'=>['empty'=>true],
        'link_model'=>['v'=>['direct','Privacy','Privacy_js','Privacy_meta','301','302','Transition'],'msg'=>'链接模式参数错误'],
        'main_link_priority'=>['int'=>true,'min'=>0,'max'=>3,'msg'=>'主链优先参数错误'],
        'link_icon'=>['int'=>true,'min'=>0,'max'=>30,'msg'=>'链接图标参数错误'],
        'repeat_url'=>['int'=>true,'min'=>0,'max'=>1,'msg'=>'重复链接参数错误'],
        'site_icon'=>['empty'=>true],
        'top_link'=>['int'=>true,'min'=>0,'max'=>100,'msg'=>'热门链接参数错误'],
        'new_link'=>['int'=>true,'min'=>0,'max'=>100,'msg'=>'最新链接参数错误'],
        'max_link'=>['int'=>true,'min'=>0,'max'=>100,'msg'=>'输出上限参数错误'],
        'custom_header'=>['empty'=>true],
        'custom_footer'=>['empty'=>true]
        ];
    $s_site = unserialize(get_db('user_config','v',['uid'=>UID,'k'=>'s_site']));
    foreach ($datas as $key => $data){
        if($data['int']){
            $s_site[$key] = ($_POST[$key] >= $data['min'] && $_POST[$key] <= $data['max'])?intval($_POST[$key]):msg(-1,$data['msg']);
        }elseif(isset($data['v'])){
            $s_site[$key] = in_array($_POST[$key],$data['v']) ? $_POST[$key]:msg(-1,$data['msg']);
        }else{
            $s_site[$key] = $data['empty']?$_POST[$key]:(!empty($_POST[$key])?$_POST[$key]:msg(-1,$data['msg']));
        }
    }

    
    $site = unserialize(get_db('user_config','v',['uid'=>UID,'k'=>'s_site']));
    //留空时尝试删除图标
    if(empty($s_site['site_icon']) && !empty($site['site_icon_file']) && is_file($site['site_icon_file'])){
        @unlink($site['site_icon_file']);
        $s_site['site_icon_file'] = '';
    }
    write_user_config('repeat_url',$_POST['repeat_url'],'config','重复链接');
    update_db("user_config",["v"=>$s_site],["k"=>'s_site',"uid"=>UID],[1,'保存成功']);
}
//写过渡页配置
function write_transit_setting(){ 
    $datas = [
        'visitor_stay_time'=>['int'=>true,'min'=>0,'max'=>60,'msg'=>'访客停留时间范围0-60'],
        'admin_stay_time'=>['int'=>true,'min'=>0,'max'=>60,'msg'=>'管理员停留时间范围0-60'],
        'default_keywords'=>['int'=>true,'min'=>0,'max'=>1,'msg'=>'默认关键字参数错误']
        ];
    $s = Post_data_filter($datas);
    write_user_config('s_transition_page',$s,'config','过渡页配置');
    msg(1,"保存成功！");
}

//写验证页配置
function write_verify_page(){ 
    $datas = [
        'link_tip'=>['empty'=>true],
        'share_tip'=>['empty'=>true]
        ];
    $s = Post_data_filter($datas);
    write_user_config('s_verify_page',$s,'config','验证页配置');
    msg(1,"保存成功！");
}

//修改密码
function write_user_password(){
    global $USER_DB;
    $NewPassword = Get_MD5_Password($_POST['NewPassword'],$USER_DB['RegTime']);
    if($USER_DB['Password'] !== Get_MD5_Password($_POST['Password'],$USER_DB['RegTime'])){
        msg(-1102,'密码错误,请核对后再试！');
    }elseif(!empty($_POST['Password'])&&strlen($_POST['Password'])!=32){
        msg(-1103,'密码异常,正常情况是32位的md5！');
    }elseif($USER_DB['Password'] === $NewPassword ){
        msg(-1103,'新密码不能和原密码一样!');
    }
    
    //修改密码
    update_db("global_user", ["Password" => $NewPassword],["User" => $USER_DB['User']]);
    $USER_DB['Password'] = $NewPassword;
    //删除所有登录记录
    delete_db( "user_login_info", ["uid"=>UID] );
    Set_key($USER_DB);//生成新的Key
    msg(1,'修改成功');
}


//读双重验证
function read_totp(){
    global $USER_DB;
    if($USER_DB['Password'] !== Get_MD5_Password($_POST['Password'],$USER_DB['RegTime'])){
        msg(-1102,'密码错误,请核对后再试！');
    }
    $LoginConfig = unserialize($USER_DB['LoginConfig']);
    if(empty($LoginConfig['totp_key'])){
        require DIR . '/system/Authenticator.php';
        $totp = new PHPGangsta_GoogleAuthenticator();
        $key = $totp->createSecret();
        msgA(['code'=>2,'msg'=>'未开启双重验证','key'=> $key ]);
    }
    msgA(['code'=>1,'msg'=>'已开启双重验证']);
}

//写双重验证
function write_totp(){
    global $USER_DB;
    if($USER_DB['Password'] !== Get_MD5_Password($_POST['Password'],$USER_DB['RegTime'])){
        msg(-1102,'密码错误,请核对后再试！');
    }
    
    if($_GET['type'] === 'delete'){ //删除双重验证
        $LoginConfig = unserialize($USER_DB['LoginConfig']);
        if(empty($LoginConfig['totp_key'])){
            msgA(['code'=>-1,'msg'=>'未开启双重验证',]);
        }
        $LoginConfig['totp_key'] = '';
        update_db("global_user", ["LoginConfig"=>$LoginConfig],["ID"=>UID],[1,'操作成功']); 
    }elseif($_GET['type'] === 'set'){ //设置双重验证
        //必填项验证
        if(empty($_POST['key'])){
            msgA(['code'=>-1,'msg'=>'Key不能为空']);
        }elseif(empty($_POST['code'])){
            msgA(['code'=>-1,'msg'=>'验证码不能为空']);
        }
        $LoginConfig = unserialize($USER_DB['LoginConfig']);
        if(!empty($LoginConfig['totp_key'])){
            msgA(['code'=>-1,'msg'=>'已开启双重验证,无法继续开启!']);
        }
        //载入totp库
        require DIR . '/system/Authenticator.php';
        $totp = new PHPGangsta_GoogleAuthenticator();
        $checkResult = $totp->verifyCode($_POST['key'], $_POST['code'], 2);
        if(!$checkResult){
            msgA(['code'=>-1,'msg'=>'验证失败,请重试']);
        }
        //写入数据库
        $LoginConfig = unserialize($USER_DB['LoginConfig']);
        $LoginConfig['totp_key'] = $_POST['key'];
        update_db("global_user", ["LoginConfig"=>$LoginConfig],["ID"=>UID],[1,'操作成功']); 
    }else{
        msg(-1,'请求参数有误');
    }
}


//查Token
function read_token(){
    global $USER_DB;
    if($USER_DB['Password'] !== Get_MD5_Password($_POST['Password'],$USER_DB['RegTime'])){
        msg(-1102,'密码错误,请核对后再试！');
    }elseif(empty($USER_DB['Token'])){
        msg(-1,'您还未设置Token,如需使用请点击更换Token');
    }
    msgA(['code'=>1,'msg'=>'获取成功','Token'=> $USER_DB['Token'] ,'SecretKey'=>$USER_DB['SecretKey']]);
}

//更换或删除Token
function write_token(){
    global $USER_DB;
    if($USER_DB['Password'] !== Get_MD5_Password($_POST['Password'],$USER_DB['RegTime'])){
        msg(-1102,'密码错误,请核对后再试！');
    }elseif($_POST['type'] === 'delete' && empty($USER_DB['Token'])){
        msg(1,'您还未设置Token');
    }
    
    if($_POST['type'] === 'delete'){
        update_db("global_user", ["SecretKey"=>"","Token" => ""],["User" => $USER_DB['User']]);
        msgA(['code'=>1,'msg'=>'您的Token已清除','Token'=>'','SecretKey'=>'']);
    }elseif($_POST['type'] === 'replace'){
        $SecretKey = md5(uniqid(mt_rand(), true));
        $Token = md5(U.$SecretKey);
        if(!empty(get_db('global_user','User',["Token" => $Token]))){
            msg(-1,'系统错误,请重试');
        }
        update_db("global_user", ["SecretKey"=>$SecretKey,"Token" => $Token],["User" => $USER_DB['User']]);
        msgA(['code'=>1,'msg'=>'获取成功','Token'=>$Token,'SecretKey'=>$SecretKey]);
        msg(1,$Token);
    }else{
        msg(-1,'请求参数有误');
    }
}

//读加密分组列表
function read_pwd_group_list(){
    $page   = empty(intval($_REQUEST['page'])) ? 1 : intval($_REQUEST['page']);
    $limit  = empty(intval($_REQUEST['limit'])) ? 50 : intval($_REQUEST['limit']);
    $offset = ($page - 1) * $limit; //起始行号
    $where["uid"] = UID;

    //统计条数
    $count = count_db('user_pwd_group',$where);
    //权重排序(数字小的排前面)
    $where['ORDER']['pid'] = 'ASC';
    //分页
    $where['LIMIT'] = [$offset,$limit];
    //查询
    $datas = select_db('user_pwd_group',['pid','name','password','description'],$where);
    msgA(['code'=>1,'msg'=>'获取成功','count'=>$count,'data'=>$datas]);
}
//写加密分组 考虑要不要限制特殊字符
function write_pwd_group(){
    msg_tip();
}


//检测链接是否有效
function other_testing_link(){
    msg_tip();
}

//读主题信息
function read_theme(){
    global $global_config;
    global $USER_DB;
    // 检查权限
    if(!check_purview('theme_in',1)){
        msg(-1,'无权限');
    }
    
    $check_dirs = ['home','login','transit','register','guide','article','apply','verify','guestbook'];
    $request_dir = $_GET['dir'];
    if(in_array($request_dir,$check_dirs)){
        if(in_array($request_dir,['register','guide']) && $USER_DB['UserGroup'] != 'root' ){
            msg(-1,'管理此模板需管理员权限');
        }
    }else{
        msg(-1,'dir参数错误');
    }
    $son_dirs = get_dir_list(DIR.'/templates/'.$request_dir);
    
    foreach ($son_dirs as $son_dir) {
        $path = DIR.'/templates/'.$request_dir.'/'.$son_dir; //目录完整路径
        //没有信息文件则跳过
        if(!is_file($path.'/info.json') ) {continue;}
        //读取主题信息
        $themes[$son_dir] = json_decode(@file_get_contents($path.'/info.json'),true);
        //是否支持配置
        $themes[$son_dir]['config'] = is_file($path.'/config.php') ? '1':'0';
        //预览图优先顺序:png>jpg>info>default
        if(is_file($path.'/screenshot.jpg')){
            $themes[$son_dir]['screenshot'] = "./templates/$request_dir/$son_dir/screenshot.jpg";
        }elseif(is_file($path.'/screenshot.png')){
            $themes[$son_dir]['screenshot'] = "./templates/$request_dir/$son_dir/screenshot.png";
        }elseif(empty($themes[$son_dir]['screenshot'])){ 
            $themes[$son_dir]['screenshot'] = "./templates/admin/static/42ed3ef2c4a50f6d.png";
        }
    }
    
    function filter($arr){
        foreach($arr as $key => $data){
            $new[$key]['name'] = $data['name'] ?? 'null';
            $new[$key]['description']= $data['description'] ?? 'null';
            $new[$key]['homepage']= $data['homepage'] ?? 'null';
            $new[$key]['version']= $data['version'] ?? 'null';
            $new[$key]['update']= $data['update'] ?? 'null';
            $new[$key]['author']= $data['author'] ?? 'null';
            $new[$key]['screenshot']= $data['screenshot'] ?? 'null';
            $new[$key]['config'] = $data['config'] ?? '0';
            $new[$key]['state'] = $data['up'] == 1 ? 'up' : (empty($data['dir']) ? 'local' : 'dw');
        }
        return $new;
    }

    //在线主题处理
    if ( !$global_config['offline'] && $USER_DB['UserGroup'] === 'root'){ 
        $sysver = get_SysVer();
        //读取缓存
        $page = 'theme_'.$request_dir;
        $template = get_db('global_config','v',['k'=>$page.'_cache']);
        if(!empty($template)){
            $data = json_decode($template, true);
        }
        
        //没有缓存 或 禁止缓存 或 缓存过时
        if(empty($template) ||   $_GET['cache'] === 'no'  || time() -  $data["time"] > 1800 ){ 
            $urls = ["gitee" => "http://tznb.gitee.io/twonav_resource/{$request_dir}_template.json"];
            $Source = $global_config['Update_Source'] ?? '';
            if (!empty($Source) && isset($urls[$Source])) {
                $urls = [$Source => $urls[$Source]];
            }
        }else{
            $cache = true;
        }
        //读取超时参数
        $overtime = !isset($global_config['Update_Overtime']) ? 3 : ($global_config['Update_Overtime'] < 3 || $global_config['Update_Overtime'] > 60 ? 3 : $global_config['Update_Overtime']);
        //远程获取
        foreach($urls as $key => $url){
            $Res = ccurl($url,$overtime);
            $data = json_decode($Res["content"], true);
            if($data["code"] == 200 ){ //如果获取成功
                $data["time"] = time(); //记录当前时间
                write_global_config($page.'_cache',json_encode($data),$request_dir.'_模板缓存');
                break; //跳出循环.
            } 
        }
        //解析
        foreach($data["data"] as $key){
            $path = DIR.'/templates/'.$request_dir.'/'.$key["dir"];
            if( is_dir($path) ) {  //本地存在
                $value = $key["dir"];
                //检查是否可以更新
                $update = str_replace('/','',$themes[$value]['update']); //本地主题版本
                $update_new = str_replace('/','',$key["update"]); //远程主题版本
                if( $sysver >= intval($key["low"])  && $sysver <= intval($key["high"]) &&  $update < $update_new ){
                    $themes[$value]['up'] = '1';
                }
            }else{
                //判断是否适配当前系统版本
                if( $sysver >= intval($key["low"])  && $sysver <= intval($key["high"]) ){
                    $value = $key["dir"];
                    $themes[$value] = json_decode(json_encode($key),true);
                }
            }
        }
    }
    
    //取正在使用的模板
    if(in_array($request_dir,['register','guide'])){
        $global_templates = unserialize(get_db("global_config",'v', ["k" => "s_templates"]));
        $current[$request_dir] = $global_templates[$request_dir] ?? 'default';
    }else{
        $s_templates = unserialize(get_db("user_config", "v", ["uid"=>UID,"k"=>"s_templates"]));
        if($request_dir == 'home'){
            $current['home_pad'] = $s_templates['home_pad'] ?? 'default';
            $current['home_pc'] = $s_templates['home_pc'] ?? 'default';
        }else{
            $current[$request_dir] = $s_templates[$request_dir] ?? 'default';
        }
    }

    
    $themes = filter($themes);
    msgA(['code'=>1,'data'=>$themes,'current'=>$current,'referrer'=>($data['referrer'] ?? '')]);
}
function msg_tip(){
    if(is_subscribe()){
        msg(-1,'请前往概要页面更新系统,未提示更新则尝试刷新页面<br />更新后即可解锁全部功能,如有疑问请联系客服');
    }
    msg(-1,'免费版不支持此功能<br /> <a href="https://gitee.com/tznb/TwoNav/wikis/pages?sort_id=7968669&doc_id=3767990" target="_blank" style="color: #1e9fff;">点击此处前往购买页面</a>');
}

//主题下载/更新/删除
function write_theme(){
    global $global_config;
    $fn = $_POST['fn'];if($_GET['type'] != 'config' && !in_array($fn,['home','login','transit','register','guide','article','verify','guestbook','apply'])){msg(-1,'fn参数错误');}
    if($_GET['type'] == 'download'){
        msg_tip();
    //删除主题
    }elseif($_GET['type'] == 'del'){
        is_root();
        $name = $_POST['dir'];
        if ( !preg_match("/^[a-zA-Z0-9_-]{1,64}$/",$name) ) { 
            msg(-1,"主题名称不合法！");
        }elseif( $name === 'default') { 
            msg(-1,"默认主题不允许删除！");
        }elseif(!is_dir(DIR."/templates/$fn/".$name)){
            msg(-1,"主题不存在！");
        }
        deldir(DIR."/templates/$fn/".$name);
        if( is_dir(DIR."/templates/$fn/".$name) ) {
            msg(-1,"删除失败，可能是权限不足！");
        }else{
            msg(1,"主题已删除！");
        }
    //使用主题
    }elseif($_GET['type'] == 'set'){
        $type = $_POST['type'];
        $name = $_POST['name'];
        //如果是注册模板则必须是root权限
        if($fn == 'register' || $fn == 'guide'){is_root();}
        //相关检测
        if ( !preg_match("/^[a-zA-Z0-9_-]{1,64}$/",$name) ) { 
            msg(-1,"主题名称不合法！");
        }elseif(!is_dir(DIR."/templates/$fn/".$name)){
            msg(-1,'主题不存在');
        }elseif(!check_purview('theme_in',1)){
            msg(-1,'无权限');
        }
        
        //读取用户模板配置
        require DIR."/system/templates.php";
        if($fn == 'register' || $fn == 'guide'){
            $global_templates[$fn] = $name;
            update_db('global_config',['v'=>$global_templates],['k'=>'s_templates'],[1,'操作成功']);
        }
        
        //判断设置的类型
        if($fn == 'home'){
            if( $type == 'PC/Pad'){
                $s_templates['home_pc'] = $name;
                $s_templates['home_pad'] = $name;
            }elseif($type == 'PC'){
                $s_templates['home_pc'] = $name;
            }elseif($type == 'Pad'){
                $s_templates['home_pad'] = $name;
            }else{
                msg(-1,'参数错误');
            }
        }else{
            $s_templates[$fn] = $name;
            if($fn == 'transit'){
                $site = unserialize(get_db('user_config','v',['uid'=>UID,'k'=>'s_site']));
                if($site['link_model'] != 'Transition'){
                    $site['link_model'] = 'Transition';
                    update_db("user_config",["v"=>$site],["k"=>'s_site',"uid"=>UID]);
                    $msg = ',已同步链接模式为过渡页面';
                }
            }
        }
        //更新数据
        update_db('user_config',['v'=>$s_templates],['uid'=>UID,'k'=>'s_templates'],[1,"设置成功{$msg}"]);
        
    //配置主题信息
    }elseif($_GET['type'] == 'config'){
        if(!check_purview('theme_set',1)){
            msg(-1,"无权限！");
        }
        if(empty($_POST)){
            msg(-1,"POST请求数据不能为空！");
        }
        //20230420,修复同名窜数据的问题!由于保存主题不提交模板类型,只能从来路中提取
        parse_str(parse_url($_SERVER['HTTP_REFERER'])['query'],$GET);
        if(empty($GET['fn']) && empty($_GET['template_type']) ){
            msg(-1,"获取模板类型错误");
        }
        $fn = empty($GET['fn']) ? $_GET['template_type'] : $GET['fn'];
        if(!in_array($fn,['home','login','transit','register','guide','article','verify','guestbook','apply'])){
            msg(-1,"参数错误");
        }
        //0420 END
        if(in_array($fn,['guide','register'])){
            write_global_config("theme_{$fn}_{$_GET['t']}",$_POST,'主题配置');
        }else{
            write_user_config($_GET['t'],$_POST,'theme_' . $fn,'主题配置');
        }
        msg(1,"保存成功！");
    }
}


//读登录信息
function read_login_info(){
    $page   = empty(intval($_REQUEST['page'])) ? 1 : intval($_REQUEST['page']);
    $limit  = empty(intval($_REQUEST['limit'])) ? 50 : intval($_REQUEST['limit']);
    $offset = ($page - 1) * $limit; //起始行号
    $where["uid"] = UID;
    //$where["cookie_key[!]"] = md5($_COOKIE[U.'_key']); //不显示当前设备
    //统计条数
    $count = count_db('user_login_info',$where);
    //权重排序(数字小的排前面)
    $where['ORDER']['id'] = 'DESC';
    //分页
    $where['LIMIT'] = [$offset,$limit];
    //查询
    $datas = select_db('user_login_info',['id','ip','ua','login_time','last_time','expire_time'],$where);
    //获取当前登录ID,用于前端标记
    $where["cookie_key"] = md5($_COOKIE[U.'_key']); 
    $current_id = get_db('user_login_info','id',$where);
    msgA(['code'=>1,'msg'=>'获取成功','count'=>$count,'data'=>$datas,'current_id'=>$current_id]);
}

//写登录信息
function write_login_info(){
    if($_GET['type'] == 'out'){
        delete_db('user_login_info',['uid'=>UID,'id'=>intval($_POST['id'])],[1,'删除成功']);
    }
}

//写分享
function write_share(){
    //规划添加和编辑共享
    if($_GET['type'] == 'save'){
        if($_POST['type'] == '1'){
            $data = $_POST['category_data'];
            $type = 1;
        }elseif($_POST['type'] == '2'){
            $data = $_POST['link_data'];
            $type = 2;
        }else{
            msg(-1,'类型参数错误');
        }
        
        if(empty($_POST['name'])){
            msg(-1,'分类名称不能为空');
        }elseif(empty($data)){
            msg(-1,'分享内容不能为空');
        }
        //到期时间检测
        if( !empty($_POST['expire']) &&  !preg_match('/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$/i',$_POST['expire'])){
            msg(-1,'到期时间格式错误');
        }
        $current_time = time();
        $d = [
            'uid'  => UID,
            'type' => $type,
            'name' => $_POST['name'],
            'pwd'  => $_POST['pwd'],
            'data' => $data,
            'pv'   => empty($_POST['pv']) ? 0 : 1,
            'expire_time'    => empty($_POST['expire'])?9999999999:strtotime($_POST['expire']),
            'description' => $_POST['description'],
            'up_time'   => $current_time
            ];
        if(empty($_POST['sid'])){
            //添加
            $d['add_time'] = $current_time;
            $d['views'] = 0;
            $d['sid'] = hash("crc32b",uniqid(Get_Rand_Str()));
            insert_db('user_share',$d,[1,'操作成功']);
        }else{
            //修改
            update_db('user_share',$d,['uid'=>UID,'sid'=>$_POST['sid']],[1,'操作成功']);
        }
        msg(-1,'1111');
        
    //删除
    }elseif($_GET['type'] == 'del'){
        delete_db('user_share',['uid'=>UID ,"sid" => json_decode($_POST['sid']) ],[1,'删除成功']);
    }
}

//读分享
function read_share(){
    //分类列表
    if($_GET['type'] == 'categorys'){
        $where['uid'] = UID;
        $where['fid'] = 0;
        $where['ORDER']['weight'] = 'ASC';
        $where['name[~]'] = $_GET['keyword'];
        $categorys = [];
        $category_parent = select_db('user_categorys',['cid(id)','fid','name'],$where);
        foreach ($category_parent as $category) {
            $where['fid'] = $category['id'];
            array_push($categorys,$category);
            $category_subs = select_db('user_categorys',['cid(id)','fid','name'],$where);
            $categorys = array_merge ($categorys,$category_subs);
        }
        msgA(['code'=>1,'msg'=>'获取成功','count'=>count($categorys),'data'=>$categorys ]);
    
    //链接列表
    }elseif($_GET['type'] == 'links'){
        $query  = $_GET['keyword'];
        $page   = empty(intval($_GET['page'])) ? 1 : intval($_GET['page']);
        $limit  = empty(intval($_GET['limit'])) ? 50 : intval($_GET['limit']);
        $offset = ($page - 1) * $limit; //起始行号
        $where = [];
        $where = ["uid"=> UID];
    
        //关键字筛选
        if(!empty($query)){
            $where['AND']['OR'] = ["title[~]" => $query,"url[~]" => $query,"description[~]" => $query];
        }
        //var_dump([$offset,$limit],$where);
        $count = count_db('user_links',$where);
        $where['ORDER']['lid'] = 'DESC';
        $where['ORDER']['weight'] = 'ASC';
        $where['LIMIT'] = [$offset,$limit];
        $datas = select_db('user_links',["lid(id)","title(name)"],$where);
        //var_dump($db->last());
        msgA(['code'=>1,'msg'=>'获取成功','count'=>$count,'data'=>$datas]);
    
    //分享列表
    }elseif($_GET['type'] == 'share_list'){
        $keyword  = $_POST['keyword'];
        $status = intval(@$_POST['status']); //状态
        $type   = intval(@$_POST['type']); //类型
        $page   = empty(intval($_REQUEST['page'])) ? 1 : intval($_REQUEST['page']);
        $limit  = empty(intval($_REQUEST['limit'])) ? 50 : intval($_REQUEST['limit']);
        $offset = ($page - 1) * $limit; //起始行号
        $where = ["uid"=> UID];

        if($status == 1){
            $where['AND']["expire_time[>]"] = time();
        }elseif($status == 2){
            $where['AND']['expire_time[<]'] = time();
        }
        
        if($type == 1){
            $where['AND']['type'] = 1;
        }elseif($type == 2){
            $where['AND']['type'] = 2;
        }
        
        //关键字筛选
        if(!empty($keyword)){
            $where['AND']['OR']["sid"] = $keyword;
            $where['AND']['OR']["name[~]"] = $keyword;
            $where['AND']['OR']["description[~]"] = $keyword;
        }
        //var_dump($where);
        $count = count_db('user_share',$where);
        $where['ORDER']['id'] = 'DESC';
        $where['LIMIT'] = [$offset,$limit];
        $datas = select_db('user_share','*',$where);
        msgA(['code'=>1,'msg'=>'获取成功','count'=>$count,'data'=>$datas]);
    }
}

//导出数据
function read_data_control(){
    require DIR . '/system/UseFew/export_data.php';
    exit;
}
//链接导入/清空
function write_data_control(){
    require DIR . '/system/UseFew/Import_data.php';
    exit;
}

//读数据(后台首页调用读取实时统计)
function read_data(){
    global $USER_DB;
    //指定类型限制仅root账号可用!
    if($USER_DB['UserGroup'] != 'root' && in_array( $_GET['type'],['diagnostic_log','connectivity_test','phpinfo'])){
        msg(-1,'无权限');
    }
    
    //概要数据统计
    if($_GET['type'] == 'home'){
        $category_count = count_db('user_categorys',['uid'=>UID])??0;
        $link_count = count_db('user_links',['uid'=>UID])??0;
        $index_count = get_db('user_count','v',['uid'=>UID,'k'=>date('Ym'),'t'=>'index_Ym'])??0;
        $click_count = get_db('user_count','v',['uid'=>UID,'k'=>date('Ym'),'t'=>'click_Ym'])??0;
        msgA( ['code'=>1,'data'=>[$category_count,$link_count,$index_count,$click_count] ]);
    //连通测试
    }elseif($_GET['type'] == 'connectivity_test'){
        if($GLOBALS['global_config']['offline'] == '1'){
            msg(1,'您已开启离线模式,无法使用该功能!');
        }
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $_POST['url']);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        $start = microtime(true);
        $response = curl_exec($ch);
        $end = microtime(true);
        $time = round(($end - $start) * 1000, 2);
        if(curl_errno($ch)) {
            $log .= "请求发生错误：".curl_error($ch);
        } else {
            $log .= "响应内容：".$response ?? 'Null' ;
            $log .= ",访问耗时：{$time} 毫秒。" ;
        }
        curl_close($ch);
        msg(1,$log);
    //一键诊断
    }elseif($_GET['type'] == 'diagnostic_log'){
        clearstatcache(); //清除缓存
        $log='';
        $log .= "服务器时间：" . date("Y-m-d H:i:s") ."\n"; 
        $log .= "系统信息：" . php_uname('s').','.php_uname('r') ."\n";
        $log .= "当前版本：" . SysVer . "\n";
        if(!empty(get_cfg_var('docker_ver'))){
            $log .= "Docker镜像版本: " .get_cfg_var('docker_ver')."\n";
        }
        //检查PHP版本
        $php_version = floatval(PHP_VERSION);
        $log .= "PHP版本：{$php_version}\n";
        $log .= "Web版本：{$_SERVER['SERVER_SOFTWARE']}\n";
        if( $php_version < 7.3  ) {
            $log .= "PHP版本：不满足要求,要求不低于7.3\n";
        }
        //获取加载的模块
        $ext = get_loaded_extensions(); 
        global $db_config,$db;
        $log .= "数据储存：{$db_config['type']}\n";
        
        if($db_config['type'] == 'sqlite'){
            $log .= "SQLite：".(is_writable($db_config['path'])?'数据库读写正常':'数据库只读(请将权限设为755)')."\n";
        }elseif($db_config['type'] == 'mysql'){
            $log .= "MySQL：".$db->info ()['version']."\n";
        }elseif($db_config['type'] == 'mariadb'){
            $log .= "MariaDB：".$db->info ()['version']."\n";
        }
        
        $path = './data/test_'.time().'.txt';
        if(file_put_contents($path, '测试文本,可以删除!由一键诊断生成!')){
            if(unlink($path)){
                $log .= "data目录：正常\n";
            }else{
                $log .= "data目录：创建文件成功,删除文件失败\n";
            }
        }else{
            $log .= "data目录：异常,请检查权限!\n";
        }
        if(function_exists("opcache_reset")){
            $log .=  "opcache: 存在\n";
        }
        if(!class_exists('SQLite3')){
            $log .=  "SQLite3: 不支持\n";
        }
        $log .= "脚本权限:" . get_current_user()."/".substr(sprintf("%o",fileperms("index.php")),-4)."\n";
        
        $log .= "PHP配置: " . 
            (ini_get('file_uploads') == 1 ? '允许上传':'禁止上传文件') .
            " ,最大文件(upload_max_filesize) > " . ini_get('upload_max_filesize').
            " ,POST数据(post_max_size) > " . ini_get('post_max_size'). 
            " ,内存限制(memory_limit) > " . ini_get('memory_limit').
            " ,执行超时(max_execution_time) > " . ini_get('max_execution_time').
            "\n";
        
        $log .= in_array("pdo_sqlite",$ext) ? "" : "PDO_Sqlite：不支持 (导入db3)\n";
        $log .= in_array("curl",$ext) ? "" : "curl：不支持 (链接识别/在线更新/主题下载/订阅等)\n";
        $log .= in_array("mbstring",$ext) ? "" : "mbstring：不支持 (链接识别)\n";
        $log .= in_array("Phar",$ext) ? "" : "Phar：不支持 (在线更新/主题下载)\n";
        $log .= in_array("hash",$ext) ? "" : "hash：不支持 (书签分享/生成注册码)\n";
        $log .= in_array("session",$ext) ? "" : "session：不支持 (影响较大)\n";
        $log .= in_array("intl",$ext) ? "" : "intl：不支持 (使用中文域名时可能会导致异常)\n";
        $log .= $GLOBALS['global_config']['offline'] == '1' ? "离线模式：已开启,会导致无法更新系统/下载主题模板等\n" : "";
        $log .= "可用模块：".implode("&#12288;",$ext)."\n";
        $updatadb_logs = select_db('updatadb_logs','file_name',['file_name[!]'=>'install.sql']);
        $log .= "数据库更新记录:".(empty($updatadb_logs)?'无':"\n".implode("\n",$updatadb_logs))."\n";
        msg(1,$log);
    //输出phpinfo信息
    }elseif($_GET['type'] == 'phpinfo'){
        session_start();
        if($_SESSION['phpinfo_id'] != $_GET['pid']){
            exit('验证失败,请刷新页面后重试!');
        }elseif(Get_MD5_Password($_GET["p"],$GLOBALS['USER_DB']["RegTime"]) === $GLOBALS['USER_DB']["Password"]){
            $_COOKIE = [];
            $_SERVER['HTTP_COOKIE'] = 'privacy';
            phpinfo();
        }else{
            exit('密码验证失败,请重试!');
        }
    //报表统计
    }elseif($_GET['type'] == 'echarts'){
        $days = isset($_GET['date']) && !empty($_GET['date']) ? $_GET['date'] : 7;
        $dates = [];
        for ($i = 0; $i < $days; $i++) {
            $date = date('Ymd', strtotime("-$i days"));
            $dates[] = $date;
        }
        $dates = array_reverse($dates);
        $day_data = [];
        array_push($day_data, ['name' => '访问量', 'type' => 'line', 'data' => []]);
        array_push($day_data, ['name' => '点击量', 'type' => 'line', 'data' => []]);
        array_push($day_data, ['name' => 'IP数', 'type' => 'line', 'data' => []]);
        foreach ($dates as $date) {
            array_push($day_data[0]['data'], get_db('user_count', 'v', ['uid' => UID, 'k' => $date, 't' => 'index_Ymd']) ?? 0);
            array_push($day_data[1]['data'], get_db('user_count', 'v', ['uid' => UID, 'k' => $date, 't' => 'click_Ymd']) ?? 0);
            array_push($day_data[2]['data'], get_db('user_count', 'v', ['uid' => UID, 'k' => $date, 't' => 'ip_count']) ?? 0);
        }
        
        $data = ['dates'=>$dates,'day_data'=>$day_data];
        msgA(['code'=>1,'data'=>$data]);
    }elseif($_GET['type'] == 'tongji_ip_list'){
        $days = isset($_GET['date']) && !empty($_GET['date']) ? $_GET['date'] : 7;
        $dates = [];
        for ($i = 0; $i < $days; $i++) {
            $date = date('Ymd', strtotime("-$i days"));
            $dates[] = $date;
        }
        $dates = array_reverse($dates);
        $day_data = [];
        foreach ($dates as $date) {
            $list = select_db('user_count','e',['uid'=>UID,'k'=>$date,'t'=>'access_ip']);
            $day_data[$date] = empty($list) ? [] : $list ;
        }
        msgA(['code'=>1,'data'=>$day_data]);
    }elseif($_GET['type'] == 'menu'){
        global $global_config;
        $menu = [];
        if(check_purview('site_info',1)){
            array_push($menu,['title'=>'站点设置','href'=>'SiteSetting','icon'=>'fa fa-cog']);
        }
        if(check_purview('theme_in',1)){
            array_push($menu,['title'=>'主题管理','href'=>'theme','icon'=>'fa fa-magic']);
        }
        array_push($menu,
            ['title'=>'分类管理','href'=>'category_list','icon'=>'fa fa-list-ul'],
            ['title'=>'加密管理','href'=>'pwd_group','icon'=>'fa fa-lock'],
            ['title'=>'链接管理','icon'=>'fa fa-folder-open-o','href'=>'','child'=>
              [
                ['title'=>'链接列表','href'=>'link_list','icon'=>'fa fa-link'],
                ['title'=>'添加链接','href'=>'link_add','icon'=>'fa fa-plus-square-o'],
                ['title'=>'书签分享','href'=>'share','icon'=>'fa fa-external-link'],
                ['title'=>'导出导入','href'=>'data_control','icon'=>'fa fa-retweet'],
              ]
            ]);
        
        //扩展功能
        $extend = [];
        //if($global_config['apply'] == 1 && check_purview('apply',1)){
            array_push($extend,['title'=>'收录管理','href'=>'expand/apply-admin','icon'=>'fa fa-pencil']);
        //}
        //if($global_config['guestbook'] == 1 && check_purview('guestbook',1)){ 
            array_push($extend,['title'=>'留言管理','href'=>'expand/guestbook-admin','icon'=>'fa fa-commenting-o']);
        //}
        //if($global_config['article'] > 0 && check_purview('article',1)){ 
            array_push($extend,['title'=>'文章管理','href'=>'expand/article-list','icon'=>'fa fa-file-text-o']);
        //}
        if(!empty($extend)){
            $extend = ['title'=>'扩展功能','icon'=>'fa fa-folder-open-o','href'=>'','child'=> $extend];
            array_push($menu,$extend);
        }
        
        //如果是管理员则追加菜单
        if($USER_DB['UserGroup'] == 'root'){
            array_push($menu,
            ['title'=>'网站管理','icon'=>'fa fa-wrench','href'=>'','child'=>
              [
                ['title'=>'系统设置','href'=>'root/sys_setting','icon'=>'fa fa-gears'],
                ['title'=>'授权管理','href'=>'root/vip','icon'=>'fa fa-diamond'],
                ['title'=>'用户管理','href'=>'root/user_control','icon'=>'fa fa-user'],
                ['title'=>'用户分组','href'=>'root/users_control','icon'=>'fa fa-users'],
                ['title'=>'注册管理','href'=>'root/reg_control','icon'=>'fa fa-user-plus'],
                ['title'=>'站长工具','href'=>'root/tool','icon'=>'fa fa-exclamation-triangle'],
              ]
            ]);
        }
        $logo_url = static_link ? get_surl('{UUID}.html'):"./?u={$USER_DB['User']}";
        $init = array( 'homeInfo'=>['title'=>'概要','href'=>'home'],'logoInfo'=>['title'=>'TwoNav','image'=>'./templates/admin/img/logo.png','href'=>$logo_url],'menuInfo'=>$menu);
        msgA($init);
    }
}

//备份/回滚/删除等
function other_local_backup(){
    require DIR . '/system/UseFew/local_backup.php';
    exit;
}
//读文章
function read_article(){
    require DIR . '/system/api_article.php';
    exit;
}
//写文章
function write_article(){
    require DIR . '/system/api_article.php';
    exit;
}
//百度推送
function other_baidu_push(){
    msg(-1,'未检测到有效授权,无法使用该功能');
}
//获取链接信息
function other_get_link_info(){
    global $global_config;
    if ( $global_config['offline'] == '1'){ msg(-1,"离线模式无法使用此功能"); }
    $url = @$_POST['url']; //获取URL
    //检查链接是否合法
    if( empty($url) ) {
        msg(-1010,'URL不能为空!');
    }elseif(!preg_match("/^(http:\/\/|https:\/\/).*/",$url)){
        msg(-1010,'只支持识别http/https协议的链接!');
    }
    $url = process_url_idn($url);
    //获取网站标题 (HTML/JS跳转无法识别)
    $c = curl_init(); 
    curl_setopt($c, CURLOPT_URL, $url); 
    curl_setopt($c, CURLOPT_RETURNTRANSFER, 1); 
    curl_setopt($c, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($c, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($c, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/95.0.4638.69 Safari/537.36');
    curl_setopt($c, CURLOPT_FOLLOWLOCATION, 1); //允许重定向,解决http跳转到https无法识别
    curl_setopt($c , CURLOPT_TIMEOUT, 5); //设置超时时间
    $data = curl_exec($c); 
    curl_close($c); 

    require (DIR .'/system/get_page_info.php');
    $info = get_page_info($data);
    $link['title'] =  $info['site_title'];
    $link['keywords'] = $info['site_keywords'];
    $link['description'] = $info['site_description'];
    msgA(['code'=>1,'data'=>$link]);
}

//POST数据过滤
function Post_data_filter($datas){
    foreach ($datas as $key => $data){
        if($data['int']){
            $s[$key] = ($_POST[$key] >= $data['min'] && $_POST[$key] <= $data['max'])?intval($_POST[$key]):msg(-1,$data['msg']);
        }elseif(isset($data['v'])){
            $s[$key] = in_array($_POST[$key],$data['v']) ? $_POST[$key]:msg(-1,$data['msg']);
        }else{
            $s[$key] = $data['empty']?$_POST[$key]:(!empty($_POST[$key])?$_POST[$key]:msg(-1,$data['msg']));
        }
    }
    return $s;
}