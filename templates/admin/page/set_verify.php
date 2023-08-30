<?php $title='验证页面 - 设置'; require 'header.php'; ?>
<body>
<div class="layuimini-container">
    <div class="layuimini-main">
        <form class="layui-form" lay-filter="form">
        <div class="layui-form layuimini-form layui-form-pane">
            <blockquote class="layui-elem-quote layui-text">注意事项: 需模板支持,提示内容以http开头则打开网页,其他内容则弹出提示<br />使用场景: 加密链接/加密分类/二级密码/书签分享等</blockquote>
            
            <div class="layui-form-item layui-form-text">
                <label class="layui-form-label">加密链接</label>
                <div class="layui-input-block">
                    <textarea name="link_tip" class="layui-textarea" placeholder='查看加密链接或分类时提示如何获取密码,可为空'></textarea>
                </div>
            </div>
            
            <div class="layui-form-item layui-form-text">
                <label class="layui-form-label">书签分享</label>
                <div class="layui-input-block">
                    <textarea name="share_tip" class="layui-textarea" placeholder='获取书签分享提取码的提示,可为空'></textarea>
                </div>
            </div>

            <div class="layui-form-item">
                <div class="layui-input-block"><button class="layui-btn layui-btn-warm" type="button" id="close" >关闭</button><button class="layui-btn layui-btn-normal" lay-submit lay-filter="save">保存</button></div>
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
    form.val('form', <?php echo json_encode(unserialize( get_db("user_config", "v", ["k" => "s_verify_page","uid"=>$USER_DB['ID']]) ));?>);
    
    //监听提交
    form.on('submit(save)', function (data) {
        $.post('./index.php?c=api&method=write_verify_page&u='+u,data.field,function(data,status){
            if(data.code == 1) {
                layer.msg(data.msg, {icon: 1});
            }else{
                layer.msg(data.msg, {icon: 5});
            }
        });
        return false;
    }); 
    //关闭按钮
    $(document).on('click', '#close', function() {
        parent.layer.close(parent.layer.getFrameIndex(window.name));
    });
});
</script>
</body>
</html>