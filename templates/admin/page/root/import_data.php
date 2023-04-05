<?php 
if($USER_DB['UserGroup'] != 'root'){$content='您没有权限访问此页面'; require(DIR.'/templates/admin/page/404.php');exit;}
$title='导入数据'; 
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
            <button type="button" class="layui-btn import_data">开始导入</button>
        </div>
        <pre class="layui-code" id="console_log" >
注意事项: 
1.导入数据目前支持将OneNav Extend导入到TwoNav
2.为了保障导入数据不冲突,导入时若账号已存在TwoNav则会跳过!
3.导入过程请勿操作页面(如点击,刷新,关闭等),以免造成导入中断
4.如果导入意外中断则建议您重装后在重新导入

升级说明:
1.将OneNav Extend导出的数据上传到TwoNav安装目录的data目录下
2.点击开始导入 > 输入文件名 > 确定
3.程序开始解压数据包,并执行导入数据
        </pre>
    </div>
</div>
<script src = "<?php echo $libs;?>/jquery/jquery-3.6.0.min.js"></script>
<script src = "./templates/admin/js/public.js?v=<?php echo $Ver;?>"></script>
<script src = "<?php echo $libs?>/Other/ClipBoard.min.js"></script>
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
    //开始导入
    $('.import_data').on('click', function(){
        index = layer.prompt({formType: 3,value: '',title: '请输入文件名:',shadeClose: false,"success":function(){
            $("input.layui-layer-input").on('keydown',function(e){
                if(e.which == 13) {
                    import_data();
                }
            });
        }},function(){
            import_data();
        });
    });
    
    function import_data(value) {
        if($("input.layui-layer-input").val() == ''){
            return false; 
        }
        $("*").blur();
        $("#console_log").text( timestampToTime(Math.round(new Date() / 1000)) + ": 开始解压数据包...\n");
        let loading = layer.msg('数据处理中,请稍后..', {icon: 16,time: 1000*300,shadeClose: false});
        layer.close(index);
        var datas = {'count':0,'data':[]};
        $.post(get_api('other_root','import_data'),{'step':1,'file':$("input.layui-layer-input").val()},function(data,status){
            $("#console_log").append(timestampToTime(Math.round(new Date() / 1000)) +": "+ data.msg +"\n");
            if(data.code == 1){
                datas = data;
                request_import()
            }else{
                layer.closeAll();
                layer.msg('导入失败', {icon: 5});
                return false; 
            }
        });
        let i =0 ;
        function request_import(){
            if( i >= datas.count){
                $.post(get_api('other_root','import_data'),{'step':3}, function(data, status) {
                    if (data.code == 1) { 
                        layer.close(loading);
                        $("#console_log").append( timestampToTime(Math.round(new Date() / 1000)) + ": " + data.msg + "\n");
                        $("#console_log").append( timestampToTime(Math.round(new Date() / 1000)) + ": 导入完毕!\n");
                        layer.msg('导入完毕', {icon: 1});
                    }else{
                        layer.closeAll();
                        layer.alert(data.msg ?? "未知错误,请联系开发者!",{icon:5,title:'导入失败',anim: 2,shadeClose: false,closeBtn: 0,btn: ['知道了']});
                    } 
                });
                return;
            }else{
                i++;
            }
            let user = datas.data[i-1].name;
            $("#layui-layer"+ loading+" .layui-layer-padding").html('<i class="layui-layer-ico layui-layer-ico16"></i>[ ' + i + ' / ' + datas.count + ' ] 正在导入 ' + user);
            
            $.post(get_api('other_root','import_data'),{'step':2,'user':user,'id':datas.data[i-1].id}, function(data, status) {
                if (data.code == 1) { 
                    $("#console_log").append( timestampToTime(Math.round(new Date() / 1000)) + ": " + data.msg + "\n");
                    request_import();
                }else{
                    layer.closeAll();
                    layer.alert(data.msg ?? "未知错误,请联系开发者!",{icon:5,title:'导入失败',anim: 2,shadeClose: false,closeBtn: 0,btn: ['知道了']});
                } 
            });
        }
    
    }
    
});
</script>
</body>
</html>