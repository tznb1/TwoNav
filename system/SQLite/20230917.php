<?php if(!defined('DIR')){header('HTTP/1.1 404 Not Found');header("status: 404 Not Found");exit;}
// 检测是否已授权
if(is_subscribe('bool')){
    //读取授权信息,判断是否存在秘钥
    $subscribe = unserialize(get_db('global_config','v',["k" => "s_subscribe"])); 
    if(!isset($subscribe['public']) || empty($subscribe['public'])){
        //尝试从服务器下载秘钥
        $Res = ccurl("https://service.twonav.cn/api.php?fn=get_subscribe&order_id={$subscribe['order_id']}&email={$subscribe['email']}&domain={$subscribe['domain']}&mark=20230917",30,true);
        $data = json_decode($Res["content"], true);
        // 获取成功
        if($data["code"] == 200){
            $subscribe['public'] = $data['data']['public'];
            $subscribe['type'] = $data['data']['type'];
            $subscribe['type_name'] = $data['data']['type_name'];
            write_global_config('s_subscribe',$subscribe,'订阅信息');
        }
    }
}
