<?php if(!defined('DIR')){header('HTTP/1.1 404 Not Found');header("status: 404 Not Found");exit;}?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>TwoNav - 注册</title>
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta http-equiv="Access-Control-Allow-Origin" content="*">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="format-detection" content="telephone=no">
    <link rel="stylesheet" href="<?php echo $libs?>/Layui/v2.6.8/css/layui.css">
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
            <span>TwoNav 注册账号</span>
            <span class="bg1"></span>
            <span class="bg2"></span>
        </div>
        <form class="layui-form login-bottom">
            <div class="center">
                <div class="item">
                    <span class="icon layui-icon layui-icon-username"></span>
                    <input type="text" name="User" lay-verify="required"  placeholder="请输入账号">
                </div>

                <div class="item">
                    <span class="icon layui-icon layui-icon-release"></span>
                    <input type="text" name="Email" lay-verify="required|email"  placeholder="请输入邮箱">
                </div>

                <div class="item">
                    <span class="icon layui-icon layui-icon-password"></span>
                    <input type="password" name="Password" lay-verify="required"  placeholder="请输入密码">
                    <span class="bind-password icon icon-4"></span>
                </div>
                
                <div class="item" <?php echo $global_config['RegOption'] == 2 ?'':'style = "display:none;"'?>>
                    <span class="icon layui-icon layui-icon-fonts-code"></span>
                    <input type="text" name="regcode"  placeholder="请输入注册码" value="<?php echo $_GET['key'];?>">
                </div>
                
            </div>
            <div class="tip">
<?php
    //若为默认值则显示登录入口
    if($global_config['Login'] == 'login'){
        if($global_config['RegOption'] == 2 && !empty($reg_tips)){ 
            echo '                <a href="javascript:;" onclick = "Get_Invitation(\''.base64_encode($reg_tips).'\')">获取注册码</a>'."\n";
        }
        echo '                <a href="./?c=login" class="forget">已有账号？立即登录</a>'."\n";
    } 
?>
            </div>
            <div class="layui-form-item" style="text-align:center; width:100%;height:100%;margin:0px;">
                <button class="login-btn" lay-submit="" lay-filter="login">注册</button>
            </div>
        </form>
    </div>
</div>
<div class="footer">
     <?php echo $copyright.( !empty($ICP)?'<span class="padding-5">|</span>':'').$ICP; ?>
</div>
<script src = "<?php echo $libs?>/jquery/jquery-3.6.0.min.js"></script>
<script src = "<?php echo $libs?>/Layui/v2.6.8/layui.js"></script>
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


        // 进行注册操作
        form.on('submit(login)', function (data) {
            $("*").blur();
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
                    layer.alert("您已成功注册,请记牢您的账号密码!",{icon:1,title:'注册成功',anim: 2,closeBtn: 0,btn: ['后台管理']},function () {window.location.href = './index.php?c=admin&u='+ data.User;});
                }else{
                    layer.msg(re.msg, {icon: 5});
                }
            });
            return false;
        });
    });
    
    //获取邀请码
function Get_Invitation($base64) {
    var content =decodeURIComponent(escape(window.atob($base64)));
    if (content.substr(0,4) =='http'){
        window.open(content);
        //window.location.href = content;
    }else{
        layer.open({title:'获取注册码',content:content});
    }
    return false;
}
</script>
</body>
</html>