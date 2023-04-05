<?php $title='您未登录'; require 'header.php'; ?>

<body>
<?php load_static('js.layui');?>
<script>
layui.use(['jquery','form'], function () {
    var layer = layui.layer
<?php if($global_config['Login'] === 'login'){ ?>
    layer.alert('您未登录,请先登录<br />', {
        time: 6*1000,success: function(layero, index){
            var timeNum = this.time/1000, setText = function(start){layer.title((start ? timeNum : --timeNum) + ' 秒后转入登录页面', index);};
            setText(!0);
            this.timer = setInterval(setText, 1000);
            if(timeNum <= 0) clearInterval(this.timer);
        },end: function(){
            top.location.href='./?c=login<?php echo $global_config['Default_User'] == U ? '':"&u=$u";?>';
        }
    });
<?php }else{?>
    layer.alert('您未登录,请先登录<br />站长隐藏了公用登录入口<br />请使用您的专属入口登录<br />如有疑问请联系站长', {
        time: 15*1000,success: function(layero, index){
            var timeNum = this.time/1000, setText = function(start){layer.title((start ? timeNum : --timeNum) + ' 秒后转入主页', index);};
            setText(!0);
            this.timer = setInterval(setText, 1000);
            if(timeNum <= 0) clearInterval(this.timer);
        },end: function(){
            top.location.href='./?c=index&u=<?php echo U;?>';
        }
    });
<?php }?>
});
</script>
</body>
</html>