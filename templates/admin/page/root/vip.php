<?php 
if($USER_DB['UserGroup'] != 'root'){$content='您没有权限访问此页面'; require(DIR.'/templates/admin/page/404.php');exit;}
$title='授权管理';require(dirname(__DIR__).'/header.php'); 
$subscribe = unserialize(get_db('global_config','v',["k" => "s_subscribe"])); ?>
<body>
<div class="layuimini-container">
  <div class="layuimini-main">
    <div class="layui-form layuimini-form layui-form-pane">
        <blockquote class="layui-elem-quote layui-text">
            <li>1. 查询授权时当前域名必须和订阅填写一致</li>
            <li>2. 其他二级域名使用时请手动输入订单号/邮箱保存</li>
            <li>3. 授权未绑定邮箱时邮箱留空,已绑定时请输入正确邮箱</li>
            <li>4. 如有其他疑问联系技术支持</li>
        </blockquote>
        
        <h3 style = "margin-bottom:1em;">当前域名：<font color="red"><?php echo $_SERVER['HTTP_HOST']; ?></font> (订阅时填写)</h3>
        
        <div class="layui-form-item">
            <label class="layui-form-label">订单号</label>
            <div class="layui-input-block">
                <input type="text" id = "order_id" name="order_id" value="<?php echo $subscribe['order_id']; ?>" required  autocomplete="off" placeholder="请输入订单号" class="layui-input">
            </div>
        </div>

        <div class="layui-form-item">
            <label class="layui-form-label">订阅邮箱</label>
            <div class="layui-input-block">
                <input type="text" name="email" id ="email" value="<?php echo $subscribe['email']; ?>" required autocomplete="off" placeholder="订阅邮箱" class="layui-input">
            </div>
        </div>

        <div class="layui-form-item" style = "display:none;">
            <label class="layui-form-label">域名</label>
            <div class="layui-input-block">
                <input type="text" name="domain" id ="domain" value="<?php echo $_SERVER['HTTP_HOST']; ?>" autocomplete="off" placeholder="网站域名" class="layui-input">
            </div>
        </div>

        <div class="layui-form-item">
            <label class="layui-form-label">到期时间</label>
            <div class="layui-input-block">
            <input type="text" name="end_time" id = "end_time" readonly="readonly" value = "<?php echo date("Y-m-d H:i:s",$subscribe['end_time']); ?>" autocomplete="off" placeholder="订阅到期时间" class="layui-input">
            </div>
        </div>

        <div class="layui-btn-group">
            <button class="layui-btn layui-btn-normal" lay-submit lay-filter="set_subscribe">保存设置</button>
            <button class="layui-btn layui-btn-warm" lay-submit lay-filter="reset_subscribe">删除</button>
            <button class="layui-btn layui-btn-danger" id="help" sort_id="7968669">购买授权</button>
            <button class="layui-btn" lay-submit lay-filter="get_subscribe">查询授权</button>
        </div>

        
        <fieldset class="layui-elem-field layui-field-title" style="margin-top:30px;"><legend>授权用户专享</legend></fieldset>
        <blockquote class="layui-elem-quote layui-text">
          <li>1. 可使用一键更新功能</li>
          <li>2. 可使用二级域名绑定账号功能</li>
          <li>3. 可使用链接检测功能</li>
          <li>4. 可自定义版权/用户组/默认配置等</li>
          <li>5. 可使用邀请码注册功能</li>
          <li>6. 可使用本地备份功能</li>
          <li>7. 可无限次数下载主题和系统更新</li>
          <li>8. 解锁全部功能和服务</li>
          <li>9. 更多专属功能开发中</li>
          <li>10. 可帮助TwoNav持续发展</li>
          <li>#. 技术支持:QQ 271152681  </li>
        </blockquote>
    </div>
  </div>
</div>
<script src = "<?php echo $libs;?>/jquery/jquery-3.6.0.min.js"></script>
<script src = "./templates/admin/js/public.js?v=<?php echo $Ver;?>"></script>
<?php load_static('js.layui');?>
<script>
layui.use(['jquery','form'], function () {
    var form = layui.form;
    var layer = layui.layer;
    var $ = layui.jquery;

    //查询订阅
    form.on('submit(get_subscribe)', function(data){
        layer.load(2, {shade: [0.1,'#fff']});
        $.get('https://api.lm21.top/api.php?fn=get_subscribe',data.field,function(data,status){
            layer.closeAll('loading');
            if(data.code == 200) {
                $("#order_id").val(data.data.order_id);
                $("#end_time").val(timestampToTime(data.data.end_time));
                layer.msg(data.msg, {icon: 1,time: 10000});
            }else{
                layer.msg(data.msg, {icon: 5,time: 10000});
            }
        });
        return false; 
    });
    
    //保存订阅
    form.on('submit(set_subscribe)', function(data){
        var order_id = data.field.order_id;
        if(order_id.length < 20){
            layer.msg('订单号错误,请核对', {icon: 5});
            return false;
        }
        if(data.field.email.length == 0){
            layer.msg('邮箱不能为空,请核对', {icon: 5});
            return false;
        }
        layer.load(2, {shade: [0.1,'#fff']});
        $.get('https://api.lm21.top/api.php?fn=check_subscribe',data.field,function(data,status){
            layer.closeAll('loading');
            if(data.code == 200) {
                $("#end_time").val(timestampToTime(data.data.end_time));
                set_subscribe(order_id,data.data.email,data.data.end_time,data.data.domain);
            }else{
                layer.msg(data.msg, {icon: 5});
            }
        });
        console.log(data.field) 
        return false;
    });
    
    //清空订阅信息
    form.on('submit(reset_subscribe)', function(data){
        var order_id = data.field.order_id;
        layer.load(2, {shade: [0.1,'#fff']});
        $("#order_id").val('');
        $("#email").val('');
        $("#end_time").val('1970-01-01 08:00:00');
        set_subscribe('','','0','');
        layer.closeAll('loading');
        return false;
    });
  
    //存储到数据库中
    function set_subscribe(order_id,email,end_time,domain) {
        $.post(get_api('write_subscribe'),{order_id:order_id,email:email,end_time:end_time,domain:domain},function(data,status){
            if(data.code == 1) {
                layer.msg(data.msg, {icon: 1});
            }else{
                layer.msg(data.msg, {icon: 5});
            }
        });
    }
    
});
</script>
</body>
</html>