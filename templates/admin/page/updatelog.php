<?php $title='更新日志'; require 'header.php'; ?>
<body>
<div class="layuimini-container">
 <div class="layuimini-main" style=" margin-left: 20px;">
    <li class="layui-timeline-item">
        <i class="layui-icon layui-timeline-axis"></i>
        <div class="layui-timeline-content layui-text">
            <h4 class="layui-timeline-title">v2.0.30-20230713</h4>
            <ul>
                <li>[修复] 登录接口的一个错误</li>
                <li>[优化] 图标配置页面新增清除缓存按钮,优化图标拉取功能的成功率</li>
                <li>[优化] 主题设置>过渡模板,当站点设置中链接模式不为过度页面时显示提示信息</li>
                <li>[新增] 后台概要页的报表统计支持选择最近7/14/30天的统计数据 (终端记忆)</li>
            </ul>
        </div>
    </li>
    <li class="layui-timeline-item">
        <i class="layui-icon layui-timeline-axis"></i>
        <div class="layui-timeline-content layui-text">
            <h4 class="layui-timeline-title">v2.0.29-20230705</h4>
            <ul>
                <li>[升级] Layui v2.8.3 升级到 v2.8.10</li>
                <li>[修复] 全新安装v2.0.22 - v2.0.28,未创建图标缓存表导致图标拉取失败的bug <a href="https://gitee.com/tznb/TwoNav/releases/tag/v2.0.22-20230523" target="_blank">手动修复说明</a></li>
                <li>[变更] 默认设置和站长工具中邮件配置/图标配置,移入系统设置中</li>
                <li>[新增] Token页面新增使用说明</li>
                <li>[安全] 优化安全性,站长工具>phpinfo使用时需输入密码核验,并移除Cookie相关信息!</li>
                <li>[安全] 系统设置中长度限制改为相关限制,并加入默认用户组禁止使用自定义代码的开关!默认为禁止!并在开启时提示站长存在安全隐患!</li>
                <li>[安全] 系统安装后默认禁止注册,如需开启请在系统设置>注册配置>设为开放注册</li>
            </ul>
        </div>
    </li>
    <li class="layui-timeline-item">
        <i class="layui-icon layui-timeline-axis"></i>
        <div class="layui-timeline-content layui-text">
            <h4 class="layui-timeline-title">v2.0.28-20230624</h4>
            <ul>
                <li>[优化] 收录管理允许用户自行设置必填项</li>
                <li>[优化] 可添加的链接类型新增wsa和vmrc</li>
                <li>[优化] 站点设置中热门网址和最新网址由下拉选项改为直接输入,范围:0-100</li>
                <li>[修复] 分类列表无法查看加密分类的bug</li>
                <li>[模板] 主页模板 WebStack-Hugo, 修复开启拖拽排序造成悬停提示失效的bug,禁止拖拽查看全部</li>
                <li>[模板] 过度模板可能无法设置的bug</li>
                <li>[新增] 链接列表添加图标显示 (仅显示自定义图标,未定义时显示ie图标)</li>
                <li>[优化] 链接列表排序由前端当前页排序改为后端全局排序</li>
            </ul>
        </div>
    </li>
    <li class="layui-timeline-item">
        <i class="layui-icon layui-timeline-axis"></i>
        <div class="layui-timeline-content layui-text">
            <h4 class="layui-timeline-title">v2.0.27-20230618</h4>
            <ul>
                <li>[优化] 增加在线数据冗余线路,以适应更多环境</li>
                <li>[优化] 安装时检测是否存在不属于本程序的伪静态规则,存在时提醒用户处理</li>
                <li>[优化] 安装成功提示内容添加安全配置说明</li>
                <li>[新增] 站长工具新增连通测试,用于检测是否能与资源服务器连通! </li>
                <li>[模板] 主页模板 WebStack-Hugo, 新增拖拽排序支持(默认关闭),修复使用分类个性图标时无法定位分类,优化iframe的自适应</li>
                <li>[模板] 非默认登录模板无法登录的bug</li>
                <li>[修复] 过度页停留时间设置无效的bug</li>
            </ul>
        </div>
    </li>
    <li class="layui-timeline-item">
        <i class="layui-icon layui-timeline-axis"></i>
        <div class="layui-timeline-content layui-text">
            <h4 class="layui-timeline-title">v2.0.26-20230611</h4>
            <ul>
                <li>[新增] 后台页面右上角新增主页图标用于返回主页</li>
                <li>[修复] 后台左侧栏收起时无法使用二级菜单</li>
                <li>[修复] 申请收录无法提交,v2.0.24更新造成</li>
                <li>[修复] 二级密码输错时提示正确密码的bug</li>
            </ul>
        </div>
    </li>
    <li class="layui-timeline-item">
        <i class="layui-icon layui-timeline-axis"></i>
        <div class="layui-timeline-content layui-text">
            <h4 class="layui-timeline-title">v2.0.25-20230607</h4>
            <ul>
                <li>[修复] 默认设置>登录保持设为浏览器关闭时无法保存</li>
                <li>[修复] 导入OneNav Extend 升级数据时,如果description存在Null值造成导入失败</li>
                <li>[新增] 默认设置>可定义登录后进入后台还是主页 (注:此页面配置仅对新注册账号有效,不会修改现有用户的配置)</li>
                <li>[优化] 前端主题WebStack-Hugo的适配性</li>
            </ul>
        </div>
    </li>
    <li class="layui-timeline-item">
        <i class="layui-icon layui-timeline-axis"></i>
        <div class="layui-timeline-content layui-text">
            <h4 class="layui-timeline-title">v2.0.24-20230606</h4>
            <ul>
                <li>[修复] 调整数据库字段长度限制,使其能够正确记录IPV6地址/较长的浏览器UA ( 同时解决MySQL严格模式报错 )</li>
                <li>[修复] 放宽登录时UA长度限制,使其能够在腾讯系列APP(微信/QQ/QQ浏览器等)的内置浏览器登录程序</li>
                <li>[修复] 安全设置>登录保持设为浏览器关闭时无法保存</li>
                <li>[优化] 站长工具>生成伪静态,优化配置规则提高站点安全性 ( 需站长手动将新规则写入指定位置,仅针对Nginx环境 )</li>
                <li>[优化] 下载主题前检测目录是否可写,不可写时提醒用户</li>
                <li>[优化] 管理员登录后台时始终显示更新系统入口 ( 避免用户不知道在哪里更新系统 )</li>
                <li>[优化] 登录设备页面支持显示当前设备(字体为红色)</li>
            </ul>
        </div>
    </li>
    <li class="layui-timeline-item">
        <i class="layui-icon layui-timeline-axis"></i>
        <div class="layui-timeline-content layui-text">
            <h4 class="layui-timeline-title">v2.0.23-20230527</h4>
            <ul>
                <li>[优化] 本地获取链接图标的成功率</li>
                <li>[优化] 链接列表图标拉取时检测是否符合拉取条件并提醒用户/无权限时不显示按钮</li>
                <li>[优化] 收录管理设置允许用户自定义提交限制</li>
                <li>[修复] 收录管理设为无需审核时无法自动通过</li>
            </ul>
        </div>
    </li>
    <li class="layui-timeline-item">
        <i class="layui-icon layui-timeline-axis"></i>
        <div class="layui-timeline-content layui-text">
            <h4 class="layui-timeline-title">v2.0.22-20230523</h4>
            <ul>
                <li>[升级] 更新数据库,增加图标缓存记录/用户组权限列表增加图标拉取</li>
                <li>[新增] 本地获取链接图标功能,功能在网站管理>站长工具>图标配置,开启并设置参数 [ 需授权 ]</li>
                <li>[新增] 链接列表新增图标拉取,用于下载链接图标到本地储存 [ 需授权 ]</li>
                <li>[修复] v2.0.21 安装页面异常导致无法安装的问题</li>
                <li>[修复] 本地备份在未使用过备份时导入,因未自动创建目录造成导入失败</li>
                <li>[修复] 升级layui导致的链接检测无法标记异常数据/扩展字段无法保存/用户组权限/书签分享等异常</li>
                <li>[优化] 导出导入页面自适应能力,移动端访问时不显示一键添加(因为不支持)</li>
            </ul>
        </div>
    </li>
    <li class="layui-timeline-item">
        <i class="layui-icon layui-timeline-axis"></i>
        <div class="layui-timeline-content layui-text">
            <h4 class="layui-timeline-title">v2.0.21-20230521</h4>
            <ul>
                <li>[修复] 升级Layui导致的分类/链接排序功能异常</li>
                <li>[修复] 升级Layui导致的主题设置异常,请更新主题 (影响范围:全新安装≥2.0.20)</li>
                <li>[优化] 删除链接/图标时若存在本地图标共用的情况则不删除图标</li>
                <li>[优化] 链接/收录列表,支持记忆筛选列 (浏览器本地储存)</li>
            </ul>
        </div>
    </li>
    <li class="layui-timeline-item">
        <i class="layui-icon layui-timeline-axis"></i>
        <div class="layui-timeline-content layui-text">
            <h4 class="layui-timeline-title">v2.0.20-20230520</h4>
            <ul>
                <li>[升级] Layui v2.6.8 升级到 v2.8.3</li>
                <li>[升级] Medoo v2.1.6 升级到 v2.1.8</li>
                <li>[优化] MySQL数据库: 分类表名称/描述字段类型改为text</li>
                <li>[优化] MySQL数据库: 链接表名称/URL/描述字段类型改为text</li>
                <li>[优化] 分类/链接的名称和描述等长度限制可由站长在系统设置中自定义 (默认不限制)</li>
                <li>[优化] 站长在开启二级域名时检测是否符合开启条件</li>
                <li>[变更] 概要页专属地址若开启二级域名时URL使用二级域名且不带u=xxx</li>
            </ul>
        </div>
    </li>
    <li class="layui-timeline-item">
        <i class="layui-icon layui-timeline-axis"></i>
        <div class="layui-timeline-content layui-text">
            <h4 class="layui-timeline-title">v2.0.19-20230515</h4>
            <ul>
                <li>[修复] 链接列表分类筛选不能选择全部 (上个版本造成)</li>
                <li>[新增] 主链优先功能,新增检测方法的选择 ( 常规检测比快速检测准,但相对会慢一点 )</li>
                <li>[优化] 主链优先检测将401视为可用 ( 兼容需要BasicAuth认证的网页 )</li>
                <li>[优化] 在链接列表点击添加链接时自动选择当前分类(筛选不为全部时),其他细节调整</li>
            </ul>
        </div>
    </li>
    <li class="layui-timeline-item">
        <i class="layui-icon layui-timeline-axis"></i>
        <div class="layui-timeline-content layui-text">
            <h4 class="layui-timeline-title">v2.0.18-20230510</h4>
            <ul>
                <li>[新增] 限制链接和分类的名称描述长度为128个字符 ( 注:一个汉字≈3个字符,数字/字母=1个字符 )</li>
                <li>[新增] 站点设置 > 主链优先 ( 对存在备用链接的书签有效,主链接可用则直接跳转反之进入过渡页,具体用法参照文档 )</li>
                <li>[修复] PHP8.2安装时提示不支持 ( 实际支持 )</li>
                <li>[优化] 链接列表选择分类时按分类层级显示</li>
                <li>[变更] 链接图标域名由favicon.rss.ink更换为favicon.png.pub ( 由xiaoz.me提供 )</li>
                <li>[变更] 站点设置 > 输出上限改为链接数量</li>
            </ul>
        </div>
    </li>
    <li class="layui-timeline-item">
        <i class="layui-icon layui-timeline-axis"></i>
        <div class="layui-timeline-content layui-text">
            <h4 class="layui-timeline-title">v2.0.17-20230428</h4>
            <ul>
                <li>[优化] 删除用户时支持同时删除用户文件夹 ( 图标/留言等数据 ) 和备份数据</li>
                <li>[优化] 链接列表 > 检测功能的准确性</li>
                <li>[优化] 系统日志按新旧排序,支持记录邮件发送日志</li>
                <li>[修复] 用户注册初始数据可能复制失败</li>
                <li>[新增] <a href="https://gitee.com/tznb/TwoNav/wikis/pages?sort_id=7993451&doc_id=3767990" target="_blank">ATool工具箱</a>支持修改用户名  ( 建议修改前先备份数据 ) </li>
                <li>[新增] 网站管理 > 站长工具 > 邮件配置 ( 用于配置注册时发送验证码 )</li>
            </ul>
        </div>
    </li>
    <li class="layui-timeline-item">
        <i class="layui-icon layui-timeline-axis"></i>
        <div class="layui-timeline-content layui-text">
            <h4 class="layui-timeline-title">v2.0.16-20230425</h4>
            <ul>
                <li>[优化] 调整部分代码,使其能够兼容一些老旧的浏览器(如2345加速浏览器,都2023年了居然还在用2018年的内核)</li>
                <li>[优化] 调整书签导出临时数据的存放路径为自身的temp,避免部分环境无法在/tmp写入数据造成导出异常</li>
                <li>[优化] 默认过渡页</li>
                <li>[优化] 默认登录模板(注册码注册时显示注册入口)</li>
                <li>[新增] 主题商城新增引导页模板</li>
                <li>[新增] 网站管理>系统设置>默认页面 (公开使用可以选择引导页面)</li>
                <li>[修复] 站点设置>设为默认主页关闭浏览器后失效的问题</li>
                <li>[修复] 站点设置在表单输入按回车弹出帮助页面的问题</li>
                <li>[修复] 用户组主题设置权限问题</li>
                <li>[修复] 其他已知问题</li>
            </ul>
        </div>
    </li>
    <li class="layui-timeline-item">
        <i class="layui-icon layui-timeline-axis"></i>
        <div class="layui-timeline-content layui-text">
            <h4 class="layui-timeline-title">v2.0.15-20230422</h4>
            <ul>
                <li>修复默认版权链接错误的问题</li>
                <li>修复维护模式未起作用</li>
                <li>网站管理>用户管理>新增账号保留,方便公开注册的站长保留一些账号</li>
            </ul>
        </div>
    </li>
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

