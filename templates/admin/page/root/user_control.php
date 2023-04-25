<?php 
if($USER_DB['UserGroup'] != 'root'){$content='您没有权限访问此页面'; require(DIR.'/templates/admin/page/404.php');exit;}
$title='用户管理'; 
require(dirname(__DIR__).'/header.php');
$user_groups = select_db('user_group',['id','code','name'],'');
?>
<body>
<div class="layuimini-container">
    <div class="layuimini-main">
        <div class="layui-inline layui-form" style="padding-bottom: 5px;">
            <label class="layui-form-label " style="width:60px;padding-left: 5px;padding-right: 5px;">用户组:</label>
            <div class="layui-input-inline" style=" width: 150px; ">
                <select id="UserGroup" name="UserGroup" >
                    <option value="" selected>全部</option>
                    <option value="default">默认</option>
<?php foreach ($user_groups as $data){echo "                    <option value=\"{$data['code']}\">{$data['name']}</option>\n";} ?>
                </select>
            </div>
        </div>
        
        <div class="layui-inline layui-form" style="padding-bottom: 5px;">
            <label class="layui-form-label layui-hide-sm" style="width:60px;padding-left: 5px;padding-right: 5px;">关键字:</label>
            <div class="layui-input-inline">
                <input class="layui-input" name="keyword" id="keyword" placeholder='请输入账号/邮箱/注册IP' value=''autocomplete="off" >
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
            <button class="layui-btn layui-btn-sm layui-btn-danger" lay-event="Del">删除</button>
            <button class="layui-btn layui-btn-sm" lay-event="register" <?php  echo $global_config['RegOption'] == 0? 'style = "display:none;"':'' ?> >注册账号</button>
            <button class="layui-btn layui-btn-sm" lay-event="set_UserGroup">设用户组</button>
            <button class="layui-btn layui-btn-sm" lay-event="username_retain">账号保留</button>
        </div>
</script>
<!-- 操作列 -->
<script type="text/html" id="line_tool">
    <a class="layui-btn layui-btn-normal layui-btn-xs" lay-event="set_pwd">改密</a>
</script>
<script src = "<?php echo $libs;?>/jquery/jquery-3.6.0.min.js"></script>
<script src = "<?php echo $libs?>/jquery/jquery.md5.js"></script>
<script src = "./templates/admin/js/public.js?v=<?php echo $Ver;?>"></script>
<?php load_static('js');?>
<script>
layui.use(['table','layer','form'], function () {
    var $ = layui.jquery;
    var form = layui.form;
    var table = layui.table;
    var layer = layui.layer;
    var limit = localStorage.getItem(u + "_limit") || 50;
    var api = get_api('read_user_list','list');
    var IDs = [];
    
    var cols = [[
      {type:'checkbox'} //开启复选框
      ,{field:'ID',title:'ID',width:60,sort:true,event:'login_entry',style:'cursor: pointer;color: #3c78d8;'}
      ,{title:'操作',toolbar:'#line_tool',width:70}
      ,{field:'User',title:'账号',minWidth:120,sort:true,templet:function(d){
          return '<a style="color:#3c78d8" title="打开用户主页" target="_blank" href="./?u='+d.User+'">'+d.User+'</a>'
      }}
      ,{field:'UserGroupName',title:'用户组',minWidth:90,sort:true}
      ,{field:'Email',title:'Email',minWidth:170,sort:true,event:'set_email',style:'cursor: pointer;color: #3c78d8;'}
      ,{field:'RegIP',title:'注册IP',minWidth:140,sort:true,templet:function(d){
          return '<a style="color:#3c78d8" title="查询归属地" target="_blank" href="//ip.rss.ink/result/'+d.RegIP+'">'+d.RegIP+'</a>'
      }}
      ,{field:'RegTime',title: '注册时间',minWidth:170,sort:true,templet:function(d){
          return d.RegTime == null ? '' : timestampToTime(d.RegTime);
      }} 
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
        var UserGroup = document.getElementById("UserGroup").value;
        var keyword = document.getElementById("keyword").value;
        table.reload('table', {
            url: api
            ,method: 'post'
            ,request: {
                pageName: 'page' //页码的参数名称
                ,limitName: 'limit' //每页数据量的参数名
            }
            ,where: {query:keyword,UserGroup:UserGroup}
            ,page: {curr: 1}
        });
    }
    //工具栏
    table.on('toolbar(table)', function (obj) {
        var event = obj.event;
        if (event == 'register') {
            window.open('./index.php?c=<?php echo $global_config['Register'];?>');
            return;
        }else if(event == 'username_retain'){
            index = layer.open({type: 1,scrollbar: false,shadeClose: true,title: '账号保留',area : ['100%', '100%'],content: $('.username_retain')});
            
            $.post(get_api('other_root','read_username_retain'),function(data,status){
                if(data.code == 1) {
                    form.val('username_retain', {"username_retain": data.data});
                }else{
                    layer.msg(data.msg, {icon: 5});
                }
            });
            return;
        }
        
        var checkStatus = table.checkStatus(obj.config.id);
        if( checkStatus.data.length == 0 && ['LAYTABLE_COLS','LAYTABLE_EXPORT','LAYTABLE_PRINT'].indexOf(event) == -1 ) {
            layer.msg('未选中任何数据！');
            return;
        }
        //取被选中的链接ID
        tableIds = checkStatus.data.map(function (value) {return value.ID;});
        tableIds = JSON.stringify(tableIds);
        table_Users = checkStatus.data.map(function (value) {return value.User;});
        console.log(event);
        //删除
        if(event == 'Del'){
            layer.alert("您将删除以下账号:<br />"+table_Users,{icon:3,title:'确认操作',anim: 2,closeBtn: 0,btn: ['确定','取消']},function () {
                $.post(get_api('write_user_info','Del'),{ID:tableIds},function(data,status){
                    if(data.code == 1){
                        search();
                        layer.msg(data.msg,{icon: 1})
                    } else{
                        layer.msg(data.msg,{icon: 5});
                    }
                });
            });
        
        //设用户组
        }else if(event == 'set_UserGroup'){
            IDs = tableIds;
            index = layer.open({type: 1,scrollbar: false,shadeClose: true,title: '修改用户组',area : ['100%', '100%'],content: $('.set_UserGroup')});
        }
    });
    //行工具
    table.on('tool(table)', function (obj) {
        var data = obj.data;
        if (obj.event == 'set_pwd') {
            layer.prompt({formType: 3,value: '',title: '请输入新密码'}, function(value, index, elem){
                $.post(get_api('write_user_info','set_pwd'),{ID:data.ID,new_pwd:$.md5(value)},function(data,status){
                    if(data.code == 1) {
                        layer.close(index);
                        layer.msg(data.msg, {icon: 1});
                    }else{
                        layer.msg(data.msg, {icon: 5});
                    }
                });
            });
        }else if(obj.event == 'set_email'){
            layer.prompt({formType: 3,value: '',title: '请输入新邮箱'}, function(value, index, elem){
                $.post(get_api('write_user_info','set_email'),{ID:data.ID,new_email:value},function(data,status){
                    if(data.code == 1) {
                        layer.close(index);
                        table.reload('table');
                        layer.msg(data.msg, {icon: 1});
                    }else{
                        layer.msg(data.msg, {icon: 5});
                    }
                });
            });
        }else if(obj.event == 'login_entry'){
            window.open('./index.php?c=' + data.Login + '&u=' + data.User);
        }else if(obj.event == 'homepage'){
            window.open('./index.php?&u=' + data.User);
        }else if(obj.event == 'ip'){
            query_ip(data.RegIP);
        }
    });
    //关闭按钮
    $(document).on('click', '#close', function() {
        layer.close(index);//关闭当前页
    });
    
    //保存用户组
    form.on('submit(save_UserGroup)', function (data) {
        var UserGroupCode = $("#New_UserGroup").val();
        $.post(get_api('write_user_info','set_UserGroup'),{ID:IDs,UserGroup:UserGroupCode},function(data,status){
            if(data.code == 1) {
                table.reload('table');
                layer.close(index);
                layer.msg(data.msg, {icon: 1});
            }else{
                layer.msg(data.msg, {icon: 5});
            }
        });
        return false;
    });
    //保存账号保留
    form.on('submit(save_username_retain)', function (data) {
        $.post(get_api('other_root','write_username_retain'),data.field,function(data,status){
            if(data.code == 1) {
                layer.msg(data.msg, {icon: 1});
            }else{
                layer.msg(data.msg, {icon: 5});
            }
        });
        return false;
    });
    
    
    
});
</script>
<ul class="set_UserGroup" style = "margin-top:18px;display:none;padding-right: 10px;" >
    <form class="layui-form layuimini-form" lay-filter="set_UserGroup">
        
        <div class="layui-inline layui-form" style="padding-bottom: 10px;">
            <label class="layui-form-label">新用户组:</label>
            <div class="layui-input-block" >
                <select id="New_UserGroup" name="New_UserGroup" lay-verify="required" lay-reqtext="请选择用户组">
                    <option value="" selected>请选择</option>
                    <option value="root">站长</option>
                    <option value="default">默认</option>
<?php foreach ($user_groups as $data){echo "                    <option value=\"{$data['code']}\">{$data['name']}</option>\n";} ?>
                </select>
            </div>
        </div>

        <div class="layui-form-item">
            <div class="layui-input-block">
                <button class="layui-btn layui-btn-warm" type="button" id="close" >关闭</button>
                <button class="layui-btn layui-btn-normal" lay-submit lay-filter="save_UserGroup" id ='save_UserGroup'>保存</button>
            </div>
        </div>
  </form>
</ul>

<ul class="username_retain" style="margin-left: 10px;padding-right: 10px;margin-top:18px;display:none;" >
    <form class="layui-form layuimini-form layui-form-pane" lay-filter="username_retain">
        
        <div class="layui-form-item layui-form-text">
            <label class="layui-form-label required">账号保留 - 正则表达式匹配</label>
            <div class="layui-input-block">
                <textarea name="username_retain" class="layui-textarea"></textarea>
            </div>
        </div>
        <pre class="layui-code" >
使用举例:
/^(root|data)$/ 匹配用户等于root或data 区分大小写!
/^(root|data)$/i 匹配用户等于root或data 不区分大小写!
/root|data/ 匹配用户含有root或data 区分大小写!
/root|data/i 匹配用户含有root或data 不区分大小写!
/^admin.+/ 匹配admin开头的任意用账号,但不匹配admin
/^admin.*/ 同上,但匹配admin本身
支持多行,一行一条规则!

举例中的表达式解释:
^ 匹配开头位置
$ 匹配结尾位置
| 或者
. 匹配换行符以外的任何字符
+ 匹配前一个字符一次或多次
* 匹配前一个字符零次或多次
更多语法请自行百度

注:错误的规则可能会造成程序异常,如需帮助请联系技术支持QQ:271152681或技术交流群695720839
        </pre>
        <div class="layui-form-item">
            <div class="layui-input-block">
                <button class="layui-btn layui-btn-warm" type="button" id="close" >关闭</button>
                <button class="layui-btn layui-btn-normal" lay-submit lay-filter="save_username_retain" id ='save_username_retain'>保存</button>
            </div>
        </div>
  </form>
</ul>
</body>
</html>