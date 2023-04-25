<!DOCTYPE HTML>
<html>
<head>
    <meta charset="utf-8" />
    <title><?php echo $site['Title'];?></title>
    <meta name="keywords" content="<?php echo $site['keywords']; ?>">
    <meta name="description" content="<?php echo $site['description']; ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no" />
    <link rel="stylesheet" href="<?php echo $theme_dir;?>/main.css" />
    <link rel="shortcut icon" href="<?php echo $favicon;?>">
</head>
<style>
	#bg:after {background-image: url("<?php echo empty($theme_config['bg_img'])?$libs."/Other/bg.png":$theme_config['bg_img'];?>");}
</style>

<body > 
	<div id="wrapper">
		<header id="header">
			<div class="content">
				<div class="inner">
					<h3><?php echo empty($theme_config['title'])?$site['title']:$theme_config['title'];?></h3>
					<p><?php echo empty($theme_config['p1'])?$site['description']:$theme_config['p1']; ?></p>
				</div>
			</div>
			<nav>
				<ul>
					<li><a href="./index.php?c=<?php echo $global_config["Login"];?>" target="_self">登录</a></li>
					<li><a href="./index.php?c=<?php echo $global_config["Register"];?>" target="_self">注册</a></li>
				</ul>
			</nav>
		</header>
		<footer id="footer">
			<?php echo $copyright.PHP_EOL;?><?php echo $ICP.PHP_EOL;?>
			<?php echo $global_config['global_footer'].PHP_EOL;?>
		</footer>
	</div>
	<div id="bg"></div>
</body>
</html>