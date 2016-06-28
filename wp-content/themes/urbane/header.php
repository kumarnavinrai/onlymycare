<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head profile="http://gmpg.org/xfn/11">
<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php bloginfo('charset'); ?>" />
<title><?php bloginfo('name'); ?> <?php if ( is_single() ) { ?> &raquo; Blog Archive <?php } ?> <?php wp_title(); ?></title>
<meta name="generator" content="WordPress <?php bloginfo('version'); ?>" /> <!-- leave this for stats -->
<link rel="stylesheet" href="<?php bloginfo('stylesheet_url'); ?>" type="text/css" media="screen" />
<link rel="alternate" type="application/rss+xml" title="<?php bloginfo('name'); ?> RSS Feed" href="<?php bloginfo('rss2_url'); ?>" />
<link rel="pingback" href="<?php bloginfo('pingback_url'); ?>" />
<script src="<?php bloginfo('template_directory'); ?>/js/curvycorners.js" type="text/javascript"></script>
<script src="<?php bloginfo('template_directory'); ?>/js/SpryTabbedPanels.js" type="text/javascript"></script>
<script src="<?php bloginfo('template_directory'); ?>/js/SpryMenuBar.js" type="text/javascript"></script>
<!--[if IE]>
<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/ie.css" type="text/css" media="screen" />
<![endif]-->
<?php if (function_exists('wp_enqueue_script') && function_exists('is_singular')) : ?>
<?php if ( is_singular() ) wp_enqueue_script( 'comment-reply' ); ?>
<?php endif; ?>
<?php wp_head(); ?>
</head>

<body id="top">
<div class="container_12">
<div class="wrapper">

    <div class="grid_7"><!--logo-->
    <div class="logo"><h1><a href="<?php echo get_option('home'); ?>/"><?php bloginfo('name'); ?></a></h1></div>
    <div class="description"><h2><?php bloginfo('description'); ?></h2></div><br />
    </div>
    
    <div class="grid_5"><!--toplinks-->
    <div class="toplinks"><a href="<?php bloginfo('url'); ?>">Home</a> | <a href="wp-login.php">Login</a> | <a href="#contentstart">Skip to Content</a></div><br />
    </div>

<div class="clear"></div><br />

    <div class="grid_12"><!--navigation-->
    <div id="menu">
    <ul id="MenuBar1" class="MenuBarHorizontal">
    <li class="<?php if (((is_home()) && !(is_paged())) or (is_archive()) or (is_single()) or (is_paged()) or (is_search())) { ?>current_page_item<?php } else { ?>page_item<?php } ?>"><a href="<?php echo get_settings('home'); ?>">Home<?php echo $langblog;?></a></li>
    <?php wp_list_pages('sort_column=menu_order&depth=2&title_li='); ?>
    </ul>
    </div>
    
    </div>
        
<div class="clear" id="contentstart"></div><br />

<script type="text/javascript">
<!--
var MenuBar1 = new Spry.Widget.MenuBar("MenuBar1", {imgDown:"<?php bloginfo('template_directory'); ?>/images/SpryMenuBarDownHover.gif", imgRight:"<?php bloginfo('template_directory'); ?>/images/SpryMenuBarRightHover.gif"});
//-->
</script>