<?php 
if($USER_DB['UserGroup'] != 'root'){$content='您没有权限访问此页面'; require(DIR.'/templates/admin/page/404.php');exit;}
$title='站长工具'; 
session_start();
$_SESSION['phpinfo_id'] = Get_Rand_Str(8);
if(function_exists("opcache_reset")){
    opcache_reset(); //清理PHP缓存
}
require(dirname(__DIR__).'/header.php');
?>
<style>
.layui-code {
    border: 1px solid #ff5722;
}
.layui-btn{border-width: 1px; border-style: solid; border-color: #FF5722!important; color: #FF5722!important;background: none;height: 30px; line-height: 30px; padding: 0 10px; font-size: 12px;}
</style>
<body>
<div class="layuimini-container">
    <div class="layuimini-main">
        <div class="layui-btn-container">
            <button type="button" class="layui-btn copy_log">复制内容</button>
            <button type="button" class="layui-btn diagnose">一键诊断</button>
            <button type="button" class="layui-btn connectivity_test">连通测试</button>
            <button type="button" class="layui-btn phpinfo">phpinfo</button>
<?php if(preg_match('/nginx/i',$_SERVER['SERVER_SOFTWARE']) ){ ?>
            <button type="button" class="layui-btn rewrite">生成伪静态</button>
<?php } ?>
            <button type="button" class="layui-btn db_upgrade">数据库升级</button>
            <button type="button" class="layui-btn clear CleanCache">清理缓存</button>
            <button type="button" class="layui-btn" layuimini-content-href="root/sys_log" data-title="系统日志">系统日志</button>
            <button type="button" class="layui-btn" layuimini-content-href="updatelog" data-title="更新日志">更新日志</button>
            <button type="button" class="layui-btn" layuimini-content-href="root/import_data" data-title="导入数据">导入数据</button>
        </div>
        <pre class="layui-code" id="console_log" >
1.功能都集中在上方的按钮了,需要那个就点击那个!
2.一键诊断和phpinfo用于帮助站长和开发者快速了解服务器环境
3.生成伪静态(仅针对Nginx,安全配置必选,其他规则按需,Apache已内置规则无需设置,其他环境不支持)
4.数据库升级: 手动安装更新时,若有说明需更新数据库,则需要手动点击此按钮!自动更新时无需干预,特殊情况除外!
5.清理缓存: 用于清理特定情况下产生的临时数据 (仅清理60分钟前的数据)
        产生原因1:用户在添加链接页面上传了图标且未点击删除图标或添加链接,而是直接关闭了页面
        产生原因2:用户在导出导入页面上传了数据,但未点击导入,而是直接关闭了页面!
        产生原因3:其他涉及文件操作时异常中断导致
6.系统日志: 目前支持查询用户登录日志/注册日志!支持搜索账号/IP/描述
        </pre>
    </div>
</div>


<script src = "<?php echo $libs;?>/jquery/jquery-3.6.0.min.js?v=<?php echo $Ver;?>"></script>
<script src = "./templates/admin/js/public.js?v=<?php echo $Ver;?>"></script>
<script src = "<?php echo $libs?>/Other/ClipBoard.min.js?v=<?php echo $Ver;?>"></script>
<script src = '<?php echo $libs?>/jquery/jquery.md5.js?v=<?php echo $Ver;?>'></script>
<?php load_static('js');?>
<script>
layui.use(['layer','form','miniTab'], function () {
    var $ = layui.jquery;
    var form = layui.form;
    var isSupported = ClipboardJS.isSupported();
    var miniTab = layui.miniTab;
    miniTab.listen();
    //复制日志
    $('.copy_log').on('click', function(){
        if(isSupported){
            ClipboardJS.copy($('#console_log').text());
            layer.msg('复制成功', {icon: 1});
        }else{
            layer.msg('复制失败,浏览器不支持', {icon: 5});
        }
    });
    //一键诊断
    $('.diagnose').on('click', function(){
        $("#console_log").text("");
        $("#console_log").append("浏览器UA：" + navigator.userAgent +"\n");
        $("#console_log").append("客户端时间：" +  timestampToTime(Math.round(new Date() / 1000) ) +"\n");
        $.post(get_api('read_data','diagnostic_log'),function(data,status){
            $("#console_log").append(data.msg);
        });
    });
    
    //连通测试
    $('.connectivity_test').on('click', function(){
        $("#console_log").text("");
        $("#console_log").append("浏览器UA：" + navigator.userAgent +"\n");
        $("#console_log").append("客户端时间：" +  timestampToTime(Math.round(new Date() / 1000) ) +"\n");
        
        var urls = [
          ['主线路', 'https://update.lm21.top/connectivity_test.txt'],
          ['备用线路(Gitee)', 'https://gitee.com/tznb/twonav_updata/raw/master/connectivity_test.txt']
        ];
        urls.forEach(function(route) {
          var routeName = route[0];
          var url = route[1];
          $("#console_log").append("正在检测: " + routeName +"\n");
          $.ajax({
            url: get_api('read_data', 'connectivity_test'),
            type: 'POST',
            data: { url: url },
            async: false,
            success: function(data, status) {
              $("#console_log").append(data.msg + "\n");
            },
            error: function(jqXHR, textStatus, errorThrown) {
              $("#console_log").append(routeName + ": 请求 " + url + " 发生错误：" + errorThrown + "\n");
            }
          });
        });
    });
    //phpinfo
    $('.phpinfo').on('click', function(){
        index = layer.prompt({formType: 1,value: '',title: '输入登录密码:',shadeClose: false,"success":function(){
            $("input.layui-layer-input").on('keydown',function(e){if(e.which == 13) {echo_phpinfo();}});
        }},function(){
            echo_phpinfo()
        }); 
    });
    
    function echo_phpinfo(){
        let p = $("input.layui-layer-input").val();
        if(p == ''){ return false;}
        layer.close(index);
        layer.open({
            title: 'phpinfo',
            type: 2,
            scrollbar: false,
            shade: 0.2,
            maxmin:false,
            shadeClose: true,
            area: ['100%', '100%'],
            content: get_api('read_data','phpinfo')+'&p='+$.md5(p)+'&pid=<?php echo $_SESSION['phpinfo_id'] ;?>'
        });
    }
    //伪静态
    $('.rewrite').on('click', function(){
        let pathname = window.location.pathname;
        $("#console_log").text("");
        //$("#console_log").append(`#更新时间: 2023.09.05\n`);
        //$("#console_log").append(`#安全规则(必选)\n`);
        //$("#console_log").append(`location ^~ ${pathname}data/ {location ~* \\.(db|db3|php|sql|tar|gz|zip|info|log|json)$ {return 403;}}\n`);
        //$("#console_log").append(`location ^~ ${pathname}templates/ {location ~* \\.(php|tar|gz|zip|info|log|json)$ {return 403;}}\n`);
        //$("#console_log").append(`#重写规则(可选)\n`);
        //$("#console_log").append(`rewrite ^${pathname}login$ ${pathname}index.php?c=login break;\n`);
        //$("#console_log").append(`rewrite ^${pathname}admin$ ${pathname}index.php?c=admin break;\n`);
        //$("#console_log").append(`rewrite ^${pathname}ico/(.+) ${pathname}index.php?c=icon&url=$1 break;\n`);
        //$("#console_log").append(`rewrite ^${pathname}([A-Za-z0-9]+)$ ${pathname}index.php?u=$1 break;\n`);
        //$("#console_log").append(`rewrite ^${pathname}([A-Za-z0-9]+)\\.html$ ${pathname}index.php?u=$1 break;\n`);
        //$("#console_log").append(`rewrite ^${pathname}(.+)/(click|article)/([A-Za-z0-9]+)$ ${pathname}index.php?c=$2&id=$3&u=$1 break;\n`);
        //$("#console_log").append(`rewrite ^${pathname}(.+)/(click|article)/([A-Za-z0-9]+)\\.html$ ${pathname}index.php?c=$2&id=$3&u=$1 break;\n`);
        ////路径修正(解决使用伪静态链接访问时路径错误的问题)
        //$("#console_log").append(`rewrite ^${pathname}(.+)/(click|article)/(templates|static|data|system)/(.+) ${pathname}$3/$4 break;\n`);
        //$("#console_log").append(`rewrite ^${pathname}(.+)/(click|article)/favicon\\.ico ${pathname}favicon.ico break;\n`);
        //$("#console_log").append(`#站点地图(可选)\n`);
        //$("#console_log").append(`rewrite ^${pathname}sitemap.xml$ ${pathname}index.php?c=sitemap break;\n`);
        
        $("#console_log").append(`#安全规则(必选)\n`);
        $("#console_log").append(`location ^~ ${pathname}data/ {location ~* \\.(db|db3|php|sql|tar|gz|zip|info|log|json)$ {return 403;}}\n`);
        $("#console_log").append(`location ^~ ${pathname}templates/ {location ~* \\.(php|tar|gz|zip|info|log|json)$ {return 403;}}\n`);
        if(pathname == '/'){
            $("#console_log").append(`#重写规则(可选)\n`);
            $("#console_log").append(`location / {\n    if ($uri ~* ^/index\.php$) { break; }\n    if ($uri ~* ^/(templates|static|data|system)/) { break; }\n    try_files $uri $uri/ /rewrite.php?$query_string;\n}\n`);
            $("#console_log").append(`rewrite ^/[a-zA-Z0-9]+/[a-zA-Z]+/(templates|static|data|system)/(.+) /$1/$2 break;\n`);
            $("#console_log").append(`rewrite ^/[a-zA-Z0-9]+/[a-zA-Z]+/favicon\\.ico /favicon.ico break;\n`);
        }else{
            $("#console_log").append(`#检测到程序运行在非根目录,此环境仅提供安全规则!部分与伪静态相关的功能将不可用!\n`);
        }

    });
    //清理缓存
    $('.CleanCache').on('click', function(){
        $.post(get_api('other_root','CleanCache'),function(data,status){
             if(data.code == 1){
                layer.msg(data.msg,{icon: 1})
            } else{
                layer.msg(data.msg,{icon: 5});
            }
        });
    });
    //数据库升级
    $('.db_upgrade').on('click', function(){
        $("#console_log").text("");
        $("#console_log").append(`正在处理中,请勿操作页面...\n`);
        $.post(get_api("other_upsys"),{"i":4,"pattern":"manual"}, function(data, status) {
            $("#console_log").text("");
            $("#console_log").append(`${data.msg}\n`);
        });
    });
});
</script>
</body>
</html>