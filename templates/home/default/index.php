<?php 
$night = $theme_config['night'] == 1 || ( $theme_config['night'] == 2 && (date('G') <= 12 || date('G') >= 19 )) ? 'mdui-theme-layout-dark':'';
$background = $theme_config['backgroundURL'];
$DescrRowNumber = intval($theme_config['DescrRowNumber']);
$WeatherKey = $theme_config['WeatherKey'];
$WeatherPosition =  intval(empty($WeatherKey)?"0":$theme_config['WeatherPosition']);
$referrer = $theme_config['referrer'];
$protectA = (($referrer == 'link' || $referrer == 'link_icon') && $site['link_model'] == 'direct') ? 'referrerpolicy="same-origin"':'';
$protectIMG = ($referrer == 'link_icon' || $referrer == 'icon' ) ? 'referrerpolicy="same-origin"':'';
if($theme_config['ClickLocation'] =='0'){
    $CLALL = "</a>";
}else{
    $CLBT = "</a>";
}

if ($DescrRowNumber <= 0 ){
    $DescrRowNumber = 0; $DescrHeight= 0; $Card = 38;
}elseif($DescrRowNumber >= 1 && $DescrRowNumber <= 4 ){
    $DescrHeight= $DescrRowNumber * 24;
    $Card = 72 + $DescrHeight;
}else{
    $DescrRowNumber = 2; $DescrHeight= 48; $Card = 120; // 超出范围则设为2行
}

?>
<!DOCTYPE html>
<html lang="zh-ch">
<head>
<meta charset="utf-8">
<title><?php echo $site['Title'];?></title>
<meta name="keywords" content="<?php echo $site['keywords']; ?>">
<meta name="description" content="<?php echo $site['description']; ?>">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<?php if($referrer == 'overall' && $site['link_model'] == 'direct'){echo '<meta name="referrer" content="same-origin">'."\n";}?> 
<link rel='stylesheet' href='<?php echo $libs?>/MDUI/v1.0.1/css/mdui.min.css'>
<link rel='stylesheet' href='<?php echo $libs?>/ContextMenu/2.9.2/jquery.contextMenu.min.css'>
<link rel="stylesheet" href="<?php echo $libs?>/Font-awesome/4.7.0/css/font-awesome.css">
<link rel="stylesheet" href="<?php echo $libs?>/Layui/v2.6.8/css/layui-icon.css">
<link rel="stylesheet" href="<?php echo $theme_dir?>/static/style<?php echo $theme_config['CardNum'];?>.css?v=<?php echo $theme_ver; ?>">
<link rel="shortcut icon" href="<?php echo $favicon;?>">
<style>
<?php  $SBC = $theme_config['SidebarBackgroundColor']; if( empty($night)  ) {?>
/*配色*/
.mdui-theme-primary-indigo .mdui-color-theme {background-color: <?php echo $theme_config['HeadBackgroundColor'];?>!important;}
.mdui-loaded .mdui-drawer { <?php echo(empty($SBC)?'':'background-color:'.$SBC.'!important;');?>}
.HFC{color: <?php echo $theme_config['HeadFontColor'];?>!important;}
.CBC{background-color: <?php echo $theme_config['CardBackgroundColor'];?>!important;} 
.OBC{background-color: <?php echo $theme_config['OtherBackgroundColor'];?>!important;}
.CFC{color: <?php echo $theme_config['CategoryFontColor'];?>!important;}
.TFC{color: <?php echo $theme_config['TitleFontColor'];?>!important;}
.DFC{color: <?php echo $theme_config['DescrFontColor'];?>!important;}
<?php } ?>
<?php if( !empty($background) && empty($night) ) {?>
/*背景图*/
body{
    background: url('<?php echo $background;?>');
    background-size:100% 100%;
    background-repeat:no-repeat; 
    background-attachment: fixed;
}
<?php } ?>
/*描述行数*/
.link-line {height:<?php echo $Card;?>px;}
.link-content { 
    height:<?php echo $DescrHeight;?>px;
    -webkit-line-clamp: <?php echo $DescrRowNumber;?>;
}
.mdui-card-primary {padding-top: <?php if($DescrHeight == 0){echo '8px';}else{echo '16px';} ;?>;}
</style>
    <?php echo $site['custom_header'].PHP_EOL?>
    <?php echo $global_config['global_header'].PHP_EOL?>
</head>
<body class = "mdui-drawer-body-left mdui-appbar-with-toolbar mdui-theme-primary-indigo mdui-theme-accent-pink mdui-loaded OBC <?php echo $night;?>" >
	<!--导航工具-->
	<header class = "mdui-appbar mdui-appbar-fixed" >
		<div class="mdui-toolbar mdui-color-theme" >
		<span class="mdui-btn mdui-btn-icon mdui-ripple mdui-ripple-white HFC" mdui-drawer="{target: '#drawer', swipe: true}"><i class="mdui-icon material-icons">menu</i></span>
		  <a href="" class = "mdui-typo-headline HFC" ><span class="mdui-typo-title"><?php echo $site['logo'];?></span></a>
		  <div class="mdui-toolbar-spacer" ></div>
		 
		  <!-- 新版搜索框 -->
		  	<div class="mdui-col-md-2 mdui-col-xs-5">
				<div class="mdui-textfield mdui-textfield-floating-label">
					<input class="mdui-textfield-input search HFC"  placeholder="输入书签关键词搜索" type="text" />
				</div>
			</div>
	
			<?php if($WeatherPosition==1){ echo '<div id="he-plugin-simple"></div>';} ?>
			<a class = "mdui-hidden-xs mdui-btn mdui-btn-icon" id="config"  title = "主题设置" <?php if(!is_login) {echo 'style="display:none;"';}?>><i   class="mdui-icon material-icons HFC">&#xe40a;</i></a>
			<!-- 新版搜索框END -->
		</div>
	</header>
	<!--导航工具END-->
	<?php if( is_login ) {
	?><!-- 添加按钮 -->
	<div class="right-button mdui-hidden-xs" style="position: fixed;right:10px;bottom:80px;z-index:1000;">
		<div><button title = "快速添加链接" id = "add" class="mdui-fab mdui-color-theme-accent mdui-ripple mdui-fab-mini"><i class="mdui-icon material-icons">add</i></button></div>
	</div>
<?php } ?>
    <!-- 返回顶部按钮 -->
	<div class="top mdui-shadow-10"><a href="javascript:;" title="返回顶部" onclick="gotop()"><i class="mdui-icon material-icons">arrow_drop_up</i></a></div>
	<!--左侧抽屉导航-->
	<div class="mdui-drawer" id="drawer">
	<ul class="mdui-list">
	  	<?php
			//遍历分类目录并显示
			foreach ($category_parent as $category) {
			//var_dump($category);
		?>
		<div class="mdui-collapse" mdui-collapse>
              <div class="mdui-collapse-item">
        <div class="mdui-collapse-item-header CFC">
		<a href="#category-<?php echo $category['id']; ?>">
			<li class="mdui-list-item mdui-ripple">
				<div class="mdui-list-item-content category-name CFC">
				    <i class='<?php echo $category['font_icon']; ?>'></i><?php echo $category['name']; ?>
				</div>
				    <?php echo !empty($category['subitem_count'])?'<i class="mdui-collapse-item-arrow mdui-icon material-icons">keyboard_arrow_down</i>':""; ?> 
			</li>
		</a>
		</div>
		<!-- 遍历二级分类-->
          <div class="mdui-collapse-item-body">
         <ul>
         <?php foreach (get_category_sub( $category['id'] ) as $category_sub){

         ?>
            <a href="#category-<?php echo $category_sub['id']; ?>">
                <li class="mdui-list-item mdui-ripple" style="margin-left:-4.3em;">
                    <div class="mdui-list-item-content category_sub CFC">
                        <i class='<?php echo $category_sub['font_icon']; ?>'></i><?php echo $category_sub['name']; ?>
                    </div>
                </li>
            </a>
         <?php } ?>
        </ul>
        </div>
		<!--遍历二级分类END-->
		</div>
        </div>
	    
		<?php } ?>
	    <div class="mdui-divider"></div>
<?php if(is_guestbook()){ ?>
    	<a href="./index.php?c=guestbook&u=<?php echo $u?>" target="_blank">
			<li class="mdui-list-item mdui-ripple">
				<div class="mdui-list-item-content category-name"><i class="fa fa-commenting-o"></i> 在线留言</div>
			</li>
		</a>
<?php } ?>
<?php if (is_apply()) { ?>
    	<a href="./index.php?c=apply&u=<?php echo $u?>" target="_blank">
			<li class="mdui-list-item mdui-ripple">
				<div class="mdui-list-item-content category-name"><i class="fa fa-pencil"></i> 申请收录</div>
			</li>
		</a>
<?php } ?>
        <a href="./index.php?c=admin&u=<?php echo $u?>"><li class="mdui-list-item mdui-ripple"><div class="mdui-list-item-content category-name CFC"><i class="fa fa-user-circle"></i>系统管理</div></li></a>
	</ul>

	</div>
	
	<!--左侧抽屉导航END-->
	<!--正文内容部分-->
	<div class="mdui-container">
	    <?php if($WeatherPosition==2){ echo '<div style="position:fixed;z-index:1000;right:0px;width:160px;padding-right:0px;"><div id="he-plugin-simple"></div></div>'."\n";} ?>
		<div class="mdui-row">
			<!-- 遍历分类目录 -->
            <?php foreach ( $categorys as $category ) {
                $fid = $category['id'];
                $links = get_links($fid);
                //如果分类是私有的
                if( $category['property'] == 1 ) {
                    $property = '&nbsp;<i class="fa fa-lock" style = "color:#5FB878"></i>';
                }
                else {
                    $property = '';
                }
            ?>
			<div id = "category-<?php echo $category['id']; ?>" class = "mdui-col-xs-12 mdui-typo-title cat-title CFC">
			    <i class='<?php echo $category['font_icon']; ?>'></i> <?php echo $category['name'].$property; ?> 
				<span class = "mdui-typo-caption DFC"><?php echo $category['description']; ?></span>
			</div>
			<!-- 遍历链接 -->
			<?php
				foreach ($links as $link) {
					//默认描述
					$link['description'] = empty($link['description']) ? '作者很懒，没有填写描述。' : $link['description'];
				//var_dump($link);
			?>
			<div class="mdui-col-lg-3 mdui-col-md-4 mdui-col-xs-12 link-space"  id = "id_<?php echo $link['id']; ?>" link-title = "<?php echo $link['title']; ?>" link-url = "<?php echo $link['url']; ?>">
			    <span style = "display:none;"><?php echo $link['real_url']; ?></span>
				<!--定义一个卡片-->
				<div class="mdui-card link-line mdui-hoverable CBC">
						<!-- 如果是私有链接，则显示角标 -->
						<?php if($link['property'] == 1 ) { ?>
						<div class="angle">
							<span> </span>
						</div>
						<?php } ?>
						<!-- 角标END -->
						<a class="TFC" href="<?php echo $link['url']; ?>" target="_blank" <?php echo $protectA; ?> title = "<?php echo $link['description']; ?>">
							<div class="mdui-card-primary" >
									<div class="mdui-card-primary-title link-title">
										<img src="<?php echo $link['ico']; ?>" alt="HUAN" width="16px" height="16px" <?php echo $protectIMG; ?>>
										<span class="link_title"><?php echo $link['title']; ?></span> 
									</div> 
							</div>
						<?php echo $CLBT; ?>
						<!-- 卡片的内容end -->
					<div class="mdui-card-content mdui-text-color-black-disabled DFC" style="padding-top:0px;"><span class="link-content"><?php echo $link['description']; ?></span></div><?php echo $CLALL; ?>
				</div>
				<!--卡片END-->
			</div>
			<?php } ?>
			<!-- 遍历链接END -->
			<?php } ?>
		</div>
		
		<!-- row end -->
	</div>
	<div class="mdui-divider" style = "margin-top:2em;"></div>
	<!--正文内容部分END-->
	<!-- footer部分 --> 
	<footer >
        <?php echo $copyright.PHP_EOL;?>
        <?php echo $ICP.PHP_EOL;?>
        <?php echo $site['custom_footer'].PHP_EOL;?>
        <?php echo $global_config['global_footer'].PHP_EOL;?>
	</footer>
	 
	<!-- footerend -->
<script>
var u = '<?php echo $u?>';
var t = '<?php echo str_replace("./templates/", "", $theme);?>';
var is_login = <?php echo is_login?'true':'false'; ?>;
</script>
<script src = "<?php echo $libs?>/jquery/jquery-3.6.0.min.js"></script>
<script src = "<?php echo $libs?>/Layer/v3.3.0/layer.js"></script> 
<script src = "<?php echo $libs?>/ContextMenu/2.9.2/jquery.contextMenu.min.js"></script>
<script src = "<?php echo $libs?>/Other/ClipBoard.min.js"></script>
<script src = "<?php echo $libs?>/MDUI/v1.0.1/js/mdui.min.js"></script>
<script src = "<?php echo $libs?>/Other/holmes.js"></script>
<script src = "<?php echo $libs; ?>/jquery/jquery.qrcode.min.js"></script>
<script src = "<?php echo $theme_dir?>/static/embed.js?v=<?php echo $theme_ver;?>"></script>
<?php 
// 如果Key不为空,则加载天气插件!
if ($WeatherPosition != 0){
    $WeatherFontColor = $theme_config['WeatherFontColor'];  
    if ($WeatherFontColor == 1){
        $WeatherFontColor = $theme_config['HeadFontColor'];
    }elseif($WeatherFontColor == 2){
        $WeatherFontColor = $theme_config['TitleFontColor'];
    }
    ?>
<!--天气插件-->
<script>
WIDGET = {
  "CONFIG": {
    "modules": "01234", //实况温度、城市、天气状况、预警
    "background": "<?php echo $theme_config['WeatherBackground'];?>", //背景颜色
    "tmpColor": "<?php echo $WeatherFontColor ?>", //温度文字颜色
    "tmpSize": "16",
    "cityColor": "<?php echo $WeatherFontColor ?>", //城市名文字颜色
    "citySize": "16",
    "aqiColor": "<?php echo $WeatherFontColor ?>", //空气质量文字颜色
    "aqiSize": "16", 
    "weatherIconSize": "24", //天气图标尺寸
    "alertIconSize": "18", //预警图标尺寸
    "padding": "5px 1px 5px 1px", //边距
    "borderRadius": "5", //圆角
    "key": "<?php echo $WeatherKey;?>"
  }
}
</script>
<script src="https://widget.qweather.net/simple/static/js/he-simple-common.js?v=2.0"></script>
<!--天气插件End-->
<?php
}
?>

</body>
</html>