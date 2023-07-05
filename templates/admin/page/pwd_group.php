<?php $title='加密分组';$awesome=true; require 'header.php';  ?>
<style>
    .layui-table-tool-temp  {padding-right:1px;}
</style>
<body>
<div class="layuimini-container">
    <div class="layuimini-main">
        <table id="table" class="layui-table" lay-filter="table" style="margin: 1px 0;"></table>
    </div>
</div>
<!-- 操作列 -->
<script type="text/html" id="tablebar">
    <a class="layui-btn layui-btn-normal layui-btn-xs" lay-event="edit">编辑</a>
    <a class="layui-btn layui-btn-danger layui-btn-xs" lay-event="del">删除</a>
</script>
<!-- 表头工具栏 -->
<script type="text/html" id="toolbar">
    <div class="layui-btn-group">
        <button class="layui-btn layui-btn-sm layui-btn-normal" lay-event="add">新增</button>
        <button class="layui-btn layui-btn-sm layui-btn-normal" lay-event="help">使用说明</button>
    </div>
</script>
<script src = "<?php echo $libs;?>/jquery/jquery-3.6.0.min.js"></script>
<script src = "./templates/admin/js/public.js?v=<?php echo $Ver;?>"></script>
<?php load_static('js');?>
<script>
layui.use(['form','table'], function () {
    var $ = layui.jquery;
    var table = layui.table;
    var form = layui.form;
    var api = get_api('read_pwd_group_list'); //列表接口
    var limit = localStorage.getItem(u + "_limit") || 50;
    
    var load_list = function () {
        table.render({
            elem: '#table'
            ,height: 'full-50' //自适应高度
            ,url: api //数据接口
            ,page: true //开启分页
            ,limit:limit  //默认每页显示行数
            ,limits: [20,50,100,300,500]
            ,even:true //隔行背景色
            ,loading:true //加载条
            ,defaultToolbar:false
            ,cellMinWidth: 200 //最小宽度
            ,toolbar: '#toolbar'
            ,id:'table'
            ,cols: [[ //表头
                {type:'checkbox'} //开启复选框
                ,{field: 'pid', title: 'pid', width:60, sort: true,hide:true}
                ,{field: 'name', title: '名称',sort:true}
                ,{field: 'password', title: '密码'}
                ,{field: 'description', title: '描述'}
                ,{ title:'操作', toolbar: '#tablebar',width:120}
            ]]
            ,method: 'post'
            ,response: {statusCode: 1 } 
            ,done: function (res, curr, count) {
                //渲染完毕回调
            }
        });
    };
    load_list();
    
    table.on('toolbar(table)', function (obj) {
        if (obj.event === 'add') {
            form.val('form', {'pid':'0','name':'','password':'','description':''});
           index = layer.open({type: 1,scrollbar: false,shadeClose: true,title: '新增分组',area : ['100%', '100%'],content: $('.form')});
        }else if(obj.event === 'help'){
            window.open("https://gitee.com/tznb/TwoNav/wikis/pages?sort_id=7969061&doc_id=3767990","target");
            return false;
        }
    });
    
    table.on('tool(table)', function (obj) {
        if (obj.event === 'del') {
            layer.confirm('确认删除?',{icon: 3, title:'温馨提示'}, function(index){
                $.post(get_api('write_pwd_group','del'),{pid:obj.data.pid},function(data,status){
                    if(data.code == 1) {
                        load_list(); //刷新表
                        layer.msg(data.msg, {icon: 1});
                        layer.close(index);
                    }else{
                        layer.msg(data.msg, {icon: 5});
                    }
                });
            });
            return false;
        }else if(obj.event === 'edit'){
            form.val('form', obj.data);
            index = layer.open({type: 1,scrollbar: false,shadeClose: true,title: '编辑分组',area : ['100%', '100%'],content: $('.form')});
        }
    });
    
    //关闭按钮
    $(document).on('click', '#close', function() {
        layer.close(index);//关闭当前页
    });

    
    //保存
    form.on('submit(save)', function (data) {
        $("*").blur();
        var url = get_api('write_pwd_group',(data.field.pid == '0' ?'add':'edit'));
        $.post(url,data.field,function(data,status){
            if(data.code == 1) {
                load_list(); //刷新表
                layer.msg(data.msg, {icon: 1});
                layer.close(index);
            }else{
                layer.msg(data.msg, {icon: 5});
            }
        });
        return false;
    });
    
});
</script>
<ul class="form" style = "margin-top:18px;display:none;padding-right: 10px;" >
    <form class="layui-form" lay-filter="form">
        <input type="text" name="pid" style = "display:none;">
        <div class="layui-form-item">
            <label class="layui-form-label required">分组名称</label>
            <div class="layui-input-block">
                <input type="text" name="name" lay-verify="required" lay-reqtext="分组名称不能为空" placeholder="请输入分组名称"  class="layui-input">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label required">分组密码</label>
            <div class="layui-input-block">
                <input type="text" name="password" lay-verify="required" lay-reqtext="分组密码不能为空" placeholder="请输入分组密码"  class="layui-input" value="<?php echo uniqid()?>">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">分组描述</label>
            <div class="layui-input-block">
                <input type="text" name="description" placeholder="分组描述(可空)"  class="layui-input">
            </div>
        </div>
        <div class="layui-form-item">
            <div class="layui-input-block">
                <button class="layui-btn layui-btn-warm" type="button" id="close" >关闭</button>
                <button class="layui-btn layui-btn-normal" lay-submit lay-filter="save">保存</button>
            </div>
        </div>
  </form>
</ul>
</body>
</html>