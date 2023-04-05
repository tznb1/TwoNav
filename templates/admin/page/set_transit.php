<?php $title='过渡页面 - 设置'; require 'header.php'; ?>
<body>
<div class="layuimini-container">
    <div class="layuimini-main">
        <form class="layui-form" lay-filter="form">
        <div class="layui-form layuimini-form layui-form-pane">
            <blockquote class="layui-elem-quote layui-text">注意: 存在备用链接时停留时间可能无效!</blockquote>
            <div class="layui-form-item">
                <label class="layui-form-label">访客停留</label>
                <div class="layui-input-inline">
                    <input type="number" min="0" max="60" lay-verify="required|number" name="visitor_stay_time" value = "3" autocomplete="off" placeholder="访客停留时间，单位s" class="layui-input">
                </div>
                <div class="layui-form-mid layui-word-aux">访客停留时间，单位秒</div>
            </div>

            <div class="layui-form-item">
                <label class="layui-form-label">管理员停留</label>
                <div class="layui-input-inline">
                    <input type="number" min="0" max="60" lay-verify="required|number" name="admin_stay_time" value = "5" autocomplete="off" placeholder="管理员停留时间，单位s" class="layui-input">
                </div>
                <div class="layui-form-mid layui-word-aux">管理员停留时间，单位秒</div>
            </div>
            
            <div class="layui-form-item">
                <div class="layui-input-block"><button class="layui-btn layui-btn-normal" lay-submit lay-filter="save">保存</button></div>
            </div>
        </div>
    </form>
    </div>
</div>
<script src = "<?php echo $libs;?>/jquery/jquery-3.6.0.min.js"></script>
<?php load_static('js.layui');?>
<script>
layui.use(['jquery','form'], function () {
        var form = layui.form,
            layer = layui.layer;
            
    //表单赋值
    form.val('form', <?php echo json_encode(unserialize( get_db("user_config", "v", ["k" => "s_transition_page","uid"=>$USER_DB['ID']]) ));?>);
    
    //监听提交
    form.on('submit(save)', function (data) {
        $.post('./index.php?c=api&method=write_transit_setting&u='+u,data.field,function(data,status){
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
</body>
</html>