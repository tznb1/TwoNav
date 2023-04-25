<?php 
if($USER_DB['UserGroup'] != 'root'){$content='您没有权限访问此页面'; require(DIR.'/templates/admin/page/404.php');exit;}
$title='日志'; 
require(dirname(__DIR__).'/header.php');
?>
<body>
<div class="layuimini-container">
    <div class="layuimini-main">
        <div class="layui-inline layui-form" style="padding-bottom: 5px;">
            <label class="layui-form-label " style="width:60px;padding-left: 5px;padding-right: 5px;">日志类型:</label>
            <div class="layui-input-inline" style=" width: 150px; ">
                <select id="RecordType" name="RecordType" >
                    <option value="" selected>全部</option>
                    <option value="login">登录</option>
                    <option value="register">注册</option>
                </select>
            </div>
        </div>
        
        <div class="layui-inline layui-form" style="padding-bottom: 5px;">
            <label class="layui-form-label layui-hide-sm" style="width:60px;padding-left: 5px;padding-right: 5px;">关键字:</label>
            <div class="layui-input-inline">
                <input class="layui-input" name="keyword" id="keyword" placeholder='请输入账号/IP/描述' value=''autocomplete="off" >
            </div>
            
        </div>
        <div class="layui-inline layui-form" style="padding-bottom: 5px;">
            <button class="layui-btn layui-btn-normal " id="search" style="height: 36px;">搜索</button>
        </div>
        <table id="table" class="layui-table" lay-filter="table" style="margin: -3px 0;"></table>
    </div>
</div>

<script type="text/html" id="user_tool">
        <div class="layui-btn-group">
            <button class="layui-btn layui-btn-sm layui-btn-danger" lay-event="refresh">刷新</button>
        </div>
</script>
<!-- 操作列 -->
<script type="text/html" id="TableBar">
    <a class="layui-btn layui-btn-normal layui-btn-xs" lay-event="details">详情</a>
</script>
<script src = "<?php echo $libs;?>/jquery/jquery-3.6.0.min.js"></script>
<script src = "./templates/admin/js/public.js?v=<?php echo $Ver;?>"></script>
<?php load_static('js');?>
<script>
layui.use(['table','layer','form'], function () {
    var $ = layui.jquery;
    var form = layui.form;
    var table = layui.table;
    var layer = layui.layer;
    var limit = localStorage.getItem(u + "_limit") || 50;
    var api = get_api('read_log');
    var IDs = [];
    
    var cols = [[
      //{type:'checkbox'} //开启复选框
      {field:'id',title:'ID',width:60}
      ,{field:'user',title:'账号',width:120,templet:function(d){
          return '<a style="color:#3c78d8" title="用户ID:' + d.uid + ',点击打开用户主页" target="_blank" href="./?u='+d.user+'">'+d.user+'</a>'
      }}
      ,{field:'ip',title:'请求IP',width:140,templet:function(d){
          return '<a style="color:#3c78d8" title="查询归属地"  href="javascript:;" onclick="query_ip(\'' + d.ip +'\')">'+d.ip+'</a>'
      }} 
      ,{field:'time',title: '请求时间',width:170,templet:function(d){
          if(d.time == null){return '';}
          else{return timestampToTime(d.time);}}} 
      ,{field:'description',title:'描述',minWidth:170}
    ]]
    //用户表渲染
    table.render({
        elem: '#table'
        ,height: 'full-110' //自适应高度
        ,url: api
        ,page: true //开启分页
        ,limit:limit  //默认每页显示行数
        ,even:true //隔行背景色
        ,loading:true //加载条
        ,toolbar: '#user_tool'
        ,id:'table'
        ,method: 'post'
        //,defaultToolbar:false
        ,response: {statusCode: 1 } 
        ,cols: cols
        ,done: function (res, curr, count) {
            //获取当前每页显示数量.并写入本都储存
            var temp_limit = $(".layui-laypage-limits option:selected").val();
            if(temp_limit > 0 && localStorage.getItem(u + "_limit") != temp_limit){
                localStorage.setItem(u + "_limit",temp_limit);
            }
            $(".layui-table-tool-self").addClass('layui-hide-xs');//手机端隐藏defaultToolbar
        }
    });
    //关键字回车
    $('#keyword').keydown(function (e){if(e.keyCode === 13){search();}}); 
    //搜索按钮点击
    $('#search').on('click', function(){search();});
    //搜索
    function search(){
        var RecordType = document.getElementById("RecordType").value;
        var keyword = document.getElementById("keyword").value;
        table.reload('table', {
            url: api
            ,method: 'post'
            ,request: {
                pageName: 'page' //页码的参数名称
                ,limitName: 'limit' //每页数据量的参数名
            }
            ,where: {keyword:keyword,RecordType:RecordType}
            ,page: {curr: 1}
        });
    }
    //工具栏
    table.on('toolbar(table)', function (obj) {
        var event = obj.event;
        if (event == 'refresh') {
            table.reload('table');
        }
    });
});
</script>
</body>
</html>