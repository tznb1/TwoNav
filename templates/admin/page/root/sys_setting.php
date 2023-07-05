<?php 
if($USER_DB['UserGroup'] != 'root'){$content='您没有权限访问此页面'; require(DIR.'/templates/admin/page/404.php');exit;}
$title='系统设置';require(dirname(__DIR__).'/header.php');
?>
<style>
.layui-btn-container .layui-btn{border-width: 1px; border-style: solid; border-color: #FF5722!important; color: #FF5722!important;background: none;height: 30px; line-height: 30px; padding: 0 10px; font-size: 12px;}
</style>
<body>
<div class="layuimini-container">
    <div class="layuimini-main">
        <div class="layui-btn-container">
            <button type="button" class="layui-btn" layuimini-content-href="root/default_setting" data-title="默认设置">默认设置</button>
            <button type="button" class="layui-btn" layuimini-content-href="root/mail_set" data-title="邮件配置">邮件配置</button>
            <button type="button" class="layui-btn" layuimini-content-href="root/icon_set" data-title="图标配置">图标配置</button>
        </div>
    <form class="layui-form" lay-filter="form">
        <div class="layui-form layuimini-form layui-form-pane">
            <blockquote class="layui-elem-quote layui-text" style="">1.带*号的选项属<a href="https://gitee.com/tznb/OneNav/wikis/%E8%AE%A2%E9%98%85%E6%9C%8D%E5%8A%A1%E6%8C%87%E5%BC%95" target="_blank">授权用户</a>专享<br />2.原OneNav Extend的部分配置已下放到用户组配置中<br />3.如果您不理解选项的作用请勿乱改   </blockquote>
            
            <div class="layui-form-item">
                <label class="layui-form-label">默认用户</label>
                <div class="layui-input-inline">
                    <input type="text" name="Default_User" lay-verify="required" lay-reqtext="默认用户不能为空" placeholder='admin'  autocomplete="off" class="layui-input">
                </div>
                <div class="layui-form-mid layui-word-aux">默认主页的账号,优先级:Get>Cookie/Host>默认用户>admin</div>
            </div>

            <div class="layui-form-item">
                <label class="layui-form-label">默认页面</label>
                <div class="layui-input-inline" >
                    <select name="default_page">
                        <option value="0" selected="">默认用户主页</option>
                        <option value="1" >登录用户主页</option>
                        <option value="2" >引导页面</option>
                    </select>
                </div>
                <div class="layui-form-mid layui-word-aux">直接访问域名不带任何参数时显示的页面</div>
            </div>

            <div class="layui-form-item">
                <label class="layui-form-label required">默认分组</label>
                <div class="layui-input-inline">
                    <input type="text" name="default_UserGroup"  lay-reqtext="默认用户不能为空" placeholder='default'  autocomplete="off" class="layui-input">
                </div>
                <div class="layui-form-mid layui-word-aux">用户注册成功后所在分组代号,留空则使用默认分组</div>
            </div>
            
            <div class="layui-form-item">
                <label class="layui-form-label">注册配置</label>
                <div class="layui-input-inline" >
                    <select name="RegOption">
                        <option value="0" >禁止注册</option>
                        <option value="1" selected="">开放注册</option>
                        <option value="2" >需注册码</option>
                    </select>
                </div>
                <div class="layui-form-mid layui-word-aux">个人使用时建议禁止注册</div>
            </div>

            <div class="layui-form-item">
                <label class="layui-form-label">注册入口</label>
                <div class="layui-input-inline">
                    <input type="text" name="Register" lay-verify="required" lay-reqtext="注册入口不能为空" placeholder='register'  autocomplete="off" class="layui-input">
                </div>
                <div class="layui-form-mid layui-word-aux">不想被随意注册时可以修改</div>
            </div>
            
            <div class="layui-form-item">
                <label class="layui-form-label">登录入口</label>
                <div class="layui-input-inline">
                    <input type="text" name="Login" lay-verify="required" lay-reqtext="登录入口不能为空" placeholder='login'  autocomplete="off" class="layui-input">
                </div>
                <div class="layui-form-mid layui-word-aux">修改可以防止被爆破,修改请记好入口名,否则无法登录后台</div>
            </div>
            
            <div class="layui-form-item">
                <label class="layui-form-label">静态路径</label>
                <div class="layui-input-inline">
                    <input type="text" name="Libs" lay-verify="required" lay-reqtext="静态路径不能为空,填错会导致无法正常加载网页!默认./static" placeholder='./static'  autocomplete="off" class="layui-input">
                </div>
                <div class="layui-form-mid layui-word-aux">默认为./static 即本地服务器!可以使用CDN来提高加载速度</div>
            </div>
            
            <div class="layui-form-item">
                <label class="layui-form-label">ICP备案</label>
                <div class="layui-input-inline">
                    <input type="text" name="ICP" autocomplete="off" class="layui-input">
                </div>
                <div class="layui-form-mid layui-word-aux">主页底部显示的备案信息</div>
            </div>
            
            <div class="layui-form-item">
                <label class="layui-form-label">防XSS脚本</label>
                <div class="layui-input-inline" >
                    <select name="XSS_WAF">
                        <option value="0" >关闭</option>
                        <option value="1" selected="">开启</option>
                    </select>
                </div>
                <div class="layui-form-mid layui-word-aux">拦截POST表单中的XSS恶意代码,提升网站安全性</div>
            </div>
            
            <div class="layui-form-item">
                <label class="layui-form-label">防SQL注入</label>
                <div class="layui-input-inline" >
                    <select name="SQL_WAF">
                        <option value="0" >关闭</option>
                        <option value="1" selected="">开启</option>
                    </select>
                </div>
                <div class="layui-form-mid layui-word-aux">拦截POST表单中的SQL注入代码,提升网站安全性</div>
            </div>
            
            <div class="layui-form-item">
                <label class="layui-form-label">离线模式</label>
                <div class="layui-input-inline" >
                    <select name="offline">
                        <option value="0" selected="">关闭</option>
                        <option value="1" >开启</option>
                    </select>
                </div>
                <div class="layui-form-mid layui-word-aux">开启将禁止服务器访问互联网,部分功能将被禁用(如:更新提示,公告,在线主题,链接识别,书签克隆等)</div>
            </div>
            
            <div class="layui-form-item">
                <label class="layui-form-label">调试模式</label>
                <div class="layui-input-inline">
                    <select name="Debug">
                        <option value="0" selected="">关闭</option>
                        <option value="1" >开启</option>
                    </select>
                </div>
                <div class="layui-form-mid layui-word-aux">开发者调试模式,请不要随意开启</div>
            </div>

            <div class="layui-form-item">
                <label class="layui-form-label">维护模式</label>
                <div class="layui-input-inline">
                    <select name="Maintenance">
                        <option value="0" selected="">关闭</option>
                        <option value="1" >开启</option>
                    </select>
                </div>
                <div class="layui-form-mid layui-word-aux">开启时将关闭主页/登录/注册等服务,站长账号不受影响(网站升级迁移时适用)</div>
            </div>

            <div class="layui-form-item">
                <label class="layui-form-label required">强制私有</label>
                <div class="layui-input-inline">
                    <select name="Privacy">
                        <option value="0" selected="">依用户组配置</option>
                        <option value="1" >全站用户</option>
                    </select>
                </div>
                <div class="layui-form-mid layui-word-aux">开启后用户必须登录才可以进入主页(过渡页不限制)</div>
            </div>
            
            <div class="layui-form-item">
                <label class="layui-form-label required">二级域名</label>
                <div class="layui-input-inline">
                    <select name="Sub_domain">
                        <option value="0" selected="">关闭</option>
                        <option value="1" >开启</option>
                    </select>
                </div>
                <div class="layui-form-mid layui-word-aux" title="不支持IP和双后缀域名,如: com.cn / net.cn / org.cn">以二级域名的形式直接进入用户主页,需配置域名泛解析和服务器泛域名绑定</div>
            </div>

            <div class="layui-form-item">
                <label class="layui-form-label required">版权信息</label>
                <div class="layui-input-inline">
                    <input type="text" name="copyright" placeholder='Copyright © TwoNav'  autocomplete="off" class="layui-input">
                </div>
                <div class="layui-form-mid layui-word-aux">主页底部显示的版权信息,开发不易,免费用户请保留版权,谢谢</div>
            </div>
            
            <div class="layui-form-item layui-form-text">
                <label class="layui-form-label required">头部(header)代码 - 全局</label>
                <div class="layui-input-block">
                    <textarea name="global_header" class="layui-textarea" placeholder=''></textarea>
                </div>
            </div>
            
            <div class="layui-form-item layui-form-text">
                <label class="layui-form-label required">底部(footer)代码 - 全局</label>
                <div class="layui-input-block">
                    <textarea name="global_footer" class="layui-textarea" placeholder='例如备案号,统计代码等,支持HTML,JS,CSS'></textarea>
                </div>
            </div>

            <div class="layui-form-item layui-hide" id="api_extend">
                <label class="layui-form-label">api_extend</label>
                <div class="layui-input-inline">
                    <select name="api_extend">
                        <option value="0" selected="">关闭</option>
                        <option value="1" >开启</option>
                    </select>
                </div>
                <div class="layui-form-mid layui-word-aux">请勿开启!请勿开启!请勿开启!</div>
            </div>

            <div class="layui-form-item layui-hide">
                <label class="layui-form-label">资源接口</label>
                <div class="layui-input-inline">
                    <select name="Update_Source">
                        <option value="0" selected="">自动</option>
                        <option value="lm21">主线路</option>
                        <option value="gitee">备用线路(gitee)</option>
                    </select>
                </div>
                <div class="layui-form-mid layui-word-aux">备用资源不定期更新,非必要请勿使用!</div>
            </div>
            
            <div class="layui-form-item layui-hide">
                <label class="layui-form-label">资源超时</label>
                <div class="layui-input-inline">
                    <input type="number" name="Update_Overtime" autocomplete="off" value="3" class="layui-input">
                </div>
                <div class="layui-form-mid layui-word-aux">默认3秒,范围3-60</div>
            </div>
            
            <fieldset class="layui-elem-field layui-field-title" style="margin-top: 30px;"><legend>扩展功能</legend></fieldset>
             <blockquote class="layui-elem-quote layui-text" style="">注:开关后请刷新整个页面</blockquote>
            <div class="layui-form-item">
                <label class="layui-form-label required">收录管理</label>
                <div class="layui-input-inline">
                    <select name="apply">
                        <option value="0" selected="">关闭</option>
                        <option value="1" >开启</option>
                    </select>
                </div>
                <div class="layui-form-mid layui-word-aux">此处为全局开关,用户默认为关闭,需自行开启!</div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label required">留言管理</label>
                <div class="layui-input-inline">
                    <select name="guestbook">
                        <option value="0" selected="">关闭</option>
                        <option value="1" >开启</option>
                    </select>
                </div>
                <div class="layui-form-mid layui-word-aux">此处为全局开关,用户默认为关闭,需自行开启!</div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label required">链接扩展</label>
                <div class="layui-input-inline">
                    <select name="link_extend">
                        <option value="0" selected="">关闭</option>
                        <option value="1" >开启</option>
                    </select>
                </div>
                <div class="layui-form-mid layui-word-aux">自定义链接的扩展信息(需自行添加字段,目前仅用于自定义过渡页)</div>
            </div>
            
            <fieldset class="layui-elem-field layui-field-title" style="margin-top: 30px;"><legend>相关限制</legend></fieldset>
            <blockquote class="layui-elem-quote layui-text" style="">程序采用UTF8编码,一个汉字约占用3个字节!英文字母和数组占用1个字节!值为0表示不限制!</blockquote>
            <div class="layui-form-item">
                <label class="layui-form-label required">分类名称</label>
                <div class="layui-input-inline">
                    <input type="number" name="c_name" autocomplete="off" value="0" class="layui-input">
                </div>
                <div class="layui-form-mid layui-word-aux">字符长度限制,单位:字节。</div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label required">分类描述</label>
                <div class="layui-input-inline">
                    <input type="number" name="c_desc" autocomplete="off" value="0" class="layui-input">
                </div>
                <div class="layui-form-mid layui-word-aux">字符长度限制,单位:字节。</div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label required">链接名称</label>
                <div class="layui-input-inline">
                    <input type="number" name="l_name" autocomplete="off" value="0" class="layui-input">
                </div>
                <div class="layui-form-mid layui-word-aux">字符长度限制,单位:字节。</div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label required">链接地址</label>
                <div class="layui-input-inline">
                    <input type="number" name="l_url" autocomplete="off" value="0" class="layui-input">
                </div>
                <div class="layui-form-mid layui-word-aux">字符长度限制,单位:字节。</div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label required">链接描述</label>
                <div class="layui-input-inline">
                    <input type="number" name="l_desc" autocomplete="off" value="0" class="layui-input">
                </div>
                <div class="layui-form-mid layui-word-aux">字符长度限制,单位:字节。</div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label required">自定义代码</label>
                <div class="layui-input-inline">
                    <select name="c_code" lay-filter="c_code">
                        <option value="0" selected="">禁止</option>
                        <option value="1" >允许</option>
                    </select>
                </div>
                <div class="layui-form-mid layui-word-aux">是否允许默认用户组使用自定义代码!允许存在安全隐患!</div>
            </div>
            
            <div class="layui-form-item">
                <div class="layui-input-block"><button class="layui-btn layui-btn-normal" lay-submit lay-filter="save">确认保存</button></div>
            </div>
        </div>
    </form>
    </div>
</div>
<script src = "<?php echo $libs;?>/jquery/jquery-3.6.0.min.js"></script>
<?php load_static('js.layui');?>
<script>
layui.use(['jquery','form','miniTab'], function () {
    var form = layui.form;
    var layer = layui.layer;
    var $ = layui.jquery;
    var miniTab = layui.miniTab;
    miniTab.listen();
    //表单赋值
    form.val('form', <?php echo json_encode($global_config);?>);
    form.val('form', <?php echo json_encode(unserialize( get_db("global_config", "v", ["k" => "length_limit"])));?>);
    
    //危险提示
    form.on('select(c_code)', function(data){
        if (data.value === '1') {
            layer.alert("允许使用自定义代码存在安全隐患<br />除非您信任使用者!否则建议禁止<br />同时请避免在登录管理员账号时浏览其他用户的主页", { title: '危险提示:' })
        }
    });
    //监听提交
    form.on('submit(save)', function (data) {
        $.post('./index.php?c=api&method=write_sys_settings&u='+u,data.field,function(data,status){
            if(data.code == 1) {
                if(data.msg!="保存成功"){
                    layer.alert(data.msg)
                }else{
                    layer.msg(data.msg, {icon: 1});
                }
            }else{
                layer.msg(data.msg, {icon: 5});
            }
        });
        return false;
    }); 
    
    //开启隐藏功能
    $('.layui-elem-field').click(function () {
        let clickCount = Number($(this).attr('click') || 0);
        if (clickCount >= 6) {
            $(".layui-hide").removeClass("layui-hide");
        } else {
            $(this).attr('click', clickCount + 1);
        }
    });
    
});
</script>
</body>
</html>