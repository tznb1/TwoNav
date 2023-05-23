<?php 
if(!defined('DIR')){
    header('HTTP/1.1 404 Not Found');
    header("status: 404 Not Found");
    exit;
}else{
    if(!is_subscribe('bool')){
        msg(-1,"未检测到有效授权,无法使用该功能!");
    }
    
    if($_GET['type'] ==  'list'){
        $backup_dir = DIR."/data/backup/".U."/"; //备份目录
        $file_list = glob("{$backup_dir}*.info"); //扫描文件
        $num = count($file_list); //取列表数
        rsort($file_list,2); //按时间从大到小重排序
        //备份文件数大于20个时删除旧数据
        if( $num > 20 ) {
            for ($i=$num; $i > 20; $i--) {
                $path = pathinfo($file_list[$i-1]);
                $path = $path['dirname'] .'/'. $path['filename'];
                unlink($path.'.info');
                unlink($path.'.db3');
                unlink($path.'.tar');
                array_pop($file_list);
            }
            $count = 20;
        }else{
            $count = $num;
        }
        
        $data = [];
        //遍历读入备份信息
        foreach ($file_list as $key => $filePath) {
            $file = pathinfo($filePath);
            $info_file = @file_get_contents("{$file['dirname']}/{$file['filename']}.info");
            $info = json_decode($info_file,true);
            if($info != false){
                array_push($data,$info);
            }
        }
        msgA( ['code' => 1,'msg' => '','count' =>  $count,'data' =>  $data] );
    }elseif($_GET['type'] == 'backup'){
        //初始信息
        $info['user_dir'] = DIR."/data/user/".U;
        $info['backup_dir'] = DIR."/data/backup/".U; //备份目录
        $info['file'] = SysVer . "_".date("ymdHis",time())."_".Get_Rand_Str(5);
        $info['file_db'] = $info['backup_dir'] .'/'. $info['file'].'.db3';
        $info['file_info'] = $info['backup_dir'] .'/'. $info['file'].'.info';
        $info['file_gz'] = $info['backup_dir'] .'/'. $info['file'].'.tar';
        $info['table_arr'] = ['user_config','user_categorys','user_links','user_pwd_group','user_apply','user_share'];
        $info['lock'] = DIR.'/data/user/'.U.'/lock.'.UID;
        if (!extension_loaded('phar')) {
            msg(-1,'不支持phar扩展');
        }elseif(!is_dir($info['backup_dir']) && !mkdir($info['backup_dir'],0755,true) ){
            msg(-1,'创建backup目录失败');
        }elseif(!is_file($info['lock']) && !file_put_contents($info['lock'],'TwoNav')){
            msg(-1,'创建lock文件失败');
        }
        
        //打包用户文件
        try {
            $phar = new PharData($info['file_gz']);
            $phar->buildFromDirectory($info['user_dir']);
        } catch (Exception $e) {
            msg(-1,'打包用户数据发生异常>'.$e->getMessage());
        }
        //创建数据
        try {
            $MyDB = new Medoo\Medoo(['type'=>'sqlite','database'=>$info['file_db']]);
            $MyDB->query('CREATE TABLE IF NOT EXISTS "backup" ("id" INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,"name" TEXT,"data" TEXT,CONSTRAINT "id" UNIQUE ("id" ASC));')->fetchAll();
            $MyDB->insert('backup',['name'=>'ver','data'=>SysVer]); //记系统版本
            $MyDB->insert('backup',['name'=>'backup_time','data'=>time()]); //记备份时间
            $MyDB->insert('backup',['name'=>'database_type','data'=>$GLOBALS['db_config']['type']]); //数据库类型
        }catch (Exception $e) {
            Amsg(-1,'创建备份数据库失败'); 
        }
        
        //开始备份数据
        $table_info = [];
        foreach($info['table_arr'] as $table_name){
            $count = count_db($table_name,['uid'=>UID]); //总条数
            $limit = 100; //每页数量
            $pages= ceil($count/$limit); //总页数
            //分页逐条处理
            for ($page=1; $page<=$pages; $page++) {
                $where['uid'] = UID;
                $where['LIMIT'] = [($page - 1) * $limit,$limit];
                $datas = select_db($table_name,'*',$where);
                foreach($datas as $data){
                    try {
                        if(isset($data['id'])){
                            unset($data['id']);
                        }
                        $MyDB->insert('backup',['name'=>$table_name,'data'=>$data]);
                    }catch (Exception $e) {
                        Amsg(-1,'插入数据时发生异常'); 
                    }
                } 
            }
            $table_info[$table_name] = ['count'=>$count,'pages'=>$pages];
        }
        
        //备份信息
        $info['info'] = [
            "name" => $info['file'],
            "db_size" => filesize($info['file_db']),
            "db_md5" => md5_file($info['file_db']),
            "tar_size" => filesize($info['file_gz']),
            "tar_md5" => md5_file($info['file_gz']),
            "backup_time" => time(),
            "version" => SysVer,
            "desc" => "{$_POST['desc']}"
            ]; 
        $info['info'] = array_merge($table_info,$info['info']);
        $info['info'] = json_encode($info['info']);
        //写到文件
        if(file_put_contents($info['file_info'], $info['info']) === false){
            msg(-1,'写备份信息失败');
        }
        msg(1,'备份成功');
    //删除备份
    }elseif($_GET['type'] == 'del'){
        $path = DIR."/data/backup/".U."/".$_POST['name'];
        if( !preg_match_all('/^v\d+\.\d+\.\d+-\d{8}_\d{12}_[A-Za-z0-9]{5}$/',$_POST['name']) ) {
            msg(-1,'数据库名称不合法');
        }elseif(!is_file($path.'.info')){
            msg(-1,'备份不存在');
        }elseif(!extension_loaded('phar')) {
            msg(-1,'不支持phar扩展');
        }
        try {
            unlink($path.'.info');
            unlink($path.'.db3');
            unlink($path.'.tar');
            msg(1,'备份数据库已被删除');
        } catch (\Throwable $th) {
            msg(-1,"删除失败，请检查目录权限");
        }
    //回滚备份
    }elseif($_GET['type'] == 'restore'){
        try {
            global $db;
            header('Content-Type:application/json; charset=utf-8');
            //使用事务来处理
            $db->action(function($db) {
                //检测是否符合回滚要求
                $path = DIR."/data/backup/".U."/".$_POST['name'];
                if( !preg_match_all('/^v\d+\.\d+\.\d+-\d{8}_\d{12}_[A-Za-z0-9]{5}$/',$_POST['name']) ) {
                    msg(-1,'数据库名称不合法');
                }
                $info_file = @file_get_contents($path.'.info');
                $info = json_decode($info_file,true);
                if($info == false){
                    msg(-1,'读取备份信息失败');
                }elseif($info['db_md5'] != md5_file($path.'.db3')){
                    msg(-1,'db3文件效验失败');
                }elseif($info['tar_md5'] != md5_file($path.'.tar')){
                    msg(-1,'tar文件效验失败');
                }
                
                //载入数据库
                try {
                    $MyDB = new Medoo\Medoo(['type'=>'sqlite','database'=>$path.'.db3']);
                }catch (Exception $e) {
                    msg(-1,'载入备份数据库失败');
                    return false;
                }
                
                //遍历删除用户数据
                $info['table_arr'] = ['user_config','user_categorys','user_links','user_pwd_group','user_apply','user_share'];
                foreach($info['table_arr'] as $table_name){
                    
                    //删除数据
                    delete_db($table_name,['uid'=>UID]);
                    
                    //确保数据已删除
                    if($db->has($table_name,['uid'=>UID])){
                        msg(-1,'del ' . $table_name . ' fail');
                    }
                    
                    //读取条数,分页逐条导入
                    $count = $MyDB->count('backup',['name'=>$table_name]); //总条数
                    $limit = 100; //每页数量
                    $pages= ceil($count/$limit); //总页数
                    for ($page=1; $page<=$pages; $page++) {
                        $where['name'] = $table_name;
                        $where['LIMIT'] = [($page - 1) * $limit,$limit];
                        $datas = $MyDB->select('backup','data',$where);
                        foreach($datas as $data){
                            $data = unserialize($data);
                            if(isset($data['id'])){
                                unset($data['id']);
                            }
                            $data['uid'] = UID;
                            insert_db($table_name,$data);
                        }
                    }
                    
                    //确保数据已导入
                    if($count != count_db($table_name,['uid'=>UID])){
                        msg(-1,'restore ' . $table_name . ' fail');
                    }
                }

                //删除用户目录
                $user_dir = DIR."/data/user/".U;
                if(is_dir($user_dir) && !deldir($user_dir)){
                    msg(-1,'删除用户目录失败');
                }
                //创建用户目录
                if(!is_dir($user_dir) && !mkdir($user_dir,0755,true)){
                    msg(-1,'创建用户目录失败');
                }
                //回滚用户目录
                try {
                    $phar = new PharData($path.'.tar');
                    $phar->extractTo($user_dir, null, true);
                } catch (Exception $e) {
                    msg(-1,'回滚用户数据失败');
                }
                //返回信息,直接msg会导致回滚
                header('Content-Type:application/json; charset=utf-8');
                echo(json_encode(['code'=>1,'msg'=>'回滚成功']));
            });
        } catch (\Throwable $th) {
            msg(-1,"回滚失败");
        }
    //导出密码验证
    }elseif($_GET['type'] == 'create'){
        global $USER_DB;
        $pwd = Get_MD5_Password($_POST['pwd'],$USER_DB["RegTime"]) === $USER_DB["Password"];
        if(!$pwd){
            msg(-1,'密码错误');
        }elseif(empty($_POST['name'])){
            msg(-1,'文件名不能为空');
        }elseif(!extension_loaded('phar')) {
            msg(-1,'不支持phar扩展');
        }

        $path = DIR."/data/backup/".U."/".$_POST['name'];
        if(!is_file($path.'.info')){
            msg(-1,'info文件不存在');
        }elseif(!is_file($path.'.db3')){
            msg(-1,'db3文件不存在');
        }elseif(!is_file($path.'.tar')){
            msg(-1,'tar文件不存在');
        }
        
        session_start();
        $key = md5(uniqid().Get_Rand_Str(8));
        try {
            $temp_dir = DIR."/data/temp/{$key}";
            if(!is_dir($temp_dir) && !mkdir($temp_dir,0755,true)){
                msg(-1,'创建临时目录失败');
            }
            copy($path.'.info',"{$temp_dir}/{$_POST['name']}.info");
            copy($path.'.db3',"{$temp_dir}/{$_POST['name']}.db3");
            copy($path.'.tar',"{$temp_dir}/{$_POST['name']}.tar");
            $backup_path = "{$temp_dir}/TwoNav_{$_POST['name']}.tar";
            $phar = new PharData($backup_path);
            $phar->buildFromDirectory($temp_dir);
            $phar->compress(Phar::GZ);
            $backup_path .= ".gz";
            if(!is_file($backup_path)){
                msg(-1,'打包数据失败');
            }
        } catch (Exception $e) {
            msg(-1,'压缩数据异常');
        }
        $_SESSION['download'][$key] = $backup_path;
        msgA(['code'=>1,'msg'=>'success','key'=>$key]);
    //下载备份数据
    }elseif($_GET['type'] == 'download'){
        session_start();
        if(empty($_GET['key']) || !isset($_SESSION['download'][$_GET['key']])){
            msg(-1,'Key不存在,请重新导出');
        }
        $path = $_SESSION['download'][$_GET['key']];
        if(!is_file($path)){
            msg(-1,'文件不存在,请重新导出');
        }
        
        $filename = pathinfo($path,PATHINFO_BASENAME);
        header("Cache-Control: public");
        header("Content-Description: File Transfer");
        header('Content-disposition: attachment; filename='.$filename); //文件名
        header("Content-Type: application/octet-stream"); 
        header("Content-Transfer-Encoding: binary"); //告诉浏览器，这是二进制文件
        header('Content-Length: '. filesize($path)); //告诉浏览器，文件大小
        readfile($path); //返回文件
        unlink ($path);//删除临时文件
        unset($_SESSION['download'][$_GET['key']]); //删除Key
        deldir(DIR."/data/temp/{$_GET['key']}"); //删除临时目录
    //导入
    }elseif($_GET['type'] == 'local_import'){
        if (!extension_loaded('phar')) {
            msg(-1,'不支持phar扩展');
        }
        $key = md5(uniqid().Get_Rand_Str(8));
        $temp_dir = DIR."/data/temp/{$key}";
        if(!is_dir($temp_dir) && !mkdir($temp_dir,0755,true)){
            msg(-1,'创建临时目录失败');
        }
        //解压数据
        try {
            copy($_FILES['file']['tmp_name'],"{$temp_dir}/{$_FILES['file']['name']}");
            $phar = new PharData("{$temp_dir}/{$_FILES['file']['name']}");
            $phar->extractTo($temp_dir, null, true);
            unlink("{$temp_dir}/{$_FILES['file']['name']}");
        } catch (Exception $e) {
            deldir($temp_dir);
            msg(-1,'解压数据失败');
        }
        //获取备份信息
        $file = glob("{$temp_dir}/*.info");
        if(count($file) != 1){
            deldir($temp_dir);
            msg(-1,'读取备份信息失败');
        }
        $file = pathinfo($file[0]);
        $info = @file_get_contents("{$temp_dir}/{$file['basename']}");
        $info = json_decode($info,true);
        if($info == false){
            deldir($temp_dir);
            msg(-1,'解析备份信息失败');
        }elseif($info['db_md5'] != md5_file("{$temp_dir}/{$info['name']}.db3")){
            deldir($temp_dir);
            msg(-1,'db3文件效验失败'.$info['db_md5']);
        }elseif($info['tar_md5'] != md5_file("{$temp_dir}/{$info['name']}.tar")){
            deldir($temp_dir);
            msg(-1,'tar文件效验失败');
        }
        //检查目录
        if(!Check_Path(DIR."/data/backup/".U)){
            msg(-1,'创建backup目录失败,请检查权限');
        }
        //复制到用户数据
        try {
            $backup_dir = DIR."/data/backup/".U."/";
            copy("{$temp_dir}/{$info['name']}.info","{$backup_dir}{$info['name']}.info");
            copy("{$temp_dir}/{$info['name']}.db3", "{$backup_dir}{$info['name']}.db3");
            copy("{$temp_dir}/{$info['name']}.tar", "{$backup_dir}{$info['name']}.tar");
            deldir($temp_dir);
            msg(1,'导入成功');
        } catch (Exception $e) {
            deldir($temp_dir);
            msg(-1,'复制数据失败,请检查目录权限');
        }
        //结束
    }
}
