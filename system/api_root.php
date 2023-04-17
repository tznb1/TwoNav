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
        
        $_SESSION['upsys']['sysver'] = intval($matches[1]);
        usleep(1000*300); //延迟300毫秒
        msg(1,'success');
    }
    //下载更新包
    if($_POST['i'] == 2){
        if(!is_subscribe('bool')){
            msg(-1,'未检测到有效授权,请
            <a href="https://gitee.com/tznb/OneNav/wikis/%E8%AE%A2%E9%98%85%E6%9C%8D%E5%8A%A1%E6%8C%87%E5%BC%95" target="_blank" style="color: #01AAED;">购买授权</a>
            或
            <a href="https://gitee.com/tznb/TwoNav/releases" target="_blank" style="color: #01AAED;">手动更新</a>');
        }
        //设置执行最长时间，0为无限制。单位秒!
        set_time_limit(5*60);
        //加载远程数据
        $urls = [ "https://update.lm21.top/TwoNav/updata.json"];
        foreach($urls as $url){ 
            $Res = ccurl($url,3);
            $data = json_decode($Res["content"], true);
            if($data["code"] == 200 ){ //如果获取成功
                break; //跳出循环.
            } 
        }
    
        if($data["code"] != '200'){
            msg(-1,'获取更新信息失败,请稍后再试..');
        }
        
        foreach($data["data"] as $key){
            if( $_SESSION['upsys']['sysver'] >= $key["low"]  && $_SESSION['upsys']['sysver'] <= $key["high"] &&  $key["update"] > $_SESSION['upsys']['sysver']){
                $file = "System_Upgrade.tar.gz";
                $filePath = "./data/temp/{$file}";
                $data = $key;
                break; //找到跳出
            }
        }
        if(empty($file)){
            msg(-1,'暂无可用更新');
        }
        
        //下载升级包
        unlink($filePath);
        foreach($data["url"] as $url){
            if(downFile($url,$file,'./data/temp/')){
                $file_md5 = md5_file($filePath);
                if($file_md5 === $data['md5']){
                    break; //下载成功,跳出循环
                }else{
                    unlink($filePath); //下载失败,删除文件
                }
            }
        }
        //检查下载结果
        if(empty($file_md5) ){
            msg(-1,'下载更新包失败');
        }elseif($file_md5 != $data['md5']){
            msgA(['code'=>-1,'msg'=> '升级包效验失败','correct_md5'=> $data['md5'],'reality_md5'=>$file_md5]);
        }
        //sleep(1);
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
        set_time_limit(5*60);
        try {
            //根据数据库类型扫描不同目录,并声明执行SQL语句的函数
            if($GLOBALS['db_config']['type'] == 'mysql'){
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
    if(!is_subscribe('bool')){
        msg(-1,'未检测到有效授权');
    }
    $purview_list = select_db('purview_list','name','');
    $datas = select_db('user_group',['id','name','allow','code','codes','uname'],'');
    foreach ($datas as $key => $data){
        $datas[$key]['codes'] = unserialize($datas[$key]['codes']);
        if(empty($datas[$key]['codes'])){
            $datas[$key]['disable'] = $purview_list;//为空表示全部
        }else{
            $datas[$key]['disable'] = array_diff($purview_list,explode(",", $data['allow']));
        }
        
        $datas[$key]['disable'] = implode(',',$datas[$key]['disable']); //数组转文本
    }
    msgA(['code'=>1,'msg'=>'获取成功','count'=>count($datas),'data'=>$datas]);
}

//写用户组
function write_users(){
    //验证代号是否合规
    if(!preg_match('/^[A-Za-z0-9]+$/',$_POST['code'])){
        msg(-1,'分组代号只能是字母和数字');
    }elseif($_POST['code'] == 'root' || $_POST['code'] == 'default'){
        msg(-1,'不能使用系统预设的代号');
    }elseif(htmlspecialchars(trim($_POST['name'])) != $_POST['name']){
        msg(-1,'分组名称不能含有特殊字符');
    }
    if(!is_subscribe('bool')){
        msg(-1,'未检测到有效授权');
    }
    $USER = $_POST['uname'];
    $USER_ID = '';
    if(!empty($USER)){
        $USER_ID = get_db("global_user", "ID", ["User"=>$USER]);
        if(empty($USER_ID)){msg(-1,'蓝图用户不存在');}
    }
    
    if($_GET['type'] == 'add'){
        if(!empty(get_db('user_group','code',['code' => $_POST['code']]))){
            msg(-1,'分组代号已存在');
        }elseif(!empty(get_db('user_group','name',['name' => $_POST['name']]))){
            msg(-1,'分组名称已存在');
        }
        
        insert_db('user_group',["uname"=>$USER,"uid"=>$USER_ID,"code"=>$_POST['code'],"name"=>$_POST['name'],"allow"=>$_POST['allow_list'],"codes"=>json_decode($_POST['allow_code_list'])],[1,'添加成功']);
    }elseif($_GET['type'] == 'edit'){
        if(empty(get_db('user_group','code',['code' => $_POST['code']]))){ 
            msg(-1,'此分组代号不存在');
        }elseif(!empty(get_db('user_group','name',['name' => $_POST['name'],'code[!]'=>$_POST['code']]))){
            msg(-1,'分组名称已存在');
        }
        update_db('user_group',["uname"=>$USER,"uid"=>$USER_ID,"name"=>$_POST['name'],'allow'=>$_POST['allow_list'],'codes'=>json_decode($_POST['allow_code_list']) ],['code'=>$_POST['code']],[1,'保存成功']);
    }elseif($_GET['type'] == 'del'){
        global $global_config;
        if(!empty(get_db('global_user','ID',['UserGroup' => $_POST['code']]))){
            msg(-1,'无法删除,有用户正在使用此用户组');
        }elseif(!empty(get_db('regcode_list','regcode',['u_group' => $_POST['code']]))){
            msg(-1,'无法删除,存在使用此用户组的注册码');
        }elseif($global_config['default_UserGroup'] == $_POST['code']){
            msg(-1,'无法删除,正在被使用:系统设置>默认分组');
        }
        delete_db('user_group',["code" => $_POST['code'] ],[1,'删除成功']);
    }
}


//写用户信息
function write_user_info(){
   switch ($_GET['type']) {
    //删除
    case "Del":
        $uids = json_decode($_POST['ID']);
        foreach (['regcode_list','user_categorys','user_config','user_count','user_links','user_log','user_login_info'] as $table){
            delete_db($table,[ "uid" => $uids ]);
        }
        delete_db('global_user',["ID" => json_decode($_POST['ID']) ]);
        msg(1,'删除成功');
        break;
    //设用户组
    case "set_UserGroup":
        if(empty($_POST['UserGroup'])){
            msg(-1,'用户组不能为空');
        }elseif(!in_array($_POST['UserGroup'],['default','root']) && empty(get_db('user_group','code',['code' => $_POST['UserGroup']]))){
            msg(-1,'用户组不存在');
        }
        update_db('global_user',['UserGroup'=>$_POST['UserGroup']],["ID" => json_decode($_POST['ID']) ],[1,'修改成功']);
        break;
    //设密码
    case "set_pwd":
        if(!has_db('global_user',['ID'=>$_POST['ID']])){
            msg(-1,'用户不存在!');
        }
        //空字符串md5 防止意外出现空密码
        if( $_POST['new_pwd']== 'd41d8cd98f00b204e9800998ecf8427e'){
            msg(-1,'密码不能为空');
        }
        $RegTime = get_db('global_user','RegTime',['ID'=>$_POST['ID']]);
        update_db('global_user',['Password'=>Get_MD5_Password($_POST['new_pwd'],$RegTime)],["ID" => $_POST['ID'] ],[1,'修改成功']);
        break;
    //设邮箱
    case "set_email":
        if(!preg_match("/\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*/i",$_POST['new_email'])){
            msg(-1,'邮箱错误!');
        }
        if(has_db('global_user',['Email'=>$_POST['new_email']])){
            msg(-1,'邮箱已存在!');
        }
        update_db('global_user',['Email'=>$_POST['new_email']],["ID" => $_POST['ID'] ],[1,'修改成功']);
        break;

    default:
        msg(-1,'操作类型错误');
   }
}

//读注册码列表
function read_regcode_list(){
    if(!is_subscribe('bool')){
        msg(-1,'未检测到有效授权');
    }
    $page   = empty(intval($_REQUEST['page'])) ? 1 : intval($_REQUEST['page']);
    $limit  = empty(intval($_REQUEST['limit'])) ? 50 : intval($_REQUEST['limit']);
    $offset = ($page - 1) * $limit; //起始行号
    $where = [];
    
    //统计条数
    $count = count_db('regcode_list',$where);
    //分页
    $where['LIMIT'] = [$offset,$limit];
    //排序
    $where['ORDER']['id'] = 'DESC';
    //查询
    $datas = select_db('regcode_list','*',$where);
    //用户组处理
    if(!empty($datas)){
       $user_group = select_db('user_group',['name','code'],'');//读用户组
       $user_group = array_column($user_group, 'name', 'code');//以代号为键
       $user_group['root'] = '站长';
       $user_group['default'] = '默认';
       foreach ($datas as $key => $data){
           $datas[$key]['UserGroupName'] = $user_group[$data['u_group']]??'Null';
       }
    }
    msgA(['code'=>1,'msg'=>'获取成功','count'=>$count,'data'=>$datas]);
}

//写注册码
function write_regcode(){
    global $db;
    if(!is_subscribe('bool')){
        msg(-1,'未检测到有效授权');
    }
    if($_GET['type'] == 'generate'){
        if(!in_array($_POST['group'] ,['default']) && empty(get_db('user_group','code',['code' => $_POST['group'] ]))){
            msg(-1,'用户组不存在');
        }
        
        $t = time();
        for ($i=1; $i<=$_POST['number']??1; $i++){
            if($_POST['regcode_length'] == 8){
                $regcode = hash("crc32b",uniqid());
            }elseif($_POST['regcode_length'] == 36){
                $regcode = $db::raw("UUID()");
            }else{
                $regcode = md5(uniqid());
            }
            insert_db('regcode_list',["uid"=>UID,"regcode"=>$regcode,"u_group"=>$_POST['group'],"use_state"=>'未使用',"add_time"=>$t,"use_time"=>0]);
        }
        
        msg(1,'注册码已生成');
    }elseif($_GET['type'] == 'set'){
        write_global_config('reg_tips',$_POST['content'],'注册提示');
        msg(1,'保存成功');
    }elseif($_GET['type'] == 'del'){
        delete_db("regcode_list",[ "id" => json_decode($_POST['id'])]);
        msg(1,'删除成功');
    }
    
    msg(-1,'无效的请求类型');
}


//写订阅信息
function write_subscribe(){
    global $USER_DB;
    $data['order_id'] = htmlspecialchars( trim($_REQUEST['order_id']) ); //获取订单ID
    $data['email'] = htmlspecialchars( trim($_REQUEST['email']) ); //获取邮箱
    $data['end_time'] = htmlspecialchars( trim($_REQUEST['end_time']) );//到期时间
    $data['domain'] = htmlspecialchars( trim($_REQUEST['domain']) );//支持域名
    $data['host'] = $_SERVER['HTTP_HOST']; //当前域名
    if(empty($data['order_id']) && empty($data['email']) && empty($data['end_time'])){
        write_global_config('s_subscribe','','订阅信息');
        msg(1,'清除成功');
    }
    if($data['end_time'] < time()){
        msg(-1,"您的订阅已过期!");
    }
    //判断是否为IP
    if(preg_match("/^(\d+\.\d+\.\d+\.\d+):*\d*$/",$data['host'],$host)) {
        $data['host'] = $host[1]; //取出IP(不含端口)
    }else{
        $host = explode(".", $data['host']);
        $count = count($host);
        if($count != 2){
            $data['host'] = $host[$count-2].'.'.$host[$count-1];
            //如果存在端口则去除
            if(preg_match("/(.+):\d+/",$data['host'],$host)) {
                $data['host'] = $host[1];
            }
        }
    }

    if(stristr($data['domain'],$data['host'])){
        write_global_config('s_subscribe',$data,'订阅信息');
        msg(1,'保存成功');
    }else{
        msg(-1,"您的订阅不支持当前域名 >> ".$_SERVER['HTTP_HOST']);
    }
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
    }
    
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
        'copyright'=>['empty'=>true],
        'global_header'=>['empty'=>true],
        'global_footer'=>['empty'=>true],
        //扩展功能-(全局开关)
        'apply'=>['int'=>true,'min'=>0,'max'=>1,'msg'=>'收录管理参数错误'],
        'guestbook'=>['int'=>true,'min'=>0,'max'=>1,'msg'=>'留言管理参数错误'],
        'link_extend'=>['int'=>true,'min'=>0,'max'=>1,'msg'=>'链接扩展参数错误'],
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
        if(!empty($_POST['copyright'])){$o_config['copyright'] = "";$filter = true;}
        if(!empty($_POST['global_header'])){$o_config['global_header'] = "";$filter = true;}
        if(!empty($_POST['global_footer'])){$o_config['global_footer'] = "";$filter = true;}
        if(!empty($_POST['apply'])){$o_config['apply'] = 0;$filter = true;}
        if(!empty($_POST['guestbook'])){$o_config['guestbook'] = 0;$filter = true;}
    }
    
    update_db("global_config", ["v" => $o_config], ["k" => "o_config"],[1,($filter ?"保存成功,未检测到有效授权,带*号的配置无法为你保存":"保存成功")]);
}

//写默认设置
function write_default_settings(){ 
    global $USER_DB;
    if(!is_subscribe('bool')){
        msg(-1,'未检测到有效授权');
    }
    if( $_POST['KeyClear'] > $_POST['Session']){
        msg(-1,'Key清理时间不能大于登录保持时间');
    }
    // 安全配置(登录配置)
    $datas = [
        'Session'=>['int'=>true,'min'=>0,'max'=>360,'msg'=>'登录保持参数错误'],
        'HttpOnly'=>['int'=>true,'min'=>0,'max'=>1,'msg'=>'HttpOnly参数错误'],
        'KeySecurity'=>['int'=>true,'min'=>0,'max'=>2,'msg'=>'Key安全参数错误'],
        'KeyClear'=>['int'=>true,'min'=>1,'max'=>60,'msg'=>'Key清理参数错误'],
        'api_model'=>['v'=>['security','compatible','compatible+open'],'msg'=>'API模式参数错误']
        ];
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
    $LoginConfig['Password2'] = '';
    update_db("global_config",["v"=>$LoginConfig],["k"=>'LoginConfig']);
    
    //站点配置
    $datas = [
        'title'=>['empty'=>false,'msg'=>'主标题不能为空'],
        'subtitle'=>['empty'=>true],
        'logo'=>['empty'=>true],
        'keywords'=>['empty'=>true],
        'description'=>['empty'=>true],
        'link_model'=>['v'=>['direct','Privacy','302','Transition'],'msg'=>'链接模式参数错误'],
        'link_icon'=>['int'=>true,'min'=>0,'max'=>6,'msg'=>'链接图标参数错误'],
        'custom_header'=>['empty'=>true],
        'custom_footer'=>['empty'=>true]
        ];
    $s_site = [];
    foreach ($datas as $key => $data){
        if($data['int']){
            $s_site[$key] = ($_POST[$key] >= $data['min'] && $_POST[$key] <= $data['max'])?intval($_POST[$key]):msg(-1,$data['msg']);
        }elseif(isset($data['v'])){
            $s_site[$key] = in_array($_POST[$key],$data['v']) ? $_POST[$key]:msg(-1,$data['msg']);
        }else{
            $s_site[$key] = $data['empty']?$_POST[$key]:(!empty($_POST[$key])?$_POST[$key]:msg(-1,$data['msg']));
        }
    }
    update_db("global_config",["v"=>$s_site],["k"=>'s_site'],[1,'保存成功']);
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
        if($size == 0){
            msg(1,'暂无可清理缓存');
        }
        
        msg(1,'已释放 '.byteFormat($size).' 缓存');
    }elseif($_GET['type'] == 'import_data'){
        require DIR .'/system/UseFew/root_import_data.php';
    }
    
}
