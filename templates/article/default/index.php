<!DOCTYPE html>
<html lang="zh-CN">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<meta http-equiv="X-UA-Compatible" content="ie=edge" />
		<meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no">
		<title><?php echo $data['title'];?></title>
		<meta name="keywords" content="<?php echo $data['summary']; ?>">
        <meta name="description" content="<?php echo $data['summary']; ?>">
		<link rel="stylesheet" href="<?php echo $theme_dir?>/index.css?v=<?php echo $theme_ver; ?>" type="text/css" media="all" />
		<link rel="shortcut icon" href="<?php echo $favicon;?>">
	</head>
	<body>
		<div class="newbui-header__bar clearfix">
			<div class="container">
				<div class="row">
					<ul class="newbui-header__menu clearfix">
					    <li class="pc"><a class="navbar-light" href="./index.php?u=<?php echo $GLOBALS['u'];?>">首页</a></li>
					</ul>
				</div>
			</div>
		</div>
		<div class="container">
			<div class="row">
				<div class="newbui-pannel clearfix">
					<div class="newbui-pannel-box clearfix">
						<div class="newbui-pannel_bd col-pd clearfix">
							<h1 class="news-title"><?php echo $data['title'];?></h1>
							<div class="news-content">
                            <?php echo $data['content'];?>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="newbui-foot clearfix">
			<p class="text-muted">
				<?php echo $copyright.PHP_EOL;?>
                <?php echo $ICP.PHP_EOL;?>
			</p>
            <?php echo $site['custom_footer'].PHP_EOL;?>
            <?php echo $global_config['global_footer'].PHP_EOL;?>
		</div>
	</body>
</html>