<!DOCTYPE html>
<html lang="zh-cn" xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta charset="utf-8" />
	<title><?php echo $link['title']; ?> - <?php echo $site['title']; ?></title>
	<meta name="keywords" content="<?php echo $link['keywords']; ?>" />
	<meta name="description" content="<?php echo $link['description']; ?>" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="stylesheet" href="<?php echo $libs?>/bootstrap4/css/bootstrap.min.css" type="" media=""/>
	<link rel="shortcut icon" href="<?php echo $favicon;?>">
	<!--<script src="<?php echo $libs?>/jquery/jquery-2.2.4.min.js"></script>-->
	<!--<script src="<?php echo $libs?>/bootstrap4/js/bootstrap.min.js"></script>-->
	<style>
		.a_d img{
			max-width:100%;
			padding-top:1em;
			padding-bottom:1em;
		}
		#menu{
			width:100%;
			background-color: #343a40!important;
		}
		.list-group-item {
            background-color: #bee5eb;
        }
        .badge-pill-2{
            margin-right: 10px;
            padding-right: 0.6em;
            padding-left: 0.6em;
            width: 60px;
        }
        .badge {
            font-size: 100%; 
        }
        a {
            display: block;
            overflow: hidden;
            white-space: nowrap;
            text-overflow: ellipsis;
        }
 
 
	</style>
    <?php echo $site['custom_header'].PHP_EOL?>
    <?php echo $global_config['global_header'].PHP_EOL?>
<?php
if( empty($link['url_standby']) ) {
    header("Refresh:".($is_login?($transition_page['admin_stay_time']??3):($transition_page['visitor_stay_time']??5)).";url=".$link['url']);
}?>
</head>
<body>
	<div id="menu">
	<div class="container">
		<div class = "row">
			<div class="col-sm-8 offset-sm-2">
				<nav class="navbar navbar-expand-md bg-dark navbar-dark">
				<a class="navbar-brand" href="./?u=<?php echo U; ?>"><?php echo $site['title']; ?></a>
				<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#collapsibleNavbar">
					<span class="navbar-toggler-icon"></span>
				</button>
				<div class="collapse navbar-collapse" id="collapsibleNavbar">
					<ul class="navbar-nav">
						<?php echo $theme_config['menu']; ?>

					</ul>
				</div>  
				</nav>
			</div>
		</div>
	</div>
	</div>
	<div class="container" style = "margin-top:2em;">
		<div class= "row">
			<div class="col-sm-8 offset-sm-2 a_d">
			<?php echo $theme_config['a_d_1']; ?>

			</div>
		</div>
		<div class="row">
			<div class="col-sm-8 offset-sm-2">
				<h3>链接信息：</h3>
				<ul class="list-group">
				    <li class="list-group-item" >
                        <span class="badge badge-primary badge-pill-2">标 题 </span><?php echo $link['title']; ?>
                    </li>
				    <li class="list-group-item" >
                        <span class="badge badge-primary badge-pill-2">描 述 </span><?php echo $link['description']; ?>
                    </li>
				    <li class="list-group-item" >
                        <a href="<?php echo $link['url']; ?>" rel = "nofollow" title = "<?php echo $link['title']; ?>"><span class="badge badge-primary badge-pill-2"> 链 接 </span><?php echo $link['url']; ?></a>
                    </li>
<?php 
$i = 0;
foreach ($link['url_standby'] as $key => $url_standby){
    $i++;
    if(preg_match('/\[(.*?)\]\((.*?)\)/', $url_standby, $match)){
        $title = $match[1];
        $url = $match[2];
    }else{
        $title = $url_standby;
        $url = $url_standby;
    }
    ?>
				    <li class="list-group-item" >
				        <a href="<?php echo $url; ?>" rel = "nofollow" title = "<?php echo $link['title']; ?>"><span class="badge badge-primary badge-pill-2">备用<?php echo $i;?></span><?php echo $title; ?></a>
                    </li>
					
<?php } ?>
				</ul>

<?php if( empty($link['url_standby']) ) { ?>

				<div class="spinner-border" style="margin-top: 16px;"></div> 即将打开，请稍等...
<?php }else{ ?>
				<div class="alert alert-primary" style="margin-top: 16px;">
					<strong>存在备用链接，请手动点击您要打开的链接！</strong>
				</div>
<?php } ?>
			</div>
		</div>
		<div class= "row">
			<div class="col-sm-8 offset-sm-2 a_d">
			<?php echo $theme_config['a_d_2']; ?>

			</div>
		</div>
		<div class = "row">
		  <div class="col-sm-8 offset-sm-2">
			<hr>
			<div class="xcdn-footer">
                <?php echo $copyright.PHP_EOL;?>
                <?php echo $ICP.PHP_EOL;?>
                <?php echo $site['custom_footer'].PHP_EOL;?>
                <?php echo $global_config['global_footer'].PHP_EOL;?>
			</div>
		  </div>
		</div>
	</div>
</body>
</html>
