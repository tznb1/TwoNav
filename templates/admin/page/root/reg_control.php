<?php 
if($USER_DB['UserGroup'] != 'root'){$content='您没有权限访问此页面'; require(DIR.'/templates/admin/page/404.php');exit;}
$title='注册管理';require(dirname(__DIR__).'/header.php');
$user_groups = select_db('user_group',['id','code','name'],'');
?>
<style>
    .layui-table-tool-temp  {padding-right:1px;}
</style>
<body>
<div class="layuimini-container">
    <div class="layuimini-main">
        <table id="table" class="layui-table" lay-filter="table" style="margin: -3px 0;"></table>
    </div>
</div>
<!-- 表头工具栏 -->
<script type="text/html" id="toolbar">
    <div class="layui-btn-group"  >
        <button class="layui-btn layui-btn-sm" lay-event="generate">生成注册码</button>
        <button class="layui-btn layui-btn-sm layui-btn-danger" lay-event="del">删除</button>
        <button class="layui-btn layui-btn-sm" lay-event="refresh">刷新</button>
        <button class="layui-btn layui-btn-sm" lay-event="set">设置</button>
    </div>
</script>
<!--生成-->
<ul class="generate" style = "margin-top:18px;display:none;padding-right: 10px;" >
    <form class="layui-form " lay-filter="generate">

        <div class="layui-form-item " >
            <label class="layui-form-label">用户组</label>
            <div class="layui-input-inline">
                <select id="UserGroup" name="UserGroup" >
                    <option value="default">默认</option>
<?php foreach ($user_groups as $data){echo "                    <option value=\"{$data['code']}\">{$data['name']}</option>\n";} ?>
                </select>
            </div>
            <div class="layui-form-mid layui-word-aux">注册后用户所属的分组</div>
        </div>
        <div class="layui-form-item" >
            <label class="layui-form-label">生成数量</label>
            <div class="layui-input-inline">
                <input type="number" id = "number" name="number" required  lay-verify="required|generate_number" placeholder="请输入生成数量1-100" autocomplete="off" class="layui-input" value="1">
            </div>
            <div class="layui-form-mid layui-word-aux">范围:1-100</div>
        </div>
        
        <div class="layui-form-item">
            <div class="layui-input-block">
                <button class="layui-btn layui-btn-warm" type="button" id="close" >关闭</button>
                <button class="layui-btn" lay-submit lay-filter="generate">开始生成</button>
            </div>
        </div>
  </form>
</ul>
<!--设置-->
<ul class="Set" style = "margin-top:18px;display:none;padding-right: 10px;" >
    <form class="layui-form" lay-filter="Set">
        <div class="layui-form-item" >
            <label class="layui-form-label">注册提示</label>
            <div class="layui-input-block">
                <textarea name = "content" placeholder="可以填获取注册码的地址,留空则不显示!非http开头时将作为提示信息弹出!" rows = "7" class="layui-textarea"><?php echo get_db('global_config','v',['k'=>'reg_tips']);?></textarea>
            </div>
        </div>
        <div class="layui-form-item">
            <div class="layui-input-block">
                <button class="layui-btn layui-btn-warm" type="button" id="close" >关闭</button>
                <button class="layui-btn" lay-submit lay-filter="save">保存</button>
            </div>
        </div>
  </form>
</ul>
<script src = "<?php echo $libs;?>/jquery/jquery-3.6.0.min.js"></script>
<script src = "./templates/admin/js/public.js?v=<?php echo $Ver;?>"></script>
<?php load_static('js');?>
<script>
var host = Get_baseUrl() + '<?php echo 'index.php?c='.$global_config['Register']."&key=";?>';

layui.use(['table','layer','form'], function(){
    var table = layui.table;
    var form = layui.form;
    var layer = layui.layer;
    var limit = localStorage.getItem(u + "_limit") || 50;
    

    var cols=[[ //表头
      {type:'checkbox'}
      ,{field:'id',title:'id',width:80,sort:true}
      ,{field:'regcode',title:'注册码',width:120,sort:true}
      ,{field:'UserGroupName',title:'用户组',width:120,sort:true}
      ,{field:'url',title:'注册链接',minWidth:400,sort:true,templet:function(d){
          return host + d.regcode;
      }}
      ,{field:'add_time',title:'生成时间',width:170,sort:true,templet:function(d){
          return timestampToTime(d.add_time);
      }} 
      ,{field:'use_state',title:'状态',minWidth:400,sort:true,templet:function(d){
          if(d.use_time == 0){
              return d.use_state;
          }else{
              return timestampToTime(d.use_time) + ',' + d.use_state ;
          }
      }} 
    ]]
    
    
    //用户表渲染
    table.render({
        elem: '#table'
        ,height: 'full-80' //自适应高度
        ,url: get_api('read_regcode_list') //数据接口
        ,method: 'post'
        ,page: true 
        ,limit:limit
        ,limits: [20,50,100,300,500]
        ,even:true //隔行背景色
        ,loading:true //加载条
        ,response: {statusCode: 1 } 
        ,toolbar: '#toolbar'
        ,id:'table'
        ,cols: cols
        ,done: function (res, curr, count) {
            $(".layui-table-tool-self").addClass('layui-hide-xs');
        }
    });

    //头工具栏事件
    table.on('toolbar(table)', function(obj){
        var checkStatus = table.checkStatus(obj.config.id);
        switch(obj.event){
            //生成注册码
            case 'generate':
                index = layer.open({
                    type: 1,
                    scrollbar: false,
                    shadeClose: true,
                    title: '生成注册码',
                    area : ['100%','100%'],
                    content: $('.generate')
                });
                break;
      
            //刷新表格
            case 'refresh':
                table.reload('table');
                break;
      
            //设置
            case 'set':
                index = layer.open({
                    type: 1,
                    scrollbar: false,
                    shadeClose: true,
                    title: '设置',
                    area : ['100%','100%'],
                    content: $('.Set')
                });
                break;
            case 'del':
                if( checkStatus.data.length == 0 && ['LAYTABLE_COLS','LAYTABLE_EXPORT','LAYTABLE_PRINT'].indexOf(obj.event) == -1 ) {
                    layer.msg('未选中任何数据！');
                    return;
                }
                layer.confirm('确认删除?',{icon: 3, title:'温馨提示'}, function(index){
                    tableIds = checkStatus.data.map(function (value) {return value.id;});
                    tableIds = JSON.stringify(tableIds);
                    $.post(get_api('write_regcode','del') ,{"id":tableIds},function(data,status){
                        if(data.code == 1){
                            table.reload('table');
                            layer.msg(data.msg, {icon: 1});
                        }else{
                            layer.msg(data.msg, {icon: 5});
                        }
                    });
                    return false; 
                });
                break;
        };
    });
    //自定义表单验证
    form.verify({
        generate_number: function(value, item){
            console.log(value);
            if(value < 1 || value > 100){return '数量范围:1-100';}
        }
    });  
    
    //关闭按钮
    $(document).on('click', '#close', function() {
        layer.close(index);//关闭当前页
    });
    
    //保存设置
    form.on('submit(save)', function(data){
        $.post(get_api('write_regcode','set') ,data.field,function(data,status){
            if(data.code == 1){
                layer.msg(data.msg, {icon: 1});
                layer.close(index);
            }else{
                layer.msg(data.msg, {icon: 5});
            }
        });
        return false; 
    });
    //开始生成
    form.on('submit(generate)', function(data){
        $.post(get_api('write_regcode','generate'),{'number':data.field.number,'group':data.field.UserGroup,'regcode_length':8},function(data,status){
                    if(data.code == 1){
                        layer.msg('已完成',{ icon: 1 })
                        table.reload('table');
                        layer.close(index);
                    }else{
                        layer.msg(data.msg,{ icon: 5 } );
                    }
                });
        return false; 
    });
});
</script>
</body>
</html>