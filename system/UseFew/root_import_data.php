<?php 
if(!defined('DIR')){
    header('HTTP/1.1 404 Not Found');
    header("status: 404 Not Found");
    exit;
}

if($_POST['step'] == 1){
    if(empty($_POST['file'])){
        msg(-1,'文件名不能为空');
    }
    if(!preg_match("/twonav_updata_[0-9a-z]{8}\.tar/",$_POST['file'])){
        msg(-1,'文件名格式错误,请勿修改导出数据的文件名!');
    }
    if(!is_file( "./data/{$_POST['file']}")){
        msg(-1,'文件不存在');
    }
    if($_POST['file'] !=  "twonav_updata_".hash_file('crc32b',"./data/{$_POST['file']}").".tar"){
        msg(-1,'文件哈希值效验失败(请勿修改文件名)');
    }
    try {
        $phar = new PharData("./data/{$_POST['file']}");
        $phar->extractTo("./data/user/", null, true);
    } catch (Exception $e) {
        msg(-1,'解压数据异常');
    }
    if(!is_file("./data/user/lm.TwoNav.db3")){
        msg(-1,'解压数据失败,未找到lm.TwoNav.db3');
    }
    $MyDB = new Medoo\Medoo(['type'=>'sqlite','database'=>"./data/user/lm.TwoNav.db3"]);
    $ver = $MyDB->get('backup','data',['name'=>'ver']);
    
    if(empty($ver)){
        msg(-1,'获取版本号失败');
    }

    
    $user_list = $MyDB->select('backup','*',['name'=>'user']);
    $users = [];
    foreach ($user_list as $user) {
        $data = unserialize($user['data']);
        array_push($users,['id'=>$user['id'],'name'=>$data['User']]);
    }
    msgA(['code'=>1,'msg'=>'版本 ' .$ver ,'count'=>count($users),'data'=>$users]);
}
if($_POST['step'] == 3){
    $MyDB = new Medoo\Medoo(['type'=>'sqlite','database'=>"./data/user/lm.TwoNav.db3"]);
    $config = $MyDB->get('backup','data',['name'=>'config']);
    global $global_config;
    if(!empty($config)){
        $config = unserialize($config);
        
        //保留默认用户
        if(!empty($config['DUser'])) !has_db('global_user',["User"=>$config['DUser']]) or $global_config['Default_User'] = $config['DUser'];
        //备案号
        if(!empty($config['ICP']) && empty($global_config['ICP'])) $global_config['ICP'] = $config['ICP'];
        //底部代码
        if(!empty($config['footer']) && empty($global_config['global_footer'])) $global_config['global_footer'] = htmlspecialchars_decode(base64_decode($config['footer']));
        update_db("global_config", ["v" => $global_config],['k'=>'o_config']); 
        //订阅信息
        if(!empty($config['s_subscribe']) && !is_subscribe('bool')){
            write_global_config('s_subscribe',$config['s_subscribe'],'订阅信息');
        }
        
    }
    msg(1, "更新配置");
}
if($_POST['step'] == 2){
    $local_user = get_db("global_user", "ID", ["User"=>$_POST['user']]);
    if(has_db('global_user',["User"=>$_POST['user']])){
        msg(1, "用户:{$_POST['user']},本地已存在,跳过!");
    }
    try {
        global $db;
        $db->action(function($db) {
            $MyDB = new Medoo\Medoo(['type'=>'sqlite','database'=>"./data/user/lm.TwoNav.db3"]);
            $User = unserialize($MyDB->get('backup','data',['id'=>$_POST['id']]));
            $LoginConfig = get_db('global_config','v',['k'=>'LoginConfig']);
            if(has_db('global_user',['Email'=>$User['Email']])){
                msg(1,"用户:{$_POST['user']},邮箱冲突,跳过!");
            }
            insert_db("global_user", [
                "FID"=>0,
                "User"=>$User['User'],
                "Password"=>$User['Pass'],
                "UserGroup"=>$User['Level'] == 999 ? 'root':'default',
                "Email"=>$User['Email'],
                "Token"=>'',
                "SecretKey"=>'',
                "RegIP"=>$User['RegIP'],
                "RegTime"=>$User['RegTime'],
                "Login"=>$User['Login'],
                "LoginConfig"=>$LoginConfig
            ]);
            $USER_DB['ID'] = get_db('global_user','ID',['User'=>$User['User']]);
            if(empty($USER_DB['ID'])){
                msg(1, "用户:{$_POST['user']},导入失败-1");
            }
            $time = time();
            
            //遍历需要的配置
            $configs = [];
            foreach( $MyDB->select('backup','data',['name'=>"{$User['User']}_on_config"]) as  $config){
                $config = unserialize($config);
                if(in_array($config ['name'],['title','subtitle','logo','keywords','description'])){
                    $configs[$config ['name']] = $config ['value'];
                }
            }
            //保留部分站点配置
            $s_site = unserialize(get_db('global_config','v',['k'=>'s_site']));
            foreach($s_site as $key => $data){
                $s_site[$key] = empty($configs[$key]) ? $s_site[$key] : $configs[$key];
            }

            insert_db("user_config", ["uid"=>$USER_DB['ID'],"k" =>"s_site","v"=>$s_site,"d"=>'站点配置','t'=>'config']);
            insert_db("user_config", ["uid"=>$USER_DB['ID'],"k"=>"s_templates","v"=>get_db('global_config','v',['k'=>'s_templates']),"t"=>"config","d"=>'默认模板']);
            
            
            //导入用户分类
            $categorys = $MyDB->select('backup','data',['name'=>"{$User['User']}_on_categorys"]);
            foreach ($categorys as  $data){
                $data = unserialize($data);
                insert_db('user_categorys',[
                    'uid'=>$USER_DB['ID'],
                    'cid'=>$data['id'],
                    'fid'=>$data['fid'] ?? 0,
                    'pid'=>0,
                    'status'=>1,
                    'property'=>$data['property']??'0',
                    'name'=>htmlspecialchars($data['name'],ENT_QUOTES),
                    'add_time'=>$data['add_time'] ?? time(),
                    'up_time'=>$data['up_time'] ?? time(),
                    'weight'=>0,
                    'description'=>htmlspecialchars($data['description'],ENT_QUOTES),
                    'font_icon'=> strstr($data['Icon'],'fa') ? 'fa '.$data['Icon'] : 'fa fa-folder',
                    'icon'=>''
                    ]
                );
            }
            //导入用户链接
            $inks = $MyDB->select('backup','data',['name'=>"{$User['User']}_on_links"]);
            foreach ($inks as  $data){
                $data = unserialize($data);
                insert_db('user_links',[
                    'uid'           =>  $USER_DB['ID'],
                    'lid'           =>  $data['id'],
                    'fid'           =>  $data['fid'],
                    'pid'           =>  0,
                    'title'         =>  $data['title'],
                    'url'           =>  $data['url'],
                    'url_standby'   =>  empty($data['url_standby']) ? '': [$data['url_standby']] ,
                    'description'   =>  $data['description'],
                    'add_time'      =>  $data['add_time'] ?? time(),
                    'up_time'       =>  $data['up_time'] ?? time(),
                    'click'         =>  $data['click'] ?? 0,
                    'weight'        =>  0,
                    'status'        =>  1,
                    'property'      =>  $data['property'] ?? 0,
                    'icon'          =>  $data['iconurl'] ?? ''
                ]);
            }
            
            //写初始ID
            $link_id = intval(max_db('user_links','lid',['uid'=>$USER_DB['ID']])) +1;
            insert_db("user_config", ["uid"=>$USER_DB['ID'],"k"=>"link_id","v"=>$link_id,"t"=>"max_id","d"=>'链接ID']);
            $category_id = intval(max_db('user_categorys','cid',['uid'=>$USER_DB['ID']])) +1;
            insert_db("user_config", ["uid"=>$USER_DB['ID'],"k"=>"category_id","v"=>$category_id,"t"=>"max_id","d"=>'分类ID']);
            insert_db("user_config", ["uid"=>$USER_DB['ID'],"k"=>"pwd_group_id","v"=>1,"t"=>"max_id","d"=>'加密组ID']);
        });
    }catch(\Throwable $e) {
        if(Debug){
            msgA(['code'=>-1,'msg'=>"导入用户数据失败,用户名:{$_POST['user']}",'Message'=>$e->getMessage(),'debug'=>debug_backtrace()]);
        }else{
            msg(-1,"导入用户数据失败,用户名:{$_POST['user']}");
        }
        
    }
    
    
    sleep(0.5);
    msg(1, "用户:{$_POST['user']},数据导入成功!");
}