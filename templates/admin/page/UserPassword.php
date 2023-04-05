<?php $title='修改密码'; require 'header.php'; ?>
<body>
<div class="layuimini-container">
    <div class="layuimini-main">
        <div class="layui-form layuimini-form layui-form-pane">
            <blockquote class="layui-elem-quote layui-text" style="">注意: 在您成功修改密码后,您在其他浏览器的登录保持都将失效 ( 需重新登录 ) </blockquote>
            <div class="layui-form-item">
                <label class="layui-form-label required">旧的密码</label>
                <div class="layui-input-block">
                    <input type="password" name="Password" lay-verify="required" lay-reqtext="旧的密码不能为空" placeholder="请输入旧的密码"  value="" class="layui-input">
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label required">新的密码</label>
                <div class="layui-input-block">
                    <input type="password" name="NewPassword" lay-verify="required" lay-reqtext="新的密码不能为空" placeholder="请输入新的密码"  value="" class="layui-input">
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label required">重复新密码</label>
                <div class="layui-input-block">
                    <input type="password" name="NewPassword2" lay-verify="required" lay-reqtext="新的密码不能为空" placeholder="请输入新的密码"  value="" class="layui-input">
                </div>
            </div>
            <div class="layui-form-item">
                <div class="layui-input-block"><button class="layui-btn layui-btn-normal" lay-submit lay-filter="save">确认保存</button></div>
            </div>
        </div>
    </div>
</div>
<script src = "<?php echo $libs;?>/jquery/jquery-3.6.0.min.js"></script>
<script src = "<?php echo $libs;?>/jquery/jquery.md5.js"></script>
<script src = "./templates/admin/js/public.js?v=<?php echo $Ver;?>"></script>
<?php load_static('js');?>
<script>
layui.use(['jquery','form','miniTab'], function () {
        var form = layui.form,
            layer = layui.layer,
            miniTab = layui.miniTab;

    //监听提交
    form.on('submit(save)', function (data) {
        $.post(get_api('write_user_password'),{Password:$.md5(data.field.Password),NewPassword:$.md5(data.field.NewPassword)},function(data,status){
            if(data.code == 1) {
                var index = layer.alert("密码修改成功!", function () {
                    layer.close(index);
                    miniTab.deleteCurrentByIframe();
                });
                //layer.alert("密码修改成功,请重新登录!", function () {top.location.href='./?c=index';});
            }else{
                layer.msg(data.msg, {icon: 5});
            }
        });
    }); 
});
</script>
</body>
</html>