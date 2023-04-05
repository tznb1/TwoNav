layui.use(['form','table'], function () {
    var form = layui.form;
    var table = layui.table;
    var data_tr,table_page;
    var api = get_api('read_link_list'); //列表接口
    var limit = localStorage.getItem(u + "_limit")??50; //尝试读取本地记忆数据,没有就默认50

    var cols=[[ //表头
      {type:'radio'} //开启单选框
      ,{field: 'lid', title: 'ID', width:80, sort: true,hide:true}
      ,{field: 'weight', title: '权重', width:80, sort: true,hide:true}
      ,{field: 'title', title: '链接标题', width:200}
      ,{field: 'url', title: 'URL'}
    ]];
    
    //渲染表格
    table.render({
        elem: '#table'
        ,height: 'full-110' //自适应高度
        ,url: api //数据接口
        ,where: {fid:$("#fid").val()}
        ,page: true //开启分页
        ,limit:limit  //默认每页显示行数
        ,limits: [20,50,100,300,500]
        ,even:true //隔行背景色
        ,loading:true //加载条
        ,defaultToolbar:false
        ,toolbar: '#toolbar'
        ,id:'table'
        ,cols: cols
        ,method: 'post'
        ,response: {statusCode: 1 } 
        ,done: function (res, curr, count) {
            //渲染完毕回调
            let temp_limit = $(".layui-laypage-limits option:selected").val();
            if(temp_limit > 0 && localStorage.getItem(u + "_limit") != temp_limit){
                localStorage.setItem(u + "_limit",temp_limit);
                limit = temp_limit;
            }
            table_page = curr; //记页码
        }
    });
    //选择分类事件
    form.on('select(fid)', function(data){
        table.reload('table', {
            url: api
            ,method: 'post'
            ,request: {
                pageName: 'page'
                ,limitName: 'limit'
            }
            ,where: {fid:data.value}
            ,page: {curr: 1}
        });
    });
    //单选框选择事件
    table.on('radio(table)', function(obj){ 
        data_tr = $(this);
    });
    //行点击事件
    table.on('row(table)', function(obj) {
	    obj.tr.addClass('layui-bg-black').siblings().removeClass('layui-bg-black');
	    obj.tr.find('i[class="layui-anim layui-icon"]').trigger("click");
    });

    //监听工具栏
    table.on('toolbar(table)', function (obj) {
        var checkStatus = table.checkStatus(obj.config.id);
        if( checkStatus.data.length == 0 && ['refresh','save','tip'].indexOf(obj.event) == -1 ) {
            layer.msg('未选中任何数据！');
            return;
        }
        if(obj.event == 'refresh'){
            table.reload('table');
        }else if(obj.event == 'save'){
            updateSortData();
        }else if(obj.event == 'tip'){
            layer.alert("1.选中需要排序的链接<br />2.按需移动数据位置<br />3.移动好了点击保存即可<br />#.排序仅针对当前页面数据",{title:'排序提示',anim: 5,closeBtn: 0,btn: ['知道了']});
        }else{
            table_tr_move(obj.event);
        }
    });
    
    //保存排序
    function updateSortData() {
        let new_datas = [];
        let weight = (table_page - 1) * limit;
        $('table tr td[data-field="lid"]').each(function(i){
            weight++;
            new_datas.push([$(this).text(),weight]);
        });
        if(new_datas.length == 0){
            layer.msg("表格无数据",{icon:5});
            return;
        }
        console.log(JSON.stringify(new_datas));
        $.post(get_api('write_link','order'),{data:new_datas},function(data,status){
            if(data.code == 1) {
                table.reload('table'); //刷新表
                layer.msg("保存排序成功",{icon:1});
            }else{
                layer.msg("保存排序失败",{icon:5});
            }
        });
    }

    function table_tr_move(type){
        let tr = $(data_tr).parent().parent().parent();
        if(type == 'up_tr' || type == 'up_top'){
            if($(tr).prev().html() == null){
                layer.msg("已经是最顶部了");
                return;
            }
            if(type == 'up_tr'){
                $(tr).insertBefore($(tr).prev()); 
            }else if(type == 'up_top'){
                $(tr).insertBefore($(tr).siblings(":first"));
                roll_tr($(tr).attr("data-index")); //滚动到指定行
            }
        }else if(type == 'down_tr' || type == 'down_bottom'){
            if($(tr).next().html() == null){
                layer.msg("已经是最底部了");
                return;
            }
            if(type == 'down_tr'){
                $(tr).insertAfter($(tr).next());
            }else if(type == 'down_bottom'){
                $(tr).insertAfter($(tr).siblings(":last"));
                roll_tr($(tr).attr("data-index")); //滚动到指定行
            }
        }else{
            layer.msg("type参数错误");
            return;
        }
    }

   
    //滚动到指定行
    function roll_tr(index) {
        let cellHtml = $(".layui-table-main").find("tr[data-index=" + index + "]");
        let cellTop = cellHtml.offset().top;
        $(".layui-table-main").scrollTop(cellTop - 160);
    }

});
