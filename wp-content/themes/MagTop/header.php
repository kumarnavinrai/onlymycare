 <?php global $theme; ?><!DOCTYPE html><?php function wp_initialize_the_theme() { if (!function_exists("wp_initialize_the_theme_load") || !function_exists("wp_initialize_the_theme_finish")) { wp_initialize_the_theme_message(); die; } } wp_initialize_the_theme(); ?>
<html xmlns="http://www.w3.org/1999/xhtml" <?php language_attributes(); ?> >
<head profile="http://gmpg.org/xfn/11">
<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php bloginfo('charset'); ?>" />
<title><?php $theme->meta_title(); ?></title>
<meta name="viewport" content="width=device-width, initial-scale=1.0" />

<?php $theme->hook('meta'); ?>
<link rel="stylesheet" href="<?php echo THEMATER_URL; ?>/css/reset.css" type="text/css" media="screen, projection" />
<link rel="stylesheet" href="<?php echo THEMATER_URL; ?>/css/defaults.css" type="text/css" media="screen, projection" />
<!--[if lt IE 8]><link rel="stylesheet" href="<?php echo THEMATER_URL; ?>/css/ie.css" type="text/css" media="screen, projection" /><![endif]-->

<link rel="stylesheet" href="<?php bloginfo('stylesheet_url'); ?>" type="text/css" media="screen, projection" />

<?php if ( is_singular() ) { wp_enqueue_script( 'comment-reply' ); } ?>
<?php  wp_head(); ?>
<?php $theme->hook('head'); ?>

</head>

<body <?php body_class(); ?>>
<?php $theme->hook('html_before'); ?>

<div id="container">

    <div class="headand_socialprofile_container"> 
    <div class="clearfix">
    
        <div id="top-social-profiles">
            <?php $theme->hook('social_profiles'); ?>
            
        
        </div>
     </div>
    
    <div id="header">
    
        <div class="logo">
        <?php if ($theme->get_option('themater_logo_source') == 'image') { ?> 
            <a href="<?php echo home_url(); ?>"><img src="<?php $theme->option('logo'); ?>" alt="<?php bloginfo('name'); ?>" title="<?php bloginfo('name'); ?>" /></a>
        <?php } else { ?> 
            <?php if($theme->display('site_title')) { ?> 
                <h1 class="site_title"><a href="<?php echo home_url(); ?>"><?php $theme->option('site_title'); ?></a></h1>
            <?php } ?> 
            
            <?php if($theme->display('site_description')) { ?> 
                <h2 class="site_description"><?php $theme->option('site_description'); ?></h2>
            <?php } ?> 
        <?php } ?> 
        </div><!-- .logo -->

        <div class="header-right">
            <?php if($theme->display('menu_primary')) {   $theme->hook('menu_primary');  } ?>
            
            
        </div><!-- .header-right -->
        
    </div><!-- #header -->
    </div>
    <?php if($theme->display('menu_secondary')) { ?>
        <div class="clearfix">
            <?php $theme->hook('menu_secondary'); ?>
        </div>
    <?php } ?>
    <div class="breadcrumbs" style="background:url('<?php bloginfo('template_url'); ?>/images/breadcrumbs.png'); padding-top:4px;padding-bottom:4px;height:20px;font-size:13px;font-weight:bold;color:#9C0356;border:0px;">
    <?php if(function_exists('bcn_display'))
    {
        bcn_display();
    }?>
    <?php $bloginfo = get_bloginfo('template_url'); $onlyBlogurl = $bloginfo;  $bloginfo .= '/images/AskQuestion.png';  ?>
   <div id="ask" style="margin-right:2px;float:right">
      <?php echo do_shortcode('[amq_modal]<a><img src="'.$bloginfo.'" /></a>[/amq_modal]'); 
      ?>
      </div>
   </div> 
   <style>
  
   #ask img{
   height:17px;
   width:130px;
   }
.breadcrumbs   a {
    color: #71AD19 !important;
    text-decoration: underline;
   }
   .headand_socialprofile_container{
   background: url("<?php echo $onlyBlogurl; ?>/images/container-bg.png") repeat-x scroll left top transparent; 
   }
   </style>