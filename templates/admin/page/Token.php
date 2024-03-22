<?php $title='Token'; require 'header.php'; ?>
<body>
<div class="layuimini-container">
    <div class="layuimini-main">
        <div class="layui-form layuimini-form layui-form-pane">
            
            <div class="layui-collapse" lay-accordion>
              <div class="layui-colla-item" >
                <div class="layui-colla-title">注意事项</div>
                <div class="layui-colla-content layui-show">
                    <blockquote class="layui-elem-quote layui-text" style="">Token(令牌),是您访问API的凭证 (不了解&不需要请勿设置) ,等同于您的账号密码,请妥善保管</blockquote>
                </div>
              </div>
              <div class="layui-colla-item">
                <div class="layui-colla-title">API模式的差别</div>
                <div class="layui-colla-content">
                  <p>安全模式: 仅提供TwoNav自身的API接口,不兼容Onenav的API接口!</p>
                  <p>兼容模式: 兼容部分OneNav的API接口,以便于其他插件调用!不支持访客调用!</p>
                  <p>如果你未使用相关扩展插件,则无需修改模式并将Token删除,以提高账号的安全性!</p>
                </div>
              </div>
              <div class="layui-colla-item">
                <div class="layui-colla-title">如何使用Chrome浏览器扩展 [非官方]</div>
                <div class="layui-colla-content">
                    注意事项:  由于浏览器扩展插件非TwoNav所开发适配,如存在Bug或无法使用属正常现象!<br />
                    安装扩展:  扩展插件获取: 
                        <a style="color:#3c78d8"  href="https://chromewebstore.google.com/detail/onenav/omlkjgkogkfpjbdigianpdbjncdchdco" >Chrome/Edge</a> /
                        <a style="color:#3c78d8"  href="https://addons.mozilla.org/zh-CN/firefox/addon/onenav" >Firefox</a> 下载并安装 ( 已知0.9.24 - 1.1.0可用,其他版本未知 )<br />
                    后台设置: 1.TwoNav后台>右上角账号>安全设置>API模式>设为<兼容模式> 2.在本页面获取Token<br />
                    扩展设置: 设置>填入域名和Token并保存即可,注意: 扩展插件中的推荐内容与本程序无关<br />
                    问题1: 对于单用户使用,确保系统设置中默认用户是当前用户即可!多用户使用时需开启二级域名功能并将域名替换成用户的二级域名,注意结尾不需要带/<br />
                    问题2: 因为插件非官方开发维护,能用就尽量不要更新,更新后可能会导致无法正常使用!如有发现不兼容我们也会尽快跟进!<br />
                    问题3: 因为国内环境限制,你可能无法访问谷歌,这种情况你可以在交流群获取插件(安装方法自行百度,部分浏览器可能需要开发者模式加载)
                </div>
              </div>
              <div class="layui-colla-item">
                <div class="layui-colla-title">如何使用uTools扩展插件 [非官方]</div>
                <div class="layui-colla-content">
                  <p>前言: 由于uTools扩展插件非TwoNav所开发适配,如存在Bug或无法使用属正常现象!</p>
                  <p>安装: 在uTools插件应用市场>搜索OneNav>点击获取 </p>
                  <p>设置S: 1.TwoNav后台>右上角账号>安全设置>API模式>设为<兼容模式> 2.在本页面获取SecretKey ( 即插件设置中的API KEY )</p>
                  <p>设置C: 打开uTools中的OneNav,点击右下角小齿轮>输入网站地址/用户名/API KEY</p>
                </div>
              </div>
            </div>
            
            <div class="layui-form-item" style="margin-top: 15px;">
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