<?php if(!defined('DIR')){header('HTTP/1.1 404 Not Found');header("status: 404 Not Found");exit;}?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title><?php echo $data['title'];?> - <?php echo $site['subtitle']?></title>
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta http-equiv="Access-Control-Allow-Origin" content="*">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="format-detection" content="telephone=no">
    <link rel="stylesheet" href="<?php echo $layui['css']; ?>">
    <link rel="stylesheet" href="<?php echo $libs?>/Other/login.css">
    <link rel="shortcut icon" href="<?php echo $favicon;?>">
</head>
<body>
<div class="main-body">
    <div class="login-main">
        <div class="login-top">
            <span><?php echo $data['tip'];?></span>
            <span class="bg1"></span>
            <span class="bg2"></span>
        </div>
        <form class="layui-form login-bottom">
            <div class="center">
                <div class="item">
                    <span class="icon icon-3"></span>
                    <input type="password" name="Password" id="Password" lay-verify="required" placeholder="<?php echo $data['input_tip'];?>" value="<?php echo $_GET['pwd'];?>">
                    <span class="bind-password icon icon-4"></span>
                </div>
            </div>
            <div class="tip">
            <?php if(!empty($data['get_tip'])){ ?>
                <a href="javascript:;" onclick="showInfo('<?php echo base64_encode($data['get_tip'])?>')">如何获取？</a>
            <?php }?>
            </div>
            <div class="layui-form-item" style="text-align:center; width:100%;height:100%;margin:0px;">
                <button class="login-btn"id="verify">验证</button>
            </div>
        </form>
    </div>
</div>
<div class="footer"><?php echo $copyright;?></div>
<script src="<?php echo $libs?>/jquery/jquery-3.6.0.min.js"></script>
<script type="text/javascript" src="<?php echo $libs?>/Layer/v3.3.0/layer.js"></script>
<script>
    $('#verify').on('click', function () {
        Password = $("#Password").val();
        if( Password == ''){
            layer.msg("<?php echo $data['input_tip'];?>", {icon: 5});
            $('#Password').focus();
            return false;
        }
        $.post('<?php echo $data['post_url']; ?>',{'Password':Password},function(re,status){
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
    // 显示密码
    $('.bind-password').on('click', function () {
        if ($(this).hasClass('icon-5')) {
            $(this).removeClass('icon-5');
            $("input[name='Password']").attr('type', 'password');
        } else {
            $(this).addClass('icon-5');
            $("input[name='Password']").attr('type', 'text');
        }
    });
    function showInfo($base64) {
        var content =decodeURIComponent(escape(window.atob($base64)));
        if(content.startsWith("http")){
            window.open(content);
            return false;
        }
        layer.open({type: 1,title: '如何获取',btn: ['知道了'],
            content: '<div style="padding: 20px; line-height: 22px; font-weight: 300;"><?php echo $data['get_tip'];?></div>'
        });
    }
</script>
</body>
</html>