<?php 

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
                    <?php echo_category(true); ?>
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
        <button class="layui-btn layui-btn-sm layui-btn-danger" id="batch_operation"><span>批量操作</span><i class="layui-icon layui-icon-down layui-font-12"></i></button>
        <button class="layui-btn layui-btn-sm layui-btn-normal" lay-event="add_article">添加文章</button>
        <button class="layui-btn layui-btn-sm " lay-event="set">设置</button>
    </div>
</script>
<script src = "<?php echo $libs;?>/jquery/jquery-3.6.0.min.js"></script>
<script src = "./templates/admin/js/public.js?v=<?php echo $Ver;?>"></script>
<?php load_static('js');?>
<!--批量修改分类-->
<ul class="batch_category" style="margin-top: 18px;display:none;padding-right: 10px;padding-left: 10px;">
    <form class="layui-form" lay-filter="batch_category">
        <div class="layui-form-item">
            <label class="layui-form-label">父级分类</label>
            <div class="layui-input-block">
                <select id="batch_category_fid">
                    <?php echo_category(true); ?>
                </select> 
            </div>
        </div>
        <div class="layui-form-item">
            <div class="layui-input-block">
                <button class="layui-btn layui-btn-normal layui-btn-danger cancel" type="button">取消</button>
                <button class="layui-btn" type="button" id="batch_category" >确定修改</button>
            </div>
        </div>
    </form>
</ul>
<!--设置-->
<ul class="set" style="margin-top: 18px;display:none;padding-right: 10px;padding-left: 10px;">
    <form class="layui-form" lay-filter="set_form">
        <div class="layui-form-item">
            <label class="layui-form-label">显示文章</label>
            <div class="layui-input-inline">
                <select name="visual">
                    <option value="1">显示靠前</option>
                    <option value="2">显示靠后</option>
                    <option value="0">隐藏</option>
                </select>
            </div>
            <div class="layui-form-mid layui-word-aux">是否在主页显示文章链接</div>
        </div>
        
        <div class="layui-form-item">
            <label class="layui-form-label">链接图标</label>
            <div class="layui-input-inline">
                <select name="icon">
                    <option value="0">首字图标</option>
                    <option value="1">站点图标</option>
                    <option value="2">文章封面</option>
                </select>
            </div>
            <div class="layui-form-mid layui-word-aux">设为文章封面且无封面时显示站点图标</div>
        </div>
        
        <div class="layui-form-item" style="padding-top: 10px;">
            
            <div class="layui-input-block">
                <button class="layui-btn layui-btn-normal layui-btn-danger cancel">取消</button>
                <button class="layui-btn" lay-submit id="save_set">保存</button>
            </div>
        </div>
        <pre class="layui-code" >
小提示: 
1.文章所属分类加密时不会对文章加密 (暂不支持文章加密,不想被看到可将文章设为私有)
2.文章所属分类私有且未登录时不显示文章链接 (通过文章链接访问不受限制,不想被看到可将文章设为私有)
3.上传图片支持格式:jpg|png|gif|bmp|jpeg|svg 大小限制:5M
4.编辑器中上传图片小于128KB时使用base64编码存入数据库,大于128KB时将以文件的方式上传到服务器
5.显示文章选项中的靠前/靠后是指文章链接在所属分类下的位置,隐藏则不在主页显示
        </pre>
   </form>
</ul>
<ul class="push" style="margin-top: 18px;display:none;padding-right: 10px;padding-left: 10px;">
    <form class="layui-form layuimini-form" lay-filter="push">
        <pre class="layui-code" >使用API推送功能会达到怎样效果
及时发现：可以缩短百度爬虫发现您站点新链接的时间，使新发布的页面可以在第一时间被百度收录
保护原创：对于网站的最新原创内容，使用API推送功能可以快速通知到百度，使内容可以在转发之前被百度发现
百度官方说明: https://ziyuan.baidu.com/linksubmit/index
注意事项: 推送的URL是静态格式,所以请务必正确配置好伪静态!
伪静态配置: 请前往站长工具>生成伪静态,并复制内容配置到服务器 (仅针对Nginx)
</pre>
        <div class="layui-form-item">
            <label class="layui-form-label">接口地址</label>
                <div class="layui-input-block">
                    <input type="text" name="push_api" id="push_api" placeholder="请输入接口调用地址如 http://data.zz.baidu.com/urls?site=lm21.top&token=xxxxxx" 
                    value="<?php echo get_db("user_config", "v", ["k" => "baidu_push_api","uid"=>UID]); ?>" class="layui-input">
                </div>
        </div>
        <div class="layui-form-item">
            <div class="layui-input-block">
                <button class="layui-btn layui-btn-warm" type="button" id="close" >关闭</button>
                <button class="layui-btn layui-btn-normal" lay-submit lay-filter="start_push" id="start_push">开始</button>
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
      ,{ title:'操作', toolbar: '#tablebar',width:110}
      ,{field: 'title', title: '标题', minWidth:200,templet: function(d){
          return '<a style="color:#3c78d8" target="_blank" href="./?c=article&id=' +d.id + '&u=' + u + '" title="' + htmlspecialchars(d.summary) + '">'+htmlspecialchars(d.title)+'</a>'
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
            batch_operation();//初始化批量操作菜单
            //获取当前每页显示数量.并写入本都储存
            var temp_limit = $(".layui-laypage-limits option:selected").val();
            if(temp_limit > 0 && localStorage.getItem(u + "_limit") != temp_limit){
                localStorage.setItem(u + "_limit",temp_limit);
            }
        }
    });
    //批量操作
    function batch_operation(){
        dropdown.render({
            elem: '#batch_operation',
            data: [{
              title: ' 修改分类 ',
              id: 'up_category'
            },{
              title: '修改状态',
              child: [{
                  title: '设为公开',
                  id: "up_state",
                  value: 1
                },{
                  title: '设为私有',
                  id: "up_state",
                  value: 2
                },{
                    title: '设为草稿',
                    id: "up_state",
                  value: 3
                },{
                    title: '设为废弃',
                    id: "up_state",
                  value: 4
                }]
            },{
              title: '批量删除',
              id: 'del_article'
            },{
              title: '百度推送',
              id: 'push'
            }],
            click: function(obj){
                let checkStatus = table.checkStatus('table').data;
                if( checkStatus.length == 0 ) {
                    layer.msg('未选中任何数据！');
                    return;
                }
                //获取被选ID并格式化
                tableIds = checkStatus.map(function (value) {return value.id;});
                tableIds = JSON.stringify(tableIds);
                //删除文章
                if(obj.id == 'del_article'){
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
                }else if(obj.id == 'up_category'){
                    index = layer.open({type: 1,scrollbar: false,shadeClose: true,title: false ,area : ['100%', '100%'],closeBtn:0,content: $('.batch_category')});
                }else if(obj.id == 'up_state'){
                    $.post(get_api('write_article','up_state'),{'id':tableIds,'state_id':obj.value},function(data,status){
                        if(data.code == 1) {
                            search();
                            layer.msg('操作成功', {icon: 1});
                        }else{
                            layer.msg(data.msg || '未知错误',{icon: 5});
                        }
                    });
                }else if(obj.id == 'push'){
                    index = layer.open({type: 1,scrollbar: false,shadeClose: true,title: '推送工具',area : ['100%', '100%'],content: $('.push')});
                }
            }
        });
    }
    //开始推送
    $('#start_push').click(function () {
        Authorization_Prompt();
        return false;
    });
    //关闭按钮
    $(document).on('click', '#close', function() {
        layer.close(index);//关闭当前页
    });
    //输入框回车事件和搜索按钮点击事件
    $('#keyword, #search').on('keydown click', function(e) {
        if ( (e.target.id === 'keyword' &&  e.keyCode === 13) || (e.target.id === 'search' && e.type === 'click') ) {
          search();
        }
    });
    //搜索
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
    //监听工具栏
    table.on('toolbar(table)', function (obj) {
        var btn = obj.event;
        if (btn == 'add_article') { //添加文章
            Authorization_Prompt();
        }else if(btn == 'set'){ //设置
            index = layer.open({type: 1,scrollbar: false,shadeClose: true,title: false ,area : ['100%', '100%'],closeBtn:0,content: $('.set')});
        }else{ //综合批量操作
            //取选中数据
            var checkStatus = table.checkStatus(obj.config.id);
            if( checkStatus.data.length == 0 && ['LAYTABLE_COLS','LAYTABLE_EXPORT','LAYTABLE_PRINT'].indexOf(btn) == -1 ) {
                layer.msg('未选中任何数据！');
                return;
            }
            //批量删除
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

    
    
    //监听行工具
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
            Authorization_Prompt();
        }
    });
    //设置相关
    form.val('set_form', <?php echo json_encode($set);?>);
    $('#save_set').on('click', function(){
        Authorization_Prompt();
        return false;
    });
    //取消按钮
    $('.cancel').click(function () {
        layer.close(index);
        return false;
    });
    
    //批量修改分类
    $('#batch_category').click(function () {
        Authorization_Prompt();
        return false;
    });
    
    function htmlspecialchars(str) {
        return $('<div/>').text(str).html();
    }
});

</script>


</body>
</html>