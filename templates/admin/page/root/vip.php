<?php 
if($USER_DB['UserGroup'] != 'root'){$content='您没有权限访问此页面'; require(DIR.'/templates/admin/page/404.php');exit;}
$title='授权管理';require(dirname(__DIR__).'/header.php'); 
$subscribe = unserialize(get_db('global_config','v',["k" => "s_subscribe"])); 
$HTTP_HOST = preg_replace('/:\d+$/','',$_SERVER['HTTP_HOST']); //去除端口号
$Notice = get_db('global_config','v',['k'=>'notice']);
if(!empty($Notice)){
    $data = json_decode($Notice, true);
}
?>
<body>
<div class="layuimini-container">
  <div class="layuimini-main">
    <div class="layui-form layuimini-form layui-form-pane">
        <h3 style = "margin-bottom:1em;">当前域名：<font color="red"><?php echo $HTTP_HOST; ?></font></h3>
        
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
                <input type="text" name="domain" id ="domain" value="<?php echo $HTTP_HOST; ?>" autocomplete="off" placeholder="网站域名" class="layui-input">
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
            <button class="layui-btn layui-btn-danger" lay-submit lay-filter="buy_vip" data-url="<?php echo empty($data['pay_rul']) ?'':$data['pay_rul']?>" >购买授权</button>
            <button class="layui-btn" lay-submit lay-filter="get_subscribe">查询授权</button>
        </div>

        <fieldset class="layui-elem-field layui-field-title" style="margin-top:30px;"><legend>授权用户专享</legend></fieldset>
        <blockquote class="layui-elem-quote layui-text">
            <ul>
                <li>在线更新系统 ( 免费只能手动更新 )</li>
                <li>在线下载和更新主题模板</li>
                <li>批量更新链接标题/关键字/描述/图标</li>
                <li>批量识别链接是否可以访问</li>
                <li>可使用本地备份功能支持回滚等操作</li>
                <li>扩展功能:收录管理/留言管理/文章管理/链接扩展字段</li>
                <li>可配置邮件服务用于注册时发送验证</li>
                <li>可配置初始设置 (新用户注册后的默认配置) </li>
                <li>可配置本地获取图标服务并支持缓存防盗链等配置</li>
                <li>可开启全站私有模式降低因用户添加违规链接导致封站的风险</li>
                <li>可自定义用户组权限,对不可信的用户禁止使用高危功能(如自定义代码)</li>
                <li>可自定义主页版权信息,可使用二级域名直接访问用户主页</li>
                <li>可自定义全局header代码和footer代码</li>
                <li>可限制用户添加链接标题描述等长度</li>
                <li>可设置保留账号,支持正字表达式 (保留账号列表不可以被用户注册)</li>
                <li>可设置生成注册码/配置注册提示等 (如需关注公众号或付费购买注册码才可以注册)</li>
                <li>支持生成sitemap.xml网站地图用于优化SEO,提高收录效果</li>
                <li>支持百度推送API(链接列表和文章列表),提高收录效果</li>
                <li>还有其他细节就不逐一举例了,TwoNav的发展离不开大家的支持</li>
                <li>未来还会增加更多专属功能, 技术支持:QQ 271152681  </li>
            </ul>
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
    //购买授权
    form.on('submit(buy_vip)', function(data){
        let url = $(this).attr('data-url');
        url = url.length > 0 ? url : 'https://gitee.com/tznb/TwoNav/wikis/pages?sort_id=7968669&doc_id=3767990';
        window.open($(this).attr('data-url'));
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