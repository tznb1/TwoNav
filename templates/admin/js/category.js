    layui.use(['form','table','treetable','xIcon'], function () {
        var $ = layui.jquery;
        var table = layui.table;
        var treetable = layui.treetable;
        var form = layui.form;
        var xIcon = layui.xIcon;
        var data_tr;
        var type = '&type=all';
        var index,limit,disabled_id;
        var pwds = [];
        

    //表头
    cols =[[
            {type:'radio'}
            ,{field:'cid',title:'ID',width:62,align:'center'}
            ,{field:'name',title:'分类名称',width:220}
            ,{title:'操作',toolbar:'#tablebar',width:120}
            ,{field:'pwd_id',title:'密码',width:70,templet: function(d){
                return d.pwd_id>0?'<a class="layui-btn layui-btn-normal layui-btn-xs" lay-event="pwd">查看</a>':'';
            }}
            ,{field:'add_time', title: '添加时间', width:166,templet:function(d){return timestampToTime(d.add_time);}}
            //,{field:'up_time',title:'修改时间',width:166,templet:function(d){return timestampToTime(d.up_time)}}
            ,{field: 'count', title: '链接数', width: 80,align:'center'}
            ,{field: 'property', title: '私有', width: 100,templet: function(d){
                return "<input type='checkbox' value='" + d.id + "' lay-filter='property' lay-skin='switch' lay-text='私有|公开' " + (d.property == 1?"checked":"" )+  ">";
            }}
            ,{field: 'status', title: '状态', width: 100,templet: function(d){
                return "<input type='checkbox' value='" + d.id + "' lay-filter='status' lay-skin='switch' lay-text='启用|禁用' " + (d.status == 1?"checked":"" )+  ">";
            }}
            
            ,{field:'description', title: '描述'}
            
           ]];

    //渲染表格函数
    var renderTable = function () {
        layer.load(2);
        treetable.render({
            treeColIndex: 2,
            treeSpid: 0,
            height: 'full-32',
            treeIdName: 'cid',
            treePidName: 'fid',
            elem: '#table',
            treeDefaultClose: false, //是否默认折叠
            url: get_api('read_category_list') + type,
            page: false,
            even: true,
            cols: cols,
            skin:'line',
            toolbar: '#toolbar',
            defaultToolbar:false,
            done: function (res, curr, count) { //渲染完毕事件
                $("[data-field='0']").css('display','none');//隐藏列
                //手机端不显示的列
                $("[data-field='cid']").addClass('layui-hide-xs');
                $("[data-field='description']").addClass('layui-hide-xs');
                $("[data-field='add_time']").addClass('layui-hide-xs');
                $("[data-field='up_time']").addClass('layui-hide-xs');
                $("[data-field='count']").addClass('layui-hide-xs');
                //显示底部条
                $('.treeTable .layui-table-page').css('display', 'block'); 
                //删除底部的分页元素
                $(".treeTable .layui-laypage-skip").remove(); 
                $(".treeTable .layui-laypage-limits").remove();
                $(".treeTable .layui-laypage-curr").remove();
                $(".treeTable .layui-table-page a").remove();
                //条数左边留空(手机端遮挡)
                $(".treeTable .layui-laypage-count").css("padding-left","35px");
                //添加当前选择提示
                $('.treeTable .layui-box').append('<span class="layui-laypage-count" id="Tips"></span>');
                //分类切换按钮
                $("#btn-type").text(type == '&type=all' ?'收起':'展开');
                //将一级分类加入下拉框
                $("#fid").empty();
                $("#fid").append("<option value=\"0\">无</option>");
                for (i = 0; i < count; i++) {
                    if(res.data[i].fid == 0){
                        $("#fid").append("<option value=\""+res.data[i].cid+"\">"+res.data[i].name+"</option>");
                    }
                }
                //加载加密分组数据
                $.post(get_api('read_pwd_group_list'),{'page':'1','limit':'9999'},function(data,status){
                    if(data.code == 1){
                        pwds = [];
                        $("#pwd_id").empty();
                        $("#pwd_id").append("<option value=\"0\">无</option>");
                        for(var i =0;i<data.count;i++){
                            pwds['pid_'+data.data[i].pid] = {'pwd':data.data[i].password,'name':data.data[i].name};
                            $("#pwd_id").append("<option value=\""+data.data[i].pid+"\">"+data.data[i].name+" | 密码 [" + data.data[i].password +"]</option>");
                        }
                    }
                });
                
                layui.form.render("select");//重新渲染下拉框
                limit = false; //取消修改限制
                layer.closeAll('loading'); //关闭加载层
            } 
        });
    };
    
    //渲染表格
    renderTable();
    
    //行点击事件
    table.on('row(table)', function(obj) {
	    obj.tr.addClass('layui-bg-black').siblings().removeClass('layui-bg-black');
	    obj.tr.find('i[class="layui-anim layui-icon"]').trigger("click");
        $("#Tips").text('当前选中: ' + obj.data.name);
    });
    
    //开关监听
    form.on('switch(property)',function(obj) {
	    var sw = obj.elem.checked; //取开关状态
	    var cid = obj.elem.value;
	    var contexts = sw?'私有':'公开';
	    $.post(get_api('write_category','property_sw'),{'cid': obj.elem.value,'property': sw?'1':'0'},function(data, status) {
			if (data.code == 1) {
				layer.msg('已设为' + contexts);
			} else {
				layer.msg('设为' + contexts + '失败');
			}
		});
	    console.log(cid,sw);
    });
    
    //开关监听
    form.on('switch(status)',function(obj) {
	    var sw = obj.elem.checked; //取开关状态
	    var cid = obj.elem.value;
	    var contexts = sw?'启用':'禁止';
	    $.post(get_api('write_category','status_sw'),{'cid': obj.elem.value,'status': sw?'1':'0'},function(data, status) {
			if (data.code == 1) {
				layer.msg('已设为' + contexts);
			} else {
				layer.msg('设为' + contexts + '失败!');
			}
		});
	    console.log(cid,sw);
    });
    
    //监听工具条
    table.on('tool(table)', function (obj) {
        if(limit){layer.tips("^_^ 请先保存排序.","#btn-save",{tips: [3, "#3595CC"],time: 3000});return;}
        var data = obj.data;
        if (obj.event === 'del') {
            layer.confirm('确认删除?',{icon: 3, title:'温馨提示'}, function(index){
                $.post(get_api('write_category','del'),{cid:data.cid},function(data,status){
                    if(data.code == 1) {
                        renderTable();
                        layer.msg(data.msg, {icon: 1});
                    }else{
                        layer.msg(data.msg, {icon: 5});
                    }
                });
            });
        }else if(obj.event === 'edit') {
            $("#save").text('更新');
            $("#fid option[value='" + data.cid + "']").attr("disabled","disabled");disabled_id = data.cid; //禁止选择自己
            $("#continuity_f").css('display','none');//隐藏连续添加
            xIcon.setValue('#font_icon',data.font_icon);//设置图标
            form.val('add',data);
            form.val('add',{"type":"edit","property":data.property == 1});
            
            //form.val('add', {"type": "edit","cid": data.cid,"name": data.name,"font_icon": data.font_icon,"icon":data.icon,"fid": data.fid,"property": (data.property ==1),"description": data.description });
            index = layer.open({type: 1,scrollbar: false,shadeClose: true,title: '编辑分类',area : ['100%', '100%'],content: $('.add')});
        }else if(obj.event === 'pwd'){
            layer.alert( '名称: ' + pwds['pid_' + data.pwd_id].name + '<br>密码: ' + pwds['pid_' + data.pwd_id].pwd,{icon:4,title: data.name+ ' - 访问密码',anim: 2,closeBtn: 0});
        }
    });
    
    //工具栏监听
    table.on('toolbar(table)', function(obj){
        $("*").blur();
        if(obj.event == 'refresh'){
            renderTable();
        }else if(obj.event == 'add'){
            if(limit){layer.tips("^_^ 请先保存排序.","#btn-save",{tips: [3, "#3595CC"],time: 3000});return;}
            xIcon.setValue('#font_icon','fa fa-star-o');//默认星星图标
            form.val('add', {"name": "","description": "","type": "add","icon":"","fid":'0',"pwd_id":"0"});
            //$("#name").val(randomString(8)); //调试用,随机字符串,省的输入名称
            $("#save").text('新增');
            $("#continuity_f").css('display','block');
            index = layer.open({type: 1,scrollbar: false,shadeClose: true,title: '新增分类',area : ['100%', '100%'],content: $('.add')});
        }else if(obj.event == 'uptr'){
            uptr();
        }else if(obj.event == 'downtr'){
            downtr();
        }else if(obj.event == 'save'){
            updateSortData();
        }else if(obj.event == 'type'){ //收起/展开
            if(limit){layer.tips("^_^ 请先保存排序.","#btn-save",{tips: [3, "#3595CC"],time: 3000});return;}
            if(type == '&type=all'){
                type = '&type=onlyf';
                renderTable();
            }else{
                type = '&type=all';
                renderTable();
            }
        }
    });
    
    //关闭按钮
    $(document).on('click', '#close', function() {
        $("#fid option[value='" + disabled_id + "']").removeAttr("disabled");//取消禁止
        layer.close(index);//关闭当前页
    });
    //单选框选择事件
    table.on('radio(table)', function(obj){ 
        data_tr = $(this);
    });

    //图标选择器初始化
    xIcon.render({
       elem: '#font_icon',
       type: 'awesome', //'layui,awesome'
       search: true, // 是否开启搜索
       page: false, // 是否开启分页
       limit: 100, // 每页显示数量
       click: function (data) {// 点击回调
           console.log(data.icon);
           $("#font_icon").val(data.icon);
        }
    });
    
    //添加/更新
    form.on('submit(save)', function (data) {
        $("*").blur();
        $.post(get_api('write_category',data.field.type),data.field,function(data,status){
            if(data.code == 1) {
                renderTable(); //刷新表
                //添加成功,如果勾选连续添加且元素未隐藏
                if($("#continuity").is(":checked") && $('#continuity_f').css('display') !='none'){
                    $("#name").val('');$("#description").val('');
                    layer.msg('添加成功', {icon: 1});
                    $("#name").focus()
                    return false;
                }
                layer.msg(data.msg, {icon: 1});
                layer.close(index);
                $("#fid option[value='" + disabled_id + "']").removeAttr("disabled");//取消禁止
            }else{
                layer.msg(data.msg, {icon: 5});
            }
        });
        return false;
    });
    
    

//保存排序
function updateSortData() {
    var new_datas = [];
    var datas = layui.table.cache["table"];
    if(datas.length > 1){
        if(type == '&type=all'){
            for (var i = 0; i < datas.length; i++) {
                if(datas[i].fid != 0){fi++;new_datas.push([datas[i].cid,fi])}else{fi = 0;}
            }
        }else{
            for (var i = 0; i < datas.length; i++) {new_datas.push([datas[i].cid,i+1])}
        }
    }
    $.post(get_api('write_category','order'),{data:new_datas},function(data,status){
        if(data.code == 1) {
            renderTable(); //刷新表
            layer.msg("保存排序成功",{icon:1});
        }else{
            layer.msg("保存排序失败",{icon:5});
        }
    });
}

// 上移
function uptr() {
    var datas = layui.table.cache["table"];
    var checkStatus = table.checkStatus('table'), data = checkStatus.data;
    if (typeof (data[0]) == "undefined") {
        layer.msg("请选择一条要移动的数据");
    } else {
        var tr = $(data_tr).parent().parent().parent();
        var tem = datas[tr.index()];
        var tem2 = datas[tr.prev().index()];
        if(type == '&type=all' && tem.fid == 0 ){
            layer.tips("^_^ 点我.","#btn-type",{tips: [3, "#3595CC"],time: 3000});
            layer.msg("请收起二级分类在排序",{icon:5});
            return;
        }
        if ($(tr).prev().html() == null) {
            layer.msg("已经是最顶部了");
            return;
        } 
        if(tem.fid != 0 && tem2.cid == tem.fid){
            layer.msg("不能超出父分类");
            return;
        }
        limit = true;
        $(tr).insertBefore($(tr).prev());
        datas[tr.index()] = tem;
        datas[tr.next().index()] = tem2;
    }
}

// 下移
function downtr() {
    var datas = layui.table.cache["table"];
    var checkStatus = table.checkStatus('table'), data = checkStatus.data;
    if (typeof (data[0]) == "undefined") {
        layer.msg("请选择一条要移动的数据");
    } else {
        var tr = $(data_tr).parent().parent().parent();
        var tem = datas[tr.index()];
        var tem2 = datas[tr.next().index()];
        if(type == '&type=all' && tem.fid == 0 ){
            layer.tips("^_^ 点我.","#btn-type",{tips: [3, "#3595CC"],time: 3000});
            layer.msg("请收起二级分类在排序",{icon:5});
            return;
        }
        if ($(tr).next().html() == null) {
            layer.msg("已经是最底部了");
            return;
        }
        if(tem.fid != 0 && tem.fid != tem2.fid){
            layer.msg("不能超出父分类");
            return;
        }
        limit = true;
        $(tr).insertAfter($(tr).next());
        datas[tr.index()] = tem;
        datas[tr.prev().index()] = tem2;
    }
}

});