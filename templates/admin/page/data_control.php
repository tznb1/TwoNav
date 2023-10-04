<?php $title='导出导入';require 'header.php';?>
  <style>    
.layui-body {
    left: 138px;
    padding: 15px;
    bottom: auto;
}

#up_html{
    margin-left:auto;
    margin-right:auto;
    width: 100%;
    -moz-box-sizing: border-box; /*Firefox3.5+*/
	-webkit-box-sizing: border-box; /*Safari3.2+*/
	-o-box-sizing: border-box; /*Opera9.6*/
	-ms-box-sizing: border-box; /*IE8*/
	box-sizing: border-box; 
    left:0%;
}
.layui-form-checked span, .layui-form-checked:hover span{background-color: #ff2e2e;}
.layui-form-checked i, .layui-form-checked:hover i{color: #f70000;}
  </style>
<body>
<div class="layuimini-container">
  <div class="layuimini-main layui-row content-body">
    <div class="layui-col-lg8 layui-col-md-offset2">
        <!--书签导入-->
        <fieldset class="layui-elem-field layui-field-title"><legend>书签导入</legend></fieldset> 
        <form class="layui-form layui-form-pane">
            <div class="layui-upload-drag" id="up_html">
                <i class="layui-icon layui-icon-upload"></i><p>点击上传，或将书签拖拽到此处</p>
                <input class="layui-input layui-hide" type="text" id="suffix" name="suffix" autocomplete="off"> 
            </div>
            <div class="layui-progress" lay-filter="progress" id="progress" style="display:none;">
                <div class="layui-progress-bar"></div>
            </div>
            <blockquote class="layui-elem-quote" style="margin-top: 10px;border-left: 5px solid #FF5722; color: #FF5722;" id='guide'>第一步:请上传数据,支持: db3 / html 格式(最大10M),使用前请参考<a href="https://gitee.com/tznb/TwoNav/wikis/pages?sort_id=7968661&doc_id=3767990" target="_blank" rel = "nofollow">帮助文档</a></blockquote>
            
            <div class="layui-form-item" style="display:none;" id='fid'>
                <label class="layui-form-label">所属分类</label>
                <div class="layui-input-block">
                    <select name="fid" lay-verify="" lay-search>
                        <option value=""></option>
                        <?php echo_category(true); ?>
                    </select>
                </div>
            </div>
            
            <div class="layui-form-item" style="display:none;" id='AutoClass'>
                <label class="layui-form-label">自动分类</label>
                <div class="layui-input-inline" style="width: 70px;">
                    <input lay-filter="AutoClass" type="checkbox" name="AutoClass" value = "1" lay-skin="switch" lay-text="是|否">
                </div>
                <div class="layui-form-mid layui-word-aux">自动创建分类目录</div>
            </div>
 
            <div class="layui-form-item" style="display:none;" id='2Class'>
                <label class="layui-form-label">二级分类</label>
                <div class="layui-input-inline" style="width: 70px;">
                    <input lay-filter="2Class" type="checkbox" name="2Class" value = "1" lay-skin="switch" lay-text="是|否">
                </div>
                <div class="layui-form-mid layui-word-aux">尝试保留分类层级,无法保留时添加为一级分类</div>
            </div> 
            
            <div class="layui-form-item" style="display:none;" id='ADD_DATE'>
                <label class="layui-form-label">保留时间</label>
                <div class="layui-input-inline" style="width: 70px;">
                    <input type="checkbox" name="ADD_DATE" value = "1" lay-skin="switch" lay-text="是|否">
                </div>
                <div class="layui-form-mid layui-word-aux">尝试保留浏览器书签的添加时间</div>
            </div>
            <div class="layui-form-item" style="display:none;" id='icon'>
                <label class="layui-form-label">提取图标</label>
                <div class="layui-input-inline" style="width: 70px;">
                    <input type="checkbox" name="icon" value = "1" lay-skin="switch" lay-text="是|否">
                </div>
                <div class="layui-form-mid layui-word-aux">尝试提取浏览器书签的图标(大小上限:2kb)</div>
            </div>
            <div class="layui-form-item" style="display:none;" id='property'>
                <label class="layui-form-label">是否私有</label>
                <div class="layui-input-inline" style="width: 70px;">
                    <input  type="checkbox" name="property" value = "1" lay-skin="switch" lay-text="是|否">
                </div>
                <div class="layui-form-mid layui-word-aux" id="propertytxt">导入的链接将设为私有</div>
            </div>
            
            <div class="layui-form-item" style="display:none;" id='source'>
                <label class="layui-form-label">数据来源</label>
                <div class="layui-input-inline" style="width: 168px;">
                    <select name="source">
                        <option value="">请选择</option>
                        <option value="1">OneNav</option>
                        <option value="2">OneNav Extend</option>
                        <option value="3">TwoNav</option>
                    </select>
                </div>
                <div class="layui-form-mid layui-word-aux">请选择您的数据来源</div>
            </div> 

            
            <div class="layui-form-item" style="display:none;" id='attr'>
                <label class="layui-form-label">保留属性</label>
                <div class="layui-input-inline" style="width: 70px;">
                    <input type="checkbox" name="attr" value = "1" lay-skin="switch" lay-text="是|否">
                </div>
                <div class="layui-form-mid layui-word-aux">将保留添加时间,修改时间,点击数</div>
            </div> 
            
            <div class="layui-form-item" style="display:none;" id='imp_link'>
                <button class="layui-btn layui-btn-disabled" lay-submit lay-filter="imp_link">开始导入</button>
            </div>
        </form>

<?php if(!preg_match('/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i',$_SERVER['HTTP_USER_AGENT'])){ ?>
        <fieldset class="layui-elem-field layui-field-title" style="margin-top: 30px;"><legend>一键添加</legend></fieldset> 
        <blockquote class="layui-elem-quote" style="margin-top: 10px;border-left: 5px solid #5FB878; color: #333;">1.按需选择参数 > 将下方蓝色的「一键添加」拖拽到浏览器书签栏(收藏夹)<br />2.点击书签栏中的一键添加,即可将正在浏览的页面快速添加到TwoNav</blockquote>
        <form class="layui-form layui-form-pane" id="one">
            <div class="layui-form-item">
                <label class="layui-form-label">默认分类</label>
                <div class="layui-input-inline">
                    <select class="ga" name="Default_category" lay-filter="Default_category">
                        <option value=""></option>
                        <?php echo_category(true); ?>
                    </select>
                </div>
                <div class="layui-form-mid layui-word-aux">默认选择的分类</div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">默认私有</label>
                <div class="layui-input-inline">
                    <select class="ga" name="property" lay-filter="property">
                        <option value="0">否</option>
                        <option value="1">是</option>
                    </select>
                </div>
                <div class="layui-form-mid layui-word-aux">是否默认私有</div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">自动添加</label>
                <div class="layui-input-inline">
                    <select class="ga" name="Auto_add" lay-filter="Auto_add">
                        <option value="0">手动</option>
                        <option value="1">自动</option>
                    </select>
                </div>
                <div class="layui-form-mid layui-word-aux">自动点击添加(需选择默认分类)</div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">自动关闭</label>
                <div class="layui-input-inline">
                    <select class="ga" name="Auto_Off" lay-filter="Auto_Off">
                        <option value="0">手动关闭</option>
                        <option value="3000">延迟3秒</option>
                        <option value="2000">延迟2秒</option>
                        <option value="1000">延迟1秒</option>
                        <option value="500">延迟0.5秒</option>
                        <option value="1">立即关闭</option>
                    </select>
                </div>
                <div class="layui-form-mid layui-word-aux">添加成功后是否自动关闭</div>
            </div>
            <div class="layui-form-item">
                <button class="layui-btn layui-btn-primary layui-border-black" type="button" id="view_script">脚本预览</button>
                <a class="layui-btn layui-btn-primary layui-border-blue" style="cursor:pointer;color: #1e9fff;" title="将此链接拖拽到书签栏" id="one-click-add" href=''>一键添加</a>
            </div>
        </form>
<?php }?> 
        <!--书签导出-->
        <fieldset class="layui-elem-field layui-field-title" style="margin-top: 30px;"><legend>书签导出</legend></fieldset> 
        <blockquote class="layui-elem-quote" style="margin-top: 30px;">
            <a target="_blank" style="cursor:pointer;"  rel="nofollow" data="db3" class="export_data">导出SQLite ( 可导入: OneNav Extend / TwoNav ) &nbsp;>>&nbsp; 仅包含分类和链接!</a>
        </blockquote>
        <blockquote class="layui-elem-quote" style="margin-top: 10px;">
            <a target="_blank" style="cursor:pointer;"  rel="nofollow" data="html" class="export_data">导出HTML ( 可导入: 浏览器 / OneNav / OneNav Extend / TwoNav ) </a>
        </blockquote>
        
        <fieldset class="layui-elem-field layui-field-title" style="margin-top: 30px;"><legend>本地备份 (订阅可用)</legend></fieldset> 
        <blockquote class="layui-elem-quote" style="margin-top: 10px;border-left: 2px solid #FF5722; color: #FF5722;">1.备份数据库仅保存最近20份数据<br />2.该功能仅辅助备份使用，无法确保100%数据安全，因此定期对整个站点打包备份仍然是必要的<br />3.不支持将新版本备份回滚到旧版本中,不建议跨数据库类型回滚</blockquote>
        <!-- 数据表格 -->
        <table class="layui-hide" id="list" lay-filter="list"></table>
        <!--本地备份备注输入-->
        <ul class="backup" style = "margin-top:18px;display:none;padding-right: 10px;" >
            <form class="layui-form" lay-filter="backup">
                <div class="layui-form-item">
                    <label class="layui-form-label">备注</label>
                    <div class="layui-input-block">
                        <input type="text" name="desc" placeholder="您可以对本次备份的描述,也可以留空"  value=""  class="layui-input">
                    </div>
                </div>
                <div class="layui-form-item">
                    <div class="layui-input-block">
                        <button class="layui-btn" lay-submit lay-filter="local_backup">开始备份</button>
                    </div>
                </div>
            </form>
        </ul>
        <!-- 行操作 -->
        <script type="text/html" id="tooloption">
            <div class="layui-btn-group">
                <a class="layui-btn layui-btn-xs" lay-event="restore">回滚</a>
                <a class="layui-btn layui-btn-xs" lay-event="export">导出</a>
                <a class="layui-btn layui-btn-danger layui-btn-xs" lay-event="del">删除</a>
            </div>
        </script>  

        <!-- 头部工具栏 -->
        <script type="text/html" id="toolbarheader">
            <div class="layui-btn-container">
                <button class="layui-btn layui-btn-sm" lay-event="local_refresh">刷新</button>
                <button class="layui-btn layui-btn-sm" lay-event="local_backup">备份</button>
                <button class="layui-btn layui-btn-sm" lay-event="local_upload" id="local_import">导入</button>
                <button class="layui-btn layui-btn-sm" lay-event="local_help">帮助</button>
            </div>
        </script>
        
        
        <!--数据清空-->
        <fieldset class="layui-elem-field layui-field-title" style="margin-top: 30px;"><legend>数据清空</legend></fieldset> 
        <blockquote class="layui-elem-quote" style="margin-top: 30px;border-left: 5px solid #ff5858;">
            <a  target="_blank" style="cursor:pointer;color: #ff5858;"  rel="nofollow" id="data_empty">数据清空 ( 慎用 ) &nbsp;>>&nbsp; 不可逆,请提前备份! </a>
        </blockquote>
        <ul class="data_empty" style = "margin-top:18px;display:none;padding-right: 10px;" >
            <form class="layui-form layuimini-form" lay-filter="data_empty">
                <blockquote class="layui-elem-quote" style="border-left: 0px; color: #ff2e2e;text-align: center;background-color: #ffffff;padding: 0px">
                    <p>部分数据存在关联性</p>
                    <p>单独清空某个数据可能会造成意想不到的后果</p>
                    <p>例:清除分类数据后链接数据会因丢失分类信息而无法显示<p>
                </blockquote>
                <div class="layui-form-item">
                    <div class="layui-input-block" style="margin-left: 32px;">
                        <input type="checkbox" name="TABLE[user_categorys]" title="分类" checked>
                        <input type="checkbox" name="TABLE[user_links]" title="链接" checked>
                        <input type="checkbox" name="TABLE[user_pwd_group]" title="加密" checked>
                        <input type="checkbox" name="TABLE[user_share]" title="分享" checked>
                        <input type="checkbox" name="TABLE[user_apply]" title="收录" checked>
                        <input type="checkbox" name="TABLE[user_article_list]" title="文章" checked>
                        <input type="checkbox" name="FILE[MessageBoard]" title="留言" checked>
                        <input type="checkbox" name="FILE[favicon]" title="图标" checked>
                        <input type="checkbox" name="FILE[upload]" title="上传目录(如文章图片)" checked>
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label">登录密码</label>
                    <div class="layui-input-block" style="#margin-left: 32px;">
                        <input type="password" name="pwd" required  lay-verify="required" placeholder="请输入登录密码" autocomplete="off" class="layui-input">
                        <div style="position: absolute;top:0px;right: 0px;">
                            <button class="layui-btn layui-btn-danger" lay-submit lay-filter="define_data_empty" style="display: inline-block;">确定清空</button>
                        </div>
                    </div>
                </div>
            </form>
        </ul>
        <fieldset class="layui-elem-field layui-field-title" style="margin-top: 30px;"><legend>TwoNav 感谢您的使用</legend></fieldset> 
    </div>
  </div>
</div>
<script src = '<?php echo $libs?>/jquery/jquery-3.6.0.min.js'></script>
<script src = '<?php echo $libs?>/jquery/jquery.md5.js'></script>
<script src = "./templates/admin/js/public.js?v=<?php echo $Ver;?>"></script>
<?php load_static('js.layui');?>
<script>

layui.use(['layer','element','upload','form','table'], function(){
    var layer = layui.layer,$ = layui.$,upload = layui.upload,form = layui.form,table = layui.table,element = layui.element;
    var page_sid = randomString(8);
    //导入书签(上传)
    var up_bookmark = upload.render({
        elem: '#up_html'
        ,url: get_api('write_data_control','upload')
        ,exts: 'html|db3|itabdata'
        ,accept: 'file'
        ,size: 1024 * 10
        ,data: {"page_sid":page_sid}
        ,choose: function(obj){
            obj.preview(function(index, file, result){
                $("#progress").show(); //显示进度条
                let suffix = file.name.split(".").pop().toLowerCase();
                $("#suffix").val(suffix);
                console.log(file);
                if(suffix == 'html'){
                    $("#fid").show();
                    $("#AutoClass").show();
                    $("#property").show();
                    $("#ADD_DATE").show();
                    $("#icon").show();
                    $("#attr").hide();
                }else if(suffix == 'db3'){
                    $("#fid").hide();
                    $("#AutoClass").hide();
                    $("#property").hide();
                    $("#attr").show();
                    $("#source").show();
                }else if(suffix == 'itabdata'){
                    $("#fid").hide();
                    $("#AutoClass").hide();
                    $("#property").hide();
                    $("#attr").hide();
                    $("#source").hide();
                }else{
                    $("#fid").show();
                    $("#AutoClass").show();
                    $("#property").show();
                    $("#attr").show();
                }
                $("#imp_link").show(); //显示导入书签按钮
                $("#up_html").hide(); //隐藏上传UI
                $('#guide').text('第二步:选择好您需要的选项,并点击开始导入!导入过程中请勿刷新或关闭页面!');
                up_bookmark.config.elem.next()[0].value = '';
            });
        },progress: function(n, elem, res, index){ //进度回调
            element.progress('progress', n + '%'); //更新进度条
        },done: function(res){
            if(res.code == 1){
                $("#imp_link button").removeClass("layui-btn-disabled");
            }else{
                layer.alert(res.msg || "上传异常,请刷新重试<br />若无法解决请联系技术支持",{icon:5,title:'上传失败',anim: 2,closeBtn: 0,btn: ['刷新页面']},function () {location.reload();});
            }
        },error: function(){
            layer.alert("上传异常,请刷新重试<br />若无法解决请联系技术支持",{icon:5,title:'错误',anim: 2,closeBtn: 0,btn: ['刷新页面']},function () {location.reload();});
        }
    });
    //导入书签
    form.on('submit(imp_link)', function(data){
        //未上传完毕
        if($("#imp_link button").hasClass('layui-btn-disabled')){
            return false; 
        }
        layer.msg('数据导入中,请稍后...', {offset: 'b',anim: 0,time: 60*1000});
        layer.load(1, {shade:[0.1,'#fff']});//加载层
        data.field.page_sid = page_sid;
        $.post(get_api('write_data_control',data.field.suffix),data.field,function(data,status){
            layer.closeAll();//关闭所有层
            if(data.code == 1) {
                $("#imp_link button").addClass("layui-btn-disabled");
                $("#imp_link button").text("导入成功,如需继续导入请刷新页面");
                if (data.fail > 0){
                    open_msg('800px', '600px',data.msg,data.res);
                }else{
                    layer.open({title:'导入完成',scrollbar: false,content:data.msg});
                }
            }else{
                layer.msg(data.msg, {icon: 5});
            }
        });
        console.log(data.field) 
        return false; 
    });
    
    $('#tip2').on('click', function(){
        layer.open({title:'温馨提示',scrollbar: false,content:'浏览器书签记录特性<br />1.链接标题可以为空,地址可以重复<br />2.文件夹名可空且可重复<br />3.支持无限子分类<br />本程序特性:<br />1.标题不能为空,链接不能重复<br />2.分类名不能为空且不能重复<br />3.仅支持二级分类<br />可能出现的问题:<br />1.书签所属的分类可能会错位(如2个相同文件夹的分类下的链接会合并到第一个)'});
    });
    
    //一键添加相关
    form.on('select', function(data){
        if( $(this).closest("form").attr("id") == 'one'){
            generate_script()
        }
    });
    
    function generate_script(){
        let fid = $("select[name='Default_category']").val();
        let Auto_Off = $("select[name='Auto_Off']").val();
        let Auto_add = $("select[name='Auto_add']").val();
        let property = $("select[name='property']").val();
        let prefix = window.location.href.replace(window.location.search,'');
        let script = `javascript:void(open("${prefix}?c=admin&page=add_quick_tpl&u=${u}&fid=${fid}&Auto_Off=${Auto_Off }&Auto_add=${Auto_add}&property=${property}&url=" + encodeURIComponent(location.href) + "&title=" + encodeURIComponent(document.title), "_blank", "toolbar=yes, location=yes, directories=no, status=no, menubar=yes, scrollbars=yes, resizable=no, copyhistory=yes, left=200,top=200,width=400, height=460"));`;
        $("#one-click-add").attr("href",script);
        return script;
    }
    //脚本预览
    $('#view_script').on('click', function(){
        var html = '<div style="padding: 15px; color:#01AAED;" ><h4>注:页面中的参数是跟随脚本的,您可以将不同参数的一键添加拖入浏览器书签栏,来实现快速添加到不同分类下!</h4><pre class="layui-code">' + generate_script() + '</pre></div>';
        layer.open({type: 1,maxmin: false,shadeClose: false,resize: false,title: '脚本预览',area : ( $(window).width() < 768 ? '100%' : '768px' ),content: html });
        return false; 
    });
    
    //点击提示
    $('#one-click-add').on('click', function(){
        layer.tips("按住鼠标左键将它拖拽到浏览器书签栏","#one-click-add",{tips: [3, "#009688"],time: 10*1000,anim: 6});
        return false; 
    });
    
    generate_script();
    //一键添加相关 结束
    
    //自动分类开关
    form.on('switch(AutoClass)', function(data){
        if(this.checked){
            $('#propertytxt').text('导入的链接和创建的分类将设为私有!');
            $("#2Class").show(); //显示二级分类开关
        }else{
            $('#propertytxt').text('导入的链接将设为私有!');
            $("#2Class").hide(); //隐藏二级分类开关
        }
    });
    
    //数据清空>弹窗
    $('#data_empty').on('click', function(){
        index = layer.open({type: 1,scrollbar: false,shadeClose: true,title: '数据清空',area : ['auto', '300px'],content: $('.data_empty')});
    });
    //数据清空>确定
    form.on('submit(define_data_empty)', function(data){
        if(JSON.stringify(data.field) == '{"pwd":"'+data.field.pwd+'"}'){
            layer.msg('正在清空寂寞..', {
				icon: 16,
				time: 3000,
				end: function() {
					layer.msg('您的寂寞已清空', {icon: 1});
				}
			});
            return false;
        }
        data.field.pwd = $.md5(data.field.pwd);
        $.post(get_api('write_data_control','data_empty'),data.field,function(data,status){
            if(data.code == 1) {
                layer.close(index);
                layer.msg(data.msg,{icon: 1,time: 60000});
            }else{
                layer.msg(data.msg,{icon: 5});
            }
        });
    return false; 
    });
    //导出数据
    $('.export_data').on('click', function(){
        let type = $(this).attr('data');
        console.log(type);
        index = layer.prompt({formType: 1,value: '',title: '输入登录密码:',shadeClose: false,"success":function(){
            //监听回车事件
            $("input.layui-layer-input").on('keydown',function(e){
                if(e.which == 13) {
                    export_data(type);
                }
            });
        }},function(){
            export_data(type)
        }); 
    });
    function export_data(type,name = null){
        //限制未输入密码时提交
        if($("input.layui-layer-input").val() == ''){
            return false; 
        }
        $("*").blur(); //失去焦点,避免重复回车触发
        let loading = layer.msg('数据处理中,请稍后..', {icon: 16,time: 1000*300,shadeClose: false});
        let api_create = name == null ? get_api('read_data_control','create') : get_api('other_local_backup','create');
        let api_download = name == null ? get_api('read_data_control',type) : get_api('other_local_backup','download');
        
        $.post(api_create,{'pwd':$.md5($("input.layui-layer-input").val()),'type':type,'name':name},function(data,status){
            layer.close(loading); //关闭加载提示
            $("input.layui-layer-input").val(""); //清空输入的密码
            if(data.code == 1) { //如果导出成功
                layer.close(index); //关闭密码输入框
                let url = api_download + '&key=' + data.key; //生成URL
                window.location.href = url; //下载
                let tip = layer.alert("请妥善保存您的数据!<br />请勿使用迅雷等第三方工具下载",{icon:1,title:'导出成功',offset:'b',anim: 2,shadeClose: true,closeBtn: 0,btn: ['知道了']});
            }else{
                $("input.layui-layer-input").focus();
                layer.msg(data.msg,{icon: 5});
            }
        });
        return false; 
    }
    
    function open_msg(x,y,t,c){
        layer.open({type: 1,scrollbar: false,title: t,area: [x, y],maxmin: true,shadeClose: true,content: c,btn: ['我知道了'] });
    }
    //初始化表格
    table.render({
        elem: '#list'
        ,id: 'list'
        ,url: get_api('other_local_backup','list') 
        ,toolbar: '#toolbarheader'
        ,defaultToolbar: false
        ,response: {statusCode: 1 } 
        ,cols: [[
            {width:60, type:'numbers', title: '序号'}
            ,{field:'name', title:'数据库文件名 / 备注',minWidth:400,templet:function(d){
                if(d.desc != '' && d.desc != null){
                    return d.name + '&emsp;&emsp;[&nbsp;' + d.desc+'&nbsp;]';
                }else{
                    return d.name;
                }
            }}
            ,{ width:180, title: '备份时间',templet:function(d){
                return timestampToTime(d.backup_time);
            }}  
            ,{field:'size', width:100, title: '大小',templet:function(d){return bytesToSize(d.db_size + d.tar_size);}}
            ,{width:70, title: '分类',templet:function(d){return d.user_categorys.count;}}
            ,{width:80, title: '链接',templet:function(d){return d.user_links.count;}}
            ,{width:160, title:'操作', toolbar: '#tooloption'}
        ]]
        ,done: function (res, curr, count) {
            elem_local_import();
        }
    });
    
    //表头工具
    table.on('toolbar(list)', function(obj){
        var checkStatus = table.checkStatus(obj.config.id);
        if(obj.event == 'local_backup'){
            layer.open({
                type: 1,
                shadeClose: true,
                title: '数据备份',
                area : $(document.body).width() <= 768 ? ['100%' , '100%'] : ['568px' , '250px'] ,
                content: $('.backup')
            });
            return false; 
        }else if(obj.event == 'local_refresh'){
            table.reload('list');
        }else if(obj.event == 'local_help'){
            window.open('https://gitee.com/tznb/TwoNav/wikis/pages?sort_id=7968661&doc_id=3767990');
        }
    });
    //行事件
    table.on('tool(list)', function(obj){ 
        let data = obj.data; //获得当前行数据
        //回滚
        if(obj.event === 'restore'){ 
            layer.confirm('确定回滚吗？', {icon:3,title:'温馨提示'},function(index){
                $("*").blur();
                let loading = layer.msg('数据处理中,请稍后..', {icon: 16,time: 1000*300,shadeClose: false});
                $.post(get_api('other_local_backup','restore'),{'name':data.name},function(data,status){
                    layer.close(loading);
                    if(data.code == 1) { 
                        layer.msg('回滚成功',{icon:1});
                    }else{
                        layer.msg(data.msg,{icon: 5});
                    }
                });
            });
        //删除
        }else if(obj.event === 'del'){ 
            layer.confirm('确定删除吗？', {icon:3,title:'温馨提示'},function(index){
                $.post(get_api('other_local_backup','del'),{'name':data.name},function(data,status){
                    if(data.code == 1) { 
                        layer.msg('删除成功',{icon:1});
                        obj.del(); //删除行
                        //table.reload('list'); //刷新表格
                    }else{
                        layer.msg(data.msg,{icon: 5});
                    }
                });
            });
        //导出
        }else if(obj.event === 'export'){
            index = layer.prompt({formType: 1,value: '',title: '输入登录密码:',shadeClose: false,"success":function(){
                //监听回车事件
                $("input.layui-layer-input").on('keydown',function(e){
                    if(e.which == 13) {
                        export_data('local_backup',data.name);
                    }
                });
            }},function(){
                export_data('local_backup',data.name);
            }); 
        }
    });
    
    //立即备份
    form.on('submit(local_backup)', function(data){
        $("*").blur(); 
        let loading = layer.msg('数据处理中,请稍后..', {icon: 16,time: 1000*300,shadeClose: false});
        $.post(get_api('other_local_backup','backup'),{'desc':data.field.desc},function(data,status){
            layer.close(loading);
            if(data.code == 1) { 
                layer.closeAll('page');
                layer.msg('备份成功！',{icon:1});
                table.reload('list');
            }else{
                layer.msg(data.msg,{icon: 5});
            }
        });
        return false; 
    });
    
    //绑定上传按钮
    function elem_local_import() {
        upload.render({
            elem: '#local_import'
            ,url: get_api('other_local_backup','local_import')
            ,size: 50*1024
            ,accept: 'file'
            ,acceptMime: '.tar,.gz'
            ,done: function(res){
                if(res.code == 1){
                    table.reload('list');
                    layer.msg(res.msg,{icon:1});
                }else{
                    layer.msg(res.msg,{icon:5});
                }
                
            }
            ,error: function(index, upload){
                layer.msg('导入失败',{icon: 5});
            }
        });
    }
  
  
});

function bytesToSize(bytes) {
    let k = 1024,
        sizes = ['B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'],
        i = Math.floor(Math.log(bytes) / Math.log(k));
   return (bytes / Math.pow(k, i)).toPrecision(3) + ' ' + sizes[i];
}
</script>
</body>
</html>