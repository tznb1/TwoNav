<?php $title='更新日志'; require 'header.php'; ?>
<body>
<div class="layuimini-container">
 <div class="layuimini-main" style=" margin-left: 20px;">
    <li class="layui-timeline-item">
        <i class="layui-icon layui-timeline-axis"></i>
        <div class="layui-timeline-content layui-text">
            <h4 class="layui-timeline-title">v2.1.16-20240525</h4>
            <ul>
                <li>[升级] Layui组件由2.9.9升级到2.9.10</li>
                <li>[修复] 添加链接时上传图标未自动创建目录</li>
            </ul>
        </div>
    </li>
    <li class="layui-timeline-item">
        <i class="layui-icon layui-timeline-axis"></i>
        <div class="layui-timeline-content layui-text">
            <h4 class="layui-timeline-title">v2.1.15-20240513</h4>
            <ul>
                <li>[修复] 因 Gitee Pages 停止服务导致的相关问题</li>
                <li>[升级] Layui组件由2.9.8升级到2.9.9</li>
                <li>[变更] 系统设置隐藏部分配置(防止乱搞导致系统异常)</li>
                <li>[变更] 保存授权后自动刷新页面并跳转到概要页方便新手用户更新系统</li>
                <li>[移除] 自助注册功能</li>
            </ul>
        </div>
    </li>
    <li class="layui-timeline-item">
        <i class="layui-icon layui-timeline-axis"></i>
        <div class="layui-timeline-content layui-text">
            <h4 class="layui-timeline-title">v2.1.14-20240416</h4>
            <ul>
                <li>[修复] 修复已知的安全漏洞提高安全性</li>
                <li>[修复] 分类停用时链接列表查找全部时出现已停用分类下的链接</li>
                <li>[升级] Layui组件由2.9.7升级到2.9.8</li>
                <li>[优化] Docker镜像支持在线下载安装包</li>
                <li>[移除] 和风天气插件 ( 因官方停止服务 )</li>
                <li>[移除] 随机背景图URL ( 大多数已经无法正常使用了 )</li>
                <li>[优化] 安装脚本优化/初始配置优化</li>
            </ul>
        </div>
    </li>
    <li class="layui-timeline-item">
        <i class="layui-icon layui-timeline-axis"></i>
        <div class="layui-timeline-content layui-text">
            <h4 class="layui-timeline-title">v2.1.13-20240321</h4>
            <ul>
                <li>[优化] 兼容OneNav浏览器扩展V1.1.0重构版</li>
                <li>[优化] 浏览器插件的相关使用说明 ( 右上角账号>安全设置>获取API )</li>
                <li>[优化] 更新检测逻辑由原来判断日期改成判断版本号</li>
                <li>[优化] 支持PHP8.3环境下运行</li>
            </ul>
        </div>
    </li>
    <li class="layui-timeline-item">
        <i class="layui-icon layui-timeline-axis"></i>
        <div class="layui-timeline-content layui-text">
            <h4 class="layui-timeline-title">v2.1.12-20240308</h4>
            <ul>
                <li>[优化] 后台概要页支持时间差异较大提示,避免因时间错误导致的各种问题</li>
                <li>[新增] 站点设置新增重复链接选项,解决部分用户需要添加相同链接的问题</li>
                <li>[升级] Layui组件由2.9.2升级到2.9.7</li>
                <li>[变更] 默认配置调整,概要页QQ群改为免费用户群(授权用户可凭授权加入会员群)</li>
            </ul>
        </div>
    </li>
    <li class="layui-timeline-item">
        <i class="layui-icon layui-timeline-axis"></i>
        <div class="layui-timeline-content layui-text">
            <h4 class="layui-timeline-title">v2.1.11-20240119</h4>
            <ul>
                <li>[修复] 链接识别遇到中文域名时提示URL无效</li>
                <li>[修复] 链接模式为隐私保护(header)时中文域名无法跳转</li>
                <li>[修复] 主链优先设为强制优先时不起作用</li>
                <li>[优化] 一键诊断缺少intl扩展模块时给出提醒</li>
            </ul>
        </div>
    </li>
    <li class="layui-timeline-item">
        <i class="layui-icon layui-timeline-axis"></i>
        <div class="layui-timeline-content layui-text">
            <h4 class="layui-timeline-title">v2.1.09-20231220</h4>
            <ul>
                <li>[修复] 紧急修复一个影响登录的bug,影响范围:v2.1.08版本 + MySQL数据库</li>
            </ul>
        </div>
    </li>
    <li class="layui-timeline-item">
        <i class="layui-icon layui-timeline-axis"></i>
        <div class="layui-timeline-content layui-text">
            <h4 class="layui-timeline-title">v2.1.08-20231219</h4>
            <ul>
                <li>[升级] Layui组件由2.9.0升级到2.9.2</li>
                <li>[修复] Atool工具和用户管理中强制修改用户密码时未将已登录的终端踢下线的bug</li>
                <li>[优化] 概要页面更新内容直接从服务器获取并显示,不需要在跳转到Gitee上查看</li>
                <li>[优化] 主题管理:可更新时在右上显示一个问号,点击可以查看更新内容</li>
                <li>[变更] 普通账号不在支持自定义登录模板,只有站长号可以选择和配置模板</li>
                <li>[优化] 系统设置的保存按钮改为悬浮在页面底部,避免老是要滚动到底部去点保存的问题</li>
                <li>[修复] 链接列表手机端不显示删除按钮的问题</li>
                <li>[修复] 书签分享特定条件下存在的bug</li>
                <li>[修复] 安全设置>登录保持设为浏览器关闭会导致无法登录的bug</li>
                <li>[修复] OTP双重认证使用公用登录入口时无法输入验证码的问题 ( 需更新登录模板 )</li>
                <li>[模板] [12.02]爱导航V1: 配置选项新增分类收缩,可选仅图标/分类/菜单/目录,用于解决部分手机端用户不知道点这个图标展开分类的问题</li>
                <li>[模板] [12.02]百素New: 新增拖拽排序功能、修复未加载用户header和全局header的bug、新增搜索框背景自定义支持</li>
                <li>[模板] [12.20]花森主页: 调整本地添加链接时判断是否为URL的条件,仅检测http(s)://开头</li>
                <li>[修复] [12.20]WebStack-Hugo: 夜间模式下搜索框热词点空白处没有取消热词显示的问题</li>
            </ul>
        </div>
    </li>
    <li class="layui-timeline-item">
        <i class="layui-icon layui-timeline-axis"></i>
        <div class="layui-timeline-content layui-text">
            <h4 class="layui-timeline-title">v2.1.06-20231114</h4>
            <ul>
                <li>[修复] 开启离线模式时概要页依旧获取在线数据,V0921</li>
                <li>[优化] 为部分操作添加处理中的效果( 防止网络极差的用户以为没点到而重复点击 )</li>
                <li>[新增] 内置用户组新增:访客 (代号:visitor,处于该用户组的账号登录后跳转到默认用户的主页,配合引导页使用实现需注册登录才能访问站点)</li>
                <li>[备注] 由于访客无权限进入后台,所以也无法自助修改密码</li>
                <li>[新增] 在用户组列表中显示内置用户组 ( 默认/访客/站长 )</li>
                <li>[变更] 系统设置中的默认分组允许设置为内置用户组 ( 例如设为访客 )</li>
                <li>[新增] 访问限制 (右上角账号下拉),可设置:无限制/白名单/黑名单,具体查看页面中的说明</li>
            </ul>
        </div>
    </li>
    <li class="layui-timeline-item">
        <i class="layui-icon layui-timeline-axis"></i>
        <div class="layui-timeline-content layui-text">
            <h4 class="layui-timeline-title">v2.1.05-20231107</h4>
            <ul>
                <li>[修复] 使用MySQL/MariaDB数据库时记录访客IP错误</li>
                <li>[修复] 注册模板/引导页模板配置无法正常读取</li>
                <li>[模板] 主页模板新增 > Snavigation ( 简约型的模板,点击时间显示书签数据 )</li>
                <li>[模板] 引导页模板新增 > 無名の主页</li>
                <li>[模板] WebStack-Hugo > 搜索栏新增站内搜索选项,用于解决同时开启站内搜索和搜索热词时遮挡问题</li>
                <li>[模板] 挽风导航V1.0 > CSS样式细节优化 </li>
            </ul>
        </div>
    </li>
    <li class="layui-timeline-item">
        <i class="layui-icon layui-timeline-axis"></i>
        <div class="layui-timeline-content layui-text">
            <h4 class="layui-timeline-title">v2.1.04-20231101</h4>
            <ul>
                <li>[修复] 连续添加分类时未正确添加到所选的父分类中</li>
                <li>[修复] 使用MySQL/MariaDB数据库时文章图片可能不显示的bug</li>
                <li>[优化] 二级域名功能支持已知的双后缀顶级域名(例如example.com.cn)</li>
                <li>[新增] Docker镜像增加intl模块</li>
                <li>[模板] WebStack-Hugo > 修复过渡页模板logo设置无效</li>
            </ul>
        </div>
    </li>
    <li class="layui-timeline-item">
        <i class="layui-icon layui-timeline-axis"></i>
        <div class="layui-timeline-content layui-text">
            <h4 class="layui-timeline-title">v2.1.03-20231020</h4>
            <ul>
                <li>[修复] 站点地图时间格式问题</li>
                <li>[修复] 后台我的主页地址错误</li>
                <li>[变更] 移除授权管理页查询授权功能 ( 如需查询请联系客服 )</li>
                <li>[优化] 使用过渡页模板时若站点设置>链接模式不是过渡页面时自动修改配置</li>
                <li>[模板] WebStack-Hugo > 修复二级分类横向滚动条过大/顶部的管理入口未遵循系统设置是否显示</li>
            </ul>
        </div>
    </li>
    <li class="layui-timeline-item">
        <i class="layui-icon layui-timeline-axis"></i>
        <div class="layui-timeline-content layui-text">
            <h4 class="layui-timeline-title">v2.1.02-20231013</h4>
            <ul>
                <li>[优化] IP统计的记录方式,提高性能和稳定性</li>
                <li>[修复] 未在系统设置保存过设置时因缺少参数而导致部分页面加载异常</li>
                <li>[安装] 数据库类型选项新增MariaDB,其他细节调整</li>
                <li>[变更] 免费版升级授权版的相关提示信息</li>
            </ul>
        </div>
    </li>
    <li class="layui-timeline-item">
        <i class="layui-icon layui-timeline-axis"></i>
        <div class="layui-timeline-content layui-text">
            <h4 class="layui-timeline-title">v2.1.01-20231003</h4>
            <ul>
                <li>[优化] 已部署国内服务器并接入了CDN加速和3个资源节点</li>
                <li>[新增] 系统设置>资源节点,可选自动/国内1/国内2/海外1</li>
                <li>[新增] 系统设置>请求超时,默认为3秒,范围3-60</li>
                <li>[新增] 后台>安全设置(右上角账号下拉)>新增管理入口选项,可选隐藏/显示/登录时显示</li>
                <li>[优化] 静态链接(伪静态)处理方案,支持选择UN(用户名)或UID(用户ID)作为用户标识</li>
                <li>[优化] 百度推送/站点地图静态链接格式调整</li>
                <li>[优化] 后台的部分前台链接支持伪静态链接</li>
                <li>[修复] 注册模板无法切换、其他已知的bug</li>
                <li>[模板] 所有模板更新至2.1.0</li>
                <li>[模板] 下载/更新多合一模板时自动释放到对应模板目录,无需重复下载!</li>
            </ul>
        </div>
    </li>
    <li class="layui-timeline-item">
        <i class="layui-icon layui-timeline-axis"></i>
        <div class="layui-timeline-content layui-text">
            <h4 class="layui-timeline-title">v2.0.41-20230921</h4>
            <ul>
                <li>[优化] 后台>概要页面改为异步加载,避免因网络不好时出现页面打开慢的问题</li>
                <li>[新增] 后台>授权管理页面新增正版验证按钮/显示授权类型/相关验证逻辑调整</li>
                <li>[修复] 文章功能全局开关为关闭时前台依然输出文章链接的bug</li>
                <li>[修复] 从免费版升级到授权版时未自动清理缓存造成不好的体验</li>
            </ul>
        </div>
    </li>
    <li class="layui-timeline-item">
        <i class="layui-icon layui-timeline-axis"></i>
        <div class="layui-timeline-content layui-text">
            <h4 class="layui-timeline-title">v2.0.40-20230919</h4>
            <ul>
                <li>[变更] 优化资源节点,提高系统更新速度,主题下载速度,预览图加载速度! </li>
                <li>[变更] 为保障授权用户的权益,本版开始请求下载主题/更新系统时服务器将验证授权</li>
                <li>[变更] 开源版(免费版)与授权版(标准版/高级版)将分开维护,以避免某些人非法获取权限,这对付费用户是极不公平的</li>
                <li>[新增] 特定操作时清理缓存,避免因版本不一致造成的问题</li>
                <li>[新增] 请求服务器时携带本地系统版本号以便于服务器返回相匹配的资源</li>
                <li>[新增] 导出导入页面的导入功能支持iTab的数据( 扩展名为: itabdata )</li>
            </ul>
        </div>
    </li>
    <li class="layui-timeline-item">
        <i class="layui-icon layui-timeline-axis"></i>
        <div class="layui-timeline-content layui-text">
            <h4 class="layui-timeline-title">v2.0.39-20230913</h4>
            <ul>
                <li>[升级] Layui组件由 v2.8.10 升级到 v2.8.17</li>
                <li>[优化] 站点地图支持生成文章页面链接</li>
                <li>[新增] 系统设新增静态链接选项,开启后部分动态链接将改为静态链接 (请确保伪静态生效中,仅针对前台内容)</li>
                <li>[优化] 挽风导航V1:文章图片点击放大,新增两处自定义代码,添加返回顶部功能!主页增加一处自定义代码,修复全局底部代码无效,磨砂风格支持</li>
                <li>[修复] 使用Mysql数据库在访问注册页面/引导时报错</li>
                <li>[修复] 全局类模板配置保存位置错误</li>
                <li>[修复] 站点地图HTTPS访问时携带443端口的问题 #I80I6K</li>
                <li>[变更] 阻止将登录/注册入口改成系统在使用的名称,避免产生冲突</li>
            </ul>
        </div>
    </li>
    <li class="layui-timeline-item">
        <i class="layui-icon layui-timeline-axis"></i>
        <div class="layui-timeline-content layui-text">
            <h4 class="layui-timeline-title">v2.0.38-20230906</h4>
            <ul>
                <li>[变更] 初始化安装默认关闭防XSS和SQL(个人使用没必要开启,会导致自定义代码时被拦截)</li>
                <li>[变更] 后台概要页获取IP列表时对IP进行排序,以方便观察非正常访问的IP(如爬虫)</li>
                <li>[变更] Nginx部分伪静态规则由程序接管,避免更新规则时用户需手动配置伪静态 (需将生成的规则重新配置到服务器)</li>
                <li>[变更] 授权管理页面内容更新</li>
                <li>[新增] 系统设置中新增站点地图入口,可配置生成sitemap.xml站点地图的参数! (首次使用请看顶部说明)</li>
                <li>[新增] 链接列表和文章链接新增百度推送,用于将链接推送到百度搜索 (首次使用请看顶部说明)</li>
                <li>[修复] 可更新的主页模板没有显示预览按钮</li>
            </ul>
        </div>
    </li>
    <li class="layui-timeline-item">
        <i class="layui-icon layui-timeline-axis"></i>
        <div class="layui-timeline-content layui-text">
            <h4 class="layui-timeline-title">v2.0.37-20230831</h4>
            <ul>
                <li>[变更] 优化前端前置处理代码,让模板调用数据更加灵活</li>
                <li>[新增] 支持更换验证模板/收录模板/留言模板</li>
                <li>[新增] 验证模板支持设置提示内容,如获取密码的提示</li>
                <li>[新增] 挽风导航V1的收录模板和留言模板</li>
                <li>[新增] 4个简约风格的验证模板</li>
                <li>[修复] 文章编辑器输入HTML代码时在编辑存在异常的问题</li>
                <li>[修复] 文章标题/摘要存在HTML标签时被解析的问题</li>
                <li>[修复] WebStack-Hugo主页模板4个已知问题</li>
                <li>[修复] 特定情况下安装时使用MySQL数据库可能乱码的问题</li>
            </ul>
        </div>
    </li>
    <li class="layui-timeline-item">
        <i class="layui-icon layui-timeline-axis"></i>
        <div class="layui-timeline-content layui-text">
            <h4 class="layui-timeline-title">v2.0.36-20230824</h4>
            <ul>
                <li>[修复] 判断是否显示收录的逻辑错误(导致设为无需审核时不显示)</li>
                <li>[变更] 移除2个链接图标API,因稳定性欠佳</li>
                <li>[修复] WebStack-Hugo主页模板悬停提示不显示</li> 
                <li>[新增] 挽风导航主页模板(内置文章模板/拟态风格),注:内置文章模板在预览状态下是不生效的!</li>
                <li>[新增] 挽风导航登录模板/过度模板</li>
                <li>[新增] 后台概要页可以点击报表统计获取访问的IP列表</li>
                <li>[修复] 文章状态非公开且已登录无法预览文章</li>
            </ul>
        </div>
    </li>
    <li class="layui-timeline-item">
        <i class="layui-icon layui-timeline-axis"></i>
        <div class="layui-timeline-content layui-text">
            <h4 class="layui-timeline-title">v2.0.35-20230816</h4>
            <ul>
                <li>[新增] Atool工具箱增加关闭OTP双重验证选项(删OTP),用于解决站长丢失OTP令牌造成无法登录</li>
                <li>[新增] 用户管理支持关闭OTP双重验证选项,用于站长帮助用户关闭OTP双重验证</li>
                <li>[优化] 邮件配置发送人只填发送人名称未按要求格式填写邮箱时由系统自动完成拼接</li>
                <li>[优化] 文章管理特定情况造成缺少资源时提醒用户如何解决</li>
                <li>[模板] 新增爱导航V1主页模板,轻量化设计简洁不卡顿/支持缓存/自适应/站内搜索,适合书签多的用户使用</li>
                <li>[模板] WebStack-Hugo主页模板新增:夜间背景图/炫彩横幅</li>
                <li>[模板] 修复默认过度模板兼容问题</li>
            </ul>
        </div>
    </li>
    <li class="layui-timeline-item">
        <i class="layui-icon layui-timeline-axis"></i>
        <div class="layui-timeline-content layui-text">
            <h4 class="layui-timeline-title">v2.0.34-20230809</h4>
            <ul>
                <li>[新增] 安全设置新增OTP双重验证</li>
                <li>[模板] 所有登录模板:已开启双重验证时,支持输入OTP验证码,版本:2.0.4 </li>
                <li>[警告] 如果您正在使用非默认登录模板,请立即更新登录模板,以免因模板不支持输入OTP验证码造成无法登录</li>
                <li>[新增] 导出导入>清空数据>支持清空文章和上传目录(upload)</li>
                <li>[新增] 导出导入>本地备份>支持备份和回滚文章列表</li>
            </ul>
        </div>
    </li>
    <li class="layui-timeline-item">
        <i class="layui-icon layui-timeline-axis"></i>
        <div class="layui-timeline-content layui-text">
            <h4 class="layui-timeline-title">v2.0.33-20230802</h4>
            <ul>
                <li>[新增] 文章编辑新增封面上传功能,插入网络视频功能</li>
                <li>[新增] 文章列表以链接的方式展现到主页( 已登录时显示公开和私有,未登录时显示公开文章 )</li>
                <li>[新增] 文章列表新增批量操作,支持批量删除,批量修改文章分类和状态</li>
                <li>[修复] 文章相关功能的已知问题</li>
                <li>[变更] 移除文章功能的独立分类机制,改为使用链接分类 ( 已存在的文章需手动更新分类 )</li>
                <li>[变更] API接口鉴权逻辑调整,新增几个兼容API,移除API模式中的兼容+开放模式</li>
                <li>[变更] 默认主页模板右键对查看全部或文章链接操作给出提示,给文章链接添加黑色角标</li>
                <li>[新增] 主页模板:简约主题 ( 需将安全设置>API模式>改为兼容模式才能使用全部功能 ) 作者:涂山</li>
                <li>[新增] 主页模板:花森主页( 自带文章浏览功能 ),作者:花森JioJio</li>
                <li>[新增] 文章模板:挽风导航,作者:凌云</li>
                <li>[优化] 默认文章模板样式</li>
            </ul>
        </div>
    </li>
    <li class="layui-timeline-item">
        <i class="layui-icon layui-timeline-axis"></i>
        <div class="layui-timeline-content layui-text">
            <h4 class="layui-timeline-title">v2.0.32-20230727</h4>
            <ul>
                <li>[新增] 扩展功能新增简易文章管理 [ 半成品,尚未完善 ]</li>
                <li>[新增] 链接自定义字段类型新增up_img,该类型支持上传1M大小的图片,权限与上传图标共享</li>
                <li>[新增] 链接自定义字段新增提示内容</li>
                <li>[变更] 主页模板前置处理,若模板支持链接扩展时提供扩展信息</li>
                <li>[跟进] 支持onenav新版浏览器插件的兼容</li>
                <li>[修复] ip统计存在异常的问题</li>
                <li>[修复] 上传链接图标后端接口未限制大小</li>
                <li>[修复] 在使用CDN的情况下可能出现授权验证问题</li>
            </ul>
        </div>
    </li>
    <li class="layui-timeline-item">
        <i class="layui-icon layui-timeline-axis"></i>
        <div class="layui-timeline-content layui-text">
            <h4 class="layui-timeline-title">v2.0.31-20230720</h4>
            <ul>
                <li>[新增] 支持统计访问IP数,可在后台概要页报表统计展示</li>
                <li>[新增] 数据库链接表新增关键字列</li>
                <li>[新增] 添加/编辑链接页面新增关键字输入,用于过渡页SEO优化 (注:230715之前的过度模板固定用链接标题作为关键字)</li>
                <li>[新增] 过度模板设置新增默认关键字选项 (针对未填写关键字时选择其他值作为关键字,需更新过度页模板)</li>
                <li>[新增] 链接列表新增识别按钮,用于批量获取URL的标题/描述/关键字/图标</li>
                <li>[新增] 系统设置中新增链接关键字长度限制</li>
                <li>[新增] 链接列表排序模式支持记忆到客户端</li>
                <li>[新增] 已开启链接扩展字段时,添加链接时支持填写扩展字段 (原仅编辑支持)</li>
                <li>[修复] 编辑链接时重置按钮未对扩展内容重置</li>
                <li>[优化] 添加/编辑链接页面识别功能支持关键字识别</li>
                <li>[优化] 链接列表的图标拉取与自动识别功能合并</li>
                <li>[优化] 链接识别的成功率</li>
            </ul>
        </div>
    </li>
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

