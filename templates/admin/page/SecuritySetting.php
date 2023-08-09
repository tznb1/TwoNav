<?php $title='安全设置'; require 'header.php'; 
$LoginConfig = unserialize($USER_DB['LoginConfig']);
$LoginConfig['totp_key'] = empty($LoginConfig['totp_key']) ? '0':'1';?>
<body>
<div class="layuimini-container">
    <div class="layuimini-main">
        <form class="layui-form" lay-filter="form">
        <div class="layui-form layuimini-form layui-form-pane">
            <blockquote class="layui-elem-quote layui-text" style="">注意: 出于账号安全考虑,在您成功保存本页设置后,您在其他浏览器的登录保持都将失效 ( 需重新登录 ) </blockquote>
            <div class="layui-form-item">
                <label class="layui-form-label">二级密码</label>
                <div class="layui-input-inline">
                    <input type="password" name="Password2" class="layui-input" placeholder="未设置">
                </div>
                <div class="layui-form-mid layui-word-aux">设置后访问后台时需要输入二级密码!不需要则留空!</div>
            </div>

            <div class="layui-form-item">
                <label class="layui-form-label">登录入口</label>
                <div class="layui-input-inline" >
                    <select name="Login">
                        <option value="0" selected>保持登录入口</option>
                        <option value="1" >重设登录入口</option>
                    </select>
                </div>
                <div class="layui-form-mid layui-word-aux">接口泄漏时可以选择重设登陆入口,更换后请及时保存!</div>
            </div>
            
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
                    </select>
                </div>
                <div class="layui-form-mid layui-word-aux">部分主题和插件需设为兼容 <a href="javascript:;" layuimini-content-href="Token" data-title="Token"><font color="red"> 获取API ( Token )</font></a></div>
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
            
            <div class="layui-form-item">
                <label class="layui-form-label required">登录密码</label>
                <div class="layui-input-inline">
                    <input type="Password" lay-verify="required" name="Password" class="layui-input" lay-reqtext="登录密码不能为空">
                </div>
                <div class="layui-form-mid layui-word-aux">需核对登录密码才能修改设置</div>
            </div>
            
            <div class="layui-form-item">
                <div class="layui-input-block">
                    <button class="layui-btn layui-btn-normal" lay-submit lay-filter="save">确认保存</button>
                    <button class="layui-btn layui-bg-purple" lay-submit lay-filter="open_totp">OTP 双重验证</button>
                </div>
            </div>
        </div>
    </form>
    </div>
</div>

<ul class="ul_totp" style="margin-top:18px;display:none;padding-right: 10px;">
    <form class="layui-form" lay-filter="ul_totp">
        
        <div class="layui-form-item">
            <label class="layui-form-label">二维码</label>
            <div id="qr"></div><div id="qrcode"></div>
        </div>
        
        <div class="layui-form-item">
            <label class="layui-form-label">秘钥</label>
            <div class="layui-input-inline">
                <input type="text" name="key" id="key" class="layui-input">
            </div>
            <div class="layui-form-mid layui-word-aux">为了您的账户安全,成功保存后无法再查看秘钥,请勿泄漏秘钥</div>
        </div>
        
        <div class="layui-form-item">
            <label class="layui-form-label">验证码</label>
            <div class="layui-input-inline">
                <input type="text" name="code" id="code" class="layui-input">
            </div>
            <div class="layui-form-mid layui-word-aux">请输入生成的验证码</div>
        </div>
        
        <pre class="layui-code" >
这东西叫法太多了,比如双重验证/动态密码/动态口令/动态令牌/身份验证器/双因子认证/2FA/TOTP验证码等等
原理是基于时间的动态验证码,网上客户端也大把,喜欢那个安装那个
开启后登录时需输入OTP验证码,作用是提高账号安全性
        </pre>
        
        <div class="layui-form-item">
            <div class="layui-input-block">
                <button class="layui-btn layui-btn-warm" type="button" id="close" >关闭</button>
                <button class="layui-btn layui-btn-normal" lay-submit lay-filter="save_totp" id="save_totp">保存</button>
            </div>
        </div>
    </form>
</ul>

<script src = "<?php echo $libs;?>/jquery/jquery-3.6.0.min.js"></script>
<script src = "<?php echo $libs;?>/jquery/jquery.md5.js"></script>
<script src = "<?php echo $libs; ?>/jquery/jquery.qrcode.min.js"></script>
<script src = "./templates/admin/js/public.js?v=<?php echo $Ver;?>"></script>
<?php load_static('js');?>
<script>
layui.use(['jquery','form','miniTab'], function () {
        var form = layui.form,
            layer = layui.layer,
            miniTab = layui.miniTab;
            miniTab.listen();
    //表单赋值
    form.val('form', <?php echo json_encode($LoginConfig);?>);
    
    //保存
    form.on('submit(save)', function (data) {
        $("*").blur(); //失去焦点,解决按回车无限提交
        data.field.Password=$.md5(data.field.Password);
        $.post(get_api('write_security_setting'),data.field,function(data,status){
            if(data.code == 1) {
                var index = layer.alert("保存成功!", function () {
                    layer.close(index);
                    //miniTab.deleteCurrentByIframe(); //关闭页面
                });
            }else{
                layer.msg(data.msg, {icon: 5});
            }
        });
        return false;
    }); 
    
    //双重验证
    form.on('submit(open_totp)', function (data) {
        $("*").blur(); //失去焦点,解决按回车无限提交
        data.field.Password=$.md5(data.field.Password);
        pwd_md5 = data.field.Password;
        $.post(get_api('read_totp'),data.field,function(data,status){
            if(data.code == 1){
                layer.confirm('已开启双重验证,是否要关闭?',{icon: 3, title:'温馨提示'}, function(index){
                    layer.closeAll();
                    $.post(get_api('write_totp','delete'),{'Password':pwd_md5},function(data,status){
                        if(data.code == 1) { 
                            layer.msg(data.msg, {icon: 1});
                        }else{
                            layer.msg(data.msg, {icon: 5});
                        }
                    });
                });
            }else if(data.code == 2) { 
                layer.confirm('未开启双重验证,是否要开启?',{icon: 3, title:'温馨提示'}, function(index){
                    layer.closeAll();
                    $('#key').val(data.key);
                    $('#code').val('');
                    $("#qr").html('');//防止多次操作出现多个二维码
                    let content = `otpauth://totp/${u}?secret=${data.key}&issuer=TwoNav`;
                    $('#qr').qrcode({render: "canvas",width: 200,height: 200,text: content});
                    var index = layer.open({type: 1,scrollbar: false,shadeClose: true,title: '双重验证',area : ['100%', '100%'],content: $('.ul_totp')});
                });
                return false;
            }else{
                layer.msg(data.msg, {icon: 5});
            }
        });
        return false;
    });
    $('#key').on('input', function() {
        $("#key").html('');
        let key = $('#key').val();
        let content = `otpauth://totp/${u}?secret=${key}&issuer=TwoNav`;
        $("#qr").html('');
        $('#qr').qrcode({render: "canvas",width: 200,height: 200,text: content});
    });
    //保存双重验证
    form.on('submit(save_totp)', function (data) {
        $("*").blur(); //失去焦点,解决按回车无限提交
        data.field.Password = pwd_md5;
        $.post(get_api('write_totp','set'),data.field,function(data,status){
            if(data.code == 1) { 
                layer.closeAll();
                layer.msg(data.msg, {icon: 1});
            }else{
                layer.msg(data.msg, {icon: 5});
            }
        });
        return false;
    });
    
    //关闭页面
    $(document).on('click', '#close', function() {
        layer.closeAll();
    });
    
    
});
</script>
</body>
</html>