<?php 
if($USER_DB['UserGroup'] != 'root'){$content='您没有权限访问此页面'; require(DIR.'/templates/admin/page/404.php');exit;}
$title='系统设置';require(dirname(__DIR__).'/header.php');
?>
<body>
<div class="layuimini-container">
    <div class="layuimini-main">
    <form class="layui-form" lay-filter="form">
        <div class="layui-form layuimini-form layui-form-pane">
            <blockquote class="layui-elem-quote layui-text" style="">
                1.此功能<a href="https://gitee.com/tznb/OneNav/wikis/%E8%AE%A2%E9%98%85%E6%9C%8D%E5%8A%A1%E6%8C%87%E5%BC%95" target="_blank">授权用户</a>专享,请仔细阅读本页说明<br />
                2.缓存时间视自身需求而定,希望及时更新则短一点(实际上站点很少会更新图标),建议值: 604800 (7天)<br />
                3.修改缓存时间可能不会立即生效,因为浏览器已经缓存的图标会等过期后再刷新 (可以清理浏览器缓存来强制刷新)<br />
                4.用户需在站点设置>链接图标>选择本地服务或本地服务(伪静态),后者需要从站长工具生成伪静态并正确配置<br />
                5.站点处于维护模式/离线模式或下方全局开关处于关闭时调用此接口则返回默认图标<br />
                6.当显示默认图标会忽略下方浏览器缓存时间的设置,时间将被设为60秒<br />
                7.受限于网络的复杂性无法百分百获取成功,当获取失败时会显示默认图标
            </blockquote>
            
            <div class="layui-form-item">
                <label class="layui-form-label">全局开关</label>
                <div class="layui-input-inline" >
                    <select name="o_switch">
                        <option value="0" selected="">关闭</option>
                        <option value="1" >开启</option>
                    </select>
                </div>
                <div class="layui-form-mid layui-word-aux">关闭时请求本地图标将得到默认图标</div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">防盗链</label>
                <div class="layui-input-inline" >
                    <select name="referer_test">
                        <option value="0" >关闭</option>
                        <option value="1" selected="">开启</option>
                    </select>
                </div>
                <div class="layui-form-mid layui-word-aux">Referer防盗链,即来路检测</div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">服务器缓存</label>
                <div class="layui-input-inline">
                    <input type="number" name="server_cache_time" lay-verify="required" placeholder='服务器缓存时间,单位:秒' value="604800" autocomplete="off" class="layui-input">
                </div>
                <div class="layui-form-mid layui-word-aux">单位:秒,可节省服务器资源,值为0表示禁止缓存</div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">浏览器缓存</label>
                <div class="layui-input-inline">
                    <input type="number" name="browse_cache_time" lay-verify="required" placeholder='浏览器缓存时间,单位:秒' value="604800" autocomplete="off" class="layui-input">
                </div>
                <div class="layui-form-mid layui-word-aux">单位:秒,可节省服务器资源,值为0表示禁止缓存</div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">解析超时</label>
                <div class="layui-input-inline">
                    <input type="number" name="analysis_timeout" lay-verify="required" placeholder='解析超时,单位:秒' value="6" autocomplete="off" class="layui-input">
                </div>
                <div class="layui-form-mid layui-word-aux">单位:秒,范围:3 - 20,默认6秒</div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">下载超时</label>
                <div class="layui-input-inline">
                    <input type="number" name="download_timeout" lay-verify="required" placeholder='下载超时,单位:秒' value="6" autocomplete="off" class="layui-input">
                </div>
                <div class="layui-form-mid layui-word-aux">单位:秒,范围:3 - 20,默认6秒</div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">大小限制</label>
                <div class="layui-input-inline">
                    <input type="number" name="icon_size" lay-verify="required" placeholder='下载超时,单位:KB' value="256" autocomplete="off" class="layui-input">
                </div>
                <div class="layui-form-mid layui-word-aux">单位:KB,范围:5 - 1024,默认256</div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">备选接口</label>
                <div class="layui-input-inline" >
                    <select name="backup_api">
                        <option value="0" selected="">关闭</option>
                        <option value="6" >api.iowen.cn</option>
                        <option value="2" >favicon.png.pub</option>
                    </select>
                </div>
                <div class="layui-form-mid layui-word-aux">本地解析失败时尝试使用备选第三方API接口获取(由其他大佬提供)</div>
            </div>
            
            <div class="layui-form-item">
                <div class="layui-input-block">
                    <button class="layui-btn layui-btn-danger" type="button" id="clean">清除缓存</button>
                    <button class="layui-btn layui-btn-normal" lay-submit lay-filter="save">确认保存</button>
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
    
    //表单赋值
    form.val('form', <?php echo json_encode(unserialize( get_db("global_config", "v", ["k" => "icon_config"])));?>);
    //清除缓存
    $('#clean').click(function() {
        layer.confirm('确定要清除全部缓存吗?',{icon: 3, title:'温馨提示'}, function(index){
            $.post(get_api('other_root','write_icon_del_cache'),function(data,status){
                if(data.code == 1) {
                    if(data.msg!="操作成功"){
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
    });
    
    //监听提交
    form.on('submit(save)', function (data) {
        $.post(get_api('other_root','write_icon_config'),data.field,function(data,status){
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
});
</script>
</body>
</html>