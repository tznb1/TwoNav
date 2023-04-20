<?php $title='更新日志'; require 'header.php'; ?>
<body>
<div class="layuimini-container">
 <div class="layuimini-main" style=" margin-left: 20px;">
    <li class="layui-timeline-item">
        <i class="layui-icon layui-timeline-axis"></i>
        <div class="layui-timeline-content layui-text">
            <h4 class="layui-timeline-title">v2.0.14-20230420</h4>
            <ul>
                <li>修复书签分享和输出上限冲突的问题</li>
                <li>[数据库更新]修复不同类型的模板目录名相同时存在窜数据的问题</li>
            </ul>
        </div>
    </li>
    <li class="layui-timeline-item">
        <i class="layui-icon layui-timeline-axis"></i>
        <div class="layui-timeline-content layui-text">
            <h4 class="layui-timeline-title">v2.0.13-20230418</h4>
            <ul>
                <li>修复链接模式不受控的问题(上个版本造成)</li>
                <li>网站管理/系统设置新增强制私有选项</li>
                <li>修复书签分享的链接可能无法访问</li>
                <li>修复扩展字段输入html代码可能造成页面渲染异常的问题</li>
            </ul>
        </div>
    </li>
    <li class="layui-timeline-item">
        <i class="layui-icon layui-timeline-axis"></i>
        <div class="layui-timeline-content layui-text">
            <h4 class="layui-timeline-title">v2.0.12-20230417</h4>
            <ul>
                <li>优化书签分享的兼容性</li>
                <li>新增链接自定义扩展信息(用于自定义过渡页模板)</li>
                <li>[数据库更新] 调整MySQL字符集编码(utf8改为utf8mb4,使其兼容Emoji字符)</li>
                <li>[数据库更新] 用户组权限列表新增3个选项</li>
                <li>调整用户无权限配置站点信息时隐藏入口</li>
                <li>修复已知问题</li>
            </ul>
        </div>
    </li>
    <li class="layui-timeline-item">
        <i class="layui-icon layui-timeline-axis"></i>
        <div class="layui-timeline-content layui-text">
            <h4 class="layui-timeline-title">v2.0.11-20230414</h4>
            <ul>
                <li>修复热门网址/最新网址的一些问题</li>
                <li>新增Atool工具 (应急工具),用于强行修改密码/配置等 <a href="https://gitee.com/tznb/TwoNav/wikis/pages?sort_id=7993451&doc_id=3767990" target="_blank">使用说明</a></li>
                <li>调整安装脚本session_name避免特定环境冲突</li>
            </ul>
        </div>
    </li>
    <li class="layui-timeline-item">
        <i class="layui-icon layui-timeline-axis"></i>
        <div class="layui-timeline-content layui-text">
            <h4 class="layui-timeline-title">v2.0.10-20230413</h4>
            <ul>
                <li>支持删除注册管理生成的注册码</li>
                <li>站点设置增加设为默认用户按钮(储存在浏览器Cookie,不影响其他用户)</li>
                <li>站点设置增加热门链接/最新链接/输出上限</li>
                <li>主页支持传参来浏览指定分类(配合站点设置>输出上限使用)</li>
            </ul>
        </div>
    </li>
    <li class="layui-timeline-item">
        <i class="layui-icon layui-timeline-axis"></i>
        <div class="layui-timeline-content layui-text">
            <h4 class="layui-timeline-title">v2.0.09-20230410</h4>
            <ul>
                <li>优化兼容性/细节调整</li>
                <li>放宽授权验证规则</li>
                <li>修复前端调用添加链接页面没有分类的bug</li>
                <li>修复默认主题删除链接提示ID不存在的bug</li>
                <li>修复下载更新时temp目录不存在时未自动创建导致下载失败的bug</li>
                <li>调整手机端布局(减小边距/不显示描述)</li>
            </ul>
        </div>
    </li>
    <li class="layui-timeline-item">
        <i class="layui-icon layui-timeline-axis"></i>
        <div class="layui-timeline-content layui-text">
            <h4 class="layui-timeline-title">v2.0.08-20230406</h4>
            <ul>
                <li>增加一些使用说明</li>
                <li>优化兼容性(php8.0.0 getdir函数重名)</li>
                <li>调整主题模板默认主题不显示删除按钮(因为不允许删除默认模板)</li>
                <li>精简部分静态资源,Medoo框架更新到2.1.8</li>
            </ul>
        </div>
    </li>
    <li class="layui-timeline-item">
        <i class="layui-icon layui-timeline-axis"></i>
        <div class="layui-timeline-content layui-text">
            <h4 class="layui-timeline-title">v2.0.07-20230405</h4>
            <ul>
                <li>优化Extend版数据导入,支持保留部分全局配置</li>
                <li>修复下载主题失败的问题(临时目录不存在时未自动创建)</li>
                <li>优化安装脚本,调整部分默认参数</li>
                <li>内置Apache伪静态配置,生成伪静态非Nginx环境时不显示</li>
                <li>修复站点设置上传图标后点击保存造成图标不显示的bug</li>
                <li>修复全局默认设置中的链接模式于站点设置中的不一致</li>
                <li>修复分类/链接添加或编辑时未正确加载加密分组数据</li>
            </ul>
        </div>
    </li>
    <li class="layui-timeline-item">
        <i class="layui-icon layui-timeline-axis"></i>
        <div class="layui-timeline-content layui-text">
            <h4 class="layui-timeline-title">v2.0.06-20230403-Alpha</h4>
            <ul>
                <li>数据库更新:MySQL用户表专属登录接口字段长度改为16</li>
                <li>站长工具新增导入数据 ( 用于导入 OneNav Extend 用户数据 ) <a href="https://gitee.com/tznb/OneNav/wikis/pages?sort_id=7955135&doc_id=2439895" target="_blank">使用说明</a></li>
                <li>安全设置中(点击右上角账号)新增登录后选项,可选登录成功后进入(主页/后台/自动),选为自动时若移动设备登录则进入主页,反正进入后台</li>
                <li>用户管理>设用户组>允许将账号分组设为站长,强制改密码,修改邮箱(点击邮箱)</li>
                <li>注册账号后端验证邮箱是否合规</li>
            </ul>
        </div>
    </li>
    <li class="layui-timeline-item">
        <i class="layui-icon layui-timeline-axis"></i>
        <div class="layui-timeline-content layui-text">
            <h4 class="layui-timeline-title">v2.0.05-20230330-Alpha</h4>
            <ul>
                <li>稳定性优化,移除部分调试数据</li>
                <li>站长工具新增清理缓存</li>
                <li>调整文件上传方案</li>
                <li>支持删除链接时如果已上传图标则一并删除</li>
                <li>一键诊断内容新增PHP配置信息,扩展正常时不显示</li>
            </ul>
        </div>
    </li>
    <li class="layui-timeline-item">
        <i class="layui-icon layui-timeline-axis"></i>
        <div class="layui-timeline-content layui-text">
            <h4 class="layui-timeline-title">v2.0.04-20230326-Alpha</h4>
            <ul>
                <li>调整更新逻辑,站长工具中新增数据库升级按钮,用于手动更新时升级数据库! </li>
                <li>API模式支持兼容模式,用于适配OneNav插件! </li>
                <li>导出导入新增本地备份,支持同步备份用户上传的图标等数据,支持回滚/导出/删除/导入</li>
            </ul>
        </div>
    </li>
    <li class="layui-timeline-item">
        <i class="layui-icon layui-timeline-axis"></i>
        <div class="layui-timeline-content layui-text">
            <h4 class="layui-timeline-title">v2.0.02-20230323-Alpha</h4>
            <ul>
                <li>优化db3数据导入的稳定性</li>
                <li>移除主页中的一键诊断入口</li>
                <li>网站管理新增站长工具(内含:一键诊断,phpinfo,生成伪静态,系统日志)</li>
                <li>安装脚本优化,新增安装前获取诊断信息</li>
                <li>支持链接排序</li>
            </ul>
        </div>
    </li>
    <li class="layui-timeline-item">
        <i class="layui-icon layui-timeline-axis"></i>
        <div class="layui-timeline-content layui-text">
            <h4 class="layui-timeline-title">v2.0.01-20230318-Alpha</h4>
            <ul>
                <li>修复前端退出登录(部分主题支持)异常的问题</li>
                <li>适配前端调用编辑链接(部分主题支持)</li>
                <li>新增更新日志(管理员后台点击当前版本号进入)</li>
                <li>恢复一键添加功能 (位于导出导入页面)</li>
                <li>修正登录模板被删除时候报错(应该使用默认模板)</li>
                <li>修正复制链接非根目录运行时URL错误</li>
            </ul>
        </div>
    </li>
    <li class="layui-timeline-item">
        <i class="layui-icon layui-timeline-axis"></i>
        <div class="layui-timeline-content layui-text">
            <h4 class="layui-timeline-title">v2.0.00-2023.03.15-Alpha</h4>
            <ul>
                <li>内测版首发</li>
            </ul>
        </div>
    </li>
 </div>
</div>
</body>
</html>

