<?php 
$title='概况'; 
$awesome=true;

//读取缓存数据
$Notice = get_db('global_config','v',['k'=>'notice']);
$data = empty($Notice)?[]:json_decode($Notice, true);

//输出最新动态
function echo_notice_link($data){
    if(empty($data["notice"])){
        return;
    }
    echo '<div class="layui-card"><div class="layui-card-header"><i class="fa fa-bullhorn icon"></i>最新动态</div><div class="layui-card-body layui-text" id="notice_link">';
    foreach($data["notice"] as $value){
        echo "<div class=\"layuimini-notice\"><div class=\"layuimini-notice-title\"><a href=\"{$value['url']}\" target=\"_blank\">{$value['title']}</a></div></div>";
    }
    echo '</div></div>';
}

//输出官方公告
function echo_notice_text($data){
    if(empty($data["message"])){
        return;
    }
    echo '<div class="layui-card"><div class="layui-card-header"><i class="fa fa-bell-o icon"></i>官方公告</div><div class="layui-card-body layui-text layadmin-text" id="notice_text">';
    echo $data['message'];
    echo '</div></div>';
}

//专属地址优先使用二级域名
if( $global_config['Sub_domain'] == 1 && check_purview('Sub_domain',1)){
    $host = explode('.',$_SERVER["HTTP_HOST"]);
    $count = count($host);
    if($count >= 2){
        $_h = "//{$u}.{$host[$count-2]}.{$host[$count-1]}";
        $_l = "{$_h}/?c={$USER_DB['Login']}";
    }
}
if(!isset($_h)){
    $_h = static_link ? get_surl('{UUID}.html'):"./?u={$u}";
    $_l = static_link ? get_surl("login-{UUID}-{$USER_DB['Login']}.html"):"./c={$USER_DB['Login']}&u={$u}" ;
}
require 'header.php'; 
?>
<style>
    .layui-card {border:1px solid #f2f2f2;border-radius:5px;}
    .icon {margin-right:10px;color:#1aa094;}
    .layuimini-qiuck-module {text-align:center;margin-top: 10px}
    .layuimini-qiuck-module a i {display:inline-block;width:100%;height:60px;line-height:60px;text-align:center;border-radius:2px;font-size:30px;background-color:#F8F8F8;color:#333;transition:all .3s;-webkit-transition:all .3s;}
    .layuimini-qiuck-module a cite {position:relative;top:2px;display:block;color:#666;text-overflow:ellipsis;overflow:hidden;white-space:nowrap;font-size:14px;}
    .welcome-module {width:100%;height:210px;}
    .panel {background-color:#fff;border:1px solid transparent;border-radius:3px;-webkit-box-shadow:0 1px 1px rgba(0,0,0,.05);box-shadow:0 1px 1px rgba(0,0,0,.05)}
    .panel-body {padding:10px}
    .panel-title {margin-top:0;margin-bottom:0;font-size:12px;color:inherit}
    .label {display:inline;padding:.2em .6em .3em;font-size:75%;font-weight:700;line-height:1;color:#fff;text-align:center;white-space:nowrap;vertical-align:baseline;border-radius:.25em;margin-top: .3em;}
    .layui-red {color:red}
    .main_btn > p {height:40px;}
    .layui-bg-number {background-color:#F8F8F8;}
    .layuimini-notice:hover {background:#f6f6f6;}
    .layuimini-notice {right: 0px;padding:7px 16px;clear:both;font-size:12px !important;cursor:pointer;position:relative;transition:background 0.2s ease-in-out;}
    .layuimini-notice-title {line-height:28px;font-size:14px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;}
</style>
<body>
<div class="layuimini-container">
    <div class="layuimini-main">
        <div class="layui-row layui-col-space15">
            <div class="layui-col-md8">
                <div class="layui-row layui-col-space15">
                    <div class="layui-col-md6">
                        <div class="layui-card">
                            <div class="layui-card-header"><i class="fa fa-warning icon"></i>数据统计</div>
                            <div class="layui-card-body">
                                <div class="welcome-module">
                                    <div class="layui-row layui-col-space10">
                                        <div class="layui-col-xs6">
                                            <div class="panel layui-bg-number">
                                                <div class="panel-body">
                                                    <div class="panel-title">
                                                        <span class="label pull-right layui-bg-blue">实时</span>
                                                        <h5>分类数量</h5>
                                                    </div>
                                                    <div class="panel-content">
                                                        <h1 class="no-margins"><?php echo $category_count;?></h1>
                                                        <small>当前分类总数</small>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="layui-col-xs6">
                                            <div class="panel layui-bg-number">
                                                <div class="panel-body">
                                                    <div class="panel-title">
                                                        <span class="label pull-right layui-bg-cyan">实时</span>
                                                        <h5>链接数量</h5>
                                                    </div>
                                                    <div class="panel-content">
                                                        <h1 class="no-margins"><?php echo $link_count;?></h1>
                                                        <small>当前链接总量</small>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="layui-col-xs6">
                                            <div class="panel layui-bg-number">
                                                <div class="panel-body">
                                                    <div class="panel-title">
                                                        <span class="label pull-right layui-bg-orange">实时</span>
                                                        <h5>访问统计</h5>
                                                    </div>
                                                    <div class="panel-content">
                                                        <h1 class="no-margins"><?php echo $index_count;?></h1>
                                                        <small>本月主页访问量</small>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="layui-col-xs6">
                                            <div class="panel layui-bg-number">
                                                <div class="panel-body">
                                                    <div class="panel-title">
                                                        <span class="label pull-right layui-bg-green">实时</span>
                                                        <h5>点击统计</h5>
                                                    </div>
                                                    <div class="panel-content">
                                                        <h1 class="no-margins"><?php echo $click_count;?></h1>
                                                        <small>本月链接点击量</small>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="layui-col-md6">
                        <div class="layui-card">
                            <div class="layui-card-header"><i class="fa fa-star icon"></i>快捷入口</div>
                            <div class="layui-card-body">
                                <div class="welcome-module">
                                    <div class="layui-row layui-col-space10 layuimini-qiuck">
                                        <div class="layui-col-xs3 layuimini-qiuck-module">
                                            <a href="javascript:;" layuimini-content-href="link_list" data-title="链接列表" data-icon="fa fa-link">
                                                <i class="fa fa-link"></i>
                                                <cite>链接列表</cite>
                                            </a>
                                        </div>
                                        <div class="layui-col-xs3 layuimini-qiuck-module">
                                            <a href="javascript:;" layuimini-content-href="link_add" data-title="添加链接" data-icon="fa fa-plus-square-o">
                                                <i class="fa fa-plus-square-o"></i>
                                                <cite>添加链接</cite>
                                            </a>
                                        </div>
                                        <div class="layui-col-xs3 layuimini-qiuck-module">
                                            <a href="javascript:;" layuimini-content-href="SiteSetting" data-title="站点设置" data-icon="fa fa-cog">
                                                <i class="fa fa-cog"></i>
                                                <cite>站点设置</cite>
                                            </a>
                                        </div>
<?php if(check_purview('theme_in',1)){ ?> 
                                        <div class="layui-col-xs3 layuimini-qiuck-module">
                                            <a href="javascript:;" layuimini-content-href="theme" data-title="主题设置" data-icon="fa fa-magic">
                                                <i class="fa fa-magic"></i>
                                                <cite>主题设置</cite>
                                            </a>
                                        </div>
<?php }?> 
                                        <div class="layui-col-xs3 layuimini-qiuck-module">
                                            <a href="javascript:;" layuimini-content-href="category_list" data-title="分类管理" data-icon="fa fa-list-ul">
                                                <i class="fa fa-list-ul"></i>
                                                <cite>分类管理</cite>
                                            </a>
                                        </div>
                                        <div class="layui-col-xs3 layuimini-qiuck-module">
                                            <a href="https://gitee.com/tznb/TwoNav/wikis/pages" target="_blank">
                                                <i class="fa fa-book"></i>
                                                <cite>使用说明</cite>
                                            </a>
                                        </div>
                                        <div class="layui-col-xs3 layuimini-qiuck-module">
                                            <a href="https://gitee.com/tznb/TwoNav" target="_blank">
                                                <i class="fa fa-github"></i>
                                                <cite>开源地址</cite>
                                            </a>
                                        </div>
                                        <div class="layui-col-xs3 layuimini-qiuck-module">
                                            <a href="https://gitee.com/tznb/TwoNav/wikis/pages?sort_id=7968669&doc_id=3767990" target="_blank">
                                                <i class="fa fa-diamond"></i>
                                                <cite>购买授权</cite>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                    <div class="layui-col-md12">
                        <div class="layui-card">
                            <div class="layui-card-header">
                                <div style="display: flex; justify-content: space-between;">
                                    <div id="tongji" style="cursor: pointer;"><i class="fa fa-line-chart icon" ></i>报表统计</div>
                                    <div>
                                        <button class="layui-btn layui-btn-primary echarts" style="border: none;display:none;"><span>最近7天</span><i class="layui-icon layui-icon-down layui-font-12"></i></button>
                                    </div>
                                </div>
                            </div>
                            <div class="layui-card-body">
                                <div id="echarts-records" style="width: 100%; min-height: 500px;"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="layui-col-md4">
                
                <div class="layui-card" id="msg">
                    <div class="layui-card-header"><i class="fa fa-fire icon"></i>版本信息</div>
                    <div class="layui-card-body layui-text">
                        <table class="layui-table">
                            <colgroup>
                                <col width="100">
                                <col>
                            </colgroup>
                            <tbody>
                            <tr>
                                <td>程序名称</td>
                                <td>TwoNav</td>
                            </tr>
                            <tr>
                                <td>当前版本</td>
                                <td><span id="ver"><?php echo SysVer;?></span></td>
                            </tr>
<?php if($USER_DB['UserGroup'] == 'root'){ ?>
                            <tr>
                                <td>最新版本</td>
                                <td id="new_ver"><a target="_blank" href="https://gitee.com/tznb/TwoNav/releases"><?php echo $data['version'] ?? SysVer; ?></a> </td>
                            </tr>
                            <tr>
                                <td>授权状态</td>
                                <td><?php echo is_subscribe("text");?></td>
                            </tr>
                            <tr>
<?php }?>
                            <tr>
                                <td>用户交流</td>
                                <td><a target="_blank" href="https://qm.qq.com/cgi-bin/qm/qr?k=LaIzFK2hfTYBZGR0cKvW3xZL6aNgcSXH&jump_from=webapi&authKey=LHh1NtAiGdK0wNyoZiHWrzAZTWWq26YgAwX0Ak7rBWchh6Y5ocUX/0cCXLMXvq/k" title="TwoNav - 技术交流">QQ群：695720839</a>
                               </td>
                            </tr>
                            <tr>
                                <td>技术支持</td>
                                <td><a target="_blank" href="tencent://message/?uin=271152681">QQ：271152681</a></td>
                            </tr>
                            <tr>
                                <td>专属地址</td>
                                <td>
                                    <a href="<?php echo $_h;?>" target="_blank">我的主页</a>
                                    &nbsp;
                                    <a href="<?php echo $_l;?>" target="_blank">TwoNav - 登录</a>
                                    &nbsp;
                                    <i class="fa fa-arrow-left layui-hide-xs" style="color: #ff5722;">&nbsp;<span style="color: #ff5722;" title="收藏专属入口可避免无法登录后台的情况">建议收藏</span></i>
                                </td>
                            <tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <?php echo_notice_link($data); //最新动态 ?>
                <?php echo_notice_text($data); //官方公告 ?>
            </div>
        </div>
    </div>
</div>
<?php 
load_static('js'); 
if($USER_DB['UserGroup'] == 'root'){
    echo '<script src="./templates/admin/js/home-root.js?v='.$Ver.'" charset="utf-8"></script>';
}
?>
<script>
    layui.extend({
        echarts: '{/}<?php echo $libs?>/Layui/extend/echarts',
        echartsTheme: '{/}<?php echo $libs?>/Layui/extend/echartsTheme'
    });
    layui.use(['layer', 'miniTab','echarts'], function () {
        var $ = layui.jquery,
            layer = layui.layer,
            miniTab = layui.miniTab,
            echarts = layui.echarts,
            dropdown = layui.dropdown;
        miniTab.listen();

        
        //报表统计下拉初始化
        var home_echarts = localStorage.getItem(u + "_home_echarts") || 7 ;
        $('.echarts').find('span').text(`最近${home_echarts}天`);
        $('.echarts').show();
        dropdown.render({
          elem: '.echarts',
          data: [{
            title: '最近7天',
            value: 7
          },{
            title: '最近14天',
            value: 14
          },{
            title: '最近30天',
            value: 30
          }],
          click: function(obj){
              this.elem.find('span').text(obj.title);
              localStorage.setItem(u + "_home_echarts",obj.value);
              home_echarts = obj.value;
              load_echarts();
          }
        });
        
        $('#tongji').on('click', function(){
            $.post('./index.php?c=api&method=read_data&date='+home_echarts+'&type=tongji_ip_list&u='+u,function(data,status){
                if(data.code == 1){
                    var content = '<table class="layui-table" border="1"><thead><tr><th>日期</th><th>IP列表</th></tr></thead><tbody>';
                    $.each(data.data, function (date, ipAddresses) {
                        content += '<tr><td>' + date + '</td><td>';
                        ipAddresses.sort((ip1, ip2) => ip1.localeCompare(ip2, undefined, { numeric: true })); //IP排序
                        $.each(ipAddresses, function (index, ipAddress) {
                            content += ipAddress + '<br>';
                        });
                        content += '</td></tr>';
                    });
                    content += '</tbody></table>';
                    layer.open({
                        title: '访问IP列表',
                        content: content,
                        area: ['100%', '100%']
                    });
                }
            });
        });
        //加载报表统计
        function load_echarts(){
            var echartsRecords = echarts.init(document.getElementById('echarts-records'), 'walden');
            $.post('./index.php?c=api&method=read_data&date='+home_echarts+'&type=echarts&u='+u,function(data,status){
                if(data.code == 1){
                    var optionRecords = {
                        tooltip: {trigger: 'axis'},
                        legend: {data:['访问量','点击量','IP数']},
                        grid: {left: '3%',right: '4%',bottom: '3%',containLabel: true},
                        xAxis: {
                            type: 'category',
                            boundaryGap: false,
                            data: data.data.dates
                        },
                        yAxis: {},
                        series: data.data.day_data
                    };
                    echartsRecords.setOption(optionRecords);
                    window.onresize = function(){echartsRecords.resize();} // echarts 窗口缩放自适应
                    return;
                }
                layer.alert("获取统计数据失败..",{icon:5,title:'错误',anim: 2,closeBtn: 0,btn: ['刷新页面']},function () {location.reload();});
            });
        }
        load_echarts();
        
        //定时刷新
        setInterval(function() {
            if($("#layuiminiHomeTabId",parent.document).attr('class') == 'layui-this' && document.visibilityState == 'visible'){
                $.post('./index.php?c=api&method=read_data&type=home&u='+u,function(data,status) {
		            if(data.code == 1) {
		                for (let i=0; i<data.data.length; i++){
		                    $(".no-margins").eq(i).text(data.data[i]);
                        }
		            }
	            });
            }
        },2000);
        
    });
</script>
</body>
</html>
