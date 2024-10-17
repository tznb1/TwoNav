<?php if(!defined('DIR')){header('HTTP/1.1 404 Not Found');header("status: 404 Not Found");exit;}?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title><?php echo $OEM['program_name'];?> - 登录</title>
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta http-equiv="Access-Control-Allow-Origin" content="*">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="format-detection" content="telephone=no">
    <link rel="stylesheet" href="<?php echo $layui['css']; ?>">
    <link rel="stylesheet" href="<?php echo $libs?>/Other/login.css?v=<?php echo SysVer; ?>">
    <link rel="shortcut icon" href="./favicon.ico" type="image/x-icon">
</head>
<body>
<div class="main-body">
    <div class="login-main">
        <div class="login-top">
            <span><?php echo $OEM['program_name'];?> 系统管理</span>
            <span class="bg1"></span>
            <span class="bg2"></span>
        </div>
        <form class="layui-form login-bottom">
            <div class="center">
                <div class="item">
                    <span class="icon layui-icon layui-icon-username"></span>
                    <input type="text" name="username" lay-verify="required"  placeholder="请输入账号">
                </div>

                <div class="item">
                    <span class="icon layui-icon layui-icon-password"></span>
                    <input type="password" name="password" lay-verify="required"  placeholder="请输入密码">
                    <span class="bind-password icon icon-4"></span>
                </div>
            </div>
            <div class="tip">
<?php
    //若为默认值则显示注册入口
    if($global_config['Register'] == 'register' && $global_config['RegOption'] > 0){
        echo '<a href="'.$urls['register'].'" class="forget">没有账号？立即注册</a>';
    } 
?>
            </div>
            <div class="layui-form-item" style="text-align:center; width:100%;height:100%;margin:0px;">
                <button class="login-btn" lay-submit="" lay-filter="login">登录</button>
            </div>
        </form>
    </div>
</div>
<div class="footer">
     <?php echo $copyright.( !empty($ICP)?'<span class="padding-5">|</span>':'').$ICP; ?>
</div>
<script src = "<?php echo $libs?>/jquery/jquery-3.6.0.min.js"></script>
<script src = "<?php echo $layui['js']; ?>"></script>
<script src = '<?php echo $libs?>/jquery/jquery.md5.js'></script>
<script>
    if (/Trident/.test(navigator.userAgent) || /MSIE/.test(navigator.userAgent)) {
        alert("当前浏览器版本过低，请使用Chrome浏览器或火狐浏览器等现代浏览器");
    }
</script>
<script>
    layui.use(['form','jquery'], function () {
        var $ = layui.jquery,
            form = layui.form,
            layer = layui.layer;

        $('.bind-password').on('click', function () {
            if ($(this).hasClass('icon-5')) {
                $(this).removeClass('icon-5');
                $("input[name='password']").attr('type', 'password');
            } else {
                $(this).addClass('icon-5');
                $("input[name='password']").attr('type', 'text');
            }
        });


        //账号登录
        form.on('submit(login)', function($form) {
            let url = `./?c=auth&mode=uname&t=` + Math.round(new Date() / 1000);
            form_data = $form.field;form_data.keep = 'on';
            form_data.password = $.md5(form_data.password);
            let load = layer.msg('正在登录..', {icon: 16,shade: [0.1, '#f5f5f5'],scrollbar: false,offset: 'auto',time: 60*1000});
            $.post(url,form_data,function(data,status){
                layer.close(load);
                if(data.code == 1) {
                    layer.msg('登录成功', {icon: 1,shade: [0.1, '#f5f5f5'],scrollbar: false,offset: 'auto',time: 888,
                        end: function() {
                            window.location.href = data.url;
                        }
                    });
                }else if(data.code == 2){
                    //双重认证
                    layer.open({
                        type: 1,
                        title: false,
                        content: $('.OTP'),
                        move: '.move',
                        success: function(layero, index, that){
                            //监听回车事件
                            $('input[name="otp_code"]').keydown(function(event) {
                                if (event.which === 13) {
                                    $('button[lay-filter="validate_otp"]').click();
                                }
                            });
                            //监听点击事件
                            form.on('submit(validate_otp)', function ($form2) {
                                form_data.otp_code = $form2.field.otp_code
                                let load = layer.msg('正在验证..', {icon: 16,shade: [0.1, '#f5f5f5'],scrollbar: false,offset: 'auto',time: 60*1000});
                                $.post(url,form_data,function(data,status){
                                    layer.close(load);
                                    if(data.code == 1) {
                                        layer.msg('登录成功', {icon: 1,shade: [0.1, '#f5f5f5'],scrollbar: false,offset: 'auto',time: 888,
                                            end: function() {
                                                window.location.href = data.url;
                                            }
                                        });
                                    }else{
                                        layer.msg(data.msg, {icon: 5});
                                    }
                                });
                                return false; 
                            }); 
                        }
                    });
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
<ul class="OTP" style="display:none;">
    <div class="layui-form layuimini-form layui-form-pane" style="padding: 20px 30px;">
        <div class="move" style="height: 30px;margin-bottom: 15px;text-align: center;font-size: 21px;">动态口令认证</div>
        <div class="layui-form-item">
          <div class="layui-input-group" style="width: 100%;">
            <input type="text" name="otp_code" lay-verify="required" lay-reqtext="请输入动态口令" placeholder="请输入动态口令" style="text-align: center;" class="layui-input" lay-affix="clear">
          </div>
        </div>
        <div class="layui-input-block" style="margin-left: 1px;"><button type="button" class="layui-btn layui-btn-fluid" lay-submit lay-filter="validate_otp">验证并登录</button></div>
        <div style="margin-top: 16px;font-size: 13px;color: #777;">* 如果您无法认证,请联系站长处理</div>
    </div>
</ul>