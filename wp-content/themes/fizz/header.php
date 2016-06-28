<!DOCTYPE html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!--> <html <?php language_attributes(); ?> class="no-js"><!--<![endif]-->

<!-- head -->
<head>
	<meta charset="utf-8">
	<!--[if IE]><meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1"/><![endif]-->
	<title><?php wp_title(' | ', true, 'right'); ?> <?php bloginfo('name'); ?></title>
	<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
	<meta name="keywords" content="<?php echo of_get_option('metakeywords'); ?>" />
	<meta name="description" content="<?php echo of_get_option('metadescription'); ?>" />

	<!-- stylesheet -->
	<link rel="stylesheet" media="all" href="<?php bloginfo('stylesheet_url'); ?>"/>
	<link href='http://fonts.googleapis.com/css?family=Open+Sans:300italic,400italic,700italic,400,300,700,800' rel='stylesheet' type='text/css'>
	<!-- stylesheet -->

	
    <!-- custom typography-->
    <?php if(of_get_option('customtypography') == '1') { ?>     
		<?php if(of_get_option('headingfontlink') != '') { ?>
			<?php echo stripslashes(html_entity_decode(of_get_option('headingfontlink')));?>
		<?php } ?>

	    <?php load_template( get_template_directory() . '/custom.typography.css.php' );?>

	<?php } ?>
	<!-- custom typography -->

   

<!-- wp_head -->
<?php wp_head(); ?>
<!-- wp_head -->

</head>
<!-- head -->

	<body <?php body_class(); ?>>
		<!--[if lt IE 7]>
            <p class="chromeframe">You are using an outdated browser. <a href="http://browsehappy.com/">Upgrade your browser today</a> or <a href="http://www.google.com/chromeframe/?redirect=true">install Google Chrome Frame</a> to better experience this site.</p>
        <![endif]-->
    	<?php if ( has_nav_menu( 'main_nav' ) ) {?>
            <div id="small-screens-menu" class="block">
                <a href="#" id="topmenu-button"><strong><?php _e(":::: MENU ::::", "site5framework"); ?></strong></a>
                <?php  site5_main_nav('nav',''); ?>
            </div>
        <?php }?>

	<!-- #page -->
	<div id="page">
		<div class="header-container">
			<div class="radial_gradient">
           <header class=" clearfix">
           		<div class="top wrapper">
           			<?php if (of_get_option('logo') !='' || of_get_option('logo_text')!='' ) { ?>
	               <h1 class="site-title"><a href="<?php bloginfo('url'); ?>"><?php if(of_get_option('logo')) : echo '<img src="'.of_get_option('logo').'" alt="'.get_bloginfo('name').'" />'; else: echo ''.of_get_option('logo_text').''; endif; ?></a></h1>
	               <?php } else { ?>
	               <h1 class="site-title"><?php echo get_bloginfo('name') ?></h1>
	               <?php } ?>

	               <p class="site-description"><?php bloginfo( 'description' ); ?></p>


	                <!-- .top-menu-container -->
			        <div class="top-menu-container">
				        <nav>
							<div id="top-menu">
							<?php  site5_main_nav(false,'sf-menu'); ?>
							</div>
						</nav>
					</div>

					<?php if (of_get_option('display_social') ==1) { ?>
						<ul class="social">
							<li>Stay updated</li>
							<?php if (of_get_option('linkedin') !='' ) { ?><li><a href="<?php echo of_get_option('linkedin');?>" class="linkedin" title="<?php _e( 'LinkedIn', 'site5framework' ); ?>"><?php _e( 'LinkedIn', 'site5framework' ); ?></a></li><?php } ?>
							<?php if (of_get_option('dribble') !='' ) { ?><li><a href="<?php echo of_get_option('dribble');?>" class="dribble" title="<?php _e( 'Dribbble', 'site5framework' ); ?>"><?php _e( 'Dribbble', 'site5framework' ); ?></a></li><?php } ?>
							<?php if (of_get_option('pinterest') !='' ) { ?><li><a href="<?php echo of_get_option('pinterest');?>" class="pinterest" title="<?php _e( 'Pinterest', 'site5framework' ); ?>"><?php _e( 'Pinterest', 'site5framework' ); ?></a></li><?php } ?>
							<?php if (of_get_option('twitter') !='' ) { ?><li><a href="<?php echo of_get_option('twitter');?>" class="twitter" title="<?php _e( 'Twitter', 'site5framework' ); ?>"><?php _e( 'Twitter', 'site5framework' ); ?></a></li><?php } ?>
							<?php if(of_get_option('rss')=='1'): ?>
								<li><a href="<?php echo of_get_option('extrss') ?  of_get_option('extrss') : bloginfo('rss_url'); ?>" title="<?php _e( 'RSS', 'site5framework' ); ?>" class="rss"></a></li>
							<?php endif ?>
						</ul>
					<?php } ?>

           		</div>           	   
           </header>
           <div class="subheader-containerc clearfix">

           </div>
           <span class="clear"></span>
           <?php if(is_front_page() and of_get_option('displayslider') == '1') { ?>
					<?php if(of_get_option('slidertype') == 'flex') { ?>
						<?php get_template_part( 'homepage', 'slider' ); ?>
					<?php } ?>
				<?php } ?>
			</div>
        </div>

        <div class="page-title">
        	<div class="wrapper">
        		<h2><?php 
        			if(is_home()) echo 'Blog'; 
        			else {
        			?>
						<?php if (is_category()) { ?>
								<?php _e("Posts Categorized", "site5framework"); ?> / <span><?php single_cat_title(); ?></span> 
						<?php } elseif (is_tag()) { ?> 
								<?php _e("Posts Tagged", "site5framework"); ?> / <span><?php single_cat_title(); ?></span>
						<?php } elseif (is_author()) { ?>
								<?php _e("Posts By", "site5framework"); ?> / <span><?php the_author_meta('display_name', $post->post_author) ?> </span> 
						<?php } elseif (is_day()) { ?>
								<?php _e("Daily Archives", "site5framework"); ?> / <span><?php the_time('l, F j, Y'); ?></span>
						<?php } elseif (is_month()) { ?>
						    	<?php _e("Monthly Archives", "site5framework"); ?> / <span><?php the_time('F Y'); ?></span>
						<?php } elseif (is_year()) { ?>
						    	<?php _e("Yearly Archives", "site5framework"); ?> / <span><?php the_time('Y'); ?></span> 
						<?php } elseif (is_Search()) { ?>
						    	<?php _e("Search Results", "site5framework"); ?> / <span><?php echo esc_attr(get_search_query()); ?></span> 
						<?php } elseif (is_single() or is_page()) { ?>
								<?php the_title(); ?> 
						<?php } ?>
        			<?php
        			} ?></h2>

        		<form role="search" method="get" id="searchform" action="<?php bloginfo('url'); ?>" >					
					<input type="text" value="search" onfocus="this.value='';" name="s" id="s" />
					<input type="submit" id="searchsubmit" value="" />
				</form>	

        	</div>
        </div>

       

		<!-- .main-container -->
		<div class="main-container">
			

			<div class="main wrapper clearfix">