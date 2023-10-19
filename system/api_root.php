<?php if(!defined('DIR')){header('HTTP/1.1 404 Not Found');header("status: 404 Not Found");exit;}

//权限核查
if($USER_DB['UserGroup'] != 'root'){
    msg(-1,'无权限');
}

//系统更新
function other_upsys(){
    session_start();
    if($_POST['i'] == 0){
        unset($_SESSION['upsys']);
        $_SESSION['upsys']['step'] = 0;
        msgA(['code'=>1,'msg'=>'获取成功','info'=>[
            '检测系统环境',
            '下载更新包',
            '释放更新包',
            '更新数据库']]);
    }
    //检查环境
    if($_POST['i'] == 1){
        clearstatcache();

        //获取版本日期
        if(!preg_match('/^v.+-(\d{8})$/i',SysVer,$matches)){
            msg(-1,"获取程序版本异常");
        }
        if (!is_dir('./data/temp')) mkdir('./data/temp',0755,true) or msg(-1,'下载失败,创建临时[/data/temp]目录失败');
        //检查指定文件夹是否可写
        $paths = ["./","./data","./data/temp","./static","./system","./templates"];
        foreach($paths as $path){
            if(!is_writable($path)){
                msg(-1,"文件夹不可写 >> $path");
            }
        }
        //检查授权状态
        if(!is_subscribe('bool')){
            msg(-1,'未检测到有效授权,请
            <a href="https://gitee.com/tznb/TwoNav/wikis/pages?sort_id=7968669&doc_id=3767990" target="_blank" style="color: #01AAED;">购买授权</a>
            或
            <a href="https://gitee.com/tznb/TwoNav/wikis/pages?sort_id=8013447&doc_id=3767990" target="_blank" style="color: #01AAED;">手动更新</a>');
        }
        $subscribe = unserialize(get_db('global_config','v',["k" => "s_subscribe"]));
        if(!isset($subscribe['public']) || empty($subscribe['public'])){
            msg(-1,'
            错误原因: 未检测到授权秘钥<br />如何处理: <br /> 
            &nbsp;&nbsp; 1. 转到<a href="./index.php?c=admin&u='.U.'#root/vip" target="_blank" style="color: #01AAED;">授权管理</a>页面点击保存设置<br />
            &nbsp;&nbsp; 2. 提示保存成功后在尝试更新');
        }
        $_SESSION['upsys']['sysver'] = intval($matches[1]);
        usleep(1000*300); //延迟300毫秒
        msg(1,'success');
    }
    //下载更新包
    if($_POST['i'] == 2){
        //设置执行最长时间，0为无限制。单位秒!
        set_time_limit(5*60);
        $overtime = !isset($GLOBALS['global_config']['Update_Overtime']) ? 3 : ($GLOBALS['global_config']['Update_Overtime'] < 3 || $GLOBALS['global_config']['Update_Overtime'] > 60 ? 3 : $GLOBALS['global_config']['Update_Overtime']);
        
        //请求获取更新包
        $node = intval($GLOBALS['global_config']['Update_Source']);
        $Res = ccurl("http://service.twonav.cn/service.php",30,true,data_encryption('updateSystem',['node'=>$node]));
        $data = json_decode($Res["content"], true);
        
        if($data["code"] != '200'){
            msg(-1,$data['msg'] ?? '获取更新信息失败,请稍后再试..');
        }
        
        $file = "System_Upgrade.tar.gz";
        $filePath = "./data/temp/{$file}";
        
        //下载升级包
        if(downFile($data['url'],$file,'./data/temp/')){
            $file_md5 = md5_file($filePath);
            if($file_md5 != $data['md5']){
                unlink($filePath);
                msg(-1,'更新包校验失败,请重试或联系客服');
            }
        }else{
            msg(-1,'下载更新包失败');
        }
        msg(1,'success');
    }
    
    //释放更新包
    if($_POST['i'] == 3){
        //设置超时时间
        set_time_limit(5*60);
        //释放更新包
        try {
            $filePath = "./data/temp/System_Upgrade.tar.gz";
            $phar = new PharData($filePath);
            $phar->extractTo('./', null, true); //路径 要解压的文件 是否覆盖
            unlink($filePath); //删除文件
            if(function_exists("opcache_reset")){
                opcache_reset(); //清理PHP缓存
            }
        } catch (Exception $e) {
            msg(-1,'释放更新包,请检查写入权限');//解压出问题了
        }
        usleep(1000*300);
        msg(1,'success');
    }
    
    //检测是否需要更新数据库
    if($_POST['i'] == 4){
        clean_cache();
        set_time_limit(5*60);
        try {
            //根据数据库类型扫描不同目录,并声明执行SQL语句的函数
            if($GLOBALS['db_config']['type'] == 'mysql' || $GLOBALS['db_config']['type'] == 'mariadb'){
                $dir = './system/MySQL';
                function exe_sql($content) {
                    global $db;
                    try {
                        $result = $db->query($content)->fetchAll();
                        return true;
                    }catch (Exception $e) {
                        return false;
                    }
                }
            }elseif($GLOBALS['db_config']['type'] == 'sqlite'){
                $dir = './system/SQLite';
                class MyDB extends SQLite3 {
                    function __construct() {
                        $this->open(DIR."/data/".$GLOBALS['db_config']['file']);
                    } 
                } 
                function exe_sql($content) {
                    try {
                        $MyDB = new MyDB();
                        if(!$MyDB) {
                            msg(-1,'打开SQLite3数据库失败:'.$MyDB->lastErrorMsg());
                        }
                    } catch(Exception $e){
                        msg(-1,"MyDB初始化失败");
                    }
                    
                    $result = $MyDB->exec($content);
                    $MyDB->close();
                    if(!$result) {
                        msg(-1,'执行SQL语句失败:'.$MyDB->lastErrorMsg());
                    }else{
                        return true;
                    }
                }
            }
            //扫描文件
            $file_list = glob("{$dir}/*.php");
            foreach ($file_list as $filePath){
                $file_name = basename($filePath); //取文件名
                //查找数据库是否已安装更新
                if(empty(get_db('updatadb_logs','*',['file_name'=>$file_name]))){
                    require $filePath; //载入升级脚本
                    //脚本规范:头部判断是否有DIR常量来避免被直接访问,中间执行升级脚本!底部将执行记录写入数据库!
                    //insert_db('updatadb_logs',['file_name'=>$file_name,'update_time'=>time(),'status'=>'TRUE','extra'=>'']);
                } 
            }
        } catch (Exception $e) {
            msg(-1,"执行数据库更新失败,建议回滚!");
        }
        if($_POST['pattern'] == 'manual'){
            $updatadb_logs = select_db('updatadb_logs','file_name',['file_name[!]'=>'install.sql']);
            $msg .= "当前版本：" . SysVer . "\n";
            $msg .= "数据储存：{$GLOBALS['db_config']['type']}\n";
            //$msg .= "脚本列表:".(empty($file_list)?'无': "\n".implode("\n",$file_list))."\n" ;
            $msg .= "更新记录:".(empty($updatadb_logs)?'无':"\n".implode("\n",$updatadb_logs))."\n";
            msg(1,$msg);
        }else{
            usleep(1000*300); //延迟300毫秒
            msg(1,'success');
        }

    }
    
    msgA(['code'=>-1,'msg'=>'步骤错误']);
}


//读用户列表
function read_user_list(){
    $query  = $_POST['query'];
    $UserGroup  = @$_POST['UserGroup'];
    $page   = empty(intval($_REQUEST['page'])) ? 1 : intval($_REQUEST['page']);
    $limit  = empty(intval($_REQUEST['limit'])) ? 50 : intval($_REQUEST['limit']);
    $offset = ($page - 1) * $limit; //起始行号
    $where['AND']['User[!]'] = U;//不查询自身
    
    //用户组筛选
    if(!empty($UserGroup)){
        $where['AND']['UserGroup'] = $UserGroup;
    }

    //关键字筛选
    if(!empty($query)){
        $where['AND']['OR'] = ["User[~]" => $query,"Email[~]" => $query,"RegIP[~]" => $query];
    }
    
    //统计条数
    $count = count_db('global_user',$where);
    //权重排序(数字小的排前面)
    $where['ORDER']['RegTime'] = 'DESC';
    //分页
    $where['LIMIT'] = [$offset,$limit];
    //查询
    $datas = select_db('global_user',['ID','User','UserGroup','Email','RegIP','RegTime','Login'],$where);
    if(!empty($datas)){
       $user_group = select_db('user_group',['name','code'],'');//读用户组
       $user_group = array_column($user_group, 'name', 'code');//以代号为键
       $user_group['root'] = '站长';
       $user_group['default'] = '默认';
       foreach ($datas as $key => $data){
           $datas[$key]['UserGroupName'] = $user_group[$data['UserGroup']]??'Null';
       }
    }
    //返回
    msgA(['code'=>1,'msg'=>'获取成功','count'=>$count,'data'=>$datas]);
}

//读权限列表
function read_purview_list(){
    $query  = $_GET['keyword'];
    $where = [];
    //关键字筛选
    if(!empty($query)){
        $where['OR'] = ["code[~]" => $query,"name[~]" => $query,"desc[~]" => $query];
    }
    
    //统计条数
    $count = count_db('purview_list',$where);
    //查询
    $datas = select_db('purview_list','*',$where);
    //返回
    msgA(['code'=>1,'msg'=>'获取成功','count'=>$count,'data'=>$datas]);
}

//读用户组列表
function read_users_list(){
    msg_tip();
}

//写用户组
function write_users(){
    //验证代号是否合规
    msg_tip();
}


//写用户信息
function write_user_info(){
   msg_tip();
}

//读注册码列表
function read_regcode_list(){
    msg_tip();
}

//写注册码
function write_regcode(){
    msg_tip();
}

// 写系统设置
function write_sys_settings(){
    global $USER_DB;
    if($_POST['Login'] == $_POST['Register']){
        msg(-1,'注册入口名不能和登录入口名相同');
    }elseif(!preg_match("/^[a-zA-Z0-9]+$/",$_POST['Register'])){ 
        msg(-1,'注册入口错误,仅允许使用字母和数字');
    }elseif(!preg_match("/^[a-zA-Z0-9]+$/",$_POST['Login'])){ 
        msg(-1,'登陆入口错误,仅允许使用字母和数字');
    }elseif(empty($_POST['Default_User']) || !get_db("global_user", "User", [ "User"=>$_POST['Default_User'] ]) ){
        msg(-1,'默认账号不存在');
    }elseif(!empty($_POST['default_UserGroup']) && empty(get_db('user_group','code',['code' => $_POST['default_UserGroup']]))){
        msg(-1,'默认分组代号不存在');
    }elseif($_POST['Sub_domain'] == 1){
        if(preg_match('/\.(com|net|org|gov|edu)\.cn$/', $_SERVER["HTTP_HOST"])){
            msg(-1,'不支持此类域名');
        }
        if(filter_var($_SERVER["HTTP_HOST"], FILTER_VALIDATE_IP) != false){
            msg(-1,'不支持IP访问开启二级域名');
        }
        if(preg_match('/\.(\d+|:\d+)$/', preg_replace('/:\d+$/','',$_SERVER['HTTP_HOST'])) || substr_count($_SERVER["HTTP_HOST"],':') > 2){
            msg(-1,'不支持IP访问开启二级域名,如有误判请联系技术支持!');
        }
    }
    
    //自定义登录入口和注册入口检测
    $prohibits = ['admin','click','api','ico','icon','verify','apply','guestbook','article','sitemap'];
    if(in_array($_POST['Login'],$prohibits)){
        msg(-1,'此登录入口名已被系统使用');
    }
    if(in_array($_POST['Register'],$prohibits)){
        msg(-1,'此注册入口名已被系统使用');
    }
    //长度限制
    foreach (['c_name','c_desc','l_name','l_url','l_key','l_desc'] as $name){
        $length_limit[$name] = is_subscribe('bool') ? intval($_POST[$name]) : 0;
    }
    write_global_config("length_limit",$length_limit,'长度限制');
    
    //全局配置
    $datas = [
        'Login'=>['empty'=>false,'msg'=>'登录入口不能为空'],
        'Register'=>['empty'=>false,'msg'=>'注册入口不能为空'],
        'RegOption'=>['int'=>true,'min'=>0,'max'=>2,'msg'=>'注册配置参数错误'],
        'Libs'=>['empty'=>false,'msg'=>'静态路径不能为空'],
        'ICP'=>['empty'=>true],
        'Default_User'=>['empty'=>false,'msg'=>'默认用户不能为空'],
        'default_UserGroup'=>['empty'=>true],
        'XSS_WAF'=>['int'=>true,'min'=>0,'max'=>1,'msg'=>'防XSS脚本参数错误'],
        'SQL_WAF'=>['int'=>true,'min'=>0,'max'=>1,'msg'=>'防SQL注入参数错误'],
        'offline'=>['int'=>true,'min'=>0,'max'=>1,'msg'=>'离线模式参数错误'],
        'Debug'=>['int'=>true,'min'=>0,'max'=>1,'msg'=>'调试模式参数错误'],
        'Maintenance'=>['int'=>true,'min'=>0,'max'=>1,'msg'=>'维护模式参数错误'],
        'Sub_domain'=>['int'=>true,'min'=>0,'max'=>1,'msg'=>'二级域名参数错误'],
        'Privacy'=>['int'=>true,'min'=>0,'max'=>1,'msg'=>'强制私有参数错误'],
        'default_page'=>['int'=>true,'min'=>0,'max'=>2,'msg'=>'默认页面参数错误'],
        'global_header'=>['empty'=>true],
        'global_footer'=>['empty'=>true],
        'api_extend'=>['empty'=>true],
        'c_code'=>['int'=>true,'min'=>0,'max'=>1,'msg'=>'自定义代码参数错误'],
        'static_link'=>['int'=>true,'min'=>0,'max'=>2,'msg'=>'静态链接参数错误'],
        //更新设置
        'Update_Source'=>['empty'=>true],
        'Update_Overtime'=>['int'=>true,'min'=>3,'max'=>60,'msg'=>'资源超时参数错误'],
        //扩展功能-(全局开关)
        'apply'=>['int'=>true,'min'=>0,'max'=>1,'msg'=>'收录管理参数错误'],
        'guestbook'=>['int'=>true,'min'=>0,'max'=>1,'msg'=>'留言管理参数错误'],
        'link_extend'=>['int'=>true,'min'=>0,'max'=>1,'msg'=>'链接扩展参数错误'],
        'article'=>['int'=>true,'min'=>0,'max'=>2,'msg'=>'文章管理参数错误']
        ];
    $o_config = [];
    foreach ($datas as $key => $data){
        if($data['int']){
            $o_config[$key] = ($_POST[$key] >= $data['min'] && $_POST[$key] <= $data['max'])?intval($_POST[$key]):msg(-1,$data['msg']);
        }elseif(isset($data['v'])){
            $o_config[$key] = in_array($_POST[$key],$data['v']) ? $_POST[$key]:msg(-1,$data['msg']);
        }else{
            $o_config[$key] = $data['empty']?$_POST[$key]:(!empty($_POST[$key])?$_POST[$key]:msg(-1,$data['msg']));
        }
    }
    if(!is_subscribe('bool')){
        if($_POST['Sub_domain'] == 1){$o_config['Sub_domain'] = 0;$filter = true;}
        if($_POST['Privacy'] == 1){$o_config['Privacy'] = 0;$filter = true;}
        if(!empty($_POST['copyright'])){$o_config['copyright'] = "";$filter = true;}
        if(!empty($_POST['global_header'])){$o_config['global_header'] = "";$filter = true;}
        if(!empty($_POST['global_footer'])){$o_config['global_footer'] = "";$filter = true;}
        if($_POST['apply'] == 1){$o_config['apply'] = 0;$filter = true;}
        if($_POST['guestbook'] == 1){$o_config['guestbook'] = 0;$filter = true;}
        if($_POST['link_extend'] == 1){$o_config['link_extend'] = 0;$filter = true;}
        if($_POST['article'] == 1){$o_config['article'] = 0;$filter = true;}
        if($_POST['static_link'] == 1){$o_config['static_link'] = 0;$filter = true;}
    }
    update_db("global_config", ["v" => $o_config], ["k" => "o_config"],[1,($filter ?"保存成功,未检测到有效授权,带*号的配置无法为你保存":"保存成功")]);
}

//写默认设置
function write_default_settings(){ 
    msg_tip();
}
//读日志
function read_log(){
    $keyword  = $_POST['keyword'];
    $RecordType  = @$_POST['RecordType'];
    $page   = empty(intval($_REQUEST['page'])) ? 1 : intval($_REQUEST['page']);
    $limit  = empty(intval($_REQUEST['limit'])) ? 50 : intval($_REQUEST['limit']);
    $offset = ($page - 1) * $limit; //起始行号

    //用户组筛选
    if(!empty($RecordType)){
        $where['AND']['type'] = $RecordType;
    }

    //关键字筛选
    if(!empty($keyword)){
        $where['AND']['OR'] = ["user[~]" => $keyword,"ip[~]" => $keyword,"description[~]" => $keyword];
    }
    
    //统计条数
    $count = count_db('user_log',$where);
    //分页
    $where['LIMIT'] = [$offset,$limit];
    $where['ORDER']['id'] = 'DESC';
    //查询
    $datas = select_db('user_log','*',$where);
    //返回
    msgA(['code'=>1,'msg'=>'获取成功','count'=>$count,'data'=>$datas]);
}

//其他接口
function other_root(){
    
    if($_GET['type'] == 'CleanCache'){
        if(!is_dir(DIR."/data/temp")){
            msg(1,'服务器很干净');
        }
        function delfile($dir,$minute){$time=time();if(is_dir($dir)){if($dh=opendir($dir)){while(false!==($file=readdir($dh))){if($file!="."&&$file!=".."){$fullpath=$dir."/".$file;if(!is_dir($fullpath)){if($time-filemtime($fullpath)>$minute* 60 ){$_SESSION['CleanCacheSize']+=filesize($fullpath);unlink($fullpath);}}else{delfile($fullpath,$minute);if(count(scandir($fullpath))== 2 ){rmdir($fullpath);}}}}}closedir($dh);}return;}
        $_SESSION['CleanCacheSize'] = 0;
        $dir = DIR."/data/temp";
        delfile($dir,30);
        $size = $_SESSION['CleanCacheSize'];
        unset($_SESSION['CleanCacheSize']);
        clean_cache();
        if($size == 0){
            msg(1,'暂无可清理缓存');
        }
        
        msg(1,'已释放 '.byteFormat($size).' 缓存');
    }elseif($_GET['type'] == 'import_data'){
        require DIR .'/system/UseFew/root_import_data.php';
    }elseif($_GET['type'] == 'read_username_retain'){
        $data = get_db("global_config", "v", ["k" => "username_retain"]);
        msgA(['code'=>1,'msg'=>'获取成功','data'=>$data]);
    }elseif($_GET['type'] == 'write_username_retain'){
        msg_tip();
    }elseif($_GET['type'] == 'write_mail_config'){
        msg_tip();
    }elseif($_GET['type'] == 'write_mail_test'){
        msg_tip();
    }elseif($_GET['type'] == 'write_icon_config'){
        msg_tip();
    }elseif($_GET['type'] == 'write_icon_del_cache'){
        //删除数据库缓存信息
        if(empty(count_db('global_icon','*'))){
            msg(-1,'无缓存记录..');
        }
        delete_db('global_icon','*');
        
        //删除缓存目录下的所有文件
        $files = glob(DIR.'/data/icon' . '/*');
        if (empty($files)) {
            msg(-1,'无缓存文件..');
        } 
        foreach ($files as $file) {
            if (is_file($file)) {
                unlink($file);
            }
        }
        
        msg(1,'操作成功');
    }elseif($_GET['type'] == 'write_sitemap_config'){
        msg_tip();
    }
}

// 通用类请求官方服务器
function other_services(){
    // 生成请求数据
    $domain = preg_replace('/:\d+$/','',$_SERVER['HTTP_HOST']);
    $post = [
        'domain'   => $domain,
        'referer'  => isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : "",
        'email'    => isset($_POST['email']) ? $_POST['email'] : "",
        'order_id' => isset($_POST['order_id']) ? $_POST['order_id'] : "",
        'sysver'   => SysVer,
        'ip'       => Get_IP(),
        'method'   => $_GET['type']
    ];
    $overtime = !isset($global_config['Update_Overtime']) ? 3 : ($global_config['Update_Overtime'] < 3 || $global_config['Update_Overtime'] > 60 ? 3 : $global_config['Update_Overtime']);
    // 判断操作类型
    if($_GET['type'] == 'query_key' || $_GET['type'] == 'save_key'){
        $Res = ccurl("http://service.twonav.cn/service.php",$overtime,true,$post);
        if($Res['code'] != 200){
            msg(-1,'请求官方服务器失败,请稍后再试');
        }
        $data = json_decode($Res["content"], true);
        $msg = $data['msg'];
        // 如果是保存设置
        if($_GET['type'] == 'save_key'){
            $data = $data['data'];
            if(!isset($data['order_id']) || empty($data['order_id'])){
                msg(-1,empty($msg) ? '保存失败,请核对信息是否有误<br />' : $msg);
            }
            //判断是否为IP
            if(preg_match("/^(\d+\.\d+\.\d+\.\d+):*\d*$/",$domain,$host)) {
                $data['host'] = $host[1]; //取出IP(不含端口)
            }else{
                $host = explode(".", $domain);
                $count = count($host);
                if($count != 2){
                    $data['host'] = $host[$count-2].'.'.$host[$count-1];
                    //如果存在端口则去除
                    if(preg_match("/(.+):\d+/",$data['host'],$host)) {
                        $data['host'] = $host[1];
                    }
                }
            }
            write_global_config('s_subscribe',$data,'订阅信息');
            clean_cache();
            msgA(['code'=>200,'msg'=>'保存成功','data'=>['order_id'=>$data['order_id'],'end_time'=>$data['end_time'],'type_name'=>$data['type_name']]]);
        }
        msgA($data);
    }elseif($_GET['type'] == 'del_key'){
        $subscribe = unserialize(get_db('global_config','v',["k" => "s_subscribe"])); 
        if(!isset($subscribe['order_id']) || empty($subscribe['order_id'])){
            msg(200,'清除成功');
        }
        ccurl("http://service.twonav.cn/service.php",$overtime,true,$post);
        write_global_config('s_subscribe','','订阅信息');
        clean_cache();
        msg(200,'删除成功');
    }elseif($_GET['type'] == 'validate'){
        $Res = ccurl("http://service.twonav.cn/service.php",$overtime,true,data_encryption('validate'));
        $data = json_decode($Res["content"], true);
        if($data["code"] != '200'){
            msg(-1,$data['msg'] ?? '验证失败');
        }
        msgA($data);
    }elseif($_GET['type'] == 'get_notice'){
        //读取缓存数据
        $Notice = get_db('global_config','v',['k'=>'notice']);$data=[];
        //如果不为空,则解析数据
        if(!empty($Notice)){
            $data = json_decode($Notice, true);
            $cache_time = 60; //缓存时间(秒);
            $reload = time() > $data["download_time"] + $cache_time; //是否更新
        }else{
            $reload = true; //需要刷新
        }
        // 判断是否刷新数据
        if(!$global_config['offline'] && $reload){
            if(is_subscribe('bool')){
                $Res = ccurl('http://service.twonav.cn/service.php',$overtime,true,data_encryption('get_new_ver',['ver'=>SysVer]));
            }else{
                $Res = ccurl('http://tznb.gitee.io/twonav_resource/Notice.json',$overtime);
            }
            $new_data = json_decode($Res['content'], true);
            if($new_data["code"] == 200 ){
                $new_data['download_time'] = time();
                $new_data['version'] = version_compare($new_data['version'],SysVer,'<') ? SysVer : $new_data['version'];
                write_global_config('notice',json_encode($new_data),'官方公告(缓存)');
                $data = $new_data;
            }
        }
        msgA($data);
    }
}

