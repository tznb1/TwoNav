<?php $title='站点设置';$awesome=true; require 'header.php'; ?>
<body>
<div class="layuimini-container">
    <div class="layuimini-main">
        <form class="layui-form" lay-filter="form">
        <div class="layui-form layuimini-form layui-form-pane">
        
           <div class="layui-form-item">
                <label class="layui-form-label">主标题</label>
                <div class="layui-input-block">
                    <input type="text" name="title" class="layui-input">
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">副标题</label>
                <div class="layui-input-block">
                    <input type="text" name="subtitle" class="layui-input">
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">Logo</label>
                <div class="layui-input-block">
                    <input type="text" name="logo" class="layui-input">
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">关键字</label>
                <div class="layui-input-block">
                    <input type="text" name="keywords" class="layui-input">
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">描述</label>
                <div class="layui-input-block">
                    <input type="text" name="description" class="layui-input">
                </div>
            </div>
            
            <div class="layui-form-item">
                <label class="layui-form-label">链接模式</label>
                <div class="layui-input-inline" >
                    <select name="link_model" >
                        <option value="direct" selected>直连模式</option>
                        <option value="Privacy">隐私保护 ( header )</option>
                        <option value="Privacy_js">隐私保护 ( js )</option>
                        <option value="Privacy_meta">隐私保护 ( meta )</option>
                        <option value="301">301重定向 ( 慎用 )</option>
                        <option value="302">302重定向</option>
                        <option value="Transition">过渡页面</option>
                    </select>
                </div>
                <div class="layui-form-mid layui-word-aux">直连模式无法统计点击数且无法使用备用链接,但它响应最快!</div>
            </div>
            
            <div class="layui-form-item">
                <label class="layui-form-label">链接图标</label>
                <div class="layui-input-inline" >
                    <select name="link_icon">
                        <option value="0" selected>离线图标</option>
                        <!--<option value="1" >本地服务(支持缓存)</option>-->
                        <option value="2" >favicon.rss.ink (小图标)</option>
                        <option value="4" >api.15777.cn</option>
                        <option value="5" >favicon.cccyun.cc</option>
                        <option value="6" >api.iowen.cn</option>
                    </select>
                </div>
                <div class="layui-form-mid layui-word-aux">所有API接口均由其他大佬提供!若有异常请尝试更换接口!</div>
            </div>
            
           <div class="layui-form-item">
                <label class="layui-form-label">站点图标</label>
                <div class="layui-input-block">
                    <input type="text" name="site_icon" class="layui-input" placeholder="浏览器显示的图标,留空则使用默认图标!" style="padding-left: 95px;">
                    <div style="position: absolute; top:0px;">
                        <span><a class="layui-btn layui-btn-primary" id="<?php echo check_purview('Upload_icon',1)?'upload':'no_purview'?>"><i class="fa fa-upload"></i> 上传</a></span>
                    </div>
                </div>
            </div>

<?php if(check_purview('header',1)){ ?>
            <div class="layui-form-item layui-form-text">
                <label class="layui-form-label">头部(header)代码 - 用户</label>
                <div class="layui-input-block">
                    <textarea name="custom_header" class="layui-textarea" placeholder=''></textarea>
                </div>
            </div>

<?php } ?>
<?php if(check_purview('footer',1)){ ?>
            <div class="layui-form-item layui-form-text">
                <label class="layui-form-label">底部(footer)代码 - 用户</label>
                <div class="layui-input-block">
                    <textarea name="custom_footer" class="layui-textarea" placeholder=''></textarea>
                </div>
            </div>

<?php } ?>
            <div class="layui-form-item">
                <button class="layui-btn layui-btn-primary layui-border-black" id="help" sort_id="7968924">帮助</button>
                <button class="layui-btn layui-btn-normal" lay-submit lay-filter="save">保存</button>
            </div>
        </div>
    </form>
    </div>
</div>
<script src = "<?php echo $libs;?>/jquery/jquery-3.6.0.min.js"></script>
<script src = "./templates/admin/js/public.js?v=<?php echo $Ver;?>"></script>
<?php load_static('js.layui');?>
<script>
layui.use(['jquery','form','upload'], function () {
    var form = layui.form,
        layer = layui.layer,
        upload = layui.upload,
        form_data = <?php echo json_encode(unserialize( get_db("user_config", "v", ["k" => "s_site","uid"=>$USER_DB['ID']]) ));?>
        
    //表单赋值
    form.val('form', form_data);
    //图标上传
    upload.render({
        elem: '#upload'
        ,exts: 'jpg|jpeg|png|bmp|gif|ico|svg'
        ,acceptMime:  'image/*'
        ,accept: 'file'
        ,size: 1024 
        ,url: get_api('write_site_setting')
        ,done: function(res){
            if(res.code == 1){
                layer.alert("您可能要清除浏览器缓存(CTRL+F5)才会看到新图标!",{icon:1,title:'上传成功',anim: 2,closeBtn: 0,btn: ['知道了']},function () {location.reload();});
            }else{
                layer.msg(res.msg, {icon: 5});
            }
        }
    });
    
    $(document).on('click', '#no_purview', function() {
        layer.msg("权限不足", {icon: 2});
    });
    
    //监听提交
    form.on('submit(save)', function (data) {
        $.post('./index.php?c=api&method=write_site_setting&u='+u,data.field,function(data,status){
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