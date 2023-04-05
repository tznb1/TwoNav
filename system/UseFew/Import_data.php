<?php 
if(!defined('DIR')){
    header('HTTP/1.1 404 Not Found');
    header("status: 404 Not Found");
    exit;
}
session_start();
$sid = $_POST['page_sid'];
if($_GET['type'] == 'upload'){
    if(empty($sid) || $sid == 'undefined'){
        msg(1,'page_sid error');
    }
    
    $temp_path = $_SESSION['upload_bookmark'][UID][$sid];
    if(!empty($sid) && !empty($temp_path) && is_file($temp_path)){
        @unlink($temp_path);
    }

    //取后缀并判断是否支持
    $suffix = strtolower(end(explode('.',$_FILES["file"]["name"])));
    if(!preg_match('/^(db3|html)$/i',$suffix)){
        @unlink($_FILES["file"]["tmp_name"]);
        msg(-1,'文件格式不被支持!');
    }
    //限制文件大小
    if(filesize($_FILES["file"]["tmp_name"]) > 10 * 1024 * 1024){
        msg(-1,'文件大小超限');
    }
    //文件临时路径
    $temp_path = DIR . "/data/temp";
    //检测目录,不存在则创建!
    if(!Check_Path($temp_path)){
        msg(-1,'创建临时目录失败,请检查权限');
    }
    //移动文件到临时目录
    $tmp_name = UID . "_upload_bookmark_" . uniqid() . ".{$suffix}"; //临时文件名
    if(!move_uploaded_file($_FILES["file"]["tmp_name"],"{$temp_path}/{$tmp_name}")) {
        msg(-1,'上传失败,请检查目录权限');
    }else{
        $_SESSION['upload_bookmark'][UID][$sid] = "{$temp_path}/{$tmp_name}";
        msg(1,'上传成功'."{$temp_path}/{$tmp_name}");
    }
}elseif($_GET['type'] == 'html'){
    $temp_path = $_SESSION['upload_bookmark'][UID][$sid];
    if(empty($temp_path) || !is_file($temp_path)){
        msg(-1,'文件不存在,请重新上传');
    }
    try{
        $content = file_get_contents($temp_path);
    }catch(Exception $e) {
        msg(-1,'读入文件失败');
    }
    
    $property = empty($_POST['property']) ? 0 : 1; //私有属性
    $all = $_POST['all'] == '1' ? true:false; //保留属性(db3)
    $AutoClass = $_POST['AutoClass']; //自动分类(HTML)
    if($AutoClass != 1 && empty($_POST['fid'])){
        msg(-1,'请先选择默认分类在开始导入!');
    }
    //如果默认分类为空,则读取默认分类(不存在则创建)
    if(empty($_POST['fid'])){
        $fid = get_db('user_categorys','cid',['uid'=>UID,'name'=>'默认分类']);
        if(empty($fid)){
            insert_db('user_categorys',[
                'uid'=>UID,
                'cid'=>get_maxid('category_id'),
                'fid'=>0,
                'pid'=>0,
                'status'=>1,
                'property'=>$property,
                'name'=>'默认分类',
                'add_time'=>time(),
                'up_time'=>time(),
                'weight'=>0,
                'description'=>'',
                'font_icon'=>'fa fa-folder',
                'icon'=>''
                ]);
            $fid = get_db('user_categorys','cid',['uid'=>UID,'name'=>'默认分类']); 
            if(empty($fid)){
                msg(-1,'创建默认分类失败');
            }
        }
    }else{
        $fid = intval($_POST['fid']); 
    }
    //默认分类
    $default_category = get_db('user_categorys','name',['uid'=>UID,'cid'=>$fid]);
    if(empty($default_category)){
        msg(-1,'获取分类名失败!'.$fid);
    }
        
    $data = []; //链接组
    $categorys = []; //分类信息组(遍历时)
    $categoryt = []; //分类信息表
    $fcategorys = []; //上级分类
    $Hierarchy = 0; //层级
    $currenttime = time(); //当前时间
    
    $HTMLs = explode("\n",$content); //按行分割
    //遍历html
    foreach( $HTMLs as $HTMLh ){
        if( $_POST['AutoClass'] == 1 && preg_match("/<DT><H3.*>(.*)<\/H3>/i",$HTMLh,$category) ){
            //匹配到文件夹名时加入数组
            $Hierarchy ++;
            $category[1] = empty($category[1]) ? $default_category : $category[1]; //如果为空则用默认分类(浏览器允许空)
            array_push($categoryt,$category[1]);
            array_push($categorys,$category[1]);
            //层级3等于是二级分类,记录父子关系;
            if($Hierarchy == 3){
                $fcategorys[$category[1]] = $categorys[$Hierarchy - 2];
            }
        }elseif( preg_match('/<DT><A HREF="(http.+)" ADD_DATE="(\d*)".*>(.+)<\/A>/i',$HTMLh,$urls) ){
            //匹配标准格式 1.链接 2.添加时间 3.标题
            $datat['category']  = $categorys[count($categorys) -1];
            $datat['category']  = empty($datat['category']) ? $default_category : $datat['category'] ;
            $datat['ADD_DATE']  = $urls[2];
            $datat['title']     = $urls[3];
            $datat['url']       = $urls[1];
            $datat['html']   = $HTMLh;
            array_push($data,$datat);
        }elseif( preg_match('/<DT><A HREF="(http.+)">(.+)<\/A>/i',$HTMLh,$urls) ){
            //匹配精简格式 1.链接 2.标题
            $datat['category']  = $categorys[count($categorys) -1];
            $datat['category']  = empty($datat['category']) ? $default_category : $datat['category'] ;
            $datat['title']     = $urls[2];
            $datat['url']       = $urls[1];
            $datat['html']   = $HTMLh;
            array_push($data,$datat);
        }elseif( $_POST['AutoClass'] == 1 && preg_match('/<\/DL><p>/i',$HTMLh) ){
            //匹配到文件夹结束标记时删除一个
            $Hierarchy --;
            array_pop($categorys);
        }
    }
    //遍历结束,分类名去重!
    $categoryt = array_unique($categoryt);
    //var_dump($categoryt);var_dump($fcategorys);var_dump($data);exit;
    
    //创建分类
    $fids = [];
    foreach( $categoryt as $name ){
        //读取分类ID
        $id = get_db('user_categorys','cid',['uid'=>UID,'name'=>$name]); 
        //如果为空则创建
        if(empty($id)){
            insert_db('user_categorys',[
                'uid'=>UID,
                'cid'=>get_maxid('category_id'),
                'fid'=>0,
                'pid'=>0,
                'status'=>1,
                'property'=>$property,
                'name'=>$name,
                'add_time'=>$currenttime,
                'up_time'=>$currenttime,
                'weight'=>0,
                'description'=>'',
                'font_icon'=>'fa fa-folder',
                'icon'=>''
                ]
            );
            $id = get_db('user_categorys','cid',['uid'=>UID,'name'=>$name]); 
            if(empty($id)){
                msg(-1,'意外结束:创建或读取分类信息失败!');
            }
        }
        $fids[$name] = $id;//名称为key,值为id
    }
    $fids[$default_category] = $fid; //加入默认分类
    //var_dump($fcategorys);exit;
    
    //二级分类处理
    if($_POST['AutoClass'] == 1 && $_POST['2Class'] == 1){
        foreach( $fcategorys as $name3 => $name2 ){
            //读取父分类信息,确定它是父分类,而不是子分类
            if(get_db('user_categorys','fid',['uid'=>UID,'name'=>$name2]) == 0){
                update_db('user_categorys',['fid'=>$fids[$name2]],['uid'=>UID,'cid' => $fids[$name3]]); //更新二级分类的父id
            }
        }
    }
    // 遍历导入链接
    $fail = 0; $success = 0; $iconcount = 0;$time = $currenttime;
    //$data = array_reverse($data); //数组倒序(这样导入后链接的顺序和浏览器一样)
    //表头
    $res='<table class="layui-table" lay-even><colgroup><col width="200"><col width="250"><col></colgroup><thead><tr><th>标题</th><th>URL</th><th>失败原因</th></tr></thead><tbody>';
    foreach( $data as $link ){
        //检查链接xss
        if(check_xss($link['url'])){
            $fail++;
            $res=$res.'<tr><td>'.mb_substr(htmlspecialchars($link['title'],ENT_QUOTES), 0, 30).'</td><td>'.mb_substr(htmlspecialchars($link['url'],ENT_QUOTES), 0, 30).'</td><td>URL存在非法字符</td></tr>';
            continue;
        }
        //检查标题xss
        if(check_xss($link['title'])){
            $fail++;
            $res=$res.'<tr><td>'.mb_substr(htmlspecialchars($link['title'],ENT_QUOTES), 0, 30).'</td><td>'.mb_substr(htmlspecialchars($link['url'],ENT_QUOTES), 0, 30).'</td><td>标题存在非法字符</td></tr>';
            continue;
        }
        // 检测链接是否合法
        if( !filter_var($link['url'], FILTER_VALIDATE_URL) ) {
            $fail++;
            $res=$res.'<tr><td>'.mb_substr(htmlspecialchars($link['title'],ENT_QUOTES), 0, 30).'</td><td>'.mb_substr(htmlspecialchars($link['url'],ENT_QUOTES), 0, 30).'</td><td>链接无效,只支持识别http/https协议的链接!</td></tr>';
            continue;
        }
        // 如果书签时间不合理则使用当前时间!
        if( $_POST['ADD_DATE'] == 1 ){
            $time = intval($link['ADD_DATE']);
            if( $time > $currenttime || $currenttime < 788889600){
                $time = $currenttime;
            }
        }
        //匹配图片 data:image/png;base64,iVBORw0KGgoAAAANSUhEU
        $base64_img = '';
        if ($_POST['icon'] == 1 && preg_match('/ICON="(data:image\/png;base64,(\S+))"/', $link['html'], $result)){
            $len = GetFileSize($result[2],'b');//取图标大小
            if($len > 128 && $len < 2048){
                $iconcount++;
                $base64_img = $result[1];
            }
        }
        //判断是否存在
        $id = get_db('user_links','id',['uid'=>UID,'url'=>$link['url'] ]);
        if(empty($id)){
            insert_db('user_links',[
                'uid'       =>  UID,
                'lid'       =>  get_maxid('link_id'),
                'fid'       =>  $fids[$link['category']],
                'add_time'  =>  $time,
                'up_time'   =>  $time,
                'weight'    =>  0,
                'title'     =>  $link['title'] ,
                'url'       =>  $link['url'],
                'property'  =>  $property,
                'icon'      =>  $base64_img,
                'status'    =>  1
            ]);
            $success++;
        }else{
            $res=$res.'<tr><td>'.mb_substr($link['title'], 0, 30).'</td><td>'.mb_substr($link['url'], 0, 40).'</td><td>URL重复'.'</td></tr>';
            $fail++;
        }
    }
    $data = [
        'code'      =>  1,
        'msg'       =>  '总数：'.count($data).' 成功：'.$success.' 失败：'.$fail.( $_POST['icon'] == 1 ? ' 图标：'.$iconcount:''),
        'res'       =>  $res.'</tbody></table>',
        'fail'      =>  $fail
        ];
    //删除文件和变量
    unlink($temp_path);
    unset($_SESSION['upload_bookmark'][UID][$sid]);
    msgA($data);
}elseif($_GET['type'] == 'db3'){
    if(empty($_POST['source'])){
        msg(-1,'请选择数据来源');
    }
    $tempnam = $_SESSION['upload_bookmark'][UID][$sid];
    //载入数据库
    try {
        $temp_db = new Medoo\Medoo(['type'=>'sqlite','database'=>$tempnam ]);
    }catch (Exception $e) {
        unset($_SESSION['upload_bookmark'][UID][$sid]);
        unlink ($tempnam);
        msg(-1,'载入数据库失败'); 
    }
    $attr = !empty($_POST['attr']); //保留属性
    $currenttime = time();
    //遍历数据库
    $res='<table class="layui-table" lay-even><colgroup><col width="200"><col width="250"><col></colgroup><thead><tr><th>标题</th><th>URL</th><th>失败原因</th></tr></thead><tbody>';
    try{
        //处理分类
        $fids = [];
        $categorys = select_data($temp_db,'on_categorys','*','');
        !empty($categorys) or msg(-1,'未找到分类数据');
        foreach ($categorys as $category) {
            //查找本地同名分类
            $local_id =  get_db('user_categorys','cid',['uid'=>UID,'name'=>$category['name']]);
            //没找到则创建
            if(empty($local_id)){
                //处理图标
                if($_POST['source'] == 1 || $_POST['source'] ==3){
                    $font_icon = strstr($category['font_icon'],'fa fa') ? $category['font_icon'] : 'fa fa-folder' ;
                }elseif($_POST['source'] == 2){
                    if(strstr($category['Icon'],'fa')){
                        $font_icon = 'fa '.$category['Icon'];
                    }else{
                        $font_icon = 'fa fa-folder';
                    }
                }
                $local_id = get_maxid('category_id');
                insert_db('user_categorys',[
                    'uid'=>UID,
                    'cid'=>$local_id,
                    'fid'=>0,
                    'pid'=>0,
                    'status'=>1,
                    'property'=>$category['property'] ?? 0,
                    'name'=>$category['name'],
                    'add_time'=>$attr ? $category['add_time'] : $currenttime,
                    'up_time'=>$attr ? $category['up_time'] ?? $currenttime : $currenttime,
                    'weight'=>0,
                    'description'=>$link['description']??'',
                    'font_icon'=>$font_icon,
                    'icon'=>''
                    ]
                );
            }
            //缓存旧分类ID所对应的新分类ID
            $fids['id-'.$category['id']] = $local_id;
        }
        
        //在遍历一次,处理二级分类
        foreach ($categorys as $category) {
            //如果fid为空或为零则跳过
            if(empty($category['fid'])) continue; 
            //更新分类的父id
            update_db('user_categorys',['fid'=>$fids['id-'.$category['fid']]],['uid'=>UID,'cid'=>$fids['id-'.$category['id']]]);
        }

        //处理链接
        $links = select_data($temp_db,'on_links','*','');
        !empty($links) or msg(-1,'未找到链接数据');
        $total = count($links); $fail = 0; $success = 0;
        //遍历链接
        foreach($links as $link){
            //判断父分类是否存在
            if(empty($fids['id-'.$link['fid']])){
                $res=$res.'<tr><td>'.mb_substr($link['title'], 0, 30).'</td><td>'.mb_substr($link['url'], 0, 40).'</td><td>父分类不存在'.'</td></tr>';
                $fail++;
                continue; 
            }
            //判断是URL否存在
            $id = get_db('user_links','id',['uid'=>UID,'url'=>$link['url'] ]);
            if(empty($id)){
                //备用链接处理
                $url_standby = '';
                if($_POST['source'] == 1 || $_POST['source'] ==2){ //OneNav / Extend
                    if(!empty($link['url_standby'])){
                        $url_standby = [$link['url_standby']]; //转为数组
                    }
                }elseif($_POST['source'] == 3){ //TwoNav
                    $url_standby = $link['url_standby'];
                }
                
                insert_db('user_links',[
                    'uid'       =>  UID,
                    'lid'       =>  get_maxid('link_id'),
                    'fid'       =>  $fids['id-'.$link['fid']],
                    'add_time'  =>  $attr ? $link['add_time'] : $currenttime,
                    'up_time'   =>  $attr ? $link['up_time'] ?? $currenttime: $currenttime,
                    'weight'    =>  0,
                    'click'     =>  $attr ? $link['click']??0 : 0,
                    'title'     =>  $link['title']??'',
                'description'   =>  $link['description']??'',
                    'url'       =>  $link['url'],
                'url_standby'   =>  $url_standby,
                    'property'  =>  $link['property']??0,
                    'icon'      =>  ''
                ]);
                $success++;
            }else{
                $res=$res.'<tr><td>'.mb_substr($link['title'], 0, 30).'</td><td>'.mb_substr($link['url'], 0, 40).'</td><td>URL重复'.'</td></tr>';
                $fail++;
            }
        }
        $data = [
            'code'  =>  1,
            'msg'   =>  '总数：'.$total.' 成功：'.$success.' 失败：'.$fail,
            'res'   =>  $res.'</tbody></table>',
            'fail'  =>  $fail
            ];
        //删除文件和变量
        unset($_SESSION['upload_bookmark'][UID][$sid]);
        unlink($tempnam);
        msgA($data);
    }catch (Exception $e) {
        unset($_SESSION['upload_bookmark'][UID][$sid]);
        unlink ($tempnam);
        if(Debug){
            msgA(['code'=>-1,'msg'=>'数据库操作失败','Message'=>$e->getMessage(),'debug'=>debug_backtrace()]);
        }else{
            Amsg(-1,'数据库操作失败');
        }
    }
    msg(-1,'导入失败.');
}elseif($_GET['type'] == 'data_empty'){
    //验证密码
    global $USER_DB;
    if(Get_MD5_Password($_POST['pwd'],$USER_DB["RegTime"]) != $USER_DB["Password"]){
        msg(-1,'密码错误');
    }
    //数据库清除
    if(!empty($_POST['TABLE'])){
        $TABLE = ["user_categorys","user_links","user_pwd_group","user_share","user_apply"];
        foreach($_POST['TABLE'] as $key =>$value){
            if(in_array($key,$TABLE)){
                delete_db($key,['uid'=>UID]);
            }
        }
        //重置链接ID
        if($_POST['TABLE']['user_links'] == 'on'){
            update_db('user_config',['v'=>1],['uid'=>UID,"k" =>'link_id']);
        }
        //重置分类ID
        if($_POST['TABLE']['user_categorys'] == 'on'){
            update_db('user_config',['v'=>1],['uid'=>UID,"k" =>'category_id']);
        }
        //重置加密组ID
        if($_POST['TABLE']['user_pwd_group'] == 'on'){
            update_db('user_config',['v'=>1],['uid'=>UID,"k" =>'pwd_group_id']);
        }
        //重置收录ID
        if($_POST['TABLE']['user_apply'] == 'on'){
            update_db('user_config',['v'=>1],['uid'=>UID,"k" =>'apply_id']);
        }
    }
    
    //文件删除
    if(!empty($_POST['FILE'])){
        $FILE = ["MessageBoard","favicon"];
        foreach($_POST['FILE'] as $key =>$value){
            $path = DIR.'/data/user/'.U.'/'.$key;
            if(in_array($key,$FILE) && is_dir($path)){
                deldir($path);
            }
        }
    }
    msg(1,'操作成功,请刷新页面!');
}


function select_data($db,$table,$columns,$where){
    try {
        $re = $db->select($table,$columns,$where);
        return $re;
    }catch (Exception $e) {
        if(Debug){
            msgA(['code'=>-1,'msg'=>'查询数据库失败','Message'=>$e->getMessage(),'debug'=>debug_backtrace()]);
        }else{
            Amsg(-1,'查询数据库失败');
        }
    }
}