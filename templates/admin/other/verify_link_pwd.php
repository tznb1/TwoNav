<?php if(!defined('DIR')){header('HTTP/1.1 404 Not Found');header("status: 404 Not Found");exit;}
$tip = $verify_type == 'link_pwd'?'请输入链接密码':'请输入分类密码';
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>查看加密链接 - TwoNav</title>
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta http-equiv="Access-Control-Allow-Origin" content="*">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="format-detection" content="telephone=no">
    <link rel="stylesheet" href="<?php echo $layui['css']; ?>">
    <link rel="stylesheet" href="<?php echo $libs?>/Other/login.css">
    <!--[if lt IE 9]>
    <script src="<?php echo $libs?>/Other/html5.min.js"></script>
    <script src="<?php echo $libs?>/Other/respond.min.js"></script>
    <![endif]-->
</head>
<body>
<div class="main-body">
    <div class="login-main">
        <div class="login-top">
            <span>TwoNav 查看加密链接</span>
            <span class="bg1"></span>
            <span class="bg2"></span>
        </div>
        <form class="layui-form login-bottom">
            <div class="center">
                <div class="item">
                    <span class="icon icon-3"></span>
                    <input type="password" name="Password" lay-verify="required" lay-reqtext="<?php echo $tip;?>" placeholder="<?php echo $tip;?>">
                    <span class="bind-password icon icon-4"></span>
                </div>
            </div>
            <div class="layui-form-item" style="text-align:center; width:100%;height:100%;margin:0px;">
                <button class="login-btn" lay-submit="" lay-filter="verify">验证</button>
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

        form.on('submit(verify)', function (data) {
            data = data.field;

            if (data.Password == '') {
                layer.msg('密码不能为空');
                return false;
            }
            data.id = '<?php echo $_GET['id']?>';
            $.post('./index.php?c=verify&type=link_pwd&u=<?php echo U?>',data,function(re,status){
                if(re.code == 1) {
                    layer.msg('正在验证..', {icon: 16,shade: [0.1, '#f5f5f5'],scrollbar: false,offset: 'auto',time: 888,
				        end: function() {
					        window.location.reload();
					        return false;
				        }
			        });
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