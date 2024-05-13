<?php if(!defined('DIR')){header('HTTP/1.1 404 Not Found');header("status: 404 Not Found");exit;}?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title><?php echo $title;?></title>
    <meta name="renderer" content="webkit">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <meta name="referrer" content="no-referrer-when-downgrade">
    <?php load_static('css');if($awesome) echo str_replace('#',$libs,'    <link rel="stylesheet" href="#/Font-awesome/4.7.0/css/font-awesome.min.css" media="all">'."\n");?>
    <script>var u = "<?php echo U;?>";</script>
</head>
