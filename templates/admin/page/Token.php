<?php $title='Token'; require 'header.php'; ?>
<body>
<div class="layuimini-container">
    <div class="layuimini-main">
        <div class="layui-form layuimini-form layui-form-pane">
            <blockquote class="layui-elem-quote layui-text" style="">注意: Token(令牌),是您访问API的凭证 (不了解&不需要请勿设置) ,等同于您的账号密码,请妥善保管</blockquote>
            <div class="layui-form-item">
                <label class="layui-form-label required">登录密码</label>
                <div class="layui-input-block">
                    <input type="password" name="Password" lay-verify="required" lay-reqtext="请输入登录密码" placeholder="请输入登录密码"  class="layui-input">
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">SecretKey</label>
                <div class="layui-input-block">
                    <input type="text" id="SecretKey" lay-reqtext="点击查询或更换查看" placeholder="点击查询或更换查看" class="layui-input">
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">Token</label>
                <div class="layui-input-block">
                    <input type="text" id="Token" lay-reqtext="点击查询或更换查看" placeholder="点击查询或更换查看" class="layui-input">
                </div>
            </div>
            <div class="layui-form-item">
                <div class="layui-input-block">
                    <button class="layui-btn layui-btn-normal" lay-submit lay-filter="query">查询</button>
                    <button class="layui-btn layui-btn-warm" lay-submit lay-filter="replace">更换</button>
                    <button class="layui-btn layui-btn-danger" lay-submit lay-filter="delete">删除</button>
                </div>
            </div>
        </div>
    </div>
</div>
<script src = "<?php echo $libs;?>/jquery/jquery-3.6.0.min.js"></script>
<script src = "<?php echo $libs;?>/jquery/jquery.md5.js"></script>
<script src = "./templates/admin/js/public.js?v=<?php echo $Ver;?>"></script>
<?php load_static('js.layui');?>
<script>
layui.use(['jquery','form'], function () {
        var form = layui.form,
            layer = layui.layer;
            
    form.on('submit(query)', function (data) {
        $.post(get_api('read_token'),{Password:$.md5(data.field.Password)},function(data,status){
            if(data.code == 1) {
                $("#Token").val(data.Token);
                $("#SecretKey").val(data.SecretKey);
            }else{
                layer.msg(data.msg, {icon: 5});
            }
        });
    }); 
    
    form.on('submit(replace)', function (data) {
        $.post(get_api('write_token'),{Password:$.md5(data.field.Password),type:'replace'},function(data,status){
            if(data.code == 1) {
                $("#Token").val(data.Token);
                $("#SecretKey").val(data.SecretKey);
            }else{
                layer.msg(data.msg, {icon: 5});
            }
        });
    }); 
    
    form.on('submit(delete)', function (data) {
        $.post(get_api('write_token'),{Password:$.md5(data.field.Password),type:'delete'},function(data,status){
            if(data.code == 1) {
                $("#Token").val(data.Token);
                $("#SecretKey").val(data.SecretKey);
            }else{
                layer.msg(data.msg, {icon: 5});
            }
        });
    }); 
});
</script>
</body>
</html>