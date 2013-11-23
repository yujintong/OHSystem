<?php
if (!isset($website) ) {header('HTTP/1.1 404 Not Found'); die; }
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
 	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<meta http-equiv="content-style-type" content="text/css" />
	<meta name="author" content="Ivan Antonijevic" />
	<meta name="rating" content="Safe For Kids" />
 	<meta name="description" content="<?=$HomeDesc?>" />
	<meta name="keywords" content="<?=$HomeKeywords?>" />
	<meta name="google-site-verification" content="SIOjMAwhhAwuaKPJ98q8yMNMuwSFE92xy0qCU4LHNfI" />
	<?=os_add_meta()?>
	<link rel="shortcut icon" href="<?=OS_THEME_PATH?>favicon.ico" />
	<title><?=$HomeTitle?></title>
	
<link rel="index" title="<?=OS_HOME_TITLE?>" href="<?=OS_HOME?>" />
<?php /* SKIP THIS CSS ?>
<link rel="stylesheet" href="<?=OS_THEME_PATH?>css/box-style.css" type="text/css" />
<?php */ ?>
<!--[if lte IE 6]><link rel="stylesheet" href="<?=OS_THEME_PATH?>css/ie6.css" type="text/css" />
<![endif]-->
<!--[if IE 7]><link rel="stylesheet" href="<?=OS_THEME_PATH?>css/ie7.css" type="text/css" />
<![endif]-->
<!--[if IE 8]><link rel="stylesheet" href="<?=OS_THEME_PATH?>css/ie8.css" type="text/css" />
<![endif]-->
<link rel="stylesheet" href="<?=OS_HOME?><?=OS_CURRENT_THEME_PATH?>style.css" type="text/css" />
<link href="<?=OS_THEME_PATH?>css/template.css" rel="stylesheet" type="text/css" />

<style type="text/css">
			body {min-width: 960px;}
			div.wrapper { margin: 0 auto; width: 960px;}
</style>

<?php os_js() ?>
<script type="text/javascript" src="<?=OS_HOME?>scripts.js"></script>
<?php os_head(); ?>
</head>
