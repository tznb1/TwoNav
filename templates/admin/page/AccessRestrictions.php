<?php $title='访问限制'; require 'header.php';?>
<style>
.layui-btn-container .layui-btn{border-width: 1px; border-style: solid; border-color: #FF5722!important; color: #FF5722!important;background: none;height: 30px; line-height: 30px; padding: 0 10px; font-size: 12px;}
</style>
<body>
<div class="layuimini-container">
    <div class="layuimini-main">
    <form class="layui-form" lay-filter="form">
        <div class="layui-form layuimini-form layui-form-pane">
            <blockquote class="layui-elem-quote layui-text" style="">
                此功能<a href="https://gitee.com/tznb/TwoNav/wikis/pages?sort_id=7968669&doc_id=3767990" target="_blank">授权用户</a>专享
            </blockquote>
            <blockquote class="layui-elem-quote layui-text" style="">白名单模式 > 除了名单中的账号和分组一律不可访问您的主页<br />黑名单模式 > 黑名单中的账号和分组不可访问您的主页<br />匹配优先级: 用户组名单 > 账号名单<br />使用黑白名单模式时,未登录账号不可访问您的主页<br />名单中多个账号或分组时用半角的逗号,间隔<br />新增功能,难免会有BUG,如有遇到请反馈并关闭该功能</blockquote>

            <div class="layui-form-item">
                <label class="layui-form-label">访问限制</label>
                <div class="layui-input-inline" >
                    <select name="mode">
                        <option value="0" selected="">无限制</option>
                        <option value="white">白名单模式</option>
                        <option value="black">黑名单模式</option>
                    </select>
                </div>
                <div class="layui-form-mid layui-word-aux"></div>
            </div>
            <div class="layui-form-item layui-form-text">
                <label class="layui-form-label">用户组名单</label>
                <div class="layui-input-block">
                    <textarea name="users_list" class="layui-textarea" placeholder='填写用户组代号,例如: group1,group2,group3'></textarea>
                </div>
            </div>
            <div class="layui-form-item layui-form-text">
                <label class="layui-form-label">账号名单</label>
                <div class="layui-input-block">
                    <textarea name="user_list" class="layui-textarea" placeholder='填写用户账号,例如: user1,user2,user3'></textarea>
                </div>
            </div>
            <div class="layui-form-item layui-form-text">
                <label class="layui-form-label">限制提示</label>
                <div class="layui-input-block">
                    <textarea name="prompt" class="layui-textarea" placeholder='留空时: 显示引导页
以http开头时: 跳转到url
其他内容则直接显示'></textarea>
                </div>
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

    //监听提交
    form.on('submit(save)', function (data) {
        return false;
    }); 
    
});
</script>
</body>
</html>