<?php 
if($global_config['article'] != 1 || !check_purview('article',1)){
    require(DIR.'/templates/admin/page/404.php');
    exit;
}

$title='文章列表';
function echo_article_category(){
    $where['uid'] = UID; 
    foreach (select_db('user_article_categorys','*',$where) as $category) {
        echo "<option value=\"{$category['id']}\">{$category['name']}</option>";
    }
}
require dirname(__DIR__).'/header.php'  ?>
<body>
<div class="layuimini-container">
    <div class="layuimini-main">
      <form class="layui-form" lay-filter="form">
        <div class="layui-inline layui-form" style="padding-bottom: 5px;">
            <label class="layui-form-label " style="width:60px;padding-left: 5px;padding-right: 5px;">文章筛选:</label>
            <div class="layui-input-inline" style="width: 150px;">
                <select name="category" lay-search>
                    <option value="0" selected="">全部</option>
                    <optgroup label="用户分类">
                    <?php echo_article_category(); ?>
                    </optgroup>
                </select>
            </div>
        </div>
        
        <div class="layui-inline layui-form" style="padding-bottom: 5px;">
            <label class="layui-form-label layui-hide-sm" style="width:60px;padding-left: 5px;padding-right: 5px;">关键字:</label>
            <div class="layui-input-inline">
                <input class="layui-input" name="keyword" id="keyword" placeholder='请输入标题或文章内容' autocomplete="off" >
            </div>
        </div>
        <div class="layui-inline layui-form layui-hide-xs" style="padding-bottom: 5px;" >
            <label class="layui-form-label layui-hide-sm" style="width:60px;padding-left: 5px;padding-right: 5px; ">属性筛选:</label>
            <div class="layui-input-inline" style="width: 80px;">
                <select name="state">
                    <option value="0" selected="">全部</option>
                    <option value="1">公开</option>
                    <option value="2">私有</option>
                    <option value="3">草稿</option>
                    <option value="4">废弃</option>
                </select>
            </div>
        </div>
        <div class="layui-inline layui-form" style="padding-bottom: 5px;">
            <button class="layui-btn layui-btn-normal "type="button" id="search" style="height: 36px;">搜索</button>
        </div>
      </form>
      <table id="table" class="layui-table" lay-filter="table" style="margin: 1px 0;"></table>
    </div>
</div>
<!-- 操作列 -->
<script type="text/html" id="tablebar">
    <div class="layui-btn-group">
        <a class="layui-btn layui-btn-danger layui-btn-xs" lay-event="del">删除</a>
        <a class="layui-btn layui-btn-normal layui-btn-xs" lay-event="edit">编辑</a>
    </div>
</script>
<!-- 表头工具栏 -->
<script type="text/html" id="toolbar">
    <div class="layui-btn-group">
        <button class="layui-btn layui-btn-sm layui-btn-danger layui-hide-xs" lay-event="batch_del">删除选中</button>
        <button class="layui-btn layui-btn-sm layui-btn-normal" lay-event="add_article">添加文章</button>
        <button class="layui-btn layui-btn-sm layui-btn-normal" lay-event="category">分类管理</button>
    </div>
</script>
<script src = "<?php echo $libs;?>/jquery/jquery-3.6.0.min.js"></script>
<script src = "./templates/admin/js/public.js?v=<?php echo $Ver;?>"></script>
<?php load_static('js');?>

<ul class="category" style="margin-top: 18px;display:none;padding-right: 10px;padding-left: 10px;">
    <div class="layui-btn-container">
        <button class="layui-btn layui-btn-sm layui-btn-normal" lay-submit id="to_article_list">返回</button>
        <button class="layui-btn layui-btn-sm layui-btn-normal" lay-submit id="add_category">添加</button>
        <button class="layui-btn layui-btn-sm layui-btn-normal" lay-submit id="refresh_category">刷新</button>
        <button class="layui-btn layui-btn-sm layui-btn-normal" lay-submit id="category_tip">权重提示</button>
    </div>
    <table id="category_list" lay-filter="category_list"></table>
</ul>

<ul class="edit_category" style="margin-top: 18px;display:none;padding-right: 10px;padding-left: 10px;">
    <form class="layui-form" lay-filter="edit_category_form">
        <input type="text" name="id" autocomplete="off" class="layui-input" style="display:none;">
        <div class="layui-form-item">
            <label class="layui-form-label" style="width: 40px;">名称</label>
            <div class="layui-input-block" style="margin-left: 70px">
                <input type="text" name="name" autocomplete="off" class="layui-input">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label" style="width: 40px;">权重</label>
            <div class="layui-input-block" style="margin-left: 70px">
                <input type="number" name="weight" autocomplete="off" class="layui-input">
            </div>
        </div>
        <div class="layui-form-item" style="padding-top: 10px;">
            <div class="layui-input-block">
                <button class="layui-btn" lay-submit id="save_category">保存</button>
            </div>
        </div>
   </form>
</ul>

<script>
layui.use(['form','table','dropdown','miniTab'], function () {
    var $ = layui.jquery;
    var table = layui.table;
    var form = layui.form;
    var dropdown = layui.dropdown;
    var miniTab = layui.miniTab;
    var api = get_api('read_article','article_list');
    var limit = localStorage.getItem(u + "_limit") || 50;
    var state_data = ["Null","公开", "私有", "草稿", "废弃"];
    var cols=[ //表头
      {type:'checkbox'} //开启复选框
      //,{field: 'id', title: 'ID', width:80, sort: true}
      ,{ title:'操作', toolbar: '#tablebar',width:110}
      ,{field: 'title', title: '标题', minWidth:200,templet: function(d){
          return '<a style="color:#3c78d8" target="_blank" href="/index.php?c=article&id=' +d.id + '&u=' + u + '">'+d.title+'</a>'
      }}
      ,{field:'category',title:'分类',width:100,templet: function(d){
          return d.category_name;
      }}
      ,{field:'state',title:'状态',width:100,templet: function(d){
          return state_data[d.state];
      }}
      ,{field: 'browse_count', title: '浏览次数',width:120,sort:true}
      ,{field: 'add_time', title: '添加时间', width:170, sort: true,templet:function(d){
          var add_time = timestampToTime(d.add_time);
          return add_time;
      }}
      ,{field: 'up_time', title: '修改时间', width:170,sort:true,templet:function(d){
          return d.up_time == null ?'':timestampToTime(d.up_time);
      }}
    ];
    
    table.render({
        elem: '#table'
        ,height: 'full-80'
        ,url: api
        ,page: true 
        ,limit:limit
        ,limits: [20,50,100,300,500]
        ,even:true
        ,loading:true
        ,toolbar: '#toolbar'
        ,id:'table'
        ,cols: [cols]
        ,method: 'post'
        ,response: {statusCode: 1 } 
        ,done: function (res, curr, count) {
            //获取当前每页显示数量.并写入本都储存
            var temp_limit = $(".layui-laypage-limits option:selected").val();
            if(temp_limit > 0 && localStorage.getItem(u + "_limit") != temp_limit){
                localStorage.setItem(u + "_limit",temp_limit);
            }
        }
    });
    
    function search(){
        let data = form.val('form');
        table.reload('table', {
            url: api
            ,method: 'post'
            ,request: {pageName: 'page',limitName: 'limit'}
            ,where: data
            ,page: {curr: 1}
        });
    }
    
    //关键字回车搜索
    $('#keyword').keydown(function (e){if(e.keyCode === 13){search();}}); 
    //搜索按钮点击
    $('#search').on('click', function(){search();});
    
    //监听工具栏 - 文章列表
    table.on('toolbar(table)', function (obj) {
        var btn = obj.event;
        if (btn == 'add_article') {
            layer.open({
                title: false,
                type: 2,
                scrollbar: false,
                shade: 0.2,
                maxmin:false,
                shadeClose: true,
                closeBtn:0,
                area: ['100%', '100%'],
                content: './index.php?c=admin&page=expand/article-edit&u=' + u,
                end: function(){
                    search();
                }
            });
        }else if(btn == 'category'){
            category_index = layer.open({type: 1,scrollbar: false,shadeClose: true,title: false ,area : ['100%', '100%'],closeBtn:0,content: $('.category'),
                success: function(layero, index, that){
                    category_list();
                }
            });
        }else{
            var checkStatus = table.checkStatus(obj.config.id);
            if( checkStatus.data.length == 0 && ['LAYTABLE_COLS','LAYTABLE_EXPORT','LAYTABLE_PRINT'].indexOf(btn) == -1 ) {
                layer.msg('未选中任何数据！');
                return;
            }
            if(btn == 'batch_del'){
                tableIds = checkStatus.data.map(function (value) {return value.id;});
                tableIds = JSON.stringify(tableIds);
                layer.confirm('确认删除?',{icon: 3, title:'温馨提示'}, function(index){
                    $.post(get_api('write_article','del_article'),{id:tableIds},function(data,status){
                        if(data.code == 1) {
                            search();
                            layer.msg(data.msg, {icon: 1});
                        }else{
                            layer.msg(data.msg, {icon: 5});
                        }
                    });
                });
            }
            

        }

    });
    //监听行工具 - 文章列表
    table.on('tool(table)', function (obj) {
        let btn = obj.event;
        let data = obj.data;
        if (btn === 'del') {
            layer.confirm('确认删除?',{icon: 3, title:'温馨提示'}, function(index){
                $.post(get_api('write_article','del_article'),{id:'['+data.id+']'},function(data,status){
                    if(data.code == 1) {
                        obj.del();
                        layer.msg(data.msg, {icon: 1});
                    }else{
                        layer.msg(data.msg, {icon: 5});
                    }
                });
            });
        }else if(btn === 'edit'){
            layer.open({
                title: false,
                type: 2,
                scrollbar: false,
                shade: 0.2,
                maxmin:false,
                shadeClose: true,
                closeBtn:0,
                area: ['100%', '100%'],
                content: './index.php?c=admin&page=expand/article-edit&id='+data.id+'&u=' + u,
                end: function(){
                    search();
                }
            });
        }
    });
    //监听行工具 - 分类列表
    table.on('tool(category_list)', function (obj) {
        let btn = obj.event;
        let data = obj.data;
        if (btn === 'del') {
            layer.confirm('确认删除?',{icon: 3, title:'温馨提示'}, function(index){
                $.post(get_api('write_article','del_category'),{id:data.id},function(data,status){
                    if(data.code == 1) {
                        obj.del();
                        layer.msg(data.msg, {icon: 1});
                    }else{
                        layer.msg(data.msg, {icon: 5});
                    }
                });
            });
        }else if(btn === 'edit'){
            form.val('edit_category_form', data);
            edit_category_index = layer.open({type: 1,scrollbar: false,shadeClose: true,title: '编辑分类',area : ['auto', 'auto'],content: $('.edit_category')});
        }
    });
    //添加分类
    $('#add_category').click(function () {
        add_category_index = layer.prompt({formType: 0,value: '',title: '请输入分类名称:',shadeClose: false,"success":function(){
            $("input.layui-layer-input").on('keydown',function(e){
                if(e.which == 13) {add_category();}
            });
        }},function(){
            add_category()
        }); 
    });
    //返回
    $('#to_article_list').click(function () {
        layer.close(category_index);
        location.reload();
    });
    //刷新
    $('#refresh_category').click(function () {
        category_list();
    });
    //分类提示
    $('#category_tip').click(function () {
        layer.alert("权重越小越靠前",{title:'提示',anim: 2,closeBtn: 0});
    });
    
    //编辑分类-保存
    $('#save_category').click(function () {
        $.post(get_api('write_article','save_category'),form.val('edit_category_form'),function(data,status){
            $("input.layui-layer-input").val("");
            if(data.code == 1) {
                category_list();
                layer.msg('操作成功', {icon: 1});
                layer.close(edit_category_index); 
            }else{
                $("input.layui-layer-input").focus();
                layer.msg(data.msg || '未知错误',{icon: 5});
            }
        });
        return false;
    });
    
    function add_category(){
        let name = $("input.layui-layer-input").val();
        if(name == ''){ return false; }
        $("*").blur();
        let loading = layer.msg('正在添加文章分类,请稍后..', {icon: 16,time: 1000*300,shadeClose: false});
        $.post(get_api('write_article','add_category'),{'name':name},function(data,status){
            layer.close(loading); layer.close(add_category_index); 
            $("input.layui-layer-input").val("");
            if(data.code == 1) {
                category_list();
                layer.msg('操作成功', {icon: 1});
            }else{
                $("input.layui-layer-input").focus();
                layer.msg(data.msg || '未知错误',{icon: 5});
            }
        });
    }
    function category_list(){
        table.render({
            elem: '#category_list'
            ,height: 'full-70'
            ,url: get_api('read_article','category_list')
            ,page: false 
            ,limit:999
            ,limits: [999]
            ,even:true
            ,loading:true
            ,id:'category_list'
            ,cols: [[ 
                {title:'操作', toolbar: '#tablebar', width:110}
                ,{field: 'name', title: '分类名称', minWidth:200,width:300}
                ,{field: 'weight', title: '权重', minWidth:100,width:160}
            ]]
            ,method: 'post'
            ,response: {statusCode: 1 } 
            ,done: function (res, curr, count) {
                //获取当前每页显示数量.并写入本都储存
                var temp_limit = $(".layui-laypage-limits option:selected").val();
                if(temp_limit > 0 && localStorage.getItem(u + "_limit") != temp_limit){
                    localStorage.setItem(u + "_limit",temp_limit);
                }
            }
        });
    }
    
});

</script>


</body>
</html>