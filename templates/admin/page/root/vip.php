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
        <h4 style = "margin-bottom:1em;"><font color="red">不要使用盗版/破解版,盗版无法升级却存在诸多问题,所造成的损失与本程序无关</font></h4>
        <blockquote class="layui-elem-quote layui-text" style="color:red" >
            <li> 如何激活授权: </li>
            <li>1. 购买授权后将授权号(卡密)和邮箱填入下方并点击保存</li>
            <li>2. 返回概要页面 > 刷新 > 更新系统 ( 不更新还是免费版 )</li>
            <li>3. 更新成功后就是授权版的系统了,可使用全部功能</li>
            <li>4. 禁止传播/破解授权版源代码,违者封授权并追责</li>
        </blockquote>
        <blockquote class="layui-elem-quote layui-text" style="color:red" >
            <li> 温馨提示: </li>
            <li>授权是跟当前访问的域名或IP绑定的,通常建议绑定域名</li>
            <li>初次保存授权时会自动激活卡密并绑定当前域名或IP</li>
            <li>请不要在临时域名或临时IP中激活授权 (特殊情况请提前说明)</li>
            <li>授权针对顶级域名授权,如授权www.nav.cn时,dh.nav.cn可以正常使用</li>
            <li>绑定IP时部分功能会无法使用,如二级域名功能</li>
            <li>激活后修改域名或IP需扣除修改次数(年授权需付费修改)</li>
            <li>如有疑问请联系技术支持QQ: 271152681</li>
        </blockquote>
        <h3 style = "margin-bottom:1em;">当前域名：<font color="red"><?php echo $HTTP_HOST; ?></font></h3>
        
        <div class="layui-form-item">
            <label class="layui-form-label">授权卡密</label>
            <div class="layui-input-block">
                <input type="text" id = "order_id" name="order_id" value="<?php echo $subscribe['order_id']; ?>" required  autocomplete="off" placeholder="请输入授权号或卡密" class="layui-input">
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
        
        <div class="layui-form-item" style = "">
            <label class="layui-form-label">授权类型</label>
            <div class="layui-input-block">
                <input type="text" name="type_name" id ="type_name" value="<?php echo $subscribe['type_name'] ?? ''; ?>" autocomplete="off" placeholder="若未正确显示请点击保存设置" class="layui-input">
            </div>
        </div>
<?php if(get_db('global_config','v',["k" => "sys_switch"]) == 'show' && $subscribe['type'] == '3'){ ?>
        <div class="layui-form-item">
            <label class="layui-form-label">版本切换</label>
            <div class="layui-input-inline" >
                <select name="sys" id="sys">
                    <option value="biaozhun" selected>标准版</option>
                    <option value="gaoji" >高级版</option>
                </select>
            </div>
            <div class="layui-form-mid layui-word-aux">希望使用的系统版本 ( 下次更新时 )</div>
        </div>
<?php } ?>
        <div class="layui-btn-group">
            <button class="layui-btn layui-btn-normal" lay-submit lay-filter="save_key">保存</button>
            <button class="layui-btn layui-btn-danger" lay-submit lay-filter="buy_vip" data-url="<?php echo empty($data['pay_rul']) ?'':$data['pay_rul']?>" >购买授权</button>
            <button class="layui-btn layui-bg-purple" type="button" id="validate" style="<?php echo empty($subscribe['order_id']) ? 'display:none;':''; ?>">正版验证</button>
        </div>

        <fieldset class="layui-elem-field layui-field-title" style="margin-top:30px;"><legend>授权用户专享</legend></fieldset>
        <blockquote class="layui-elem-quote layui-text">
            <ul>
                <li>在线更新系统 ( 免费版只能手动更新 )</li>
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
    var vcode;
    $("#sys").val('<?php echo empty($subscribe['sys']) ? 'biaozhun':$subscribe['sys']; ?>');
    form.render('select');
    //保存订阅
    form.on('submit(save_key)', function(data){
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
        $.post(get_api('other_services','save_key'),{'order_id':data.field.order_id,'email':data.field.email,'sys':data.field.sys},function(data,status){
            layer.closeAll('loading');
            if(data.code == 200) {
                $("#order_id").val(data.data.order_id);
                $("#end_time").val(timestampToTime(data.data.end_time));
                $("#type_name").val(data.data.type_name);
                layer.msg(data.msg, {icon: 1,time: 10000});
            }else{
                layer.alert(data.msg,{icon:5,title:'保存结果',anim: 2,closeBtn: 0,btn: ['我知道了']});
            }
        }).fail(function () {
            layer.msg('请求失败', {icon: 5});
        });
    });
    
    //购买授权
    form.on('submit(buy_vip)', function(data){
        let url = $(this).attr('data-url');
        url = url.length > 0 ? url : 'https://gitee.com/tznb/TwoNav/wikis/pages?sort_id=7968669&doc_id=3767990';
        window.open($(this).attr('data-url'));
        return false;
    });
    
    //清空订阅信息
    form.on('submit(del_key)', function(data){
        vcode = randomnum(6);
        index = layer.prompt({formType: 0,value: '',title: '请输入验证码: ' + vcode,shadeClose: false,"success":function(){
            $("input.layui-layer-input").on('keydown',function(e){
                if(e.which == 13) {
                    del_key(data);
                }
            });
        }},function(){
            del_key(data)
        }); 
        return false;
    });
    
    function del_key(data){
        layer.close(index);
        if($("input.layui-layer-input").val() != vcode){
            layer.msg('验证码错误', {icon: 5});
            return false; 
        }
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
        $.post(get_api('other_services','del_key'),{'order_id':data.field.order_id,'email':data.field.email},function(data,status){
            layer.closeAll('loading');
            if(data.code == 200) {
                $("#order_id").val('');
                $("#email").val('');
                $("#end_time").val('1970-01-01 08:00:00');
                $("#type_name").val('');
                layer.msg(data.msg, {icon: 1,time: 10000});
            }else{
                layer.alert(data.msg,{icon:5,title:'保存结果',anim: 2,closeBtn: 0,btn: ['我知道了']});
            }
        }).fail(function () {
            layer.msg('请求失败', {icon: 5});
        });
    }
    
    // 正版验证
    $('#validate').on('click', function(){
        vcode = randomnum(6);
        index = layer.prompt({formType: 0,value: '',title: '请输入验证码: ' + vcode,shadeClose: false,"success":function(){
            $("input.layui-layer-input").on('keydown',function(e){
                if(e.which == 13) {
                    validate();
                }
            });
        }},function(){
            validate()
        });
    });
    
    function validate() {
        layer.close(index);
        if($("input.layui-layer-input").val() != vcode){
            layer.msg('验证码错误', {icon: 5});
            return false; 
        }
        $.post(get_api('other_services','validate'),function(data,status){
            layer.closeAll('loading');
            layer.alert(data.msg,{icon:(data.code == 200 ? 1 : 5),title:'验证结果',anim: 2,closeBtn: 0,btn: ['我知道了']});
        }).fail(function () {
            layer.msg('请求失败', {icon: 5});
        });
    }
});
</script>
</body>
</html>