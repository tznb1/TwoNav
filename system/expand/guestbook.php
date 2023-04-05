<?php 
if($global_config['guestbook'] != 1 || !check_purview('guestbook',1)){
    require DIR.'/templates/admin/page/404.php';
    exit;
}

$s = unserialize( get_db("user_config", "v", ["k" => "guestbook","uid"=>UID]) );
if(empty($s) || $s['allow'] != 1){
    $content = '站点已设置禁止留言';
    require DIR.'/templates/admin/page/404.php';
    exit;
}
if(!Check_Path("data/user/{$u}/MessageBoard")){
    exit("<h2>创建目录失败,请检查权限</h2>");
}

//POST提交留言
if($_SERVER['REQUEST_METHOD'] === 'POST'){
     if($s['allow'] != '1'){ msg(-1015,'提交失败,当前禁止留言!');  }
     $type = $_POST['type']; //类型
     $contact = $_POST['contact']; //联系方式
     $title = $_POST['title']; //标题
     $content = $_POST['content']; //内容
     if(empty($type)){
         msg(-1015,'提交失败,类型不能为空');
     }elseif(empty($contact)){
         msg(-1015,'提交失败,联系方式不能为空');
     }elseif(empty($title)){
         msg(-1015,'提交失败,标题不能为空');
     }elseif(empty($content)){
         msg(-1015,'提交失败,内容不能为空');
     }elseif(strlen($type) >= 32 || strlen($contact) >= 64 || strlen($title) >= 128 || strlen($content) >= 2048){
         msg(-1015,'提交失败,长度超限');
     }elseif(ShuLiang("data/user/{$u}/MessageBoard/") > 256){
         msg(-1015,'提交失败,留言太多了请稍后再试');
     }
     
     $json_arr = array(
         'type'=>htmlentities($type),
         'contact'=>htmlentities($contact),
         'title'=>htmlentities($title),
         'content'=>htmlentities($content),
         'time'=>time(),
         'ip'=>get_IP()
         );
         //限制长度 参数
     //var_dump($json_arr);exit;
     $json = json_encode($json_arr);
     $path = "data/user/{$u}/MessageBoard/".time().'_'.crc32($json).'.json';
     if( Check_Path("data/user/{$u}/MessageBoard") && file_put_contents($path, $json)){
        msg(0,'提交成功'); 
    }else{
        msg(-1015,'系统错误,提交失败'); //创建目录或写入文件失败,请检查权限
    }
 } 

//获取文件数
function ShuLiang($path){
    $sl=0;
    $arr = glob($path);
    foreach ($arr as $v){
        if(is_file($v)){
            $sl++;
        }else{
            $sl+=ShuLiang($v."/*");
        }
    }
    return $sl;
}
require DIR.'/templates/admin/page/expand/guestbook-user.php';
exit;