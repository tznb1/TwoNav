<?php 
if($global_config['apply'] != 1 || !check_purview('apply',1)){
    require(DIR.'/templates/admin/page/404.php');
    exit;
}
$data = unserialize( get_db("user_config", "v", ["k" => "apply","uid"=>UID]) );
$title='收录管理';$awesome=true; require dirname(__DIR__).'/header.php'; 
?>

<body>
<div class="layuimini-container">
    <div class="layuimini-main">
        <script type="text/html" id="user_tool">
            <div class="layui-btn-group">
                <button class="layui-btn layui-btn-sm " lay-event="conf" >设置</button>
                <button class="layui-btn layui-btn-sm layui-btn-danger" lay-event="empty" >清空收录申请</button>
                <button class="layui-btn layui-btn-sm" lay-event="oepn" >申请收录</button>
            </div>
        </script>
        <script type="text/html" id="link_operate">
            <a class="layui-btn layui-btn-xs" lay-event="operation">操作 <i class="layui-icon layui-icon-down"></i></a>
        </script>
        <table id="apply_list" class="layui-table" lay-filter="apply_list" style="margin: -3px 0;"></table>
    </div>
</div>
<!--设置-->
<ul class="conf" style = "margin-top:18px;display:none;padding-right: 10px;" >
    <form class="layui-form" lay-filter="conf">
        <div class="layui-form-item" >
            <label class="layui-form-label">申请收录</label>
            <div class="layui-input-inline">
                <select lay-verify="required"  id="apply" name="apply" lay-search>
                    <option value="0">关闭申请</option>
                    <option value="1">需要审核</option>
                    <option value="2">无需审核</option>
                </select>
            </div>
            <div class="layui-form-mid layui-word-aux">此功能存在安全隐患,请慎用!特别是无需审核!</div>
        </div>
        <div class="layui-form-item layui-form-text">
            <label class="layui-form-label">收录公告</label>
            <div class="layui-input-block">
            <textarea name = "Notice" placeholder="显示在收录页的公告使用HTML代码编写(如有拦截提示,请暂时关闭防XSS脚本和防SQL注入)" rows = "5" class="layui-textarea"></textarea>
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">提交限制</label>
            <div class="layui-input-inline" style="    width: 71px;">
                <input type="number" name="submit_limit" lay-verify="required" placeholder='单位:秒' value="10" autocomplete="off" class="layui-input">
            </div>
            <div class="layui-form-mid layui-word-aux">单位:次,指最近24小时内可以提交多少次(为了防止恶意提交,删除记录可以恢复次数)</div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">必填选项</label>
            <div class="layui-input-block" style="margin-left: 32px;">
                <input type="checkbox" name="iconurl" title="图标" >
                <input type="checkbox" name="description" title="描述" >
                <input type="checkbox" name="email" title="邮箱" >
            </div>
        </div>
        
        <div class="layui-form-item layui-form-text">
            <label class="layui-form-label">使用说明</label>
            <div class="layui-form-mid ">部分主题没有收录入口,请自行添加到链接或者底部等你认为合适的地方!前往<a style="color:#3c78d8" target="_blank" href="./index.php?c=apply&u=<?php echo $u?>" target="_blank">申请收录</a></div>
        </div>
        <div class="layui-form-item layui-form-text">
            <label class="layui-form-label">安全限制</label>
            <div class="layui-form-mid ">1.禁止含有特殊字符<'&">等 &nbsp;  2.SQL和XSS相关的敏感词  &nbsp; 3.限制超过256个字符</div>
        </div>
        <div class="layui-form-item">
            <div class="layui-input-block">
                <button class="layui-btn" lay-submit lay-filter="conf">保存设置</button>
            </div>
        </div>
  </form>
</ul>

<!--详情-->
<ul class="details" style = "margin-top:18px;display:none;padding-right: 10px;" >
    <form class="layui-form" lay-filter="details">
  <div class="layui-form-item">
    <label class="layui-form-label">网站标题</label>
    <div class="layui-input-block">
      <input type="text" name="title" disabled class="layui-input">
    </div>
  </div>
  <div class="layui-form-item">
    <label class="layui-form-label">网站链接</label>
    <div class="layui-input-block">
      <input type="text" name="url" disabled class="layui-input">
    </div>
  </div>
  <div class="layui-form-item">
    <label class="layui-form-label">网站图标</label>
    <div class="layui-input-block">
      <input type="text" name="iconurl" disabled class="layui-input">
    </div>
  </div>
  <div class="layui-form-item">
    <label class="layui-form-label">网站描述</label>
    <div class="layui-input-block">
      <input type="text" name="description" disabled class="layui-input">
    </div>
  </div>
  <div class="layui-form-item">
    <label class="layui-form-label">分类名称</label>
    <div class="layui-input-block">
      <input type="text" name="category" disabled class="layui-input">
    </div>
  </div>
  <div class="layui-form-item">
    <label class="layui-form-label">联系邮箱</label>
    <div class="layui-input-block">
      <input type="text" name="email" disabled class="layui-input">
    </div>
  </div>
  <div class="layui-form-item">
    <label class="layui-form-label">申请者IP</label>
    <div class="layui-input-block">
      <input type="text" name="ip" disabled class="layui-input">
    </div>
  </div>
  <div class="layui-form-item">
    <label class="layui-form-label">申请者UA</label>
    <div class="layui-input-block">
      <input type="text" name="ua" disabled class="layui-input">
    </div>
  </div>
  <div class="layui-form-item">
    <label class="layui-form-label">申请时间</label>
    <div class="layui-input-block">
      <input type="text" name="time" disabled class="layui-input">
    </div>
  </div>
  </form>
</ul>
<!--编辑-->
<ul class="edit" style = "margin-top:18px;display:none;padding-right: 10px;" >
    <form class="layui-form" lay-filter="edit">
  <div class="layui-form-item" style = "display:none;">
    <label class="layui-form-label">ID</label>
    <div class="layui-input-block">
      <input type="text" name="id" required  disabled lay-verify="required" class="layui-input">
    </div>
  </div>
  <div class="layui-form-item">
    <label class="layui-form-label">网站标题</label>
    <div class="layui-input-block">
      <input type="text" name="title" required  lay-verify="required" class="layui-input">
    </div>
  </div>
  <div class="layui-form-item">
    <label class="layui-form-label">网站链接</label>
    <div class="layui-input-block">
      <input type="text" name="url" required  lay-verify="required" class="layui-input">
    </div>
  </div>
  <div class="layui-form-item">
    <label class="layui-form-label">网站图标</label>
    <div class="layui-input-block">
      <input type="text" name="iconurl"  class="layui-input">
    </div>
  </div>
  <div class="layui-form-item">
    <label class="layui-form-label">网站描述</label>
    <div class="layui-input-block">
      <input type="text" name="description"  class="layui-input">
    </div>
  </div>
  <div class="layui-form-item">
    <label class="layui-form-label">网站分类</label>
    <div class="layui-input-block">
      <select name="edit_category" required lay-verify="required" lay-search>
        <?php echo_category(true); ?>
      </select>
    </div>
  </div>
    <div class="layui-form-item">
    <div class="layui-input-block">
      <button class="layui-btn" lay-submit lay-filter="edit_serv">保存</button>
      <button class="layui-btn" lay-submit lay-filter="edit_serv_2">保存并通过</button>
    </div>
  </div>
  </form>
</ul>
<script src = '<?php echo $libs?>/jquery/jquery-3.6.0.min.js'></script>
<script src = "./templates/admin/js/public.js?v=<?php echo $Ver;?>"></script>
<?php load_static('js.layui');?>
<script>

layui.use(['element','table','layer','form','util','dropdown'], function(){
    var element = layui.element;
    var table = layui.table;
    var util = layui.util;
    var form = layui.form;
    var dropdown = layui.dropdown;
    var layer = layui.layer;
    var limit = localStorage.getItem(u + "_limit") || 50;
    
    form.val('conf', <?php echo json_encode($data);?>);
//表头
var cols=[
      //{type:'checkbox'}, //开启复选框
      {field:'id',title:'ID',width:60,sort:true}
      ,{field:'iconurl',title:'图标',width:60,templet:function(d){
          if (d.iconurl.length !== 0){
              return '<img style="width: 28px" src="' + d.iconurl + '" />'
          }else{
              return '无';
          }
      }}
      ,{field:'title',title:'名称',minWidth:150,sort:true}
      ,{field:'url',title:'链接',minWidth:120,sort:true,templet:function(d){
          return '<a style="color:#3c78d8" target="_blank" href="'+d.url+'">'+d.url+'</a>'
      }}
      ,{field:'description',title:'描述',minWidth:120,sort:true}
      ,{field:'category_name',title:'分类',minWidth:120,sort:true}
      ,{field:'email',title:'Email',minWidth:120,sort:true}
      ,{field:'ip',title:'申请者IP',minWidth:140,sort:true,templet:function(d){
          return '<a style="color:#3c78d8" title="查询归属地" href="javascript:;" onclick="query_ip(\'' + d.ip +'\')">'+d.ip+'</a>'
      }}
      ,{field:'time',title: '申请时间',minWidth:160,sort:true,templet:function(d){
          if(d.time == null){return '';}
          else{return timestampToTime(d.time);}}} 
      ,{field:'state',title:'状态',width:120,templet:function(d){
          if (d.state == 0){
              return '待审核'
          }else if(d.state == 1){
              return '手动通过';
          }else if(d.state == 2){
              return '已拒绝';
          }else if(d.state == 3){
              return '自动通过';
          }else{
              return 'null';
          }
      }} 
      ,{title:'操作',toolbar:'#link_operate',width:130}
    ]
    //读取列筛选
    var local = layui.data('table-filter-apply-list'); 
    layui.each(cols, function(index, item){
        if(item.field in local){
            item.hide = local[item.field];
        }
    });
//表渲染
table.render({
    elem: '#apply_list'
    ,height: 'full-50'
    ,url: get_api('read_apply_list') 
    ,page: true 
    ,limit:limit
    ,even:true
    ,loading:true
    ,toolbar: '#user_tool'
    ,id:'apply_list'
    ,cols: [cols]
    ,response: {statusCode: 1 } 
    ,done: function (res, curr, count) {
        var temp_limit = $(".layui-laypage-limits option:selected").val();
        if(temp_limit > 0 && localStorage.getItem(u + "_limit") != temp_limit){
            localStorage.setItem(u + "_limit",temp_limit);
        }
        $(".layui-table-tool-self").addClass('layui-hide-xs');//手机端隐藏defaultToolbar
        //记忆列筛选
        var that = this;
        that.elem.next().on('mousedown', 'input[lay-filter="LAY_TABLE_TOOL_COLS"]+', function(){
            var input = $(this).prev()[0];
            layui.data('table-filter-apply-list', {
                key: input.name,value: input.checked
            });
        });
    }
});


//用户行工具栏事件
table.on('tool(apply_list)', function(obj){
    var data = obj.data;
    var that = this;
    if(obj.event === 'operation'){
        // 0.待审核 1.手动通过 2.已拒绝 3.自动通过 
        if(data.state == 0){
            menu = [{title: '详情',id: 0},{title: '编辑',id: 1},{title: '通过',id: 2}, {title: '拒绝',id: 3}, {title: '删除',id: 4}];
        }else if(data.state == 1 || data.state == 3 ) {
            menu = [{title: '详情',id: 0},{title: '删除',id: 4}];
        }else {
            menu = [{title: '详情',id: 0},{title: '删除',id: 4}];
        }
        dropdown.render({
            elem: that
            ,show: true
            ,data: menu
            ,click: function(d, othis){
            //根据 id 做出不同操作
                if(d.id === 0){
                    form.val('details', {
                        "title": data.title
                        ,"url": data.url
                        ,"iconurl": data.iconurl
                        ,"description": data.description
                        ,"email": data.email
                        ,"category": data.category_name + '  ID:'+ data.category_id 
                        ,"ip": data.ip
                        ,"ua": data.ua
                        ,"time":timestampToTime(data.time)
                    });
                    if(document.body.clientWidth < 768){area = ['100%' , '100%'];}else{area = ['768px' , '570px'];}
                    layer.open({
                        type: 1,
                        scrollbar: false,
                        shadeClose: true,
                        title: '详情',
                        area : area,
                        content: $('.details')
                    });
                }else if(d.id === 1){
                    form.val('edit', {
                        "title": data.title
                        ,"id": data.id
                        ,"url": data.url
                        ,"iconurl": data.iconurl
                        ,"edit_category": data.category_id
                        ,"description": data.description
                    });
                    if(document.body.clientWidth < 768){area = ['100%' , '100%'];}else{area = ['768px' , '420px'];}
                        layer.open({
                            type: 1,
                            scrollbar: false,
                            shadeClose: true,
                            title: '编辑',
                            area : area,
                            content: $('.edit')
                        });
                }else{
                    layer.load(2, {shade: [0.1,'#fff']});//加载层
                    $.post(get_api('write_apply',d.id),{"id" : data.id },function(data,status){
                        if(data.code == 1){
                            layer.msg(data.msg, {icon: 1});
                            setTimeout(() => {location.reload();}, 700);
                        }else{
                            layer.msg(data.msg, {icon: 5});
                        }
                        layer.closeAll('loading');//关闭加载层
                    });
                }
            }
        }); 
    }
});

//表头工具
table.on('toolbar(apply_list)', function(obj){
    var checkStatus = table.checkStatus(obj.config.id),id='';
    var data = checkStatus.data;
    switch(obj.event){
        case 'conf':
            if(document.body.clientWidth < 768){area = ['100%' , '100%'];}else{area = ['768px' , '520px'];}
            layer.open({
                type: 1,
                scrollbar: false,
                shadeClose: true,
                title: '收录设置',
                area : area,
                content: $('.conf')
            });
        break;
        
        case 'empty':
            layer.confirm('确定清空数据？',{icon: 3, title:'温馨提示！'}, function(index){
                layer.load(2, {shade: [0.1,'#fff']});//加载层
                $.post(get_api('write_apply','empty'),{"id" : data.id },function(data,status){
                    if(data.code == 1){
                        layer.msg(data.msg, {icon: 1});
                        setTimeout(() => {location.reload();}, 700);
                    }else{
                        layer.msg(data.msg, {icon: 5});
                    }
                    layer.closeAll('loading');//关闭加载层
                });
            });
        break;
        case 'oepn':
            window.open("./index.php?c=apply&u=<?php echo $u?>", "_blank");
            break;
        
    }
});

//保存配置
form.on('submit(conf)', function(data){
    layer.load(2, {shade: [0.1,'#fff']});//加载层
    $.post(get_api('write_apply','set'),data.field,function(data,status){
        if(data.code == 1){
            layer.msg(data.msg, {icon: 1});
            setTimeout(() => {location.reload();}, 700);
        }else{
            layer.msg(data.msg, {icon: 5});
        }
        layer.closeAll('loading');//关闭加载层
    });
    return false; 
});
    
//保存链接
form.on('submit(edit_serv)', function(data){
    if( data.field.edit_category.length == 0){
        layer.msg('请选择分类!', {icon: 5});
        return false; 
    } 
    layer.load(2, {shade: [0.1,'#fff']});//加载层
    $.post(get_api('write_apply','edit'),data.field,function(data,status){
        if(data.code == 1){
            layer.msg(data.msg, {icon: 1});
            setTimeout(() => {location.reload();}, 700);
        }else{
            layer.msg(data.msg, {icon: 5});
        }
        layer.closeAll('loading');//关闭加载层
    });
    return false; 
});

//保存链接并通过
form.on('submit(edit_serv_2)', function(data){
    if( data.field.edit_category.length == 0){
        layer.msg('请选择分类!', {icon: 5});
        return false; 
    }
    $.post(get_api('write_apply','edit'),data.field,function(d,status){
        if(d.code == 1){
            $.post(get_api('write_apply',"2"),{"id" : data.field.id},function(d,status){
                if(d.code == 1){
                    layer.msg(d.msg, {icon: 1});
                    setTimeout(() => {location.reload();}, 700);
                }else{
                    layer.msg(d.msg, {icon: 5});
                }
            });
        }else{
            layer.msg(d.msg, {icon: 5});
        }
    });
    return false; 
});
//layui end
});
</script>
</body>
</html>