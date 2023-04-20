<?php if(!defined('DIR')){header('HTTP/1.1 404 Not Found');header("status: 404 Not Found");exit;}
//弥补主题配置目录名相同造成的窜数据的bug
//复制主题配置,并重新标记t类型
//  'theme_home','theme_login','theme_transit','theme_register';

$datas = select_db('user_config','*',['t'=>'theme']);
foreach ($datas as $data) {
    $name = $data['k'];
    unset($data['id']);
    if($name == 'default'){
        $data['t'] = 'theme_transit';
        insert_db('user_config',$data);
    }
    if($name == 'WebStack-Hugo'){
        $data['t'] = 'theme_transit';
        insert_db('user_config',$data);
    }
    $data['t'] = 'theme_home';
    insert_db('user_config',$data);
}

insert_db('updatadb_logs',['file_name'=>$file_name,'update_time'=>time(),'status'=>'TRUE','extra'=>'']);
delete_db('user_config',['t'=>'theme']);