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
                <label class="layui-form-label" title="存在备用链接并且链接模式为隐私保护或302重定向时生效">主链优先</label>
                <div class="layui-input-inline" >
                    <select name="main_link_priority" >
                        <option value="0" selected>关闭</option>
                        <option value="1">开启 (快速检测)</option>
                        <option value="2">开启 (常规检测)</option>
                        <option value="3">开启 (强制优先)</option>
                    </select>
                </div>
                <div class="layui-form-mid layui-word-aux">特定条件下生效,主链接可用则直接跳转反之进入过渡页,用法参照帮助文档</div>
            </div>
            
            <div class="layui-form-item">
                <label class="layui-form-label">链接图标</label>
                <div class="layui-input-inline" >
                    <select name="link_icon">
                        <option value="0" selected>离线图标</option>
                        <option value="20" >本地服务</option>
                        <option value="21" >本地服务(伪静态)</option>
                        <option value="2" >favicon.png.pub (小图标)</option>
                        <option value="4" >api.15777.cn</option>
                        <option value="5" >favicon.cccyun.cc</option>
                        <option value="6" >api.iowen.cn</option>
                        <!--<option value="7" >toolb.cn</option>-->
                        <!--<option value="8" >apis.jxcxin.cn</option>-->
                    </select>
                </div>
                <div class="layui-form-mid layui-word-aux">所有API接口均由其他大佬提供!若有异常请尝试更换接口!</div>
            </div>
            
            <div class="layui-form-item">
                <label class="layui-form-label">热门网址</label>
                <div class="layui-input-inline" >
                    <select name="top_link">
                        <option value="0" selected>不显示</option>
                        <option value="5" >显示5条</option>
                        <option value="10" >显示10条</option>
                        <option value="15" >显示15条</option>
                        <option value="20" >显示20条</option>
                    </select>
                </div>
                <div class="layui-form-mid layui-word-aux">在主页显示热门网址(点击排行)</div>
            </div>
            
            <div class="layui-form-item">
                <label class="layui-form-label">最新网址</label>
                <div class="layui-input-inline" >
                    <select name="new_link">
                        <option value="0" selected>不显示</option>
                        <option value="5" >显示5条</option>
                        <option value="10" >显示10条</option>
                        <option value="15" >显示15条</option>
                        <option value="20" >显示20条</option>
                    </select>
                </div>
                <div class="layui-form-mid layui-word-aux">在主页显示最新的网址(创建时间)</div>
            </div>
            
           <div class="layui-form-item">
                <label class="layui-form-label">链接数量</label>
                <div class="layui-input-inline">
                    <input type="number" name="max_link" class="layui-input" value="0" placeholder="输入范围: 0-100" lay-verify="required">
                </div>
                <div class="layui-form-mid layui-word-aux">限制首页每个分类下显示多少链接,0表示不限制</div>
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
                <button type="button" class="layui-btn layui-btn-primary layui-border-black" id="help" sort_id="7968924">帮助</button>
<?php if($global_config['Default_User'] != U ){ ?>
                <button type="button" class="layui-btn layui-btn-primary layui-border-black" id="sdhp" data=>设为默认主页</button>
<?php } ?>
                <button class="layui-btn layui-btn-normal" lay-submit lay-filter="save">保存</button>
            </div>
        </div>
    </form>
    </div>
</div>
<script src="<?php echo $libs;?>/jquery/jquery-3.6.0.min.js"></script>
<script src="<?php echo $libs;?>/jquery/jQueryCookie.js"></script>
<script src="./templates/admin/js/public.js?v=<?php echo $Ver;?>"></script>
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
    
    $("#sdhp").text( getCookie("Default_User") == u ? '取消默认主页' : '设为默认主页')
    $('#sdhp').click(function () {
        if(getCookie("Default_User") == u){
            $.removeCookie("Default_User");
            $("#sdhp").text('设为默认主页')
        }else{
            $.cookie("Default_User",u,{expires: 360});
            $("#sdhp").text('取消默认主页')
        }
        layer.msg("设置成功", {icon: 1});
        return false;
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