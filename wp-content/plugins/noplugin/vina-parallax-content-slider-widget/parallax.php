<?php
/*
Plugin Name: Vina Parallax Content Slider Widget
Plugin URI: http://VinaThemes.biz
Description: A Wordpress content slider with delayed animations and background parallax effect.
Version: 1.0
Author: VinaThemes
Author URI: http://VinaThemes.biz
Author email: mr_hiennc@yahoo.com
Demo URI: http://VinaDemo.biz
Forum URI: http://VinaForum.biz
License: GPLv3+
*/

//Defined global variables
if(!defined('VINA_PARALLAX_DIRECTORY')) 		define('VINA_PARALLAX_DIRECTORY', dirname(__FILE__));
if(!defined('VINA_PARALLAX_INC_DIRECTORY')) 	define('VINA_PARALLAX_INC_DIRECTORY', VINA_PARALLAX_DIRECTORY . '/includes');
if(!defined('VINA_PARALLAX_URI')) 			define('VINA_PARALLAX_URI', get_bloginfo('url') . '/wp-content/plugins/vina-parallax-widget');
if(!defined('VINA_PARALLAX_INC_URI')) 		define('VINA_PARALLAX_INC_URI', VINA_PARALLAX_URI . '/includes');

//Include library
if(!defined('TCVN_FUNCTIONS')) {
    include_once VINA_PARALLAX_INC_DIRECTORY . '/functions.php';
    define('TCVN_FUNCTIONS', 1);
}
if(!defined('TCVN_FIELDS')) {
    include_once VINA_PARALLAX_INC_DIRECTORY . '/fields.php';
    define('TCVN_FIELDS', 1);
}

class Parallax_Widget extends WP_Widget 
{
	function Parallax_Widget()
	{
		$widget_ops = array(
			'classname' => 'parallax_widget',
			'description' => __("A Wordpress content slider with delayed animations and background parallax effect.")
		);
		$this->WP_Widget('parallax_widget', __('Vina Parallax Content Slider Widget'), $widget_ops);
	}
	
	function form($instance)
	{
		$instance = wp_parse_args( 
			(array) $instance, 
			array( 
				'title' 			=> '',
				'categoryId' 		=> '',
				'noItem' 			=> '5',
				'ordering' 			=> 'id',
				'orderingDirection' => 'desc',
				
				'current'		=> '0',
				'bgincrement'	=> '50',
				'autoplay'		=> 'yes',
				'interval'		=> '4000',
				'navArrows'		=> 'yes',
				
				'showTitle'		=> 'yes',
				'showImage'		=> 'yes',
				'thumbW'		=> '256',
				'thumbH'		=> '256',
				'showContent'	=> 'yes',
				'readmore'		=> 'yes',
			)
		);

		$title			= esc_attr($instance['title']);
		$categoryId		= esc_attr($instance['categoryId']);
		$noItem			= esc_attr($instance['noItem']);
		$ordering		= esc_attr($instance['ordering']);
		$orderingDirection = esc_attr($instance['orderingDirection']);
		
		$current		= esc_attr($instance['current']);
		$bgincrement	= esc_attr($instance['bgincrement']);
		$autoplay		= esc_attr($instance['autoplay']);
		$interval		= esc_attr($instance['interval']);
		$navArrows		= esc_attr($instance['navArrows']);
		
		$showTitle		= esc_attr($instance['showTitle']);
		$showImage		= esc_attr($instance['showImage']);
		$thumbW			= esc_attr($instance['thumbW']);
		$thumbH			= esc_attr($instance['thumbH']);
		$showContent	= esc_attr($instance['showContent']);
		$readmore		= esc_attr($instance['readmore']);
		?>
        <div id="tcvn-timeline" class="tcvn-plugins-container">
            <div style="color: red; padding: 0px 0px 10px; text-align: center;">You are using free version ! <a href="http://vinathemes.biz/commercial-plugins/item/28-vina-content-slider-widget.html" title="Download full version." target="_blank">Click here</a> to download full version.</div>
            <div id="tcvn-tabs-container">
                <ul id="tcvn-tabs">
                    <li class="active"><a href="#basic"><?php _e('Basic'); ?></a></li>
                    <li><a href="#display"><?php _e('Display'); ?></a></li>
                    <li><a href="#advanced"><?php _e('Advanced'); ?></a></li>
                </ul>
            </div>
            <div id="tcvn-elements-container">
                <!-- Basic Block -->
                <div id="basic" class="tcvn-telement" style="display: block;">
                    <p><?php echo eTextField($this, 'title', 'Title', $title); ?></p>
                    <p><?php echo eSelectOption($this, 'categoryId', 'Category', buildCategoriesList('Select all Categories.'), $categoryId); ?></p>
                    <p><?php echo eTextField($this, 'noItem', 'Number of Post', $noItem, 'Number of posts to show. Default is: 5.'); ?></p>
                	<p><?php echo eSelectOption($this, 'ordering', 'Post Field to Order By', 
						array('id'=>'ID', 'title'=>'Title', 'comment_count'=>'Comment Count', 'post_date'=>'Published Date'), $ordering); ?></p>
                    <p><?php echo eSelectOption($this, 'orderingDirection', 'Ordering Direction', 
						array('asc'=>'Ascending', 'desc'=>'Descending'), $orderingDirection, 
						'Select the direction you would like Articles to be ordered by.'); ?></p>
                </div>
                <!-- Display Block -->
                <div id="display" class="tcvn-telement">
                    <p><?php echo eTextField($this, 'current', 'Index of current slide', $current); ?></p>
                    <p><?php echo eTextField($this, 'bgincrement', 'Background Increment', $bgincrement); ?></p>
                    <p><?php echo eSelectOption($this, 'autoplay', 'Autoplay', 
						array('yes'=>'On Slideshow', 'no'=>'Off Slideshow'), $autoplay); ?></p>
                    <p><?php echo eTextField($this, 'interval', 'Time between transitions', $interval); ?></p>
                    <p><?php echo eSelectOption($this, 'navArrows', 'Navigation Arrows', 
						array('yes'=>'Show Navigation Arrows', 'no'=>'Hide Navigation Arrows'), $navArrows); ?></p>
                </div>
                <!-- Advanced Block -->
                <div id="advanced" class="tcvn-telement">
                    <p><?php echo eSelectOption($this, 'showTitle', 'Post Title', 
						array('yes'=>'Show post title', 'no'=>'Hide post title'), $showTitle); ?></p>
                    <p><?php echo eSelectOption($this, 'showImage', 'Show Thumbnail', 
						array('yes'=>'Yes', 'no'=>'No'), $showImage); ?></p>
                    <p><?php echo eTextField($this, 'thumbW', 'Thumbnail Width', $thumbW); ?></p>
                    <p><?php echo eTextField($this, 'thumbH', 'Thumbnail Height', $thumbH); ?></p>
                    <p><?php echo eSelectOption($this, 'showContent', 'Post Content', 
						array('yes'=>'Show post content', 'no'=>'Hide post content'), $showContent); ?></p>
                    <p><?php echo eSelectOption($this, 'readmore', 'Readmore', 
						array('yes'=>'Show readmore button', 'no'=>'Hide readmore button'), $readmore); ?></p>
                </div>
            </div>
        </div>
		<script>
			jQuery(document).ready(function($){
				var prefix = '#tcvn-timeline ';
				$(prefix + "li").click(function() {
					$(prefix + "li").removeClass('active');
					$(this).addClass("active");
					$(prefix + ".tcvn-telement").hide();
					
					var selectedTab = $(this).find("a").attr("href");
					$(prefix + selectedTab).show();
					
					return false;
				});
			});
        </script>
		<?php
	}
	
	function update($new_instance, $old_instance) 
	{
		return $new_instance;
	}
	
	function widget($args, $instance) 
	{
		extract($args);
		
		$title 			= getConfigValue($instance, 'title',		'');
		$categoryId		= getConfigValue($instance, 'categoryId',	'');
		$noItem			= getConfigValue($instance, 'noItem',		'5');
		$ordering		= getConfigValue($instance, 'ordering',		'id');
		$orderingDirection = getConfigValue($instance, 'orderingDirection',	'desc');
		
		$bgImage		= getConfigValue($instance, 'bgImage',  'wp-content/plugins/vina-parallax-widget/includes/images/waves.gif');
		$current		= getConfigValue($instance, 'current', '0');
		$bgincrement	= getConfigValue($instance, 'bgincrement', '50');
		$autoplay		= getConfigValue($instance, 'autoplay', 'yes');
		$interval 		= getConfigValue($instance, 'interval', '4000');
		$navArrows 		= getConfigValue($instance, 'navArrows', 'yes');
		
		$showTitle		= getConfigValue($instance, 'showTitle',	'yes');
		$showImage		= getConfigValue($instance, 'showImage',	'yes');
		$thumbW			= getConfigValue($instance, 'thumbW',	'256');
		$thumbH			= getConfigValue($instance, 'thumbH',	'256');
		$showContent	= getConfigValue($instance, 'showContent',	'yes');
		$readmore		= getConfigValue($instance, 'readmore',		'yes');
		
		$params = array(
			'numberposts' 	=> $noItem, 
			'category' 		=> $categoryId, 
			'orderby' 		=> $order,
			'order' 		=> $orderingDirection,
		);
		
		if($categoryId == '') {
			$params = array(
				'numberposts' 	=> $noItem, 
				'orderby' 		=> $order,
				'order' 		=> $orderingDirection,
			);
		}
		
		$posts 	 = get_posts($params);
		
		echo $before_widget;
		
		if($title) echo $before_title . $title . $after_title;
		
		if(!empty($posts)) :
		?>
        <div id="vina-parallax-slider" class="vina-parallax-slider">
        	<?php 
				foreach($posts as $post) :
					$thumbnailId = get_post_thumbnail_id($post->ID);				
					$thumbnail 	 = wp_get_attachment_image_src($thumbnailId, '70x45');	
					$altText 	 = get_post_meta($thumbnailId, '_wp_attachment_image_alt', true);
					$commentsNum = get_comments_number($post->ID);
					$postTitle   = $post->post_title;
					$largeImage  = VINA_PARALLAX_INC_URI . '/timthumb.php?w='.$thumbW.'&h='.$thumbH.'&a=c&q=99&z=0&src=';
					$link   = get_permalink($post->ID);
					$text   = explode('<!--more-->', $post->post_content);
					$sumary = $text[0];
			?>
            <div class="vina-parallax-slide">
                <!-- Title Block -->
				<?php if($showTitle == 'yes') : ?>
                <h2><?php echo $postTitle; ?></h2>
                <?php endif; ?>
                
                <!-- Content Block -->
                <?php if($showContent == 'yes') : ?>
                <p><?php echo $sumary; ?></p>
                <?php endif; ?>
                
                <!-- Readmore Block -->
                <?php if($readmore == 'yes') : ?>
                <a href="<?php echo $link; ?>" class="vina-parallax-link">Read more</a>
                <?php endif; ?>
                
                <!-- Image Block -->
                <div class="vina-parallax-img">
                	<?php if($showImage == 'yes') : ?>
                    <img src="<?php echo $largeImage . $thumbnail[0]; ?>" alt="<?php echo $postTitle; ?>" />
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
            
            <!-- Navigation Arrows -->
            <?php if($navArrows == 'yes') : ?>
            <nav class="vina-parallax-arrows">
                <span class="vina-parallax-arrows-prev"></span>
                <span class="vina-parallax-arrows-next"></span>
            </nav>
            <?php endif; ?>
        </div>
        <div id="tcvn-copyright">
        	<a href="http://vinathemes.biz" title="Free download Wordpress Themes, Wordpress Plugins - VinaThemes.biz">Free download Wordpress Themes, Wordpress Plugins - VinaThemes.biz</a>
        </div>
        <script type="text/javascript">
			jQuery(function($) {
				var $slider	= $('#vina-parallax-slider');
				$slider.cslider({
					current     : <?php echo $current; ?>,
					bgincrement : <?php echo $bgincrement; ?>,
					autoplay    : <?php echo ($autoplay == 'yes') ? 'true' : 'false'; ?>,
					interval    : <?php echo $interval; ?>,	
				});
			});
		</script>
		<?php
		endif;
		
		echo $after_widget;
	}
}
wp_enqueue_script("jquery");

add_action('widgets_init', create_function('', 'return register_widget("Parallax_Widget");'));
wp_enqueue_style('vina-admin-css', VINA_PARALLAX_INC_URI . '/admin/css/style.css', '', '1.0', 'screen' );
wp_enqueue_script('vina-tooltips', VINA_PARALLAX_INC_URI . '/admin/js/jquery.simpletip-1.3.1.js', 'jquery', '1.0', true);

wp_enqueue_style('vina-parallax-css', 	VINA_PARALLAX_INC_URI . '/css/style.css', '', '1.0', 'screen' );
wp_enqueue_script('vina-modernizr', 	VINA_PARALLAX_INC_URI . '/js/modernizr.custom.28468.js', 'jquery', '1.0', true);
wp_enqueue_script('vina-parallax-tabs', VINA_PARALLAX_INC_URI . '/js/jquery.cslider.js', 'jquery', '1.0', true);
?>