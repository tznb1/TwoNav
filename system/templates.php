<?php if(!defined('DIR')){header('HTTP/1.1 404 Not Found');header("status: 404 Not Found");exit;}

//读取全局模板配置
$global_templates = unserialize(get_db("global_config",'v', ["k" => "s_templates"]));

//读取用户模板配置
$s_templates = unserialize(get_db("user_config", "v", ["uid"=>UID,"k"=>"s_templates"]));

//没找到用户模板配置
if(empty($s_templates)){
    //将全局默认模板配置写到用户配置
    $s_templates = $global_templates;
    insert_db("user_config", ["uid" => UID,"k"=>"s_templates","v"=>$global_templates,"t"=>"config","d" => '默认模板']);
}

//载入辅助函数
if(empty($c) || in_array($c,['index','click'])){
    //将URL转换为base64编码
    function base64($url){
        $urls = parse_url($url);
        $scheme = empty( $urls['scheme'] ) ? 'http://' : $urls['scheme'].'://'; //获取请求协议
        $host = $urls['host']; //获取主机名
        $port = empty( $urls['port'] ) ? '' : ':'.$urls['port']; //获取端口
        $new_url = $scheme.$host.$port;
        return base64_encode($new_url);
    }
    //是否启用收录
    function is_apply(){
        global $global_config;
        $apply_user = unserialize( get_db("user_config", "v", ["k" => "apply","uid"=>UID]));
        return ($global_config['apply'] == 1 && $apply_user['apply'] == 1);
    }
    //是否启用留言
    function is_guestbook(){
        global $global_config;
        $guestbook_user = unserialize( get_db("user_config", "v", ["k" => "guestbook","uid"=>UID]) );
        return ($global_config['guestbook'] == 1 && $guestbook_user['allow'] == 1);
        
    }
    //获取图标URL
    function geticourl($icon,$link){
        if( !empty( $link['icon']) ){
            if(substr($link['icon'], 0,4) == '<svg'){
                return('data:image/svg+xml;base64,'.base64_encode($link['icon']));
            }else{
                return($link['icon']);
            }
        }
        if ($site['link_icon'] == 'default'){
            return($GLOBALS['libs'].'/Other/default.ico');
        }elseif ($icon ==1){
            return('./favicon/index2.php?url='.$link['real_url']);
        }elseif($icon ==2){
            return('//favicon.rss.ink/v1/'.base64($link['real_url']));
        }elseif($icon ==4){
            return('//api.15777.cn/get.php?url='.$link['real_url']);
        }elseif($icon ==5){
            return('//favicon.cccyun.cc/'.$link['real_url']);
        }elseif($icon ==6){
            return('//api.iowen.cn/favicon/'.parse_url($link['real_url'])['host'].'.png');
        }elseif($icon ==7){
            return('https://toolb.cn/favicon/'.parse_url($link['real_url'])['host']);
        }elseif($icon ==8){
            return('https://apis.jxcxin.cn/api/Favicon?url='.$link['real_url']);
        }elseif($icon ==0){
            return('./system/ico.php?text='.mb_strtoupper(mb_substr($link['title'], 0, 1)));
        }else{
            return('./favicon/index2.php?url='.$link['real_url']);
        }//如果参数错误则使用本地服务器
    }
    //取分类图标(六零系主题在用)
    function get_category($content){
        if(empty($content)){
            return '';
        }
        if(substr($content, 0,4) == '<svg'){
            return 'data:image/svg+xml;base64,'.base64_encode($content);
        }else{
            return $content;
        }
    }
    //获取公开分类(返回数组cid)
    function get_open_category(){
        $where['uid'] = UID; 
        $where['fid'] = 0;
        $where['status'] = 1;
        $where['property'] = 0;
        $categorys = select_db('user_categorys','cid',$where);
        $where['fid'] = $categorys;
        $categorys = array_merge ($categorys,select_db('user_categorys','cid',$where));
        return $categorys;
    }

}

