<?php 
if($USER_DB['UserGroup'] != 'root'){$content='您没有权限访问此页面'; require(DIR.'/templates/admin/page/404.php');exit;}
$title='默认设置';require(dirname(__DIR__).'/header.php'); 
$s_site = unserialize( get_db("global_config", "v", ["k" => "s_site"]));
$LoginConfig = unserialize( get_db("global_config", "v", ["k" => "LoginConfig"]));
?>
<body>
<div class="layuimini-container">
    <div class="layuimini-main">
    <form class="layui-form" lay-filter="form">
        <div class="layui-form layuimini-form layui-form-pane">
            <blockquote class="layui-elem-quote layui-text" style="">1.本页功能<a href="https://gitee.com/tznb/TwoNav/wikis/pages?sort_id=7968669&doc_id=3767990" target="_blank">授权用户</a>专享<br />2.用户注册后默认使用此方案<br />3.此功能不会修改现有用户的配置<br />4.如果您不理解选项的作用请勿乱改   </blockquote>
            <fieldset class="layui-elem-field layui-field-title"><legend>安全设置</legend></fieldset>
            
            <div class="layui-form-item">
                <label class="layui-form-label">登录保持</label>
                <div class="layui-input-inline" >
                    <select name="Session">
                        <option value="0">浏览器关闭时</option>
                        <option value="15">15天</option>
                        <option value="30">30天</option>
                        <option value="60">60天</option>
                        <option value="90">90天</option>
                        <option value="180">180天</option>
                        <option value="360" selected>360天</option>
                    </select>
                </div>
                <div class="layui-form-mid layui-word-aux">登陆后保持的时间</div>
            </div>
            
            <div class="layui-form-item">
                <label class="layui-form-label">HttpOnly</label>
                <div class="layui-input-inline" >
                    <select name="HttpOnly">
                        <option value="0" >禁止HttpOnly</option>
                        <option value="1" selected>使用HttpOnly(荐)</option>
                    </select>
                </div>
                <div class="layui-form-mid layui-word-aux">防止跨站脚本窃取Cookie保护账号安全(无特殊情况不建议禁止)</div>
            </div>
            
            <div class="layui-form-item">
                <label class="layui-form-label">Key安全</label>
                <div class="layui-input-inline" >
                    <select name="KeySecurity">
                        <option value="0">0级(无)</option>
                        <option value="1" selected>1级(UA)</option>
                        <option value="2">2级(UA + IP )</option>
                    </select>
                </div>
                <div class="layui-form-mid layui-word-aux">保持登陆状态的Key算法,更高级别的算法可以降低被窃取的风险!</div>
            </div>
            
            <div class="layui-form-item">
                <label class="layui-form-label">Key清理</label>
                <div class="layui-input-inline" >
                    <select name="KeyClear">
                        <option value="3">3天未访问</option>
                        <option value="7" selected>7天未访问</option>
                        <option value="15">15天未访问</option>
                        <option value="30">30天未访问</option>
                        <option value="60">60天未访问</option>
                    </select>
                </div>
                <div class="layui-form-mid layui-word-aux">优先于登录保持,保持时间内未访问过则强制踢出登录</div>
            </div>
            
            <div class="layui-form-item">
                <label class="layui-form-label">API模式</label>
                <div class="layui-input-inline">
                    <select name="api_model">
                        <option value="security" selected>安全模式</option>
                        <option value="compatible">兼容模式</option>
                        <option value="compatible+open">兼容模式+开放</option>
                    </select>
                </div>
                <div class="layui-form-mid layui-word-aux">部分主题和插件需设为开放模式!</div>
            </div>
            
            <div class="layui-form-item">
                <label class="layui-form-label">登录后</label>
                <div class="layui-input-inline" >
                    <select name="login_page">
                        <option value="admin" selected>进入后台</option>
                        <option value="index">进入主页</option>
                        <option value="auto">自动识别</option>
                    </select>
                </div>
                <div class="layui-form-mid layui-word-aux">自动识别:移动设备登录则进入主页,反之进入后台</div>
            </div>
            
            <fieldset class="layui-elem-field layui-field-title"><legend>站点设置</legend></fieldset>
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
                        <option value="0" selected>离线图标(首字图标)</option>
                        <option value="20" >本地服务</option>
                        <option value="21" >本地服务(伪静态)</option>
                        <option value="2" >favicon.png.pub (小图标)</option>
                        <option value="6" >api.iowen.cn</option>
                    </select>
                </div>
                <div class="layui-form-mid layui-word-aux">所有API接口均由其他大佬提供!若有异常请尝试更换接口!</div>
            </div>
            
            <div class="layui-form-item layui-form-text">
                <label class="layui-form-label">头部(header)代码 - 用户</label>
                <div class="layui-input-block">
                    <textarea name="custom_header" class="layui-textarea" placeholder=''></textarea>
                </div>
            </div>
            
            <div class="layui-form-item layui-form-text">
                <label class="layui-form-label">底部(footer)代码 - 用户</label>
                <div class="layui-input-block">
                    <textarea name="custom_footer" class="layui-textarea" placeholder=''></textarea>
                </div>
            </div>
            
            <div class="layui-form-item"><div class="layui-input-block"><button class="layui-btn layui-btn-normal" lay-submit lay-filter="save">确认保存</button></div></div>
        </div>
    </form>
    </div>
</div>
<?php load_static('js.layui');?>
<script>
layui.use(['jquery','form'], function () {
    var form = layui.form;
    var layer = layui.layer;
    var $ = layui.jquery;
    
    //表单赋值
    form.val('form', <?php echo json_encode($s_site);?>);
    form.val('form', <?php echo json_encode($LoginConfig);?>);

    //监听提交
    form.on('submit(save)', function (data) {
        $.post('./index.php?c=api&method=write_default_settings&u='+u,data.field,function(data,status){
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