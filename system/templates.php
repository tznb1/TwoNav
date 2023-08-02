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
if(empty($c) || in_array($c,['index','click','article'])){
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
        }elseif ($icon ==20){
            return('./index.php?c=icon&url='.base64_encode($link['real_url']));
        }elseif ($icon ==21){
            return('./ico/'.base64_encode($link['real_url']));
        }elseif($icon ==2){
            return('//favicon.png.pub/v1/'.base64($link['real_url']));
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
    function get_category($content){ //抽风的命名..过度几个版本后删除
        return get_category_icon($content);
    }
    function get_category_icon($content){
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

//获取文章列表
function get_article_list($category = 0,$limit = 0){
    $where['uid'] = UID; 
    if(!is_login()){
        $where['AND']['state'] = 1; //状态筛选
    }else{
        $where['AND']['OR']['state'] = [1,2]; //状态筛选
    }
    
    //分类筛选
    if($category > 0){
        $where['AND']['category'] = $category;
    }
    //统计条数
    $count = count_db('user_article_list',$where);
    //获取条数
    if($limit > 0){
        $where['LIMIT'] = [0,$limit];
    }
    //获取文章列表
    $datas = select_db('user_article_list','*',$where);
    
    //查询分类
    $categorys = select_db('user_categorys',['cid(id)','name'],['uid'=>UID]);
    $categorys = array_column($categorys,'name','id');
    //为文章添加分类名称
    foreach ($datas as &$data) {
        $data['category_name'] = $categorys[$data['category']] ?? 'Null';
    }
    return ['data'=>$datas,'count'=>$count];
}
//根据文章id获取内容
function get_article_content($id){
    $where['uid'] = UID;
    if(!is_login()){
        $where['AND']['state'] = 1; //状态筛选
    }else{
        $where['AND']['OR']['state'] = [1,2]; //状态筛选
    }
    $where['id'] = $id;
    $data = get_db('user_article_list','*',$where);
    $data['category_name'] = get_db('user_categorys','name',['uid'=>UID,'cid'=>$data['category']]);
    return $data;
}

//获取分类列表
function get_category_list($layer = false){
    //查询条件
    $where = [];
    $where['uid'] = UID; 
    $where['fid'] = 0;
    $where['status'] = 1;
    $where['ORDER'] = ['weight'=>'ASC'];
    if(!is_login()){
        $where['property'] = 0;
    }
    //查找一级分类
    $content = ['cid(id)','name','property','font_icon','icon','description'];
    $category_parent = select_db('user_categorys',$content,$where);
    //查找二级分类
    $categorys = [];
    if($layer === true){
        foreach ($category_parent as $key => $category) {
            $where['fid'] = $category['id'];
            $category_subitem = select_db('user_categorys',$content,$where);
            $category['subitem_count'] = count($category_subitem);
            $category['subitem'] = $category_subitem;
            array_push($categorys,$category);
        }
    }else{
        foreach ($category_parent as $key => $category) {
            $where['fid'] = $category['id'];
            $category_subitem = select_db('user_categorys',$content,$where);
            $category['subitem_count'] = count($category_subitem);
            array_push($categorys,$category);
            $categorys = array_merge ($categorys,$category_subitem);
        }
    }
    return $categorys;
}