<?php 
if($USER_DB['UserGroup'] != 'root'){$content='您没有权限访问此页面'; require(DIR.'/templates/admin/page/404.php');exit;}
$title='用户分组'; 
require(dirname(__DIR__).'/header.php');
?>
<body>
<div class="layuimini-container">
    <div class="layuimini-main">
        <table id="table" class="layui-table" lay-filter="table" style="margin: 1px 0;"></table>
    </div>
</div>
<script type="text/html" id="tool">
        <div class="layui-btn-group">
            <button class="layui-btn layui-btn-sm layui-btn-normal" lay-event="load">刷新</button>
            <button class="layui-btn layui-btn-sm layui-btn-normal" lay-event="add">新增</button>
        </div>
</script>
<!-- 操作列 -->
<script type="text/html" id="TableBar">
    <a class="layui-btn layui-btn-normal layui-btn-xs" lay-event="edit">编辑</a>
    <a class="layui-btn layui-btn-danger layui-btn-xs" lay-event="del">删除</a>
</script>
<ul class="add" style = "margin-top:18px;display:none;padding-right: 10px;" >
    <form class="layui-form layuimini-form" lay-filter="add">
        
        <div class="layui-form-item">
            <label class="layui-form-label required">分组名称</label>
            <div class="layui-input-block">
                <input type="text" name="name" id="name" lay-verify="required" lay-reqtext="分组名称不能为空" placeholder="请输入分组名称"  class="layui-input">
            </div>
        </div>
        
        <div class="layui-form-item" id="fcode">
            <label class="layui-form-label required">分组代号</label>
            <div class="layui-input-block">
                <input type="text" name="code" id="code" lay-verify="required|code" lay-reqtext="代号不能为空" placeholder="请输入代号(字母和数字)"  class="layui-input">
            </div>
        </div>
        
        <div class="layui-form-item">
            <label class="layui-form-label">可用功能</label>
            <div class="layui-input-block">
                <input type="text" name="allow_list" id="allow_list"  readonly="readonly" placeholder="点击弹出选择列表" class="layui-input">
            </div>
        </div>
        
        <div class="layui-form-item" style = "display:none;">
            <label class="layui-form-label">可用代号</label>
            <div class="layui-input-block">
                <input type="text" name="allow_code_list" id="allow_code_list" class="layui-input">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">蓝图用户</label>
            <div class="layui-input-inline">
                <input type="text" name="uname" id="uname" placeholder="填用户名,可留空"  class="layui-input">
            </div>
            <div class="layui-form-mid layui-word-aux">用户注册后初始数据和蓝图用户相同&nbsp;<a href="javascript:;" onclick = "tips('生效条件: 当前用户组作为默认分组时或注册码绑定当前分组<br />用途说明: 用户注册后初始数据和蓝图用户相同!<br />初始数据: 分类/链接/站点设置<br />&emsp;&emsp;&emsp;&emsp;&emsp;登录配置(不含二级密码)<br />&emsp;&emsp;&emsp;&emsp;&emsp;默认模板(不含模板配置)<br />注意事项: 请确保该用户没有隐私数据<br />其他: 留空则以默认设置创建初始数据')" style="color: #e41010;">查看帮助</a></div>
        </div>
        
        <div class="layui-form-item">
            <div class="layui-input-block">
                <button class="layui-btn layui-btn-warm" type="button" id="close" >关闭</button>
                <button class="layui-btn layui-btn-normal" lay-submit lay-filter="edit" id ='edit'>保存</button>
                <button class="layui-btn layui-btn-normal" lay-submit lay-filter="add" id ='add'>添加</button>
            </div>
        </div>
  </form>
</ul>
<script src = "<?php echo $libs;?>/jquery/jquery-3.6.0.min.js"></script>
<script src = "./templates/admin/js/public.js?v=<?php echo $Ver;?>"></script>
<?php load_static('js');?>
<script>
layui.use(['table','layer','form','tableSelect'], function () {
    var $ = layui.jquery;
    var form = layui.form;
    var table = layui.table;
    var layer = layui.layer;
    var tableSelect = layui.tableSelect;
    var api = get_api('read_users_list');
    var code = [];
    
    var cols = [[
       //{field:'id',title:'id',width:80,sort:true},
      {field:'code',title:'代号',width:120}
      ,{field:'name',title:'名称',width:120}
      ,{title:'操作',width:120,toolbar:'#TableBar'}
      ,{field:'allow',title:'可用'}
      ,{field:'disable',title:'禁用'}
      
    ]]
    //用户表渲染
    table.render({
        elem: '#table'
        ,height: 'full-60' //自适应高度
        ,url: api
        ,page: false //开启分页
        ,even:true //隔行背景色
        ,loading:true //加载条
        ,toolbar: '#tool'
        ,id:'table'
        ,method: 'post'
        ,response: {statusCode: 1 } 
        ,cols: cols
        ,done: function (res, curr, count) {
            //$("[data-field='code']").addClass('layui-hide-xs');
            $("[data-field='allow']").addClass('layui-hide-xs');
            $("[data-field='disable']").addClass('layui-hide-xs');
            //渲染完毕回调
        }
    });
    
    //自定义表单验证
    form.verify({
        code: function(value, item){
            if(!(/^[a-z0-9]+$/i.test(value))){return '分组代号只能是字母和数字';}
        }
    });      


    //工具栏
    table.on('toolbar(table)', function (obj) {
        var event = obj.event;
        if (event == 'load') {
            //window.location.reload();
            table.reload('table');
            return;
        }else if(event == 'add'){
            $("#edit").css('display','none');
            $("#add").css('display','inline');
            $('#allow_list').attr( "ts-selected",'');//清空初始选中
            $('#code').removeAttr( "readonly");//移除禁止编辑属性
            $("#fcode").css('display','block');//显示代号
            form.val('add', {"code": '',"allow_list": '' ,"name": '','allow_code_list':'[]','uname':'' });
            index = layer.open({type: 1,scrollbar: false,shadeClose: true,title: '新增分组',area : ['100%', '100%'],content: $('.add')});
        }
    });
    //行工具
    table.on('tool(table)', function (obj) {
        var data = obj.data;
        if (obj.event === 'edit') {
            $("#add").css('display','none');
            $("#edit").css('display','inline');
            $('#allow_list').attr( "ts-selected",data.allow);//赋值初始选中
            $('#code').attr( "readonly","readonly");//禁止编辑代号
            $("#fcode").css('display','none');//隐藏代号
            form.val('add', {"code": data.code,"allow_list": data.allow ,"name": data.name,'allow_code_list':JSON.stringify(data.codes),'uname':data.uname});
            index = layer.open({type: 1,scrollbar: false,shadeClose: true,title: '编辑分组',area : ['100%', '100%'],content: $('.add')});
        }else if(obj.event === 'del'){
            layer.confirm('确认删除?',{icon: 3, title:'温馨提示'}, function(index){
                $.post(get_api('write_users','del'),{code:data.code},function(data,status){
                    if(data.code == 1) {
                        table.reload('table');
                        layer.msg(data.msg, {icon: 1});
                    }else{
                        layer.msg(data.msg, {icon: 5});
                    }
                });
            });
        }
    });
    
    //关闭按钮
    $(document).on('click', '#close', function() {
        layer.close(index);//关闭当前页
    });
    //添加
    form.on('submit(add)', function (data) {
        $.post(get_api('write_users','add'),data.field,function(data,status){
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
    //保存编辑
    form.on('submit(edit)', function (data) {
        $.post(get_api('write_users','edit'),data.field,function(data,status){
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
    tableSelect.render({
        elem: '#allow_list',
        checkedKey: 'name',
        searchKey: 'keyword',
        searchPlaceholder: '关键词搜索',
        height:'400',  //自定义高度
        width:'600',  //自定义宽度
        rowDouble:false, //禁止双击
        table: {
            url:get_api('read_purview_list',''),
            response: {statusCode: 1},
            page: false, //开启分页
            limit:100,
            limits: [20,50,100],
            cols: [[
				{type: 'checkbox' },
				{field:'code',title:'代号',width:120,hide:true},
				{field:'name',title:'名称',width:100},
				{field:'description',title:'描述',width:288}
            ]]},
        done: function (elem, data) {
            var name = [];code = [];
            layui.each(data.data, function (index, item) {
                name.push(item.name);
                code.push(item.code);
            })
            elem.val(name.join(","));
            $('#allow_code_list').val(JSON.stringify(code));
        }
    })

});

function tips(content) {
    layer.open({title:'帮助详情',scrollbar: false,content:content});
}
</script>
</body>
</html>