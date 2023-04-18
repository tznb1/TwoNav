<?php 
if($global_config['guestbook'] != 1 || !check_purview('guestbook',1)){
    require(DIR.'/templates/admin/page/404.php');
    exit;
}

$s = unserialize( get_db("user_config", "v", ["k" => "guestbook","uid"=>UID]) );
if(empty($s)){
    $s = [];
}
if(!Check_Path("data/user/{$u}/MessageBoard")){
    exit("<h2>创建目录失败,请检查权限</h2>");
}
$dir = DIR."/data/user/{$u}/MessageBoard/";

if($_POST['type'] == 'set'){
    $s['allow'] = $_POST['set'];
    write_user_config('guestbook',$s,'config','留言板配置');
    msg(1,'操作成功');
}elseif($_POST['type'] == 'del'){
    if($_POST['name'] == 'help'){
        $s['help'] = 'del';
        write_user_config('guestbook',$s,'config','留言板配置');
        msg(1,'删除成功');
    }
    //文件名检测
    if( !preg_match_all('/^\d+_\d+\.json$/',$_POST['name']) ) {
        msg(-1,'数据库名称不合法！');
    }
    $path = DIR."/data/user/{$u}/MessageBoard/".$_POST['name'];
    if(!file_exists($path)){
        msg(-1,'文件不存在');
    }else if(unlink($path)){
        msg(1,'删除成功');
    }else{
        msg(-1,'删除失败');
    }
}


$dbs = scandir($dir); 
$newdbs = $dbs;
//列表过滤
for ($i=0; $i < count($dbs); $i++) { 
    if( ($dbs[$i] == '.') || ($dbs[$i] == '..') || ( substr($newdbs[$i], -5) != '.json') ) {
        unset($newdbs[$i]);
    }
}

$dbs = $newdbs; //赋值过滤后的数据
$num = count($dbs); //取列表数
rsort($dbs,2); //按时间从大到小重排序

$data = [];
//符合条件时显示使用说明
if($s['help'] != 'del'|| !count($dbs) ||  isset($_GET['help'])){
    $id = 1; 
    $arr['type'] = '使用说明'; 
    $arr['contact'] = '271152681@qq.com';
    $arr['title'] = 'TwoNav 极简留言板';
    $arr['content'] = "1.极简留言板采用轻量设计,整体只有几KB\n2.留言数据存放路径/data/user/xxx/MessageBoard/ (xxx表示用户名)\n3.默认是禁止留言的,点击上方蓝色字(禁止留言/允许留言)可切换状态\n4.使用方法: 点击极简留言板(蓝字)>把地址栏的URL复制>在后台添加链接即可(部分主题已支持自动展现入口)\n5.本条信息被删除时如果存在留言则不显示,没有留言时依旧会显示!\n6.有提交长度限制,类型32,联系方式64,标题128,内容2048字节!若不够用请自己修改源代码!\n7.为了防止被恶意提交,当留言数超过256时将不在接收留言!";
    $arr['time'] = date("Y-m-d H:i:s",time());
    $arr['ip'] = '127.0.0.1';
    $arr['id'] = $id;
    $arr['file'] = 'help';
    $data['help'] = $arr;
}else{
    $id = 0;
}

//遍历数据库，获取时间，大小
foreach ($dbs as $key => $value) {
    $id ++;
    $arr['id'] = $id;
    try{ //读取信息文件
        $info_file = @file_get_contents($dir.$value);
        $info = json_decode($info_file,true);
        $arr['type'] = $info['type']; //类型
        $arr['contact'] = $info['contact']; //联系方式
        $arr['title'] = $info['title']; //标题
        $arr['content'] = $info['content']; //内容
        $arr['time'] = date("Y-m-d H:i:s",$info['time']); //提交时间
        $arr['ip'] = $info['ip']; //ip
        $arr['file'] = $value; //MD5
    }catch (\Throwable $th) {
        $arr['type'] = 'Null'; 
        $arr['contact'] = 'Null';
        $arr['title'] = 'Null';
        $arr['content'] = 'Null';
        $arr['time'] = 'Null';
        $arr['ip'] = 'Null';
        $arr['file'] = $value;
    }
    $data[$key] = $arr;
}
$show = 5; //展开的数量

$title='留言管理';require dirname(__DIR__).'/header.php';
?>
  <style>    
    a{color:blue;}
    p{word-break: break-all;white-space:normal;word-wrap: break-word;}
  </style>
<body>
<div class="layuimini-container">
    <div class="layuimini-main">
        <fieldset class="layui-elem-field layui-field-title" style="margin-top: 30px;"><legend><a style="cursor:pointer;" title="点击打开客户留言页面" rel = "nofollow" href="./?c=guestbook&u=<?php echo U;?>" target="_blank">TowNav 极简留言板</a></legend></fieldset>
        <fieldset class="layui-elem-field layui-field-title" style="margin-top: 30px;">
            <legend>当前设置:<a style="cursor:pointer;" title="点击切换" rel = "nofollow" onclick = "set('<?php echo $s['allow']== '1'?'0':'1';?>')"><?php echo $s['allow']== '1'?'允许留言':'禁止留言';?></a>
          </legend>
        </fieldset>
        <div class="layui-collapse" lay-filter="test">
<?php foreach ( $data as $value ) { ?>
            <div class="layui-colla-item">
                <h2 class="layui-colla-title"><?php echo $value['id'] .'.&nbsp;[&nbsp;'. $value['type'] .'&nbsp;]&nbsp;[&nbsp;'. $value['title'].'&nbsp;]'; ?>&emsp;
                    <a style="cursor:pointer;"  rel = "nofollow" onclick = "del('<?php echo $value['file'] ?>')">删除</a>
                </h2>
                <div class="layui-colla-content <?php  if( $value['id'] <= $show ){echo 'layui-show';} ?>">
                    <p><?php echo '提交时间: '. $value['time'] .'<br />终端地址: '. $value['ip'] .'<br />联系方式: '. $value['contact'] .'<br />  <br />'. str_replace("\n","<br />",str_replace(" ","&nbsp;",$value['content'])) ; ?></p>
                </div>
            </div>
<?php } ?>
        </div>
<?php if(empty($dbs)){echo '        <fieldset class="layui-elem-field layui-field-title" style="margin-top: 30px;"><legend> 当前没有留言 </legend></fieldset>';}?>
    </div>
</div>

<script src = '<?php echo $libs?>/jquery/jquery-3.6.0.min.js'></script>
<script src = "./templates/admin/js/public.js?v=<?php echo $Ver;?>"></script>
<?php load_static('js.layui');?>
<script>

layui.use(['layer','element'], function(){
    var layer = layui.layer;
});

function del(name) {
    $.post('',{'type':'del','name':name},function(data,status){
        if(data.code == 1) {
            layer.msg("删除成功", {icon: 1});
            setTimeout(() => {location.reload();}, 500);
        }else{
            layer.msg(data.msg, {icon: 5});
        }
    });
}
   
function set(key){
    $.post('',{'type':'set','set':key},function(data,status){
        if(data.code == 1) {
            location.reload();
        }else{
            layer.msg(data.msg, {icon: 5});
        }
    });
}
</script>
</body>
</html>