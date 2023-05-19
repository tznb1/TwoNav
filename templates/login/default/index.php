<?php if(!defined('DIR')){header('HTTP/1.1 404 Not Found');header("status: 404 Not Found");exit;}?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>TwoNav - 登录</title>
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta http-equiv="Access-Control-Allow-Origin" content="*">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="format-detection" content="telephone=no">
    <link rel="stylesheet" href="<?php echo $libs?>/Layui/v2.8.3/css/layui.css">
    <link rel="stylesheet" href="<?php echo $libs?>/Other/login.css">
    <link rel="shortcut icon" href="./favicon.ico" type="image/x-icon">
    <!--[if lt IE 9]>
    <script src="<?php echo $libs?>/Other/html5.min.js"></script>
    <script src="<?php echo $libs?>/Other/respond.min.js"></script>
    <![endif]-->
</head>
<body>
<div class="main-body">
    <div class="login-main">
        <div class="login-top">
            <span>TwoNav 系统管理</span>
            <span class="bg1"></span>
            <span class="bg2"></span>
        </div>
        <form class="layui-form login-bottom">
            <div class="center">
                <div class="item">
                    <span class="icon icon-2"></span>
                    <input type="text" name="User" lay-verify="required"  placeholder="请输入账号">
                </div>

                <div class="item">
                    <span class="icon icon-3"></span>
                    <input type="password" name="Password" lay-verify="required"  placeholder="请输入密码">
                    <span class="bind-password icon icon-4"></span>
                </div>

            </div>
            <div class="tip">
<?php
    //若为默认值则显示注册入口
    if($global_config['Register'] == 'register' && $global_config['RegOption'] > 0){
        echo '<a href="./?c=register" class="forget">没有账号？立即注册</a>';
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
<script src = "<?php echo $libs?>/Layui/v2.8.3/layui.js"></script>
<script src = '<?php echo $libs?>/jquery/jquery.md5.js'></script>
<script>
    layui.use(['form','jquery'], function () {
        var $ = layui.jquery,
            form = layui.form,
            layer = layui.layer;

        $('.bind-password').on('click', function () {
            if ($(this).hasClass('icon-5')) {
                $(this).removeClass('icon-5');
                $("input[name='Password']").attr('type', 'password');
            } else {
                $(this).addClass('icon-5');
                $("input[name='Password']").attr('type', 'text');
            }
        });


        // 进行登录操作
        form.on('submit(login)', function (data) {
            data = data.field;
            if (data.User == '') {
                layer.msg('用户名不能为空');
                return false;
            }
            if (data.Password == '') {
                layer.msg('密码不能为空');
                return false;
            }
            data.Password = $.md5(data.Password);
            $.post('./index.php?c=<?php echo $c; ?>&u='+data.User,data,function(re,status){
                if(re.code == 1) {
                    window.location.href = re.url;
                }else{
                    layer.msg(re.msg, {icon: 5});
                }
            });
            return false;
        });
    });
</script>
</body>
</html>