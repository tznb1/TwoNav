<?php 
if($USER_DB['UserGroup'] != 'root'){$content='您没有权限访问此页面'; require(DIR.'/templates/admin/page/404.php');exit;}
$title='站点地图';require(dirname(__DIR__).'/header.php');
?>
<body>
<div class="layuimini-container">
    <div class="layuimini-main">
    <form class="layui-form" lay-filter="form">
        <div class="layui-form layuimini-form layui-form-pane">
            <blockquote class="layui-elem-quote layui-text" style="">
                2023.09.05 新增功能 (授权用户专享),如有问题请及时反馈 ! <br />
                URL格式: 静态地址需配置伪静态,二级域名需配置DNS泛解析和服务器绑定并已开启功能<br />
<?php if(preg_match('/nginx/i',$_SERVER['SERVER_SOFTWARE']) ){ ?>
                注意: 此功能依赖伪静态,请前往站长工具>生成伪静态,并复制内容配置到服务器
<?php } ?>
            </blockquote>
            <fieldset class="layui-elem-field layui-field-title" style="margin-top: 30px;"><legend>综合配置</legend></fieldset>
            <div class="layui-form-item">
                <label class="layui-form-label">功能开关</label>
                <div class="layui-input-inline" >
                    <select name="switch">
                        <option value="0" selected>关闭</option>
                        <option value="1">开启</option>
                    </select>
                </div>
                <div class="layui-form-mid layui-word-aux">关闭后将禁止生成和读取站点地图</div>
            </div>
            
            <fieldset class="layui-elem-field layui-field-title" style="margin-top: 30px;"><legend>自动生成</legend></fieldset>
            
            <div class="layui-form-item">
                <label class="layui-form-label">被动请求</label>
                <div class="layui-input-inline" >
                    <select name="beidong">
                        <option value="0">不生成</option>
                        <option value="1">生成</option>
                    </select>
                </div>
                <div class="layui-form-mid layui-word-aux">当请求根目录的<a href="./sitemap.xml" target="_blank">sitemap.xml</a>是否生成地图数据(需配置伪静态),受更新频率的限制</div>
            </div>
            
            <div class="layui-form-item">
                <label class="layui-form-label">主动更新</label>
                <div class="layui-input-inline" >
                    <select name="zhudong">
                        <option value="0" selected>关闭</option>
                        <option value="1">添加链接时</option>
                    </select>
                </div>
                <div class="layui-form-mid layui-word-aux">受更新频率的限制</div>
            </div>
            
            <div class="layui-form-item">
                <label class="layui-form-label">更新频率</label>
                <div class="layui-input-inline" >
                    <select name="changefreq">
                        <option value="monthly">每月</option>
                        <option value="weekly">每周</option>
                        <option value="daily" selected>每天</option>
                        <option value="hourly">每小时</option>
                        <option value="minute">每分钟(不推荐)</option>
                        <option value="second">每秒钟(仅用于测试)</option>
                    </select>
                </div>
                <div class="layui-form-mid layui-word-aux">根据自己站点的更新频率设置</div>
            </div>
            
            <fieldset class="layui-elem-field layui-field-title" style="margin-top: 30px;"><legend>用户主页</legend></fieldset>
            <div class="layui-form-item">
                <label class="layui-form-label">URL格式</label>
                <div class="layui-input-block" >
                    <select name="user_homepage">
                        <option value="0" >不生成</option>
                        <option value="1" selected="">动态地址 | http://example.com/index.php?u=user</option>
                        <option value="2" >静态地址 | http://example.com/user.html</option>
                        <option value="3" >二级域名 | http://user.example.com</option>
                    </select>
                </div>
            </div>
            
            <div class="layui-form-item">
                <label class="layui-form-label">权重</label>
                <div class="layui-input-inline">
                    <input type="text" name="user_homepage_weight" lay-verify="required" value="0.9" autocomplete="off" class="layui-input">
                </div>
            </div>
            
            <div class="layui-form-item">
                <label class="layui-form-label">更新频率</label>
                <div class="layui-input-inline" >
                    <select name="user_homepage_changefreq"></select>
                </div>
            </div>
            
            <fieldset class="layui-elem-field layui-field-title" style="margin-top: 30px;"><legend>过度页面</legend></fieldset>
            <div class="layui-form-item">
                <label class="layui-form-label">URL格式</label>
                <div class="layui-input-block" >
                    <select name="click_page">
                        <option value="0" >不生成</option>
                        <option value="1" selected="">动态地址 | http://example.com/index.php?c=click&id=1&u=user</option>
                        <option value="2" >静态地址 | http://example.com/user/click/1.html</option>
                    </select>
                </div>
            </div>
            
            <div class="layui-form-item">
                <label class="layui-form-label">权重</label>
                <div class="layui-input-inline">
                    <input type="text" name="click_page_weight" lay-verify="required" value="0.8" autocomplete="off" class="layui-input">
                </div>
            </div>
            
            <div class="layui-form-item">
                <label class="layui-form-label">更新频率</label>
                <div class="layui-input-inline" >
                    <select name="click_page_changefreq"></select>
                </div>
            </div>
            
            <!--<fieldset class="layui-elem-field layui-field-title" style="margin-top: 30px;"><legend>文章页面</legend></fieldset>-->
            <!--<div class="layui-form-item">-->
            <!--    <label class="layui-form-label">URL格式</label>-->
            <!--    <div class="layui-input-block" >-->
            <!--        <select name="article_page">-->
            <!--            <option value="0" >不生成</option>-->
            <!--            <option value="1" selected="">动态地址 | http://example.com/index.php?c=article&id=1&u=user</option>-->
            <!--            <option value="2" >静态地址 | http://example.com/user/article/1.html</option>-->
            <!--        </select>-->
            <!--    </div>-->
            <!--</div>-->
            
            <!--<div class="layui-form-item">-->
            <!--    <label class="layui-form-label">权重</label>-->
            <!--    <div class="layui-input-inline">-->
            <!--        <input type="text" name="article_page_weight" lay-verify="required" value="0.8" autocomplete="off" class="layui-input">-->
            <!--    </div>-->
            <!--</div>-->
            
            <!--<div class="layui-form-item">-->
            <!--    <label class="layui-form-label">更新频率</label>-->
            <!--    <div class="layui-input-inline" >-->
            <!--        <select name="article_page_changefreq"></select>-->
            <!--    </div>-->
            <!--</div>-->
            
            <div class="layui-form-item">
                <div class="layui-input-block">
                    <button class="layui-btn layui-btn-normal" lay-submit lay-filter="save">保存配置</button>
                    <button class="layui-btn layui-btn-normal" lay-submit lay-filter="generate">手动生成</button>
                </div>
            </div>
        </div>
    </form>
    </div>
</div>
<?php load_static('js.layui');?>
<script src="./templates/admin/js/public.js?v=<?php echo $Ver;?>"></script>
<script>
layui.use(['jquery','form'], function () {
    var form = layui.form;
    var layer = layui.layer;
    var $ = layui.jquery;
    const changefreq_Map = {"always": "始终","hourly": "每小时","daily": "每天","weekly": "每周","monthly": "每月","yearly": "每年","never": "从不"};

    //给更新频率下拉框添加选项
    $("select[name$='_changefreq']").each(function() {
        const select = $(this);
        $.each(changefreq_Map, function(optionValue, optionText) {
            select.append($("<option>").text(optionText).val(optionValue));
        });
        select.val('daily'); //默认值改为每天
    });
    //刷新组件
    layui.form.render('select');
    
    //表单赋值
    form.val('form', <?php echo json_encode(unserialize( get_db("global_config", "v", ["k" => "sitemap_config"])));?>);

    //监听提交
    form.on('submit(save)', function (data) {
        $.post(get_api('other_root','write_sitemap_config'),data.field,function(data,status){
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
    //测试
    form.on('submit(generate)', function (data) {
        layer.load(1, {shade:[0.3,'#fff']});
        layer.msg('正在处理中..', {icon: 16,time: 1000*300});
        $.post('./?c=sitemap&mode=manual',data.field,function(data,status){
            layer.closeAll();
            if(data.code == 1) {
                layer.alert(data.msg);
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