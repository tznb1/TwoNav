<?php $title='分享列表';$awesome=true; require 'header.php';  ?>
<style>
    .layui-table-tool-temp  {padding-right:1px;}
    @media screen and (max-width: 768px) {
        .layui-word-aux {
            display: none!important;
        }
    } 
</style>
<body>
<div class="layuimini-container">
    <div class="layuimini-main">
        
        <div class="layui-inline layui-form" style="padding-bottom: 5px;">
            <div class="layui-input-inline" style="width: 80px;">
                <select id="status">
                    <option value="0">全部</option>
                    <option value="1">有效</option>
                    <option value="2">过期</option>
                </select>
            </div>
            <div class="layui-input-inline" style="width: 80px;">
                <select id="type">
                    <option value="0">全部</option>
                    <option value="1">分类</option>
                    <option value="2">链接</option>
                </select>
            </div>
        </div>
        
        <div class="layui-inline layui-form" style="padding-bottom: 5px;">
            <div class="layui-input-inline">
                <input class="layui-input" name="keyword" id="keyword" placeholder='请输入标识/名称/备注' autocomplete="off" >
            </div>
            
        </div>

        <div class="layui-inline layui-form" style="padding-bottom: 5px;">
            <button class="layui-btn layui-btn-normal " id="search" style="height: 36px;">搜索</button>
        </div>
        <table id="table" class="layui-table" lay-filter="table" style="margin: -3px 0;"></table>
    </div>
</div>
<!-- 操作列 -->
<script type="text/html" id="tablebar">
    <div class="layui-btn-group">
        <a class="layui-btn layui-btn-danger layui-btn-xs" lay-event="del">删除</a>
        <a class="layui-btn layui-btn-normal layui-btn-xs" lay-event="edit">编辑</a>
        <a class="layui-btn layui-btn-normal layui-btn-xs" lay-event="copy" id="copy">复制</a>
    </div>
</script>
<!-- 表头工具栏 -->
<script type="text/html" id="toolbar">
    <div class="layui-btn-group">
        <button class="layui-btn layui-btn-sm layui-btn-danger" lay-event="del">删除</button>
        <button class="layui-btn layui-btn-sm layui-btn-normal" lay-event="add">添加</button>
    </div>
</script>

<ul class="data" style = "margin-top:18px;display:none;padding-right: 10px;" >
    <form class="layui-form layuimini-form" lay-filter="data">
        <input type="text" name="sid" id="sid" value="" style = "display:none;">
        
        <div class="layui-form-item">
            <label class="layui-form-label required">类型</label>
            <div class="layui-input-inline" style="width: 100px;">
                <select name="type" lay-filter="type">
                    <option value="1">分类</option>
                    <option value="2">链接</option>
                </select> 
            </div>
            <div class="layui-form-mid layui-word-aux">创建后无法修改</div>
        </div>
        
        <div class="layui-form-item">
            <label class="layui-form-label required">名称</label>
            <div class="layui-input-block">
                <input type="text" name="name" lay-verify="required" lay-reqtext="名称不能为空" placeholder="请输入名称"  class="layui-input">
            </div>
        </div>

        <div class="layui-form-item">
            <label class="layui-form-label">提取码</label>
            <div class="layui-input-block">
                <input type="text" name="pwd" placeholder="留空则视为公开" class="layui-input">
            </div>
        </div>
        
        <div class="layui-form-item" id="cf">
            <label class="layui-form-label">分享内容</label>
            <div class="layui-input-block">
                <input type="text" name="category_data" id="category_data" readonly="readonly" placeholder="点击此处选择需要分享的内容" class="layui-input">
            </div>
        </div>
        
        <div class="layui-form-item" id="lf" style="display:none;">
            <label class="layui-form-label">分享内容</label>
            <div class="layui-input-block">
                <input type="text" name="link_data" id="link_data" readonly="readonly" placeholder="点击此处选择需要分享的内容" class="layui-input">
            </div>
        </div>
        
        <div class="layui-form-item">
            <label class="layui-form-label">私有可见</label>
            <div class="layui-input-block">
                <input type="checkbox" name="pv" value = "1" lay-skin="switch" lay-text="是|否">
            </div>
        </div>
        
        <div class="layui-form-item">
            <label class="layui-form-label">有效期</label>
            <div class="layui-input-block">
                <input name="expire" placeholder="有效期,留空为永久" readonly="readonly" class="layui-input" id="expire" style="padding-left: 95px;">
                <div style="position: absolute; top:0px;width: 90px;">
                    <select name="days" lay-filter="days" id="days">
                        <option value="#" disabled="">自定义</option>
                        <option value="0" selected="">永久</option>
                        <option value="1">1天</option>
                        <option value="3">3天</option>
                        <option value="7">7天</option>
                        <option value="15">15天</option>
                        <option value="30">30天</option>
                        <option value="60">60天</option>
                    </select>
                </div>
            </div>
        </div>
        
        <div class="layui-form-item">
            <label class="layui-form-label">备注</label>
            <div class="layui-input-block">
                <textarea name="description" id="description" placeholder="请输入内容" class="layui-textarea"></textarea>
            </div>
        </div>
    
        <div class="layui-form-item">
            <div class="layui-input-block">
                <button class="layui-btn layui-btn-warm" type="button" id="close" >关闭</button>
                <button class="layui-btn layui-btn-normal" lay-submit lay-filter="save" id="save">保存</button>
            </div>
        </div>
  </form>
</ul>
<script src = "<?php echo $libs;?>/jquery/jquery-3.6.0.min.js"></script>
<script src = "./templates/admin/js/public.js?v=<?php echo $Ver;?>"></script>
<script src = "<?php echo $libs?>/Other/ClipBoard.min.js"></script>
<?php load_static('js');?>
<script>
layui.use(['form','table','laydate','tableSelect'], function () {
    var $ = layui.jquery,table = layui.table,form = layui.form,laydate = layui.laydate,tableSelect = layui.tableSelect;
    var api = get_api('read_share','share_list');
    var limit = localStorage.getItem(u + "_limit")??50;
    var index,temp_date,type='category';
    var isSupported = ClipboardJS.isSupported();
    var baseUrl = Get_baseUrl();
    //日期时间选择器
    laydate.render({
        elem: '#expire'
        ,type: 'datetime'
        ,btns: ['clear', 'confirm']
        ,min: 1
        ,done: function(value, date, endDate){
            if(temp_date != value){
                form.val('data', {"days":"#"});
            }
            temp_date = value;
        },ready: function(value,date){
            //缓存初始时间,用于比较确定后是否改变.
            temp_date = $('#expire').val();
        }
    });
    
    //快选时间
    form.on('select(days)', function(data){
        if(data.value > 0){
           now = Math.round(new Date() / 1000);
            after = now + ( data.value * 86400);
            $('#expire').val(timestampToTime(after)); 
        }else if(data.value === '0'){
            $('#expire').val(''); 
        }
    });
    
    //根据选择类型初始化下拉选择
    form.on('select(type)', function(data){
        console.log(data.value);
        if(data.value == '1' && type != 'category'){
            type = 'category';
            $('#cf').show();
            $('#lf').hide();
        }else if(data.value == '2' && type != 'link'){
            type = 'link';
            $('#cf').hide();
            $('#lf').show();
        }
    });

    var cols=[[ //表头
        {type:'checkbox'}
        ,{field:'id',title:'ID',width:60,sort:true,hide:true}
        ,{title: '操作',toolbar: '#tablebar',align:'center',width:140}
        ,{field:'sid',title:'标识',width:118,align:'center',templet:function(d){
            return '<a style="color:#3c78d8" href = "./index.php?share='+d.sid+'" target = "_blank" title = "点击打开">'+d.sid+'</a>';
        }}
        ,{field:'name',title:'名称',width:180}
        ,{field:'pwd',title:'提取码',width:160}
        ,{field:'type',title:'类型', width:80,align:'center',templet:function(d){
            if(d.type == 1){
                return '分类';
            }else if(d.type == 2){
                return '链接';
            }else{
                return 'Null';
            }
        }}
        ,{field:'views',title:'访问量',width:90,align:'center'}
        ,{field:'add_time',title:'添加时间', width:170,templet:function(d){
            return timestampToTime(d.add_time);
        }}
        ,{field:'expire_time',title:'有效期',width:190,templet:function(d){
            if(d.expire_time == 9999999999){
                return '<i class="fa fa-check" style="color:green" title="永久有效"> 永久</i>';
            }else if( Math.round(new Date() / 1000) > d.expire_time ) {
                return '<i class="fa fa-close" style="color:red" title="已过期"> '+timestampToTime(d.expire_time)+'</i>';
            }else{
                return '<i class="fa fa-check" style="color:green" title="有效"> '+timestampToTime(d.expire_time)+'</i>';
            }
        }}
    ]]
    
    table.render({
        elem: '#table'
        ,height: 'full-100' //自适应高度
        ,url: api //数据接口
        ,page: true //开启分页
        ,limit:limit  //默认每页显示行数
        ,limits: [20,50,100,300,500]
        ,even:true //隔行背景色
        ,loading:true //加载条
        ,toolbar: '#toolbar'
        ,id:'table'
        ,cols: cols
        ,method: 'post'
        ,response: {statusCode: 1 } 
        ,done: function (res, curr, count) {
            var temp_limit = $(".layui-laypage-limits option:selected").val();
            if(temp_limit > 0 && localStorage.getItem(u + "_limit") != temp_limit){
                localStorage.setItem(u + "_limit",temp_limit);
            }
            $(".layui-table-tool-self").addClass('layui-hide-xs');
        }
    });
    table.on('toolbar(table)', function (obj) {
        var event = obj.event;
        if (event == 'add') {
            $('#link_data').attr( "ts-selected",'');
            $('#category_data').attr( "ts-selected",'');
            form.val('data', {'sid':'','name': '','pwd': '','category_data':'','link_data':'','description':'','expire':'','days':''});
            index = layer.open({type: 1,scrollbar: false,shadeClose: true,title: '添加分享',area : ['100%', '100%'],content: $('.data')});
            return;
        }
        var checkStatus = table.checkStatus(obj.config.id);
        if( checkStatus.data.length == 0 && ['del'].indexOf(event) != -1) {
            layer.msg('未选中任何数据！');
            return;
        }
        if(event == 'del'){
            sid = checkStatus.data.map(function (value) {return value.sid;});
            sid = JSON.stringify(sid);
            layer.confirm('确认删除选中数据?',{icon: 3, title:'温馨提示'}, function(index){
                    $.post(get_api('write_share','del'),{'sid':sid},function(data,status){
                    if(data.code == 1){
                        table.reload('table');
                        layer.msg(data.msg, {icon: 1});
                    }else{
                        layer.msg(data.msg,{icon:5});
                    }
                });
            });
            return;
        }
    });
    //搜索标题输入框回车事件
    $('#title').keydown(function (e){
        if(e.keyCode === 13){
            $("#search").click();
        }
    }); 
    //搜索按钮
    $(document).on('click', '#search', function() {
        var keyword = $('#keyword').val();
        var status = $('#status').val();
        var type = $('#type').val();
        table.reload('table', {
            method: 'post'
            ,request: {pageName: 'page',limitName: 'limit'}
            ,where: {"keyword":keyword,"status":status,"type":type}
            ,page: {curr: 1}
        });
    });
    //关闭按钮
    $(document).on('click', '#close', function() {
        layer.close(index);//关闭当前页
    });
    //保存
    form.on('submit(save)', function (data) {
        $.post(get_api('write_share','save'),data.field,function(data,status){
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
    //监听工具条
    table.on('tool(table)', function (obj) {
        var data = obj.data;
        if (obj.event === 'del') {
            layer.confirm('确认删除数据？',{icon: 3, title:'温馨提示'}, function(index){
                $.post(get_api('write_share','del'),{sid:'["'+data.sid+'"]'},function(data,status){
                    if(data.code == 1) {
                        obj.del();
                        layer.msg(data.msg, {icon: 1});
                    }else{
                        layer.msg(data.msg, {icon: 5});
                    }
                });
            });
        }else if(obj.event === 'copy'){
            if(isSupported){
                ClipboardJS.copy(baseUrl + "index.php?share=" + data.sid +(data.pwd != '' ? '&pwd=' + data.pwd:''));
                layer.msg('复制成功', {icon: 1});
            }else{
                layer.msg('复制失败,浏览器不支持', {icon: 5});
            }
        }else if(obj.event === 'edit'){
            form.val('data', data);
            form.val('data', {'pv':data.pv == 1});
            console.log(data.data);
            if(data.type == '1'){
                $('#category_data').val(data.data);
                $('#category_data').attr("ts-selected",data.data.replace('[', '').replace(']', ''));
                type = 'category';
                $('#cf').show();
                $('#lf').hide();
            }else if(data.type == '2'){
                $('#link_data').val(data.data);
                $('#link_data').attr("ts-selected",data.data.replace('[', '').replace(']', ''));
                type = 'link';
                $('#cf').hide();
                $('#lf').show();
            }
            index = layer.open({type: 1,scrollbar: false,shadeClose: true,title: '编辑分享',area : ['100%', '100%'],content: $('.data')});
            return;
        }
    });

      
      
    //书签选择 
    function load_tableSelect(searchPlaceholder,name,url,elem,limit,limits){ tableSelect.render({
        elem: elem,
        checkedKey: 'id',
        searchType: 'more', 
        searchList: [{searchKey: 'keyword',searchPlaceholder:searchPlaceholder ,width:'190px'},],
        height:'400',  //自定义高度
        width:'600',  //自定义宽度
        rowDouble:false, //禁止双击
        table: {
            url:url,
            response: {statusCode: 1},
            page: true, 
            limit:limit,
            limits: limits,
            cols: [[
				{type: 'checkbox' },
				{field:'id',title:'id',width:120,hide:true},
				{field:'name',title:name,width:'85%'},
            ]]},
        done: function (elem, data) {
            var id = [];
            layui.each(data.data, function (index, item) {
                id.push(item.id);
            })
            elem.val(id.join(","));
            $(elem).val(JSON.stringify(id));
        }
    })}
    load_tableSelect('分类名称搜索','分类名',get_api('read_share','categorys'),'#category_data',9999,[9999]);
    load_tableSelect('链接标题搜索','链接标题',get_api('read_share','links'),'#link_data',10,[10,20,30,50,100]);

});
</script>
</body>
</html>