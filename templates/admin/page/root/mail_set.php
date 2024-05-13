<?php 
if($USER_DB['UserGroup'] != 'root'){$content='您没有权限访问此页面'; require(DIR.'/templates/admin/page/404.php');exit;}
$title='系统设置';require(dirname(__DIR__).'/header.php');
?>
<body>
<div class="layuimini-container">
    <div class="layuimini-main">
    <form class="layui-form" lay-filter="form">
        <div class="layui-form layuimini-form layui-form-pane">
            <blockquote class="layui-elem-quote layui-text" style="">
                1.此功能<a href="https://gitee.com/tznb/TwoNav/wikis/pages?sort_id=7968669&doc_id=3767990" target="_blank">授权用户</a>专享
            </blockquote>
            
            <fieldset class="layui-elem-field layui-field-title" style="margin-top: 30px;"><legend>SMTP 配置</legend></fieldset>
            <div class="layui-form-item">
                <label class="layui-form-label">账号</label>
                <div class="layui-input-inline">
                    <input type="pass" name="user" lay-reqtext="账号不能为空" placeholder='请输入账号' autocomplete="off" class="layui-input">
                </div>
                <div class="layui-form-mid layui-word-aux">邮箱账号,例如: admin@qq.com</div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">密码</label>
                <div class="layui-input-inline">
                    <input type="password" name="pwd" lay-reqtext="密码不能为空" placeholder='请输入密码或授权码' autocomplete="off" class="layui-input">
                </div>
                <div class="layui-form-mid layui-word-aux">邮箱密码,也可能是独立密码或者授权码</div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">服务器</label>
                <div class="layui-input-inline">
                    <input type="text" name="host" lay-reqtext="服务器不能为空" placeholder='请输入发件服务器地址' autocomplete="off" class="layui-input">
                </div>
                <div class="layui-form-mid layui-word-aux">例如: smtp.qq.com</div>
            </div>
            
            <div class="layui-form-item">
                <label class="layui-form-label">端口</label>
                <div class="layui-input-inline">
                    <input type="number" name="port" lay-reqtext="端口不能为空" placeholder='请输入服务器端口' value="465" autocomplete="off" class="layui-input">
                </div>
                <div class="layui-form-mid layui-word-aux">通常是: 465或587</div>
            </div>
            
            <div class="layui-form-item">
                <label class="layui-form-label">协议</label>
                <div class="layui-input-inline" >
                    <select name="secure">
                        <option value="ssl" selected="">SSL</option>
                        <option value="TLS" >TLS</option>
                    </select>
                </div>
                <div class="layui-form-mid layui-word-aux">通常是: ssl</div>
            </div>
            
            <div class="layui-form-item">
                <label class="layui-form-label">发送人</label>
                <div class="layui-input-inline">
                    <input type="text" name="sender" lay-reqtext="发送人名称不能为空" placeholder='' autocomplete="off" class="layui-input">
                </div>
                <div class="layui-form-mid layui-word-aux">例如: TwoNav</div>
            </div>
            
            <div class="layui-form-item">
                <label class="layui-form-label">收件人</label>
                <div class="layui-input-inline">
                    <input type="text" name="addressee" placeholder='仅用于发件测试' autocomplete="off" class="layui-input">
                </div>
                <div class="layui-form-mid layui-word-aux">例如:user@qq.com</div>
            </div>
            
            <fieldset class="layui-elem-field layui-field-title" style="margin-top: 30px;"><legend>注册参数</legend></fieldset>
            <div class="layui-form-item">
                <label class="layui-form-label">验证邮箱</label>
                <div class="layui-input-inline" >
                    <select name="verify_email">
                        <option value="0" selected="">关闭</option>
                        <option value="1" >开启</option>
                    </select>
                </div>
                <div class="layui-form-mid layui-word-aux">开启时用户注册需通过邮箱接收验证码</div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">发送间隔</label>
                <div class="layui-input-inline">
                    <input type="number" name="send_interval" lay-reqtext="发送间隔不能为空" placeholder='IP发送间隔,单位秒!' value="60" autocomplete="off" class="layui-input">
                </div>
                <div class="layui-form-mid layui-word-aux">为了避免被恶意发送,建议不低于30秒</div>
            </div>
            
            <div class="layui-form-item layui-form-text">
                <label class="layui-form-label">验证码模板</label>
                <div class="layui-input-block">
                    <textarea name="verify_template" class="layui-textarea" placeholder='您的验证码: $code'></textarea>
                </div>
            </div>
            
            <div class="layui-form-item">
                <div class="layui-input-block">
                    <button class="layui-btn layui-btn-normal" lay-submit lay-filter="send_test">测试</button>
                    <button class="layui-btn layui-btn-normal" lay-submit lay-filter="save">确认保存</button>
                </div>
            </div>
        </div>
    </form>
    </div>
</div>
<?php load_static('js.layui');?>
<script src="./templates/admin/js/public.js?v=<?php echo $Ver;?>"></script>
<script>
layui.use(['jquery','form'], function () {
    var form = layui.form;
    var layer = layui.layer;
    var $ = layui.jquery;

    //监听提交
    form.on('submit(save)', function (data) {
        layer.msg('当前版本不支持此功能,如需此功能请购买高级版授权', {icon: 5,time: 1000*300});
        return false;
    }); 
    //测试
    form.on('submit(send_test)', function (data) {
        layer.msg('当前版本不支持此功能,如需此功能请购买高级版授权', {icon: 5,time: 1000*300});
        return false;
    }); 

    
});
</script>
</body>
</html>