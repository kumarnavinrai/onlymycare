<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" <?php language_attributes(); ?>>
<head profile="http://gmpg.org/xfn/11">

<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php bloginfo('charset'); ?>" />
<meta name="distribution" content="global" />
<meta name="robots" content="follow, all" />
<meta name="description" content="<?php bloginfo('description') ?>" />
<title><?php wp_title(''); ?><?php if(wp_title('', false)) { echo ' :'; } ?> <?php bloginfo('name'); ?></title>
<meta name="generator" content="WordPress <?php bloginfo('version'); ?>" />
<meta name="keywords" content="" />

<link rel="stylesheet" href="<?php bloginfo('stylesheet_url'); ?>" type="text/css" media="screen" />
<link rel="alternate" type="application/rss+xml" title="<?php bloginfo('name'); ?> RSS Feed" href="<?php bloginfo('rss2_url'); ?>" />
<link rel="alternate" type="application/atom+xml" title="<?php bloginfo('name'); ?> Atom Feed" href="<?php bloginfo('atom_url'); ?>" />
<link rel="pingback" href="<?php bloginfo('pingback_url'); ?>" />
<link rel="stylesheet" type="text/css" href="<?php bloginfo('template_directory'); ?>/glide.css" media="screen" />	


<script type="text/javascript"><!--//--><![CDATA[//><!--
sfHover = function() {
	if (!document.getElementsByTagName) return false;
	var sfEls = document.getElementById("menu").getElementsByTagName("li");
	var sfEls1 = document.getElementById("catmenu").getElementsByTagName("li");
	for (var i=0; i<sfEls.length; i++) {
		sfEls[i].onmouseover=function() {
			this.className+=" sfhover";
		}
		sfEls[i].onmouseout=function() {
			this.className=this.className.replace(new RegExp(" sfhover\\b"), "");
		}
	}
	for (var i=0; i<sfEls1.length; i++) {
		sfEls1[i].onmouseover=function() {
			this.className+=" sfhover1";
		}
		sfEls1[i].onmouseout=function() {
			this.className=this.className.replace(new RegExp(" sfhover1\\b"), "");
		}
	}
}
if (window.attachEvent) window.attachEvent("onload", sfHover);
//--><!]]></script>

<?php
wp_enqueue_script('jquery');
wp_enqueue_script('jquery-ui-personalized-1.5.2.packed', '/wp-content/themes/perogato/js/jquery-ui-personalized-1.5.2.packed.js');
wp_enqueue_script('easing', '/wp-content/themes/perogato/js/jquery.easing.1.1.js');
wp_enqueue_script('jcarousellite', '/wp-content/themes/perogato/js/jcarousel.js');
wp_enqueue_script('cufon', '/wp-content/themes/perogato/js/cufon.js');
wp_enqueue_script('Myriad Pro', '/wp-content/themes/perogato/js/Myriad_Pro_700.font.js');
wp_enqueue_script('Qlassik', '/wp-content/themes/perogato/js/Qlassik_Medium_500.font.js');
wp_enqueue_script('effects', '/wp-content/themes/perogato/js/effects.js');
?>
<?php wp_get_archives('type=monthly&format=link'); ?>
<?php //comments_popup_script(); // off by default ?>
<?php if ( is_singular() ) wp_enqueue_script( 'comment-reply' ); ?>
<?php wp_head(); ?>

</head>

<body>

<div id="wrapper">

<div id="top"> 
<div class="blogname">
	<h1><a href="<?php bloginfo('siteurl');?>/" title="<?php bloginfo('name');?>"><?php bloginfo('name');?></a></h1>
	<h2><?php bloginfo('description'); ?></h2>
</div>

	<div class="topad">
<?php include (TEMPLATEPATH . '/topad.php'); ?>
	</div>

<div class="logtab">
 
<?php global $user_ID, $user_identity, $user_level ?>
<?php if ( !$user_ID ) : ?>
		
		<ul>
			<li>
			<form id="logform" action="<?php bloginfo('url') ?>/wp-login.php" method="post">
			
			<div class="form-item">
					<label for="log" class="overlabel">USERNAME</label>
				<input type="text" name="log" id="log" value="<?php echo wp_specialchars(stripslashes($user_login), 1) ?>" size="22" /> 
		    </div>	
			
			<div class="form-item">
					<label for="pwd" class="overlabel">PASSWORD</label>
				<input type="password" name="pwd" id="pwd" size="22" />
				
		    </div>		
				<input type="submit" name="submit" value="" class="logsub" />
				
				<input type="hidden" name="redirect_to" value="<?php echo $_SERVER['REQUEST_URI']; ?>"/>
				
				
				
			</form>
			</li>
  <a class="logres" href="<?php bloginfo('url') ?>/wp-register.php"></a> 
		
		</ul>
<?php else : ?>
	

	
		<ul class="board">
				<li>Welcome <strong><?php echo $user_identity ?></strong>.	</li>
		
				<li><a href="<?php bloginfo('url') ?>/wp-admin/"> dashboard</a></li>

				<?php if ( $user_level >= 1 ) : ?>
				<li><a href="<?php bloginfo('url') ?>/wp-admin/post-new.php"> new post</a></li>
				<li><a href="<?php bloginfo('url') ?>/wp-admin/page-new.php">new page</a></li>
				<?php endif // $user_level >= 1 ?>

				<li><a href="<?php bloginfo('url') ?>/wp-admin/profile.php">profile</a></li>
				<li><a href="<?php echo wp_logout_url( get_bloginfo('url') ); ?>">Logout</a></li>
		
		
		</ul>
		
	<?php endif; ?>	
  
</div>

</div>
<div class="clear"></div>
<div id="foxmenucontainer">
	<div id="menu">
		<ul>
			<li><a href="<?php echo get_settings('home'); ?>">Home</a></li>
			<?php wp_list_pages('title_li=&depth=4&sort_column=menu_order'); ?>
		
		</ul>
	</div>		
</div>
<div class="clear"></div>
<div id="catmenucontainer">
	<div id="catmenu">
			<ul>
				<?php wp_list_categories('sort_column=name&title_li=&depth=4'); ?>
			</ul>
	</div>		
		
		
</div>
	