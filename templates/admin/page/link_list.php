<?php $title='链接列表';$awesome=true; require 'header.php';  ?>
<style>
    .layui-table-tool-temp  {padding-right:1px;}
</style>
<body>
<div class="layuimini-container">
    <div class="layuimini-main">
        
        <div class="layui-inline layui-form" style="padding-bottom: 5px;">
            <label class="layui-form-label " style="width:60px;padding-left: 5px;padding-right: 5px;">分类筛选:</label>
            <div class="layui-input-inline">
                <select id="fid" lay-filter="fid" name="categorys" lay-search>
                </select>
            </div>
        </div>
        
        <div class="layui-inline layui-form" style="padding-bottom: 5px;">
            <label class="layui-form-label layui-hide-sm" style="width:60px;padding-left: 5px;padding-right: 5px;">关键字:</label>
            <div class="layui-input-inline">
                <input class="layui-input" name="keyword" id="link_keyword" placeholder='请输入标题或描述或URL' value=''autocomplete="off" >
            </div>
            
        </div>
        <div class="layui-inline layui-form layui-hide-xs" style="padding-bottom: 5px;" >
            <label class="layui-form-label layui-hide-sm" style="width:60px;padding-left: 5px;padding-right: 5px; ">属性筛选:</label>
            <div class="layui-input-inline" style=" width: 80px; ">
                <select id="property"  >
                    <option value="" selected>不限</option>
                    <option value="0" >公开</option>
                    <option value="1" >私有</option>
                </select>
            </div>
            <div class="layui-input-inline" style=" width: 80px; ">
                <select id="status"  >
                    <option value="" selected>不限</option>
                    <option value="0" >禁用</option>
                    <option value="1" >启用</option>
                </select>
            </div>
        </div>
        <div class="layui-inline layui-form" style="padding-bottom: 5px;">
            <button class="layui-btn layui-btn-normal " id="link_search" style="height: 36px;">搜索</button>
        </div>
        
        <span id = "testing_tip" style = "display:none;">测试中...</span>
        <span id = "subscribe" style = "display:none;"><?php echo is_subscribe('bool')?'1':'0' ?></span>
        <table id="table" class="layui-table" lay-filter="table" style="margin: -3px 0;"></table>
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
        <button class="layui-btn layui-btn-sm layui-btn-normal layui-btn-danger layui-hide-xs" lay-event="testing" id="testing" <?php echo $global_config['offline']?'style="display:none;"':''?> >检测</button>
        <button class="layui-btn layui-btn-sm layui-btn-normal layui-btn-danger" layuimini-content-href="link_sort" data-title="链接排序">排序模式</button>
    </div>
</script>
<script src = "<?php echo $libs;?>/jquery/jquery-3.6.0.min.js"></script>
<script src = "./templates/admin/js/public.js?v=<?php echo $Ver;?>"></script>
<?php load_static('js');?>
<script src = "./templates/admin/js/link_list.js?v=<?php echo $Ver;?>"></script>
<ul class="batch_category" style = "margin-top:18px;display:none;padding-right: 10px;" >
    <form class="layui-form" lay-filter="batch_category">
        
        <div class="layui-form-item">
            <label class="layui-form-label">父级分类</label>
            <div class="layui-input-block">
                <select  id="batch_category_fid">
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
</body>
</html>