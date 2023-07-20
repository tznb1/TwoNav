var blank_img = 'data:image/bmp;base64,Qk1CAAAAAAAAAD4AAAAoAAAAAQAAAAEAAAABAAEAAAAAAAQAAADEDgAAxA4AAAAAAAAAAAAAAAAAAP///wCAAAAA';
var bak_link_id = 0;
var page_sid = '';
var link_id = '';
var load_index;
var module = _GET('source') === 'tpl' ? ['form', 'upload'] : ['form', 'upload', 'miniTab'];
layui.use(module, function () {
    var $ = layui.jquery;
    var form = layui.form;
    var upload = layui.upload;
    var edit_mode = _GET('page') == 'link_edit'; //是否编辑模式
    //独立页面
    if(top.location == self.location){
        $(".layuimini-container").addClass("layui-col-lg6 layui-col-md-offset3");
    }
    //初始变量
    if(edit_mode){
        link_id = form_data.lid;
    }else{
        page_sid = randomString(8);
    }
    
    //添加链接
    form.on('submit(add_link)', function(data){
        data.field.file = page_sid; //传递sid,用于上传图标时保存图标
        $.post(get_api('write_link','add'),data.field,function(data,status){
            if(data.code == 1) {
                //将服务端返回的图标填入页面
                if(data.path != '' ){$("#iconurl").val(data.path);}
                //如果勾选连续添加
                if($("#continuity").is(":checked")){
                    form.val('form',{'url':'','title':'','description':'','icon':'','keywords':''});
                    $('form input[name^="_"]').val(''); //扩展字段清空
                    $('#icon_img').attr('src', blank_img);//清除缩略图
                    layer.msg('添加成功', {icon: 1});
                    $("#url").focus();//URL获取输入焦点
                    if(_GET('source')=='link_list'){
                        parent.layui.table.reload('table');//刷新父页面的表格
                    }
                    return false;
                }
                layer.msg('添加成功！', {icon: 1,time: 700,
				    end: function() {
				        if(_GET('source') == 'tpl'){ //第三方调用时刷新父页面
				            parent.location.reload();
				        }else if(_GET('source')=='link_list'){ // 链接列表调用
				            parent.layui.table.reload('table');//刷新父页面的表格
				            $('#close').click();//关闭子页面
				        }else{
				            if($("#continuity").is(":checked")){
				                location.reload();
				            }else{
				                $('#close').click();
				            }
				            
				        }
				    }
                });
            }else{
                layer.msg(data.msg, {icon: 5});
            }
        });
        return false; 
    });
    
    //上传图标
    upload.render({
        elem: '#up_icon'
        ,url: get_api('write_link','upload_images')
        ,exts: 'jpg|jpeg|png|ico|bmp|svg'
        ,acceptMime:  'image/*'
        ,accept: 'file'
        ,size: 1024 
        ,data: {"page_sid":page_sid,"link_id":link_id}
        ,choose: function(obj){  //选择文件回调
            load_index = layer.load(1);
            obj.preview(function(index, file, result){
                $("#ico_preview").show(); //显示预览图
                $('#icon_img').attr('src', result); //加载预览图
            });
        }
        ,done: function(res){
            layer.close(load_index);
            if(res.code == 1){
                form_data.icon = res.icon;
                $("#icon").val(res.icon);
            }else{
                layer.msg(res.msg || '上传失败', {icon: 5});
            }

        },error: function(){
            layer.msg("上传异常,请刷新重试", {icon: 5});
        }
    });    
    
    //编辑链接(保存更新)
    form.on('submit(edit_link)', function(data){
        $.post(get_api('write_link','edit'),data.field,function(data,status){
            if(data.code == 1) {
                if(data.icon != '' ){
                    $("#icon").val(data.icon);
                    preview_icon(data.icon);
                }else{
                    $('#icon_img').attr('src', blank_img); //清除预览图
                    $("#ico_preview").hide();
                }
                if(top.location == self.location){
                    layer.msg('已更新！', {icon: 1});
                }else{
                    if(_GET('source') == 'tpl'){ //第三方调用时刷新父页面
				        layer.msg('添加成功！', {icon: 1,time: 700,
				            end: function() {
				                parent.location.reload();
				                $('#close').click();//关闭子页面
				            }
                        });
				    }else{
                        parent.layui.table.reload('table');//刷新父页面的表格
                        parent.layui.layer.msg('已更新！', {icon: 1});
                        $('#close').click();//关闭子页面
				    }
                }
            }else{
                layer.msg(data.msg, {icon: 5});
            }
        });
        return false; 
    });
    

    //权限不足提示
    $(document).on('click', '#no_purview', function() {
        layer.msg("您的用户组无权限上传图标", {icon: 2});
    });
    
    //关闭按钮
    $(document).on('click', '#close', function() {
        //独立页面时关闭页面,而非标签
        if(top.location == self.location){
            window.close(); //关闭当前页面
        }else{
            parent.layer.close(parent.layer.getFrameIndex(window.name));//关闭当前页(内嵌窗口)
            if(_GET('source') != 'tpl'){
                layui.miniTab.deleteCurrentByIframe(); //关闭当前标签(标签窗口)
            }
        }
    });
    
    //添加备用链接
    $(document).on('click', '#add_standby_url', function() {
        if(bak_link_id >= 10){
            layer.msg("最多能支持10条备用链接!", {icon: 2});
            return false; 
        }
        bak_link_id++;
        html = '<div class="layui-form-item"><label class="layui-form-label required">备用链接</label><div class="layui-input-block"><input type="url" name="url_standby[]" lay-verify="required" placeholder="[百度](https://www.baidu.com) 或 https://www.baidu.com" autocomplete="off" class="layui-input obtn" ><div style="position: absolute;top: 0px;"><span><a class="layui-btn layui-btn-primary" id="del_standby_url"><i class="fa fa-trash-o"></i> 删除</a></span></div></div></div>';
        $("#backup_link").append(html);
    });
    
    //删除备用链接
    $(document).on('click', '#del_standby_url', function() {
        if($(this).parents('.layui-form-item').remove()){
            bak_link_id--;
        }else{
            layer.msg("删除失败", {icon: 3});
        }
    });
    
    //删除图标
    $(document).on('click', '#del_icon', function() {
        $.post(get_api('write_link','del_images'),{"page_sid":page_sid,"link_id":link_id},function(data,status){
            if(data.code == 1) {
                form_data.icon = '';
                $("#icon").val(''); //清除图标URL
                $('#icon_img').attr('src', blank_img); //清除预览图
                $("#ico_preview").hide();
            }else{
                layer.msg(data.msg, {icon: 5});
            }
        });
    });
    
    if(edit_mode){
        //重置按钮点击事件
        $(document).on('click', '#reset', function() {
            form.val('form',form_data); //重置表单
            form.val('form',{"property":form_data.property == 1});
            //重新渲染备用链接
            $("#backup_link").empty();
            bak_link_id = 0;
            for (let i=0; i<form_data.url_standby.length; i++){
                bak_link_id++;
                html = '<div class="layui-form-item"><label class="layui-form-label required">备用链接</label><div class="layui-input-block"><input type="url" name="url_standby[]" lay-verify="required" placeholder="[百度](https://www.baidu.com) 或 https://www.baidu.com" autocomplete="off" class="layui-input obtn" value="'+ form_data.url_standby[i] +'"><div style="position: absolute;top: 0px;"><span><a class="layui-btn layui-btn-primary" id="del_standby_url"><i class="fa fa-trash-o"></i> 删除</a></span></div></div></div>';
                $("#backup_link").append(html);
            }
        });
        //点击重置来填表
        $("#reset").click();
        //加载预览图
        preview_icon(form_data.icon);
    }else{
        if(_GET('fid') > 0){
            form.val('form',{"fid":_GET('fid')});
        }
    }
    
//layui>end
});

//加载图标
function preview_icon(icon =''){
    if(icon.length != 0){
        $("#ico_preview").show();
        if(icon.substr(0,5) =='data:') {
            $('#icon_img').attr('src',icon) ;
        }else if(icon.substr(0,4) == '<svg'){
            $('#icon_img').attr('src','data:image/svg+xml;base64,'+ btoa(icon.replace(/[\u00A0-\u2666]/g, function(c) {return '&#' + c.charCodeAt(0) + ';';}))  );
        }else{
            $('#icon_img').attr('src',icon + (icon.indexOf('?') !== -1 ? '&_t=' : '?_t=')  + Math.random() ) ;
        }
    }
}

//异步识别链接信息
function get_link_info() {
    var url = $("#url").val();
    load_index = layer.load(1);
    $.post(get_api('other_get_link_info'),{url:url},function(data,status){
        if(data.code == 1) {
            if(data.data.title != null) {
                $("#title").val(data.data.title);
            }
            if(data.data.description != null) {
                $("#description").val(data.data.description);
            }
            if(data.data.keywords != null) {
                $("#keywords").val(data.data.keywords);
            }
        }else{
            layer.msg(data.msg, {icon: 5});
        }
        layer.close(load_index);
    });
}
