<?php $title='链接列表';$awesome=true; require 'header.php';  ?>
<style>
    .layui-table-tool-temp  {padding-right:1px;}
</style>
<body>
<div class="layuimini-container">
    <div class="layuimini-main">
      <form class="layui-form" lay-filter="form">
        <div class="layui-inline layui-form" style="padding-bottom: 5px;">
            <label class="layui-form-label " style="width:60px;padding-left: 5px;padding-right: 5px;">分类筛选:</label>
            <div class="layui-input-inline">
                <select name="fid" lay-search>
                    <option value="0" selected="">全部</option>
                    <optgroup label="用户分类">
                    <?php echo_category(true); ?>
                    </optgroup>
                </select>
            </div>
        </div>
        
        <div class="layui-inline layui-form" style="padding-bottom: 5px;">
            <label class="layui-form-label layui-hide-sm" style="width:60px;padding-left: 5px;padding-right: 5px;">关键字:</label>
            <div class="layui-input-inline">
                <input class="layui-input" name="query" id="link_keyword" placeholder='请输入标题或描述或URL' autocomplete="off" >
            </div>
        </div>
        <div class="layui-inline layui-form layui-hide-xs" style="padding-bottom: 5px;" >
            <label class="layui-form-label layui-hide-sm" style="width:60px;padding-left: 5px;padding-right: 5px; ">属性筛选:</label>
            <div class="layui-input-inline" style=" width: 80px; ">
                <select name="property"  >
                    <option value="" selected>不限</option>
                    <option value="0" >公开</option>
                    <option value="1" >私有</option>
                </select>
            </div>
            <div class="layui-input-inline" style=" width: 80px; ">
                <select name="status"  >
                    <option value="" selected>不限</option>
                    <option value="0" >禁用</option>
                    <option value="1" >启用</option>
                </select>
            </div>
        </div>
        <div class="layui-inline layui-form" style="padding-bottom: 5px;">
            <button class="layui-btn layui-btn-normal "type="button" id="link_search" style="height: 36px;">搜索</button>
        </div>
        <span id = "testing_tip" style = "display:none;">测试中...</span>
        <span id = "subscribe" style = "display:none;"><?php echo is_subscribe('bool')?'1':'0' ?></span>
      </form>
      <table id="table" class="layui-table" lay-filter="table" style="margin: 1px 0;"></table>
    </div>
</div>
<!-- 操作列 -->
<script type="text/html" id="tablebar">
    <div class="layui-btn-group">
        <a class="layui-btn layui-btn-danger layui-btn-xs" lay-event="del">删除</a>
        <a class="layui-btn layui-btn-normal layui-btn-xs" lay-event="edit">编辑</a>
    </div>
</script>
<!-- 表头工具栏 -->
<script type="text/html" id="toolbar">
    <div class="layui-btn-group">
        <button class="layui-btn layui-btn-sm layui-btn-normal layui-hide-sm layui-hide-md layui-hide-lg" lay-event="batch" id="batch">批量操作 <i class="layui-icon layui-icon-down layui-font-12"></i></button>
        <button class="layui-btn layui-btn-sm layui-btn-danger layui-hide-xs" lay-event="batch_del" id="batch_del">删除选中</button>
        <button class="layui-btn layui-btn-sm layui-btn-normal" lay-event="add_link" id="add_link">添加链接</button>
        <button class="layui-btn layui-btn-sm layui-btn-normal layui-hide-xs" lay-event="batch_category" id="batch_category">修改分类</button>
        <button class="layui-btn layui-btn-sm layui-btn-normal layui-hide-xs" lay-event="batch_private" id="batch_private">设为私有</button>
        <button class="layui-btn layui-btn-sm layui-btn-normal layui-hide-xs" lay-event="batch_public" id="batch_public">设为公开</button>
        <button class="layui-btn layui-btn-sm layui-btn-normal layui-hide-xs" lay-event="batch_start" id="batch_start">设为启用</button>
        <button class="layui-btn layui-btn-sm layui-btn-normal layui-hide-xs" lay-event="batch_disable" id="batch_disable">设为禁用</button>
<?php if($global_config['link_extend']  == 1 &&  check_purview('link_extend',1)){ ?> 
        <button class="layui-btn layui-btn-sm layui-btn-normal layui-btn-danger layui-hide-xs" lay-event="link_extend" id="link_extend">扩展字段</button>
<?php }?> 
<?php if($global_config['offline']  != 1 ){ ?> 
        <button class="layui-btn layui-btn-sm layui-btn-normal layui-btn-danger layui-hide-xs" lay-event="testing" id="testing">检测</button>
        <button class="layui-btn layui-btn-sm layui-btn-normal layui-btn-danger layui-hide-xs" lay-event="msg_pull" id="msg_pull">识别</button>
        <button class="layui-btn layui-btn-sm layui-btn-normal layui-btn-danger layui-hide-xs" lay-event="push" id="push">推送</button>
<?php }?> 
        <button class="layui-btn layui-btn-sm layui-btn-normal layui-btn-danger" layuimini-content-href="link_sort" data-title="链接排序">排序模式</button>
    </div>
</script>
<script src = "<?php echo $libs;?>/jquery/jquery-3.6.0.min.js"></script>
<script src = "./templates/admin/js/public.js?v=<?php echo $Ver;?>"></script>
<?php load_static('js');?>
<script src = "./templates/admin/js/link_list.js?v=<?php echo $Ver;?>"></script>
<ul class="batch_category" style="margin-top:18px;display:none;padding-right: 10px;">
    <form class="layui-form" lay-filter="batch_category">
        <div class="layui-form-item">
            <label class="layui-form-label">父级分类</label>
            <div class="layui-input-block">
                <select id="batch_category_fid">
                    <?php echo_category(true); ?>
                </select> 
            </div>
        </div>
        <div class="layui-form-item">
            <div class="layui-input-block">
                <button class="layui-btn layui-btn-normal" lay-submit lay-filter="batch_category" id="batch_category" >确定修改</button>
                <button class="layui-btn layui-btn-warm" type="button" id="close" >关闭</button>
            </div>
        </div>
    </form>
</ul>
<ul class="link_extend" style="margin-top: 18px;display:none;padding-right: 10px;padding-left: 10px;">
    <div class="layui-btn-container">
        <button class="layui-btn" lay-submit id="add_field">新增字段</button>
        <button class="layui-btn" lay-submit id="save_field">保存</button>
        <button class="layui-btn layui-btn-primary" style="color: red;">数据变更需要点击保存!确定好需要的字段后请勿随意修改,以免造成数据错乱!</button>
    </div>
    <table id="link_extend_list" lay-filter="link_extend_list"></table>
    <script type="text/html" id="link_extend_toolbar">
        <div class="layui-btn-container">
            <button class="layui-btn layui-btn-sm layui-btn-danger del" lay-event="del">移除</button>
        </div>
    </script>
</ul>
<?php if($global_config['offline']  != 1 ){ ?> 
<ul class="msg_pull" style="margin-top: 18px;display:none;padding-right: 10px;padding-left: 10px;">
    <form class="layui-form layuimini-form" lay-filter="msg_pull">
        <pre class="layui-code" id="tip" >提示: 自动识别仅针对http/https有效,且不能保证百分百成功!未成功识别时不会对链接信息进行修改!大批量识别前建议先备份数据,效果不理想时可以回退!</pre>
        <div class="layui-form-item">
            <label class="layui-form-label">网站标题</label>
            <div class="layui-input-inline">
                <select name="title" >
                    <option value="0">保持不变</option>
                    <option value="1">获取 (覆盖)</option>
                </select> 
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">关键字</label>
            <div class="layui-input-inline">
                <select name="keywords" >
                    <option value="0">保持不变</option>
                    <option value="1" selected>获取 (未填关键字时)</option>
                    <option value="2">获取 (覆盖)</option>
                </select> 
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">网站描述</label>
            <div class="layui-input-inline">
                <select name="description" >
                    <option value="0">保持不变</option>
                    <option value="1" selected>获取 (未填描述时)</option>
                    <option value="2">获取 (覆盖)</option>
                </select> 
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">网站图标</label>
            <div class="layui-input-inline">
                <select name="icon" >
                    <option value="0">保持不变</option>
                    <option value="1" selected>获取 (未上传图标时)</option>
                    <option value="2">获取 (覆盖)</option>
                </select> 
            </div>
        </div>
        <div class="layui-form-item">
            <div class="layui-input-block">
                <button class="layui-btn layui-btn-warm" type="button" id="close" >关闭</button>
                <button class="layui-btn layui-btn-normal" lay-submit lay-filter="start_pull" id="start_pull">开始</button>
            </div>
        </div>
    </form>
</ul>
<ul class="push" style="margin-top: 18px;display:none;padding-right: 10px;padding-left: 10px;">
    <form class="layui-form layuimini-form" lay-filter="push">
        <pre class="layui-code" >使用API推送功能会达到怎样效果
及时发现：可以缩短百度爬虫发现您站点新链接的时间，使新发布的页面可以在第一时间被百度收录
保护原创：对于网站的最新原创内容，使用API推送功能可以快速通知到百度，使内容可以在转发之前被百度发现
百度官方说明: https://ziyuan.baidu.com/linksubmit/index
注意事项: 推送的URL是静态格式,所以请务必正确配置好伪静态!
伪静态配置: 请前往站长工具>生成伪静态,并复制内容配置到服务器 (仅针对Nginx)
</pre>
        <div class="layui-form-item">
            <label class="layui-form-label">接口地址</label>
                <div class="layui-input-block">
                    <input type="text" name="push_api" id="push_api" placeholder="请输入接口调用地址如 http://data.zz.baidu.com/urls?site=lm21.top&token=xxxxxx" 
                    value="<?php echo get_db("user_config", "v", ["k" => "baidu_push_api","uid"=>UID]); ?>" class="layui-input">
                </div>
        </div>
        <div class="layui-form-item">
            <div class="layui-input-block">
                <button class="layui-btn layui-btn-warm" type="button" id="close" >关闭</button>
                <button class="layui-btn layui-btn-normal" lay-submit lay-filter="start_push" id="start_push">开始</button>
            </div>
        </div>
    </form>
</ul>
<?php }?> 
</body>
</html>